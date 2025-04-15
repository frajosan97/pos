<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    /**
    * Run the database seeds.
    */

    public function run(): void {
        // Step 1: Define permissions
        $allPermissions = getProtectedRouteNames();

        // Create and/or get permissions
        foreach ( $allPermissions as $permissionSlug ) {
            // Normalize the route name to a consistent slug format ( e.g., 'chat.index' -> 'chat_index' )
            $slug = permissionSlug($permissionSlug);

            // Remove 'index' from the slug when generating name and description
            $displayName = str_replace( [ '_', 'index' ], [ ' ', '' ], $slug );
            // removes 'index' and replaces '_' with space

            $permission = Permission::firstOrCreate( [
                'slug' => $slug,
            ], [
                'name' => ucfirst( $displayName ),
                'description' => ucfirst( $displayName ) . ' Permission',
            ] );
        }

        // Step 3: Create the admin user ( modify values as necessary )
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
        $this->command->info('Admin user with permissions created successfully.' );
        }
    }
