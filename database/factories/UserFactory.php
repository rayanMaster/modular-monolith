<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => 'Rayan',
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'password' => 'Rayan123@@',
        ];
    }

    public function mainAdmin(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Main Admin',
            'phone' => '0945795749',
            'email' => 'main_admin@admin.com',
            'password' => 'admin123',
        ])->afterCreating(function (User $user) {
            return $user->assignRole('admin');
        });
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Admin',
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'password' => 'admin123',
        ])->afterCreating(function (User $user) {
            return $user->assignRole('admin');
        });
    }

    public function siteManager(): static
    {
        return $this->afterCreating(function (User $user) {
            return $user->assignRole('site_manager');
        });
    }

    public function worker(): static
    {
        return $this->afterCreating(function (User $user) {
            return $user->assignRole('worker');
        });
    }
}
