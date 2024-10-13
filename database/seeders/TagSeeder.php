<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Tag::create([
                'id' => (string) Str::uuid(),
                'name' => $faker->word,
                'slug' => Str::slug($faker->word),
                'fee' => $faker->randomFloat(2, 10, 100),
            ]);
        }
    }
    
}
