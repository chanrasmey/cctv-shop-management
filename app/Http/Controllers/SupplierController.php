<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->where('supplier_code', 'like', "%{$request->search}%")
                  ->orWhere('company_name', 'like', "%{$request->search}%")
                  ->orWhere('contact_person', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
        }

        $suppliers = $query->latest()->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_code'   => 'required|max:30|unique:suppliers',
            'company_name'    => 'required|max:255',
            'contact_person'  => 'nullable|max:255',
            'phone'           => 'nullable|max:50',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable',
            'tax_number'      => 'nullable|max:100',
            'opening_balance' => 'required|numeric|min:0',
            'status'          => 'required|boolean',
        ]);

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_code'   => 'required|max:30|unique:suppliers,supplier_code,' . $supplier->id,
            'company_name'    => 'required|max:255',
            'contact_person'  => 'nullable|max:255',
            'phone'           => 'nullable|max:50',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable',
            'tax_number'      => 'nullable|max:100',
            'opening_balance' => 'required|numeric|min:0',
            'status'          => 'required|boolean',
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}