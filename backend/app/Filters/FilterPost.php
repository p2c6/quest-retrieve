<?php

namespace App\Filters;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FilterPost implements Filter
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
            
            return $this->dateFilter($query, $value, [
                'incident_date',
                'finish_transaction_date'
            ]);
        }

        return $this->normalFilter($query, $value);
    }

    public function normalFilter($query, $value)
    {
        $query->where(function($subQuery) use ($value) {
            $subQuery->where('type', 'LIKE', "$value")
            ->orWhere('incident_location', 'LIKE', "$value")
            ->orWhere('status', 'LIKE', "%$value%")
            ->orWhereHas('subcategory', function ($subQuery) use ($value) {
                $subQuery->where('name', 'LIKE', "$value");
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

    public function dateFilter($query, $value, $columns,  $relatedModel = null)
    {
        $inputDate = trim(str_replace(",","", $value));

        if ($this->isValidDate($inputDate)) {       
            foreach ($columns as $column) {
                $this->addColumnToFilter($query, $relatedModel, $inputDate, $column);
            }
        }

    }

    public function monthFilter($query, $relatedModel = null, $column, $month)
    {
        if (is_null($relatedModel)) {
            return $query->where(function($subQuery) use ($column, $month) {
                $subQuery->where('user_id', auth()->id())
                ->whereMonth($column, $month);
                
            })->orWhere(function($subQuery) use ($column, $month) {
                $subQuery->where('user_id', auth()->id())
                ->whereMonth($column, $month);
            });
        }

        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $month) {
            $subQuery->whereMonth($column, '=', "$month");
        });
    }

    public function yearFilter($query, $relatedModel = null, $column, $year)
    {
        if (is_null($relatedModel)) {
            return $query->where(function($subQuery) use ($column, $year) {
                $subQuery->where('user_id', auth()->id())
                ->whereYear($column, '=', "$year");
                
            })->orWhere(function($subQuery) use ($column, $year) {
                $subQuery->where('user_id', auth()->id())
                ->whereYear($column, $year);
            });
        }

        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $year) {
            $subQuery->whereYear($column, '=', "$year");
        });
    }

    public function monthDayFilter($query, $relatedModel = null, $column, $month, $day)
    {
        if (is_null($relatedModel)) {
            return $query->where(function($subQuery) use ($column, $month, $day) {
                $subQuery->where('user_id', auth()->id())
                ->whereMonth($column, '=', "$month")
                ->whereDay($column, '=', "$day");
                
            })->orWhere(function($subQuery) use ($column, $month, $day) {
                $subQuery->where('user_id', auth()->id())
                ->whereMonth($column, '=', "$month")
                ->whereDay($column, '=', "$day");
            });
        }

        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $month, $day) {
            $subQuery->whereMonth($column, '=', "$month")
            ->whereDay($column, '=', "$day");
        });
    }

    public function monthYearFilter($query, $relatedModel = null, $column, $month, $year)
    {
        if (is_null($relatedModel)) {
            return $query->where(function($subQuery) use ($column, $month, $year) {
                $subQuery->where('user_id', auth()->id())
                ->whereMonth($column, '=', "$month")
                ->whereYear($column, '=', "$year");
                
            })->orWhere(function($subQuery) use ($column, $month, $year) {
                $subQuery->where('user_id', auth()->id())
                ->whereMonth($column, '=', "$month")
                ->whereYear($column, '=', "$year");
            });
        }

        return $query->orWhereHas($relatedModel, function ($subQuery) use ($column, $month, $year) {
            $subQuery->whereMonth($column, '=', "$month")
            ->whereYear($column, '=', "$year");
        });
    }

    public function completeDateFilter($query, $relatedModel = null, $column, $month, $day, $year)
    {
        if (is_null($relatedModel)) {
            return $query->where(function($subQuery) use ($column, $month, $day, $year) {
                $subQuery->where('user_id', auth()->id())
                ->whereMonth($column, '=', "$month")
                ->whereDay($column, '=', "$day")
                ->whereYear($column, '=', "$year");
                
            })->orWhere(function($subQuery) use ($column, $month, $day, $year) {
                $subQuery->where('user_id', auth()->id())
                ->whereMonth($column, '=', "$month")
                ->whereDay($column, '=', "$day")
                ->whereYear($column, '=', "$year");
            });
        }

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

            if (stripos(strtolower($inputDate), 'Feb') !== false) {
                $febDate = Carbon::create(2024, 2, 1, 0, 0, 0, 'Asia/Manila');

                $month = $febDate->month;
            }

            return $this->monthFilter($query, $relatedModel, $column,  $month);
        }

        if ($this->hasOnlyYear($inputDate)) {
            $date = Carbon::createFromFormat('Y', $inputDate);
            $year = $date->format('Y');

            return $this->yearFilter($query, $relatedModel, $column,  $year);
        }

        if ($this->hasOnlyMonthDay($inputDate)) {
            $date = Carbon::createFromFormat('F j', $inputDate);
            $month = $date->format('n');

            if (stripos(strtolower($inputDate), 'Feb') !== false) {
                $febDate = Carbon::create(2024, 2, 1, 0, 0, 0, 'Asia/Manila');

                $month = $febDate->month;
            }

            $day = $date->format('j');
        
            return $this->monthDayFilter($query, $relatedModel, $column, $month, $day);
        }
        if ($this->hasOnlyMonthYear($inputDate)) {
            $date = Carbon::createFromFormat('F Y', $inputDate);
            $month = $date->format('n');

            if (stripos(strtolower($inputDate), 'Feb') !== false) {
                $febDate = Carbon::create(2024, 2, 1, 0, 0, 0, 'Asia/Manila');

                $month = $febDate->month;
            }

            $year = $date->format('Y');

            return $this->monthYearFilter($query, $relatedModel, $column, $month, $year);
        }

        if ($this->hasCompleteDate($inputDate)) {
            $date = Carbon::createFromFormat('F j Y', $inputDate);
            $month = $date->format('n');

            if (stripos(strtolower($inputDate), 'Feb') !== false) {
                $febDate = Carbon::create(2024, 2, 1, 0, 0, 0, 'Asia/Manila');

                $month = $febDate->month;
            }

            $day = $date->format('j');
            $year = $date->format('Y');

            return $this->completeDateFilter($query, $relatedModel, $column, $month, $day, $year);
        }
        
    }
}
