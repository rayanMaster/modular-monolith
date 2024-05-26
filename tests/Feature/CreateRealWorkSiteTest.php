<?php

use App\Models\Customer;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\WorkSite;
use App\Models\WorkSiteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

describe('Create WorkSite Controller', function () {

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

        $admin = \App\Models\User::factory()->admin()->create();
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
//        dd($response->getContent());
        $response->assertOk();

        // Assert the file was stored...
        $path = lcfirst('WorkSite');
        $name = 'test'.'_'.now()->format('YmdH');
        $fullPath = public_path('storage/'.$path).'/'.$name.'.webp';
        $this->assertFileExists($fullPath);

        $workSite = WorkSite::query()->latest('id')->first();

        expect($workSite->main_worksite)->toBeNull('that indicates that worksite is main')
            ->and($workSite?->title)->toBe('worksite A')
            ->and($workSite?->description)->toBe('this worksite is for freeTown')
            ->and($workSite?->resources[0]->pivot->getAttributes())->toBe(
                ['work_site_id' => 1,
                    'resource_id' => 1,
                    'quantity' => 23,
                    'price' => 34])
            ->and($workSite?->resources[1]->pivot->getAttributes())->toBe(
                ['work_site_id' => 1,
                    'resource_id' => 2,
                    'quantity' => 30,
                    'price' => 30])
            ->and($workSite->last_payment->work_site_id)->toBe($workSite->id)
            ->and($workSite->last_payment->amount)->toBe(3000)
            ->and($workSite->last_payment->payment_date)->toBe('2024-04-12 10:34')
            ->and($workSite->last_payment->payment_type)->toBe(1);

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

    });
    test('As an administrator, should return validation error when no data', function () {

        $mainWorkSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
        ]);
        $response->assertStatus(422);

    });

});
