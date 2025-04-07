<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DynamicPageController;
use App\Http\Controllers\DynamicFieldController;
use App\Http\Controllers\DynamicDataController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\CsvImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home');

// Proteggi le rotte con middleware auth
    Route::middleware(['auth'])->group(function () {

    // Modifica le route per bypassare temporaneamente il middleware di permessi
    // Route di gestione ruoli e permessi senza controlli
    //Route::get('roles', [UserManagementController::class, 'roles'])->name('roles.index');
    //Route::get('roles/create', [UserManagementController::class, 'createRole'])->name('roles.create');
    //Route::post('roles', [UserManagementController::class, 'storeRole'])->name('roles.store');

    // csv iMPORT
    Route::get('csv-import', [CsvImportController::class, 'showImportForm'])->name('csv-import.form');
    Route::post('csv-import', [CsvImportController::class, 'processImport'])->name('csv-import.process');

    // Dynamic Pages
    Route::resource('dynamic-pages', DynamicPageController::class);
   
    // Dynamic Fields
    Route::get('dynamic-fields/create/{page_id}', [DynamicFieldController::class, 'create'])->name('dynamic-fields.create');
    Route::resource('dynamic-fields', DynamicFieldController::class)->except(['create']);
   
    // Dynamic Data with Permission Middleware
    Route::get('dynamic-data/page/{page}', [DynamicDataController::class, 'index'])
        ->name('dynamic-data.page')
        ->middleware('permission:view');
        
    Route::get('dynamic-data/page/{page}/create', [DynamicDataController::class, 'create'])
        ->name('dynamic-data.create')
        ->middleware('permission:create');
        
    // Dynamic Data operations with permission middleware
    Route::post('dynamic-data', [DynamicDataController::class, 'store'])
        ->name('dynamic-data.store')
        ->middleware('permission:create');
        
    Route::get('dynamic-data/{dynamicData}/edit', [DynamicDataController::class, 'edit'])
        ->name('dynamic-data.edit')
        ->middleware('permission:edit');
        
    Route::put('dynamic-data/{dynamicData}', [DynamicDataController::class, 'update'])
        ->name('dynamic-data.update')
        ->middleware('permission:edit');
        
    Route::delete('dynamic-data/{dynamicData}', [DynamicDataController::class, 'destroy'])
        ->name('dynamic-data.destroy')
        ->middleware('permission:delete');
        
    Route::get('dynamic-data/{dynamicData}', [DynamicDataController::class, 'show'])
        ->name('dynamic-data.show')
        ->middleware('permission:view');
   
    // Export/Import with Permission Middleware
    Route::get('dynamic-data/export/{page}', [DynamicDataController::class, 'export'])
        ->name('dynamic-data.export')
        ->middleware('permission:export');
        
    Route::post('dynamic-data/import/{page}', [DynamicDataController::class, 'import'])
        ->name('dynamic-data.import')
        ->middleware('permission:import');
    
    // User Management
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Roles Management
    Route::get('roles', [UserManagementController::class, 'roles'])->name('roles.index');
    Route::get('roles/create', [UserManagementController::class, 'createRole'])->name('roles.create');
    Route::post('roles', [UserManagementController::class, 'storeRole'])->name('roles.store');
    Route::get('roles/{role}/edit', [UserManagementController::class, 'editRole'])->name('roles.edit');
    Route::put('roles/{role}', [UserManagementController::class, 'updateRole'])->name('roles.update');
    Route::delete('roles/{role}', [UserManagementController::class, 'destroyRole'])->name('roles.destroy');

    // Permissions Management
    Route::get('roles/{role}/permissions', [UserManagementController::class, 'permissions'])->name('roles.permissions');
    Route::post('roles/{role}/permissions', [UserManagementController::class, 'updatePermissions'])->name('roles.permissions.update');

    Route::get('export-routes', function() {
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename="routes.csv"');
        
        $routes = Route::getRoutes();
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['METHOD', 'URI', 'NAME', 'ACTION']);
        
        foreach ($routes as $route) {
            fputcsv($fp, [
                implode('|', $route->methods()), 
                $route->uri(), 
                $route->getName(), 
                $route->getActionName()
            ]);
        }
        
        fclose($fp);
        exit;
    });


});