<?php

namespace App\Modules\Notify\Services\SimproWebhooks;

use App\Modules\Notify\DB\Models\NotifySimproWebhook;
use App\Modules\Notify\DB\Services\NotifySimproWebhookService;
use App\Modules\Notify\Services\AssetReport\AssetReportGenerator;

class ProcessSimproWebhookAction
{
    protected const WEBHOOK_TYPE_ASSET_CREATED = 'asset.created';
    protected const WEBHOOK_TYPE_ASSET_UPDATED = 'asset.updated';
    protected const WEBHOOK_TYPE_ASSET_DELETED = 'asset.deleted';

    protected NotifySimproWebhookService $notifySimproWebhookService;
    protected AssetReportGenerator $assetReportGenerator;

    public function __construct()
    {
        $this->notifySimproWebhookService = app(NotifySimproWebhookService::class);
        $this->assetReportGenerator = app(AssetReportGenerator::class);
    }

    public function processSimproWebhook(int $simproWebhookId): void
    {
        $this->notifySimproWebhookService->update($simproWebhookId, [
            'status' => NotifySimproWebhook::STATUS_PROCESSING,
        ]);

        /** @var NotifySimproWebhook $simproWebhook */
        $simproWebhook = $this->notifySimproWebhookService->find($simproWebhookId);
        $this->processWebhookData($simproWebhook->data);

        $this->notifySimproWebhookService->update($simproWebhookId, [
            'status' => NotifySimproWebhook::STATUS_SUCCESS,
        ]);
    }

    public function simproWebhookProcessingFailed(int $simproWebhookId): void
    {
        $this->notifySimproWebhookService->update($simproWebhookId, [
            'status' => NotifySimproWebhook::STATUS_FAILED,
        ]);
    }

    protected function processWebhookData(array $webhookData): void
    {
        switch ($webhookData['ID']) {
            case self::WEBHOOK_TYPE_ASSET_CREATED:
            case self::WEBHOOK_TYPE_ASSET_UPDATED:
                $this->assetReportGenerator->createOrUpdateAssetReport($webhookData['reference']['siteID'], $webhookData['reference']['assetID']);
                break;
            case self::WEBHOOK_TYPE_ASSET_DELETED:
                $this->assetReportGenerator->deleteAssetReport($webhookData['reference']['siteID'], $webhookData['reference']['assetID']);
                break;
            default:
                //ignore
        }
    }
}
