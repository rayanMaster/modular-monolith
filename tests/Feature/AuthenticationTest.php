<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\postJson;

describe('Login', function () {

    beforeEach(function () {

        $this->artisan('migrate:fresh --seed');
    });
    it('should authenticate with valid credentials', function () {
        $response = postJson('/api/v1/auth/login', [
            'user_name' => '0945795748',
            'password' => 'admin123',
        ]);
        $response->assertStatus(Response::HTTP_OK);
    });
    it('should not authenticate with incorrect credentials', function () {
        $response = postJson('/api/v1/auth/login', [
            'user_name' => '0945795748',
            'password' => 'admin12',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
});
