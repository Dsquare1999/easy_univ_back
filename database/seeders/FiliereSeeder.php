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
            'description' => 'This field focuses on the study of computer systems, programming, and software development.',
        ]);

        Filiere::create([
            'id' => (string) Str::uuid(),
            'name' => 'Business Administration',
            'slug' => Str::slug('Business Administration'),
            'description' => 'This field covers the principles of management, finance, and marketing in a business context.',
        ]);

        Filiere::create([
            'id' => (string) Str::uuid(),
            'name' => 'Mechanical Engineering',
            'slug' => Str::slug('Mechanical Engineering'),
            'description' => 'This field involves the design, analysis, and manufacturing of mechanical systems.',
        ]);

        Filiere::create([
            'id' => (string) Str::uuid(),
            'name' => 'Civil Engineering',
            'slug' => Str::slug('Civil Engineering'),
            'description' => 'This field focuses on the design and construction of infrastructure such as roads, bridges, and buildings.',
        ]);

        Filiere::create([
            'id' => (string) Str::uuid(),
            'name' => 'Electrical Engineering',
            'slug' => Str::slug('Electrical Engineering'),
            'description' => 'This field deals with the study and application of electricity, electronics, and electromagnetism.',
        ]);

        Filiere::create([
            'id' => (string) Str::uuid(),
            'name' => 'Chemical Engineering',
            'slug' => Str::slug('Chemical Engineering'),
            'description' => 'This field focuses on the design and operation of chemical processes for the production of chemicals, materials, and energy.',
        ]);

        Filiere::create([
            'id' => (string) Str::uuid(),
            'name' => 'Architecture',
            'slug' => Str::slug('Architecture'),
            'description' => 'This field involves the art and science of designing buildings and other physical structures.',
        ]);
    }
}
