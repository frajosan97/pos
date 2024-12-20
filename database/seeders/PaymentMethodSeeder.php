<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::create([
            'icon' => 'cash-icon', // replace with actual icon path or class
            'name' => 'cash',
            'image' => 'assets/images/icons/cash.png', // replace with the actual image path
        ]);

        PaymentMethod::create([
            'icon' => 'mpesa-icon', // replace with actual icon path or class
            'name' => 'mpesa',
            'image' => 'assets/images/icons/mpesa.png', // replace with the actual image path
        ]);
    }
}
