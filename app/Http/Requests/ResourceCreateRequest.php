<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResourceCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string,array<int,ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required','string'],
            'description' => ['sometimes','string'],
            'resource_category_id' => ['required','exists:resource_categories,id'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array{
     *     name:string,
     *     description:string | null,
     *     resource_category_id : int
     * }
     */

}
