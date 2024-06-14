<?php

use App\Http\Controllers\ContractorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ResourceCategoryController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\WorkSiteCategoryController;
use App\Http\Controllers\WorkSiteController;
use App\Http\Controllers\WorkSiteCustomerController;
use App\Http\Controllers\WorkSitePaymentController;
use App\Http\Controllers\WorkSiteResourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'worksite'], function () {
            Route::post('/create', [WorkSiteController::class, 'store'])
                ->middleware('can:worksite-create')
                ->name('worksite.create');

            Route::get('/list', [WorkSiteController::class, 'list'])
                ->middleware('can:worksite-list')
                ->name('worksite.list');

            Route::get('/show/{id}', [WorkSiteController::class, 'show'])
                ->middleware('can:worksite-show')
                ->name('worksite.show');

            Route::put('/update/{id}', [WorkSiteController::class, 'update'])
                ->middleware('can:worksite-update')
                ->name('worksite.update');

            Route::post('/close/{id}', [WorkSiteController::class, 'close'])
                ->middleware('can:worksite-close')
                ->name('worksite.close');

            Route::delete('/delete/{id}', [WorkSiteController::class, 'delete'])
                ->middleware('can:worksite-delete')
                ->name('worksite.delete');

            Route::group(['prefix' => 'category'], function () {

                Route::get('/list', [WorkSiteCategoryController::class, 'list'])
                    ->middleware('can:worksite-category-list')
                    ->name('worksite.category.list');

                Route::get('/show/{id}', [WorkSiteCategoryController::class, 'show'])
                    ->middleware('can:worksite-category-show')
                    ->name('worksite.category.show');

                Route::post('/create', [WorkSiteCategoryController::class, 'create'])
                    ->middleware('can:worksite-category-create')
                    ->name('worksite.category.create');

                Route::put('/update/{id}', [WorkSiteCategoryController::class, 'update'])
                    ->middleware('can:worksite-category-update')
                    ->name('worksite.category.update');

                Route::delete('/delete/{id}', [WorkSiteCategoryController::class, 'destroy'])
                    ->middleware('can:worksite-category-delete')
                    ->name('worksite.category.delete');
            });

            Route::group(['prefix' => '{worksiteId}/payment'], function () {

                Route::post('create', [WorkSitePaymentController::class,'create'])
                    ->middleware('can:payment-create')
                    ->name('worksite.payment.create');

                Route::get('list', [WorkSitePaymentController::class,'list'])
                    ->middleware('can:payment-list')
                    ->name('worksite.payment.list');
//
//                Route::post('show/{id}', WorkSitePaymentController::class)
//                    ->middleware('can:payment-show')
//                    ->name('worksite.payment.show');
            });

            Route::group(['prefix' => '{worksiteId}/resource'], function () {
                Route::post('/{resourceId}/add', [WorkSiteResourceController::class,'add'])
                    ->middleware('can:worksite-resource-add')
                    ->name('worksite.resource.add');

                Route::get('/list', [WorkSiteResourceController::class,'list'])
                    ->middleware('can:worksite-resource-list')
                    ->name('worksite.resource.list');
//
//                Route::post('update/{id}', WorkSitePaymentController::class)
//                    ->middleware('can:worksite-resource-update')
//                    ->name('worksite.resource.update');
//
//                Route::post('show/{id}', WorkSitePaymentController::class)
//                    ->middleware('can:worksite-resource-show')
//                    ->name('worksite.resource.show');
//
//                Route::post('delete/{id}', WorkSitePaymentController::class)
//                    ->middleware('can:worksite-resource-delete')
//                    ->name('worksite.resource.delete');
            });

            Route::group(['prefix' => '{worksiteId}/employee'], function () {
                Route::post('assign', [WorkSiteController::class, 'assignEmployee'])
                    ->middleware('can:worksite-employee-assign')
                    ->name('worksite.employee.assign');

                //                Route::post('list', WorkSitePaymentController::class)
                //                    ->middleware('can:worksite-resource-list')
                //                    ->name('worksite.resource.list');
                //
                //                Route::post('update/{id}', WorkSitePaymentController::class)
                //                    ->middleware('can:worksite-resource-update')
                //                    ->name('worksite.resource.update');
                //
                //                Route::post('show/{id}', WorkSitePaymentController::class)
                //                    ->middleware('can:worksite-resource-show')
                //                    ->name('worksite.resource.show');
                //
                //                Route::post('delete/{id}', WorkSitePaymentController::class)
                //                    ->middleware('can:worksite-resource-delete')
                //                    ->name('worksite.resource.delete');
            });

            Route::group(['prefix' => '{worksiteId}/contractor'], function () {
                Route::put('assign', [WorkSiteController::class, 'assignContractor'])
                    ->middleware('can:worksite-contractor-assign')
                    ->name('worksite.contractor.assign');
            });

            Route::group(['prefix' => '{worksiteId}/customer'], function () {
                Route::post('/{customerId}/assign', [WorkSiteCustomerController::class, 'assignCustomer'])
                    ->middleware('can:worksite-customer-assign')
                    ->name('worksite.customer.assign');
            });
        });

        Route::group(['prefix' => 'resource'], function () {
            Route::get('/list', [ResourceController::class, 'list'])
                ->middleware('can:resource-list')
                ->name('resource.list');
            Route::get('/show/{id}', [ResourceController::class, 'show'])
                ->middleware('can:resource-show')
                ->name('resource.show');
            Route::post('/create', [ResourceController::class, 'store'])
                ->middleware('can:resource-create')
                ->name('resource.create');
            Route::put('/update/{id}', [ResourceController::class, 'update'])
                ->middleware('can:resource-update')
                ->name('resource.update');
            Route::delete('/delete/{id}', [ResourceController::class, 'destroy'])
                ->middleware('can:resource-delete')
                ->name('resource.delete');

            Route::group(['prefix' => '{resourceId}/category'], function () {
                Route::get('/list', [ResourceCategoryController::class, 'list'])
                    ->middleware('can:resource-category-list')
                    ->name('resource.category.list');
                Route::get('/show/{id}', [ResourceCategoryController::class, 'show'])
                    ->middleware('can:resource-category-show')
                    ->name('resource.category.show');
                Route::post('/create', [ResourceCategoryController::class, 'store'])
                    ->middleware('can:resource-category-create')
                    ->name('resource.category.create');
                Route::put('/update/{id}', [ResourceCategoryController::class, 'update'])
                    ->middleware('can:resource-category-update')
                    ->name('resource.category.update');
                Route::delete('/delete/{id}', [ResourceCategoryController::class, 'destroy'])
                    ->middleware('can:resource-category-delete')
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
                ->middleware('can:customer-create')
                ->name('customer.create');

            Route::put('/update/{id}', [CustomerController::class, 'update'])
                ->middleware('can:customer-update')
                ->name('customer.update');

            Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])
                ->middleware('can:customer-delete')
                ->name('customer.delete');

        });
        Route::group(['prefix' => 'contractor'], function () {
            Route::get('/list', [ContractorController::class, 'list'])
                ->middleware('can:contractor-list')
                ->name('contractor.list');

            Route::get('/show/{id}', [ContractorController::class, 'show'])
                ->middleware('can:contractor-show')
                ->name('contractor.show');

            Route::post('/create', [ContractorController::class, 'store'])
                ->middleware('can:contractor-create')
                ->name('contractor.create');

            Route::put('/update/{id}', [ContractorController::class, 'update'])
                ->middleware('can:contractor-update')
                ->name('contractor.update');

            Route::delete('/delete/{id}', [ContractorController::class, 'destroy'])
                ->middleware('can:contractor-delete')
                ->name('contractor.delete');

        });
        Route::group(['prefix' => 'employee'], function () {
            Route::get('/list', [EmployeeController::class, 'list'])
                ->middleware('can:employee-list')
                ->name('employee.list');

            Route::get('/show/{id}', [EmployeeController::class, 'show'])
                ->middleware('can:employee-show')
                ->name('employee.show');

            Route::post('/create', [EmployeeController::class, 'store'])
                ->middleware('can:employee-create')
                ->name('employee.create');

            Route::put('/update/{id}', [EmployeeController::class, 'update'])
                ->middleware('can:employee-update')
                ->name('employee.update');

            Route::delete('/delete/{id}', [EmployeeController::class, 'destroy'])
                ->middleware('can:employee-delete')
                ->name('employee.delete');

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
