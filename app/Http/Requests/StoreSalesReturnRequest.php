<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesReturnRequest extends FormRequest
{
    /**
     * Authorize the request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'return_date' => [
                'required',
                'date',
            ],

            'remark' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'sale_detail_id' => [
                'required',
                'array',
                'min:1',
            ],

            'sale_detail_id.*' => [
                'required',
                'integer',
                'exists:sale_details,id',
            ],

            'qty' => [
                'required',
                'array',
                'same:sale_detail_id',
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

            'remark_item' => [
                'nullable',
                'array',
            ],

            'remark_item.*' => [
                'nullable',
                'string',
                'max:255',
            ],

        ];
    }

    /**
     * Validation messages.
     */
    public function messages(): array
    {
        return [

            'sale_detail_id.required' =>
                'Please select at least one product.',

            'sale_detail_id.*.exists' =>
                'The selected sale item is invalid.',

            'qty.required' =>
                'Return quantity is required.',

            'qty.*.gt' =>
                'Return quantity must be greater than zero.',

            'qty.*.numeric' =>
                'Return quantity must be numeric.',

            'return_date.required' =>
                'Return date is required.',

        ];
    }

    /**
     * Prepare request before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'sale_detail_id' => array_values(
                array_filter(
                    $this->input('sale_detail_id', []),
                    fn ($value) => ! is_null($value) && $value !== ''
                )
            ),

            'qty' => array_values(
                array_filter(
                    $this->input('qty', []),
                    fn ($value) => ! is_null($value) && $value !== ''
                )
            ),

        ]);
    }
}