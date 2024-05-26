<?php

namespace App\Http\Controllers;

use App\DTO\WorkSiteCreateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorkSiteCreateRequest;
use App\Mapper\CreateWorkSiteMapper;
use App\Models\Payment;
use App\Models\WorkSite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;

class WorkSiteController extends Controller
{
    //    public function __construct(
    //        private readonly IFileManager $fileManager
    //    )
    //    {
    //    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(WorkSiteCreateRequest $request)
    {

        $data = WorkSiteCreateDTO::fromRequest($request->validated());

        $workSiteData = CreateWorkSiteMapper::toWorkSiteEloquent($data);

        $workSite = WorkSite::query()->create($workSiteData);

        $resourcesData = CreateWorkSiteMapper::toWorkSiteResourcesEloquent($data);
        $workSite->resources()->syncWithoutDetaching($resourcesData);

        $paymentData = CreateWorkSiteMapper::toPaymentEloquent($data, $workSite?->id);

        Payment::query()->insert($paymentData);

        $file = $request->file('image');
        if ($file) {
            $fileNameParts = explode('.', $file->name);
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

        return ApiResponseHelper::sendSuccessResponse(
            new Result());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
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
