<?php

namespace App\Services;

use App\Repositories\MediaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use RonasIT\Support\Traits\FilesUploadTrait;

/**
 * @property MediaRepository $repository
 * @mixin MediaRepository
 */
class MediaService extends BaseService
{
    use FilesUploadTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(MediaRepository::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['name'])
            ->getSearchResults();
    }

    public function create(string $content, string $fileName, array $data = []): Model
    {
        $url = 'url';//$this->saveFile($fileName, $content, true);
        $data['link'] = str_replace(config('app.url'), '', $url);
        $data['name'] = $fileName;
        $data['owner_id'] = Auth::user()->id;

        return $this->repository->create($data);
    }
}
