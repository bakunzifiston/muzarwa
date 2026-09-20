<?php

namespace App\Services;

use App\Models\CashAccount;
use App\Models\EquityMovement;
use App\Models\Expense;
use App\Models\FinancialReport;
use App\Models\FixedAsset;
use App\Models\InventoryRecord;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\OpeningBalance;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\SupplierPayment;
use App\Statements\IncomeStatementDefinition;
use App\Statements\StatementSupport;
use Carbon\CarbonImmutable;

/**
 * Statement of financial position at each month end of the reporting year.
 *
 * The opening position comes from `opening_balances`, one dated cut-over set, so the
 * historical workbooks never have to be reconciled or imported. Anything dated on or
 * before that cut-over is assumed to be inside the opening figures and is excluded from
 * the transaction queries; counting both would double the opening position.
 *
 * Retained earnings reuse IncomeStatementDefinition::snapshot() rather than recomputing
 * profit independently. That is what makes the statements articulate: the movement in
 * retained earnings is, by construction, the net income the income statement reports.
 */
class BalanceSheetService
{
    public function __construct(
        private readonly IncomeStatementDefinition $incomeStatement,
        private readonly InventoryValuationService $valuation,
        private readonly ProductionCostingService $production,
    ) {}

    /** Cached per report so a twelve-column statement does not re-read the cut-over set. */
    private ?array $openingCache = null;

    public function snapshot(FinancialReport $report): array
    {
        [$start, $end] = StatementSupport::window($report);
        $monthEnds = StatementSupport::monthEnds($report);
        $this->openingCache = null;
        $opening = $this->opening($start);

        $available = array_map(fn (CarbonImmutable $m) => ! $m->startOfMonth()->greaterThan($end), $monthEnds);
        $series = function (callable $at) use ($monthEnds, $available): array {
            $out = [];
            foreach ($monthEnds as $i => $monthEnd) {
                $out[] = $available[$i] ? round($at($monthEnd), 2) : null;
            }

            return $out;
        };

        $rows = [];
        $put = function (string $section, string $label, array $values) use (&$rows): void {
            $rows[] = ['section' => $section, 'label' => $label, 'values' => $values];
        };

        $put('assets_current', 'Cash and bank', $series(fn ($d) => $this->cashBalanceAt($d)));
        $put('assets_current', 'Accounts receivable', $series(fn ($d) => $this->metricAt('receivables', $d)));
        $put('assets_current', 'Inventory', $series(fn ($d) => $this->metricAt('inventory', $d)));

        $put('assets_non_current', 'Fixed assets at cost', $series(fn ($d) => $this->assetCostAt($d)));
        // Contra-asset: carried negative so the section total subtracts it.
        $put('assets_non_current', 'Less accumulated depreciation', $series(fn ($d) => -1 * $this->accumulatedDepreciationAt($d)));

        $put('liabilities_current', 'Accounts payable', $series(fn ($d) => $this->metricAt('payables', $d)));
        $put('liabilities_current', 'Customer prepayments', $series(fn ($d) => $this->metricAt('prepayments', $d)));
        $put('liabilities_current', 'Accrued expenses', $series(fn ($d) => $this->metricAt('accruals', $d)));
        $put('liabilities_current', 'Loans due within one year', $series(fn ($d) => $this->loansAt($d, true)));
        $put('liabilities_non_current', 'Loans due after one year', $series(fn ($d) => $this->loansAt($d, false)));

        $put('equity', 'Share capital', $series(fn ($d) => $this->equityAt($d, EquityMovement::TYPE_SHARE_CAPITAL, (float) ($opening[OpeningBalance::SHARE_CAPITAL] ?? 0))));
        $put('equity', 'Owner contributions', $series(fn ($d) => $this->equityAt($d, EquityMovement::TYPE_CONTRIBUTION, 0)));
        // Drawings reduce equity.
        $put('equity', 'Owner drawings', $series(fn ($d) => -1 * $this->equityAt($d, EquityMovement::TYPE_DRAWING, 0)));
        $put('equity', 'Retained earnings brought forward', $series(fn () => (float) ($opening[OpeningBalance::RETAINED_EARNINGS] ?? 0)));
        $put('equity', 'Profit for the period to date', $this->mask($this->cumulativeProfit($report), $available));

        return [$rows, $this->notes($report, $start, $end, $opening)];
    }

    // ── Public metric accessors, shared with CashFlowService ─────────────────

    /**
     * Load the cut-over set for a report before reading any balance.
     *
     * Callers other than snapshot() must do this first, otherwise no opening position is
     * applied and transactions before the cut-over are counted that should not be.
     */
    public function prime(FinancialReport $report): void
    {
        [$start] = StatementSupport::window($report);
        $this->openingCache = null;
        $this->opening($start);
    }

