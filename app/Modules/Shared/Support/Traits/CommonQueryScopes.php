<?php

namespace App\Modules\Shared\Support\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    private const DEFAULT_DATE_COLUMN = 'date';
    private const DEFAULT_TITLE_COLUMN = 'title';

    /**
     * @template TModel of Model
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function filterByDate(Builder $query, ?string $date, string $column = self::DEFAULT_DATE_COLUMN): Builder
    {
        if ($date === null || $date === '') {
            return $query;
        }

        return $query->whereDate($column, $date);
    }

    /**
     * @template TModel of Model
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function searchByTitle(Builder $query, ?string $search, string $column = self::DEFAULT_TITLE_COLUMN): Builder
    {
        if ($search === null || $search === '') {
            return $query;
        }

        return $query->where($column, 'like', '%'.$search.'%');
    }
}
