<?php

use App\Models\Employee;
use App\Models\User;
use App\Models\WorkSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\{postJson, getJson, putJson, actingAs, assertDatabaseHas, assertDatabaseCount};

//describe('Assign worker for the worksite', function () {
//    uses(RefreshDatabase::class);
//
//    beforeEach(function () {
//        $this->artisan('db:seed');
//        $this->admin = User::factory()->admin()->create();
//        expect($this->admin->hasRole('admin'))->toBeTrue();
//
//        $this->notAdmin = User::factory()->siteManager()->create();
//        expect($this->notAdmin->hasRole('site_manager'))->toBeTrue();
//
//        $this->workSite = WorkSite::factory()->create();
//
//        $this->worker = User::factory()->worker()->create();
//    });
//    test('As a non-authenticated, I cant assign a worker to a worksite', function () {
//        $response = postJson('/api/v1/worksite/' . $this->workSite->id . '/employee/create', []);
//        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
//    });
//    test('As not admin, I cant assign a worker of a worksite', function () {
//        $response = actingAs($this->notAdmin)->postJson('/api/v1/worksite/' . $this->workSite->id . '/employee/create', []);
//        $response->assertStatus(Response::HTTP_FORBIDDEN);
//    });
//    it('should assign new worker to a worksite', function () {
//        $response = actingAs($this->admin)->postJson('/api/v1/worksite/' . $this->workSite->id . '/employee/assign');
//        $response->assertStatus(Response::HTTP_OK);
//    });
//    it('should update a worker of a worksite', function () {
//
//    });
//    it('should delete a worker of a worksite', function () {
//
//    });
//    it('should show all employees of the worksite', function () {
//
//    });
//    it('should show details of a worker in a worksite', function () {
//
//    });
//});
