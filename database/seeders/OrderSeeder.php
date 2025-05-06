<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Faker\Factory as Faker;
class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker    = Faker::create();
        $user     = User::first();                  // l'utente creato in DatabaseSeeder
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];

        // Per ciascun cliente creo da 1 a 5 ordini
        \App\Models\Customer::all()->each(function ($customer) use ($faker, $user, $statuses) {
            $ordersCount = rand(1, 5);

            for ($j = 0; $j < $ordersCount; $j++) {
                // Creo l'ordine con total = 0 (aggiorneremo dopo)
                $order = Order::create([
                    'user_id'      => $user->id,
                    'customer_id'  => $customer->id,
                    'order_date'   => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                    'order_code'   => strtoupper($faker->bothify('ORD-#####')),
                    'status'       => $faker->randomElement($statuses),
                    'total'        => 0,
                ]);

                // Associo da 1 a 5 prodotti all'ordine
                $productIds = Product::inRandomOrder()->take(rand(1, 5))->pluck('id');
                $total      = 0;

                foreach ($productIds as $productId) {
                    $product  = Product::find($productId);
                    $quantity = rand(1, 10);
                    $price    = $product->price;
                    $subtotal = $quantity * $price;

                    // Pivot con quantità, prezzo unitario e timestamps
                    $order->products()->attach($productId, [
                        'quantity'   => $quantity,
                        'price'      => $price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $total += $subtotal;
                }

                // Aggiorno il totale
                $order->update(['total' => $total]);
            }
        });
    }
}
