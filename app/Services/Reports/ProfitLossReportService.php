<?php

namespace App\Services\Reports;

use App\Models\CompanySetting;
use App\Services\Finance\FinancialSummaryService;

class ProfitLossReportService
{
    public function __construct(
        protected FinancialSummaryService $financial
    ) {
    }

    /**
     * Generate Profit & Loss report data.
     */
    public function report(array $filters = []): array
    {
        return $this->financial->dashboard($filters);
    }

    /**
     * Company information for report header.
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
        return 'Profit & Loss Report';
    }

    /**
     * Export filename.
     */
    public function filename(): string
    {
        return 'profit_loss_' . now()->format('Ymd_His');
    }
}
