<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Purchase Header
            |--------------------------------------------------------------------------
            */

            'purchase_date' => [
                'required',
                'date',
            ],

            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'invoice_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                'in:Draft,Completed,Cancelled',
            ],

            'remark' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Purchase Summary
            |--------------------------------------------------------------------------
            */

            'subtotal' => [
                'required',
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

            'paid_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'balance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Purchase Items
            |--------------------------------------------------------------------------
            */

            'product_id' => [
                'required',
                'array',
                'min:1',
            ],

            'product_id.*' => [
                'required',
                'exists:products,id',
            ],

            'qty' => [
                'required',
                'array',
            ],

            'qty.*' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'unit_cost' => [
                'required',
                'array',
            ],

            'unit_cost.*' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount_percent_item' => [
                'nullable',
                'array',
            ],

            'discount_percent_item.*' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'discount_amount_item' => [
                'nullable',
                'array',
            ],

            'discount_amount_item.*' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'subtotal_item' => [
                'required',
                'array',
            ],

            'subtotal_item.*' => [
                'required',
                'numeric',
                'min:0',
            ],

        ];
    }

    /**
     * Additional business validation.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $this->validateArrayLengths($validator);

            $this->validateDuplicateProducts($validator);

        });
    }

    /**
     * Validate all item arrays contain the same number of elements.
     */
    private function validateArrayLengths(Validator $validator): void
    {
        $arrays = [
            'product_id',
            'qty',
            'unit_cost',
            'discount_percent_item',
            'discount_amount_item',
            'subtotal_item',
        ];

        $expected = null;

        foreach ($arrays as $field) {

            if (!is_array($this->$field)) {
                continue;
            }

            $count = count($this->$field);

            if ($expected === null) {
                $expected = $count;
                continue;
            }

            if ($count !== $expected) {

                $validator->errors()->add(
                    $field,
                    'Purchase item data is inconsistent.'
                );

            }
        }
    }

    /**
     * Reject duplicate products.
     */
    private function validateDuplicateProducts(Validator $validator): void
    {
        if (!is_array($this->product_id)) {
            return;
        }

        if (count($this->product_id) !== count(array_unique($this->product_id))) {

            $validator->errors()->add(
                'product_id',
                'Duplicate products are not allowed in the same purchase.'
            );

        }
    }

    /**
     * Custom Validation Messages
     */
    public function messages(): array
    {
        return [

            'supplier_id.required' =>
                'Please select a supplier.',

            'purchase_date.required' =>
                'Purchase date is required.',

            'product_id.required' =>
                'Please add at least one product.',

            'product_id.min' =>
                'Please add at least one product.',

            'qty.*.gt' =>
                'Quantity must be greater than zero.',

            'unit_cost.*.required' =>
                'Unit cost is required.',

            'subtotal.required' =>
                'Subtotal cannot be empty.',

            'grand_total.required' =>
                'Grand total cannot be empty.',

            'discount_percent.max' =>
                'Discount percentage cannot exceed 100%.',

            'tax_percent.max' =>
                'Tax percentage cannot exceed 100%.',

            'discount_percent_item.*.max' =>
                'Item discount percentage cannot exceed 100%.',

        ];
    }
}