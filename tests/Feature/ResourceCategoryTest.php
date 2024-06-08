<?php

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

describe('Resource Category routes check', function () {
    it('should have all routes for /resource/category/', function () {
        $this->artisan('optimize:clear');
        // Define the expected route names
        $expectedRouteNames = [
            'resource.category.create',
            'resource.category.update',
            'resource.category.list',
            'resource.category.show',
            'resource.category.delete',
        ];

        // Collect routes and filter based on the prefix
        $customerRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, "api/v1/resource/{resourceId}/category");
        });

        // Assert that only the expected routes exist
        $customerRoutes->each(function ($route) use ($expectedRouteNames) {
            $this->assertTrue(in_array($route->getName(), $expectedRouteNames),
                "Route {$route->getName()} does not match expected routes.");

        });
        // Assert that there are routes found for /resource/category/
        $this->assertFalse($customerRoutes->isEmpty(), 'No routes found for /resource/category');

    });

});

describe('Resource Category Create', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
        $this->resource = \App\Models\Resource::factory()->create();

    });
    it('should prevent non auth creating new Resource Category', function () {
        $response = postJson("/api/v1/resource/".$this->resource->id."/category/create");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin creating new Resource', function () {

        $response = actingAs($this->notAdmin)->postJson("/api/v1/resource/".$this->resource->id."/category/create");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return validation error when data is missed', function () {
        $response = actingAs($this->admin)->postJson("/api/v1/resource/".$this->resource->id."/category/create", []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
    it('should create new Resource with valid data', function () {
        $response = actingAs($this->admin)->postJson("/api/v1/resource/".$this->resource->id."/category/create", [
            'name' => 'new',
            'description' => 'new',
        ]);
        $response->assertOk();
    });
});

describe('Resource Category Update', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->resource = \App\Models\Resource::factory()->create();
        $this->resourceCategory = \App\Models\ResourceCategory::factory()->create(['name' => 'new']);

    });

    it('should prevent non auth updating existed ResourceCategory', function () {
        $response = putJson("/api/v1/resource/".$this->resource->id."/category/update/".$this->resourceCategory->id, [
            'name' => 'new1',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin updating existed ResourceCategory', function () {

        $response = actingAs($this->notAdmin)
            ->putJson("/api/v1/resource/".$this->resource->id."/category/update/".$this->resourceCategory->id, [
            'name' => 'new1',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should not return validation error when data is missed', function () {

        $response = actingAs($this->admin)
            ->putJson("/api/v1/resource/".$this->resource->id."/category/update/".$this->resourceCategory->id, []);

        $response->assertStatus(Response::HTTP_OK);
    });
    it('should update existed Resource with valid data', function () {

        $response = actingAs($this->admin)
            ->putJson("/api/v1/resource/".$this->resource->id."/category/update/".$this->resourceCategory->id, [
            'name' => 'new1',
        ]);
        assertDatabaseHas('resource_categories', ['name' => 'new1']);
        $response->assertOk();
    });
});

describe('Resource Category List', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->resource = \App\Models\Resource::factory()->create();

        $this->resourceCategory1 = ResourceCategory::factory()->create(['name' => 'resource 1']);
        $this->resourceCategory2 = ResourceCategory::factory()->create(['name' => 'resource 2']);
        $this->resourceCategory3 = ResourceCategory::factory()->create(['name' => 'resource 3']);


    });
    it('should prevent non auth updating existed resource', function () {
        $response = getJson("/api/v1/resource/".$this->resource->id."/category/list");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should return data', function () {
        $response = actingAs($this->admin)->getJson("/api/v1/resource/".$this->resource->id."/category/list");
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment(['name' => 'resource 1']);
    });
});

describe('Resource Category Details', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->resourceCategory = ResourceCategory::factory()->create(['id' => 10, 'name' => 'resource 10']);
        $this->resource = \App\Models\Resource::factory()->create();

    });
    it('should prevent non auth show resource', function () {
        $response = getJson("/api/v1/resource/".$this->resource->id."/category/show/".$this->resourceCategory->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should return not found for un-existed resource', function () {
        $nonExisted = rand(222,333);
        $response = actingAs($this->admin)
            ->getJson("/api/v1/resource/".$this->resource->id."/category/show/".$nonExisted);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return data', function () {
        $response = actingAs($this->admin)
            ->getJson("/api/v1/resource/".$this->resource->id."/category/show/".$this->resourceCategory->id);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(6, 'data')
            ->assertJsonFragment(['name' => 'resource 10']);
    });
});

describe('Resource Category Delete', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->resource = \App\Models\Resource::factory()->create();
        $this->resourceCategory = ResourceCategory::factory()->create(['id' => 10, 'name' => 'resource 10']);

    });
    it('should prevent non auth delete resourceCategory', function () {
        $response = deleteJson("/api/v1/resource/".$this->resource->id."/category/delete/".$this->resourceCategory->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin delete existed resourceCategory', function () {
        $response = actingAs($this->notAdmin)
            ->deleteJson("/api/v1/resource/".$this->resource->id."/category/delete/".$this->resourceCategory->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found for un-existed resourceCategory', function () {
        $nonExisted = rand(222,333);
        $response = actingAs($this->admin)
            ->deleteJson("/api/v1/resource/".$this->resource->id."/category/delete/".$nonExisted);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should delete resourceCategory from database', function () {
        assertDatabaseCount(ResourceCategory::class, 2);
        actingAs($this->admin)
            ->deleteJson("/api/v1/resource/".$this->resource->id."/category/delete/".$this->resourceCategory->id);
        assertSoftDeleted('resource_categories', ['id' => $this->resourceCategory->id]);
        $count = \App\Models\ResourceCategory::query()->get()->count();
        // Assert the count
        $this->assertEquals(1, $count, "The count of non deleted should be 1");

    });
});
