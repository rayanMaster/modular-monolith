<?php

use App\Models\WorkSite;
use Illuminate\Foundation\Testing\RefreshDatabase;

describe('Create WorkSite Controller', function () {

    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);

    });

    test('As an administrator, I want to make payment to worksite', function () {

        $workSite = WorkSite::factory()->create();

        $admin = \App\Models\User::factory()->admin()->create();
        expect($admin->hasRole('admin'))->toBe(true);

        $response = $this->actingAs($admin)->postJson("/api/v1/worksite/payment/add/$workSite->id", [
            'payment_amount' => 3000,
            'payment_date' => '2024-04-12 10:34',

        ]);

        $response->assertOk();

        expect($workSite->last_payment->amount)->toBe(3000)
            ->and($workSite->last_payment->payment_date)->toBe('2024-04-12 10:34')
            ->and($workSite->last_payment->payment_type)->toBe(1);

    });

});
