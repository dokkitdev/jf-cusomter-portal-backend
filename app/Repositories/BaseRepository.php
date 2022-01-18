<?php

namespace App\Repositories;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RonasIT\Support\Repositories\BaseRepository as Repository;

class BaseRepository extends Repository
{
    public function filterByIntQuery(string $field, string $filterName = null)
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

    public function filterByQueryWithValue(string $field, string $filterName)
    {
        if (Arr::has($this->filter, $filterName)) {
            $this->query->where($this->getQuerySearchCallbackWithValue($field, $this->filter[$filterName]));
        }

        return $this;
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
}