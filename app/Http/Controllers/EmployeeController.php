<?php

namespace App\Http\Controllers;

use App\Enums\RolesEnum;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Requests\EmployeeListRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeDetailsResource;
use App\Http\Resources\EmployeeListResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class EmployeeController extends Controller
{
    public function list(EmployeeListRequest $request): JsonResponse
    {
        /**
         * @var array{
         *     is_manager?:int
         * } $filter
         */
        $filter = $request->validated();
        DB::listen(function (QueryExecuted $queryExecuted) {
            //dump($queryExecuted->toRawSql());
        });

        // this is the raw query
        //          SELECT * FROM `users`
        //          INNER JOIN `model_has_roles`ON `users`.`id` = `model_has_roles`.`model_id`
        //          AND `model_has_roles`.`model_type` = 'App\\Models\\User'
        //          INNER JOIN `roles` ON `model_has_roles`.`role_id` = `roles`.`id`
        //          AND `roles`.`name` = 'site_manager'
        $employees = User::query()
            ->select('users.*')
            ->when(
                value: isset($filter['is_manager']) && $filter['is_manager'],
                callback: function (Builder $query) {
                    return $query->join(
                        table: 'model_has_roles',
                        first: function (JoinClause $join) {
                            $join->on(
                                first: 'users.id',
                                operator: '=',
                                second: 'model_has_roles.model_id')
                                ->where(
                                    column: 'model_has_roles.model_type',
                                    operator: '=',
                                    value: User::class)
                                ->join(
                                    table: 'roles',
                                    first: function (JoinClause $roleJoin) {
                                        $roleJoin->on(
                                            first: 'model_has_roles.role_id',
                                            operator: '=',
                                            second: 'roles.id')
                                            ->where(column: 'roles.name',
                                                operator: '=',
                                                value: RolesEnum::SITE_MANAGER->value);
                                    }
                                );
                        }
                    );
                })
            ->get();

        return ApiResponseHelper::sendSuccessResponse(new Result(EmployeeListResource::collection($employees)));
    }

    /**
     * @throws Throwable
     */
    public function store(EmployeeCreateRequest $request): JsonResponse
    {
        /**
         * @var array{
         *     first_name:string,
         *     last_name:string|null,
         *     phone:string,
         *     password:string|null,
         *     role:string|null
         * } $requestedData
         */
        $requestedData = $request->validated();
        $dataToSave = array_filter([
            'first_name' => $requestedData['first_name'],
            'last_name' => $requestedData['last_name'] ?? null,
            'phone' => $requestedData['phone'],
            'password' => $requestedData['password'] ?? '123456',
        ], fn ($value) => $value != null);
        DB::transaction(function () use ($dataToSave, $requestedData) {
            $user = User::query()->create($dataToSave);
            $role = $requestedData['role'] ?? RolesEnum::WORKER->value;
            $user->assignRole($role);
        });

        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * @throws Throwable
     */
    public function update(EmployeeUpdateRequest $request, int $workerId): JsonResponse
    {
        /**
         * @var array{
         *     first_name:string|null,
         *     last_name:string|null,
         *     phone:string|null
         * } $requestedData
         */
        $requestedData = $request->validated();
        $worker = User::query()->findOrFail($workerId);

        $dataToSave = array_filter([
            'first_name' => $requestedData['first_name'] ?? null,
            'last_name' => $requestedData['last_name'] ?? null,
            'phone' => $requestedData['phone'] ?? null,
        ], fn ($value) => $value != null);

        $worker->update($dataToSave);

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function show(int $workerId): JsonResponse
    {
        $worker = User::query()->findOrFail($workerId);

        return ApiResponseHelper::sendSuccessResponse(new Result(EmployeeDetailsResource::make($worker)));
    }

    public function destroy(int $workerId): JsonResponse
    {
        $worker = User::query()->findOrFail($workerId);
        $worker->delete();

        return ApiResponseHelper::sendSuccessResponse();
    }
}
