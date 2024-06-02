<?php

use App\Models\User;
use App\Models\WorkSiteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

describe('WorkSiteCategory routes check', function () {
    it('should have all routes for /worksite/category/', function () {
        $this->artisan('optimize:clear');
        // Define the expected route names
        $expectedRouteNames = [
            'worksite.category.create',
            'worksite.category.update',
            'worksite.category.list',
            'worksite.category.show',
            'worksite.category.delete',
        ];

        // Collect routes and filter based on the prefix
        $customerRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/v1/worksite/category');
        });

        // Assert that only the expected routes exist
        $customerRoutes->each(function ($route) use ($expectedRouteNames) {
            $this->assertTrue(in_array($route->getName(), $expectedRouteNames),
                "Route {$route->getName()} does not match expected routes.");

        });
        // Assert that there are routes found for /worksite/category/
        $this->assertFalse($customerRoutes->isEmpty(), 'No routes found for /worksite/category');

    });

});

describe('WorkSiteCategory Create', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

    });
    it('should prevent non auth creating new category', function () {
        $response = postJson('/api/v1/worksite/category/create', [
            'name' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin creating new category', function () {

        $response = actingAs($this->notAdmin)->postJson('/api/v1/worksite/category/create', [
            'name' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return validation error when data is missed', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/worksite/category/create', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
    it('should create new category with valid data', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/worksite/category/create', [
            'name' => 'new',
        ]);
        $response->assertOk();
    });
});

describe('WorkSiteCategory Update', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->category = WorkSiteCategory::factory()->create(['name' => 'new']);

    });

    it('should prevent non auth updating existed category', function () {
        $response = putJson('/api/v1/worksite/category/update/'.$this->category->id, [
            'name' => 'new1',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin updating existed category', function () {

        $response = actingAs($this->notAdmin)->putJson('/api/v1/worksite/category/update/'.$this->category->id, [
            'name' => 'new1',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should not return validation error when data is missed', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/worksite/category/create', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
    it('should update existed category with valid data', function () {

        $response = actingAs($this->admin)->putJson('/api/v1/worksite/category/update/'.$this->category->id, [
            'name' => 'new1',
        ]);
        assertDatabaseHas('work_site_categories', ['name' => 'new1']);
        $response->assertOk();
    });
});

describe('WorkSiteCategory List', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->category1 = WorkSiteCategory::factory()->create(['name' => 'category 1']);
        $this->category2 = WorkSiteCategory::factory()->create(['name' => 'category 2']);
        $this->category3 = WorkSiteCategory::factory()->create(['name' => 'category 3']);

    });
    it('should prevent non auth updating existed category', function () {
        $response = getJson('/api/v1/worksite/category/list/');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should return data', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/worksite/category/list/');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['name' => 'category 1']);
    });
});

describe('WorkSiteCategory Details', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->category = WorkSiteCategory::factory()->create(['id' => 10, 'name' => 'category 10']);

    });
    it('should prevent non auth show category', function () {
        $response = getJson('/api/v1/worksite/category/show/'.$this->category->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should return not found for un-existed category', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/worksite/category/show/2');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return data', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/worksite/category/show/'.$this->category->id);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'category 10']);
    });
});

describe('WorkSiteCategory Delete', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->category = WorkSiteCategory::factory()->create(['id' => 10, 'name' => 'category 10']);

    });
    it('should prevent non auth delete category', function () {
        $response = deleteJson('/api/v1/worksite/category/delete/'.$this->category->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin delete existed category', function () {
        $response = actingAs($this->notAdmin)->deleteJson('/api/v1/worksite/category/delete/'.$this->category->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found for un-existed category', function () {
        $response = actingAs($this->admin)->deleteJson('/api/v1/worksite/category/delete/2');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should delete category from database', function () {
        assertDatabaseCount('work_site_categories', 1);
        actingAs($this->admin)->deleteJson('/api/v1/worksite/category/delete/'.$this->category->id);
        assertSoftDeleted('work_site_categories', ['id' => $this->category->id]);
    });
});
