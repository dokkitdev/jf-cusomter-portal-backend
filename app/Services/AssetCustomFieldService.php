<?php

namespace App\Services;

use App\Repositories\AssetCustomFieldRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @property AssetCustomFieldRepository $repository
 * @mixin AssetCustomFieldRepository
 */
class AssetCustomFieldService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetCustomFieldRepository::class);
    }

    public function syncByAsset(array $simproAsset, int $assetId): void
    {
        $this->repository->delete(['asset_id' => $assetId]);

        $simproAssetCustomFields = array_slice($simproAsset['CustomFields'], 0, 4);

        foreach ($simproAssetCustomFields as $simproAssetCustomField) {
            try {
                $this->repository->create([
                    'asset_id' => $assetId,
                    'simpro_custom_field_id' => Arr::get($simproAssetCustomField, 'CustomField.ID'),
                    'name' => Arr::get($simproAssetCustomField, 'CustomField.Name'),
                    'value' => Arr::get($simproAssetCustomField, 'Value'),
                ]);
            } catch (QueryException $exception) {
                if (!Str::contains(strtolower($exception->getMessage()), 'unique violation')) {
                    throw $exception;
                }
            }
        }
    }
}
