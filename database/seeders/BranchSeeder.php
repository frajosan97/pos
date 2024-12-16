<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use App\Models\County;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch the County instance for "Nairobi" with its constituencies and wards
        $county = County::where('name', 'Nairobi')
            ->with(['constituencies.wards.locations'])  // Eager load constituencies and their related wards
            ->first();

        if (!$county) {
            $this->command->warn('County "Nairobi" not found. Skipping BranchSeeder.');
            return;
        }

        // Fetch the Constituency instance for "Westlands" in the "Nairobi" county
        $constituency = $county->constituencies->firstWhere('name', 'Westlands');

        if (!$constituency) {
            $this->command->warn('Constituency "Westlands" not found in County "Nairobi". Skipping BranchSeeder.');
            return;
        }

        // Fetch the Ward instance for "Karura" in the "Westlands" constituency
        $ward = $constituency->wards->firstWhere('name', 'Karura');

        if (!$ward) {
            $this->command->warn('Ward "Karura" not found in Constituency "Westlands". Skipping BranchSeeder.');
            return;
        }

        // Fetch the Location instance for "Westgate" in the "Karura" Ward
        $location = $constituency->locations->firstWhere('name', 'Westgate');

        if (!$location) {
            $this->command->warn('Location "Westgate" not found in Ward "Karura". Skipping BranchSeeder.');
            return;
        }

        // Define the locations for the Karura Ward
        $branches = [
            'Head Quota (HQ)',
        ];

        // Insert each location into the database
        foreach ($branches as $branchName) {
            Branch::create([
                'name' => $branchName,
                'location_id' => $location->id,
                'ward_id' => $ward->id,
                'constituency_id' => $constituency->id,
                'county_id' => $county->id,
            ]);
        }

        $this->command->info('Branches for Westgate Location in Karura Ward in Westlands Constituency, Nairobi County added successfully.');
    }
}
