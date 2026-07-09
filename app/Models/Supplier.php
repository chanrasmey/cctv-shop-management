<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_code',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_number',
        'opening_balance',
        'status',
    ];
}