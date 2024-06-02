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
            'email' => 'rayan@rayan.com',
            'password' => 'Rayan123@@',
        ];
    }

    public function mainAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Main Admin',
            'email' => 'main_admin@admin.com',
            'password' => 'admin123',
        ])->afterCreating(function (User $user) {
            return $user->assignRole('admin');
        });
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Admin',
            'email' => 'admin@admin.com',
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

    public function employee(): static
    {
        return $this->afterCreating(function (User $user) {
            return $user->assignRole('worker');
        });
    }
}
