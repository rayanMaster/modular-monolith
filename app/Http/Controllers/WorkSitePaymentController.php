<?php

namespace App\Http\Controllers;

use App\DTO\PaymentCreateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\PaymentCreateRequest;
use App\Models\WorkSite;
use Illuminate\Http\Request;

class WorkSitePaymentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(PaymentCreateRequest $request, int $workSiteId)
    {
        $workSite = WorkSite::query()->findOrFail($workSiteId);

        dd(PaymentCreateDTO::fromRequest($request->validated())->toArray());
        $workSite->payments()->create(PaymentCreateDTO::fromRequest($request->validated())->toArray());

        return ApiResponseHelper::sendSuccessResponse(new Result());
    }
}
