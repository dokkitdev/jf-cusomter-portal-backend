<?php

namespace App\Services;

use App\Repositories\JobCatalogRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobCatalogRepository $repository
 * @mixin JobCatalogRepository
 */
class JobCatalogService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(JobCatalogRepository::class);
    }

    public function syncBySimpro(array $simproJob, int $jobId): void
    {
        $catalogs = $this->repository->get(['job_id' => $jobId]);

        $sections = Arr::get($simproJob, 'Sections', []);

        foreach ($sections as $section) {
            $sectionId = $section['ID'];
            $costCenters = Arr::get($section, 'CostCenters', []);
            foreach ($costCenters as $costCenter) {
                $costCenterId = $costCenter['ID'];
                $simproCatalogs = Arr::get($costCenter, 'Items.Catalogs', []);
                foreach ($simproCatalogs as $simproCatalog) {
                    $this->updateOrCreateCatalog($simproCatalog, $jobId, $sectionId, $costCenterId, $catalogs);
                }
            }
        }

        if ($catalogs->isNotEmpty()) {
            $ids = $catalogs->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    protected function updateOrCreateCatalog(array $simproCatalog, int $jobId, int $sectionId, int $costCenterId, Collection &$catalogs): void
    {
        $catalogId = $simproCatalog['ID'];
        $data = [
            'job_id' => $jobId,
            'simpro_section_id' => $sectionId,
            'simpro_cost_center_id' => $costCenterId,
            'simpro_catalog_id' => $catalogId,
            'simpro_original_catalog_id' => Arr::get($simproCatalog, 'Catalog.ID'),
            'name' => Arr::get($simproCatalog, 'Catalog.Name'),
            'part_no' => Arr::get($simproCatalog, 'Catalog.PartNo'),
            'qty' => Arr::get($simproCatalog, 'Total.Qty'),
        ];
        $catalog = $catalogs->first(function($item) use ($sectionId, $costCenterId, $catalogId) {
            return ($item['simpro_section_id'] === $sectionId) && ($item['simpro_cost_center_id'] === $costCenterId) && ($item['simpro_catalog_id'] === $catalogId);
        });
        if ($catalog) {
            $this->repository->update($catalog['id'], $data);
            $catalogs = $catalogs->where('id', '!=', $catalog['id']);
        } else {
            $this->repository->create($data);
        }
    }
}
