<?php

namespace App\Http\Resources;

use App\Enums\PaymentTypesEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property float $amount
 * @property string $payment_date
 * @property string $payment_type
 */
class WorksitePaymentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'payment_date' => Carbon::parse($this?->payment_date)?->format('Y-m-d H:i'),
            'payment_type' => PaymentTypesEnum::from($this?->payment_type)?->name,
        ];
    }
}
