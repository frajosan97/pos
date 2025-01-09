<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Define permissions
        $permissions = [
            // Manager-related permissions
            'manager_catalogue',
            'manager_product',
            'manager_self',
            'manager_branch',
            'manager_general',
            // Sale-related permissions
            'sale_create',
            'sale_view',
            'sale_edit',
            'sale_delete',
            // Product-related permissions
            'catalogue_create',
            'catalogue_view',
            'catalogue_edit',
            'catalogue_delete',
            'product_create',
            'product_view',
            'product_edit',
            'product_delete',
            // User-related permissions
            'user_create',
            'user_view',
            'user_edit',
            'user_delete',
            // Permission-related permissions
            'permission_create',
            'permission_view',
            'permission_edit',
            'permission_delete',
            // Payment-related permissions
            'payment_create',
            'payment_view',
            'payment_edit',
            'payment_delete',
            // Report-related permissions
            'reports_inventory',
            'reports_sales',
            'reports_payments',
            // Setting-related permissions
            'system_setting',
            'system_cache',
            'system_optimization',
            // Analytics
            'view_analytics',
        ];

        // Step 2: Create and/or get predefined permissions
        foreach ($permissions as $permissionSlug) {
            $permission = Permission::firstOrCreate([
                'slug' => $permissionSlug,
                'name' => ucfirst(str_replace('_', ' ', $permissionSlug)),
                'description' => ucfirst(str_replace('_', ' ', $permissionSlug)) . ' Permission', // Optional: Add description
            ]);
        }

        // Step 3: Create the admin user (modify values as necessary)
        $user = [
            'branch_id' => 1, // Example: modify based on actual data
            'user_name' => 'admin', // Admin username
            'name' => 'Administrator', // Full name
            'passport' => 'A12345678', // Example passport number, if needed
            'gender' => 'male', // Gender example
            'phone' => '0700000000', // Phone number
            'id_number' => '12345678', // ID number
            'email' => 'frajosan97@gmail.com', // Admin's email
            'password' => Hash::make('Frajosan97@001'), // Password
            'created_by' => 'System', // Created by system
            'updated_by' => 'System', // Updated by system
        ];

        // Step 4: Create the admin user
        $adminUser = User::create($user);

        // Step 5: Assign permissions to the admin user directly
        $permissions = Permission::all(); // Get all permissions

        // Attach permissions to the user
        $adminUser->permissions()->attach($permissions);

        // Step 6: Output success message
        $this->command->info('Admin user with permissions created successfully.');
    }
}
