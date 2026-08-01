<?php

namespace App\Services\Reports;

use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\SalesReturn;

class SalesReturnReportService
{
    public function returns(array $filters = [])
    {
        return $this->query($filters)
            ->latest('return_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();
    }

    public function returnsForPrint(array $filters = [])
    {
        return $this->query($filters)
            ->latest('return_date')
            ->latest('id')
            ->get();
    }

    protected function query(array $filters = [])
    {
        return SalesReturn::with([
            'customer',
            'sale',
            'details.product',
        ])

        ->when(
            !empty($filters['date_from']),
            fn ($q) => $q->whereDate(
                'return_date',
                '>=',
                $filters['date_from']
            )
        )

        ->when(
            !empty($filters['date_to']),
            fn ($q) => $q->whereDate(
                'return_date',
                '<=',
                $filters['date_to']
            )
        )

        ->when(
            !empty($filters['customer_id']),
            fn ($q) => $q->where(
                'customer_id',
                $filters['customer_id']
            )
        )

        ->when(
            !empty($filters['status']),
            fn ($q) => $q->where(
                'status',
                $filters['status']
            )
        );
    }

    public function summary(array $filters = []): array
    {
        $returns = $this->query($filters)->get();

        return [

            'returns' => $returns->sum('grand_total'),

            'refund' => $returns->sum(function ($return) {
                return $return->refund_amount ?? 0;
            }),

            'items' => $returns->sum(function ($return) {
                return $return->details->sum('qty');
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
            'Completed',
            'Cancelled',
        ];
    }

    public function company()
    {
        return CompanySetting::first();
    }
}