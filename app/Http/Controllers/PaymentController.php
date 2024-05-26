<?php

namespace App\Http\Controllers;

use App\DTO\PaymentCreateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('worksite::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $work_site_id)
    {
        Payment::query()->create(PaymentCreateDTO::fromRequest($request->all(), $work_site_id)->toArray());

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
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('worksite::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
