<?php

namespace App\Filters;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class GlobalFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        
        $query->where(function ($query) use ($value) {
            if ($this->isDate($value)) {
                $date = Carbon::parse($value);

                $count = count(explode(" ", $value));
            
                $month = $date->format('n');
                $day = $date->format('j');
                $year = $date->format('Y');

                $this->applyDateCondition($query, $month, $day, $year, $count);
            }
            
            $query->orWhere('type', 'like', "%$value%")
                ->orWhere('status', 'like', "%$value%")
                ->orWhere('incident_location', 'like', "%$value%");

        });
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

    private function applyDateCondition($query, $month, $day, $year, $count): void
    {
        if ($count == 1) {
            $this->applyMonthFilter($query, $month);
        }

        if ($count == 2) {
            $this->applyMonthWithDayFilter($query, $month, $day);
        }

        if ($count == 3) {
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