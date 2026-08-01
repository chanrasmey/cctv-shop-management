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

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,

            'total_assets' => collect($assets)->sum('amount'),

            'total_liabilities' => collect($liabilities)->sum('amount'),

            'total_equity' => collect($equity)->sum('amount'),

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