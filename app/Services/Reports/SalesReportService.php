<?php

namespace App\Services\Reports;

use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Sale;

class SalesReportService
{
    public function sales(array $filters = [])
    {
        return $this->query($filters)
            ->latest('sale_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();
    }

    public function salesForPrint(array $filters = [])
    {
        return $this->query($filters)
            ->latest('sale_date')
            ->latest('id')
            ->get();
    }

    protected function query(array $filters = [])
    {
        return Sale::with([
            'customer',
            'details.product',
        ])

        ->when(
            !empty($filters['date_from']),
            fn ($q) => $q->whereDate('sale_date', '>=', $filters['date_from'])
        )

        ->when(
            !empty($filters['date_to']),
            fn ($q) => $q->whereDate('sale_date', '<=', $filters['date_to'])
        )

        ->when(
            !empty($filters['customer_id']),
            fn ($q) => $q->where('customer_id', $filters['customer_id'])
        )

        ->when(
            !empty($filters['status']),
            fn ($q) => $q->where('status', $filters['status'])
        );
    }

    public function summary(array $filters = []): array
    {
        $sales = $this->query($filters)->get();

        return [
            'sales' => $sales->sum('grand_total'),

            'paid' => $sales->sum('paid_amount'),

            'balance' => $sales->sum('balance'),

            'profit' => $sales->sum(function ($sale) {
                return $sale->details->sum('profit');
            }),
        ];
    }

    public function customers()
    {
        return Customer::orderBy('name')->get();
    }

    public function statuses(): array
    {
        return [
            'Draft',
            'Pending',
            'Completed',
            'Cancelled',
        ];
    }

    public function company()
    {
        return CompanySetting::first();
    }
}