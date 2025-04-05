<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\DynamicPage;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Aggiungi dinamicamente le pagine al menu di AdminLTE
        $this->addDynamicPagesToMenu();

        // Altro codice di boot...
    }

    protected function addDynamicPagesToMenu()
    {
        try {
            // Verificare se la tabella esiste (utile durante le migrazioni iniziali)
            if (!\Schema::hasTable('dynamic_pages')) {
                return;
            }

            $pages = DynamicPage::where('is_active', true)
                ->orderBy('menu_group')
                ->orderBy('order')
                ->get();

            $menuGroups = [];
            foreach ($pages as $page) {
                $group = $page->menu_group ?? 'Main';
                if (!isset($menuGroups[$group])) {
                    $menuGroups[$group] = [];
                }
                $menuGroups[$group][] = $page;
            }

            $dynamicPagesMenu = [];
            foreach ($menuGroups as $groupName => $pages) {
                if (count($pages) > 1) {
                    $submenuItems = [];
                    foreach ($pages as $page) {
                        $submenuItems[] = [
                            'text' => $page->name,
                            'url'  => 'dynamic-data/page/'.$page->id,
                            'icon' => $page->icon ?: 'fas fa-file-alt',
                        ];
                    }
                    $dynamicPagesMenu[] = [
                        'text'    => $groupName,
                        'icon'    => 'fas fa-folder',
                        'submenu' => $submenuItems,
                    ];
                } else {
                    foreach ($pages as $page) {
                        $dynamicPagesMenu[] = [
                            'text' => $page->name,
                            'url'  => 'dynamic-data/page/'.$page->id,
                            'icon' => $page->icon ?: 'fas fa-file-alt',
                        ];
                    }
                }
            }

            // Ottieni la configurazione corrente del menu
            $menu = Config::get('adminlte.menu', []);

            // Trova l'indice del gruppo "Pagine Dinamiche"
            $dynamicPageIndex = null;
            foreach ($menu as $index => $item) {
                if (isset($item['text']) && $item['text'] === 'Pagine Dinamiche') {
                    $dynamicPageIndex = $index;
                    break;
                }
            }

            // Se esiste, aggiorna il submenu
            if ($dynamicPageIndex !== null) {
                $menu[$dynamicPageIndex]['submenu'] = $dynamicPagesMenu;
            }

            // Aggiorna la configurazione
            Config::set('adminlte.menu', $menu);
        } catch (\Exception $e) {
            // Log l'errore ma non far fallire l'applicazione
            \Log::error('Errore nel caricamento dinamico del menu: '.$e->getMessage());
        }
    }
}