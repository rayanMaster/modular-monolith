<?php

use App\Models\Address;
use App\Models\User;
use App\Models\WareHouse;
use App\Models\WorkSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{assertSoftDeleted, getJson, postJson, putJson, actingAs, assertDatabaseCount};
use \Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);
describe('Warehouse routes check', function () {
    it('should have all routes for /warehouse', function () {
        $this->artisan('optimize:clear');
        // Define the expected route names
        $expectedRouteNames = [
            'warehouse.create',
            'warehouse.update',
            'warehouse.list',
            'warehouse.show',
            'warehouse.delete',
        ];

        // Collect routes and filter based on the prefix
        $warehouseRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/v1/warehouse/');
        });

        // Assert that only the expected routes exist
        $warehouseRoutes->each(function ($route) use ($expectedRouteNames) {
            $this->assertTrue(in_array($route->getName(), $expectedRouteNames),
                "Route {$route->getName()} does not match expected routes.");

        });
        // Assert that there are routes found for /warehouse
        $this->assertFalse($warehouseRoutes->isEmpty(), 'No routes found for /warehouse');

    });

})->only();
describe('Warehouse Create Test', function () {

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->artisan('db:seed');

        $this->workSite = WorkSite::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();
        $this->address = Address::factory()->create();

    });

    it('should create main warehouse in a specific location', function () {
        actingAs($this->admin)->postJson('/api/warehouse/store', [
            'name' => 'Main Warehouse',
            'address_id' => $this->address->id,
        ])->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', [
                'name' => 'Main Warehouse',
                'address' => $this->address->title,
            ]);
    });
})->only();
describe('Warehouse Update Test', function () {
    beforeEach(function () {
        $this->artisan('storage:link');
        $this->artisan('db:seed');

        $this->workSite = WorkSite::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();
        $this->address = Address::factory()->create();

    });

    it('should update a warehouse', function () {
        $wareHouse = WareHouse::factory()->create([
            'name' => 'Main Warehouse',
            'address_id' => $this->address->id,
        ]);
        $newAddress = Address::factory()->create();
        actingAs($this->admin)->putJson('/api/warehouse/' . $wareHouse->id . '/update', [
            'name' => 'Main Warehouse Updated',
            'address_id' => $this->address->id,
        ])->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', [
                'name' => 'Main Warehouse Updated',
                'address' => $newAddress->title,
            ]);
    });
})->only();
describe('Warehouse List Test', function () {

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->artisan('db:seed');

        $this->workSite = WorkSite::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();
        $this->address = Address::factory()->create();
        $this->otherAddress = Address::factory()->create();
        $this->firstWarehouse = WareHouse::factory()->create([
            'title' => 'Main Warehouse',
            'address_id' => $this->address->id,
        ]);
        $this->secondWarehouse = WareHouse::factory()->create([
            'title' => 'Second Warehouse',
            'address_id' => $this->otherAddress->id,
        ]);

    });
    it('should get a list of warehouses', function () {
        actingAs($this->admin)->getJson('/api/warehouse/list')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'name' => 'Main Warehouse',
                'address' => $this->address->title,
            ])
            ->assertJsonFragment([
                'name' => 'Second Warehouse',
                'address' => $this->otherAddress->title,
            ]);
        assertDatabaseCount(WareHouse::class, 2);
    });
})->only();
describe('Warehouse Details Test', function () {

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->artisan('db:seed');

        $this->workSite = WorkSite::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();
        $this->address = Address::factory()->create();

    });
    it('should return not found error if warehouse nof found', function () {
        $unExistedWarehouseId = rand(22, 33);
        actingAs($this->admin)->getJson('/api/warehouse/show/' . $unExistedWarehouseId)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should get a warehouse details', function () {
        actingAs($this->admin)->getJson('/api/warehouse/details/' . $this->workSite->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'name' => 'Main Warehouse',
                'address' => $this->address->title,
            ]);
    });
})->only();
describe('Warehouse Delete Test', function () {

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->artisan('db:seed');

        $this->workSite = WorkSite::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();
        $this->address = Address::factory()->create();
        $this->wareHouse = WareHouse::factory()->create();

    });
    it('should return not found error if warehouse nof found', function () {
        $unExistedWarehouseId = rand(22, 33);
        actingAs($this->admin)->deleteJson('/api/warehouse/delete/' . $unExistedWarehouseId)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should delete a warehouse', function () {
        actingAs($this->admin)->deleteJson('/api/warehouse/delete/' . $this->wareHouse->id)
            ->assertStatus(Response::HTTP_OK);
        assertSoftDeleted('warehouse', ['id' => $this->wareHouse->id]);
    });
})->only();
