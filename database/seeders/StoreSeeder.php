<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
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
            Store::create([
                'main' => $customer->main ?? 1,
                'rel_id' => $customer->rel_id ?? 0,
                'employee_id' => $customer->employee_id ?? 0,
                'name' => $customer->name ?? 'No Name',
                'phone' => $customer->phone ?? '123456789',
                'address' => $customer->address ?? 'address',
                'city' => $customer->city ?? 'city',
                'region' => $customer->region ?? 'region',  // 
                'country' => $customer->country ?? 'country',
                'postbox' => $customer->postbox ?? 'postbox',
                'email' => $customer->email ?? '    email@email',
                'picture' => $customer->picture ?? 'picture.png',
                'company' => $customer->company ?? 'company',
                'taxid' => $customer->taxid ?? '',
                'name_s' => $customer->name_s ?? '',
                'phone_s' => $customer->phone_s ?? '',
                'email_s' => $customer->email_s ?? '',
                'address_s' => $customer->address_s ?? '',
                'city_s' => $customer->city_s ?? '',
                'region_s' => $customer->region_s ?? '-',
                'country_s' => $customer->country_s ?? '',
                'postbox_s' => $customer->postbox_s ?? '',
                'balance' => $customer->balance ?? 0,
                'docid' => $customer->docid ?? '',
                'custom1' => $customer->custom1 ?? '',
                'ins' => $customer->ins ?? 0,
                'active' => $customer->active ?? 1,
                'password' => $customer->password ?? '',
                'role_id' => $customer->role_id ?? 0,
                'remember_token' => $customer->remember_token ?? '',
                'referral_id' => $customer->referral_id ?? '',
                'account_no' => $customer->account_no ?? '',
                'provider' => $customer->provider ?? '',
                'provider_id' => $customer->provider_id ?? '',
                'birth_date' => $customer->birth_date ?? '',
                'auto_substation' => $customer->auto_substation ?? 0,
                'store_name' => $customer->store_name ?? 'Unnamed Store',
                'segment' => $customer->segment ?? 'General',
                'governorate' => $customer->governorate ?? '',
                'lat' => $customer->lat ?? '',
                'lng' => $customer->lng ?? '',
                'store_status' => $customer->store_status ?? 0,
                'points' => $customer->points ?? 0,
            ]);

        }
    }
}
