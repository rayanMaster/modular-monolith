<?php

use App\Models\Address;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\Worksite;
use App\Models\WorksiteItem;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;

describe('Warehouse Movements', function () {
    beforeEach(function () {

        $this->worksite = Worksite::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();
        $this->address = Address::factory()->create();

        $workSiteResourceCategory = ItemCategory::factory()->create();

        $this->item1 = Item::factory()->create([
            'item_category_id' => $workSiteResourceCategory->id,
        ]);
        $this->item2 = Item::factory()->create([
            'item_category_id' => $workSiteResourceCategory->id,
        ]);
        $this->warehouse = Warehouse::factory()->create();

    });

    it('should return error while moving a quantity bigger than already exists in the warehouse', function () {

        // Arrange
        WarehouseItem::factory()->create([
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item1->id,
            'quantity' => 5,
        ]);

        // Act
        $response = actingAs($this->admin)
            ->postJson('/api/v1/worksite/'.$this->worksite->id.'/item/add', [
                'warehouse_id' => $this->warehouse->id,
                'items' => [
                    [
                        'item_id' => $this->item1->id,
                        'quantity' => 6,
                        'price' => 20,
                    ],
                ],
            ]);
        // Assert
        expect($response->status())->toBe(Response::HTTP_BAD_REQUEST);
        $response->assertJsonFragment([
            'message' => 'Insufficient quantity in warehouse.',
        ]);

    });
    test('item quantity balance while moving items between warehouses', function () {
        // Arrange
        WarehouseItem::factory()->create([
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item1->id,
            'quantity' => 50,
        ]);

        // Act
        actingAs($this->admin)
            ->postJson('/api/v1/worksite/'.$this->worksite->id.'/item/add', [
                'warehouse_id' => $this->warehouse->id,
                'items' => [
                    [
                        'item_id' => $this->item1->id,
                        'quantity' => 20,
                        'price' => 20,
                    ],
                ],
            ]);
        // Assert
        $itemsInWarehouse = WarehouseItem::query()->where('item_id', $this->item1->id)
            ->where('warehouse_id', $this->warehouse->id)->value('quantity');
        $itemMovedToWorksite = WorksiteItem::query()->where('worksite_id', $this->worksite->id)
            ->where('item_id', $this->item1->id)->value('quantity');

        expect((int) $itemsInWarehouse)->toBe(50 - 20)
            ->and((int) $itemMovedToWorksite)->toBe(20);
    });

    //    it('should be adding items between supplier and warehouse', function() {});
    //    it('should be dropping items between warehouse and worksite', function() {});

});

//    it('should have the option to attach a wareHouse with a worksite or make it as main workHouse
//    without a worksite', function () {
//    });
//    it('should assign store keeper to a wareHouse, and at least one for each', function () {
//    });
//    it('should be able to move items between warehouses and make data consist between them', function () {
//    });
//    it('should be able to add new items to a warehouse from any external supplier', function () {
//    });
//    it('should track all movements between warehouses', function () {
//    });
//    it('should move items to a worksite from its own warehouse only', function () {
//    });
//    it('should track all items movements between worksite and its warehouse', function () {
//    });
//
//    test('if items entered to a worksite are the same the drop off its wareHouse', function () {
//    });
//});
