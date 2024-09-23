<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Invoice;
use App\Models\Tag;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Invoice::create([
                'id' => (string) Str::uuid(),
                'tag' => Tag::inRandomOrder()->first()->id,
                'classe' => Classe::inRandomOrder()->first()->id,
                'description' => $faker->sentence,
                'amount' => $amount = $faker->randomFloat(2, 100, 1000),
                'remain' => $amount - 100,
                'total' => $amount,
                'fee' => 100,
                'file' => $faker->filePath(),
                'user' => User::inRandomOrder()->first()->id,
            ]);
        }
    }
}
