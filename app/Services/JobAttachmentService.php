<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\JobAttachmentRepository;
use Illuminate\Database\Eloquent\Model;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobAttachmentRepository $repository
 * @mixin JobAttachmentRepository
 */
class JobAttachmentService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected int $companyId;

    public function __construct()
    {
        $this->setRepository(JobAttachmentRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->companyId = config('services.simpro.company_id');
    }

    public function syncBySimpro(int $companyId, int $simproJobId, int $jobId): void
    {
        $simproJobAttachmentsPages = $this->simproClient->getAsGenerator(
            "companies/{$companyId}/jobs/{$simproJobId}/attachments/files/",
            [
                'columns' => 'ID,Filename,Public,DateAdded',
                'Public' => 'true'
            ]
        );

        $jobAttachments = $this->repository->get(['job_id' => $jobId]);

        foreach ($simproJobAttachmentsPages as $simproJobAttachmentsPage) {
            foreach ($simproJobAttachmentsPage as $simproJobAttachment) {
                $simproJobAttachmentId = $simproJobAttachment['ID'];
                $data = [
                    'job_id' => $jobId,
                    'simpro_attachment_id' => $simproJobAttachmentId,
                    'name' => $simproJobAttachment['Filename'],
                    'date_added' => empty($simproJobAttachment['DateAdded']) ? null : $simproJobAttachment['DateAdded'],
                ];
                $attachment = $jobAttachments->firstWhere('simpro_attachment_id', $simproJobAttachmentId);
                if ($attachment) {
                    $this->repository->update($attachment['id'], $data);
                    $jobAttachments = $jobAttachments->where('id', '!=', $attachment['id']);
                } else {
                    $this->repository->create($data);
                }
            }
        }

        if ($jobAttachments->isNotEmpty()) {
            $ids = $jobAttachments->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    public function download(int $id): Model
    {
        $attachment = $this->repository
            ->with(['job'])
            ->find($id);

        $simproJobId = $attachment['job']['simpro_job_id'];
        $simproAttachmentId = $attachment['simpro_attachment_id'];

        $this->simproClient->downloadJobAttachment($this->companyId, $simproJobId, $simproAttachmentId);

        return $attachment;
    }
}
