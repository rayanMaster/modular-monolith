<?php

namespace App\Http\Requests;

use App\Enums\WorksiteCompletionStatusEnum;
use App\Enums\WorksiteReceptionStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorksiteCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int,ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'manager_id' => ['required', 'integer', 'exists:users,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'category_id' => ['sometimes', 'integer', 'exists:worksite_categories,id'],
            'parent_worksite_id' => ['nullable', 'integer', 'exists:worksites,id'],
            'contractor_id' => ['nullable', 'integer', 'exists:contractors,id'],
            'starting_budget' => ['sometimes', 'integer', 'min:0'],
            'cost' => ['sometimes', 'integer', 'min:0'],
            'address' => ['sometimes', 'string'],
            'city_id' => ['sometimes', 'integer', 'exists:cities,id'],
            'workers_count' => ['sometimes', 'integer', 'min:0'],
            'receipt_date' => ['sometimes', 'date'],
            'starting_date' => ['sometimes', 'date'],
            'deliver_date' => ['sometimes', 'date'],
            'reception_status' => ['sometimes', 'integer', Rule::in(WorksiteReceptionStatusEnum::cases())],
            'completion_status' => ['sometimes', 'integer', Rule::in(WorksiteCompletionStatusEnum::cases())],
            'items' => ['sometimes', 'array'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.price' => ['required', 'numeric'],
            'items.*.id' => ['required', 'integer', 'exists:items,id'],
            'payments' => ['sometimes', 'array'],
            'payments.*.payment_amount' => ['sometimes', 'numeric'],
            'payments.*.payment_date' => ['sometimes', 'date_format:Y-m-d H:i'],
            'images' => ['sometimes'],
            'images.*' => ['sometimes', 'file', 'mimes:jpeg,png,gif,webp', 'max:2048'], // max:2048 for 2MB
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