    /**
     * One named balance at a date. Used both for the balance sheet itself and for the
     * movements in the indirect cash flow reconciliation, so both read the same figures.
     */
    public function metricAt(string $metric, CarbonImmutable $asOf): float
    {
        return match ($metric) {
            'cash' => $this->cashBalanceAt($asOf),
            'receivables' => $this->receivablesAt($asOf),
            'inventory' => $this->inventoryAt($asOf),
            'payables' => $this->payablesAt($asOf),
            'prepayments' => $this->prepaymentsAt($asOf),
            'accruals' => $this->accruedExpensesAt($asOf),
            default => 0.0,
        };
    }

    /**
     * Cash is the opening position plus every recorded movement: collections in, supplier
     * and expense payments out, loan draws in and repayments out, capital in, drawings out,
     * asset purchases out and disposal proceeds in.
     */
    public function cashBalanceAt(CarbonImmutable $asOf): float
    {
        $opening = $this->openingCache ?? [];
        $d = $asOf->toDateString();

        $in = (float) $this->after(SalePayment::query(), 'paid_at', $d)->sum('amount');
        $in += (float) $this->after(Loan::query(), 'start_date', $d)->sum('principal');
        $in += (float) $this->after(EquityMovement::query()->whereIn('type', [EquityMovement::TYPE_SHARE_CAPITAL, EquityMovement::TYPE_CONTRIBUTION]), 'occurred_at', $d)->sum('amount');
        $in += (float) $this->after(FixedAsset::query()->whereNotNull('disposal_date'), 'disposal_date', $d)->sum('disposal_proceeds');

        $out = (float) $this->after(SupplierPayment::query(), 'paid_at', $d)->sum('amount');
        $out += (float) $this->after(Expense::query()->whereNotNull('paid_at')->where('currency', 'RWF'), 'paid_at', $d)->sum('amount');
        $out += (float) $this->after(LoanRepayment::query(), 'paid_at', $d)->sum('total_amount');
        $out += (float) $this->after(EquityMovement::query()->where('type', EquityMovement::TYPE_DRAWING), 'occurred_at', $d)->sum('amount');
        $out += (float) $this->after(FixedAsset::query(), 'acquisition_date', $d)->sum('cost');

        return round((float) ($opening[OpeningBalance::CASH] ?? 0) + $in - $out, 2);
    }

    /** Net income per month, straight from the income statement snapshot. */
    public function monthlyProfit(FinancialReport $report): array
    {
        [$rows] = $this->incomeStatementRows($report);

        $monthly = array_fill(0, 12, 0.0);
        foreach ($rows as $row) {
            $sign = $row['section'] === 'revenue' ? 1 : -1;
            foreach (array_slice($row['values'], 0, 12) as $i => $value) {
                if ($value !== null) {
                    $monthly[$i] = round($monthly[$i] + $sign * (float) $value, 2);
                }
            }
        }

        return $monthly;
    }

    /** The depreciation charge per month, for the cash flow add-back. */
    public function monthlyDepreciation(FinancialReport $report): array
    {
        [$start] = StatementSupport::window($report);
        $monthly = array_fill(0, 12, 0.0);

        foreach (FixedAsset::query()->get() as $asset) {
            for ($i = 0; $i < 12; $i++) {
                $monthly[$i] = round($monthly[$i] + $asset->chargeForMonth($start->addMonths($i)), 2);
            }
        }

        return $monthly;
    }

    // ── Internals ────────────────────────────────────────────────────────────

    /**
     * Restrict a query to activity after the cut-over and up to a date.
     *
     * Transactions dated on or before the cut-over are already inside the opening
     * balances; including them as well would double the opening position.
     */
    private function after($query, string $column, string $asOf)
    {
        $cutover = $this->openingCache['_as_of'] ?? null;

        return $query
            ->when($cutover !== null, fn ($q) => $q->whereDate($column, '>', $cutover))
            ->whereDate($column, '<=', $asOf);
    }

    private function opening(CarbonImmutable $start): array
    {
        if ($this->openingCache !== null) {
            return $this->openingCache;
        }

        $date = OpeningBalance::query()->whereDate('as_of_date', '<=', $start->toDateString())->max('as_of_date');
        if (! $date) {
            return $this->openingCache = [];
        }

        // max() can come back as a full datetime depending on driver; whereDate() against
        // that string matches nothing and the opening position silently disappears.
        $date = CarbonImmutable::parse($date)->toDateString();

        $values = OpeningBalance::query()->whereDate('as_of_date', $date)
            ->pluck('amount', 'line_key')->map(fn ($v) => (float) $v)->all();
        $values['_as_of'] = $date;

        return $this->openingCache = $values;
    }

