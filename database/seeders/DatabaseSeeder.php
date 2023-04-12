<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Category::factory(3)->hasProducts(10)->create();
        \App\Models\OrderStatus::create(['title' => 'Pending', 'uuid' => Str::uuid()]);
        \App\Models\OrderStatus::create(['title' => 'Paid', 'uuid' => Str::uuid()]);
        \App\Models\User::factory()->create([
            'email' => 'admin@buckhill.co.uk',
            'password' => bcrypt('admin'),
            'is_admin' => 1,
        ]);
        \App\Models\User::factory()
            ->hasOrders(1, [ // use this uuid for testing
                'uuid' => '415834b1-a411-4470-b944-5ab423fadbae',
            ])
            ->create([
                'email' => 'test@buckhill.co.uk',
                'password' => bcrypt('userpassword'),
            ]);
        \App\Models\User::factory(10)->hasOrders(5)->create();
        
    }
}
