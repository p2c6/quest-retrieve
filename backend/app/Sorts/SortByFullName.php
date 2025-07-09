<?php
namespace App\Sorts;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByFullName implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'desc' : 'asc';

        return $query->orderByRaw("CONCAT(profiles.first_name, ' ', profiles.last_name) $direction");
    }
}