    /**
     * Receivables are recognised on the same basis as revenue: a claim exists once the
     * goods are delivered. Money taken for an undelivered order is a customer prepayment,
     * a liability, not a negative receivable.
     */
    private function receivablesAt(CarbonImmutable $asOf): float
    {
        $d = $asOf->toDateString();
        $billed = (float) $this->after(Sale::query()->where('delivery_status', RevenueRecognitionService::DELIVERED), 'sale_date', $d)->sum('total_revenue');

        return round((float) ($this->openingCache[OpeningBalance::ACCOUNTS_RECEIVABLE] ?? 0) + $billed - $this->collectionsOnDelivered($d), 2);
    }

    private function prepaymentsAt(CarbonImmutable $asOf): float
    {
        $d = $asOf->toDateString();
        $all = (float) $this->after(SalePayment::query(), 'paid_at', $d)->sum('amount');

        return round($all - $this->collectionsOnDelivered($d), 2);
    }

    private function collectionsOnDelivered(string $asOf): float
    {
        return (float) $this->after(SalePayment::query(), 'paid_at', $asOf)
            ->whereHas('sale', fn ($q) => $q->where('delivery_status', RevenueRecognitionService::DELIVERED)
                ->whereDate('sale_date', '<=', $asOf))
            ->sum('amount');
    }

    private function accruedExpensesAt(CarbonImmutable $asOf): float
    {
        $d = $asOf->toDateString();
        $incurred = (float) $this->after(Expense::query()->where('currency', 'RWF'), 'expense_date', $d)->sum('amount');
        $paid = (float) $this->after(Expense::query()->where('currency', 'RWF')->whereNotNull('paid_at'), 'paid_at', $d)->sum('amount');

        return round($incurred - $paid, 2);
    }

    /**
     * Inventory is raw materials still in the store plus finished goods not yet sold.
     *
     * Production moves value between the two: counting only raw materials would make every
     * batch look like value disappearing, and the statement would stop balancing.
     */
    private function inventoryAt(CarbonImmutable $asOf): float
    {
        // Net of sales so resold goods are relieved from stock. For manufactured goods this
        // equals the plain FIFO value, because a finished product never matches a
        // raw-material layer; those are relieved through the finished-goods figure instead.
        $raw = $this->valuation->getFifoValueNetOfSalesAt($asOf->toDateString());
        $finished = $this->production->finishedGoodsValueAt($asOf->toDateString());

        return round((float) ($this->openingCache[OpeningBalance::INVENTORY] ?? 0) + $raw + $finished, 2);
    }

    private function payablesAt(CarbonImmutable $asOf): float
    {
        $d = $asOf->toDateString();
        $billed = (float) $this->after(InventoryRecord::query(), 'record_date', $d)->sum('total_amount');
        $paid = (float) $this->after(SupplierPayment::query(), 'paid_at', $d)->sum('amount');

        return round((float) ($this->openingCache[OpeningBalance::ACCOUNTS_PAYABLE] ?? 0) + $billed - $paid, 2);
    }

    private function assetCostAt(CarbonImmutable $asOf): float
    {
        $cost = (float) $this->after(FixedAsset::query(), 'acquisition_date', $asOf->toDateString())
            ->where(fn ($q) => $q->whereNull('disposal_date')->orWhereDate('disposal_date', '>', $asOf->toDateString()))
            ->sum('cost');

        return round((float) ($this->openingCache[OpeningBalance::FIXED_ASSETS_COST] ?? 0) + $cost, 2);
    }

    private function accumulatedDepreciationAt(CarbonImmutable $asOf): float
    {
        $total = (float) ($this->openingCache[OpeningBalance::ACCUMULATED_DEPRECIATION] ?? 0);

        foreach ($this->after(FixedAsset::query(), 'acquisition_date', $asOf->toDateString())
            ->where(fn ($q) => $q->whereNull('disposal_date')->orWhereDate('disposal_date', '>', $asOf->toDateString()))
            ->get() as $asset) {
            $total = round($total + $asset->accumulatedDepreciationAt($asOf), 2);
        }

        return $total;
    }

    /**
     * Outstanding borrowings split by maturity. The opening figure is treated as current
     * because the cut-over set carries no maturity information.
     */
    private function loansAt(CarbonImmutable $asOf, bool $current): float
    {
        $total = $current ? (float) ($this->openingCache[OpeningBalance::LOANS_OUTSTANDING] ?? 0) : 0.0;

        foreach ($this->after(Loan::query(), 'start_date', $asOf->toDateString())->get() as $loan) {
            $total = round($total + ($current ? $loan->currentPortionAt($asOf) : $loan->nonCurrentPortionAt($asOf)), 2);
        }

        return $total;
    }

