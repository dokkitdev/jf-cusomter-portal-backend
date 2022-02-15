<?php

namespace App\Http\Controllers;

use App\Http\Requests\Media\DownloadMediaRequest;
use App\Services\MediaService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Media\CreateMediaRequest;
use App\Http\Requests\Media\DeleteMediaRequest;
use App\Http\Requests\Media\SearchMediaRequest;

class MediaController extends Controller
{
    public function create(CreateMediaRequest $request, MediaService $service)
    {
        $file = $request->file('file');
        $data = $request->onlyValidated();

        $content = 'content';//file_get_contents($file->getPathname());

        $media = $service->create($content, $file->getClientOriginalName(), $data);

        return response()->json($media);
    }

    public function delete(DeleteMediaRequest $request, MediaService $service, $id)
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchMediaRequest $request, MediaService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }

    public function download(DownloadMediaRequest $request, MediaService $service, $id)
    {
        $media = $service->find($id);

        return Storage::download($service->getFilePathFromUrl($media['link']), $media['name']);
    }
}
