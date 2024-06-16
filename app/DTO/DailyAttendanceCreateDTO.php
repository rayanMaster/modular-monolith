<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class DailyAttendanceCreateDTO extends Data
{
    public function __construct(
        public ?int $employeeId,
        public int $workSiteId,
        public ?string $dateFrom,
        public ?string $dateTo,
    ) {
    }

    /**
     * @param array{
     *     employee_id : int | null,
     *     work_site_id : int,
     *     date_from : string | null,
     *     date_to : string | null
     * } $request
     */
    public static function fromRequest(array $request, ?int $employeeId): DailyAttendanceCreateDTO
    {
        return new self(
            employeeId: $request['employee_id'] ?? $employeeId,
            workSiteId: $request['work_site_id'],
            dateFrom: $request['date_from'] ?? null,
            dateTo: $request['date_to'] ?? null,
        );
    }
}
