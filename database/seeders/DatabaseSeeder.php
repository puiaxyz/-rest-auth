<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call MenuSeeder and CartSeeder
        $this->call([
            OrderSeeder::class,
            MenuSeeder::class,
        ]);
    }
}
