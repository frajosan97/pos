<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'Frajosan IT Consultancies POS.',
            'address' => '222 - 90200 Kitui, Nairobi, Kenya',
            'phone' => '+254796594366',
            'email' => 'info@frajosantech.co.ke',
            'logo' => 'logo.png',
            'color' => 'blue',
            'sms_mode' => 'online',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
