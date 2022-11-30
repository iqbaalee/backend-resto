<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CustomerTableSeeder::class,
            DiscountTableSeeder::class,
            ProductTableSeeder::class,
            UserTableSeeder::class,
            PermissionTableSeeder::class,
            MenuTableSeeder::class,
            RoleTableSeeder::class
        ]);
    }
}
