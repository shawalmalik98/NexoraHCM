<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;
    protected $table = 'tbl_employee_salary';
    protected $primaryKey = 'salary_id';
    public $timestamps = false;

}
