<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
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

        Route::middleware('dokkit.team.api.key')->group(function () {
            Route::post('update-token', [App\Http\Controllers\DokkitExtension\DokkitExtensionController::class, 'updateToken']);


            Route::group(['prefix' => 'data'], function () {
                Route::get('dashboard', ['uses' => UserController::class . '@dashboard']);
                Route::get('jobs', ['uses' => JobController::class . '@search']);


                Route::group(['prefix' => 'jobs'], function () {
                    Route::get('/', ['uses' => JobController::class . '@search']);
                    Route::get('cost-centers', ['uses' => JobController::class . '@getCostCenters']);
                });

                Route::group(['prefix' => 'sites'], function () {
                    Route::get('/', ['uses' => SiteController::class . '@search']);
                });

                Route::group(['prefix' => 'customers'], function () {
                    Route::get('/', ['uses' => CustomerController::class . '@search']);
                });

                Route::group(['prefix' => 'assets'], function () {
                    Route::get('/', ['uses' => AssetController::class . '@search']);
                });
            });
        });
    });
});

