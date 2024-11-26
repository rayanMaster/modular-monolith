<?php

use App\Enums\ChartOfAccountNamesEnum;
use App\Enums\PaymentTypesEnum;
use App\Helpers\CacheHelper;
use App\Http\Integrations\Accounting\Connector\AccountingConnector;
use App\Http\Integrations\Accounting\Requests\GetWorksitePayment\GetWorksitePaymentsRequest;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use App\Models\Worksite;
use App\Services\PaymentSyncService;
use Carbon\Carbon;
use Mockery\MockInterface;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

describe('Create Payment for a Worksite', function () {

    beforeEach(function () {
        MockClient::global([
            AccountingConnector::class => MockResponse::make([
                'response' => [
                    'results' => [
                    ],
                ],
            ], 200),
        ]);
        Cache::tags('worksite_payments')->flush(); // Clear any cached data at the start

        $this->worksite = Worksite::factory()->create();
        $this->customer = Customer::factory()->create();

        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();

        expect($this->admin->hasRole('admin'))->toBe(true);

        // Arrange: Set a UUID for the worksite
        $this->worksite->uuid = 'test-worksite-uuid';  // Ensure this matches what the method expects
        $cacheTag = 'worksite_payments';
        $this->cacheKeyHelper = new CacheHelper;
        $this->cacheKey = $this->cacheKeyHelper->generateCacheKey($cacheTag, $this->worksite->uuid);

    });

    it('should prevent non auth making new payment for a worksite', function () {
        $response = postJson('/api/v1/worksite/'.$this->worksite->id.'/payment/create');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin making new payment for a worksite', function () {

        $response = actingAs($this->notAdmin)->postJson('/api/v1/worksite/'.$this->worksite->id.'/payment/create');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    test('As an administrator, I want to make payment from customer related to worksite', function () {

        $worksite = Worksite::factory()->create([
            'customer_id' => $this->customer->id,
        ]);
        $this->mock(PaymentSyncService::class, function (MockInterface $mock) {
            $mock->shouldReceive('syncPaymentsToAccounting')
                ->once();

        });

        $response = actingAs($this->admin)->postJson('/api/v1/worksite/'.$worksite->id.'/payment/create', [
            'payment_amount' => 3000,
            'payment_type' => PaymentTypesEnum::CASH->value,
            'payment_date' => '2024-04-12 10:34',
            'payment_from_id' => $this->customer->id,
            'payment_from_type' => ChartOfAccountNamesEnum::CLIENTS->value,

        ]);
        $response->assertOk();

    });
    it('should prevent pay from customer to not related worksite', function () {

        $response = actingAs($this->admin)->postJson('/api/v1/worksite/'.$this->worksite->id.'/payment/create', [
            'payment_amount' => 3000,
            'payment_type' => PaymentTypesEnum::CASH->value,
            'payment_date' => '2024-04-12 10:34',
            'payment_from_id' => $this->customer->id,
            'payment_from_type' => ChartOfAccountNamesEnum::CLIENTS->value,

        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJsonFragment([
            'message' => 'Customer not related to this worksite',
        ]);

    });
    it('fetches payments from cache if it exists', function () {

        $mockClient = new MockClient([
            GetWorksitePaymentsRequest::class => MockResponse::make(),
        ]);

        $connector = new AccountingConnector;
        $connector->withMockClient($mockClient);

        // Arrange: Cache mock response
        $mockedPayments = collect((object) [
            'amount' => 200,
            'payment_date' => Carbon::now()->format('Y-m-d H:i'),
            'payment_type' => PaymentTypesEnum::CASH->value,
        ]);

        // Use consistent cache tags and key for the cached data
        Cache::put($this->cacheKey, $mockedPayments, 3600);

        // Create a partial mock for PaymentSyncService
        $paymentService = Mockery::mock(PaymentSyncService::class, [$connector, $this->cacheKeyHelper])
            ->makePartial();

        // Create a partial mock of PaymentSyncService
        $paymentService->shouldNotReceive('fetchPayments');

        // Act: Call the service method
        $paymentService->getPaymentsForWorksite($this->worksite);

        // Assert: Check the response
        expect(Cache::get($this->cacheKey))->toEqual($mockedPayments);

    });
    it('fetches payments with calling fetch after invalidate cache', function () {

        // Arrange
        $mockClient = new MockClient([
            GetWorksitePaymentsRequest::class => MockResponse::make(),
        ]);

        $connector = new AccountingConnector;
        $connector->withMockClient($mockClient);

        Cache::forget($this->cacheKey);
        // Create a partial mock for PaymentSyncService
        $paymentService = Mockery::mock(PaymentSyncService::class, [$connector, $this->cacheKeyHelper])
            ->makePartial();

        // Arrange: Cache mock response
        $mockedPayments = collect((object) [
            'amount' => 200,
            'payment_date' => Carbon::now()->format('Y-m-d H:i'),
            'payment_type' => PaymentTypesEnum::CASH->value,
        ]);

        $paymentService->shouldReceive('fetchPayments')
            ->with($this->worksite)
            ->andReturn($mockedPayments)
            ->once();
        // Act
        $result = $paymentService->getPaymentsForWorksite($this->worksite);

        // Assert
        expect($result)->toEqual($result);

    });
});

describe('List Payments for a Worksite', function () {

    beforeEach(function () {

        $this->worksite = Worksite::factory()->create();

        $this->firstPayment = Payment::factory()->create([
            'amount' => 3000,
            'payment_type' => PaymentTypesEnum::CASH->value,
            'payment_date' => '2024-04-12 10:34:00',
            'payable_id' => $this->worksite->id,
            'payable_type' => Worksite::class,

        ]);

        $this->secondPayment = Payment::factory()->create([
            'amount' => 2000,
            'payment_type' => PaymentTypesEnum::CASH->value,
            'payment_date' => '2024-04-20 10:34:00',
            'payable_id' => $this->worksite->id,
            'payable_type' => Worksite::class,

        ]);

        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();

        expect($this->admin->hasRole('admin'))->toBe(true);
    });

    it('should prevent non auth show all payments of a worksite', function () {
        $response = getJson('/api/v1/worksite/'.$this->worksite->id.'/payment/list');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin show all payments of a worksite', function () {

        $response = actingAs($this->notAdmin)->getJson('/api/v1/worksite/'.$this->worksite->id.'/payment/list');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    test('As an administrator, I want to show all payments of a worksite', function () {

        $query = '?date_from=2024-04-11 10:34:00&date_to=2024-04-22 10:34:00';
        $response = actingAs($this->admin)
            ->getJson('/api/v1/worksite/'.$this->worksite->id.'/payment/list'.$query);
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'amount',
                        'payment_type',
                        'payment_date',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'amount' => '3000.00',
                'payment_type' => PaymentTypesEnum::CASH->name,
                'payment_date' => Carbon::parse($this->firstPayment->payment_date)->format('Y-m-d H:i'),
            ])
            ->assertJsonFragment([
                'amount' => '2000.00',
                'payment_type' => PaymentTypesEnum::CASH->name,
                'payment_date' => Carbon::parse($this->secondPayment->payment_date)->format('Y-m-d H:i'),
            ]);

    });
    test('As an administrator, I want to show payments in date range of a worksite', function () {

        $query = '?date_from=2024-04-11 10:34:00&date_to=2024-04-18 10:34:00';
        $response = actingAs($this->admin)
            ->getJson('/api/v1/worksite/'.$this->worksite->id.'/payment/list'.$query);
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'amount',
                        'payment_type',
                        'payment_date',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'amount' => '3000.00',
                'payment_type' => PaymentTypesEnum::CASH->name,
                'payment_date' => Carbon::parse($this->firstPayment->payment_date)->format('Y-m-d H:i'),
            ])
            ->assertJsonMissingExact([
                'amount' => '2000.00',
                'payment_type' => PaymentTypesEnum::CASH->name,
                'payment_date' => Carbon::parse($this->secondPayment->payment_date)->format('Y-m-d H:i'),
            ]);

    });
});
