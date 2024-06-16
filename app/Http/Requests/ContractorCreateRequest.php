<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContractorCreateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array{
     *     first_name:string,
     *     last_name:string|null,
     *     phone:string|null,
     *     address_id:int|null,
     * }|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['sometimes', 'string'],
            'phone' => ['sometimes', 'string'],
            'address_id' => ['sometimes', 'int'],
        ];
    }

    /**
     * @param  null  $key
     * @param  null  $default
     * @return array{first_name: string, last_name: string|null, phone: string|null, address_id: int|null}
     */
    public function validated($key = null, $default = null): array
    {
        return parent::validated();
    }
}
