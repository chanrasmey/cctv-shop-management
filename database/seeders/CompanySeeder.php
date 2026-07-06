<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'company_name' => 'My CCTV Shop',
            'owner_name' => 'Administrator',
            'phone' => '',
            'email' => '',
            'website' => '',
            'address' => '',
            'currency' => 'USD',
            'timezone' => 'Asia/Phnom_Penh',
            'invoice_prefix' => 'INV',
            'invoice_footer' => 'Thank you for your business.',
        ]);
    }
}