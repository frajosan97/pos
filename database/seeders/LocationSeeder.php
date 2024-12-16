<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\County;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch the County instance for "Nairobi" with its constituencies and wards
        $county = County::where('name', 'Nairobi')
            ->with(['constituencies.wards'])  // Eager load constituencies and their related wards
            ->first();

        if (!$county) {
            $this->command->warn('County "Nairobi" not found. Skipping LocationSeeder.');
            return;
        }

        // Fetch the Constituency instance for "Westlands" in the "Nairobi" county
        $constituency = $county->constituencies->firstWhere('name', 'Westlands');

        if (!$constituency) {
            $this->command->warn('Constituency "Westlands" not found in County "Nairobi". Skipping LocationSeeder.');
            return;
        }

        // Fetch the Ward instance for "Karura" in the "Westlands" constituency
        $ward = $constituency->wards->firstWhere('name', 'Karura');

        if (!$ward) {
            $this->command->warn('Ward "Karura" not found in Constituency "Westlands". Skipping LocationSeeder.');
            return;
        }

        // Define the locations for the Karura Ward
        $locations = [
            'Karura Forest',
            'Village Market',
            'Ridgeways',
            'Zambezi',
            'Kitisuru',
            'Muthaiga North',
            'Westgate',
            'Mombasa Road (near Sarit Centre)',
        ];

        // Insert each location into the database
        foreach ($locations as $locationName) {
            Location::create([
                'name' => $locationName,
                'ward_id' => $ward->id,
                'constituency_id' => $constituency->id,
                'county_id' => $county->id,
            ]);
        }

        $this->command->info('Locations for Karura Ward in Westlands Constituency, Nairobi County added successfully.');
    }
}
