<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('inventory.index');
    }

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    $totalValue = \App\Models\Inventory::selectRaw('SUM(price * quantity) as total')
        ->value('total') ?? 0;

    $inventoryStats = [
        'total_items' => \App\Models\Inventory::count(),
        'active_items' => \App\Models\Inventory::where('is_active', true)->count(),
        'total_value' => (float) $totalValue,
        'low_stock_items' => \App\Models\Inventory::where('quantity', '<', 10)->count(),
    ];

    $recentItems = \App\Models\Inventory::latest()->limit(10)->get();

    return Inertia::render('Dashboard', [
        'inventoryStats' => $inventoryStats,
        'recentItems' => $recentItems,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('inventory', App\Http\Controllers\InventoryController::class);
});

require __DIR__.'/settings.php';
