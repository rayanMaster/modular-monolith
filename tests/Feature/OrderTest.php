<?php

use App\Enums\OrderPriorityEnum;
use App\Models\Item;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\WorkSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);
beforeEach(function () {

    $this->artisan('db:seed');
    $this->siteManager = User::factory()->siteManager()->create();
    $this->worker = User::factory()->worker()->create();
    $this->workSite = WorkSite::factory()->create();
    $this->item1 = Item::factory()->create();
    $this->item2 = Item::factory()->create();
});

describe('Order Create', function () {
    test('As a worksite manager/admin, I can create an order for a worksite or general one', function () {
        $response = actingAs($this->siteManager)->postJson('api/v1/order/create', [
            'work_site_id' => $this->workSite->id,
            'items' => [
                [
                    'item_id' => $this->item1->id,
                    'quantity' => 10,
                ],
                [
                    'item_id' => $this->item2->id,
                    'quantity' => 15,
                ],
            ],
            'priority' => OrderPriorityEnum::NORMAL->value,
        ])->assertStatus(Response::HTTP_OK);
        $orderId = json_decode($response->content())->data->id;
        assertDatabaseHas('orders', [
            'id' => $orderId,
            'created_by' => $this->siteManager->id,
            'work_site_id' => $this->workSite->id,
            'priority' => OrderPriorityEnum::NORMAL->value,
        ]);
        assertDatabaseHas(OrderItem::class, [
            'order_id' => $orderId,
            'item_id' => $this->item1->id,
            'quantity' => 10,
        ]);
        assertDatabaseHas(OrderItem::class, [
            'order_id' => $orderId,
            'item_id' => $this->item2->id,
            'quantity' => 15,
        ]);
    });
    test('As a not worksite manager or admin, I cant create an order for a worksite or general one', function () {
        actingAs($this->worker)->postJson('api/v1/order/create')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    });
});
describe('Order Update', function () {
    test('As a worksite manager, I can update an order items, while its pending', function () {
    });
    test('As a worksite manager, I cant update an order items, while its processed', function () {
    });
    test('As an admin i can update an order items at any status', function () {
    });
});

describe('Order List', function () {
    test('As a worksite manager, I can see list of my orders', function () {
    });
    test('As an admin, I can see list of all orders in the system', function () {
    });
});

describe('Order Detail', function () {
    test('As a worksite manager, I can see details of my order', function () {
    });
    test('As an admin, I can see details of any order in the system', function () {
    });
});

describe('Order Status', function () {
    test('As a worksite manager, I can see the status of the order', function () {
    });
    test('As a worksite manager, I can update the status of the order to Delivered only', function () {
    });
    test('As a store keeper, I can update the status of the order to Received only', function () {
    });
    test('As an admin, I can update the status of the order to any status', function () {
    });
    test('As an admin, I can see the status of the order in the system', function () {
    });
    test('As an admin, I should receive notifications with all order statuses in the system', function () {
    });
    test('As an worksite manager, I should received notification with my orders status', function () {
    });
});
