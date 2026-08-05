<?php

namespace App\Services\Reports;

use App\Models\CompanySetting;
use App\Services\Finance\FinancialSummaryService;

class BalanceSheetReportService
{
    public function __construct(
        protected FinancialSummaryService $financial
    ) {
    }

    /**
     * Generate Balance Sheet report.
     */
    public function report(array $filters = []): array
    {
        $cash = $this->financial->cashBalance($filters);

        $inventory = $this->financial->inventoryValue();

        $receivable = $this->financial->accountsReceivable($filters);

        $payable = $this->financial->accountsPayable($filters);

        $retainedEarnings = $this->financial->netProfit($filters);
                $assets = [
            [
                'name' => 'Cash',
                'amount' => $cash,
            ],
            [
                'name' => 'Accounts Receivable',
                'amount' => $receivable,
            ],
            [
                'name' => 'Inventory',
                'amount' => $inventory,
            ],
        ];

        $liabilities = [
            [
                'name' => 'Accounts Payable',
                'amount' => $payable,
            ],
        ];

        $equity = [
            [
                'name' => 'Retained Earnings',
                'amount' => $retainedEarnings,
            ],
        ];

        $totalAssets = collect($assets)->sum('amount');
        $totalLiabilities = collect($liabilities)->sum('amount');
        $totalEquity = collect($equity)->sum('amount');
        $liabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,

            'cash' => $cash,
            'inventory' => $inventory,
            'accounts_receivable' => $receivable,
            'other_assets' => 0,
            'total_assets' => $totalAssets,

            'accounts_payable' => $payable,
            'other_liabilities' => 0,
            'total_liabilities' => $totalLiabilities,

            'owner_capital' => 0,
            'retained_earnings' => $retainedEarnings,
            'current_profit' => 0,
            'total_equity' => $totalEquity,
            'liabilities_and_equity' => $liabilitiesAndEquity,
            'balanced' => round($totalAssets, 2) === round($liabilitiesAndEquity, 2),

            'company' => $this->company(),
        ];
    }

    /**
     * Company information.
     */
    public function company(): ?CompanySetting
    {
        return CompanySetting::first();
    }
        /**
     * Report title.
     */
    public function title(): string
    {
        return 'Balance Sheet';
    }

    /**
     * Export filename.
     */
    public function filename(): string
    {
        return 'balance_sheet_' . now()->format('Ymd_His');
    }
}
