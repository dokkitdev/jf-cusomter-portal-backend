<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthenticationCodeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\JobAttachmentController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\SimproWebhookController;
use App\Http\Controllers\SiteContactController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SettingController;

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

Route::group(['middleware' => 'auth'], function () {
    Route::post('/users/{id}/resend-invitation', ['uses' => UserController::class . '@resendInvitation']);
    Route::post('/users', ['uses' => UserController::class . '@create']);
    Route::put('/users/{id}', ['uses' => UserController::class . '@update']);
    Route::delete('/users/{id}', ['uses' => UserController::class . '@delete']);
    Route::get('/users/{id}', ['uses' => UserController::class . '@get']);
    Route::get('/users', ['uses' => UserController::class . '@search']);
    Route::get('/profile', ['uses' => UserController::class . '@profile']);
    Route::put('/profile', ['uses' => UserController::class . '@updateProfile']);

    Route::get('/dashboard', ['uses' => UserController::class . '@dashboard']);

    Route::post('/media', ['uses' => MediaController::class . '@create']);
    Route::delete('/media/{id}', ['uses' => MediaController::class . '@delete']);
    Route::get('/media/{id}/download', ['uses' => MediaController::class . '@download']);
    Route::get('/media', ['uses' => MediaController::class . '@search']);

    Route::put('/settings/{name}', ['uses' => SettingController::class . '@update']);
    Route::get('/settings/{name}', ['uses' => SettingController::class . '@get']);
    Route::get('/settings', ['uses' => SettingController::class . '@search']);

    Route::get('/customers/{id}', ['uses' => CustomerController::class . '@get']);
    Route::get('/customers', ['uses' => CustomerController::class . '@search']);

    Route::post('/documents', ['uses' => DocumentController::class . '@create']);
    Route::put('/documents/{id}', ['uses' => DocumentController::class . '@update']);
    Route::delete('/documents/{id}', ['uses' => DocumentController::class . '@delete']);
    Route::get('/documents/{id}', ['uses' => DocumentController::class . '@get']);
    Route::get('/documents', ['uses' => DocumentController::class . '@search']);

    Route::put('/sites/{id}', ['uses' => SiteController::class . '@update']);
    Route::get('/sites/export', ['uses' => SiteController::class . '@export']);
    Route::get('/sites/{id}', ['uses' => SiteController::class . '@get']);
    Route::get('/sites', ['uses' => SiteController::class . '@search']);

    Route::post('/site-contacts', ['uses' => SiteContactController::class . '@create']);
    Route::put('/site-contacts/{id}', ['uses' => SiteContactController::class . '@update']);
    Route::delete('/site-contacts/{id}', ['uses' => SiteContactController::class . '@delete']);
    Route::get('/site-contacts/{id}', ['uses' => SiteContactController::class . '@get']);

    Route::post('/jobs/create-in-simpro', ['uses' => JobController::class . '@createInSimpro']);
    Route::get('/jobs/cost-centers', ['uses' => JobController::class . '@getCostCenters']);
    Route::get('/jobs/statuses', ['uses' => JobController::class . '@getStatuses']);
    Route::get('/jobs/report/export', ['uses' => JobController::class . '@exportReport']);
    Route::get('/jobs/export', ['uses' => JobController::class . '@export']);
    Route::get('/jobs/{id}', ['uses' => JobController::class . '@get']);
    Route::get('/jobs', ['uses' => JobController::class . '@search']);

    Route::get('/job-attachments/download/{id}', ['uses' => JobAttachmentController::class . '@download']);

    Route::get('/assets/service-levels', ['uses' => AssetController::class . '@getServiceLevels']);
    Route::get('/assets/types', ['uses' => AssetController::class . '@getTypes']);
    Route::get('/assets/names', ['uses' => AssetController::class . '@getNames']);
    Route::get('/assets/report/export', ['uses' => AssetController::class . '@exportReport']);
    Route::get('/assets/export', ['uses' => AssetController::class . '@export']);
    Route::get('/assets/{id}', ['uses' => AssetController::class . '@get']);
    Route::get('/assets', ['uses' => AssetController::class . '@search']);

    Route::get('/asset-attachments/{id}/download', ['uses' => AssetController::class . '@download']);
});

Route::group(['middleware' => 'guest'], function () {
    Route::post('/login', ['uses' => AuthController::class . '@login']);
    Route::get('/auth/refresh', ['uses' => AuthController::class . '@refreshToken']);
    Route::post('/auth/logout', ['uses' => AuthController::class . '@logout']);
    Route::post('/auth/forgot-password', ['uses' => AuthController::class . '@forgotPassword']);
    Route::post('/auth/restore-password', ['uses' => AuthController::class . '@restorePassword']);
    Route::post('/auth/token/check', ['uses' => AuthController::class . '@checkRestoreToken']);
    Route::post('/auth/2fa-codes', ['uses' => AuthenticationCodeController::class . '@send']);

    Route::get('/status', ['uses' => StatusController::class . '@status']);

    Route::post('/simpro-webhook', ['uses' => SimproWebhookController::class . '@process']);
});
