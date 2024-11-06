<?php

namespace App\Filters;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DateTime;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class GlobalFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        
        $query->where(function ($query) use ($value) {
            if (is_array($value) || $this->isDate($value)) {
                    $filterDateValue = $value;
                    $formattedDate = null;

                    if (is_array($filterDateValue)) {
                        $formattedDate = implode(" " , $filterDateValue);

                    } else {
                        $formattedDate = $filterDateValue;
                    }

                    $sanitizedDate = $this->filterDate($formattedDate); 
                    
                    $month = $day = $year = null;
                    
                    if ($this->hasMonth($sanitizedDate)) {
                        $date = Carbon::createFromFormat('F', $sanitizedDate);
                        $month = $date->format('n');
                    }

                    if ($this->hasMonthAndDay($sanitizedDate)) {
                        $date = Carbon::createFromFormat('F j', $sanitizedDate);
                        $month = $date->format('n');
                        $day = $date->format('j');
                        
                    }

                    if ($this->hasMonthDayAndYear($sanitizedDate)) {
                        $date = Carbon::createFromFormat('F j Y', $sanitizedDate);
                        $month = $date->format('n');
                        $day = $date->format('j');
                        $year = $date->format('Y');
                    }
                    
                    $this->applyDateCondition($query, $month, $day, $year);
            } else {    
                $query->orWhere('type', 'like', "%$value%")
                ->orWhere('status', 'like', "%$value%")
                ->orWhere('incident_location', 'like', "%$value%");
            }

        });
    }

    private function hasMonth($date)
    {
        try {
            $date = Carbon::createFromFormat('F', $date);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    
    private function hasMonthAndDay($date)
    {
        try {
            $date = Carbon::createFromFormat('F j', $date);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }


    private function hasMonthDayAndYear($date)
    {
        try {
            $date = Carbon::createFromFormat('F j Y', $date);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }


    private function filterDate($date): string
    {
        $filteredDate = implode(' ', array_values(array_filter(explode(' ', $date))));

        return $filteredDate;
    }

    private function isDate($string): bool
    {
        try {
            Carbon::parse($string);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    private function applyDateCondition($query, $month, $day, $year): void
    {
        if ($month !== null && $day === null && $year === null) {
            $this->applyMonthFilter($query, $month);
        }

        if ($month !== null && $day !== null && $year === null) {
            $this->applyMonthWithDayFilter($query, $month, $day);
        }

        if ($month !== null && $day !== null && $year !== null) {
            $this->applyFullDateFilter($query, $month, $day, $year);
        }
    }

    private function applyMonthFilter($query, $month): void
    {
        $query->whereMonth('incident_date', '=', $month);
        $this->addColumnMonthFilter($query, 'finish_transaction_date', '=', $month);
        $this->addColumnMonthFilter($query, 'expiration_date', '=', $month);
    }

    private function addColumnMonthFilter($query, $column,$value, $operator = null ): void
    {
        $query->orWhereMonth($column, $value, $operator, $value);
    }

    private function applyMonthWithDayFilter($query, $month, $day): void
    {
        $query->where(function($query) use ($month, $day) {
            $query->whereMonth('incident_date', '=', "$month")
                ->whereDay('incident_date', '=', "$day");
        });

        $this->addColumnMonthWithDayFilter($query, "finish_transaction_date", $month, $day, "=");
        $this->addColumnMonthWithDayFilter($query, "expiration_date", $month, $day, "=");
    }

    private function addColumnMonthWithDayFilter($query, $column, $month, $day, $operator = null): void
    {
        $query->orWhere(function($query) use ($column, $month, $day, $operator) {
            $query->whereMonth($column, $operator, $month)
            ->whereDay($column,  $operator, $day);
        });
    }

    private function addColumnFullDateFilter($query, $column, $month, $day, $year, $operator = null): void
    {
        $query->orWhere(function($query) use ($column, $month, $day, $year, $operator) {
            $query->whereMonth($column, $operator, $month)
            ->whereDay($column, $operator, "$day")
            ->whereYear($column, $operator, "$year");
        });
    }

    private function applyFullDateFilter($query, $month, $day, $year): void
    {
        $query->where(function($query) use ($month, $day, $year) {
            $query->whereMonth('incident_date', '=', "$month")
                ->whereDay('incident_date', '=', "$day")
                ->whereYear('incident_date', '=', "$year");
        });

        $this->addColumnFullDateFilter($query, "finish_transaction_date", $month, $day, $year, "=");
        $this->addColumnFullDateFilter($query, "expiration_date", $month, $day, $year, "=");
    }
}