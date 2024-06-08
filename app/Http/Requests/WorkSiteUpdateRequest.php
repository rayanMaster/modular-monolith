<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class WorkSiteUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'customer_id' => 'sometimes|integer',
            'category_id' => 'sometimes|integer',
            'parent_worksite_id' => 'nullable|int',
            'starting_budget' => 'sometimes|integer|min:0',
            'cost' => 'sometimes|integer|min:0',
            'address' => 'sometimes|integer|min:0',
            'workers_count' => 'sometimes|integer|min:0',
            'receipt_date' => 'sometimes|date',
            'starting_date' => 'sometimes|date',
            'deliver_date' => 'sometimes|date',
            'status_on_receive' => 'sometimes|integer',
            'resources' => 'sometimes|array',
            'resources.*.quantity' => 'sometimes|numeric',
            'resources.*.price' => 'sometimes|numeric',
            'resources.*.id' => 'sometimes|integer',
            'image' => ['sometimes',File::types(['jpeg', 'png', 'gif', 'webp'])
                ->max('2mb')],
        ];
    }
}
