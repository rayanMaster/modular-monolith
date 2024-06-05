<?php

namespace App\Http\Controllers;

use App\DTO\WorkSiteCreateDTO;
use App\DTO\WorkSiteUpdateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorkSiteCreateRequest;
use App\Http\Requests\WorkSiteUpdateRequest;
use App\Http\Resources\WorkSiteListResource;
use App\Mapper\CreateWorkSiteMapper;
use App\Models\WorkSite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;

class WorkSiteController extends Controller
{
    //    public function __construct(
    //        private readonly IFileManager $fileManager
    //    )
    //    {
    //    }

    public function list()
    {
        $workSites = WorkSite::query()->with('payments')->get();

        return ApiResponseHelper::sendResponse(new Result(WorkSiteListResource::collection($workSites)));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws \Throwable
     */
    public function store(WorkSiteCreateRequest $request)
    {
        DB::transaction(
            callback: function () use ($request) {
                $data = WorkSiteCreateDTO::fromRequest($request->validated());

                $workSiteData = CreateWorkSiteMapper::toWorkSiteEloquent($data);

                $workSite = WorkSite::query()->create($workSiteData);

                $resourcesData = CreateWorkSiteMapper::toWorkSiteResourcesEloquent($data);
                $workSite->resources()->syncWithoutDetaching($resourcesData);

                $paymentData = CreateWorkSiteMapper::toPaymentEloquent($data);

                foreach ($paymentData as $payment) {
                    $workSite->payments()->create($payment);
                }

                $file = $request->file('image');
                if ($file) {
                    $fileNameParts = explode('.', $file->getClientOriginalName());
                    $fileName = $fileNameParts[0];
                    $path = lcfirst('WorkSite');
                    $name = $fileName.'_'.now()->format('YmdH');

                    if (! File::exists(public_path('storage/'.$path))) {
                        File::makeDirectory(public_path('storage/'.$path));
                    }

                    $fullPath = public_path('storage/'.$path).'/'.$name.'.webp';

                    // create new manager instance with desired driver
                    $manager = new \Intervention\Image\ImageManager(new Driver());

                    // read image from filesystem
                    $image = $manager->read($file)->save($fullPath);
                }
                //        $this->fileManager->upload($files);
            },
            attempts: 3);

        return ApiResponseHelper::sendSuccessResponse(
            new Result());
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('worksite::show');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Throwable
     */
    public function update(WorkSiteUpdateRequest $request, $id)
    {
        DB::transaction(
            callback: function () use ($request,$id) {

                $workSite = WorkSite::query()->findOrFail($id);

                $data = WorkSiteUpdateDTO::fromRequest($request->validated());

                $workSiteData = CreateWorkSiteMapper::toWorkSiteEloquent($data);

                $workSite->update($workSiteData);

                $resourcesData = CreateWorkSiteMapper::toWorkSiteResourcesEloquent($data);
                $workSite->resources()->syncWithoutDetaching($resourcesData);

                $paymentData = CreateWorkSiteMapper::toPaymentEloquent($data);


                $file = $request->file('image');
                if ($file) {
                    $fileNameParts = explode('.', $file->getClientOriginalName());
                    $fileName = $fileNameParts[0];
                    $path = lcfirst('WorkSite');
                    $name = $fileName.'_'.now()->format('YmdH');

                    if (! File::exists(public_path('storage/'.$path))) {
                        File::makeDirectory(public_path('storage/'.$path));
                    }

                    $fullPath = public_path('storage/'.$path).'/'.$name.'.webp';

                    // create new manager instance with desired driver
                    $manager = new \Intervention\Image\ImageManager(new Driver());

                    // read image from filesystem
                    $image = $manager->read($file)->save($fullPath);
                }
                //        $this->fileManager->upload($files);
            },
            attempts: 3);

        return ApiResponseHelper::sendSuccessResponse(
            new Result());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
