<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\WorkSite;
use App\Models\WorkSiteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class WorkSiteFactory extends Factory
{
    protected $model = WorkSite::class;

    public function definition(): array
    {
        Storage::fake();

        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');

        $wsCategory = WorkSiteCategory::factory()->create();

        $customer = Customer::factory()->create();

        return [
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
            'customer_id' => $customer?->id,
            'category_id' => $wsCategory?->id, // construction
            'main_worksite' => null, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address' => 1,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'status_on_receive' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
