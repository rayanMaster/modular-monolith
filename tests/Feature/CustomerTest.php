<?php

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\{postJson,actingAs,putJson,getJson,assertDatabaseHas};


describe('Customer routes check', function () {
    it('should have all routes for /customer', function () {
        $this->artisan('optimize:clear');
        // Define the expected route names
        $expectedRouteNames = [
            'customer.create',
            'customer.update',
            'customer.list',
            'customer.show',
            'customer.delete',
        ];

        // Collect routes and filter based on the prefix
        $customerRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/v1/customer/');
        });

        // Assert that only the expected routes exist
        $customerRoutes->each(function ($route) use ($expectedRouteNames) {
            $this->assertTrue(in_array($route->getName(), $expectedRouteNames),
                "Route {$route->getName()} does not match expected routes.");

        });
        // Assert that there are routes found for /customer
        $this->assertFalse($customerRoutes->isEmpty(), 'No routes found for /customer');

    });

});
describe('Customer Create', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
    });

    it('should prevent non auth creating new Customer', function () {
        $response = postJson('/api/v1/customer/create');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin creating new Customer', function () {

        $response = actingAs($this->notAdmin)->postJson('/api/v1/customer/create');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return validation error when data is missed', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/customer/create', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
    it('should create new Customer with valid data', function () {
        $response = actingAs($this->admin)->postJson('/api/v1/customer/create', [
            'first_name' => 'Rayan',
            'last_name' => 'Azzam',
        ]);
        $response->assertOk();
    });
});
describe('Customer Update', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->customer = Customer::factory()->create(['first_name' => 'Rayan']);
    });

    it('should prevent non auth updating a Customer', function () {
        $response = putJson("/api/v1/customer/update/" . $this->customer->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin updating a Customer', function () {
        $response = actingAs($this->notAdmin)->putJson("/api/v1/customer/update/" . $this->customer->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should not return validation error when data is missed', function () {
        $response = actingAs($this->admin)->putJson("/api/v1/customer/update/" . $this->customer->id, []);
        $response->assertStatus(Response::HTTP_OK);
    });
    it('should not touch a field if not updated', function () {
        $response = actingAs($this->admin)->putJson("/api/v1/customer/update/" . $this->customer->id, []);
        assertDatabaseHas('customers', ['first_name' => 'Rayan']);
        $response->assertStatus(Response::HTTP_OK);
    });
    it('should create new Customer with valid data', function () {
        $response = actingAs($this->admin)->putJson("/api/v1/customer/update/" . $this->customer->id, [
            'first_name' => 'John',
        ]);
        assertDatabaseHas('customers', ['first_name' => 'John']);
        $response->assertOk();
    });
});
