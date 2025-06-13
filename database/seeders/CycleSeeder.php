<?php

namespace Database\Seeders;

use App\Models\Cycle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cycle::create([
            'id' => (string) Str::uuid(),
            'name' => 'Undergraduate',
            'slug' => Str::slug('Undergraduate'),
            'description' => 'This is the first cycle of the university',
            'duration' => 3,
        ]);

        Cycle::create([
            'id' => (string) Str::uuid(),
            'name' => 'Master',
            'slug' => Str::slug('Master'),
            'description' => 'This is the fifth cycle of the university',
            'duration' => 2,
        ]);

        Cycle::create([
            'id' => (string) Str::uuid(),
            'name' => 'Engeneering',
            'slug' => Str::slug('Engeneering'),
            'description' => 'This is the third cycle of the university',
            'duration' => 5,
        ]);

        Cycle::create([
            'id' => (string) Str::uuid(),
            'name' => 'Doctorate',
            'slug' => Str::slug('Doctorate'),
            'description' => 'This is the fourth cycle of the university',
            'duration' => 3,
        ]);


        Cycle::create([
            'id' => (string) Str::uuid(),
            'name' => 'PhD',
            'slug' => Str::slug('PhD'),
            'description' => 'This is the sixth cycle of the university',
            'duration' => 4,
        ]);


    }
}
