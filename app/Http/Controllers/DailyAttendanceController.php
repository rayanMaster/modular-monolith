<?php

namespace App\Http\Controllers;

use App\DTO\DailyAttendanceCreateDTO;
use App\DTO\DailyAttendanceListDTO;
use App\Exceptions\InvalidSubWorkSiteAttendanceException;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\DailyAttendanceCreateRequest;
use App\Http\Requests\DailyAttendanceListRequest;
use App\Http\Resources\DailyAttendanceListResource;
use App\Models\DailyAttendance;
use App\Models\User;
use App\Models\WorkSite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DailyAttendanceController extends Controller
{
    /**
     * @throws InvalidSubWorkSiteAttendanceException
     */
    public function store(int $employeeId, DailyAttendanceCreateRequest $request): JsonResponse
    {

        $employee = User::query()->findOrFail($employeeId);

        $dataFromRequest = DailyAttendanceCreateDTO::fromRequest($request->validated(), $employeeId);
        $dateFrom = $dataFromRequest->dateFrom ?? Carbon::today();
        $dateTo = $dataFromRequest->dateTo ?? Carbon::today();

        $alreadyHasDailyAttendance = DailyAttendance::query()
            ->where(
                column: 'employee_id',
                operator: '=',
                value: $dataFromRequest->employeeId)
            ->whereDate(column: 'date',
                operator: '>=',
                value: $dateFrom)
            ->whereDate(column: 'date',
                operator: '<=',
                value: $dateTo)
            ->exists();
        if ($alreadyHasDailyAttendance) {
            throw new InvalidSubWorkSiteAttendanceException('You already have a daily attendance for a work site');
        }
        $workSite = WorkSite::query()->findOrFail($dataFromRequest->workSiteId);
        // test if work site is a sub-worksite
        if ($workSite->parent_work_site_id != null) {
            throw new InvalidSubWorkSiteAttendanceException('Cant Assign employee to work site');
        }

        $dates = $this->getDates($dateFrom, $dateTo);
        $dataToSave = [];
        foreach ($dates as $date) {
            $dataToSave[] = [
                'employee_id' => $dataFromRequest->employeeId,
                'work_site_id' => $dataFromRequest->workSiteId,
                'date' => $date,
            ];
        }

        DailyAttendance::query()->insert($dataToSave);

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function list(int $employeeId, DailyAttendanceListRequest $request): JsonResponse
    {
        $employee = User::query()->findOrFail($employeeId);

        $dataFromRequest = DailyAttendanceListDTO::fromRequest($request->validated(), $employeeId);
        $dateFrom = $dataFromRequest->dateFrom ?? Carbon::today();
        $dateTo = $dataFromRequest->dateTo ?? Carbon::today();
        $result = DailyAttendance::query()
            ->where(
                column: 'employee_id',
                operator: '=',
                value: $dataFromRequest->employeeId)
            ->whereDate(column: 'date',
                operator: '>=',
                value: $dateFrom)
            ->whereDate(column: 'date',
                operator: '<=',
                value: $dateTo)
            ->get();

        return ApiResponseHelper::sendSuccessResponse(new Result(DailyAttendanceListResource::collection($result)));
    }

    private function getDates(?string $from = null, ?string $to = null): array
    {
        $from = $from ? Carbon::parse($from) : Carbon::today();
        $to = $to ? Carbon::parse($to) : $from->copy();

        $dates = [];

        while ($from <= $to) {
            $dates[] = $from->toDateString();
            $from->addDay();
        }

        return $dates;
    }
}
