<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    use HasFactory;
    protected $table = 'tbl_employees';
    protected $primaryKey = 'employee_id';
    public $timestamps = false;

    protected static function booted()
    {
        // every employee needs a unique public code for the business card / QR link
        static::creating(function ($employee) {
            if (empty($employee->emp_unique_code)) {
                $employee->emp_unique_code = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
