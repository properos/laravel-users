<?php

use Illuminate\Database\Seeder;
use Properos\Users\Models\Permission;
use Properos\Users\Models\Role;

class RolesPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = Role::firstOrCreate([
            'name' => 'admin',
            'label' => 'Admin',
            'url' => '/admin/dashboard'
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $role_customer = Role::firstOrCreate([
            'name' => 'customer',
            'label' => 'Customer',
            'url' => '/',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $role_customer = Role::firstOrCreate([
            'name' => 'support',
            'label' => 'Support',
            'url' => '/',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $role_customer = Role::firstOrCreate([
            'name' => 'sale',
            'label' => 'Sales',
            'url' => '/',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $permission_create_item = Permission::firstOrCreate([
            'name' => 'create_items',
            'label' => 'Create items',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $permission_edit_item = Permission::firstOrCreate([
            'name' => 'edit_items',
            'label' => 'Edit items',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $permission_delete_item = Permission::firstOrCreate([
            'name' => 'delete_items',
            'label' => 'Delete items',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $permission_create_user = Permission::firstOrCreate([
            'name' => 'create_users',
            'label' => 'Create users',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $permission_edit_user = Permission::firstOrCreate([
            'name' => 'edit_users',
            'label' => 'Edit users',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $permission_delete_user = Permission::firstOrCreate([
            'name' => 'delete_users',
            'label' => 'Delete users',
        ],[
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $role_admin->assignPermission($permission_create_item);
        $role_admin->assignPermission($permission_edit_item);
        $role_admin->assignPermission($permission_delete_item);
        $role_admin->assignPermission($permission_create_user);
        $role_admin->assignPermission($permission_edit_user);
        $role_admin->assignPermission($permission_delete_user);
    }
}
