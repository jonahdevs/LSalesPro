<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Territory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $territories = [
            'Nairobi',
            'Thika',
            'Mombasa',
            'Kisimu',
        ];

        foreach ($territories as $territory) {
            Territory::create([
                'name' => $territory,
                'slug' => Str::slug($territory, '-'),
            ]);
        }

        $customers = [
            [
                "name" => "Quick Auto Services Ltd",
                "type" => "Garage",
                "category" => "A",
                "contact_person" => "John Mwangi",
                "phone" => "+254-712-345678",
                "email" => "info@quickautoservices.co.ke",
                "tax_id" => "P051234567Q",
                "payment_terms" => 30,
                "credit_limit" => 500000.00,
                "current_balance" => 120000.00,
                "latitude" => -1.319370,
                "longitude" => 36.824120,
                "address" => "Mombasa Road, Auto Plaza Building, Nairobi"
            ],
            [
                "name" => "Premium Motors Kenya",
                "type" => "Dealership",
                "category" => "A+",
                "contact_person" => "Sarah Wanjiku",
                "phone" => "+254-722-678901",
                "email" => "sarah.w@premiummotors.co.ke",
                "tax_id" => "P051345678R",
                "payment_terms" => 45,
                "credit_limit" => 1000000.00,
                "current_balance" => 450000.00,
                "latitude" => -1.292066,
                "longitude" => 36.821946,
                "address" => "Uhuru Highway, Premium Towers, Nairobi"
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
