<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                "code" => "NCW",
                "name" => "Nairobi Central Warehouse",
                "type" => "Main",
                "address" => "Enterprise Road, Industrial Area, Nairobi",
                "manager_email" => "warehouse.nairobi@leysco.co.ke",
                "phone" => "+254-20-5551234",
                "capacity" => 50000,
                "latitude" => -1.308971,
                "longitude" => 36.851523
            ],
            [
                "code" => "MRW",
                "name" => "Mombasa Regional Warehouse",
                "type" => "Regional",
                "address" => "Port Reitz Road, Changamwe, Mombasa",
                "manager_email" => "warehouse.mombasa@leysco.co.ke",
                "phone" => "+254-41-2224567",
                "capacity" => 30000,
                "latitude" => -4.034396,
                "longitude" => 39.647446
            ]
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create([
                'name' => $warehouse['name'],
                'code' => $warehouse['code'],
                'type' => $warehouse['type'],
                'address' => $warehouse['address'],
                'manager_email' => $warehouse['manager_email'],
                'phone' => $warehouse['phone'],
                'capacity' => $warehouse['capacity'],
                'latitude' => $warehouse['latitude'],
                'longitude' => $warehouse['longitude'],
            ]);
        }
    }

}
