<?php

use App\Models\Item;
use App\Models\User;
use App\Models\WorkSite;
use App\Models\WorkSiteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

describe('WorkSite Item assign', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->worker()->create();
        $this->admin = User::factory()->admin()->create();

        $this->workSite = Worksite::factory()->create();
        $this->resource = Item::factory()->create();
    });

    it('should prevent non auth adding a resource to a workSite', function () {
        $response = postJson('/api/v1/workSite/'.$this->workSite->id.'/item/'.$this->resource->id.'/add');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin adding a resource to a workSite', function () {
        $response = actingAs($this->notAdmin)
            ->postJson('/api/v1/workSite/'.$this->workSite->id.'/item/'.$this->resource->id.'/add');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found error when workSite not found', function () {
        $undefinedWorkSiteId = rand(222, 333);
        $response = actingAs($this->admin)
            ->postJson('/api/v1/workSite/'.$undefinedWorkSiteId.'/item/'.$this->resource->id.'/add', [
                'quantity' => 10,
                'price' => 30,
            ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return not found error when resource not found', function () {
        $undefinedResourceId = rand(222, 333);
        $response = actingAs($this->admin)
            ->postJson('/api/v1/workSite/'.$this->workSite->id.'/item/'.$undefinedResourceId.'/add', [
                'quantity' => 10,
                'price' => 30,
            ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should add valid resource to a valid workSite', function () {
        $response = actingAs($this->admin)
            ->postJson('/api/v1/workSite/'.$this->workSite->id.'/item/'.$this->resource->id.'/add', [
                'quantity' => 10,
                'price' => 30,
            ]);
        $response->assertOk();
        assertDatabaseHas(WorkSiteItem::class, [
            'work_site_id' => $this->workSite->id,
            'item_id' => $this->resource->id,
            'quantity' => 10,
            'price' => '30.00',
        ]);
    });
});
describe('WorkSite Item list', function () {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->artisan('storage:link');
        $this->assertDatabaseCount(Role::class, 0);
        $this->artisan('db:seed');
        $this->assertDatabaseCount(Role::class, 4);
        $this->notAdmin = User::factory()->worker()->create();
        $this->admin = User::factory()->admin()->create();

        $this->workSite = Worksite::factory()->create();
        $this->item = Item::factory()->create([
            'name' => 'Iron',
        ]);
        $this->workSiteItem = WorkSiteItem::factory()->create([
            'quantity' => 10,
            'price' => 3000,
            'work_site_id' => $this->workSite->id,
            'item_id' => $this->item->id,
        ]);
    });

    it('should prevent non auth show list items of a workSite', function () {
        $response = getJson('/api/v1/workSite/'.$this->workSite->id.'/item/list');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
    it('should prevent non admin show list items of a workSite', function () {
        $response = actingAs($this->notAdmin)
            ->getJson('/api/v1/workSite/'.$this->workSite->id.'/item/list');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
    it('should return not found error when workSite not found', function () {
        $undefinedWorkSiteId = rand(222, 333);
        $response = actingAs($this->admin)
            ->getJson('/api/v1/workSite/'.$undefinedWorkSiteId.'/item/list');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
    it('should return list of items of a  valid workSite', function () {
        $response = actingAs($this->admin)
            ->getJson('/api/v1/workSite/'.$this->workSite->id.'/item/list');
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'item',
                        'quantity',
                        'price',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'id' => $this->item->id,
                'item' => $this->item->name,
                'quantity' => 10,
                'price' => '3000.00',
            ]);
    });
});
