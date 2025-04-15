<?php

use App\Http\Controllers\Api\IndexController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API routes for authenticated users
Route::middleware('api.key')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Routes that require an active subscription
    Route::middleware('subscription')->group(function () {
        // Extended data API endpoints - premium feature
        Route::get('/indices/{slug}/rates/extended', [IndexController::class, 'extendedRates']);

        // Additional premium endpoints can be added here
        Route::get('/indices/{slug}/analysis', [IndexController::class, 'analysis']);
        Route::get('/indices/compare', [IndexController::class, 'compare']);
    });

    // Subscription management endpoints
    Route::get('/plans', 'App\Http\Controllers\Api\PlanController@index');
    Route::get('/plans/{id}', 'App\Http\Controllers\Api\PlanController@show');
    Route::get('/subscription', 'App\Http\Controllers\Api\SubscriptionController@current');
    Route::post('/subscription', 'App\Http\Controllers\Api\SubscriptionController@subscribe');
    Route::delete('/subscription', 'App\Http\Controllers\Api\SubscriptionController@cancel');
});

// Public API routes - basic access
Route::get('/indices', [IndexController::class, 'index']);
Route::get('/indices/{slug}', [IndexController::class, 'show']);
Route::get('/indices/{slug}/rates', [IndexController::class, 'rates']);
Route::get('/indices/{slug}/latest', [IndexController::class, 'latest']);
Route::get('/indices/{slug}/latest', [IndexController::class, 'latest']);
