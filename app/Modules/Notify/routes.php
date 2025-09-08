<?php

use App\Modules\Notify\Http\Controllers\AssetReportValidationController;
use App\Modules\Notify\Http\Controllers\LetterTemplateController;
use App\Modules\Notify\Http\Controllers\NotifyCsvReportController;
use App\Modules\Notify\Http\Controllers\NotifyReportController;
use App\Modules\Notify\Http\Controllers\NotifySimproWebhookController;
use App\Modules\Notify\Http\Controllers\ParsingLogController;
use App\Modules\Notify\Http\Controllers\WarehouseReportController;
use App\Modules\Notify\Http\Controllers\ZeroReportController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth']], function () {
    Route::prefix('/notify')->group(function () {
        Route::get('/letter-templates', ['uses' => LetterTemplateController::class . '@getGroupedLetterTemplates']);
        Route::post('/letter-templates/{name}/upload', ['uses' => LetterTemplateController::class . '@upload']);
        Route::get('/letter-templates/{name}/download', ['uses' => LetterTemplateController::class . '@download']);

        Route::post('/reports/warehouse', ['uses' => WarehouseReportController::class . '@create']);
        Route::post('/csv-reports/zero', ['uses' => ZeroReportController::class . '@create']);

        Route::get('/reports', ['uses' => NotifyReportController::class . '@search']);
        Route::get('/reports/{id}/letters', ['uses' => NotifyReportController::class . '@downloadLettersPdf']);

        Route::get('/csv-reports', ['uses' => NotifyCsvReportController::class . '@search']);
        Route::get('/csv-reports/{id}/download', ['uses' => NotifyCsvReportController::class . '@downloadCsvReport']);

        Route::get('/asset-reports/validation', ['uses' => AssetReportValidationController::class . '@search']);
        Route::get('/asset-reports/validation/search-filters', ['uses' => AssetReportValidationController::class . '@getSearchFilters']);

        Route::get('/parsing-logs', ['uses' => ParsingLogController::class . '@search']);
    });
});

Route::group(['middleware' => ['api', 'guest']], function () {
    Route::prefix('/notify')->group(function () {
        Route::post('/simpro-webhook', ['uses' => NotifySimproWebhookController::class . '@registerWebhook']);
    });
});