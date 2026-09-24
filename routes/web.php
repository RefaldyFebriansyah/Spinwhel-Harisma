<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\PublicSpinwheelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Stage / Projector Routes & API
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicSpinwheelController::class, 'index'])->name('stage');
Route::get('/api/categories/{slug}/items', [PublicSpinwheelController::class, 'getItems'])->name('api.items');
Route::post('/api/spin/record', [PublicSpinwheelController::class, 'recordWinner'])->name('api.spin.record');
Route::post('/api/items/{item}/toggle', [PublicSpinwheelController::class, 'toggleItem'])->name('api.items.toggle');
Route::get('/export/print-pdf', [PublicSpinwheelController::class, 'printMasterPdf'])->name('export.print-pdf');

/*
|--------------------------------------------------------------------------
| Admin Management System Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Items CRUD & Bulk Import
    Route::get('/items', [AdminDashboardController::class, 'items'])->name('items.index');
    Route::post('/items', [AdminDashboardController::class, 'storeItem'])->name('items.store');
    Route::put('/items/{item}', [AdminDashboardController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{item}', [AdminDashboardController::class, 'destroyItem'])->name('items.destroy');
    Route::post('/items/bulk-import', [AdminDashboardController::class, 'bulkImport'])->name('items.bulk-import');

    // Engine & Theme Settings
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');

    // History & Export Logs
    Route::get('/history', [AdminDashboardController::class, 'history'])->name('history.index');
    Route::get('/history/export', [AdminDashboardController::class, 'exportHistory'])->name('history.export');
    Route::post('/history/reset', [AdminDashboardController::class, 'resetHistory'])->name('history.reset');
});
