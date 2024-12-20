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
                'branch_id' => 1,
                'role_id' => 3,
                'user_name' => 'admin',
                'name' => 'Administrator',
                'phone' => '0700000000',
                'id_number' => '12345678',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123456'),
                'created_by' => 'System',
                'updated_by' => 'System',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
