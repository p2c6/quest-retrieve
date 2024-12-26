<?php

namespace App\Filters;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterUser implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $this->filter($query, $value);
    }

    private function isValidDate($string): bool
    {
        try {
            if (Carbon::parse($string)) {
                return true;
            }

            throw new Exception("Cannot parse string into date");

        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    public function filter($query, $value) {
        if (is_array($value) || $this->isValidDate($value)) {

            if (is_array($value)) {
                $value = implode(" ", $value);
            }
            
            return $this->dateFilter($query, $value, 'profile', [
                'birthday',
            ]);
        }

        return $this->normalFilter($query, $value);
    }

    public function normalFilter($query, $value)
    {
        $query->where(function($subQuery) use ($value) {
            $subQuery->where('email', 'LIKE', "%$value%")
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

    private function hasOnlyMonth($date)
    {
        try {
            return Carbon::createFromFormat('F', $date);
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    private function hasOnlyYear($date)
    {
        try {
            $date = Carbon::createFromFormat('Y', $date);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    private function hasOnlyMonthDay($date)
    {
        try {
            $date = Carbon::createFromFormat('F j', $date);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    private function hasOnlyMonthYear($date)
    {
        try {
            $date = Carbon::createFromFormat('F Y', $date);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    private function hasCompleteDate($date)
    {
        try {
            $date = Carbon::createFromFormat('F j Y', $date);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    public function dateFilter($query, $value, $relatedModel, $columns)
    {
        $inputDate = trim(str_replace(",","", $value));

        if ($this->isValidDate($inputDate)) {       
            foreach ($columns as $column) {
                $this->addColumnToFilter($query, $relatedModel, $inputDate, $column);
            }
        }

    }

    public function monthFilter($query, $relatedModel, $column, $month)
    {
        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $month) {
            $subQuery->whereMonth($column, '=', "$month");
        });
    }

    public function yearFilter($query, $relatedModel, $column, $year)
    {
        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $year) {
            $subQuery->whereYear($column, '=', "$year");
        });
    }

    public function monthDayFilter($query, $relatedModel, $column, $month, $day)
    {
        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $month, $day) {
            $subQuery->whereMonth($column, '=', "$month")
            ->whereDay($column, '=', "$day");
        });
    }

    public function monthYearFilter($query, $relatedModel, $column, $month, $year)
    {
        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $month, $year) {
            $subQuery->whereMonth($column, '=', "$month")
            ->whereYear($column, '=', "$year");
        });
    }

    public function completeDateFilter($query, $relatedModel, $column, $month, $day, $year)
    {
        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $month, $day, $year) {
            $subQuery->whereMonth($column, '=', "$month")
            ->whereDay($column, '=', "$day")
            ->whereYear($column, '=', "$year");
        });
    }

    public function addColumnToFilter($query, $relatedModel, $inputDate, $column)
    {
        if ($this->hasOnlyMonth($inputDate)) {
            $date = Carbon::createFromFormat('F', $inputDate);
            $month = $date->format('n');

            $this->monthFilter($query, $relatedModel, $column,  $month);
        }

        if ($this->hasOnlyYear($inputDate)) {
            $date = Carbon::createFromFormat('Y', $inputDate);
            $year = $date->format('Y');

            $this->yearFilter($query, $relatedModel, $column,  $year);
        }

        if ($this->hasOnlyMonthDay($inputDate)) {
            $date = Carbon::createFromFormat('F j', $inputDate);
            $month = $date->format('n');
            $day = $date->format('j');

            $this->monthDayFilter($query, $relatedModel, $column, $month, $day);
        }

        if ($this->hasOnlyMonthYear($inputDate)) {
            $date = Carbon::createFromFormat('F Y', $inputDate);
            $month = $date->format('n');
            $year = $date->format('Y');

            $this->monthYearFilter($query, $relatedModel, $column, $month, $year);
        }

        if ($this->hasCompleteDate($inputDate)) {
            $date = Carbon::createFromFormat('F j Y', $inputDate);
            $month = $date->format('n');
            $day = $date->format('j');
            $year = $date->format('Y');

            $this->completeDateFilter($query, $relatedModel, $column, $month, $day, $year);
        }
        
    }
}
