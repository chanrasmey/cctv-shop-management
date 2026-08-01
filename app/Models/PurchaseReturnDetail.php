<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_return_id',
        'purchase_detail_id',
        'product_id',
        'qty',
        'unit_cost',
        'subtotal',
        'reason',
    ];

    protected $casts = [
        'qty'       => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'subtotal'  => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function purchaseDetail()
    {
        return $this->belongsTo(PurchaseDetail::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getLineTotalAttribute(): float
    {
        return (float) $this->subtotal;
    }

    public function getFormattedUnitCostAttribute(): string
    {
        return number_format($this->unit_cost, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function calculateSubtotal(): float
    {
        return round(
            (float) $this->qty * (float) $this->unit_cost,
            2
        );
    }

    protected static function booted(): void
    {
        static::saving(function (PurchaseReturnDetail $detail) {
            $detail->subtotal = $detail->calculateSubtotal();
        });

        static::saved(function (PurchaseReturnDetail $detail) {
            if ($detail->purchaseReturn) {
                $detail->purchaseReturn->recalculateTotals();
            }
        });

        static::deleted(function (PurchaseReturnDetail $detail) {
            if ($detail->purchaseReturn) {
                $detail->purchaseReturn->recalculateTotals();
            }
        });
    }
}