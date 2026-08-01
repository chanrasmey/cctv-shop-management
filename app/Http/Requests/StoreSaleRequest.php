<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'sale_date' => [
                'required',
                'date',
            ],

            'customer_id' => [
                'nullable',
                'exists:customers,id',
            ],

            'paid_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'discount_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'tax_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'tax_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'grand_total' => [
                'required',
                'numeric',
                'min:0',
            ],

            'balance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'change_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'remark' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],

            'items.*.qty' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.discount_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'items.*.discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'items.required' => 'Please add at least one product.',

            'items.min' => 'Please add at least one product.',

            'items.*.product_id.required' => 'Please select a product.',

            'items.*.qty.required' => 'Quantity is required.',

            'items.*.qty.gt' => 'Quantity must be greater than zero.',

            'items.*.unit_price.required' => 'Unit price is required.',

            'sale_date.required' => 'Sale date is required.',

        ];
    }

    public function attributes(): array
    {
        return [

            'sale_date' => 'sale date',

            'customer_id' => 'customer',

            'paid_amount' => 'paid amount',

            'discount_percent' => 'discount',

            'tax_percent' => 'tax',

            'remark' => 'remark',

            'items' => 'sale items',

            'items.*.product_id' => 'product',

            'items.*.qty' => 'quantity',

            'items.*.unit_price' => 'unit price',

            'items.*.discount_percent' => 'item discount',

        ];
    }
}