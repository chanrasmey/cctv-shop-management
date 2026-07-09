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
     * @return array<string, mixed>
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
            ],

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
                'nullable',
                'numeric',
                'min:0',
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
     * Additional business validation.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateArrayLengths($validator);
            $this->validateAtLeastOneQuantity($validator);
            $this->validateDuplicateDetails($validator);
        });
    }

    private function validateArrayLengths(Validator $validator): void
    {
        $arrays = [
            'purchase_detail_id',
            'qty',
            'reason',
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
                    'Purchase return item data is inconsistent.'
                );
            }
        }
    }

    private function validateAtLeastOneQuantity(Validator $validator): void
    {
        if (! is_array($this->qty)) {
            return;
        }

        foreach ($this->qty as $qty) {
            if ((float) $qty > 0) {
                return;
            }
        }

        $validator->errors()->add(
            'qty',
            'Please enter at least one return quantity.'
        );
    }

    private function validateDuplicateDetails(Validator $validator): void
    {
        if (! is_array($this->purchase_detail_id)) {
            return;
        }

        if (count($this->purchase_detail_id) !== count(array_unique($this->purchase_detail_id))) {
            $validator->errors()->add(
                'purchase_detail_id',
                'Duplicate purchase items are not allowed in the same return.'
            );
        }
    }
}
