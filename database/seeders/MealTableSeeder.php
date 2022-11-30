<?php

namespace Database\Seeders;

use App\Models\Meal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MealTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $meals = [
            [
                'name' => 'Nasi Goreng',
                'price' => 10000,
                'description' => 'Nasi Goreng',
                'stock' => 10,

            ],
            [
                'name' => 'Nasi Goreng Spesial',
                'price' => 15000,
                'description' => 'Nasi Goreng Spesial',
                'stock' => 10,

            ],
            [
                'name' => 'Nasi Goreng Spesial Pedas',
                'price' => 20000,
                'description' => 'Nasi Goreng Spesial Pedas',
                'stock' => 10,

            ],
            [
                'name' => 'Nasi Goreng Spesial Pedas Manis',
                'price' => 25000,
                'description' => 'Nasi Goreng Spesial Pedas Manis',
                'stock' => 10,

            ],
            [
                'name' => 'Nasi Goreng Spesial Pedas Manis Asin',
                'price' => 30000,
                'description' => 'Nasi Goreng Spesial Pedas Manis Asin',
                'stock' => 10,

            ],
            [
                'name' => 'Nasi Goreng Spesial Pedas Manis Asin Gurih',
                'price' => 35000,
                'description' => 'Nasi Goreng Spesial Pedas Manis Asin Gurih',
                'stock' => 10,

            ],
        ];
        Meal::insert($meals);
    }
}
