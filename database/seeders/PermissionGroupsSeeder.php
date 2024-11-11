<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PermissionGroup::query()->truncate();
        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        // Worksite
        PermissionGroup::query()->updateOrCreate(['name' => 'work-site', 'display_name' => 'Worksite'], ['name' => 'worksite']);
        /*
        |--------------------------------------------------------------------------
        | Worksite Category
        |--------------------------------------------------------------------------
        */
        PermissionGroup::query()->updateOrCreate(['name' => 'worksite-category', 'display_name' => 'Worksite Category'], ['name' => 'ws-category']);

        /*
       |--------------------------------------------------------------------------
       | Customer
       |--------------------------------------------------------------------------
       */
        PermissionGroup::query()->updateOrCreate(['name' => 'customer', 'display_name' => 'Customer'], ['name' => 'customer']);

        /*
      |--------------------------------------------------------------------------
      | Payment
      |--------------------------------------------------------------------------
      */
        PermissionGroup::query()->updateOrCreate(['name' => 'payment', 'display_name' => 'Payment'], ['name' => 'payment']);

    }
}
