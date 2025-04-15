<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('index', 'IndexCrudController');
    Route::crud('rate', 'RateCrudController');
    Route::crud('user', 'UserCrudController');

    // Dashboard route for showing index statistics
    //Route::get('dashboard', 'AdminController@dashboard')->name('backpack.dashboard');
    Route::crud('plan', 'PlanCrudController');
    Route::crud('subscription', 'SubscriptionCrudController');
    Route::crud('api-key', 'ApiKeyCrudController');
    Route::crud('role', 'RoleCrudController');
    Route::crud('permission', 'PermissionCrudController');

    // Analytics routes
    Route::get('analytics', 'AnalyticsController@index')->name('analytics.index');
    Route::get('analytics/subscriptions', 'AnalyticsController@subscriptions')->name('analytics.subscriptions');
    Route::get('analytics/plan-optimization', 'AnalyticsController@planOptimization')->name('analytics.plan-optimization');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
