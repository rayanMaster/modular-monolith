<?php

namespace App\Http\Requests;

use App\Enums\WorkSiteCompletionStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'customer_id' => 'sometimes|integer',
            'category_id' => 'sometimes|integer',
            'parent_work_site_id' => 'nullable|int',
            'starting_budget' => 'sometimes|integer|min:0',
            'cost' => 'sometimes|integer|min:0',
            'address_id' => 'sometimes|integer|exists:addresses,id',
            'workers_count' => 'sometimes|integer|min:0',
            'receipt_date' => 'sometimes|date',
            'starting_date' => 'sometimes|date',
            'deliver_date' => 'sometimes|date',
            'completion_status' => ['sometimes', 'integer', Rule::in(WorkSiteCompletionStatusEnum::cases())],
            'reception_status' => 'sometimes|integer',
            'resources' => 'sometimes|array',
            'resources.*.quantity' => 'sometimes|numeric',
            'resources.*.price' => 'sometimes|numeric',
            'resources.*.id' => 'sometimes|integer',
            'image' => ['sometimes', File::types(['jpeg', 'png', 'gif', 'webp'])
                ->max('2mb')],
        ];
    }
}
