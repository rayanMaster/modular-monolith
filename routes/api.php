<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DailyAttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WorksiteCategoryController;
use App\Http\Controllers\WorksiteController;
use App\Http\Controllers\WorksiteCustomerController;
use App\Http\Controllers\WorksiteItemController;
use App\Http\Controllers\WorksitePaymentController;
use App\Http\Middleware\CheckWorksiteAttendance;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'worksite'], function () {
            Route::post('create', [WorksiteController::class, 'store'])
                ->middleware('can:worksite-create')
                ->name('worksite.create');

            Route::get('list', [WorksiteController::class, 'list'])
                ->middleware('can:worksite-list')
                ->name('worksite.list');

            Route::get('show/{id}', [WorksiteController::class, 'show'])
                ->middleware('can:worksite-show')
                ->name('worksite.show');

            Route::put('update/{id}', [WorksiteController::class, 'update'])
                ->middleware('can:worksite-update')
                ->name('worksite.update');

            Route::post('close/{id}', [WorksiteController::class, 'close'])
                ->middleware('can:worksite-close')
                ->name('worksite.close');

            Route::delete('delete/{id}', [WorksiteController::class, 'delete'])
                ->middleware('can:worksite-delete')
                ->name('worksite.delete');

            Route::group(['prefix' => 'category'], function () {

                Route::get('/list', [WorksiteCategoryController::class, 'list'])
                    ->middleware('can:worksite-category-list')
                    ->name('worksite.category.list');

                Route::get('/show/{id}', [WorksiteCategoryController::class, 'show'])
                    ->middleware('can:worksite-category-show')
                    ->name('worksite.category.show');

                Route::post('/store', [WorksiteCategoryController::class, 'store'])
                    ->middleware('can:worksite-category-create')
                    ->name('worksite.category.create');

                Route::put('/update/{id}', [WorksiteCategoryController::class, 'update'])
                    ->middleware('can:worksite-category-update')
                    ->name('worksite.category.update');

                Route::delete('/delete/{id}', [WorksiteCategoryController::class, 'destroy'])
                    ->middleware('can:worksite-category-delete')
                    ->name('worksite.category.delete');
            });

            Route::group(['prefix' => '{worksiteId}/payment'], function () {

                Route::post('create', [WorksitePaymentController::class, 'create'])
                    ->middleware('can:payment-create')
                    ->name('worksite.payment.create');

                Route::get('list', [WorksitePaymentController::class, 'list'])
                    ->middleware('can:payment-list')
                    ->name('worksite.payment.list');
                //
                //                Route::post('show/{id}', WorksitePaymentController::class)
                //                    ->middleware('can:payment-show')
                //                    ->name('worksite.payment.show');
            });

            Route::group(['prefix' => '{worksiteId}/item'], function () {
                Route::post('/add', [WorksiteItemController::class, 'addItems'])
                    ->middleware('can:worksite-item-add')
                    ->name('worksite.item.add');

                Route::get('/list', [WorksiteItemController::class, 'list'])
                    ->middleware('can:worksite-item-list')
                    ->name('worksite.item.list');
                //
                //                Route::post('update/{id}', WorksitePaymentController::class)
                //                    ->middleware('can:worksite-item-update')
                //                    ->name('worksite.item.update');
                //
                //                Route::post('show/{id}', WorksitePaymentController::class)
                //                    ->middleware('can:worksite-item-show')
                //                    ->name('worksite.item.show');
                //
                //                Route::post('delete/{id}', WorksitePaymentController::class)
                //                    ->middleware('can:worksite-item-delete')
                //                    ->name('worksite.item.delete');
            });

            Route::group(['prefix' => '{worksiteId}/employee'], function () {
                Route::post('assign', [WorksiteController::class, 'assignEmployee'])
                    ->middleware('can:worksite-employee-assign')
                    ->name('worksite.employee.assign');

                //                Route::post('list', WorksitePaymentController::class)
                //                    ->middleware('can:worksite-item-list')
                //                    ->name('worksite.item.list');
                //
                //                Route::post('update/{id}', WorksitePaymentController::class)
                //                    ->middleware('can:worksite-item-update')
                //                    ->name('worksite.item.update');
                //
                //                Route::post('show/{id}', WorksitePaymentController::class)
                //                    ->middleware('can:worksite-item-show')
                //                    ->name('worksite.item.show');
                //
                //                Route::post('delete/{id}', WorksitePaymentController::class)
                //                    ->middleware('can:worksite-item-delete')
                //                    ->name('worksite.item.delete');
            });

            Route::group(['prefix' => '{worksiteId}/contractor'], function () {
                Route::put('{contractorId}/assign', [WorksiteController::class, 'assignContractor'])
                    ->middleware('can:worksite-contractor-assign')
                    ->name('worksite.contractor.assign');

                Route::put('{contractorId}/unAssign', [WorksiteController::class, 'unAssignContractor'])
                    ->middleware('can:worksite-contractor-assign')
                    ->name('worksite.contractor.unAssign');
            });

            Route::group(['prefix' => '{worksiteId}/customer'], function () {
                Route::post('/{customerId}/assign', [WorksiteCustomerController::class, 'assignCustomer'])
                    ->middleware('can:worksite-customer-assign')
                    ->name('worksite.customer.assign');
            });
        });
        Route::group(['prefix' => 'item'], function () {
            Route::get('list', [ItemController::class, 'list'])
                ->middleware('can:item-list')
                ->name('item.list');
            Route::get('show/{id}', [ItemController::class, 'show'])
                ->middleware('can:item-show')
                ->name('item.show');
            Route::post('create', [ItemController::class, 'store'])
                ->middleware('can:item-create')
                ->name('item.create');
            Route::put('update/{id}', [ItemController::class, 'update'])
                ->middleware('can:item-update')
                ->name('item.update');
            Route::delete('delete/{id}', [ItemController::class, 'destroy'])
                ->middleware('can:item-delete')
                ->name('item.delete');

            Route::group(['prefix' => '{itemId}/category'], function () {
                Route::get('/list', [ItemCategoryController::class, 'list'])
                    ->middleware('can:item-category-list')
                    ->name('item.category.list');
                Route::get('/show/{id}', [ItemCategoryController::class, 'show'])
                    ->middleware('can:item-category-show')
                    ->name('item.category.show');
                Route::post('/create', [ItemCategoryController::class, 'store'])
                    ->middleware('can:item-category-create')
                    ->name('item.category.create');
                Route::put('/update/{id}', [ItemCategoryController::class, 'update'])
                    ->middleware('can:item-category-update')
                    ->name('item.category.update');
                Route::delete('/delete/{id}', [ItemCategoryController::class, 'destroy'])
                    ->middleware('can:item-category-delete')
                    ->name('item.category.delete');
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
            Route::post('/list', [EmployeeController::class, 'list'])
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

            Route::group(['prefix' => '{employeeId}/daily_attendance'], function () {
                Route::post('create', [DailyAttendanceController::class, 'store'])
                    ->middleware('can:employee-attendance-add')
                    ->name('employee.dailyAttendance.add');

                Route::put('update/{dailyAttendanceId}', [DailyAttendanceController::class, 'update'])
                    ->middleware('can:employee-attendance-update')
                    ->name('employee.dailyAttendance.update');

                Route::get('list', [DailyAttendanceController::class, 'list'])
                    ->middleware('can:employee-attendance-list')
                    ->name('employee.dailyAttendance.list');
            });

        });
        Route::group(['prefix' => 'warehouse'], function () {
            Route::post('/store', [WarehouseController::class, 'store'])
                ->middleware('can:warehouse-create')
                ->name('warehouse.create');

            Route::put('/update/{warehouseId}', [WarehouseController::class, 'update'])
                ->middleware('can:warehouse-update')
                ->name('warehouse.update');

            Route::get('/list', [WarehouseController::class, 'list'])
                ->middleware('can:warehouse-list')
                ->name('warehouse.list');

            Route::get('/show/{warehouseId}', [WarehouseController::class, 'show'])
                ->middleware('can:warehouse-show')
                ->name('warehouse.show');

            Route::delete('/delete/{warehouseId}', [WarehouseController::class, 'destroy'])
                ->middleware('can:warehouse-delete')
                ->name('warehouse.delete');

            Route::group(['prefix' => '{warehouseId}/items'], function () {
                Route::post('add', [WarehouseController::class, 'addItems'])
                    ->middleware('can:warehouse-item-add')
                    ->name('warehouse.item.create');

                Route::post('move', [WarehouseController::class, 'moveItems'])
                    ->middleware('can:warehouse-item-move')
                    ->name('warehouse.item.move');

                Route::post('update', [WarehouseController::class, 'updateItems'])
                    ->middleware('can:warehouse-item-update')
                    ->name('warehouse.item.update');

                Route::post('list', [WarehouseController::class, 'listItems'])
                    ->middleware('can:warehouse-item-list')
                    ->name('warehouse.item.list');
            });
        });
        Route::group(['prefix' => 'order'], function () {
            Route::post('/create', [OrderController::class, 'store'])
                ->middleware(['can:order-create', CheckWorksiteAttendance::class])
                ->name('order.create');
            Route::put('/update/{orderId}', [OrderController::class, 'update'])
                ->middleware('can:order-update')
                ->name('order.update');
            Route::get('/list', [OrderController::class, 'list'])
                ->middleware('can:order-list')
                ->name('order.list');
            Route::get('/show/{orderId}', [OrderController::class, 'show'])
                ->middleware('can:order-show')
                ->name('order.show');
        });
        Route::group(['prefix' => 'city'], function () {
            Route::post('/create', [CityController::class, 'create'])
                ->middleware('can:city-create')
                ->name('city.create');
            Route::put('/update/{cityId}', [CityController::class, 'update'])
                ->middleware('can:city-update')
                ->name('city.update');
            Route::get('/list', [CityController::class, 'list'])
                ->middleware('can:city-list')
                ->name('city.list');
            Route::get('/show/{cityId}', [CityController::class, 'show'])
                ->middleware('can:city-show')
                ->name('city.show');
            Route::delete('/delete/{cityId}', [CityController::class, 'delete'])
                ->middleware('can:city-delete')
                ->name('city.delete');
        });
    });
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', LoginController::class)->name('login');
    });
});
