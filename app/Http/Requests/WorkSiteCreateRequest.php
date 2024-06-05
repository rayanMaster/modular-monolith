<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class WorkSiteCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'customer_id' => 'sometimes|integer',
            'category_id' => 'sometimes|integer',
            'main_worksite' => 'nullable|boolean',
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
            'payments' => 'sometimes|array',
            'payments.*.payment_amount' => 'sometimes|numeric',
            'payments.*.payment_date' => ['sometimes', 'date_format:Y-m-d H:i'],
            'image' => ['sometimes',File::types(['jpeg', 'png', 'gif', 'webp'])
                ->max('2mb')],
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
