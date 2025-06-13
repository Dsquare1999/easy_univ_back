<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Operation;
use App\Models\Tag;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class OperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 5; $i++) {
            Operation::create([
                'id' => (string) Str::uuid(),
                'tag' => Tag::inRandomOrder()->first()->id,
                'invoice' => Invoice::inRandomOrder()->first()->id,
                'type' => $faker->randomElement(['in', 'out']),
                'montant' => $faker->randomFloat(2, 100, 1000),
                'date' => $faker->date(),
            ]);
        }
    }
}
