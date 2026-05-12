<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth (Sanctum SPA session-based)
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Current user
    Route::get('/user', fn (Request $request) => $request->user());

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Register (owner only)
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('role:owner');

    // Dashboard
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);

    // CRUD Resources
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('payments', PaymentController::class)->except(['create', 'edit']);
});
