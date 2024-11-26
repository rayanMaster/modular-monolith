<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Worksite;
use App\Models\WorksiteItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorksiteItem>
 */
class WorksiteItemFactory extends Factory
{
    protected $model = WorksiteItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'worksite_id' => fn () => Worksite::query()->first() ?
                Worksite::query()->first()->id : Worksite::factory()->create()->id,
            'item_id' => fn () => Item::query()->first() ?
                Item::query()->first()->id : Item::factory()->create()->id,
            'quantity' => fake()->numberBetween(1, 20),
            'price' => fake()->randomFloat(10, 100),
        ];
    }
}
