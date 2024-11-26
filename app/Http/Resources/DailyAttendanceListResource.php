<?php

namespace App\Http\Resources;

use App\Models\Worksite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Worksite $workSite
 * @property mixed $date
 */
class DailyAttendanceListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'worksite' => $this->worksite->title,
            'date' => $this->date,
        ];
    }
}
