<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalePaymentController extends Controller
{
    public function store(Request $request, Sale $sale): RedirectResponse
    {
        $this->authorizeSale($request, $sale);

        $balance = max(0, round((float) $sale->total_revenue - (float) $sale->payments()->sum('amount'), 2));

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'max:'.max($balance, 0.01)],
            'method' => ['required', Rule::in(SalePayment::METHODS)],
            'paid_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'amount.max' => 'Amount exceeds the outstanding balance of '.number_format($balance, 0).' RWF.',
        ]);

        $sale->payments()->create(array_merge($validated, [
            'recorded_by' => auth()->id(),
        ]));

        $sale->recalculatePaymentState();

        return redirect()
            ->route('admin.sales.show', $sale)
            ->with('status', number_format((float) $validated['amount'], 0).' RWF payment recorded. Balance: '.number_format($sale->balance_due, 0).' RWF.');
    }

    public function destroy(Request $request, Sale $sale, SalePayment $payment): RedirectResponse
    {
        abort_unless($payment->sale_id === $sale->id, 404);
        // Reversing a recorded payment is reserved for owner/manager.
        abort_unless($request->user()->can('delete-records'), 403);

        $payment->delete();
        $sale->recalculatePaymentState();

        return redirect()
            ->route('admin.sales.show', $sale)
            ->with('status', 'Payment removed. Balance recalculated: '.number_format($sale->balance_due, 0).' RWF.');
    }

    /**
     * A sales user may only record payments against sales they recorded.
     */
    private function authorizeSale(Request $request, Sale $sale): void
    {
        if ($request->user()->can('manage-all-sales')) {
            return;
        }

        abort_unless($sale->created_by === $request->user()->id, 403);
    }
}
