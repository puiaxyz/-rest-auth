<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Insert test data for menu items
        MenuItem::create([
            'name' => 'Cheeseburger',
            'description' => 'A delicious cheeseburger with all the fixings.',
            'price' => 8.99,
        ]);

        MenuItem::create([
            'name' => 'Pizza Margherita',
            'description' => 'Classic pizza with fresh mozzarella, tomatoes, and basil.',
            'price' => 12.99,
        ]);

        MenuItem::create([
            'name' => 'Caesar Salad',
            'description' => 'Crisp romaine lettuce, creamy Caesar dressing, and croutons.',
            'price' => 6.99,
        ]);

        MenuItem::create([
            'name' => 'Grilled Chicken Wrap',
            'description' => 'Grilled chicken, fresh veggies, and sauce wrapped in a tortilla.',
            'price' => 7.99,
        ]);

        MenuItem::create([
            'name' => 'Spaghetti Bolognese',
            'description' => 'Pasta with a rich and savory meat sauce.',
            'price' => 14.99,
        ]);
    }
}
