<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Creo 20 prodotti
        for ($i = 0; $i < 20; $i++) {
            Product::create([
                'name'        => ucfirst($faker->word()),
                'description' => $faker->sentence(6),
                'price'       => $faker->randomFloat(2, 5, 200),
            ]);
        }
    }
}
