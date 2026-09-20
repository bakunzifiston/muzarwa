<?php

namespace App\Services;

use App\Models\InventoryRecord;
use App\Models\SaleItem;
use Carbon\Carbon;

class InventoryValuationService
{
    /**
     * FIFO: Build remaining layers per item (product_id or item_name).
     * Each layer is [quantity_remaining, unit_cost].
     */
    /** Per-request memo for getFifoLayers(), which otherwise rescans every inventory record. */
    protected array $layerCache = [];

    public function getFifoLayers(?string $asOfDate = null): array
    {
        if (array_key_exists((string) $asOfDate, $this->layerCache)) {
            return $this->layerCache[(string) $asOfDate];
        }

        $query = InventoryRecord::query()
            ->orderBy('record_date')
            ->orderBy('id');

        if ($asOfDate) {
            $query->where('record_date', '<=', $asOfDate);
        }

        $records = $query->get();
        $layersByKey = [];

        foreach ($records as $record) {
            $key = $this->itemKey($record);
            $unitCost = max(0, (float) ($record->unit_cost ?? 0));

            if (($record->quantity_in ?? 0) > 0) {
                if (! isset($layersByKey[$key])) {
                    $layersByKey[$key] = [];
                }
                $layersByKey[$key][] = [(float) $record->quantity_in, $unitCost];
            }

            $toConsume = (float) ($record->quantity_out ?? 0);
            if ($toConsume > 0 && isset($layersByKey[$key])) {
                $layersByKey[$key] = $this->consumeFromLayers($layersByKey[$key], $toConsume);
            }
        }

        return $this->layerCache[(string) $asOfDate] = $layersByKey;
    }

    /**
     * Total stock value using FIFO (all items).
     */
    public function getFifoTotalValue(?string $asOfDate = null): float
    {
        $layers = $this->getFifoLayers($asOfDate);
        $total = 0;
        foreach ($layers as $itemLayers) {
            foreach ($itemLayers as [$qty, $cost]) {
                $total += $qty * $cost;
            }
        }

        return round($total, 2);
    }

    /**
     * Total quantity on hand (FIFO remaining).
     */
    public function getFifoTotalQuantity(?string $asOfDate = null): float
    {
        $layers = $this->getFifoLayers($asOfDate);
        $total = 0;
        foreach ($layers as $itemLayers) {
            foreach ($itemLayers as [$qty]) {
                $total += $qty;
            }
        }

        return $total;
    }

    /**
     * COGS for sales in a date range: process inventory + sales in date order, consume FIFO per sale item.
     */
    public function getCogsForSalesInPeriod(?string $startDate, ?string $endDate): float
    {
        if (! $startDate || ! $endDate) {
            return 0;
        }
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Initial layers at day before start
        $layers = $this->getFifoLayers($start->copy()->subDay()->format('Y-m-d'));

        // Events: inventory records in range
        $inventoryRecords = InventoryRecord::query()
            ->whereBetween('record_date', [$start, $end])
            ->orderBy('record_date')
            ->orderBy('id')
            ->get();

        // Events: sale items in range with sale date
        $saleItems = SaleItem::query()
            ->with('sale', 'product')
            ->whereHas('sale', fn ($q) => $q->whereBetween('sale_date', [$start, $end]))
            ->get()
            ->sortBy(fn ($item) => $item->sale->sale_date.' '.$item->id);

        // Merge and sort: we'll process inventory first by record_date, then sales by sale_date
        $events = [];
        foreach ($inventoryRecords as $r) {
            $events[] = ['date' => Carbon::parse($r->record_date), 'type' => 'inventory', 'record' => $r];
        }
        foreach ($saleItems as $si) {
            $events[] = ['date' => Carbon::parse($si->sale->sale_date), 'type' => 'sale_item', 'sale_item' => $si];
        }
        usort($events, fn ($a, $b) => $a['date']->getTimestamp() <=> $b['date']->getTimestamp());

        $totalCogs = 0;
        foreach ($events as $ev) {
            if ($ev['type'] === 'inventory') {
                $r = $ev['record'];
                $key = $this->itemKey($r);
                $unitCost = max(0, (float) ($r->unit_cost ?? 0));
                if (($r->quantity_in ?? 0) > 0) {
                    if (! isset($layers[$key])) {
                        $layers[$key] = [];
                    }
                    $layers[$key][] = [(float) $r->quantity_in, $unitCost];
                }
                $toConsume = (float) ($r->quantity_out ?? 0);
                if ($toConsume > 0 && isset($layers[$key])) {
                    $layers[$key] = $this->consumeFromLayers($layers[$key], $toConsume);
                }
            } else {
                $si = $ev['sale_item'];
                $productId = $si->product_id;
                $productName = $si->product->name ?? '';
                $qty = (float) $si->quantity_sold;
                if ($qty <= 0) {
                    continue;
                }
                // Try product_id key first, then item name key (for inventory not linked to product)
                $key = $productId ? (string) $productId : 'name:'.$productName;
                $nameKey = 'name:'.$productName;
                $tryKey = isset($layers[$key]) ? $key : (isset($layers[$nameKey]) ? $nameKey : null);
                if ($tryKey === null) {
                    continue;
                }
                [$newLayers, $cost] = $this->consumeFromLayersAndReturnCost($layers[$tryKey], $qty);
                $layers[$tryKey] = $newLayers;
                $totalCogs += $cost;
            }
        }

        return round($totalCogs, 2);
    }

