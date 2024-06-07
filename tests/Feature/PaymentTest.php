<?php

use App\Models\WorkSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

describe('Create Payment Feature', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);

    });

    test('As an administrator, I want to make payment to worksite', function () {

        $workSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();
        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson("/api/v1/worksite/$workSite->id/payment/create", [
            'payment_amount' => 3000,
            'payment_type' => 1,
            'payment_date' => '2024-04-12 10:34',
            'payable_id' => $workSite->id,
            'payable_type' => WorkSite::class,

        ]);
        $response->assertOk();

        expect($workSite->last_payment->amount)->toBe(3000)
            ->and($workSite->last_payment->payment_date)->toBe('2024-04-12 10:34')
            ->and($workSite->last_payment->payment_type)->toBe(1)
            ->and($workSite->last_payment->entity_id)->toBe($workSite->id)
            ->and($workSite->last_payment->entity_type)->toBe(WorkSite::class);

    })->skip();

});
