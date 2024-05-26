<?php

use App\Http\Controllers\WorkSiteController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\WorkSiteCategoryController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {

        Route::group(['prefix' => 'worksite'], function () {
            Route::post('/create', [WorkSiteController::class, 'create'])->middleware('can:work-site-add');

            Route::group(['prefix' => 'payment'], function () {
                Route::post('/add/{work_site_id}', [PaymentController::class, 'create']);
            });

            Route::group(['prefix' => 'category'], function () {
                Route::get('/list', [WorkSiteCategoryController::class, 'list'])->middleware('can:ws-category-list');
                Route::get('/show/{id}', [WorkSiteCategoryController::class, 'show'])->middleware('can:ws-category-show');
                Route::post('/create', [WorkSiteCategoryController::class, 'create'])->middleware('can:ws-category-add');
                Route::put('/update/{id}', [WorkSiteCategoryController::class, 'update'])->middleware('can:ws-category-update');
                Route::delete('/delete/{id}', [WorkSiteCategoryController::class, 'destroy'])->middleware('can:ws-category-delete');
            });
        });
        Route::group(['prefix' => 'resource'], function () {

            Route::get('/list', [ResourceController::class, 'list'])->middleware('can:ws-resource-list');
            Route::get('/show/{id}', [ResourceController::class, 'show'])->middleware('can:ws-resource-show');
            Route::post('/create', [ResourceController::class, 'create'])->middleware('can:ws-resource-add');
            Route::put('/update/{id}', [ResourceController::class, 'update'])->middleware('can:ws-resource-update');
            Route::delete('/delete/{id}', [ResourceController::class, 'destroy'])->middleware('can:ws-resource-delete');


            Route::group(['prefix' => 'category'], function () {
                Route::get('/list', [ResourceController::class, 'list'])->middleware('can:ws-resource-list');
                Route::get('/show/{id}', [ResourceController::class, 'show'])->middleware('can:ws-resource-show');
                Route::post('/create', [ResourceController::class, 'create'])->middleware('can:ws-resource-add');
                Route::put('/update/{id}', [ResourceController::class, 'update'])->middleware('can:ws-resource-update');
                Route::delete('/delete/{id}', [ResourceController::class, 'destroy'])->middleware('can:ws-resource-delete');
            });

        });

    });

});
