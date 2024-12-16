<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\County;
use App\Models\Ward;

class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch the County instance for "Nairobi"
        $county = County::where('name', 'Nairobi')->with('constituencies')->first();

        if (!$county) {
            $this->command->warn('County "Nairobi" not found. Skipping WardSeeder.');
            return;
        }

        // Fetch the Constituency instance for "Westlands" in the "Nairobi" county
        $constituency = $county->constituencies->firstWhere('name', 'Westlands');

        if (!$constituency) {
            $this->command->warn('Constituency "Westlands" not found in County "Nairobi". Skipping WardSeeder.');
            return;
        }

        // Define wards for the Westlands Constituency
        $wards = [
            'Karura',
            'Kangemi',
            'Kitisuru',
            'Mountain View',
            'Parklands/Highridge',
        ];

        // Insert each ward into the database
        foreach ($wards as $wardName) {
            Ward::create([
                'name' => $wardName,
                'constituency_id' => $constituency->id,
                'county_id' => $county->id,
            ]);
        }

        $this->command->info('Wards for Westlands Constituency in Nairobi County added successfully.');
    }
}
