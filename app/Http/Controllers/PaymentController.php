<?php

namespace App\Http\Controllers;

use App\DTO\PaymentCreateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\PaymentCreateRequest;
use App\Http\Requests\PaymentListRequest;
use App\Http\Resources\PaymentListResource;
use App\Models\Payment;
use App\Models\WorkSite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
//    public function list()
//    {
//        $payments = Payment::query()->whereHasMorph(
//            'payable',
//            WorkSite::class,
//            function (Builder $query) {
//                $query->whereDate('receipt_date', '2024-08-04');
//            }
//        )->get();
//
//        return ApiResponseHelper::sendSuccessResponse(new Result(PaymentListResource::collection($payments)));
//    }

    public function store(PaymentCreateRequest $request): \Illuminate\Http\JsonResponse
    {
        Payment::query()->create(PaymentCreateDTO::fromRequest($request->validated())->toArray());

        return ApiResponseHelper::sendSuccessResponse(new Result());
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('worksite::show');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
