<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "name" => "Engine Oils",
                "subCategories" => ["Mineral Oils", "Synthetic Oils"]
            ],
        ];

        foreach ($categories as $category) {
            $newCategory = Category::create([
                'name' => $category['name'],
                "slug" => Str::slug($category["name"], '-'),
            ]);

            foreach ($category['subCategories'] as $sub) {
                $newCategory->subCategories()->create([
                    'name' => $sub,
                    "slug" => Str::slug($sub, '-'),
                ]);
            }
        }
    }
}
