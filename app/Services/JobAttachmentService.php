<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Team\SimProTeams;
use App\Repositories\JobAttachmentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobAttachmentRepository $repository
 * @mixin JobAttachmentRepository
 */
class JobAttachmentService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected int $companyId;
    protected ?SimProTeams $team = null;

    public function __construct(
        SimProTeams $team = null
    )
    {
        $this->team = $team;
        $this->setRepository(JobAttachmentRepository::class);

        if($team){
            $this->simproClient = new SimproApiClient($team);
        }else{
            $this->simproClient = new SimproApiClient($team);
        }
        $this->companyId = config('services.simpro.company_id');
    }

    public function syncBySimpro(int $companyId, int $simproJobId, int $jobId): void
    {
        $simproJobAttachmentsPages = $this->simproClient->getJobAttachments($companyId, $simproJobId);

        $jobAttachments = $this->repository->get(['job_id' => $jobId]);

        foreach ($simproJobAttachmentsPages as $simproJobAttachmentsPage) {
            if ($simproJobAttachmentsPage) {
                foreach ($simproJobAttachmentsPage as $simproJobAttachment) {
                    $attachment = $this->updateOrCreate([
                        'job_id' => $jobId,
                        'simpro_attachment_id' => $simproJobAttachment['ID'],
                    ], [
                        'name' => $simproJobAttachment['Filename'],
                        'date_added' => empty($simproJobAttachment['DateAdded']) ? null : $simproJobAttachment['DateAdded'],
                    ]);

                    $jobAttachments = $jobAttachments->where('id', '!=', $attachment['id']);
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

        if (!Storage::exists($simproAttachmentId)) {
            $file = $this->simproClient->downloadJobAttachment($this->companyId, $simproJobId, $simproAttachmentId);

            Storage::put($simproAttachmentId, base64_decode($file['Base64Data']));
        }

        return $attachment;
    }
}
