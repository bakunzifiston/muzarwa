<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductionRequest;
use App\Http\Requests\Admin\UpdateProductionRequest;
use App\Models\Employee;
use App\Models\InventoryRecord;
use App\Models\Product;
use App\Models\Production;
use App\Models\Sale;
use App\Support\TablePageSize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('search', '');
        $sort = in_array($request->query('sort'), ['batch_id', 'quantity_produced', 'damaged', 'production_date', 'created_at'], true)
            ? $request->query('sort')
            : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $productions = Production::query()
            ->with('product')
            ->unless($request->user()->can('manage-all-production'),
                fn ($query) => $query->where('created_by', $request->user()->id))
            ->when($search !== '', function ($query) use ($search) {
                $query->where('batch_id', 'like', '%'.$search.'%')
                    ->orWhere('responsible_staff', 'like', '%'.$search.'%')
                    ->orWhere('barcode', 'like', '%'.$search.'%');
            })
            ->orderBy($sort, $direction)
            ->paginate(TablePageSize::resolve($request, 15))
            ->withQueryString();

        return view('admin.productions.index', compact('productions', 'search', 'sort', 'direction'));
    }

    public function create(): View
    {
        $products = Product::orderBy('type')->pluck('type', 'id');
        $productBarcodes = Product::orderBy('type')->pluck('barcode', 'id');
        $inventoryOptions = $this->inventoryOptions();

        $employees = Employee::query()->orderBy('name')->get(['id', 'name', 'position']);

        return view('admin.productions.create', compact('products', 'productBarcodes', 'inventoryOptions', 'employees'));
    }

    public function store(StoreProductionRequest $request): RedirectResponse
    {
        // One transaction: if material deduction fails, the batch insert rolls back too (no orphan).
        DB::transaction(fn () => Production::create($request->validated()));

        return redirect()->route('admin.productions.index')->with('status', 'Production created successfully.');
    }

    public function show(Production $production): View
    {
        $this->authorizeProduction($production);
        $production->load('product');

        // Resolve consumed raw materials once (no N+1), with per-line cost.
        $raw = Production::normalizeMaterials($production->inventory_record_id);
        $records = InventoryRecord::whereIn('id', collect($raw)->pluck('inventory_id')->filter()->all())
            ->get()
            ->keyBy('id');

        $materials = collect($raw)->map(function ($item) use ($records) {
            $record = $records[$item['inventory_id'] ?? null] ?? null;
            $qty = (float) ($item['quantity_used'] ?? 0);
            $unitCost = $record?->unit_cost !== null ? (float) $record->unit_cost : null;

            return [
                'record' => $record,
                'name' => $record?->item_name ?? 'Unknown',
                'quantity' => $qty,
                'unit_cost' => $unitCost,
                'line_cost' => $unitCost !== null ? round($qty * $unitCost, 2) : null,
            ];
        });

        $materialsCost = $materials->sum(fn ($m) => $m['line_cost'] ?? 0);

        [$batchSales, $batchSalesQty, $batchSalesRevenue] = $this->batchSales($production);

        // Reconciliation: units that left the batch (produced − remaining) should equal what
        // the sales ledger says was sold from it. A gap hints at a data-integrity issue.
        $unitsLeftBatch = round((float) $production->quantity_produced - (float) $production->quantity_remaining, 2);
        $salesReconciles = abs($unitsLeftBatch - $batchSalesQty) < 0.01;

        return view('admin.productions.show', compact(
            'production', 'materials', 'materialsCost',
            'batchSales', 'batchSalesQty', 'batchSalesRevenue', 'unitsLeftBatch', 'salesReconciles',
        ));
    }

    public function edit(Production $production): View
    {
        $this->authorizeProduction($production);
        $products = Product::orderBy('type')->pluck('type', 'id');
        $productBarcodes = Product::orderBy('type')->pluck('barcode', 'id');
        $inventoryOptions = $this->inventoryOptions($production);

        $employees = Employee::query()->orderBy('name')->get(['id', 'name', 'position']);

        return view('admin.productions.edit', compact('production', 'products', 'productBarcodes', 'inventoryOptions', 'employees'));
    }

    public function update(UpdateProductionRequest $request, Production $production): RedirectResponse
    {
        $this->authorizeProduction($production);
        DB::transaction(fn () => $production->update($request->validated()));

        return redirect()->route('admin.productions.show', $production)->with('status', 'Production updated successfully.');
    }

    public function destroy(Production $production): RedirectResponse
    {
        abort_unless(request()->user()->can('delete-records'), 403);

        $production->delete();

        return redirect()->route('admin.productions.index')->with('status', 'Production deleted successfully.');
    }

    /**
     * Every sale line drawn from this batch, plus legacy sales that point at the batch
     * directly but never created sale-item rows (deduped so a sale never appears twice).
     * Returns [rows, totalQty, totalRevenue].
     *
     * @return array{0: \Illuminate\Support\Collection, 1: float, 2: float}
     */
    private function batchSales(Production $production): array
    {
        $production->loadMissing(['saleItems.sale.creator']);

        $rows = $production->saleItems
            ->filter(fn ($item) => $item->sale !== null)
            ->map(fn ($item) => [
                'sale' => $item->sale,
                'sales_id' => $item->sale->sales_id,
                'sale_date' => $item->sale->sale_date,
                'customer' => $item->sale->customer_name,
                'quantity' => (float) $item->quantity_sold,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
                'payment_status' => $item->sale->payment_status,
                'recorded_by' => $item->sale->creator?->name,
            ]);

        // Legacy fallback: sales linked at the header level with no sale_items for this batch.
        $seenSaleIds = $production->saleItems->pluck('sale_id')->filter()->all();
        $legacy = Sale::with('creator')
            ->where('production_id', $production->id)
            ->when($seenSaleIds, fn ($q) => $q->whereNotIn('id', $seenSaleIds))
            ->get()
            ->map(fn ($sale) => [
                'sale' => $sale,
                'sales_id' => $sale->sales_id,
                'sale_date' => $sale->sale_date,
                'customer' => $sale->customer_name,
                'quantity' => (float) $sale->quantity_sold,
                'unit_price' => (float) $sale->selling_price,
                'line_total' => (float) $sale->total_revenue,
                'payment_status' => $sale->payment_status,
                'recorded_by' => $sale->creator?->name,
            ]);

        $all = $rows->concat($legacy)
            ->sortByDesc(fn ($row) => $row['sale_date'])
            ->values();

        return [$all, round($all->sum('quantity'), 2), round($all->sum('line_total'), 2)];
    }

    /**
     * Raw-material lots available to consume, keyed by id, with the remaining on-hand
     * quantity baked into the label. Depleted lots (0 remaining) are hidden so you can
     * only pick stock that actually exists — except any lot already attached to the
     * batch being edited, which is kept so its existing row still resolves.
     *
     * @return array<int|string, string>
     */
    private function inventoryOptions(?Production $production = null): array
    {
        $keepIds = collect(Production::normalizeMaterials($production?->inventory_record_id ?? []))
            ->pluck('inventory_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        return InventoryRecord::query()
            ->orderBy('item_name')
            ->orderBy('record_date')
            ->get(['id', 'item_name', 'lot_number', 'quantity_in', 'quantity_out'])
            ->filter(fn (InventoryRecord $record) => $record->remainingQuantity() > 0 || in_array((int) $record->id, $keepIds, true))
            ->mapWithKeys(function (InventoryRecord $record) {
                $remaining = rtrim(rtrim(number_format($record->remainingQuantity(), 2), '0'), '.');
                $name = $record->item_name;
                if ($record->lot_number) {
                    $name .= ' · '.$record->lot_number;
                }

                return [$record->id => "{$name} — {$remaining} in stock"];
            })
            ->all();
    }

    /**
     * A production user may only touch batches they recorded; owner/manager may touch any.
     */
    private function authorizeProduction(Production $production): void
    {
        if (request()->user()->can('manage-all-production')) {
            return;
        }

        abort_unless($production->created_by === request()->user()->id, 403);
    }
}
