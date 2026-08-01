<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePurchaseReturnRequest extends FormRequest
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
            | Return Header
            |--------------------------------------------------------------------------
            */

            'return_date' => [
                'required',
                'date',
            ],

            'purchase_id' => [
                'required',
                'exists:purchases,id',
            ],

            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'remark' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Summary
            |--------------------------------------------------------------------------
            */

            'subtotal' => [
                'required',
                'numeric',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Items
            |--------------------------------------------------------------------------
            */

            'purchase_detail_id' => [
                'required',
                'array',
                'min:1',
            ],

            'purchase_detail_id.*' => [
                'required',
                'exists:purchase_details,id',
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

            'reason' => [
                'nullable',
                'array',
            ],

            'reason.*' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Additional validation.
     */
    public function withValidator(
        Validator $validator
    ): void {

        $validator->after(function (
            Validator $validator
        ) {

            $this->validateArrayLengths(
                $validator
            );

            $this->validateDuplicateDetails(
                $validator
            );

        });

    }

    /**
     * Validate array lengths.
     */
    private function validateArrayLengths(
        Validator $validator
    ): void {

        $arrays = [
            'purchase_detail_id',
            'qty',
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
                    'Purchase Return item data is inconsistent.'
                );

            }
        }
    }

    /**
     * Reject duplicate items.
     */
    private function validateDuplicateDetails(
        Validator $validator
    ): void {

        if (! is_array($this->purchase_detail_id)) {
            return;
        }

        if (
            count($this->purchase_detail_id)
            !==
            count(array_unique($this->purchase_detail_id))
        ) {

            $validator->errors()->add(
                'purchase_detail_id',
                'Duplicate products are not allowed.'
            );

        }

    }

    /**
     * Validation Messages.
     */
    public function messages(): array
    {
        return [

            'return_date.required'
                => 'Return date is required.',

            'purchase_id.required'
                => 'Purchase is required.',

            'supplier_id.required'
                => 'Supplier is required.',

            'purchase_detail_id.required'
                => 'Please select at least one item.',

            'purchase_detail_id.min'
                => 'Please select at least one item.',

            'qty.*.gt'
                => 'Return quantity must be greater than zero.',

            'subtotal.required'
                => 'Subtotal is required.',

        ];
    }
}