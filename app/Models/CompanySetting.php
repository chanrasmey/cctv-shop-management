<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    protected $fillable = [

        'company_name',
        'company_name_kh',
        'owner_name',

        'logo',
        'favicon',

        'address',

        'phone',
        'mobile',

        'email',
        'website',

        'telegram',
        'facebook',

        'tax_number',
        'business_registration',

        'invoice_prefix',
        'purchase_prefix',
        'quotation_prefix',

        'currency',
        'currency_symbol',

        'timezone',

        'invoice_footer',
        'warranty_policy',
        'receipt_note',

        'is_active',

        'updated_by',
    ];

    protected $casts = [

        'is_active' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'company_name'      => 'My CCTV Shop',
                'invoice_prefix'    => 'INV',
                'purchase_prefix'   => 'PUR',
                'quotation_prefix'  => 'QT',
                'currency'          => 'USD',
                'currency_symbol'   => '$',
                'timezone'          => 'Asia/Phnom_Penh',
                'is_active'         => true,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return asset('storage/' . $this->logo);
    }

    public function getFaviconUrlAttribute(): ?string
    {
        if (!$this->favicon) {
            return null;
        }

        return asset('storage/' . $this->favicon);
    }
}