<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobAttachments\DownloadJobAttachmentRequest;
use App\Services\JobAttachmentService;
use Illuminate\Support\Facades\Storage;

class JobAttachmentController extends Controller
{
    public function download(DownloadJobAttachmentRequest $request, JobAttachmentService $service, $id)
    {
        $attachment = $service->download($id);

        return Storage::response($attachment['simpro_attachment_id'], $attachment['name']);
    }
}
