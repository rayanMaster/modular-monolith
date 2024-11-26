<?php

namespace App\Http\Requests;

use App\Enums\RolesEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeCreateRequest extends FormRequest
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
     * @return array<string, array<int,ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['nullable', 'string'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'password' => ['sometimes', 'string'],
            'role' => ['sometimes', Rule::in(RolesEnum::cases())],
        ];
    }
}
