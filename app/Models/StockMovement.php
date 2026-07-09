<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

        'qty_in' => 'decimal:2',

        'qty_out' => 'decimal:2',

        'balance' => 'decimal:2',

        'unit_cost' => 'decimal:2',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * User who created the movement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this movement is stock in.
     */
    public function isStockIn(): bool
    {
        return $this->qty_in > 0;
    }

    /**
     * Check if this movement is stock out.
     */
    public function isStockOut(): bool
    {
        return $this->qty_out > 0;
    }

    /**
     * Get movement quantity.
     */
    public function quantity(): float
    {
        return $this->qty_in > 0
            ? (float) $this->qty_in
            : (float) $this->qty_out;
    }

    /**
     * Movement badge color (AdminLTE).
     */
    public function badgeColor(): string
    {
        return match ($this->movement_type) {

            'Purchase'         => 'success',

            'Sale'             => 'primary',

            'Purchase Return'  => 'warning',

            'Sales Return'     => 'info',

            'Adjustment'       => 'danger',

            'Opening Stock'    => 'secondary',

            default            => 'dark',
        };
    }
}