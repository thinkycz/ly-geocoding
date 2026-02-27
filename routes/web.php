<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;

// Map root URL to the CompanyController index
Route::get('/', [CompanyController::class, 'index'])->name('companies.index');

// Import routes (must be before resource routes to avoid {company} catching 'import')
Route::get('/companies/import', [CompanyController::class, 'import'])->name('companies.import');
Route::post('/companies/import', [CompanyController::class, 'processImport'])->name('companies.import.process');
Route::get('/companies/export', [CompanyController::class, 'export'])->name('companies.export');

// Resource routes, excluding 'index' since it's already mapped to root
Route::resource('companies', CompanyController::class)->except(['index']);
