<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'products' => Product::count(),
            'categories' => Category::count(),
            'brands' => Brand::count(),
            'customers' => Customer::count(),
            'suppliers' => Supplier::count(),

            'latestCategories' => Category::latest()->take(5)->get(),
            'latestBrands' => Brand::latest()->take(5)->get(),
        ]);
    }
}