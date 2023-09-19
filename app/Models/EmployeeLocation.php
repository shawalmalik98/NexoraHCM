<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLocation extends Model
{
    use HasFactory;
    protected $table = 'tbl_employees_location';
    protected $primaryKey = 'employee_location_id';
    public $timestamps = false;
}
