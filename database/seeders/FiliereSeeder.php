<?php

namespace Database\Seeders;

use App\Models\Filiere;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Filiere::create([
            'id' => (string) Str::uuid(),
            'name' => 'Computer Science',
            'slug' => Str::slug('Computer Science'),
        ]);

        Filiere::create([
            'id' => (string) Str::uuid(),
            'name' => 'Business Administration',
            'slug' => Str::slug('Business Administration'),
        ]);
    }
}
