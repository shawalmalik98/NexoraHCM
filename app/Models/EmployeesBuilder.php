<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

// Query builder of Employees: pluck('employee_id') reads the ids directly. Eloquent otherwise builds a model per row
// (the primary key counts as a cast), which costs ~0.2s for 20k employees for the same list of ints.
class EmployeesBuilder extends Builder
{
    public function pluck($column, $key = null)
    {
        if (is_string($column) && $key === null && Str::after($column, $this->model->getTable() . '.') === $this->model->getKeyName()) {
            return $this->toBase()->pluck($column)->map(function ($value) {
                return $value === null ? null : (int) $value;
            });
        }
        return parent::pluck($column, $key);
    }
}
