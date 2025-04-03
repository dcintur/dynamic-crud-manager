<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\DynamicPage;

class DynamicPagesServiceProvider extends ServiceProvider
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
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            // Raggruppa le pagine per menu_group
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
            
            $view->with('menuGroups', $menuGroups);
        });
    }
}