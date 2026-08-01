<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturnDetail extends Model
{
    protected $fillable = [
        'sales_return_id',
        'sale_detail_id',
        'product_id',
        'qty',
        'unit_price',
        'unit_cost',
        'subtotal',
        'profit_adjustment',
        'reason',
        'remark',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'profit_adjustment' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(
            SalesReturn::class
        );
    }

    public function saleDetail(): BelongsTo
    {
        return $this->belongsTo(
            SaleDetail::class
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Recalculate subtotal and profit adjustment.
     */
    public function recalculate(): void
    {
        $subtotal = round(
            (float) $this->qty *
            (float) $this->unit_price,
            2
        );

        $profit = round(
            (
                (float) $this->unit_price -
                (float) $this->unit_cost
            ) * (float) $this->qty,
            2
        );

        $this->update([
            'subtotal' => $subtotal,
            'profit_adjustment' => $profit,
        ]);
    }
}