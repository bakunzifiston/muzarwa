<?php

namespace App\Services;

use App\Models\InventoryRecord;
use App\Models\Production;
use Illuminate\Support\Facades\DB;

/**
 * What a unit of finished goods actually cost to make.
 *
 * Muzarwa manufactures rather than resells: every inventory record is a raw material, and
 * finished goods only exist once a production batch consumes those materials. So the cost
 * of a sale cannot be found by consuming purchase layers of the product sold — no such
 * layers exist. It has to come down the chain the data already records:
 *
 *   raw-material lot cost  ->  batch cost  ->  cost per unit produced  ->  cost of a sale line
 *
 * Because `sale_items.production_id` names the batch each sale came from, this is exact
 * per batch rather than an averaging approximation.
 */
class ProductionCostingService
{
    /** @var array<int, float|null> */
    private array $unitCostCache = [];

    /** @var array<int, float>|null */
    private ?array $lotUnitCosts = null;

    /**
     * Cost of one unit produced by a batch, or null when it cannot be established.
     *
     * Null rather than zero matters: a batch whose material lots are missing would
     * otherwise look free to make, and every sale from it infinitely profitable.
     */
    public function unitCost(Production $production): ?float
    {
        if (array_key_exists($production->id, $this->unitCostCache)) {
            return $this->unitCostCache[$production->id];
        }

        $quantity = (float) $production->quantity_produced;
        if ($quantity <= 0) {
            return $this->unitCostCache[$production->id] = null;
        }

        $batchCost = $this->batchCost($production);

        return $this->unitCostCache[$production->id] = $batchCost === null
            ? null
            : round($batchCost / $quantity, 4);
    }

    /** Total raw-material cost consumed by a batch, or null if no material is costable. */
    public function batchCost(Production $production): ?float
    {
        $materials = $this->materials($production);
        if ($materials === []) {
            return null;
        }

        $costs = $this->lotUnitCosts();
        $total = 0.0;
        $costed = 0;

        foreach ($materials as $material) {
            $lotId = (int) ($material['inventory_id'] ?? 0);
            $used = (float) ($material['quantity_used'] ?? 0);
            if ($lotId <= 0 || $used <= 0 || ! isset($costs[$lotId])) {
                continue;
            }
            $total += $used * $costs[$lotId];
            $costed++;
        }

        // A batch where not one material could be priced is not costable at all.
        return $costed === 0 ? null : round($total, 2);
    }

    /**
     * Normalise the materials list.
     *
     * `raw_materials_used` is sometimes a JSON array and sometimes a JSON string holding
     * one, and `inventory_record_id` carries the same shape with string values, so both
     * are read defensively.
     */
    public function materials(Production $production): array
    {
        foreach ([$production->raw_materials_used, $production->inventory_record_id] as $source) {
            $value = $source;
            if (is_string($value)) {
                $value = json_decode($value, true);
            }
            if (is_array($value) && $value !== []) {
                return array_values(array_filter($value, 'is_array'));
            }
        }

        return [];
    }

    /**
     * Unit cost of every raw-material lot, keyed by inventory record id.
     *
     * Where a lot records only a total and a quantity, the unit cost is derived from them.
     */
    private function lotUnitCosts(): array
    {
        if ($this->lotUnitCosts !== null) {
            return $this->lotUnitCosts;
        }

        $costs = [];
        InventoryRecord::query()
            ->select(['id', 'unit_cost', 'total_amount', 'quantity_in'])
            ->chunkById(500, function ($records) use (&$costs): void {
                foreach ($records as $record) {
                    $unit = (float) ($record->unit_cost ?? 0);
                    if ($unit <= 0) {
                        $quantity = (float) $record->quantity_in;
                        $total = (float) $record->total_amount;
                        $unit = $quantity > 0 && $total > 0 ? $total / $quantity : 0.0;
                    }
                    if ($unit > 0) {
                        $costs[(int) $record->id] = $unit;
                    }
                }
            });

        return $this->lotUnitCosts = $costs;
    }

    /**
     * Value of finished goods still on hand at a date.
     *
     * Production turns raw materials into finished goods: one asset becomes another. If the
     * balance sheet counted only raw materials, every batch would look like value being
     * destroyed. Stock is reconstructed as units produced less units sold from that batch up
     * to the date, valued at the batch cost per unit, which is the same basis as COGS.
     */
    public function finishedGoodsValueAt(string $asOfDate): float
    {
        $sold = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.delivery_status', RevenueRecognitionService::DELIVERED)
            ->whereDate('sales.sale_date', '<=', $asOfDate)
            ->whereNotNull('sale_items.production_id')
            ->groupBy('sale_items.production_id')
            ->select('sale_items.production_id', DB::raw('SUM(sale_items.quantity_sold) as sold_qty'))
            ->pluck('sold_qty', 'production_id')
            ->map(fn ($v) => (float) $v)
            ->all();

        $total = 0.0;

        Production::query()
            ->whereDate('production_date', '<=', $asOfDate)
            ->select(['id', 'quantity_produced', 'damaged', 'raw_materials_used', 'inventory_record_id'])
            ->chunkById(300, function ($batches) use (&$total, $sold): void {
                foreach ($batches as $batch) {
                    $unit = $this->unitCost($batch);
                    if ($unit === null) {
                        continue;
                    }
                    $onHand = (float) $batch->quantity_produced
                        - (float) ($batch->damaged ?? 0)
                        - (float) ($sold[$batch->id] ?? 0);

                    if ($onHand > 0) {
                        $total = round($total + $onHand * $unit, 2);
                    }
                }
            });

        return $total;
    }

    /** Unit costs for many batches at once, keyed by production id. */
    public function unitCostsFor(array $productionIds): array
    {
        $out = [];
        Production::query()->whereIn('id', array_filter($productionIds))
            ->get(['id', 'quantity_produced', 'raw_materials_used', 'inventory_record_id'])
            ->each(function (Production $production) use (&$out): void {
                $out[$production->id] = $this->unitCost($production);
            });

        return $out;
    }
}
