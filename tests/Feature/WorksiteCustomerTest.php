<?php

use App\Models\Customer;
use App\Models\User;
use App\Models\Worksite;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

describe('Worksite Customer assign', function () {

    beforeEach(function () {

        $this->notAdmin = User::factory()->worker()->create(['email' => 'not_admin@admin.com']);
        $this->admin = User::factory()->admin()->create(['email' => 'admin@admin.com']);

        $this->worksite = Worksite::factory()->create();
        $this->customer = Customer::factory()->create();
    });

    it('should prevent non auth assigning a Customer to worksite', function () {
        $response = postJson('/api/v1/worksite/'.$this->worksite->id.'/customer/'.$this->customer->id.'/assign');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin assigning a Customer to worksite', function () {

        $response = actingAs($this->notAdmin)
            ->postJson('/api/v1/worksite/'.$this->worksite->id.'/customer/'.$this->customer->id.'/assign');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found error when worksite not found', function () {
        $undefinedWorksiteId = rand(222, 333);
        $response = actingAs($this->admin)
            ->postJson('/api/v1/worksite/'.$undefinedWorksiteId.'/customer/'.$this->customer->id.'/assign');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return not found error when customer not found', function () {
        $undefinedCustomerId = rand(222, 333);
        $response = actingAs($this->admin)
            ->postJson('/api/v1/worksite/'.$this->worksite->id.'/customer/'.$undefinedCustomerId.'/assign');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should assign valid customer to a valid worksite', function () {
        $response = actingAs($this->admin)
            ->postJson('/api/v1/worksite/'.$this->worksite->id.'/customer/'.$this->customer->id.'/assign');
        $response->assertOk();
        assertDatabaseHas(Worksite::class, [
            'customer_id' => $this->customer->id,
        ]);
    });
});
