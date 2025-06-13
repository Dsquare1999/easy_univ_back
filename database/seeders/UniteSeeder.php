<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Unite;
use Illuminate\Support\Str;

class UniteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Unite::create([
            'id' => (string) Str::uuid(),
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'slug' => Str::slug('Mathematics'),
            'description' => 'This unit covers basic and advanced mathematical concepts.',
        ]);

        Unite::create([
            'id' => (string) Str::uuid(),
            'name' => 'Physics',
            'code' => 'PHYS101',
            'slug' => Str::slug('Physics'),
            'description' => 'This unit explores the fundamental principles of physics.',
        ]);

        Unite::create([
            'id' => (string) Str::uuid(),
            'name' => 'Chemistry',
            'code' => 'CHEM101',
            'slug' => Str::slug('Chemistry'),
            'description' => 'This unit introduces the basic concepts of chemistry.',
        ]);

        Unite::create([
            'id' => (string) Str::uuid(),
            'name' => 'Biology',
            'code' => 'BIOL101',
            'slug' => Str::slug('Biology'),
            'description' => 'This unit covers the fundamental concepts of biology.',
        ]);
    }
}