    /**
     * FIFO cost of goods sold, split by month and by product, for a reporting window.
     *
     * Returns ['product:<key>' => ['label' => string, 'values' => [monthIndex => cost]]].
     *
     * Only delivered sales are costed, so COGS is recognised in the same period and on the
     * same basis as the revenue it relates to (see RevenueRecognitionService). Raw-material
     * purchases are deliberately NOT included: a purchase moves value from cash into
     * inventory, it is not an expense until the goods are sold.
     */
    public function getMonthlyCogsByProduct(string $startDate, string $endDate, int $months = 12): array
    {
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfDay();

        // Opening layers as at the day before the window, then replay events in date order.
        $layers = $this->getFifoLayers($start->copy()->subDay()->format('Y-m-d'));

        $events = [];
        foreach (InventoryRecord::query()
            ->whereBetween('record_date', [$start, $end])
            ->orderBy('record_date')->orderBy('id')->get() as $record) {
            $events[] = ['date' => Carbon::parse($record->record_date), 'seq' => $record->id, 'type' => 'inventory', 'record' => $record];
        }
        foreach (SaleItem::query()
            ->with(['sale', 'product'])
            ->whereHas('sale', fn ($q) => $q->whereBetween('sale_date', [$start, $end])
                ->where('delivery_status', RevenueRecognitionService::DELIVERED))
            ->get() as $item) {
            $events[] = ['date' => Carbon::parse($item->sale->sale_date), 'seq' => $item->id, 'type' => 'sale_item', 'sale_item' => $item];
        }

        // Inventory intake must settle before same-day sales consume it, hence the type tiebreak.
        usort($events, fn ($a, $b) => [$a['date']->getTimestamp(), $a['type'] === 'sale_item' ? 1 : 0, $a['seq']]
            <=> [$b['date']->getTimestamp(), $b['type'] === 'sale_item' ? 1 : 0, $b['seq']]);

        $rows = [];
        foreach ($events as $event) {
            if ($event['type'] === 'inventory') {
                $record = $event['record'];
                $key = $this->itemKey($record);
                if (($record->quantity_in ?? 0) > 0) {
                    $layers[$key][] = [(float) $record->quantity_in, max(0, (float) ($record->unit_cost ?? 0))];
                }
                $toConsume = (float) ($record->quantity_out ?? 0);
                if ($toConsume > 0 && isset($layers[$key])) {
                    $layers[$key] = $this->consumeFromLayers($layers[$key], $toConsume);
                }

                continue;
            }

            $item = $event['sale_item'];
            $quantity = (float) $item->quantity_sold;
            if ($quantity <= 0) {
                continue;
            }
            $layerKey = $this->resolveLayerKey($layers, $item->product_id, $item->product->name ?? '');
            if ($layerKey === null) {
                continue;
            }
            [$layers[$layerKey], $cost] = $this->consumeFromLayersAndReturnCost($layers[$layerKey], $quantity);

            $index = ($event['date']->year - $start->year) * 12 + $event['date']->month - $start->month;
            if ($index < 0 || $index >= $months) {
                continue;
            }
            $rowKey = 'product:'.($item->product_id ?? $layerKey);
            $rows[$rowKey] ??= ['label' => $item->product->type ?? $item->product->name ?? 'Unspecified product', 'values' => []];
            $rows[$rowKey]['values'][$index] = round(($rows[$rowKey]['values'][$index] ?? 0) + $cost, 2);
        }

        return $rows;
    }

