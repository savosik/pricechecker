<?php

use App\Http\Controllers\Api\DomParserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('dom-parser')->group(function () {
    Route::get('/task', [DomParserController::class, 'getTask']);
    Route::post('/result', [DomParserController::class, 'submitResult']);
    Route::get('/status', [DomParserController::class, 'status']);
    Route::post('/reset-stale', [DomParserController::class, 'resetStale']);
});
