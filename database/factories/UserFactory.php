<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => $this->faker->numberBetween(1, 10), // Random branch ID, adjust range as needed
            'role' => $this->faker->randomElement([1, 2, 3]), // Valid roles: 1 (Officer), 2 (Branch Manager), 3 (Administrator)
            'name' => $this->faker->name,
            'passport' => 'avatar.png', // Default avatar
            'gender' => $this->faker->randomElement(['male', 'female']),
            'phone' => '0700000000', // Default phone number for consistency
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'otp' => null, // Default OTP is null
            'password' => Hash::make('password'), // Default password
            'created_by' => 'Seeder', // Optional: Seeder as creator
            'updated_by' => 'Seeder', // Optional: Seeder as updater
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model's role is 'admin'.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 3, // Administrator role
        ]);
    }

    /**
     * Indicate that the model's role is 'credit_officer'.
     */
    public function branchManager(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 2, // Branch Manager role
        ]);
    }

    /**
     * Indicate that the model's role is 'operations_officer'.
     */
    public function marketingOfficer(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 1, // Officer role
        ]);
    }
}
