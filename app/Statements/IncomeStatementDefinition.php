<?php

namespace App\Statements;

use App\Contracts\StatementDefinition;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialReport;
use App\Models\FixedAsset;
use App\Models\LoanRepayment;
use App\Models\SaleItem;
use App\Services\InventoryValuationService;
use App\Services\RevenueRecognitionService;
use App\Services\SaleCostingService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Income statement, and the RWAZK financial table which is the same statement with an
 * extra financing section for loan principal and reserve allocations.
 */
class IncomeStatementDefinition implements StatementDefinition
{
    public const SECTIONS = [
        'revenue' => 'Revenue / sales',
        'costs' => 'Cost of goods sold',
        'operating' => 'Operating expenses',
        'other' => 'Other expenses and tax',
        'financing' => 'Loan principal and reserves',
    ];

    public function __construct(
        private readonly RevenueRecognitionService $revenue,
        private readonly InventoryValuationService $valuation,
    ) {}

    public function kind(): string
    {
        return FinancialReport::KIND_INCOME_STATEMENT;
    }

    public function sections(FinancialReport $report): array
    {
        $sections = self::SECTIONS;
        if (! $report->isFinancialTable()) {
            unset($sections['financing']);
        }

        return $sections;
    }

    public function results(FinancialReport $report): array
    {
        $results = [
            ['key' => 'gross', 'label' => 'Gross result', 'after' => 'costs', 'type' => 'result',
                'terms' => [['key' => 'revenue', 'sign' => 1], ['key' => 'costs', 'sign' => -1]]],
            ['key' => 'ebit', 'label' => 'Operating result / EBIT', 'after' => 'operating', 'type' => 'result',
                'terms' => [['key' => 'gross', 'sign' => 1], ['key' => 'operating', 'sign' => -1]]],
            ['key' => 'net', 'label' => $report->isFinancialTable() ? 'Net result before financing allocations' : 'Net income / loss', 'after' => 'other', 'type' => 'result',
                'terms' => [['key' => 'ebit', 'sign' => 1], ['key' => 'other', 'sign' => -1]]],
        ];

        if ($report->isFinancialTable()) {
            $results[] = ['key' => 'after_financing', 'label' => 'Result after loan principal and reserves', 'after' => 'financing', 'type' => 'result',
                'terms' => [['key' => 'net', 'sign' => 1], ['key' => 'financing', 'sign' => -1]]];
        }

        return $results;
    }

    public function annualMode(): string
    {
        return 'sum';
    }

    public function marginBaseKey(): ?string
    {
        return 'revenue';
    }

    public function supportsSnapshot(): bool
    {
        return true;
    }

    public function blankRows(FinancialReport $report): array
    {
        $labels = [
            'revenue' => ['Neza Chilli Oil 80g', 'Neza Processed Chilli 60g', 'Neza Heat (Sauce)', 'Neza Processed Chilli 120g', 'Other product sales'],
            'costs' => ['Fresh red chilli', 'Sunflower oil', 'Onion', 'Labels and stickers', 'Gloves', 'Firewood', 'Garlic', 'Ginger', 'Amabido', 'Pavro', 'Green pepper', 'Preservatives', 'Packaging materials'],
            'operating' => ['Employee salaries', "Entrepreneur's salary", 'Social security', 'Rent (premises)', 'Rent (machines)', 'Other rental costs', 'Maintenance and repair', 'Car costs and petrol', 'Insurance', 'Government fees', 'Energy', 'Transportation', 'Advertising', 'Legal costs', 'Telephone and internet', 'Depreciation / amortisation'],
            'other' => ['Loan interest', 'Business setup costs', 'Income tax provision', 'Other expenses and taxes'],
        ];
        if ($report->isFinancialTable()) {
            $labels['financing'] = ['Loan principal repayments', 'Reserve allocation'];
        }

        return StatementSupport::rowsFromLabels($labels, StatementSupport::columnCount($report));
    }

