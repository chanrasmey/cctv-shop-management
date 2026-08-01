<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    public function edit()
    {
        $setting = CompanySetting::current();

        return view('settings.company', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = CompanySetting::current();

        $validated = $request->validate([

            'company_name'            => ['required','string','max:150'],
            'company_name_kh'         => ['nullable','string','max:150'],
            'owner_name'             => ['nullable','string','max:150'],

            'address'                => ['nullable','string'],

            'phone'                  => ['nullable','string','max:50'],
            'mobile'                 => ['nullable','string','max:50'],

            'email'                  => ['nullable','email'],
            'website'                => ['nullable','string','max:255'],

            'telegram'               => ['nullable','string','max:255'],
            'facebook'               => ['nullable','string','max:255'],

            'tax_number'             => ['nullable','string','max:100'],
            'business_registration'  => ['nullable','string','max:100'],

            'invoice_prefix'         => ['required','string','max:20'],
            'purchase_prefix'        => ['required','string','max:20'],
            'quotation_prefix'       => ['required','string','max:20'],

            'currency'               => ['required','string','max:20'],
            'currency_symbol'        => ['required','string','max:10'],

            'timezone'               => ['required','string','max:100'],

            'invoice_footer'         => ['nullable','string'],
            'warranty_policy'        => ['nullable','string'],
            'receipt_note'           => ['nullable','string'],

            'logo'                   => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'favicon'                => ['nullable','image','mimes:jpg,jpeg,png,ico,webp','max:1024'],

            'is_active'              => ['nullable','boolean'],

        ]);

        if ($request->hasFile('logo')) {

            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }

            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('favicon')) {

            if ($setting->favicon && Storage::disk('public')->exists($setting->favicon)) {
                Storage::disk('public')->delete($setting->favicon);
            }

            $validated['favicon'] = $request->file('favicon')->store('company', 'public');
        }

        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active');

        $setting->update($validated);

        return redirect()
            ->route('settings.company.edit')
            ->with('success', 'Company settings updated successfully.');
    }
}