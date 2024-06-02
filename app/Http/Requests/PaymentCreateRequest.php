<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payable_id' => ['nullable', 'numeric'],
            'payable_type' => ['nullable', 'string'],
            'payment_date' => ['required', 'date'],
            'payment_amount' => ['required', 'numeric', 'min:1'],
            'payment_type' => ['sometimes', 'numeric'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
