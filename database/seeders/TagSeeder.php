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

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Inscription',
            'slug' => 'inscription',
            'fee' => $faker->randomFloat(2, 10000, 100000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Frais de formation',
            'slug' => 'frais-de-formation',
            'fee' => $faker->randomFloat(2, 100000, 500000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Attestation de réussite',
            'slug' => 'attestation-de-reussite',
            'fee' => $faker->randomFloat(2, 5000, 15000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Rattrapage',
            'slug' => 'rattrapage',
            'fee' => $faker->randomFloat(2, 5000, 25000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Relevés de notes',
            'slug' => 'releves-de-notes',
            'fee' => $faker->randomFloat(2, 2000, 10000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Uniformes',
            'slug' => 'uniformes',
            'fee' => $faker->randomFloat(2, 15000, 50000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Carte d\'étudiant',
            'slug' => 'carte-etudiant',
            'fee' => $faker->randomFloat(2, 3000, 8000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Frais de bibliothèque',
            'slug' => 'frais-de-bibliotheque',
            'fee' => $faker->randomFloat(2, 5000, 20000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Frais de TD',
            'slug' => 'frais-de-td',
            'fee' => $faker->randomFloat(2, 10000, 30000),
        ]);

        Tag::create([
            'id' => (string) Str::uuid(),
            'name' => 'Frais de TP',
            'slug' => 'frais-de-tp',
            'fee' => $faker->randomFloat(2, 15000, 40000),
        ]);
    }
}
