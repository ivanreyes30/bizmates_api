<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherForecastController;
use App\Http\Controllers\PlacesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('check_auth')->group(function () {
    Route::prefix('forecast')->group(function () {
        Route::post('/show', [WeatherForecastController::class, 'show'])->name('show_weather');
    });
    Route::prefix('places')->group(function () {
        Route::get('/category_taxonomy', [PlacesController::class, 'category_taxonomy'])->name('category_taxonomy');
        Route::post('/show', [PlacesController::class, 'show'])->name('show_places');
    });
});

