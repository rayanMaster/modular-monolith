<?php

namespace App\Http\Requests;

use App\Enums\ChartOfAccountNamesEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorksitePaymentCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,array<int,ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'payment_from_id' => ['required', 'integer'],
            'payment_from_type' => ['required', 'string', Rule::in(ChartOfAccountNamesEnum::payableAccounts())],
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

    //    /**
    //     * @return array{
    //     *     payable_id:int|null,
    //     *     payable_type:string|null,
    //     *     payment_date:string,
    //     *     payment_amount:float,
    //     *     payment_type:int|null,
    //     * }
    //     */
}
