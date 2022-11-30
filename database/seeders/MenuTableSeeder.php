<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->insert(
            [
                [
                    'name' => 'Menu',
                    'url' => 'menu',
                    'icon' => 'fas fa-bars',
                ],
                [
                    'name' => 'Hak Akses',
                    'url' => 'role',
                    'icon' => 'fas fa-user-tag',
                ],
                [
                    'name' => 'Meja',
                    'url' => 'table',
                    'icon' => 'fas fa-chair',
                ],
                [
                    'name' => 'Transaksi',
                    'url' => 'transaction',
                    'icon' => 'fas fa-money-bill-wave',
                ],
                [
                    'name' => 'Customer',
                    'url' => 'customer',
                    'icon' => 'fas fa-users',
                ],
                [
                    'name' => 'Laporan',
                    'url' => 'report',
                    'icon' => 'fas fa-file-alt',
                ],
                ['name' => 'Hidangan', 'url' => 'meal', 'icon' => 'fas fa-hamburger']
            ]
        );
    }
}
