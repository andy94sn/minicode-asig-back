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
        $role = Role::firstOrCreate(
            ['name' => 'dev-admin', 'guard_name' => 'api'],
            ['description' => 'Dev Admin']
        );
        

        $permissions = [
            'manage-admins',
            'manage-permissions',
            'manage-orders',
            'manage-pages',
            'manage-contacts',
            'manage-settings',
            'manage-refund',
            // 'manage-blog',
            'manage-payments',
            'manage-all-payments',
        ];

        $permissionsDescriptions = [
            'Manage Admins',
            'Manage Permissions',
            'Manage Orders',
            'Manage Pages',
            'Manage Contacts',
            'Manage Settings',
            'Manage Refund',
            // 'Manage Blog',
            'Create order payment links',
            "Show all payment links"
        ];

        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'api'],
                ['description' => $permissionsDescriptions[$key]]
            );
        }

        $role->syncPermissions(Permission::all());

        $admin = Admin::firstOrCreate(
            ['email' => 'dev@minicode.md'],
            [
                'name' => 'Dev Admin',
                'password' => 'cRv*n0F8cvbG'
            ]
        );
        

        $admin->assignRole('dev-admin');
    }
}
