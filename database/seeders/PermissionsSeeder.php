<?php

namespace Database\Seeders;

use App\Models\ModelHasRole;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        // Worksite
        //        Role::query()->truncate();
        //        ModelHasRole::query()->truncate();
        //
        //        Permission::query()->truncate();
        $this->addPermission('Work Site', 'worksite', [
            'list',
            'show',
            'create',
            'update',
            'delete',
            'close',
            'employee-assign',
            'contractor-assign',
            'customer-assign',
            'item-add',
            'item-list',
        ]);

        // Worksite Category
        $this->addPermission('Work Site Category', 'worksite-category', ['list', 'show', 'create', 'update', 'delete']);

        // Item
        $this->addPermission('Item', 'item', ['list', 'show', 'create', 'update', 'delete']);

        // Item Category
        $this->addPermission('Item Category', 'item-category', ['list', 'show', 'create', 'update', 'delete']);

        // Customer
        $this->addPermission('Customer', 'customer', ['list', 'show', 'create', 'update', 'delete']);

        // Customer
        $this->addPermission('Payment', 'payment', ['list', 'show', 'create']);

        //City
        $this->addPermission('City', 'city', ['list', 'show', 'create', 'update', 'delete']);

        //Employee
        $this->addPermission('Employee', 'employee',
            [
                'list',
                'show',
                'create',
                'update',
                'delete',
                'attendance-add',
                'attendance-update',
                'attendance-list',
            ]);

        //Contractor
        $this->addPermission('Contractor', 'contractor', ['list', 'show', 'create', 'update', 'delete']);

        //Warehouse
        $this->addPermission('Warehouse', 'warehouse', [
            'list', 'show', 'create', 'update', 'delete',
            'item-add',
            'item-move',
            'item-update',
            'item-list',
        ]);

        $this->addPermission('Order', 'order', ['list', 'show', 'create', 'update', 'delete']);

    }
}
