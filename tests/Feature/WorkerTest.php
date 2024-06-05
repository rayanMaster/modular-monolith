<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\{postJson, actingAs, assertDatabaseHas, assertDatabaseCount};

describe('Worker routes check', function () {
    it('should have all routes for /worker', function () {
        $this->artisan('optimize:clear');
        // Define the expected route names
        $expectedRouteNames = [
            'worker.create',
            'worker.update',
            'worker.list',
            'worker.show',
            'worker.delete',
        ];

        // Collect routes and filter based on the prefix
        $customerRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/v1/worker/');
        });

        // Assert that only the expected routes exist
        $customerRoutes->each(function ($route) use ($expectedRouteNames) {
            $this->assertTrue(in_array($route->getName(), $expectedRouteNames),
                "Route {$route->getName()} does not match expected routes.");

        });
        // Assert that there are routes found for /customer
        $this->assertFalse($customerRoutes->isEmpty(), 'No routes found for /worker');

    });

});

describe('Create  Worker', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
    });

    it('should prevent non auth creating new Worker', function () {
        $response = postJson('/api/v1/worker/create');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin creating new Worker', function () {

        $response = actingAs($this->notAdmin)->postJson('/api/v1/worker/create');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return validation error when data is missed', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/worker/create', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
    it('should create new Worker with valid data', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/worker/create', [
            'first_name' => 'Rayan',
        ]);
        $response->assertOk();
        assertDatabaseHas('workers', ['first_name' => 'Rayan']);
    });
});
describe('Update  Worker', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->worker = \App\Models\Worker::factory()->create(['first_name' => 'Rayan']);
    });

    it('should prevent non auth updating a Worker', function () {
        $response = $this->putJson('/api/v1/worker/update/' . $this->worker->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin updating a Worker', function () {

        $response = actingAs($this->notAdmin)->putJson('/api/v1/worker/update/' . $this->worker->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return validation error when data is missed', function () {
        $response = actingAs($this->admin)->putJson('/api/v1/worker/update/' . $this->worker->id, []);
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas('workers', ['first_name' => 'Rayan']);
    });
    it('should update a Worker with valid data', function () {
        $response = actingAs($this->admin)->putJson('/api/v1/worker/update/' . $this->worker->id, [
            'first_name' => 'Komay',
        ]);
        $response->assertOk();
        assertDatabaseHas('workers', ['first_name' => 'Komay']);
    });
});
describe('Show  Workers list', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->worker = \App\Models\Worker::factory(10)->create(['first_name' => 'Rayan']);
    });
    it('should prevent non auth show list of Workers', function () {
        $response = $this->getJson('/api/v1/worker/list');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin show list of Workers', function () {
        $response = actingAs($this->notAdmin)->getJson('/api/v1/worker/list');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return right number of Workers in database', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/worker/list');
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseCount('workers', 10);
    });
    it('should return list of workers', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/worker/list');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['first_name' => 'Rayan']);
    });
});
describe('Show  Worker details', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->worker = \App\Models\Worker::factory()->create(['first_name' => 'Rayan']);
    });
    it('should prevent non auth show details of a Worker', function () {
        $response = $this->getJson('/api/v1/worker/show/'.$this->worker->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin show details of a Worker', function () {
        $response = actingAs($this->notAdmin)->getJson('/api/v1/worker/show/'.$this->worker->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return error if worker not existed', function () {
        $nonExistedWorker = rand(100,200);
        $response = actingAs($this->admin)->getJson('/api/v1/worker/show/'.$nonExistedWorker);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return details of a Worker', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/worker/show/'.$this->worker->id);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['first_name' => 'Rayan']);
    });
});
describe('Delete Worker', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->worker = \App\Models\Worker::factory()->create();
    });
    it('should prevent non auth delete a Worker', function () {
        $response = $this->deleteJson('/api/v1/worker/delete/'.$this->worker->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin delete a Worker', function () {
        $response = actingAs($this->notAdmin)->deleteJson('/api/v1/worker/delete/'.$this->worker->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return error if worker not existed', function () {
        $nonExistedWorker = rand(100,200);
        $response = actingAs($this->admin)->deleteJson('/api/v1/worker/delete/'.$nonExistedWorker);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should delete a worker', function () {
        $response = actingAs($this->admin)->deleteJson('/api/v1/worker/delete/'.$this->worker->id);
        $response->assertStatus(Response::HTTP_OK);
           assertDatabaseCount('workers', 0);
    });
});




