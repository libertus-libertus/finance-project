<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Report\TrialBalanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Daftar route API untuk resource:
Route::apiResource('chart-of-accounts', ChartOfAccountController::class);
Route::apiResource('journals', JournalController::class)->only(['index', 'show', 'store', 'destroy']);
Route::apiResource('invoices', InvoiceController::class)->only(['index', 'show']);
Route::apiResource('payments', PaymentController::class)->only(['index']);

// Reporting
Route::get('/reports/trial-balance', [TrialBalanceController::class, 'view']);