<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DynamicField;
use App\Models\DynamicPage;

class DynamicFieldSeeder extends Seeder
{
    public function run()
    {
        // Clienti
        $clientiPage = DynamicPage::where('slug', 'clienti')->first();
        if ($clientiPage) {
            $fields = [
                [
                    'dynamic_page_id' => $clientiPage->id,
                    'name' => 'nome',
                    'label' => 'Nome',
                    'type' => 'text',
                    'is_required' => true,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 1
                ],
                [
                    'dynamic_page_id' => $clientiPage->id,
                    'name' => 'cognome',
                    'label' => 'Cognome',
                    'type' => 'text',
                    'is_required' => true,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 2
                ],
                [
                    'dynamic_page_id' => $clientiPage->id,
                    'name' => 'email',
                    'label' => 'Email',
                    'type' => 'email',
                    'is_required' => true,
                    'is_unique' => true,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 3
                ],
                [
                    'dynamic_page_id' => $clientiPage->id,
                    'name' => 'telefono',
                    'label' => 'Telefono',
                    'type' => 'tel',
                    'is_required' => false,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 4
                ],
                [
                    'dynamic_page_id' => $clientiPage->id,
                    'name' => 'azienda',
                    'label' => 'Azienda',
                    'type' => 'text',
                    'is_required' => false,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 5
                ],
                [
                    'dynamic_page_id' => $clientiPage->id,
                    'name' => 'e_attivo',
                    'label' => 'È attivo',
                    'type' => 'checkbox',
                    'is_required' => false,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 6
                ]
            ];

            foreach ($fields as $field) {
                DynamicField::create($field);
            }
        }

        // Prodotti
        $prodottiPage = DynamicPage::where('slug', 'prodotti')->first();
        if ($prodottiPage) {
            $fields = [
                [
                    'dynamic_page_id' => $prodottiPage->id,
                    'name' => 'nome',
                    'label' => 'Nome',
                    'type' => 'text',
                    'is_required' => true,
                    'is_unique' => true,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 1
                ],
                [
                    'dynamic_page_id' => $prodottiPage->id,
                    'name' => 'descrizione',
                    'label' => 'Descrizione',
                    'type' => 'textarea',
                    'is_required' => false,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => false,
                    'is_visible' => true,
                    'order' => 2
                ],
                [
                    'dynamic_page_id' => $prodottiPage->id,
                    'name' => 'prezzo',
                    'label' => 'Prezzo',
                    'type' => 'number',
                    'is_required' => true,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 3
                ],
                [
                    'dynamic_page_id' => $prodottiPage->id,
                    'name' => 'categoria',
                    'label' => 'Categoria',
                    'type' => 'select',
                    'is_required' => true,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'options' => json_encode(['Elettronica', 'Abbigliamento', 'Casa', 'Cibo', 'Altro']),
                    'order' => 4
                ],
                [
                    'dynamic_page_id' => $prodottiPage->id,
                    'name' => 'disponibile',
                    'label' => 'Disponibile',
                    'type' => 'checkbox',
                    'is_required' => false,
                    'is_unique' => false,
                    'is_searchable' => true,
                    'is_sortable' => true,
                    'is_visible' => true,
                    'order' => 5
                ]
            ];

            foreach ($fields as $field) {
                DynamicField::create($field);
            }
        }

        // Aggiungere altri campi per le altre pagine...
    }
}