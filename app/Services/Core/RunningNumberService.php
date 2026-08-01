<?php

namespace App\Services\Core;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RunningNumberService
{
    /**
     * Generate next running number.
     *
     * Example:
     * PUR-20260729-0001
     */
    public function generate(string $type): string
    {
        $prefix = strtoupper($type);

        $date = Carbon::now()->format('Ymd');

        $mapping = $this->mapping();

        if (! isset($mapping[$prefix])) {
            throw new \InvalidArgumentException(
                "Unknown running number type [{$prefix}]"
            );
        }

        $config = $mapping[$prefix];

        return DB::transaction(function () use (
            $config,
            $prefix,
            $date
        ) {

            $column = $config['column'];

            $last = DB::table($config['table'])
                ->lockForUpdate()
                ->where($column, 'like', "{$prefix}-{$date}-%")
                ->orderByDesc($column)
                ->value($column);

            $next = 1;

            if ($last) {

                $parts = explode('-', $last);

                $next = (int) end($parts) + 1;
            }

            return sprintf(
                '%s-%s-%04d',
                $prefix,
                $date,
                $next
            );
        });
    }

    /**
     * Purchase Number.
     */
    public function purchaseNo(): string
    {
        return $this->generate('PUR');
    }

    /**
     * Sale Number.
     */
    public function saleNo(): string
    {
        return $this->generate('SAL');
    }

    /**
     * Invoice Number.
     */
    public function invoiceNo(): string
    {
        return $this->generate('INV');
    }

    /**
     * Purchase Return Number.
     */
    public function purchaseReturnNo(): string
    {
        return $this->generate('PRN');
    }

    /**
     * Sales Return Number.
     */
    public function salesReturnNo(): string
    {
        return $this->generate('SRN');
    }

    /**
     * Future:
     * Stock Adjustment.
     */
    public function adjustmentNo(): string
    {
        return $this->generate('ADJ');
    }

    /**
     * Table mapping.
     */
    protected function mapping(): array
    {
        return [

            'PUR' => [
                'table' => 'purchases',
                'column' => 'purchase_no',
            ],

            'SAL' => [
                'table' => 'sales',
                'column' => 'sale_no',
            ],

            'INV' => [
                'table' => 'sales',
                'column' => 'invoice_no',
            ],

            'PRN' => [
                'table' => 'purchase_returns',
                'column' => 'return_no',
            ],

            'SRN' => [
                'table' => 'sales_returns',
                'column' => 'return_no',
            ],

            /*
             * Reserved for future module.
             */
            'ADJ' => [
                'table' => 'stock_adjustments',
                'column' => 'adjustment_no',
            ],

        ];
    }
}