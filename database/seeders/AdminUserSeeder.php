<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role
        $adminRole = Role::create(['name' => 'Admin']);
        $managerRole = Role::create(['name' => 'Manager']);
        $salesRole = Role::create(['name' => 'Sales']);

        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create manager user
        $managerUser = User::create([
            'name' => 'Sales Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create sales user
        $salesUser = User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign roles
        $adminUser->assignRole('Admin');
        $managerUser->assignRole('Manager');
        $salesUser->assignRole('Sales');

        // Create permissions
        $permissions = [
            'view dashboard',
            'manage orders',
            'create orders',
            'edit orders',
            'delete orders',
            'manage products',
            'create products',
            'edit products',
            'delete products',
            'view analytics',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());

        $managerRole->givePermissionTo([
            'view dashboard',
            'manage orders',
            'manage products',
            'view analytics',
            'export reports',
        ]);

        $salesRole->givePermissionTo([
            'view dashboard',
            'view analytics',
            'create orders',
        ]);
    }
}
