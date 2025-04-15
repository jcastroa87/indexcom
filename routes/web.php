<?php

use App\Http\Controllers\Web\IndexController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Homepage route showing all active indices
Route::get('/', [IndexController::class, 'index'])->name('index.home');

// API Documentation route
Route::get('/api-docs', [IndexController::class, 'apiDocs'])->name('api.docs');

// Routes for viewing daily rates and historical graphs
Route::get('/{slug}', [IndexController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('index.show');

// Dashboard routes for authenticated users
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/api-usage', [DashboardController::class, 'apiUsage'])->name('api.usage');
    Route::get('/subscriptions', [DashboardController::class, 'subscriptions'])->name('subscriptions');
});
