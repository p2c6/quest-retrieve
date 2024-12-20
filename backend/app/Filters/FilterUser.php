<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterUser implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function($subQuery) use ($value) {
            $subQuery->where('email', $value)
                ->orWhereHas('profile', function ($subQuery) use ($value) {
                    $subQuery->where('last_name', $value)
                        ->orWhere('first_name', $value)
                        ->orWhere('contact_no', 'LIKE', "%$value%")
                        ->orWhere(function($subQuery) use ($value) {
                            $subQuery->filterByFullName($value);
                        });
                });
        });
    }
}
