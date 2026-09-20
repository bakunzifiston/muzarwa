<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSaleRequest;
use App\Http\Requests\Admin\UpdateSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Production;
use App\Models\Sale;
use App\Services\SaleWorkflowService;
use App\Support\TablePageSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(private readonly SaleWorkflowService $workflow) {}

    public function index(Request $request): View
    {
        $search = (string) $request->string('search', '');

        $deliveryStatus = in_array($request->query('delivery_status'), ['Delivered', 'Pending', 'In Transit', 'Returned'], true)
            ? $request->query('delivery_status') : '';
        $paymentStatus = in_array($request->query('payment_status'), ['Paid', 'Partially Paid', 'Pending', 'Credit'], true)
            ? $request->query('payment_status') : '';

        $sales = Sale::query()
            ->withCount('items')
            ->unless($request->user()->can('manage-all-sales'),
                fn ($query) => $query->where('created_by', $request->user()->id))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('sales_id', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%')
                        ->orWhere('invoice_number', 'like', '%'.$search.'%')
                        ->orWhere('customer_Phone', 'like', '%'.$search.'%')
                        ->orWhere('barcode', 'like', '%'.$search.'%')
                        ->orWhere('sales_channel', 'like', '%'.$search.'%');
                });
            })
            ->when($deliveryStatus !== '', fn ($q) => $q->where('delivery_status', $deliveryStatus))
            ->when($paymentStatus !== '', fn ($q) => $q->where('payment_status', $paymentStatus))
            ->orderByDesc('id')
            ->paginate(TablePageSize::resolve($request, 15))
            ->withQueryString();

        return view('admin.sales.index', compact('sales', 'search', 'deliveryStatus', 'paymentStatus'));
    }

    public function create(): View
    {
        return view('admin.sales.create', $this->formData());
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $this->workflow->create($request->validated());

        return redirect()->route('admin.sales.index')->with('status', 'Sale created successfully.');
    }

    public function show(Sale $sale): View
    {
        $this->authorizeSale($sale);
        $sale->load(['items.product', 'items.production', 'payments.recordedBy', 'statusEvents.changedBy', 'customer', 'creator']);

        return view('admin.sales.show', compact('sale'));
    }

    public function edit(Sale $sale): View
    {
        $this->authorizeSale($sale);
        $sale->load('items');

        return view('admin.sales.edit', array_merge($this->formData(), compact('sale')));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $this->authorizeSale($sale);
        $this->workflow->update($sale->load('items'), $request->validated());

        return redirect()->route('admin.sales.show', $sale)->with('status', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        // Deleting a financial record is reserved for owner/manager.
        abort_unless(request()->user()->can('delete-records'), 403);

        $sale->delete();

        return redirect()->route('admin.sales.index')->with('status', 'Sale deleted successfully.');
    }

    public function customerNameSuggestions(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q'));

        $fromSales = Sale::query()
            ->when($q !== '', fn ($query) => $query->where('customer_name', 'like', '%'.$q.'%'))
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->distinct()
            ->pluck('customer_name');

        $fromCustomers = Customer::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%'.$q.'%'))
            ->distinct()
            ->pluck('name');

        $names = $fromSales->merge($fromCustomers)->unique()->sort()->values();

        return response()->json($names);
    }

    /**
     * A sales user may only touch sales they recorded; owner/manager may touch any.
     */
    private function authorizeSale(Sale $sale): void
    {
        if (request()->user()->can('manage-all-sales')) {
            return;
        }

        abort_unless($sale->created_by === request()->user()->id, 403);
    }

    private function formData(): array
    {
        $products = Product::orderBy('type')->get(['id', 'type']);
        $productions = Production::query()
            ->where('quantity_remaining', '>', 0)
            ->get(['id', 'product_id', 'batch_id']);

        return compact('products', 'productions');
    }
}
