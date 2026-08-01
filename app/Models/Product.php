<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

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

    'buy_price',
    'sell_price',
    'average_cost',

    'stock',
    'minimum_stock',

    'image',
    'status',
];

 protected $casts = [
    'buy_price'      => 'decimal:2',
    'sell_price'     => 'decimal:2',
    'average_cost'   => 'decimal:2',
    'stock'          => 'decimal:2',
    'minimum_stock'  => 'decimal:2',
    'status'         => 'boolean',
];

    /**
     * Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Unit
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    /**
     * Purchase Details
     */
    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * Sale Details
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Stock Movements
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Scope active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'minimum_stock');
    }

    /**
     * Scope latest products.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('id');
    }
    /**
     * Check whether the product has stock.
     */
    public function hasStock(): bool
    {
        return (float) $this->stock > 0;
    }

    /**
     * Check whether the product is low on stock.
     */
    public function isLowStock(): bool
    {
        return (float) $this->stock <= (float) $this->minimum_stock;
    }

    /**
     * Check whether the product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return (float) $this->stock <= 0;
    }

    /**
     * Current inventory value.
     */
    public function getInventoryValueAttribute(): float
    {
        return round(
            ((float) $this->stock) * ((float) $this->average_cost),
            2
        );
    }

    /**
     * Expected profit per unit.
     */
    public function getProfitPerUnitAttribute(): float
    {
        return round(
    ((float) $this->sell_price) -
    ((float) $this->average_cost),
    2
);
    }

    /**
     * Profit margin percentage.
     */
    public function getProfitMarginAttribute(): float
    {
        if ((float) $this->sell_price <= 0) {
            return 0;
        }

        return round(
            (
                (
                    (float) $this->sell_price -
                    (float) $this->average_cost
                ) / (float) $this->sell_price
            ) * 100,
            2
        );
    }

    /**
     * Product image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image)) {
            return asset('storage/' . $this->image);
        }

        return asset('images/no-image.png');
    }
}
