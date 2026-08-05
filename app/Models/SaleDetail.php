<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',

        'qty',

        'unit_price',
        'unit_cost',

        'discount_percent',
        'discount_amount',

        'subtotal',

        'profit',

        'remark',
    ];

    protected $casts = [

        'qty' => 'decimal:2',

        'unit_price' => 'decimal:2',

        'unit_cost' => 'decimal:2',

        'discount_percent' => 'decimal:2',

        'discount_amount' => 'decimal:2',

        'subtotal' => 'decimal:2',

        'profit' => 'decimal:2',

        'created_at' => 'datetime',

        'updated_at' => 'datetime',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function salesReturns(): HasMany
    {
        return $this->hasMany(SalesReturnDetail::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getGrossTotalAttribute(): float
    {
        return round(
            ((float) $this->qty) * ((float) $this->unit_price),
            2
        );
    }

    public function getNetTotalAttribute(): float
    {
        return (float) $this->subtotal;
    }

    public function getTotalCostAttribute(): float
    {
        return round(
            ((float) $this->qty) * ((float) $this->unit_cost),
            2
        );
    }

    public function getProfitMarginAttribute(): float
    {
        if ((float) $this->subtotal <= 0) {
            return 0;
        }

        return round(
            ($this->profit / $this->subtotal) * 100,
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasDiscount(): bool
    {
        return (float) $this->discount_amount > 0;
    }

    public function isProfitable(): bool
    {
        return (float) $this->profit > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('id');
    }
}
