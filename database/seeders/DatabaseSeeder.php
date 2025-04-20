<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KategoriSeeder::class,
            SupplierSeeder::class,
            LevelSeeder::class,
            UserSeeder::class,
            BarangSeeder::class,
            StockSeeder::class,
            PenjualanSeeder::class,
            DetailSeeder::class,
        ]);
}
}
