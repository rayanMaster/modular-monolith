<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\WorkSite;
use App\Models\WorkSiteResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkSiteResource>
 */
class WorkSiteResourceFactory extends Factory
{
    protected $model = WorkSiteResource::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'work_site_id' => fn() => WorkSite::query()->first() ?
                WorkSite::query()->first()->id : WorkSite::factory()->create()->id,
            'resource_id' => fn() => Resource::query()->first() ?
                Resource::query()->first()->id : Resource::factory()->create()->id,
            'quantity' => fake()->numberBetween(1, 20),
            'price' => fake()->randomFloat(10, 100),
        ];
    }
}
