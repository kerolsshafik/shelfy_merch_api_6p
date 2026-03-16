<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PaswordChangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = Customer::all();

        foreach ($customers as $customer) {
            $customer->password = Hash::make($customer->phone . '@GKL');
            $customer->save();
        }
    }
}
