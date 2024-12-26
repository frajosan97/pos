<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Create the admin role (if not already exists)
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator role with full system access',
        ]);

        // Step 2: Create permissions (or use predefined permissions from your app)
        $permissions = [
            // User-related permissions
            'user_create',
            'user_view',
            'user_edit',
            'user_delete',
            // Role-related permissions
            'role_create',
            'role_view',
            'role_edit',
            'role_delete',
            // Permission-related permissions
            'permission_create',
            'permission_view',
            'permission_edit',
            'permission_delete',
            // Catalogue-related permissions
            'catalogue_create',
            'catalogue_view',
            'catalogue_edit',
            'catalogue_delete',
            // Product-related permissions
            'product_create',
            'product_view',
            'product_edit',
            'product_delete',
            // Sale-related permissions
            'sale_create',
            'sale_view',
            'sale_edit',
            'sale_delete',
            // Payments-related permissions
            'payment_create',
            'payment_view',
            'payment_edit',
            'payment_delete',
            // Settings-related permissions
            'analytics',
            // Reports-related permissions
            'reports_inventory',
            'reports_payments',
            'reports_sales',
            // Settings-related permissions
            'settings',
            // Cache-related permissions
            'clear_cache',
            // Data management
            'data_manager_self',
            'data_manager_branch',
            'data_manager_general',
        ];

        // Step 3: Assign permissions to the admin role
        foreach ($permissions as $permissionSlug) {
            $permission = Permission::firstOrCreate([
                'slug' => $permissionSlug,
                'name' => ucfirst(str_replace('_', ' ', $permissionSlug)),
            ]);
            $adminRole->permissions()->attach($permission);
        }

        // Step 4: Create the admin user
        $user = [
            'branch_id' => 1, // Modify as needed
            'user_name' => 'admin', // Admin username
            'name' => 'Administrator', // Admin's full name
            'phone' => '0700000000', // Admin's phone number
            'id_number' => '12345678', // Admin's ID number
            'email' => 'frajosan97@gmail.com', // Admin's email address
            'password' => Hash::make('Frajosan97@001'), // Admin's password
            'created_by' => 'System', // Created by system
            'updated_by' => 'System', // Updated by system
        ];

        // Step 5: Create the admin user
        $adminUser = User::create($user);

        // Step 6: Attach the admin role to the created user
        $adminUser->roles()->attach($adminRole);

        // Output a success message
        $this->command->info('Admin user with role and permissions created successfully.');
    }
}
