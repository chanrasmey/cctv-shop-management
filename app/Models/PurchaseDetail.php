<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [

        'purchase_id',

        'product_id',

        'qty',

        'unit_cost',

        'discount_percent',

        'discount_amount',

        'subtotal',

        'remark',

    ];

    protected $casts = [

        'qty' => 'decimal:2',

        'unit_cost' => 'decimal:2',

        'discount_percent' => 'decimal:2',

        'discount_amount' => 'decimal:2',

        'subtotal' => 'decimal:2',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Purchase Header
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function returnDetails(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate discount value.
     */
    public function calculateDiscount(): float
    {
        return ($this->qty * $this->unit_cost) * ($this->discount_percent / 100);
    }

    /**
     * Calculate line subtotal.
     */
    public function calculateSubtotal(): float
    {
        return ($this->qty * $this->unit_cost) - $this->calculateDiscount();
    }

    public function returnedQty(): float
    {
        return (float) $this->returnDetails()
            ->whereHas('purchaseReturn', function ($query): void {
                $query->where('status', 'Completed');
            })
            ->sum('qty');
    }

    public function returnableQty(): float
    {
        return max(0, (float) $this->qty - $this->returnedQty());
    }
}
