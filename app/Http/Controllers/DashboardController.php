<?php

namespace App\Http\Controllers;

use App\Models\DynamicPage;
use App\Models\DynamicData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiche di base
        $stats = [
            'total_pages' => DynamicPage::count(),
            'total_records' => DynamicData::count(),
            'total_users' => User::count(),
            'active_pages' => DynamicPage::where('is_active', true)->count(),
        ];
        
        // Pagine più popolate
        $popularPages = DynamicPage::withCount('data')
            ->orderBy('data_count', 'desc')
            ->take(5)
            ->get();
            
        // Attività recenti
        $recentActivity = DynamicData::with('page')
            ->latest()
            ->take(10)
            ->get();
            
        // Grafico distribuzione dei dati
        $dataDistribution = DynamicPage::select('name')
            ->withCount('data')
            ->get()
            ->map(function ($page) {
                return [
                    'label' => $page->name,
                    'value' => $page->data_count
                ];
            });
            
        return view('dashboard', compact('stats', 'popularPages', 'recentActivity', 'dataDistribution'));
    }
}