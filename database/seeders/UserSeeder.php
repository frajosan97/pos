<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'branch_id' => 1, // Adjust branch_id as needed
                'role_id' => 3, // Administrator role_id
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123456'),
                'created_by' => 'System', // Optional
                'updated_by' => 'System', // Optional
            ],
            [
                'branch_id' => 1, // Adjust branch_id as needed
                'role_id' => 2, // Branch Manager role_id
                'name' => 'Branch Manager',
                'email' => 'bm@gmail.com',
                'password' => Hash::make('bm123456'),
                'created_by' => 'System', // Optional
                'updated_by' => 'System', // Optional
            ],
            [
                'branch_id' => 1, // Adjust branch_id as needed
                'role_id' => 1, // Officer role_id
                'name' => 'Marketing Officer',
                'email' => 'officer@example.com',
                'password' => Hash::make('officer123456'),
                'created_by' => 'System', // Optional
                'updated_by' => 'System', // Optional
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
