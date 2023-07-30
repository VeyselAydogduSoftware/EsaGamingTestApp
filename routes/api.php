<?php

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

Route::group(['middleware' => ['api.public']], function () {

    Route::group(['name' => 'Auth', 'middleware' => 'guest'], function () {

        Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
        Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

    });

    Route::apiResource('currencies', \App\Http\Controllers\Currencies\CurrenciesController::class);

    Route::apiResource('currency-values', \App\Http\Controllers\Currencies\CurrencyValuesController::class)
        ->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);

    Route::post('currency-calculator', [\App\Http\Controllers\Calculator\ExchangeController::class, 'index']);

    //daha complex yapılarda resource kullanmadan rotalar genişletilebilir

});


