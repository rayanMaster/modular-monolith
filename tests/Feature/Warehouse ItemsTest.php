<?php

use App\Models\Address;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\WorkSite;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{assertSoftDeleted, getJson, postJson, putJson, actingAs, assertDatabaseCount,assertDatabaseHas};
use \Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Warehouse Items', function () {
    beforeEach(function () {
        $this->artisan('storage:link');
        $this->artisan('db:seed');

        $this->workSite = WorkSite::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();
        $this->address = Address::factory()->create();
        $this->warehouse = Warehouse::factory()->create();

        $this->otherWarehouse = Warehouse::factory()->create();

        $this->item1 = Item::factory()->create();
        $this->item2 = Item::factory()->create();

        $this->supplier = Supplier::factory()->create();

        WarehouseItem::factory()->create([
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item1->id,
            'quantity' => 5,
        ]);

        WarehouseItem::factory()->create([
            'warehouse_id' => $this->otherWarehouse->id,
            'item_id' => $this->item1->id,
            'quantity' => 10,
        ]);

    });
    it('should return error if adding same item twice to a warehouse', function () {
        actingAs($this->admin)->postJson('/api/v1/warehouse/' . $this->warehouse->id . '/items/add', [
            'items' => [
                [
                    'item_id' => $this->item1->id,
                    'quantity' => 1,
                    'price' => 20
                ],
                [
                    'item_id' => $this->item1->id,
                    'quantity' => 10,
                    'price' => 30
                ]
            ],
            'supplier_id' => $this->supplier->id,
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
        ])
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJsonFragment([
                'message' => 'Item already exists in this warehouse'
            ]);
        assertDatabaseCount(WarehouseItem::class, 0);
    });
    test('adding items to warehouse from a supplier', function () {
        actingAs($this->admin)->postJson('/api/v1/warehouse/' . $this->warehouse->id . '/items/add', [
            'items' => [
                [
                    'item_id' => $this->item1->id,
                    'quantity' => 1,
                    'price' => 20
                ],
                [
                    'item_id' => $this->item2->id,
                    'quantity' => 10,
                    'price' => 30
                ]
            ],
            'supplier_id' => $this->supplier->id,
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
        ])->assertStatus(Response::HTTP_OK);
        assertDatabaseCount(WarehouseItem::class, 2);
    });
    it('should prevent adding negative quantity for an item', function() {
        actingAs($this->admin)->postJson('/api/v1/warehouse/' . $this->warehouse->id . '/items/add', [
            'items' => [
                [
                    'item_id' => $this->item1->id,
                    'quantity' => -1,
                    'price' => 20
                ]
            ],
            'supplier_id' => $this->supplier->id,
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
    test('moving item from one warehouse to other', function() {
        actingAs($this->admin)->postJson('/api/v1/warehouse/' . $this->warehouse->id . '/items/move', [
            'items' => [
                [
                    'item_id' => $this->item1->id,
                    'quantity' => 2,
                    'to_warehouse_id' => $this->otherWarehouse->id,
                ],
            ],

        ])->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(WarehouseItem::class, [
            'item_id' => $this->item1->id,
            'warehouse_id' => $this->otherWarehouse->id,
            'quantity' => 12,
        ]);
        assertDatabaseHas(WarehouseItem::class, [
            'item_id' => $this->item1->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 3,
        ]);
    })->only();
//    test('updating quantity and price for multiple items', function() {});
//    test('getting list of low stock items', function() {});
//    test('getting list of out off stock items', function() {});

});



//    it('should have the option to attach a wareHouse with a workSite or make it as main workHouse
//    without a workSite', function () {
//    });
//    it('should assign store keeper to a wareHouse, and at least one for each', function () {
//    });
//    it('should be able to move items between warehouses and make data consist between them', function () {
//    });
//    it('should be able to add new items to a warehouse from any external supplier', function () {
//    });
//    it('should track all movements between warehouses', function () {
//    });
//    it('should move items to a workSite from its own warehouse only', function () {
//    });
//    it('should track all items movements between worksite and its warehouse', function () {
//    });
//
//    test('if items entered to a worksite are the same the drop off its wareHouse', function () {
//    });
//});

