<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i < 5; $i++) {
            Discount::insert([
                'name' => 'Discount ' . $i,
                'description' => 'Deskripsi ' . $i,
                'min_order' => $i
            ]);
        }
    }
}
