<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'owner_name',
        'phone',
        'email',
        'website',
        'address',
        'logo',
        'tax_number',
        'currency',
        'timezone',
        'invoice_prefix',
        'invoice_footer',
    ];
}