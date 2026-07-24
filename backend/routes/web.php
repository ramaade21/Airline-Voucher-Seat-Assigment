<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Airline Voucher Seat Assignment API',
        'docs' => '/api/documentation',
        'health' => '/up',
    ]);
});
