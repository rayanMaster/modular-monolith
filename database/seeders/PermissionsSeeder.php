<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Base Add Permission Function
    |--------------------------------------------------------------------------
    */
    private function addPermission(string $group, string $name, array $roles)
    {
        $permissionGroup = PermissionGroup::query()->updateOrCreate(
            ['name' => $group],
            ['name' => $group, 'display_name' => $group]);
        foreach ($roles as $role) {
            Permission::query()->updateOrCreate(['name' => $name != '' ? $name.'-'.$role : $role], [
                'name' => $name != '' ? $name.'-'.$role : $role,
                'display_name' => ucwords(str_replace('-', ' ', $role)),
                'guard_name' => 'web',
                'permission_group_id' => $permissionGroup?->id,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Run the database seeds
    |--------------------------------------------------------------------------
    */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        // WorkSite
        $this->addPermission('Work Site', 'worksite', ['list', 'show', 'add', 'update', 'delete']);

        // WorkSite Category
        $this->addPermission('Work Site Category', 'worksite-category', ['list', 'show', 'add', 'update', 'delete']);

        // Resource
        $this->addPermission('Resource', 'resource', ['list', 'show', 'add', 'update', 'delete']);

        // Resource Category
        $this->addPermission('Resource Category', 'resource-category', ['list', 'show', 'add', 'update', 'delete']);

        // Customer
        $this->addPermission('Customer', 'customer', ['list', 'show', 'add', 'update', 'delete']);

        // Customer
        $this->addPermission('Payment', 'payment', ['list', 'show', 'add']);

        //Worker
        $this->addPermission('Worker', 'worker', ['list', 'show', 'add', 'update', 'delete']);

    }
}
