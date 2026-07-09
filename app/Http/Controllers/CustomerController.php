<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {

            $query->where('customer_code', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
        }

        $customers = $query->latest()->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'customer_code' => 'required|max:30|unique:customers',

            'name' => 'required|max:255',

            'phone' => 'nullable|max:50',

            'email' => 'nullable|email|max:255',

            'address' => 'nullable',

            'opening_balance' => 'required|numeric|min:0',

            'status' => 'required|boolean',

        ]);

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([

            'customer_code' => 'required|max:30|unique:customers,customer_code,' . $customer->id,

            'name' => 'required|max:255',

            'phone' => 'nullable|max:50',

            'email' => 'nullable|email|max:255',

            'address' => 'nullable',

            'opening_balance' => 'required|numeric|min:0',

            'status' => 'required|boolean',

        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}