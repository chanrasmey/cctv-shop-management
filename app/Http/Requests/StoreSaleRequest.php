<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
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

            'invoice_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                'in:Draft,Pending,Completed',
            ],

            'remark' => [
                'nullable',
                'string',
            ],

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

            'change_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

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

            'unit_price' => [
                'required',
                'array',
            ],

            'unit_price.*' => [
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
        $validator->after(function (Validator $validator): void {
            $this->validateArrayLengths($validator);
            $this->validateDuplicateProducts($validator);
        });
    }

    private function validateArrayLengths(Validator $validator): void
    {
        $arrays = [
            'product_id',
            'qty',
            'unit_price',
            'discount_percent_item',
            'discount_amount_item',
            'subtotal_item',
        ];

        $expected = null;

        foreach ($arrays as $field) {
            if (! is_array($this->$field)) {
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
                    'Sale item data is inconsistent.'
                );
            }
        }
    }

    private function validateDuplicateProducts(Validator $validator): void
    {
        if (! is_array($this->product_id)) {
            return;
        }

        if (count($this->product_id) !== count(array_unique($this->product_id))) {
            $validator->errors()->add(
                'product_id',
                'Duplicate products are not allowed in the same sale.'
            );
        }
    }
}
