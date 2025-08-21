<?php

use App\Modules\Notify\Http\Controllers\LetterTemplateController;
use App\Modules\Notify\Http\Controllers\NotifyReportController;
use App\Modules\Notify\Http\Controllers\WarehouseReportController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth']], function () {
    Route::prefix('/notify')->group(function () {
        Route::get('/letter-templates', ['uses' => LetterTemplateController::class . '@getGroupedLetterTemplates']);
        Route::post('/letter-templates/{name}/upload', ['uses' => LetterTemplateController::class . '@upload']);
        Route::get('/letter-templates/{name}/download', ['uses' => LetterTemplateController::class . '@download']);

        Route::post('/reports/warehouse', ['uses' => WarehouseReportController::class . '@create']);

        Route::get('/reports', ['uses' => NotifyReportController::class . '@search']);
        Route::get('/reports/{id}/letters', ['uses' => NotifyReportController::class . '@downloadLettersPdf']);
    });
});