<?php

use App\Enums\PaymentTypesEnum;
use App\Models\Customer;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\WorkSite;
use App\Models\WorkSiteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\{postJson, getJson, putJson, actingAs, assertDatabaseHas, assertDatabaseCount};

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
            'worksite.resource.list',
            'worksite.resource.create',
            'worksite.resource.show',
            'worksite.resource.delete',
            'worksite.resource.update',

            'worksite.payment.create',
            'worksite.payment.list',
            'worksite.payment.show',

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
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);

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
            'status_on_receive' => \App\Enums\WorkSiteStatusesEnum::SCRATCH->value,
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
        $path = 'workSite';
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
    test('As a non-authenticated, I cant create a main worksite', function () {

        $response = $this->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

    });
    test('As an administrator, should return validation error when no data', function () {

        $mainWorkSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'success' => false,
                'error_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'The title field is required.',
                'data' => [
                    'title' => ['The title field is required.'],
                    'description' => ['The description field is required.'],
                ],
            ]);

    });
    test('As not admin, I cant create a main worksite', function () {

        $siteManager = \App\Models\User::factory()->siteManager()->create();
        expect($siteManager->hasRole('site_manager'))->toBe(true);

        $response = $this->actingAs($siteManager)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

    });
    test('As an administrator, I want to create a sub worksites', function () {

        $mainWorkSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = actingAs($admin)->postJson('/api/v1/worksite/create', [
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
            ->and($workSite?->description)->toBe('this worksite is for freeTown')
            ->and($workSite->main_worksite)->toBe($mainWorkSite->id);

    });


});


describe('Update WorkSite', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);

        $this->admin = \App\Models\User::factory()->admin()->create();
        $this->workSite = WorkSite::factory()->create();

    });

    test('As a non-authenticated, I cant update a main worksite', function () {
        $response = putJson('/api/v1/worksite/update/' . $this->workSite->id, []);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

    });
    test('As not admin, I cant update a main worksite', function () {

        $siteManager = \App\Models\User::factory()->siteManager()->create();
        expect($siteManager->hasRole('site_manager'))->toBe(true);

        $response = actingAs($siteManager)->putJson('/api/v1/worksite/update/' . $this->workSite->id, []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

    });
    test('As an administrator, I want to update worksite main info', function () {

        assertDatabaseCount(WorkSite::class, 1);
        actingAs($this->admin)->putJson('/api/v1/worksite/update/' . $this->workSite->id, [
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown new'
        ]);

        assertDatabaseHas(WorkSite::class, [
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown new',
        ]);


    });

});
describe('List WorkSites', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);

        $this->notAdmin = \App\Models\User::factory()->siteManager()->create();
        expect($this->notAdmin->hasRole('site_manager'))->toBe(true);

        //create multiple worksites in DB
        WorkSite::factory()->count(3)->create();

    });
    test('As a non-authenticated, I cant show list of worksites', function () {
        $response = getJson('/api/v1/worksite/list');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    test('As not admin, I cant show list of worksites', function () {
        $response = actingAs($this->notAdmin)->getJson('/api/v1/worksite/list');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    test('As an admin, I can show list of worksites', function () {
        assertDatabaseCount(WorkSite::class, 3);
    });

});
describe('Show WorkSites Details', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);

    });
    test('As a non-authenticated, I cant show details of a worksite', function () {

    });
    test('As not admin, I cant show details of a worksite', function () {

    });
    it('should return not found error if worksite not existed in database', function () {

    });
    test('As an admin, I can show details of a worksite', function () {

    });

});
describe('Close WorkSites', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);

    });
    test('As a non-authenticated, I cant close a worksite', function () {

    });
    test('As not admin, I cant close a worksite', function () {

    });
    it('should prevent me closing worksite with active worksites', function () {

    });
    it('should prevent me closing worksite with unpaid payments', function () {

    });
    test('As an admin, I can close a worksite', function () {

    });

});
describe('Manage payment to a worksite', function () {
    test('As a non-authenticated, I cant make a payment', function () {

    });
    test('As not admin, I cant make a payment', function () {

    });

    it('should prevent updating existed payment', function () {

    });
    it('should prevent removing existed payment', function () {

    });
    it('should make a payment to existed worksite', function () {

    });
});
describe('Manage resources of the worksite', function () {
    test('As a non-authenticated, I cant manage resource of a worksite', function () {

    });
    test('As not admin, I cant manage resource of a worksite', function () {

    });

    it('should add new resource to a worksite', function () {

    });
    it('should update existed resource of a worksite', function () {

    });
    it('should delete resources of a worksite', function () {

    });
    it('should show all resources a worksite', function () {

    });
    it('should show details of a resource in the worksite', function () {

    });
});
describe('Manage workers of the worksite', function () {
    test('As a non-authenticated, I cant manage workers of a worksite', function () {

    });
    test('As not admin, I cant manage workers of a worksite', function () {

    });
    it('should add new worker to a worksite', function () {

    });
    it('should update a worker of a worksite', function () {

    });
    it('should delete a worker of a worksite', function () {

    });
    it('should show all workers of the worksite', function () {

    });
    it('should show details of a worker in a worksite', function () {

    });
});
describe('manage customer of the worksite', function () {
    test('As a non-authenticated, I cant manage resource of a worksite', function () {

    });
    test('As not admin, I cant manage resource of a worksite', function () {

    });

    it('should add new customer to a worksite', function () {

    });
    it('should update a customer details of a worksite', function () {

    });
    it('should prevent delete a customer of an active worksite', function () {

    });
    it('should delete a customer of a closed worksite', function () {

    });
    it('should show details of a customer in a worksite', function () {

    });
});
