<?php

use App\Enums\OrderPriorityEnum;
use App\Enums\OrderStatusEnum;
use App\Models\DailyAttendance;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\WorkSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;


describe('Order Create', function () {
    beforeEach(function () {
        $this->artisan('db:seed');
        $this->admin = User::factory()->admin()->create();
        $this->siteManager = User::factory()->siteManager()->create();
        $this->worker = User::factory()->worker()->create();
        $this->workSite1 = WorkSite::factory()->create();
        $this->workSite2 = WorkSite::factory()->create();
        $this->item1 = Item::factory()->create();
        $this->item2 = Item::factory()->create();



    });
    test('As an admin , I can create an order for a worksite or general one', function () {
        $response = actingAs($this->admin)->postJson('api/v1/order/create', [
            'work_site_id' => $this->workSite1->id,
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
            'created_by' => $this->admin->id,
            'work_site_id' => $this->workSite1->id,
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
    test('As a worksite manager, I cant create an order for a worksite,if i am not in this worksite at this time', function () {
        DailyAttendance::factory()->create([
            'work_site_id' => $this->workSite1->id,
            'employee_id' => $this->siteManager->id,
            'date' => Carbon::now()->addDays(-1),
        ]);
        actingAs($this->siteManager)->postJson('api/v1/order/create', [
            'work_site_id' => $this->workSite1->id,
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
            'date' => Carbon::now()->toDateString(),
        ])->assertStatus(Response::HTTP_FORBIDDEN);
    });
    test('As a worksite manager, I can create an order for a worksite, if i am in this worksite at this time', function () {
        DailyAttendance::factory()->create([
            'work_site_id' => $this->workSite1->id,
            'employee_id' => $this->siteManager->id,
            'date' => Carbon::now()->toDateString(),
        ]);
        actingAs($this->siteManager)->postJson('api/v1/order/create', [
            'work_site_id' => $this->workSite1->id,
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
            'date' => Carbon::now()->toDateString(),
        ])->assertStatus(Response::HTTP_OK);
    });
    test('As a not worksite manager or admin, I cant create an order for a worksite or general one', function () {
        actingAs($this->worker)->postJson('api/v1/order/create')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    });
});
describe('Order Update', function () {
    beforeEach(function () {
        $this->artisan('db:seed');

        $this->admin = User::factory()->admin()->create();
        $this->siteManager = User::factory()->siteManager()->create();
        $this->worker = User::factory()->worker()->create();
        $this->workSite = WorkSite::factory()->create();
        $this->item1 = Item::factory()->create();
        $this->item2 = Item::factory()->create();
    });
    test('As a worksite manager, I can update an order items, while its pending', function () {
        $order = Order::factory()->create([
            'work_site_id' => $this->workSite->id,
            'status' => OrderStatusEnum::PENDING->value,
            'created_by' => $this->siteManager->id,
        ]);
        DailyAttendance::factory()->create([
            'work_site_id' => $this->workSite->id,
            'employee_id' => $this->siteManager->id,
            'date' => Carbon::now()->toDateString(),
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $this->item1->id,
            'quantity' => 10,
        ]);
        $response = actingAs($this->siteManager)->putJson('api/v1/order/update/' . $order->id, [
            'items' => [
                [

                    'item_id' => $orderItem->item_id,
                    'quantity' => 20,
                ]
            ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(OrderItem::class, [
            'order_id' => $order->id,
            'item_id' => $orderItem->item_id,
            'quantity' => 20,
        ]);
    });
    test('As a worksite manager, I cant update an order items, while its processed', function () {
        $order = Order::factory()->create([
            'status' => OrderStatusEnum::APPROVED->value,
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $this->item1->id,
            'quantity' => 10,
        ]);
        $response = actingAs($this->siteManager)->putJson('api/v1/order/update/' . $order->id, [
            'items' => [
                [

                    'item_id' => $orderItem->item_id,
                    'quantity' => 20,
                ]
            ]
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonFragment([
                'message' => 'You cannot update an order not in pending approval',
            ]);
        assertDatabaseHas(OrderItem::class, [
            'order_id' => $order->id,
            'item_id' => $orderItem->item_id,
            'quantity' => 10,
        ]);

    });
    test('As an admin I can update an order items at any status', function () {
        $order = Order::factory()->create([
            'status' => OrderStatusEnum::APPROVED->value,
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $this->item1->id,
            'quantity' => 10,
        ]);
        $response = actingAs($this->admin)->putJson('api/v1/order/update/' . $order->id, [
            'items' => [
                [

                    'item_id' => $orderItem->item_id,
                    'quantity' => 20,
                ]
            ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(OrderItem::class, [
            'order_id' => $order->id,
            'item_id' => $orderItem->item_id,
            'quantity' => 20,
        ]);

    });
});
describe('Order List', function () {
    beforeEach(function () {
        $this->artisan('db:seed');
        $this->admin = User::factory()->admin()->create();
        $this->siteManager1 = User::factory()->siteManager()->create();
        $this->siteManager2 = User::factory()->siteManager()->create();
        $this->worker = User::factory()->worker()->create();
        $this->workSite1 = WorkSite::factory()->create();
        $this->workSite2 = WorkSite::factory()->create();
        $this->item1 = Item::factory()->create();
        $this->item2 = Item::factory()->create();

        $this->employeeAttendance = DailyAttendance::factory()->create([
            'employee_id' => $this->siteManager1->id,
            'work_site_id' => $this->workSite1->id,
            'date' => Carbon::today()->toDateString(),

        ]);

    });
    test('As a worksite manager, I can see list of my orders', function () {

        $order1 = Order::factory()->create([
            'status' => OrderStatusEnum::PENDING->value,
            'priority' => OrderPriorityEnum::LOW->value,
            'work_site_id' => $this->workSite1->id,
            'created_by' => $this->siteManager1->id,
        ]);
        Order::factory()->create([
            'work_site_id' => $this->workSite2->id,
            'created_by' => $this->siteManager2->id,
        ]);
        $response = actingAs($this->siteManager1)->getJson('api/v1/order/list');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', [
                [
                    'id' => $order1->id,
                    'workSite' => $this->workSite1->title,
                    'total_amount' => number_format($order1->total_amount, 2, '.', ''),
                    'status' => OrderStatusEnum::from($order1->status)->name,
                    'priority' => OrderPriorityEnum::from($order1->priority)->name,
                    'created_by' => $this->siteManager1->fullName,
                ]
            ]);
    });
    test('As an admin, I can see list of all orders in the system', function () {
        $order1 = Order::factory()->create([
            'status' => OrderStatusEnum::PENDING->value,
            'priority' => OrderPriorityEnum::LOW->value,
            'work_site_id' => $this->workSite1->id,
            'created_by' => $this->siteManager1->id,
        ]);
        $order2 = Order::factory()->create([
            'status' => OrderStatusEnum::PENDING->value,
            'priority' => OrderPriorityEnum::LOW->value,
            'work_site_id' => $this->workSite2->id,
            'created_by' => $this->siteManager2->id,
        ]);
        $response = actingAs($this->admin)->getJson('api/v1/order/list');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', [
                [
                    'id' => $order1->id,
                    'workSite' => $this->workSite1->title,
                    'total_amount' => number_format($order1->total_amount, 2, '.', ''),
                    'status' => OrderStatusEnum::from($order1->status)->name,
                    'priority' => OrderPriorityEnum::from($order1->priority)->name,
                    'created_by' => $this->siteManager1->fullName,
                ],
                [
                    'id' => $order2->id,
                    'workSite' => $this->workSite2->title,
                    'total_amount' => number_format($order2->total_amount, 2, '.', ''),
                    'status' => OrderStatusEnum::from($order2->status)->name,
                    'priority' => OrderPriorityEnum::from($order2->priority)->name,
                    'created_by' => $this->siteManager2->fullName,
                ]
            ]);
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
