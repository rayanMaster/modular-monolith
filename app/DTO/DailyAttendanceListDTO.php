<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class DailyAttendanceListDTO extends Data
{
    public function __construct(
        public ?int $employeeId,
        public ?string $dateFrom,
        public ?string $dateTo,
    ) {
    }

    /**
     * @param array{
     *     employee_id : int | null,
     *     date_from : string | null,
     *     date_to : string | null
     * } $request
     */
    public static function fromRequest(array $request, ?int $employeeId): DailyAttendanceListDTO
    {
        return new self(
            employeeId: $request['employee_id'] ?? $employeeId,
            dateFrom: $request['date_from'] ?? null,
            dateTo: $request['date_to'] ?? null,
        );
    }
}
