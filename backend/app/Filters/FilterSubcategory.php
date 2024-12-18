<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterSubcategory implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function($subQuery) use ($value) {
            $subQuery->where('name', $value)
            ->orWhereHas('category', function ($subQuery) use ($value) {
                $subQuery->where('name', $value);
            });
        });
    }
}