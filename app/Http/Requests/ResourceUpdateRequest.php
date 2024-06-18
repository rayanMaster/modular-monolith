<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResourceUpdateRequest extends FormRequest
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
            'name' => ['sometimes','string'],
            'description' => ['sometimes','string'],
            'resource_category_id' => ['sometimes','exists:resource_categories,id'],
        ];
    }

    /**
     * @return array{
     *     name:string | null,
     *     description:string | null,
     *     resource_category_id : int
     * }
     */

}
