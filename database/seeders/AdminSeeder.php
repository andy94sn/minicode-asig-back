<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $role = Role::create([
            'name' => 'dev-admin',
            'description' => 'Dev Admin',
            'guard_name' => 'api'
        ]);

        $permissions = [
            'manage-admins',
            'manage-permissions',
            'manage-orders',
            'manage-pages',
            'manage-contacts',
            'manage-settings',
            'manage-refund',
            'manage-blog'
        ];

        $permissionsDescriptions = [
            'Manage Admins',
            'Manage Permissions',
            'Manage Orders',
            'Manage Pages',
            'Manage Contacts',
            'Manage Settings',
            'Manage Refund',
            'Manage Blog'
        ];

        foreach ($permissions as $key => $permission) {
            Permission::create([
                'name' => $permission,
                'description' => $permissionsDescriptions[$key],
                'guard_name' => 'api'
            ]);
        }

        $role->syncPermissions(Permission::all());

        $admin = Admin::create([
            'name' => 'Dev Admin',
            'email' => 'dev@minicode.md',
            'password' => 'cRv*n0F8cvbG'
        ]);

        $admin->assignRole('dev-admin');
    }
}
