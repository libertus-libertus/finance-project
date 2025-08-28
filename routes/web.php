<?php

use App\Http\Controllers\Report\TrialBalanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Mengubah route default agar menampilkan halaman Chart of Accounts
Route::get('/', function () {
    return view('coa.index');
});

Route::get('/journals', function () {
    return view('journal.index');
});

Route::get('/invoices', function () {
    return view('invoice.index');
});

Route::get('/payments', function () {
    return view('payment.index');
});

Route::get('/reports/trial-balance', function () {
    return view('report.trial_balance');
});

Route::get('/reports/trial-balance/pdf', [TrialBalanceController::class, 'downloadPdf']);
