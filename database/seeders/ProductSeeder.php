<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                "sku" => "SF-MAX-20W50",
                "name" => "SuperFuel Max 20W-50",
                "category" => "Mineral Oils",
                "description" => "High-performance mineral oil for heavy-duty engines",
                "price" => 4500.00,
                "tax_rate" => 16.0,
                "unit" => "Liter",
                "packaging" => "5L Container",
                "min_order_quantity" => 1,
                "reorder_level" => 30
            ],
            [
                "sku" => "ED-SYN-5W30",
                "name" => "EcoDrive Synthetic 5W-30",
                "category" => "Synthetic Oils",
                "description" => "Fully synthetic oil for modern passenger vehicles",
                "price" => 7200.00,
                "tax_rate" => 16.0,
                "unit" => "Liter",
                "packaging" => "4L Container",
                "min_order_quantity" => 1,
                "reorder_level" => 40
            ],
        ];

        foreach ($products as $product) {
            $category_id = Category::where('name', $product['category'])->first()?->id;

            Product::create([
                'name' => $product['name'],
                'sku' => $product['sku'],
                'category_id' => $category_id,
                'description' => $product['description'],
                'price' => $product['price'],
                'tax_rate' => $product['tax_rate'],
                'unit' => $product['unit'],
                'packaging' => $product['packaging'],
                'min_order_quantity' => $product['min_order_quantity'],
                'reorder_level' => $product['reorder_level'],
            ]);
        }
    }
}
