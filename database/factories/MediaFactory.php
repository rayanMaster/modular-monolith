<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Worksite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => Worksite::class,
            'model_id' => fn () => Worksite::query()->first() ?
                Worksite::query()->first()->id :
                Worksite::factory()->create()->id,
            'name' => fake()->name,
            'file_name' => fake()->filePath(),
            'mime_type' => 'jpeg',
            'disk' => 'storage',
            'size' => '100',
        ];
    }
}
