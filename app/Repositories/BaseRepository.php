<?php

namespace App\Repositories;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use RonasIT\Support\Repositories\BaseRepository as Repository;

class BaseRepository extends Repository
{
    public function updateOrCreate($where, $data): Model
    {
        if ($this->exists($where)) {
            return $this->update($where, $data);
        }

        if (!is_array($where)) {
            $where = [$this->primaryKey => $where];
        }

        try {
            return $this->create(array_merge($data, $where));
        } catch (QueryException $exception) {
            if (Str::contains(strtolower($exception->getMessage()), 'unique violation')) {
                return $this->update($where, $data);
            }

            dump($exception->getMessage());
            throw $exception;
        }
    }

    public function retryForeignKeyViolation(callable $callback, int $maxAttempts = 10)
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            try {
                return DB::transaction($callback);
            } catch (QueryException $exception) {
                if (!Str::contains(strtolower($exception->getMessage()), 'foreign key violation')) {
                    throw $exception;
                }
            }
        }

        throw $exception;
    }

    public function findByPermissions(int $id, int $userId): ?Model
    {
        return $this->getQuery()
            ->onlyPermitted($userId)
            ->find($id);
    }

    public function filterTimeFrom(string $fieldName, string $filterName, bool $strict = false): self
    {
        if (Arr::has($this->filter, $filterName)) {
            $sign = $strict ? '>' : '>=';

            $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter[$filterName])->format('H:i');

            $this->query->where(DB::raw("cast({$fieldName} as time)"), $sign, $time);
        }

        return $this;
    }

    public function filterTimeTo(string $fieldName, string $filterName, bool $strict = false): self
    {
        if (Arr::has($this->filter, $filterName)) {
            $sign = $strict ? '<' : '<=';

            $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter[$filterName])->format('H:i');

            $this->query->where(DB::raw("cast({$fieldName} as time)"), $sign, $time);
        }

        return $this;
    }

    public function filterByIntQuery(string $field, string $filterName = null): self
    {
        if (empty($filterName)) {
            if (Str::contains($field, '.')) {
                $entities = explode('.', $field);
                $filterName = Arr::last($entities);
            } else {
                $filterName = $field;
            }
        }

        if (Arr::has($this->filter, $filterName)) {
            $this->addIntQueryWhere($this->query, $field, $this->filter[$filterName]);
        }

        return $this;
    }

    public function filterByQueryWithValue(string $field, string $filterName): self
    {
        if (Arr::has($this->filter, $filterName)) {
            $this->query->where($this->getQuerySearchCallbackWithValue($field, $this->filter[$filterName]));
        }

        return $this;
    }

    public function iterateSearchResults(?int $chunkSize = null): LazyCollection
    {
        $this->query->reorder();

        return $this->query->lazyById($chunkSize);
    }

    protected function addIntQueryWhere(&$query, string $field, $value): void
    {
        $this->applyWhereCallback($query, $field, function (&$q, $field) use ($value) {
            $q->where($this->getIntQuerySearchCallbackWithValue($field, $value));
        });
    }

    protected function getIntQuerySearchCallbackWithValue(string $field, $value): Closure
    {
        return function ($query) use ($field, $value) {
            $field = DB::raw("cast({$field} as text)");

            $query->where($field, 'like', "{$value}%");
        };
    }

    protected function getQuerySearchCallbackWithValue(string $field, $value, string $left = '%', string $right = '%'): Closure
    {
        return function ($query) use ($field, $value, $left, $right) {
            $loweredQuery = mb_strtolower($value);
            $field = DB::raw("lower({$field})");

            $query->orWhere($field, 'like', "{$left}{$loweredQuery}{$right}");
        };
    }

    public function filterByCompanyId(int $companyId): self
    {
        if (Arr::has($this->filter, 'company_id')) {
            $this->query->where('company_id', $companyId);
        }

        return $this;
    }


    public function filterByTeam($team): self
    {
        $this->query->where('team_id', $team->id);

        return $this;
    }
}
