<?php

namespace Database\Factories;

use App\Models\WorksiteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkSiteCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = WorksiteCategory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id' => null,
            'name' => fake()->name,
            'status' => fake()->randomElement([0, 1]),
        ];
    }
}
