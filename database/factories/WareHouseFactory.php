<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\WareHouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WareHouse>
 */
class WareHouseFactory extends Factory
{
    protected $model = WareHouse::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'address_id'=>fn()=>Address::query()->first() ? Address::query()->first()->id :
                Address::factory()->create()->id,
        ];
    }
}
