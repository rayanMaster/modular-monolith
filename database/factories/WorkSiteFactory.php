<?php

namespace Database\Factories;

use App\Enums\WorkSiteCompletionStatusEnum;
use App\Enums\WorkSiteReceptionStatusEnum;
use App\Models\Address;
use App\Models\Contractor;
use App\Models\Customer;
use App\Models\User;
use App\Models\Worksite;
use App\Models\WorksiteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WorkSiteFactory extends Factory
{
    protected $model = Worksite::class;

    public function definition(): array
    {
        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');

        $wsCategory = WorksiteCategory::factory()->create();

        $customer = Customer::factory()->create();
        $contractor = Contractor::factory()->create();
        $manager = User::factory()->siteManager()->create();

        return [
            'uuid' => fake()->uuid,
            'title' => 'worksite A',
            'description' => 'this worksite is for freeTown',
            'manager_id' => $manager->id,
            'customer_id' => $customer->id,
            'category_id' => $wsCategory->id, // construction
            'contractor_id' => $contractor->id,
            'parent_worksite_id' => null, // this is main worksite == top level worksite
            'starting_budget' => 15,
            'cost' => 20,
            'address_id' => fn () => Address::query()->first() != null ?
                Address::query()->first()->id : Address::factory()->create()->id,
            'workers_count' => 20,
            'receipt_date' => '2024-04-12',
            'starting_date' => '2024-04-12',
            'deliver_date' => '2024-04-12',
            'reception_status' => WorkSiteReceptionStatusEnum::SCRATCH->value,
            'completion_status' => WorkSiteCompletionStatusEnum::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
