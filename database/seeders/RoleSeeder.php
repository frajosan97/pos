<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'role' => 1,
                'name' => 'marketing officer',
                'description' => 'none',
            ],
            [
                'role' => 2,
                'name' => 'branch manager',
                'description' => 'none',
            ],
            [
                'role' => 3,
                'name' => 'administrator',
                'description' => 'none',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
