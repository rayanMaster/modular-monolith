<?php

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentTypesEnum;
use App\Enums\WorksiteCompletionStatusEnum;
use App\Enums\WorksiteReceptionStatusEnum;
use App\Helpers\Data\AddressFormat;
use App\Http\Integrations\Accounting\Connector\AccountingConnector;
use App\Models\Address;
use App\Models\City;
use App\Models\Contractor;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Media;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\Worksite;
use App\Models\WorksiteCategory;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

describe('Worksite entity fields check', function () {
    beforeEach(function () {
        $this->requiredFields = [
            'id',
            'uuid',
            'title',
            'description',
            'manager_id',
        ];
        $this->nullableFields = [
            'customer_id',
            'category_id',
            'parent_worksite_id',
            'contractor_id',
            'starting_budget',
            'cost',
            'address_id',
            'workers_count',
            'receipt_date',
            'starting_date',
            'deliver_date',
            'reception_status',
            'completion_status',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    });
    it('should have not nullable fields', function () {
        // Get table columns
        $tableColumns = collect(Schema::getColumns('worksites'));

        $requiredColumns = $tableColumns->filter(function ($item) {
            return !$item['nullable'];
        })->map(function ($subItem) {
            return $subItem['name'];
        })->toArray();

        $nullableColumns = $tableColumns->filter(function ($item) {
            return $item['nullable'];
        })->map(function ($subItem) {
            return $subItem['name'];
        })->toArray();

        $this->assertEqualsCanonicalizing($requiredColumns, $this->requiredFields);
        $this->assertEqualsCanonicalizing($nullableColumns, $this->nullableFields);

    })->todo('note working for now');

});
describe('Worksite routes check', function () {
    it('should have all routes for /worksite', function () {
        $this->artisan('optimize:clear');
        // Define the expected route names
        $expectedRouteNames = [
            'worksite.create',
            'worksite.update',
            'worksite.list',
            'worksite.show',
            'worksite.delete',
            'worksite.close',

            'worksite.resource.list',
            'worksite.resource.create',
            'worksite.resource.show',
            'worksite.resource.delete',
            'worksite.resource.update',

            'worksite.payment.create',
            'worksite.payment.list',
            'worksite.payment.show',

            'worksite.category.create',
            'worksite.category.update',
            'worksite.category.list',
            'worksite.category.show',
            'worksite.category.delete',

            'worksite.employee.assign',
            'worksite.contractor.assign',
            'worksite.contractor.unAssign',

            'worksite.customer.assign',
            'worksite.customer.update',
            'worksite.customer.show',
            'worksite.customer.delete',

            'worksite.item.add',
            'worksite.item.list',
        ];

        // Collect routes and filter based on the prefix
        $worksiteRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/v1/worksite/');
        });

        // Assert that only the expected routes exist
        $worksiteRoutes->each(function ($route) use ($expectedRouteNames) {
            $this->assertTrue(in_array($route->getName(), $expectedRouteNames),
                "Route {$route->getName()} does not match expected routes.");

        });
        // Assert that there are routes found for /worksite
        $this->assertFalse($worksiteRoutes->isEmpty(), 'No routes found for /worksite');

    });

});
describe('Create Worksite', function () {

    beforeEach(function () {
        MockClient::global([
            AccountingConnector::class => MockResponse::make([
                'response' => [
                    'results' => [
                    ],
                ],
            ], 200),
        ]);
    });

    test('As an administrator, I want to create a main worksite', function () {

        $wsCategory = WorksiteCategory::factory()->create();

        $customer = Customer::factory()->create();
        $city = City::factory()->create();
        $contractor = Contractor::factory()->create();

        $this->manager = User::factory()->siteManager()->create();
        expect($this->manager->hasRole('site_manager'))->toBe(true);

        $workSiteResourceCategory = ItemCategory::factory()->create();

        $workSiteResource1 = Item::factory()->create([
            'item_category_id' => $workSiteResourceCategory->id,
        ]);
        $workSiteResource2 = Item::factory()->create([
            'item_category_id' => $workSiteResourceCategory->id,
        ]);

        $admin = User::factory()->admin()->create();
        expect($admin->hasRole('admin'))->toBe(true);

        Storage::fake();

        $files = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
            'manager_id' => $this->manager->id,
            'customer_id' => $customer->id,
            'category_id' => $wsCategory->id, // construction
            'contractor_id' => $contractor->id,
            'parent_worksite_id' => null, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'city_id' => $city->id,
            'address' => 'town center',
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'reception_status' => WorksiteReceptionStatusEnum::SCRATCH->value,
            'completion_status' => WorksiteCompletionStatusEnum::PENDING->value,
            'items' => [
                ['id' => $workSiteResource1?->id, 'quantity' => 23, 'price' => 34],
                ['id' => $workSiteResource2?->id, 'quantity' => 30, 'price' => 30],
            ],
            'payments' => [
                ['payment_amount' => 3000,
                    'payment_date' => '2024-04-12 10:34'],
            ],
            'images' => [$files],
        ]);

        $response->assertOk();

        // Assert the file was stored...
        $path = 'worksite';
        $name = 'test' . '_' . now()->format('YmdH');
        $fullPath = public_path('storage/' . $path) . '/' . $name . '.webp';

        $this->assertFileExists($fullPath);

        $workSite = Worksite::query()->latest('id')->with(['lastPayment'])->first();

        assertDatabaseHas(Media::class, [
            'model_id' => $workSite->id,
            'model_type' => 'worksite',
            'name' => $name,
        ]);

        assertDatabaseHas(Worksite::class, [
            'reception_status' => WorksiteReceptionStatusEnum::SCRATCH->value,
            'completion_status' => WorksiteCompletionStatusEnum::PENDING->value,
            'contractor_id' => $contractor->id,
            'manager_id' => $this->manager->id,
        ]);
        expect($workSite->parentWorksite)->toBeNull('that indicates that worksite is main')
            ->and($workSite?->title)->toBe('worksite A')
            ->and($workSite?->description)->toBe('this worksite is for freeTown')
            ->and($workSite?->items[0]->pivot->getAttributes())->toBe(
                ['worksite_id' => $workSite->id,
                    'item_id' => $workSiteResource1->id,
                    'quantity' => 23,
                    'price' => '34.00'])
            ->and($workSite?->items[1]->pivot->getAttributes())->toBe(
                ['worksite_id' => $workSite->id,
                    'item_id' => $workSiteResource2->id,
                    'quantity' => 30,
                    'price' => '30.00'])
            ->and($workSite->lastPayment->payable_id)->toBe($workSite->id)
            ->and($workSite->lastPayment->payable_type)->toBe('worksite')
            ->and($workSite->lastPayment->amount)->toBe('3000.00')
            ->and($workSite->lastPayment->payment_date)->toBe('2024-04-12 10:34:00')
            ->and($workSite->lastPayment->payment_type)->toBe(PaymentTypesEnum::CASH->value)
            ->and($workSite->address->city->id)->toBe($city->id)
            ->and($workSite->address->title)->toBe('town center');
    });
    test('As a non-authenticated, I cant create a main worksite', function () {

        $response = $this->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

    });
    test('As an administrator, should return validation error when no data', function () {

        $mainWorksite = Worksite::factory()->create();

        $admin = User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson('/api/v1/worksite/create', [
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'success' => false,
                'error_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'The title field is required.',
                'data' => [
                    'title' => ['The title field is required.'],
                    'description' => ['The description field is required.'],
                    'manager_id' => ['The manager id field is required.'],
                ],
            ]);

    });
    test('As not admin, I cant create a main worksite', function () {

        $siteManager = User::factory()->siteManager()->create();
        expect($siteManager->hasRole('site_manager'))->toBe(true);

        $response = $this->actingAs($siteManager)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

    });
    test('As an administrator, I want to create a sub worksites', function () {

        $mainWorksite = Worksite::factory()->create();
        $address = Address::factory()->create();

        $admin = User::factory()->admin()->create();

        expect($admin->hasRole('admin'))->toBe(true);

        $response = actingAs($admin)->postJson('/api/v1/worksite/create', [
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown',
            'customer_id' => $mainWorksite->customer?->id,
            'manager_id' => $mainWorksite->manager?->id,
            'category_id' => $mainWorksite->category?->id, // construction
            'parent_worksite_id' => $mainWorksite->id, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address_id' => $address->id,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'reception_status' => 1,

        ]);
        $response->assertOk();

        $workSite = Worksite::query()->latest('id')->first();

        expect($workSite?->title)->toBe('worksite AB')
            ->and($workSite?->description)->toBe('this worksite is for freeTown')
            ->and($workSite->parentWorksite->id)->toBe($mainWorksite->id);

    });

});
describe('Update Worksite', function () {

    beforeEach(function () {

        $this->city = City::factory()->create();
        $this->address = Address::factory()->create([
            'city_id' => $this->city->id,
            'title' => 'Free town',
        ]);
        $this->admin = User::factory()->admin()->create();
        $this->manager = User::factory()->siteManager()->create();
        $this->worksite = Worksite::factory()->create([
            'address_id' => $this->address->id,
        ]);

    });

    test('As a non-authenticated, I cant update a main worksite', function () {
        $response = putJson('/api/v1/worksite/update/' . $this->worksite->id, []);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

    });
    test('As not admin, I cant update a main worksite', function () {

        $siteManager = User::factory()->siteManager()->create();
        expect($siteManager->hasRole('site_manager'))->toBe(true);

        $response = actingAs($siteManager)->putJson('/api/v1/worksite/update/' . $this->worksite->id, []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

    });
    test('As an administrator, I want to update worksite main info', function () {

        assertDatabaseCount(Worksite::class, 1);
        $response = actingAs($this->admin)->putJson('/api/v1/worksite/update/' . $this->worksite->id, [
            'address' => 'Free town',
            'city_id' => $this->city->id,
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown new',
            'manager_id' => $this->manager->id,
        ]);
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(Worksite::class, [
            'address_id' => $this->address->id,
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown new',
            'manager_id' => $this->manager->id,
        ]);

    });
    test('As an administrator, I want to update worksite address', function () {

        assertDatabaseCount(Worksite::class, 1);
        $response = actingAs($this->admin)->putJson('/api/v1/worksite/update/' . $this->worksite->id, [
            'address' => 'Free town new',
            'city_id' => $this->city->id,
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown new',
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $newAddressId = Address::query()->latest('id')->value('id');
        assertDatabaseHas(Worksite::class, [
            'address_id' => $newAddressId,
            'title' => 'worksite AB',
            'description' => 'this worksite is for freeTown new',
        ]);

    });
    test('As an administrator, I want to update worksite contractor before worksite finished', function () {
    });
});
describe('List WorkSites', function () {

    beforeEach(function () {

        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->siteManager()->create();
        expect($this->notAdmin->hasRole('site_manager'))->toBe(true);

    });
    test('As a non-authenticated, I cant show list of worksites', function () {
        $response = getJson('/api/v1/worksite/list');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    test('As not admin, I cant show list of worksites', function () {
        $response = actingAs($this->notAdmin)->getJson('/api/v1/worksite/list');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    test('As an admin, I can show list of worksites without customer and category while creating', function () {
        $address = Address::factory()->create();
        $manager = User::factory()->siteManager()->create();
        $data = [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
            'manager_id' => $manager->id,
            'customer_id' => null,
            'category_id' => null, // construction
            'parent_worksite_id' => null, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address_id' => $address->id,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'reception_status' => WorkSiteReceptionStatusEnum::SCRATCH->value,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $workSite = Worksite::factory()->create($data);
        assertDatabaseCount(Worksite::class, 1);
        actingAs($this->admin)->getJson('/api/v1/worksite/list')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'success' => true,
                'error_code' => null,
            ])
            ->assertJsonFragment([
                'id' => $workSite->id,
                'title' => $workSite->title,
                'description' => $workSite->description,
                'manager' => $workSite->manager?->fullName,
                'customer' => $workSite->customer?->fullName,
                'category' => $workSite->category?->name,
                'sub_worksites' => [],
                'starting_budget' => number_format($workSite->starting_budget, 2),
                'cost' => number_format($workSite->cost, 2),
                'address' => [
                    'id' => $address->id,
                    'title' => $address->title,
                    'city' => $address->city?->name,
                    'street' => $address->street,
                    'state' => $address->state,
                    'zipCode' => $address->zipcode,
                ],
                'workers_count' => $workSite->workers_count,
                'receipt_date' => $workSite->receipt_date,
                'starting_date' => $workSite->starting_date,
                'deliver_date' => $workSite->deliver_date,
                'reception_status' => WorkSiteReceptionStatusEnum::from($workSite->reception_status)->name,
                'completion_status' => 'PENDING',
                'created_at' => Carbon::parse($workSite->created_at)->toDateTimeString(),
                'updated_at' => Carbon::parse($workSite->updated_at)->toDateTimeString(),
                'payments' => $workSite->payments,
            ]);

    });
    test('As an admin, I can show list of worksites', function () {
        $wsCategory = WorksiteCategory::factory()->create();
        $customer = Customer::factory()->create();
        $manager = User::factory()->siteManager()->create();
        $address = Address::factory()->create();
        $data = [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
            'manager_id' => $manager->id,
            'customer_id' => $customer?->id,
            'category_id' => $wsCategory?->id, // construction
            'parent_worksite_id' => null, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address_id' => $address->id,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'reception_status' => WorkSiteReceptionStatusEnum::SCRATCH->value,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $workSite = Worksite::factory()->create($data);
        assertDatabaseCount(Worksite::class, 1);

        // create orders for testing
        Order::factory()->create([
            'worksite_id' => $workSite->id,
            'status' => OrderStatusEnum::PENDING->value,
        ]);
        Order::factory()->create([
            'worksite_id' => $workSite->id,
            'status' => OrderStatusEnum::APPROVED->value,
        ]);

        actingAs($this->admin)->getJson('/api/v1/worksite/list')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'id' => $workSite->id,
                'title' => $workSite->title,
                'description' => $workSite->description,
                'manager' => $workSite->manager->fullName,
                'customer' => $workSite->customer->fullName,
                'category' => $workSite->category->name,
                'sub_worksites' => $workSite->subWorkSites,
                'starting_budget' => number_format($workSite->starting_budget, 2),
                'cost' => number_format($workSite->cost, 2),
                'address' => [
                    'id' => $address->id,
                    'city' => $address->city?->name,
                    'title' => $address->title,
                    'street' => $address->street,
                    'state' => $address->state,
                    'zipCode' => $address->zipcode,
                ],
                'pending_orders_count' => 1,
                'workers_count' => $workSite->workers_count,
                'receipt_date' => $workSite->receipt_date,
                'starting_date' => $workSite->starting_date,
                'deliver_date' => $workSite->deliver_date,
                'reception_status' => WorkSiteReceptionStatusEnum::from($workSite->reception_status)->name,
                'created_at' => Carbon::parse($workSite->created_at)->toDateTimeString(),
                'updated_at' => Carbon::parse($workSite->updated_at)->toDateTimeString(),
                'payments' => $workSite->payments,
            ]);

    });
    test('As an admin, I can show list of worksites in desc order', function () {

        Worksite::factory()->create([
            'created_at' => Carbon::now()->subDay(),
        ]);
        Worksite::factory()->create();
        assertDatabaseCount(Worksite::class, 2);

        $response = actingAs($this->admin)->getJson('/api/v1/worksite/list');
        $decodedJson = json_decode($response->getContent(), true);
        $response->assertStatus(Response::HTTP_OK);

        $firstCreatedAt = $decodedJson['data'][0]['created_at'];
        $lastCreatedAt = $decodedJson['data'][1]['created_at'];
        $this->assertTrue($firstCreatedAt > $lastCreatedAt);

    });

});
describe('Show WorkSites Details', function () {

    beforeEach(function () {

        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->siteManager()->create();
        expect($this->notAdmin->hasRole('site_manager'))->toBe(true);

        $this->worksite = Worksite::factory()->create();
        $this->wsCategory = WorksiteCategory::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->address = Address::factory()->create();
        $data = [
            'title' => 'worksite sub',
            'description' => 'this worksite is for freeTown sub',
            'customer_id' => $this->customer?->id,
            'category_id' => $this->wsCategory?->id, // construction
            'parent_worksite_id' => $this->worksite->id, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address_id' => $this->address->id,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'reception_status' => WorkSiteReceptionStatusEnum::SCRATCH->value,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $this->subWorkSite = Worksite::factory()->create($data);

        Order::factory()->create([
            'worksite_id' => $this->worksite->id,
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        // add payment to this worksite
        Payment::factory()->create([
            'payable_id' => $this->worksite->id,
            'payable_type' => 'worksite',
            'amount' => 20,
            'payment_date' => Carbon::now(),
            'payment_type' => PaymentTypesEnum::CASH->value,
        ]);
    });
    test('As a non-authenticated, I cant show details of a worksite', function () {
        $response = getJson('/api/v1/worksite/show/' . $this->worksite->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    test('As not admin, I cant show details of a worksite', function () {
        $response = actingAs($this->notAdmin)->getJson('/api/v1/worksite/show/' . $this->worksite->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found error if worksite not existed in database', function () {
        $unExistedWorkSiteId = rand(200, 333);
        $response = actingAs($this->admin)->getJson('/api/v1/worksite/show/' . $unExistedWorkSiteId);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    test('As an admin, I can show details of a worksite', function () {
        $response = actingAs($this->admin)->getJson('/api/v1/worksite/show/' . $this->worksite->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'id' => $this->worksite->id,
                'title' => $this->worksite->title,
                'description' => $this->worksite->description,
                'manager' => $this->worksite->manager?->fullName,
                'customer' => $this->worksite->customer?->fullName,
                'category' => $this->worksite->category?->name, // construction
                'starting_budget' => number_format($this->worksite->starting_budget, 2),
                'cost' => number_format($this->worksite->cost, 2),
                'address' => AddressFormat::format([
                    'title' => $this->address?->title,
                    'state' => $this->address?->state,
                    'city' => $this->address?->city?->name,
                    'street' => $this->address?->street,
                    'zipcode' => $this->address?->zipcode,
                ]),
                'pending_orders_count' => $this->worksite->pendingOrders?->count(),
                'total_payments_amount' => '20.00',
                'workers_count' => $this->worksite->workers_count,
                'receipt_date' => $this->worksite->receipt_date,
                'starting_date' => $this->worksite->starting_date,
                'deliver_date' => $this->worksite->deliver_date,
                'reception_status' => WorkSiteReceptionStatusEnum::from($this->worksite->reception_status)->name,
                'created_at' => Carbon::parse($this->worksite->created_at)->toDateTimeString(),
                'updated_at' => Carbon::parse($this->worksite->updated_at)->toDateTimeString(),
            ]);
    });
    test('As an admin, I can show details of a worksite with payments and items', function () {

        $workSiteResourceCategory = ItemCategory::factory()->create();

        $workSiteResource1 = Item::factory()->create([
            'item_category_id' => $workSiteResourceCategory->id,
        ]);
        $workSiteResource2 = Item::factory()->create([
            'item_category_id' => $workSiteResourceCategory->id,
        ]);
        $data = [
            'title' => 'worksite sub',
            'description' => 'this worksite is for freeTown sub',
            'customer_id' => $this->customer?->id,
            'category_id' => $this->wsCategory?->id, // construction
            'parent_worksite_id' => $this->worksite->id, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address_id' => $this->address->id,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'reception_status' => WorkSiteReceptionStatusEnum::SCRATCH->value,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $workSite = Worksite::factory()->create($data);

        // add payment to this worksite
        Payment::factory()->create([
            'payable_id' => $workSite->id,
            'payable_type' => 'worksite',
            'amount' => 20,
            'payment_date' => Carbon::now(),
            'payment_type' => PaymentTypesEnum::CASH->value,
        ]);

        //add media to this worksite
        $media = Media::factory()->create([
            'model_type' => 'worksite',
            'model_id' => $workSite->id,
            'name' => 'test',
            'file_name' => 'worksite/test.webp',
        ]);

        $workSite->items()->syncWithoutDetaching([
            $workSiteResource1->id => [
                'quantity' => 10,
                'price' => '34.00',
            ],
        ]);

        //add the item to the warehouse then pick some of them to the worksite
        $this->warehouse = Warehouse::factory()->create();
        WarehouseItem::factory()->create([
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $workSiteResource1->id,
            'quantity' => 23,
            'price' => '34.00',
        ]);

        $response = actingAs($this->admin)->getJson('/api/v1/worksite/show/' . $workSite->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'id' => $workSite->id,
                'title' => $workSite->title,
                'description' => $workSite->description,
                'customer' => $workSite->customer?->fullName,
                'category' => $workSite->category?->name, // construction
                'starting_budget' => number_format($workSite->starting_budget, 2),
                'cost' => number_format($workSite->cost, 2),
                'address' => AddressFormat::format([
                    'title' => $this->address?->title,
                    'state' => $this->address?->state,
                    'city' => $this->address?->city?->name,
                    'street' => $this->address?->street,
                    'zipcode' => $this->address?->zipcode,
                ]),
                'workers_count' => $workSite->workers_count,
                'receipt_date' => $workSite->receipt_date,
                'starting_date' => $workSite->starting_date,
                'deliver_date' => $workSite->deliver_date,
                'reception_status' => WorkSiteReceptionStatusEnum::from($workSite->reception_status)->name,
                'completion_status' => WorkSiteCompletionStatusEnum::from($workSite->completion_status)->name,
                'created_at' => Carbon::parse($workSite->created_at)->toDateTimeString(),
                'updated_at' => Carbon::parse($workSite->updated_at)->toDateTimeString(),
                'payments' => [
                    [
                        'amount' => number_format(20, 2),
                        'payment_date' => Carbon::parse(Carbon::now())->format('Y-m-d H:i'),
                        'payment_type' => PaymentTypesEnum::CASH->name,
                    ],
                ],
                'items' => [
                    [
                        'id' => $workSiteResource1->id,
                        'name' => $workSiteResource1->name,
                        'quantity_in_warehouse' => 23,
                        'quantity_in_worksite' => 10,
                        'in_stock' => 'In-Stock',
                        'price' => '34.00',
                    ],
                ],
                'media' => [
                    [
                        'id' => $media->id,
                        'url' => 'http://127.0.0.1:8000/storage/worksite/test.webp',
                    ],
                ],
            ]);
    });

});
describe('Close WorkSites', function () {

    beforeEach(function () {

        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->siteManager()->create();
        expect($this->notAdmin->hasRole('site_manager'))->toBe(true);

        $this->worksite = Worksite::factory()->create([
            'cost' => 2000,
        ]);

    });
    test('As a non-authenticated, I cant close a worksite', function () {
        $response = postJson('/api/v1/worksite/close/' . $this->worksite->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    test('As not admin, I cant close a worksite', function () {
        $response = actingAs($this->notAdmin)->postJson('/api/v1/worksite/close/' . $this->worksite->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found error if worksite not existed in database', function () {
        $unExistedWorkSiteId = rand(200, 333);
        $response = actingAs($this->admin)->postJson('/api/v1/worksite/close/' . $unExistedWorkSiteId);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should prevent me closing worksite with active worksites', function () {
        Worksite::factory()->create([
            'completion_status' => WorkSiteCompletionStatusEnum::STARTED,
            'parent_worksite_id' => $this->worksite->id,
        ]);
        $response = actingAs($this->admin)->postJson('/api/v1/worksite/close/' . $this->worksite->id);
        $response->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'message' => "You can't close a worksite with active sub-worksites",
            ]);

    });
    it('should prevent me closing worksite with unpaid payments', function () {
        Payment::factory()->create([
            'payable_id' => $this->worksite->id,
            'payable_type' => 'worksite',
            'amount' => 1000,
            'payment_date' => Carbon::now(),
            'payment_type' => PaymentTypesEnum::CASH->value,
        ]);
        Payment::factory()->create([
            'payable_id' => $this->worksite->id,
            'payable_type' => 'worksite',
            'amount' => 100,
            'payment_date' => Carbon::now(),
            'payment_type' => PaymentTypesEnum::CASH->value,
        ]);
        $response = actingAs($this->admin)->postJson('/api/v1/worksite/close/' . $this->worksite->id);

        $response->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'message' => "You can't close a worksite with unpaid payment",
            ]);
    });
    test('As an admin, I can close a worksite with full payments and closed sub worksites', function () {
        Payment::factory()->create([
            'payable_id' => $this->worksite->id,
            'payable_type' => 'worksite',
            'amount' => $this->worksite->cost,
            'payment_date' => Carbon::now(),
            'payment_type' => PaymentTypesEnum::CASH->value,
        ]);
        Worksite::factory()->create([
            'completion_status' => WorkSiteCompletionStatusEnum::CLOSED,
            'parent_worksite_id' => $this->worksite->id,
        ]);
        $response = actingAs($this->admin)->postJson('/api/v1/worksite/close/' . $this->worksite->id);
        $response->assertStatus(Response::HTTP_OK);

        assertDatabaseHas(Worksite::class, [
            'completion_status' => WorkSiteCompletionStatusEnum::CLOSED,
        ]);
    });

});
describe('Assign Contractor to WorkSites', function () {

    beforeEach(function () {

        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->siteManager()->create();
        expect($this->notAdmin->hasRole('site_manager'))->toBe(true);

        $this->worksite = Worksite::factory()->create();
        $this->contractor = Contractor::factory()->create();

    });
    test('As a non-authenticated, I cant assign contractor to a worksite', function () {
        $response = putJson('/api/v1/worksite/' . $this->worksite->id . '/contractor/' . $this->contractor->id . '/assign');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    test('As not admin, I cant assign contractor to a worksite', function () {
        $response = actingAs($this->notAdmin)->putJson('/api/v1/worksite/' . $this->worksite->id . '/contractor/' . $this->contractor->id . '/assign');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found error if worksite not existed in database and if contractor not existed', function () {
        $unExistedWorkSiteId = rand(200, 333);
        $unExistedContractorId = rand(200, 333);
        $response = actingAs($this->admin)->putJson('/api/v1/worksite/' . $unExistedWorkSiteId . '/contractor/' . $this->contractor->id . '/assign');
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $otherResponse = actingAs($this->admin)->putJson('/api/v1/worksite/' . $this->worksite->id . '/contractor/' . $unExistedContractorId . '/assign');
        $otherResponse->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should add contractor of a worksite', function () {
        $response = actingAs($this->admin)->putJson('/api/v1/worksite/' . $this->worksite->id . '/contractor/' . $this->contractor->id . '/assign');
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(Worksite::class, [
            'contractor_id' => $this->contractor->id,
        ]);

    });
    it('should update contractor of a worksite', function () {
        $response = actingAs($this->admin)->putJson('/api/v1/worksite/' . $this->worksite->id . '/contractor/' . $this->contractor->id . '/assign');
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(Worksite::class, [
            'contractor_id' => $this->contractor->id,
        ]);

        $otherContractor = Contractor::factory()->create();

        $response = actingAs($this->admin)->putJson('/api/v1/worksite/' . $this->worksite->id . '/contractor/' . $otherContractor->id . '/assign');
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(Worksite::class, [
            'contractor_id' => $otherContractor->id,
        ]);

    });
    test('As an admin i can remove contractor of a worksite ', function () {
        $response = actingAs($this->admin)->putJson('/api/v1/worksite/' . $this->worksite->id . '/contractor/' . $this->contractor->id . '/assign');
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(Worksite::class, [
            'contractor_id' => $this->contractor->id,
        ]);

        $response = actingAs($this->admin)->putJson('/api/v1/worksite/' . $this->worksite->id . '/contractor/' . $this->contractor->id . '/unAssign');
        $response->assertStatus(Response::HTTP_OK);
        assertDatabaseHas(Worksite::class, [
            'contractor_id' => null,
        ]);

    });
});
describe('Manage items of the worksite', function () {
    test('As a non-authenticated, I cant manage resource of a worksite', function () {
    });
    test('As not admin, I cant manage resource of a worksite', function () {
    });

    it('should add new resource to a worksite', function () {
    });
    it('should update existed resource of a worksite', function () {
    });
    it('should delete items of a worksite', function () {
    });
    it('should show all items a worksite', function () {
    });
    it('should show details of a resource in the worksite', function () {
    });
});
describe('Manage employees of the worksite', function () {
    test('As a non-authenticated, I cant manage workers of a worksite', function () {
    });
    test('As not admin, I cant manage workers of a worksite', function () {
    });
    it('should add new worker to a worksite', function () {
    });
    it('should update a worker of a worksite', function () {
    });
    it('should delete a worker of a worksite', function () {
    });
    it('should show all workers of the worksite', function () {
    });
    it('should show details of a worker in a worksite', function () {
    });
});
describe('manage customer of the worksite', function () {
    test('As a non-authenticated, I cant manage resource of a worksite', function () {
    });
    test('As not admin, I cant manage resource of a worksite', function () {
    });

    it('should add new customer to a worksite', function () {
    });
    it('should update a customer details of a worksite', function () {
    });
    it('should prevent delete a customer of an active worksite', function () {
    });
    it('should delete a customer of a closed worksite', function () {
    });
    it('should show details of a customer in a worksite', function () {
    });
});
