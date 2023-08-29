<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDivision extends Model
{
    use HasFactory;
    protected $table = 'tbl_salary_division';
    protected $primaryKey = 'salary_division_id';
    public $timestamps = false;

}
