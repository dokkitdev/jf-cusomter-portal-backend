<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Support\Arr;

/**
 * @property Document $model
*/
class DocumentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Document::class);
    }

    public function filterByTitle()
    {
        if (Arr::has($this->filter, 'title_query')) {
            $this->query->where($this->getQuerySearchCallbackWithValue('title', $this->filter['title_query']));
        }

        return $this;
    }
}
