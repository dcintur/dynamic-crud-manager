<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\DynamicPage;
use Illuminate\Support\Facades\Config;

class DynamicMenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Esegui questo processo dopo che le route sono state caricate
        $this->app->booted(function () {
            // Ottieni le pagine attive
            $dynamicPages = DynamicPage::where('is_active', true)
                ->orderBy('menu_group')
                ->orderBy('order')
                ->get();

            // Raggruppa le pagine per menu_group
            $menuGroups = $dynamicPages->groupBy('menu_group');
            
            // Ottieni il menu attuale di AdminLTE
            $menu = Config::get('adminlte.menu', []);
            
            // Aggiungi un separatore prima delle pagine dinamiche se ci sono altre voci di menu
            if (count($menu) > 0) {
                $menu[] = ['header' => 'PAGINE DINAMICHE'];
            }
            
            // Aggiungi gruppi di menu e pagine
            foreach ($menuGroups as $groupName => $pages) {
                // Se il gruppo è vuoto, salta
                if (empty($groupName)) {
                    // Aggiungi pagine senza gruppo direttamente al menu
                    foreach ($pages as $page) {
                        $menu[] = [
                            'text' => $page->name,
                            'url' => '/dynamic-data/page/' . $page->id,
                            'icon' => $page->icon ?: 'fas fa-table',
                        ];
                    }
                    continue;
                }
                
                // Crea un submenu per il gruppo
                $submenu = [];
                foreach ($pages as $page) {
                    $submenu[] = [
                        'text' => $page->name,
                        'url' => '/dynamic-data/page/' . $page->id,
                        'icon' => $page->icon ?: 'fas fa-table',
                    ];
                }
                
                // Aggiungi il gruppo al menu
                $menu[] = [
                    'text' => $groupName,
                    'icon' => 'fas fa-folder',
                    'submenu' => $submenu,
                ];
            }
            
            // Aggiorna la configurazione del menu
            Config::set('adminlte.menu', $menu);
        });
    }
}