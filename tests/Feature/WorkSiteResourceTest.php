<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\{get,assertDatabaseHas,postJson,actingAs,putJson,
    getJson,deleteJson,assertDatabaseCount,assertSoftDeleted};



//describe("WorkSiteResource Create", function () {
//    uses(RefreshDatabase::class);
//
//    beforeEach(function () {
//        $this->artisan('storage:link');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
//        $this->artisan('db:seed');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
//        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
//        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
//
//    });
//    it('should prevent non auth creating new Resource', function () {
//        $response = postJson('/api/v1/worksite/resource/create');
//        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
//    });
//    it('should prevent non admin creating new Resource', function () {
//
//        $response = actingAs($this->notAdmin)->postJson('/api/v1/worksite/resource/create');
//        $response->assertStatus(Response::HTTP_FORBIDDEN);
//    });
//    it('should return validation error when data is missed', function () {
//        $response = actingAs($this->admin)->postJson('/api/v1/worksite/resource/create', []);
//        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
//    });
//    it('should create new Resource with valid data', function () {
//        $response = actingAs($this->admin)->postJson('/api/v1/worksite/resource/create',  [
//            'name' => 'new',
//            'description' => 'new',
//            'category_id' => '1',
//        ]);
//        $response->assertOk();
//    });
//});

//describe("WorkSiteesource Update", function () {
//    uses(RefreshDatabase::class);
//
//    beforeEach(function () {
//        $this->artisan('storage:link');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
//        $this->artisan('db:seed');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
//        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
//        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
//
//        $this->resource = WorkSiteResource::factory()->create(['title' => 'new']);
//
//    });
//
//    it('should prevent non auth updating existed Resource', function () {
//        $response = putJson('/api/v1/worksite/resource/update/'.$this->resource->id, [
//            'title' => 'new1'
//        ]);
//        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
//    });
//    it('should prevent non admin updating existed Resource', function () {
//
//        $response = actingAs($this->notAdmin)->putJson('/api/v1/worksite/resource/update/'.$this->resource->id, [
//            'title' => 'new1'
//        ]);
//        $response->assertStatus(Response::HTTP_FORBIDDEN);
//    });
//    it('should not return validation error when data is missed', function () {
//        $response = actingAs($this->admin)->postJson('/api/v1/worksite/resource/create', []);
//        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
//    });
//    it('should update existed Resource with valid data', function () {
//
//        $response = actingAs($this->admin)->putJson('/api/v1/worksite/resource/update/'.$this->resource->id, [
//            'title' => 'new1'
//        ]);
//        assertDatabaseHas('ws_categories', ['title'=>'new1']);
//        $response->assertOk();
//    });
//});
//
//describe("WorkSiteesource List", function () {
//    uses(RefreshDatabase::class);
//
//    beforeEach(function () {
//        $this->artisan('storage:link');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
//        $this->artisan('db:seed');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
//        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
//        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
//
//        $this->esource1 = WorkSiteesource::factory()->create(['title' => 'resource 1']);
//        $this->esource2 = WorkSiteesource::factory()->create(['title' => 'resource 2']);
//        $this->esource3 = WorkSiteesource::factory()->create(['title' => 'resource 3']);
//
//    });
//    it('should prevent non auth updating existed resource', function () {
//        $response = getJson('/api/v1/worksite/resource/list/');
//        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
//    });
//    it('should return data', function () {
//        $response = actingAs($this->admin)->getJson('/api/v1/worksite/resource/list/');
//        $response->assertStatus(Response::HTTP_OK)
//            ->assertJsonCount(3,'data')
//            ->assertJsonFragment(["title"=>"resource 1"]);
//    });
//});
//
//describe("WorkSiteesource Details", function () {
//    uses(RefreshDatabase::class);
//
//    beforeEach(function () {
//        $this->artisan('storage:link');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
//        $this->artisan('db:seed');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
//        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
//        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
//
//        $this->resource = WorkSiteesource::factory()->create(['id'=>10, 'title' => 'resource 10']);
//
//    });
//    it('should prevent non auth show resource', function () {
//        $response = getJson('/api/v1/worksite/resource/show/'.$this->resource->id);
//        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
//    });
//    it('should return not found for un-existed resource', function () {
//        $response = actingAs($this->admin)->getJson('/api/v1/worksite/resource/show/2');
//        $response->assertStatus(Response::HTTP_NOT_FOUND);
//    });
//    it('should return data', function () {
//        $response = actingAs($this->admin)->getJson('/api/v1/worksite/resource/show/'.$this->resource->id);
//        $response->assertStatus(Response::HTTP_OK)
//            ->assertJsonCount(2,'data')
//            ->assertJsonFragment(["title"=>"resource 10"]);
//    });
//});
//
//describe("WorkSiteesource Delete", function () {
//    uses(RefreshDatabase::class);
//
//    beforeEach(function () {
//        $this->artisan('storage:link');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 0);
//        $this->artisan('db:seed');
//        $this->assertDatabaseCount(\Spatie\Permission\Models\Role::class, 4);
//        $this->notAdmin = User::factory()->employee()->create(['email' => 'not_admin@admin.com']);
//        $this->admin = \App\Models\User::factory()->admin()->create(['email' => 'admin@admin.com']);
//
//        $this->resource = WorkSiteesource::factory()->create(['id'=>10, 'title' => 'resource 10']);
//
//    });
//    it('should prevent non auth delete resource', function () {
//        $response = deleteJson('/api/v1/worksite/resource/delete/'.$this->resource->id);
//        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
//    });
//    it('should prevent non admin delete existed resource', function () {
//        $response = actingAs($this->notAdmin)->deleteJson('/api/v1/worksite/resource/delete/'.$this->resource->id);
//        $response->assertStatus(Response::HTTP_FORBIDDEN);
//    });
//    it('should return not found for un-existed resource', function () {
//        $response = actingAs($this->admin)->deleteJson('/api/v1/worksite/resource/delete/2');
//        $response->assertStatus(Response::HTTP_NOT_FOUND);
//    });
//    it('should delete resource from database', function () {
//        assertDatabaseCount('ws_categories',1);
//        actingAs($this->admin)->deleteJson('/api/v1/worksite/resource/delete/'.$this->resource->id);
//        assertSoftDeleted('ws_categories',['id'=>$this->resource->id]);
//    });
//});
