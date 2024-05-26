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
            'email' => 'Rayan@Rayan.com',
            'password' => 'Rayan',
        ];
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
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
