<?php

namespace Database\Seeders;

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
    }
}
