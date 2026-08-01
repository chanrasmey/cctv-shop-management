<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_no',
        'return_date',
        'purchase_id',
        'supplier_id',
        'subtotal',
        'status',
        'remark',
        'created_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'subtotal'    => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedDateAttribute(): string
    {
        return $this->return_date
            ? $this->return_date->format('Y-m-d')
            : '';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Completed' => 'success',
            'Cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'Cancelled';
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->details()->sum('subtotal');

        $this->update([
            'subtotal' => $subtotal,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('return_date', today());
    }

    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('return_date', [$from, $to]);
    }
}