<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed an order for user_id 1
        Order::create([
            'user_id' => 1,
            'total_price' => 150.75, // Example total price
            'status' => 'pending', // Initial status
        ]);

        // Optionally, create multiple orders for testing
        Order::factory()->count(5)->create([
            'user_id' => 1,
        ]);
    }
}
