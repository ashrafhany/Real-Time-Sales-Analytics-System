<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic roles if they don't exist
        $roles = [
            'admin' => 'Full system access',
            'manager' => 'Sales management access',
            'sales' => 'Sales data access',
            'user' => 'Basic API user access',
        ];

        foreach ($roles as $roleName => $description) {
            Role::firstOrCreate(['name' => $roleName], [
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }
    }
}
