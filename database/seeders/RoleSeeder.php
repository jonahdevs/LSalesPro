<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'sanctum']);
        $salesManager = Role::create(['name' => 'Sales Manager', 'guard_name' => 'sanctum']);
        $salesRepresentative = Role::create(['name' => 'Sales Representative', 'guard_name' => 'sanctum']);

        $permissions = [
            "view_all_sales",
            "create_sales",
            "approve_sales",
            "manage_inventory",
            "view_own_sales",
            "view_inventory",
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        $admin->syncPermissions($permissions);

        $salesManager->syncPermissions([
            "view_all_sales",
            "create_sales",
            "approve_sales",
            "manage_inventory"
        ]);

        $salesRepresentative->syncPermissions([
            "view_own_sales",
            "create_sales",
            "view_inventory"
        ]);
    }
}
