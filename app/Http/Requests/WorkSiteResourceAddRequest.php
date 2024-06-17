<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WorkSiteResourceAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
        ];
    }

    /**
     * @return array{
     *    quantity:float,
     *    price:float
     * }
     */
    public function validated($key = null, $default = null): array
    {
        return parent::validated();
    }
}
