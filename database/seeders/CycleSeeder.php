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
            'name' => 'Postgraduate',
            'slug' => Str::slug('Postgraduate'),
            'description' => 'This is the second cycle of the university',
            'duration' => 2,
        ]);
    }
}
