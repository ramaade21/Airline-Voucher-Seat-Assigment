<?php

use App\Http\Controllers\Api\V1\VoucherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Versioned API (v1) as the canonical set of endpoints, plus unversioned
| aliases (/api/check, /api/generate) matching the task specification
| exactly so the frontend can call either.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/check', [VoucherController::class, 'check'])->name('vouchers.check');
    Route::post('/generate', [VoucherController::class, 'generate'])->name('vouchers.generate');
});

// Unversioned aliases matching the spec's exact contract:
// POST /api/check and POST /api/generate
Route::post('/check', [VoucherController::class, 'check'])->name('api.vouchers.check');
Route::post('/generate', [VoucherController::class, 'generate'])->name('api.vouchers.generate');
