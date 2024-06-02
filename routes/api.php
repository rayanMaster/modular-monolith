<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ResourceCategoryController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkSiteCategoryController;
use App\Http\Controllers\WorkSiteController;
use App\Http\Controllers\WorkSitePaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'worksite'], function () {
            Route::post('/create', [WorkSiteController::class, 'create'])
                ->middleware('can:work-site-add')
                ->name('worksite.create');
            Route::get('/list', [WorkSiteController::class, 'list'])
                ->middleware('can:work-site-add')
                ->name('worksite.list');
            Route::get('/show/{id}', [WorkSiteController::class, 'show'])
                ->middleware('can:work-site-add')
                ->name('worksite.show');
            Route::put('/update/{id}', [WorkSiteController::class, 'show'])
                ->middleware('can:work-site-add')
                ->name('worksite.update');
            Route::delete('/delete/{id}', [WorkSiteController::class, 'show'])
                ->middleware('can:work-site-add')
                ->name('worksite.delete');

            Route::group(['prefix' => 'category'], function () {
                Route::get('/list', [WorkSiteCategoryController::class, 'list'])
                    ->middleware('can:ws-category-list')
                    ->name('worksite.category.list');

                Route::get('/show/{id}', [WorkSiteCategoryController::class, 'show'])
                    ->middleware('can:ws-category-show')
                    ->name('worksite.category.show');

                Route::post('/create', [WorkSiteCategoryController::class, 'create'])
                    ->middleware('can:ws-category-add')
                    ->name('worksite.category.create');

                Route::put('/update/{id}', [WorkSiteCategoryController::class, 'update'])
                    ->middleware('can:ws-category-update')
                    ->name('worksite.category.update');

                Route::delete('/delete/{id}', [WorkSiteCategoryController::class, 'destroy'])
                    ->middleware('can:ws-category-delete')
                    ->name('worksite.category.delete');
            });

            Route::group(['prefix' => '{worksiteId}/payment'], function () {
                Route::post('create', WorkSitePaymentController::class)
                    ->middleware('can:payment-add')
                    ->name('worksite.payment.create');
            });
        });
        Route::group(['prefix' => 'resource'], function () {
            Route::get('/list', [ResourceController::class, 'list'])
                ->middleware('can:ws-resource-list')
                ->name('resource.list');
            Route::get('/show/{id}', [ResourceController::class, 'show'])
                ->middleware('can:ws-resource-show')
                ->name('resource.show');
            Route::post('/create', [ResourceController::class, 'store'])
                ->middleware('can:ws-resource-add')
                ->name('resource.create');
            Route::put('/update/{id}', [ResourceController::class, 'update'])
                ->middleware('can:ws-resource-update')
                ->name('resource.update');
            Route::delete('/delete/{id}', [ResourceController::class, 'destroy'])
                ->middleware('can:ws-resource-delete')
                ->name('resource.delete');
            Route::group(['prefix' => '{resourceId}/category'], function () {
                Route::get('/list', [ResourceCategoryController::class, 'list'])
                    ->middleware('can:ws-resource-list')
                    ->name('resource.category.list');
                Route::get('/show/{id}', [ResourceCategoryController::class, 'show'])
                    ->middleware('can:ws-resource-show')
                    ->name('resource.category.show');
                Route::post('/create', [ResourceCategoryController::class, 'store'])
                    ->middleware('can:ws-resource-add')
                    ->name('resource.category.create');
                Route::put('/update/{id}', [ResourceCategoryController::class, 'update'])
                    ->middleware('can:ws-resource-update')
                    ->name('resource.category.update');
                Route::delete('/delete/{id}', [ResourceCategoryController::class, 'destroy'])
                    ->middleware('can:ws-resource-delete')
                    ->name('resource.category.delete');
            });
        });
        Route::group(['prefix' => 'customer'], function () {
            Route::get('/list', [CustomerController::class, 'list'])
                ->middleware('can:customer-list')
                ->name('customer.list');

            Route::get('/show/{id}', [CustomerController::class, 'show'])
                ->middleware('can:customer-show')
                ->name('customer.show');

            Route::post('/create', [CustomerController::class, 'store'])
                ->middleware('can:customer-add')
                ->name('customer.create');

            Route::put('/update/{id}', [CustomerController::class, 'update'])
                ->middleware('can:customer-update')
                ->name('customer.update');

            Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])
                ->middleware('can:ws-customer-delete')
                ->name('customer.delete');

        });
        Route::group(['prefix' => 'worker'], function () {
            Route::get('/list', [WorkerController::class, 'list'])
                ->middleware('can:worker-list')
                ->name('worker.list');

            Route::get('/show/{id}', [WorkerController::class, 'show'])
                ->middleware('can:worker-show')
                ->name('worker.show');

            Route::post('/create', [WorkerController::class, 'store'])
                ->middleware('can:worker-add')
                ->name('worker.create');

            Route::put('/update/{id}', [WorkerController::class, 'update'])
                ->middleware('can:worker-update')
                ->name('worker.update');

            Route::delete('/delete/{id}', [WorkerController::class, 'destroy'])
                ->middleware('can:ws-worker-delete')
                ->name('worker.delete');

        });
        Route::group(['prefix' => 'payment'], function () {
            Route::get('/list', [PaymentController::class, 'list'])
                ->middleware('can:payment-list')
                ->name('payment.list');

            Route::get('/show/{id}', [PaymentController::class, 'show'])
                ->middleware('can:payment-show')
                ->name('payment.show');

        });
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', LoginController::class);
    });
});
