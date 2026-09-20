<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Production;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleWorkflowService
{
    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $items = $this->normalizeItems($data['items'] ?? []);
            $saleData = $this->buildSaleData($data, $items);

            $sale = Sale::create($saleData);
            $this->createItems($sale, $items);
            $this->syncSaleTotal($sale);
            $this->reconcileLedger($sale, $data['payment_status'] ?? null);

            return $sale->fresh(['items']);
        });
    }

    public function update(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            // Revert old stock deductions before rebuilding items.
            foreach ($sale->items as $oldItem) {
                $production = Production::find($oldItem->production_id);
                if ($production) {
                    $production->quantity_remaining = (float) $production->quantity_remaining + (float) $oldItem->quantity_sold;
                    $production->saveQuietly();

                    StockMovement::record(
                        type: 'sale_reversal',
                        quantity: (float) $oldItem->quantity_sold,
                        productId: $oldItem->product_id,
                        productionId: $oldItem->production_id,
                        referenceType: 'sale_item',
                        referenceId: $oldItem->id,
                        notes: 'Sale ' . $sale->sales_id . ' edited; line restored before rebuild',
                    );
                }
            }

            $sale->items()->delete();

            $items = $this->normalizeItems($data['items'] ?? []);
            $saleData = $this->buildSaleData($data, $items);

            $sale->update($saleData);
            $this->createItems($sale, $items);
            $this->syncSaleTotal($sale);
            $this->reconcileLedger($sale, $data['payment_status'] ?? null);

            return $sale->fresh(['items']);
        });
    }

    private function createItems(Sale $sale, array $items): void
    {
        foreach ($items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'production_id' => $item['production_id'],
                'quantity_sold' => $item['quantity_sold'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['line_total'],
            ]);
        }
    }

    private function buildSaleData(array $data, array $items): array
    {
        $totalRevenue = collect($items)->sum(fn (array $item): float => (float) $item['line_total']);
        $first = $items[0];

        $customer = Customer::resolve(
            name: $data['customer_name'],
            phone: $data['customer_Phone'] ?? null,
            email: $data['customer_email'] ?? null,
            address: $data['delivery_address'] ?? null,
            source: ($data['sales_channel'] ?? '') === 'Online Store' ? 'storefront' : 'admin',
        );

        return [
            'customer_name' => $data['customer_name'],
            'customer_Phone' => $data['customer_Phone'] ?? null,
            'customer_id' => $customer?->id,
            'customer_email' => $data['customer_email'] ?? null,
            'delivery_address' => $data['delivery_address'] ?? null,
            'barcode' => $data['barcode'] ?? ($data['customer_email'] ?? null),
            'invoice_number' => $data['invoice_number'] ?? null,
            'sale_date' => $data['sale_date'],
            'due_date' => $data['due_date'] ?? null,
            'payment_status' => $data['payment_status'],
            'delivery_status' => $data['delivery_status'],
            'sales_channel' => $data['sales_channel'],
            'product_id' => $first['product_id'] ?? null,
            'production_id' => $first['production_id'] ?? null,
            'quantity_sold' => $first['quantity_sold'] ?? 0,
            'selling_price' => $first['unit_price'] ?? 0,
            'total_revenue' => round($totalRevenue, 2),
        ];
    }

    private function normalizeItems(array $items): array
    {
        return collect($items)->map(function ($item) {
            $qty = (float) ($item['quantity_sold'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            return [
                'product_id' => $item['product_id'],
                'production_id' => $item['production_id'],
                'quantity_sold' => $qty,
                'unit_price' => $price,
                'line_total' => round($qty * $price, 2),
            ];
        })->values()->all();
    }

    private function syncSaleTotal(Sale $sale): void
    {
        $sale->update([
            'total_revenue' => round((float) $sale->items()->sum('line_total'), 2),
        ]);
    }

    /**
     * Keep the payments ledger consistent with a manually chosen status:
     * marking a sale "Paid" auto-records the outstanding balance as a payment,
     * then status/amount_paid are re-derived from the ledger.
     */
    private function reconcileLedger(Sale $sale, ?string $requestedStatus): void
    {
        $sale->refresh();

        if ($requestedStatus === 'Paid') {
            $balance = max(0, round((float) $sale->total_revenue - (float) $sale->payments()->sum('amount'), 2));
            if ($balance > 0) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'amount' => $balance,
                    'method' => $this->methodFromChannel($sale->sales_channel),
                    'paid_at' => $sale->sale_date,
                    'notes' => 'Auto-recorded when sale was marked Paid',
                    'recorded_by' => auth()->id(),
                ]);
            }
        }

        $sale->recalculatePaymentState();
    }

    private function methodFromChannel(?string $channel): string
    {
        return match ($channel) {
            'Momo Pay' => 'MoMo',
            'Card' => 'Card',
            'Cash' => 'Cash',
            default => 'Other',
        };
    }
}
