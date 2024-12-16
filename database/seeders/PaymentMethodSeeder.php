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
            'name' => 'Cash',
            'image' => 'assets/images/icons/cash.png', // replace with the actual image path
        ]);

        PaymentMethod::create([
            'icon' => 'mpesa-icon', // replace with actual icon path or class
            'name' => 'M-Pesa',
            'image' => 'assets/images/icons/mpesa.png', // replace with the actual image path
        ]);
    }
}
