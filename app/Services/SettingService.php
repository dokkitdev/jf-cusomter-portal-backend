<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Repositories\SettingRepository;

/**
 * @property SettingRepository $repository
 * @mixin SettingRepository
 */
class SettingService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(SettingRepository::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['name'])
            ->orderBy('name')
            ->getSearchResults();
    }

    public function get(string $key, ?string $default = null)
    {
        $explodedKey = explode('.', $key);
        $primaryKey = array_shift($explodedKey);

        $setting = $this->repository->findBy('name', $primaryKey);

        if (empty($setting)) {
            return $default;
        }

        if (empty($explodedKey)) {
            return $setting['value'];
        }

        array_unshift($explodedKey, 'value');
        $valuePath = implode('.', $explodedKey);

        return Arr::get($setting, $valuePath);
    }

    public function set(string $key, $value): Model
    {
        $explodedKey = explode('.', $key);
        $primaryKey = array_shift($explodedKey);

        $setting = $this->repository->findBy('name', $primaryKey);

        if (empty($setting)) {
            return $this->repository->create([
                'name' => $key,
                'value' => $value
            ]);
        }

        array_unshift($explodedKey, 'value');
        $valuePath = implode('.', $explodedKey);

        Arr::set($setting, $valuePath, $value);

        return $this->repository->update([
            'name' => $primaryKey
        ], [
            'value' => $setting['value']
        ]);
    }
}
