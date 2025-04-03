<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // In app/Providers/AppServiceProvider.php

    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $pages = \App\Models\DynamicPage::where('is_active', true)
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
