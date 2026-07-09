<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand_id',
        'unit_id',
        'supplier_id',
        'sku',
        'barcode',
        'serial_number',
        'product_name',
        'description',

        // Cost
        'buy_price',
        'average_cost',
        'sell_price',

        // Stock
        'minimum_stock',
        'stock',

        // Other
        'image',
        'status',
    ];

    protected $casts = [
        'buy_price'     => 'decimal:2',
        'average_cost'  => 'decimal:2',
        'sell_price'    => 'decimal:2',
        'stock'         => 'decimal:2',
        'status'        => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function purchaseReturnDetails(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getProfitAttribute(): float
    {
        return (float) $this->sell_price - (float) $this->buy_price;
    }

    public function getInventoryValueAttribute(): float
    {
        return (float) $this->stock * (float) ($this->average_cost ?: $this->buy_price);
    }

    public function getIsLowStockAttribute(): bool
    {
        return (float) $this->stock <= (float) $this->minimum_stock;
    }
}