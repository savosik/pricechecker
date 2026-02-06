<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/price-history/delete-all', [\App\Http\Controllers\PriceHistoryController::class, 'deleteAll'])
    ->name('moonshine.price-history.delete-all')
    ->middleware(['moonshine']);

Route::get('/docs', [\App\Http\Controllers\DocumentationController::class, 'index'])->name('documentation');
