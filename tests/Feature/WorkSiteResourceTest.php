<?php

use App\Models\Resource;
use App\Models\User;
use App\Models\WorkSite;
use App\Models\WorkSiteResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

describe('WorkSite Resource assign', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->worker()->create();
        $this->admin = User::factory()->admin()->create();

        $this->workSite = Worksite::factory()->create();
        $this->resource = Resource::factory()->create();
    });

    it('should prevent non auth adding a resource to a workSite', function () {
        $response = postJson('/api/v1/workSite/'.$this->workSite->id.'/resource/'.$this->resource->id.'/add');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin adding a resource to a workSite', function () {
        $response = actingAs($this->notAdmin)
            ->postJson('/api/v1/workSite/'.$this->workSite->id.'/resource/'.$this->resource->id.'/add');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found error when workSite not found', function () {
        $undefinedWorkSiteId = rand(222, 333);
        $response = actingAs($this->admin)
            ->postJson('/api/v1/workSite/'.$undefinedWorkSiteId.'/resource/'.$this->resource->id.'/add', [
                'quantity' => 10,
                'price' => 30,
            ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return not found error when resource not found', function () {
        $undefinedResourceId = rand(222, 333);
        $response = actingAs($this->admin)
            ->postJson('/api/v1/workSite/'.$this->workSite->id.'/resource/'.$undefinedResourceId.'/add', [
                'quantity' => 10,
                'price' => 30,
            ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should add valid resource to a valid workSite', function () {
        $response = actingAs($this->admin)
            ->postJson('/api/v1/workSite/'.$this->workSite->id.'/resource/'.$this->resource->id.'/add', [
                'quantity' => 10,
                'price' => 30,
            ]);
        $response->assertOk();
        assertDatabaseHas(WorkSiteResource::class, [
            'work_site_id' => $this->workSite->id,
            'resource_id' => $this->resource->id,
            'quantity' => 10,
            'price' => '30.00',
        ]);
    });
});
describe('WorkSite Resource list', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->worker()->create();
        $this->admin = User::factory()->admin()->create();

        $this->workSite = Worksite::factory()->create();
        $this->resource = Resource::factory()->create([
            'name' => 'Iron',
        ]);
        $this->workSiteResource = WorkSiteResource::factory()->create([
            'quantity' => 10,
            'price' => 3000,
            'work_site_id' => $this->workSite->id,
            'resource_id' => $this->resource->id,
        ]);
    });

    it('should prevent non auth show list resources of a workSite', function () {
        $response = getJson('/api/v1/workSite/'.$this->workSite->id.'/resource/list');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin show list resources of a workSite', function () {
        $response = actingAs($this->notAdmin)
            ->getJson('/api/v1/workSite/'.$this->workSite->id.'/resource/list');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found error when workSite not found', function () {
        $undefinedWorkSiteId = rand(222, 333);
        $response = actingAs($this->admin)
            ->getJson('/api/v1/workSite/'.$undefinedWorkSiteId.'/resource/list');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return list of resources of a  valid workSite', function () {
        $response = actingAs($this->admin)
            ->getJson('/api/v1/workSite/'.$this->workSite->id.'/resource/list');
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'resource',
                        'quantity',
                        'price',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'id' => $this->resource->id,
                'resource' => $this->resource->name,
                'quantity' => 10,
                'price' => '3000.00',
            ]);
    });
});
