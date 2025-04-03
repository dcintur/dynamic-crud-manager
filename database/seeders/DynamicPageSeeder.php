<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DynamicPage;

class DynamicPageSeeder extends Seeder
{
    public function run()
    {
        $pages = [
            [
                'name' => 'Clienti',
                'slug' => 'clienti',
                'icon' => 'bi bi-people',
                'menu_group' => 'Business',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Prodotti',
                'slug' => 'prodotti',
                'icon' => 'bi bi-box',
                'menu_group' => 'Business',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Ordini',
                'slug' => 'ordini',
                'icon' => 'bi bi-cart',
                'menu_group' => 'Business',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Dipendenti',
                'slug' => 'dipendenti',
                'icon' => 'bi bi-person-badge',
                'menu_group' => 'HR',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Progetti',
                'slug' => 'progetti',
                'icon' => 'bi bi-kanban',
                'menu_group' => 'Business',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            DynamicPage::create($page);
        }
    }
}