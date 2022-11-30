<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            Table::insert([
                'name' => 'Meja ' . $i,
                'description' => 'Meja ' . $i,
                'capacity' => random_int(1, 5),
            ]);
        }
    }
}
