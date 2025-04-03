<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DynamicData;
use App\Models\DynamicPage;
use Faker\Factory as Faker;

class DynamicDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('it_IT');
        
        // Clienti
        $clientiPage = DynamicPage::where('slug', 'clienti')->first();
        if ($clientiPage) {
            for ($i = 0; $i < 20; $i++) {
                DynamicData::create([
                    'dynamic_page_id' => $clientiPage->id,
                    'data' => [
                        'nome' => $faker->firstName(),
                        'cognome' => $faker->lastName(),
                        'email' => $faker->unique()->safeEmail(),
                        'telefono' => $faker->phoneNumber(),
                        'azienda' => $faker->company(),
                        'e_attivo' => $faker->boolean(80)
                    ]
                ]);
            }
        }
        
        // Prodotti
        $prodottiPage = DynamicPage::where('slug', 'prodotti')->first();
        if ($prodottiPage) {
            $categorie = ['Elettronica', 'Abbigliamento', 'Casa', 'Cibo', 'Altro'];
            
            for ($i = 0; $i < 30; $i++) {
                DynamicData::create([
                    'dynamic_page_id' => $prodottiPage->id,
                    'data' => [
                        'nome' => $faker->words(3, true),
                        'descrizione' => $faker->paragraph(),
                        'prezzo' => $faker->randomFloat(2, 10, 1000),
                        'categoria' => $faker->randomElement($categorie),
                        'disponibile' => $faker->boolean(70)
                    ]
                ]);
            }
        }
        
        // Aggiungere altri dati per le altre pagine...
    }
}