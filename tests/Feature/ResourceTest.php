<?php

use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

describe('Resource routes check', function () {
    it('should have all routes for /resource/', function () {
        $this->artisan('optimize:clear');
        // Define the expected route names
        $expectedRouteNames = [
            'resource.create',
            'resource.update',
            'resource.list',
            'resource.show',
            'resource.delete',
            'resource.category.create',
            'resource.category.update',
            'resource.category.list',
            'resource.category.show',
            'resource.category.delete',
        ];

        // Collect routes and filter based on the prefix
        $customerRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/v1/resource');
        });

        // Assert that only the expected routes exist
        $customerRoutes->each(function ($route) use ($expectedRouteNames) {
            $this->assertTrue(in_array($route->getName(), $expectedRouteNames),
                "Route {$route->getName()} does not match expected routes.");

        });
        // Assert that there are routes found for /resource/category/
        $this->assertFalse($customerRoutes->isEmpty(), 'No routes found for /resource');

    });

});

describe('WorkSiteResource Create', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
        $this->resourceCategory = ResourceCategory::factory()->create();

    });
    it('should prevent non auth creating new Resource', function () {
        $response = postJson('/api/v1/resource/create');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin creating new Resource', function () {

        $response = actingAs($this->notAdmin)->postJson('/api/v1/resource/create');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return validation error when data is missed', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/resource/create', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
    it('should create new Resource with valid data', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/resource/create', [
            'name' => 'new',
            'description' => 'new',
            'resource_category_id' => $this->resourceCategory->id,
        ]);
        $response->assertOk();
    });
});

describe('WorkSite Resource Update', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->resource = \App\Models\Resource::factory()->create(['name' => 'new']);

    });

    it('should prevent non auth updating existed Resource', function () {
        $response = putJson('/api/v1/resource/update/'.$this->resource->id, [
            'name' => 'new1',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin updating existed Resource', function () {

        $response = actingAs($this->notAdmin)->putJson('/api/v1/resource/update/'.$this->resource->id, [
            'name' => 'new1',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should not return validation error when data is missed', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/resource/create', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
    it('should update existed Resource with valid data', function () {

        $response = actingAs($this->admin)->putJson('/api/v1/resource/update/'.$this->resource->id, [
            'name' => 'new1',
        ]);
        assertDatabaseHas('resources', ['name' => 'new1']);
        $response->assertOk();
    });
});

describe('WorkSite Resource List', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->resource1 = Resource::factory()->create(['name' => 'resource 1']);
        $this->resource2 = Resource::factory()->create(['name' => 'resource 2']);
        $this->resource3 = Resource::factory()->create(['name' => 'resource 3']);

    });
    it('should prevent non auth updating existed resource', function () {
        $response = getJson('/api/v1/resource/list/');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should return data', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/resource/list/');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['name' => 'resource 1']);
    });
});

describe('WorkSite Resource Details', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->resource = Resource::factory()->create(['id' => 10, 'name' => 'resource 10']);

    });
    it('should prevent non auth show resource', function () {
        $response = getJson('/api/v1/resource/show/'.$this->resource->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should return not found for un-existed resource', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/resource/show/2');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return data', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/resource/show/'.$this->resource->id);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(8, 'data')
            ->assertJsonFragment(['name' => 'resource 10']);
    });
});

describe('WorkSite Resource Delete', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->resource = Resource::factory()->create(['id' => 10, 'name' => 'resource 10']);

    });
    it('should prevent non auth delete resource', function () {
        $response = deleteJson('/api/v1/resource/delete/'.$this->resource->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin delete existed resource', function () {
        $response = actingAs($this->notAdmin)->deleteJson('/api/v1/resource/delete/'.$this->resource->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found for un-existed resource', function () {
        $response = actingAs($this->admin)->deleteJson('/api/v1/resource/delete/2');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should delete resource from database', function () {
        assertDatabaseCount(Resource::class, 1);
        actingAs($this->admin)->deleteJson('/api/v1/resource/delete/'.$this->resource->id);
        assertSoftDeleted('resources', ['id' => $this->resource->id]);
    });
});
