<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUnlockController;
use App\Http\Controllers\CompanyController;

Route::get('/', [CompanyController::class, 'index'])->name('companies.index');

Route::get('/admin/unlock', [AdminUnlockController::class, 'create'])->name('admin.unlock');
Route::post('/admin/unlock', [AdminUnlockController::class, 'store'])->name('admin.unlock.store');
Route::post('/admin/lock', [AdminUnlockController::class, 'destroy'])->name('admin.lock');

Route::middleware('admin.unlocked')->group(function () {
    Route::get('/companies/import', [CompanyController::class, 'import'])->name('companies.import');
    Route::post('/companies/import', [CompanyController::class, 'processImport'])->name('companies.import.process');
    Route::get('/companies/export', [CompanyController::class, 'export'])->name('companies.export');
    Route::resource('companies', CompanyController::class)->except(['index', 'show']);
});

Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
