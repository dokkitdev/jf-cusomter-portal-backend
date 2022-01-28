<?php

namespace App\Repositories;

use App\Models\Site;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property Site $model
*/
class SiteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Site::class);
    }

    public function filterByOnlyPermitted(): self
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->onlyPermitted($this->filter['site_has_user']);
        }

        return $this;
    }

    public function filterByPostalCode(): self
    {
        if (Arr::has($this->filter, 'postal_code')) {
            $postalCode = str_replace(' ', '', $this->filter['postal_code']);

            $this->query->where(DB::raw("REPLACE(postal_code, ' ', '')"), $postalCode);
        }

        return $this;
    }

    public function filterByPrimaryContact(): self
    {
        if (Arr::has($this->filter, 'primary_contact_query')) {
            $this->query->whereHas('primary_site_contact', function ($query) {
                $query->where($this->getQuerySearchCallbackWithValue('name', $this->filter['primary_contact_query']));
            });
        }

        return $this;
    }

    public function filterByOpenJobs(): self
    {
        if (Arr::has($this->filter, 'has_open_jobs')) {
            if (Arr::get($this->filter, 'has_open_jobs')) {
                $this->query->whereHas('open_jobs');
            } else {
                $this->query->whereDoesntHave('open_jobs');
            }
        }

        return $this;
    }

    public function filterByName(): self
    {
        if (Arr::has($this->filter, 'name_query')) {
            $this->query->where($this->getQuerySearchCallbackWithValue('name', $this->filter['name_query']));
        }

        return $this;
    }

    public function filterByUprn(): self
    {
        if (Arr::has($this->filter, 'uprn_query')) {
            $this->query->where($this->getQuerySearchCallbackWithValue('uprn', $this->filter['uprn_query']));
        }

        return $this;
    }
}
