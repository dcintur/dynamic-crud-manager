<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class, // Se già esiste
            DynamicPageSeeder::class,
            DynamicFieldSeeder::class,
            DynamicDataSeeder::class,
        ]);
    }
}