    private function equityAt(CarbonImmutable $asOf, string $type, float $opening): float
    {
        return round($opening + (float) $this->after(EquityMovement::query()->where('type', $type), 'occurred_at', $asOf->toDateString())->sum('amount'), 2);
    }

    /** Profit accumulated from the start of the reporting year to each month end. */
    private function cumulativeProfit(FinancialReport $report): array
    {
        $running = 0.0;

        return array_map(function ($value) use (&$running) {
            $running = round($running + $value, 2);

            return $running;
        }, $this->monthlyProfit($report));
    }

    /** The income statement for the same period, built once and reused. */
    private function incomeStatementRows(FinancialReport $report): array
    {
        return $this->incomeStatement->snapshot(new FinancialReport([
            'kind' => FinancialReport::KIND_INCOME_STATEMENT,
            'mode' => FinancialReport::MODE_ACTUAL,
            'source' => FinancialReport::SOURCE_SYSTEM,
            'start_date' => $report->start_date,
            'as_of' => $report->as_of,
        ]));
    }

    private function mask(array $values, array $available): array
    {
        $out = [];
        foreach ($values as $i => $value) {
            $out[] = ($available[$i] ?? false) ? $value : null;
        }

        return $out;
    }

    private function notes(FinancialReport $report, CarbonImmutable $start, CarbonImmutable $end, array $opening): array
    {
        $notes = [
            'Frozen system snapshot as of '.$end->toDateString().'. Each column is the position at that month end, not a movement during the month.',
            $opening === []
                ? 'No opening balances are recorded on or before '.$start->toDateString().'. The statement therefore assumes the business owned nothing and owed nothing at that date, which is almost certainly wrong. Record the cut-over figures before relying on this report.'
                : 'Opening position taken from the cut-over balances dated '.$opening['_as_of'].'. Transactions on or before that date are assumed to be inside those figures and are not counted again.',
            'Profit for the period uses the same delivered-sales revenue and FIFO cost of sales as the income statement, so the movement in retained earnings equals the net income reported there.',
            'Loans are split into current and non-current by time remaining to maturity, apportioned straight-line in the absence of a full amortisation schedule.',
            'The balance check line must be zero. A non-zero figure means the opening balances do not balance, or a subledger is incomplete. All amounts are RWF.',
        ];

        // When the statement does not balance, name the usual causes rather than leaving the
        // preparer to guess. These are data-completeness problems, not arithmetic ones.
        $uncosted = \App\Models\SaleItem::query()
            ->whereHas('sale', fn ($q) => $q->where('delivery_status', RevenueRecognitionService::DELIVERED)
                ->whereDate('sale_date', '>=', $start->toDateString())
                ->whereDate('sale_date', '<=', $end->toDateString()))
            ->whereNull('line_cogs')->count();

        if ($uncosted > 0) {
            $notes[] = $uncosted.' delivered sale lines have no cost, usually because their production batch records no raw materials. Those sales add profit with nothing against them and their stock carries no value, so the statement will not balance until the batches are completed.';
        }

        $consumed = 0.0;
        foreach (\App\Models\Production::query()->get(['id', 'raw_materials_used', 'inventory_record_id']) as $batch) {
            foreach ($this->production->materials($batch) as $material) {
                $consumed += (float) ($material['quantity_used'] ?? 0);
            }
        }
        $relieved = (float) \App\Models\InventoryRecord::query()->sum('quantity_out');
        if (abs($consumed - $relieved) > 1) {
            $notes[] = 'Production records consume '.number_format($consumed, 2).' units of raw material while the inventory lots show '.number_format($relieved, 2).' units issued, a difference of '.number_format(abs($consumed - $relieved), 2).' units. The two subledgers disagree, so inventory on this statement cannot be relied on until they are reconciled.';
        }

        $declared = (float) ($opening[OpeningBalance::CASH] ?? 0);
        $accounts = (float) CashAccount::query()->sum('opening_balance');
        if ($opening !== [] && abs($declared - $accounts) >= 0.01) {
            $notes[] = 'Cash opening balance of RWF '.number_format($declared, 2).' does not agree with the sum of cash account opening balances, RWF '.number_format($accounts, 2).'. The cut-over figure is used; reconcile the accounts.';
        }

        if ($opening !== [] && abs($check = OpeningBalance::balanceCheck($opening['_as_of'])) >= 0.01) {
            $notes[] = 'The recorded opening balances do not themselves balance; they are out by RWF '.number_format($check, 2).'. Every column of this statement will be out by that amount until they are corrected.';
        }

        return $notes;
    }
}