    /** A frozen snapshot of recorded trading activity. */
    public function snapshot(FinancialReport $report): array
    {
        [$start, $end, $template] = StatementSupport::window($report);

        $rows = [];
        $add = function (string $key, string $section, string $label, string $date, float $amount) use (&$rows, $template, $start): void {
            $index = (CarbonImmutable::parse($date)->year - $start->year) * 12 + CarbonImmutable::parse($date)->month - $start->month;
            $rows[$key] ??= ['section' => $section, 'label' => $label, 'values' => $template];
            $rows[$key]['values'][$index] = round($rows[$key]['values'][$index] + $amount, 2);
        };

        $salesCount = 0;
        $difference = 0;
        $this->revenue->deliveredSales($start->toDateString(), $end->toDateString())
            ->with('items.product')->chunkById(200, function ($sales) use ($add, &$salesCount, &$difference): void {
                foreach ($sales as $sale) {
                    $salesCount++;
                    $itemTotal = 0;
                    foreach ($sale->items as $item) {
                        $itemTotal += (float) $item->line_total;
                        $add('product:'.$item->product_id, 'revenue', $item->product?->type ?? 'Unspecified product', $sale->sale_date, (float) $item->line_total);
                    }
                    $residual = round((float) $sale->total_revenue - $itemTotal, 2);
                    if ($residual != 0) {
                        $difference++;
                        $add('sales-reconciliation', 'revenue', 'Sales without item detail / reconciliation', $sale->sale_date, $residual);
                    }
                }
            });

        // Cost any delivered line in the window that has not been costed yet. A statement
        // must not be wrong because nobody remembered to press "Recalculate costs"; costing
        // is idempotent, so this is a no-op once the lines carry a cost.
        $needsCosting = SaleItem::query()
            ->whereHas('sale', fn ($q) => $q->where('delivery_status', RevenueRecognitionService::DELIVERED)
                ->whereDate('sale_date', '>=', $start->toDateString())
                ->whereDate('sale_date', '<=', $end->toDateString()))
            ->whereNull('line_cogs')
            ->exists();

        if ($needsCosting) {
            app(SaleCostingService::class)->recost($start->toDateString(), $end->toDateString());
        }

        // Cost of the goods actually sold, taken from the cost frozen onto each sale line
        // by SaleCostingService. For a manufacturer that cost comes from the production
        // batch (materials consumed / units produced); consuming purchase layers of the
        // finished product would find nothing, because finished goods are never purchased.
        // Raw-material purchases are absent here by design: a purchase moves value into
        // inventory and only reaches the income statement when the goods are sold.
        $uncostedLines = 0;
        foreach (SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.delivery_status', RevenueRecognitionService::DELIVERED)
            ->whereDate('sales.sale_date', '>=', $start->toDateString())
            ->whereDate('sales.sale_date', '<=', $end->toDateString())
            ->get([
                'sale_items.product_id',
                'sale_items.line_cogs',
                'sales.sale_date',
                DB::raw('COALESCE(products.type, products.name) as product_label'),
            ]) as $line) {
            if ($line->line_cogs === null) {
                $uncostedLines++;

                continue;
            }
            $add('cogs:'.$line->product_id, 'costs', ($line->product_label ?? 'Unspecified product').' (cost of sales)',
                (string) $line->sale_date, (float) $line->line_cogs);
        }

        $foreignCurrency = 0;
        $materialExpenseCount = 0;
        $materialExpenseTotal = 0.0;
        $depreciationExpenseCount = 0;
        Expense::query()->with(['category' => fn ($q) => $q->withTrashed()])
            ->whereDate('expense_date', '>=', $start->toDateString())->whereDate('expense_date', '<=', $end->toDateString())
            ->chunkById(200, function ($expenses) use ($add, &$foreignCurrency, &$materialExpenseCount, &$materialExpenseTotal, &$depreciationExpenseCount): void {
                foreach ($expenses as $expense) {
                    if (strtoupper($expense->currency) !== 'RWF') {
                        $foreignCurrency++;

                        continue;
                    }
                    $section = $expense->category?->statementSection() ?? 'operating';
                    // Raw-material spend reaches the statement through inventory and FIFO
                    // costing. The COGS expense categories mirror the same purchases, so
                    // counting them here as well would charge the business twice.
                    if ($section === 'costs') {
                        $materialExpenseCount++;
                        $materialExpenseTotal = round($materialExpenseTotal + (float) $expense->amount, 2);

                        continue;
                    }
                    // Depreciation is charged from the asset register below. Counting a
                    // keyed-in depreciation expense as well would overstate costs and
                    // leave the balance sheet unable to balance.
                    if (in_array($section, ExpenseCategory::DERIVED_SECTIONS, true)) {
                        $depreciationExpenseCount++;

                        continue;
                    }
                    $add('expense:'.$expense->expense_category_id, $section, $expense->category?->name ?? 'Unclassified expenses', $expense->expense_date->toDateString(), (float) $expense->amount);
                }
            });

        // Depreciation straight from the fixed-asset register: one schedule, charged
        // consistently, instead of the workbook figure that started in January on one
        // sheet and February on another.
        $depreciation = $template;
        $assets = FixedAsset::query()->whereDate('acquisition_date', '<=', $end->toDateString())->get();
        foreach ($assets as $asset) {
            for ($i = 0; $i < 12; $i++) {
                if ($depreciation[$i] === null) {
                    continue;
                }
                $depreciation[$i] = round($depreciation[$i] + $asset->chargeForMonth($start->addMonths($i)), 2);
            }
        }
        if ($assets->isNotEmpty()) {
            $rows['depreciation'] = ['section' => 'operating', 'label' => 'Depreciation (fixed-asset register)', 'values' => $depreciation];
        }

        // Interest from the repayment register, where it is already separated from
        // principal. The workbook template buried both inside operating expenses.
        $interest = $template;
        $hasInterest = false;
        foreach (LoanRepayment::query()
            ->whereDate('paid_at', '>=', $start->toDateString())
            ->whereDate('paid_at', '<=', $end->toDateString())->get() as $repayment) {
            $paid = CarbonImmutable::parse($repayment->paid_at);
            $index = ($paid->year - $start->year) * 12 + $paid->month - $start->month;
            if ($index < 0 || $index > 11 || $interest[$index] === null) {
                continue;
            }
            $interest[$index] = round($interest[$index] + (float) $repayment->interest_portion, 2);
            $hasInterest = true;
        }
        if ($hasInterest) {
            $rows['interest'] = ['section' => 'other', 'label' => 'Loan interest (repayment register)', 'values' => $interest];
        }

        foreach (array_slice($this->sections($report), 0, 4, true) as $key => $label) {
            if (! collect($rows)->contains('section', $key)) {
                $rows['empty:'.$key] = ['section' => $key, 'label' => 'No '.$label.' recorded - review', 'values' => $template];
            }
        }

        $notes = [
            'Frozen system snapshot as of '.$end->toDateString().'. Later transactions do not change this draft; generate a new report to refresh.',
            RevenueRecognitionService::BASIS_NOTE,
            'Cost of goods sold is the cost frozen onto each delivered sale line, matched to the month of the sale. Manufactured goods are costed from their production batch: materials consumed divided by units produced. Raw-material purchases are not expensed here; they reach the income statement only when the goods are sold.',
            'Expenses are grouped by category and placed using the category statement section. Reclassify interest, tax and depreciation as needed. No tax rate, VAT calculation, depreciation schedule or loan repayment is inferred.',
            'Zero means no amount recorded in the selected system sources, not proof of a complete ledger. Blank months after the cutoff are unavailable. All amounts are RWF.',
            $salesCount.' delivered sales included; '.$difference.' sales have a header/detail reconciliation line.',
        ];
        if ($salesCount === 0) {
            $notes[] = 'No delivered sales exist in this period. Historical workbook figures have not been imported.';
        }
        if ($materialExpenseCount > 0) {
            $notes[] = $materialExpenseCount.' direct-cost expenses totalling RWF '.number_format($materialExpenseTotal, 2).' were excluded to avoid double counting: raw materials are costed through inventory (FIFO) instead. Reclassify any that are genuinely separate direct costs, such as production labour.';
        }
        if ($depreciationExpenseCount > 0) {
            $notes[] = $depreciationExpenseCount.' keyed-in depreciation or interest expenses were excluded: both are charged from their registers so the income statement, balance sheet and cash flow statement use one schedule each.';
        }
        if ($uncostedLines > 0) {
            $notes[] = $uncostedLines.' delivered sale lines carry no cost and contribute revenue with nothing against it, so the result is overstated. Open Reports > Profitability and use Recalculate costs, then check that each sale names a production batch with recorded materials.';
        }
        if ($assets->isEmpty()) {
            $notes[] = 'No fixed assets are registered, so no depreciation has been charged. Register machinery and equipment for the charge to appear.';
        }
        if ($foreignCurrency > 0) {
            $notes[] = $foreignCurrency.' non-RWF expenses excluded: convert and enter reviewed RWF figures before using this report.';
        }

        return [array_values($rows), $notes];
    }
}
