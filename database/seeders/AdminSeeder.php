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
            'manage-content',
            'manage-pages',
            'manage-contacts',
            'manage-settings',
            'manage-refund'
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }

        $role->syncPermissions(Permission::all());

        $admin = Admin::create([
            'name' => 'Dev Admin',
            'email' => 'admin@example.com',
            'password' => 'cRv*n0F8cvbG',
            'is_super' => true
        ]);

        $admin->assignRole('dev-admin');
    }
}
