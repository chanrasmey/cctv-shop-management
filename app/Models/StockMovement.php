<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'movement_type',
        'reference_no',
        'reference_id',
        'qty_in',
        'qty_out',
        'balance',
        'unit_cost',
        'remark',
        'created_by',
    ];

    protected $casts = [
        'qty_in'     => 'decimal:2',
        'qty_out'    => 'decimal:2',
        'balance'    => 'decimal:2',
        'unit_cost'  => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getQuantityAttribute(): float
    {
        return $this->qty_in > 0
            ? (float) $this->qty_in
            : (float) $this->qty_out;
    }

    public function getIsStockInAttribute(): bool
    {
        return (float) $this->qty_in > 0;
    }

    public function getIsStockOutAttribute(): bool
    {
        return (float) $this->qty_out > 0;
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

    public function scopePurchase($query)
    {
        return $query->where('movement_type', 'Purchase');
    }

    public function scopeSale($query)
    {
        return $query->where('movement_type', 'Sale');
    }

    public function scopePurchaseReturn($query)
    {
        return $query->where('movement_type', 'Purchase Return');
    }

    public function scopeSalesReturn($query)
    {
        return $query->where('movement_type', 'Sales Return');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('movement_type', 'Adjustment');
    }

    public function scopeOpeningStock($query)
    {
        return $query->where('movement_type', 'Opening Stock');
    }
}