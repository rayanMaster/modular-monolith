<?php

use App\Enums\PaymentTypesEnum;
use App\Models\Customer;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\WorkSite;
use App\Models\WorkSiteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

describe('WorkSite routes check', function () {
    it('should have all routes for /worksite', function () {
        $this->artisan('optimize:clear');
        // Define the expected route names
        $expectedRouteNames = [
            'worksite.create',
            'worksite.update',
            'worksite.list',
            'worksite.show',
            'worksite.delete',
            'worksite.payment.create',

            'worksite.category.create',
            'worksite.category.update',
            'worksite.category.list',
            'worksite.category.show',
            'worksite.category.delete',
        ];

        // Collect routes and filter based on the prefix
        $worksiteRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/v1/worksite/');
        });

        // Assert that only the expected routes exist
        $worksiteRoutes->each(function ($route) use ($expectedRouteNames) {
            $this->assertTrue(in_array($route->getName(), $expectedRouteNames),
                "Route {$route->getName()} does not match expected routes.");

        });
        // Assert that there are routes found for /worksite
        $this->assertFalse($worksiteRoutes->isEmpty(), 'No routes found for /worksite');

    });

});

describe('Create WorkSite', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);

    });

    test('As an administrator, I want to create a main worksite', function () {

        $wsCategory = WorkSiteCategory::factory()->create();

        $customer = Customer::factory()->create();

        $workSiteResourceCategory = ResourceCategory::factory()->create();

        $workSiteResource1 = Resource::factory()->create([
            'resource_category_id' => $workSiteResourceCategory->id,
        ]);
        $workSiteResource2 = Resource::factory()->create([
            'resource_category_id' => $workSiteResourceCategory->id,
        ]);

        $admin = \App\Models\User::factory()->admin()->create([
            'name' => 'Admin1',
            'email' => 'admin1@admin.com',
            'password' => 'admin123',
        ]);
        expect($admin->hasRole('admin'))->toBe(true);

        Storage::fake();

        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');
        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
            'customer_id' => $customer?->id,
            'category_id' => $wsCategory?->id, // construction
            'main_worksite' => null, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address' => 1,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'status_on_receive' => 1,
            'resources' => [
                ['id' => $workSiteResource1?->id, 'quantity' => 23, 'price' => 34],
                ['id' => $workSiteResource2?->id, 'quantity' => 30, 'price' => 30],
            ],
            'payments' => [
                ['payment_amount' => 3000,
                    'payment_date' => '2024-04-12 10:34'],
            ],

            'image' => $file,

        ]);
        $response->assertOk();

        // Assert the file was stored...
        $path = lcfirst('WorkSite');
        $name = 'test' . '_' . now()->format('YmdH');
        $fullPath = public_path('storage/' . $path) . '/' . $name . '.webp';
        $this->assertFileExists($fullPath);

        $workSite = WorkSite::query()->latest('id')->first();

        expect($workSite->main_worksite)->toBeNull('that indicates that worksite is main')
            ->and($workSite?->title)->toBe('worksite A')
            ->and($workSite?->description)->toBe('this worksite is for freeTown')
            ->and($workSite?->resources[0]->pivot->getAttributes())->toBe(
                ['work_site_id' => $workSite->id,
                    'resource_id' => $workSiteResource1->id,
                    'quantity' => 23,
                    'price' => '34.00'])
            ->and($workSite?->resources[1]->pivot->getAttributes())->toBe(
                ['work_site_id' => $workSite->id,
                    'resource_id' => $workSiteResource2->id,
                    'quantity' => 30,
                    'price' => '30.00'])
            ->and($workSite->lastPayment->payable_id)->toBe($workSite->id)
            ->and($workSite->lastPayment->payable_type)->toBe('worksite')
            ->and($workSite->lastPayment->amount)->toBe('3000.00')
            ->and($workSite->lastPayment->payment_date)->toBe('2024-04-12 10:34:00')
            ->and($workSite->lastPayment->payment_type)->toBe(PaymentTypesEnum::CASH->value);

    });
    test('As a guest, I cant create a main worksite', function () {

        $response = $this->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
        ]);
        $response->assertStatus(401);

    });
    test('As not admin, I cant create a main worksite', function () {

        $siteManager = \App\Models\User::factory()->siteManager()->create();
        expect($siteManager->hasRole('site_manager'))->toBe(true);

        $response = $this->actingAs($siteManager)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
        ]);
        $response->assertStatus(403);

    });

    test('As an administrator, I want to create a sub worksites under main worksite without payment', function () {

        $mainWorkSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown',
            'customer_id' => $mainWorkSite->customer?->id,
            'category_id' => $mainWorkSite->category?->id, // construction
            'main_worksite' => $mainWorkSite->id, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address' => 1,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'status_on_receive' => 1,

        ]);
        $response->assertOk();

        $workSite = WorkSite::query()->latest('id')->first();

        expect($workSite?->title)->toBe('worksite AB')
            ->and($workSite?->description)->toBe('this worksite is for freeTown');

    })->skip();
    test('As an administrator, should return validation error when no data', function () {

        $mainWorkSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    })->skip();

});
describe('Update WorkSite', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);

    });

    test('As an administrator, I want to create a main worksite', function () {

        $wsCategory = WorkSiteCategory::factory()->create();

        $customer = Customer::factory()->create();

        $workSiteResourceCategory = ResourceCategory::factory()->create();

        $workSiteResource1 = Resource::factory()->create([
            'resource_category_id' => $workSiteResourceCategory->id,
        ]);
        $workSiteResource2 = Resource::factory()->create([
            'resource_category_id' => $workSiteResourceCategory->id,
        ]);

        $admin = \App\Models\User::factory()->admin()->create([
            'name' => 'Admin1',
            'email' => 'admin1@admin.com',
            'password' => 'admin123',
        ]);
        expect($admin->hasRole('admin'))->toBe(true);

        Storage::fake();

        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');
        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
            'customer_id' => $customer?->id,
            'category_id' => $wsCategory?->id, // construction
            'main_worksite' => null, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address' => 1,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'status_on_receive' => 1,
            'resources' => [
                ['id' => $workSiteResource1?->id, 'quantity' => 23, 'price' => 34],
                ['id' => $workSiteResource2?->id, 'quantity' => 30, 'price' => 30],
            ],
            'payments' => [
                ['payment_amount' => 3000,
                    'payment_date' => '2024-04-12 10:34'],
            ],

            'image' => $file,

        ]);
        $response->assertOk();

        // Assert the file was stored...
        $path = lcfirst('WorkSite');
        $name = 'test' . '_' . now()->format('YmdH');
        $fullPath = public_path('storage/' . $path) . '/' . $name . '.webp';
        $this->assertFileExists($fullPath);

        $workSite = WorkSite::query()->latest('id')->first();

        expect($workSite->main_worksite)->toBeNull('that indicates that worksite is main')
            ->and($workSite?->title)->toBe('worksite A')
            ->and($workSite?->description)->toBe('this worksite is for freeTown')
            ->and($workSite?->resources[0]->pivot->getAttributes())->toBe(
                ['work_site_id' => $workSite->id,
                    'resource_id' => $workSiteResource1->id,
                    'quantity' => 23,
                    'price' => '34.00'])
            ->and($workSite?->resources[1]->pivot->getAttributes())->toBe(
                ['work_site_id' => $workSite->id,
                    'resource_id' => $workSiteResource2->id,
                    'quantity' => 30,
                    'price' => '30.00'])
            ->and($workSite->lastPayment->payable_id)->toBe($workSite->id)
            ->and($workSite->lastPayment->payable_type)->toBe('worksite')
            ->and($workSite->lastPayment->amount)->toBe('3000.00')
            ->and($workSite->lastPayment->payment_date)->toBe('2024-04-12 10:34:00')
            ->and($workSite->lastPayment->payment_type)->toBe(PaymentTypesEnum::CASH->value);

    });
    test('As a guest, I cant create a main worksite', function () {

        $response = $this->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
        ]);
        $response->assertStatus(401);

    });
    test('As not admin, I cant create a main worksite', function () {

        $siteManager = \App\Models\User::factory()->siteManager()->create();
        expect($siteManager->hasRole('site_manager'))->toBe(true);

        $response = $this->actingAs($siteManager)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
        ]);
        $response->assertStatus(403);

    });

    test('As an administrator, I want to create a sub worksites under main worksite without payment', function () {

        $mainWorkSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown',
            'customer_id' => $mainWorkSite->customer?->id,
            'category_id' => $mainWorkSite->category?->id, // construction
            'main_worksite' => $mainWorkSite->id, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address' => 1,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'status_on_receive' => 1,

        ]);
        $response->assertOk();

        $workSite = WorkSite::query()->latest('id')->first();

        expect($workSite?->title)->toBe('worksite AB')
            ->and($workSite?->description)->toBe('this worksite is for freeTown');

    })->skip();
    test('As an administrator, should return validation error when no data', function () {

        $mainWorkSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    });

});
describe('List WorkSites', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);

    });


});
describe('Show WorkSites Details', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);

    });


});
describe('Close WorkSites', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);

    });


});
describe('Make payment to a worksite', function () {
});
describe('manage resources of the worksite', function () {
});
describe('manage workers of the worksite', function () {
});
describe('manage customer of the worksite', function () {
});
