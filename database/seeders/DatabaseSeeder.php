<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\TagSeeder;
use Database\Seeders\CycleSeeder;
use Database\Seeders\FiliereSeeder;
use Database\Seeders\ClasseSeeder;
use Database\Seeders\InvoiceSeeder;
use Database\Seeders\OperationSeeder;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TagSeeder::class,
            CycleSeeder::class,
            FiliereSeeder::class,
            ClasseSeeder::class,
            InvoiceSeeder::class,
            OperationSeeder::class,
        ]);
    }
}
