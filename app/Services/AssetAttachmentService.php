<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\AssetAttachmentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property AssetAttachmentRepository $repository
 * @mixin AssetAttachmentRepository
 */
class AssetAttachmentService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected int $companyId;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetAttachmentRepository::class);

        $this->companyId = config('services.simpro.company_id');
        $this->simproClient = app(SimproApiClient::class);
    }

    public function syncByAsset(int $companyId, int $simproSiteId, int $simproAssetId, int $assetId): void
    {
        $simproAttachmentsPages = $this->simproClient->getAssetAttachments($companyId, $simproSiteId, $simproAssetId);

        $assetAttachments = $this->repository->get(['asset_id' => $assetId]);

        foreach ($simproAttachmentsPages as $simproAttachmentsPage) {
            if ($simproAttachmentsPage) {
                foreach ($simproAttachmentsPage as $simproAttachment) {
                    $simproAttachmentId = $simproAttachment['ID'];
                    $data = [
                        'asset_id' => $assetId,
                        'simpro_attachment_id' => $simproAttachmentId,
                        'name' => $simproAttachment['Filename'],
                    ];
                    $attachment = $assetAttachments->firstWhere('simpro_attachment_id', $simproAttachmentId);
                    if ($attachment) {
                        $this->repository->update($attachment['id'], $data);
                        $assetAttachments = $assetAttachments->where('id', '!=', $attachment['id']);
                    } else {
                        $this->repository->create($data);
                    }
                }
            }
        }

        if ($assetAttachments->isNotEmpty()) {
            $ids = $assetAttachments->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    public function download(int $id): Model
    {
        $attachment = $this->repository
            ->with(['asset.site'])
            ->find($id);

        $siteId = $attachment['asset']['site']['simpro_site_id'];
        $assetId = $attachment['asset']['simpro_asset_id'];
        $attachmentId = $attachment['simpro_attachment_id'];

        if (!Storage::exists($attachmentId)) {
            $file = $this->simproClient->downloadAssetAttachment($this->companyId, $siteId, $assetId, $attachmentId);

            Storage::put($attachmentId, base64_decode($file['Base64Data']));
        }

        return $attachment;
    }
}
