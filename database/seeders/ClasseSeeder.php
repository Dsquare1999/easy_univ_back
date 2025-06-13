<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Cycle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        Classe::create([
            'filiere' => Filiere::first()->id,  // Assign first Filiere
            'cycle' => Cycle::first()->id,      // Assign first Cycle
            'year' => $faker->numberBetween(1, 3),
            'academic_year' => $faker->year,
        ]);
    }
}
