<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    public function filterByDate(Builder $query, ?string $date): Builder
    {
        if ($date === null || $date === '') {
            return $query;
        }

        return $query->whereDate('date', $date);
    }

    public function searchByTitle(Builder $query, ?string $search): Builder
    {
        if ($search === null || $search === '') {
            return $query;
        }

        return $query->where('title', 'like', '%'.$search.'%');
    }
}