    /**
     * Stock value at a date, net of goods sold.
     *
     * getFifoTotalValue() only consumes layers through `inventory_records.quantity_out`,
     * so it ignores despatches recorded as sale items and overstates stock by exactly the
     * cost of goods sold. The balance sheet needs the net figure, otherwise assets exceed
     * liabilities plus equity by the COGS amount and the statement cannot balance.
     */
    public function getFifoValueNetOfSalesAt(string $asOfDate): float
    {
        $end = Carbon::parse($asOfDate)->endOfDay();
        $layers = [];

        $events = [];
        foreach (InventoryRecord::query()
            ->whereDate('record_date', '<=', $end->toDateString())
            ->orderBy('record_date')->orderBy('id')->get() as $record) {
            $events[] = ['date' => Carbon::parse($record->record_date), 'seq' => $record->id, 'type' => 'inventory', 'record' => $record];
        }
        foreach (SaleItem::query()
            ->with(['sale', 'product'])
            ->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '<=', $end->toDateString())
                ->where('delivery_status', RevenueRecognitionService::DELIVERED))
            ->get() as $item) {
            $events[] = ['date' => Carbon::parse($item->sale->sale_date), 'seq' => $item->id, 'type' => 'sale_item', 'sale_item' => $item];
        }

        usort($events, fn ($a, $b) => [$a['date']->getTimestamp(), $a['type'] === 'sale_item' ? 1 : 0, $a['seq']]
            <=> [$b['date']->getTimestamp(), $b['type'] === 'sale_item' ? 1 : 0, $b['seq']]);

        foreach ($events as $event) {
            if ($event['type'] === 'inventory') {
                $record = $event['record'];
                $key = $this->itemKey($record);
                if (($record->quantity_in ?? 0) > 0) {
                    $layers[$key][] = [(float) $record->quantity_in, max(0, (float) ($record->unit_cost ?? 0))];
                }
                $toConsume = (float) ($record->quantity_out ?? 0);
                if ($toConsume > 0 && isset($layers[$key])) {
                    $layers[$key] = $this->consumeFromLayers($layers[$key], $toConsume);
                }

                continue;
            }

            $item = $event['sale_item'];
            $quantity = (float) $item->quantity_sold;
            if ($quantity <= 0) {
                continue;
            }
            $layerKey = $this->resolveLayerKey($layers, $item->product_id, $item->product->name ?? '');
            if ($layerKey === null) {
                continue;
            }
            [$layers[$layerKey]] = $this->consumeFromLayersAndReturnCost($layers[$layerKey], $quantity);
        }

        $total = 0.0;
        foreach ($layers as $itemLayers) {
            foreach ($itemLayers as [$qty, $cost]) {
                $total += $qty * $cost;
            }
        }

        return round($total, 2);
    }

    /**
     * FIFO cost attributable to each individual sale line in a window.
     *
     * Returns [saleItemId => cost]. A line whose product has no matching purchase layer is
     * absent from the result rather than present with a zero, so callers can tell
     * "cost nothing" apart from "cannot be costed".
     */
    public function costPerSaleItem(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $layers = $this->getFifoLayers($start->copy()->subDay()->format('Y-m-d'));

        $events = [];
        foreach (InventoryRecord::query()
            ->whereBetween('record_date', [$start, $end])
            ->orderBy('record_date')->orderBy('id')->get() as $record) {
            $events[] = ['date' => Carbon::parse($record->record_date), 'seq' => $record->id, 'type' => 'inventory', 'record' => $record];
        }
        foreach (SaleItem::query()
            ->with(['sale', 'product'])
            ->whereHas('sale', fn ($q) => $q->whereBetween('sale_date', [$start, $end])
                ->where('delivery_status', RevenueRecognitionService::DELIVERED))
            ->get() as $item) {
            $events[] = ['date' => Carbon::parse($item->sale->sale_date), 'seq' => $item->id, 'type' => 'sale_item', 'sale_item' => $item];
        }

        usort($events, fn ($a, $b) => [$a['date']->getTimestamp(), $a['type'] === 'sale_item' ? 1 : 0, $a['seq']]
            <=> [$b['date']->getTimestamp(), $b['type'] === 'sale_item' ? 1 : 0, $b['seq']]);

        $costs = [];
        foreach ($events as $event) {
            if ($event['type'] === 'inventory') {
                $record = $event['record'];
                $key = $this->itemKey($record);
                if (($record->quantity_in ?? 0) > 0) {
                    $layers[$key][] = [(float) $record->quantity_in, max(0, (float) ($record->unit_cost ?? 0))];
                }
                $toConsume = (float) ($record->quantity_out ?? 0);
                if ($toConsume > 0 && isset($layers[$key])) {
                    $layers[$key] = $this->consumeFromLayers($layers[$key], $toConsume);
                }

                continue;
            }

            $item = $event['sale_item'];
            $quantity = (float) $item->quantity_sold;
            if ($quantity <= 0) {
                continue;
            }
            $layerKey = $this->resolveLayerKey($layers, $item->product_id, $item->product->name ?? '');
            if ($layerKey === null) {
                continue;
            }
            [$layers[$layerKey], $cost] = $this->consumeFromLayersAndReturnCost($layers[$layerKey], $quantity);
            $costs[$item->id] = round($cost, 2);
        }

        return $costs;
    }

    /** Inventory may be tracked against a product id or, when unlinked, against an item name. */
    protected function resolveLayerKey(array $layers, $productId, string $productName): ?string
    {
        $byId = $productId ? (string) $productId : null;
        if ($byId !== null && isset($layers[$byId])) {
            return $byId;
        }
        $byName = 'name:'.$productName;

        return isset($layers[$byName]) ? $byName : null;
    }

    /**
     * Consume quantity from layers (FIFO), return remaining layers.
     */
    protected function consumeFromLayers(array $layers, float $quantity): array
    {
        $remaining = $quantity;
        $newLayers = [];
        foreach ($layers as [$qty, $cost]) {
            if ($remaining <= 0) {
                $newLayers[] = [$qty, $cost];

                continue;
            }
            if ($remaining >= $qty) {
                $remaining -= $qty;

                continue;
            }
            $newLayers[] = [$qty - $remaining, $cost];
            $remaining = 0;
        }

        return $newLayers;
    }

    /**
     * Consume quantity from layers; return [newLayers, cost].
     */
    protected function consumeFromLayersAndReturnCost(array $layers, float $quantity): array
    {
        $remaining = $quantity;
        $cost = 0;
        $newLayers = [];
        foreach ($layers as [$qty, $unitCost]) {
            if ($remaining <= 0) {
                $newLayers[] = [$qty, $unitCost];

                continue;
            }
            if ($remaining >= $qty) {
                $cost += $qty * $unitCost;
                $remaining -= $qty;

                continue;
            }
            $take = $remaining;
            $cost += $take * $unitCost;
            $newLayers[] = [$qty - $take, $unitCost];
            $remaining = 0;
        }

        return [$newLayers, $cost];
    }

    protected function itemKey(InventoryRecord $record): string
    {
        if ($record->product_id) {
            return (string) $record->product_id;
        }

        return 'name:'.($record->item_name ?? '');
    }
}
