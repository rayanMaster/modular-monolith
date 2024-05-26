<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'description' => fake()->name,
            'resource_category_id' => ResourceCategory::query()->first() ?
                ResourceCategory::query()->first()->id :
                ResourceCategory::factory()->create()->id,

        ];
    }
}
