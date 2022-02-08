<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use RonasIT\Support\Traits\ModelTrait;

class BaseModel extends Model
{
    use ModelTrait;

    public function scopeOrderByRelated($query, string $relations, string $desc = 'DESC', ?string $asField = null, string $manyToManyStrategy = 'max')
    {
        if (version_compare(app()::VERSION, '5.5') <= 0) {
            return $query->legacyOrderByRelated($relations, $desc);
        }

        if (empty($asField)) {
            $asField = str_replace('.', '_', $relations);
        }

        $relations = $this->prepareRelations($relations);
        $orderField = $this->getOrderedField($relations);

        if (!empty($relations)) {
            $queries = $this->getQueriesList($query, $relations);
            $prevQuery = array_shift($queries);
            array_pop($queries);

            $this
                ->applyManyToManyStrategy($prevQuery, $manyToManyStrategy)
                ->select($orderField);

            foreach ($queries as $queryInCollection) {
                $prevQuery = $this
                    ->applyManyToManyStrategy($queryInCollection, $manyToManyStrategy)
                    ->selectSub($prevQuery, $asField);
            }

            $query->addFieldsToSelect();
            $query->selectSub($prevQuery, $asField);
        }

        return $query->orderBy($asField ?? $orderField, $desc);
    }

    protected function getQueriesList($query, array $relations): array
    {
        $requiredColumns = [];
        $queryCollection = [$query];

        foreach ($relations as $relationString) {
            $query = Arr::last($queryCollection);

            $relation = $this->getRelationWithoutConstraints($query, $relationString);
            $subQuery = $relation->getRelationExistenceQuery(
                $relation->getQuery(),
                $query,
                $requiredColumns
            );

            $queryCollection[] = $subQuery;
        }

        return array_reverse($queryCollection);
    }

    protected function applyManyToManyStrategy($query, string $strategy)
    {
        if ($strategy === 'max') {
            $query->orderBy('id', 'ASC')->limit(1);
        } else {
            $query->orderBy('id', 'DESC')->limit(1);
        }

        return $query;
    }
}