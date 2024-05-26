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
        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        // WorkSite
        PermissionGroup::query()->updateOrCreate(['name' => 'work-site', 'display_name' => 'WorkSite'], ['name' => 'workSite']);

        /*
        |--------------------------------------------------------------------------
        | WorkSite Category
        |--------------------------------------------------------------------------
        */

        PermissionGroup::query()->updateOrCreate(['name' => 'ws-category', 'display_name' => 'WorkSite Category'], ['name' => 'ws-category']);


    }
}
