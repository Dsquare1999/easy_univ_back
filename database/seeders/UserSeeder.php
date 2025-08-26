<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        User::create([
            'firstname' => $faker->firstName,
            'lastname' => $faker->lastName,
            'email' => $faker->unique()->safeEmail,
            'matricule' => $faker->unique()->numerify('MATRICULE-####'),
            'password' => Hash::make('password'),
            'type' => $faker->boolean
        ]);

        User::create([
            'firstname' => $faker->firstName,
            'lastname' => $faker->lastName,
            'email' => $faker->unique()->safeEmail,
            'matricule' => $faker->unique()->numerify('MATRICULE-####'),
            'password' => Hash::make('password'),
            'type' => $faker->boolean
        ]);

        User::create([
            'firstname' => $faker->firstName,
            'lastname' => $faker->lastName,
            'email' => $faker->unique()->safeEmail,
            'matricule' => $faker->unique()->numerify('MATRICULE-####'),
            'password' => Hash::make('password'),
            'type' => $faker->boolean
        ]);

        User::create([
            'firstname' => $faker->firstName,
            'lastname' => $faker->lastName,
            'email' => $faker->unique()->safeEmail,
            'matricule' => $faker->unique()->numerify('MATRICULE-####'),
            'password' => Hash::make('password'),
            'type' => $faker->boolean
        ]);

        User::create([
            'firstname' => $faker->firstName,
            'lastname' => $faker->lastName,
            'email' => $faker->unique()->safeEmail,
            'matricule' => $faker->unique()->numerify('MATRICULE-####'),
            'password' => Hash::make('password'),
            'type' => $faker->boolean
        ]);
    }
}
