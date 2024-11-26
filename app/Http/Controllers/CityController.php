<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    public function create(): JsonResponse
    {
        City::factory()->create();

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function list(): JsonResponse
    {
        $cities = City::query()->get();

        return ApiResponseHelper::sendSuccessResponse(new Result($cities));

    }

    //    public function update()
    //    {
    //
    //    }
    //
    //    public function show()
    //    {
    //
    //    }
    //
    //    public function delete()
    //    {
    //
    //    }
}
