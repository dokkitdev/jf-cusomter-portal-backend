<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Microservices Routes
|--------------------------------------------------------------------------
|
| Here is where you can register microservices routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group and "dokkit.api.key" middleware.
|
*/


Route::middleware('dokkit.api.key')->group(function () {
    Route::group(['prefix' => 'dokkit-extension'], function () {
        Route::get('status', [App\Http\Controllers\DokkitExtension\DokkitExtensionController::class, 'status']);

        Route::post('add-team', [App\Http\Controllers\DokkitExtension\DokkitExtensionController::class, 'addTeam']);
        Route::post('update-token', [App\Http\Controllers\DokkitExtension\DokkitExtensionController::class, 'updateToken']);
    });
});

