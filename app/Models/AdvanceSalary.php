<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceSalary extends Model
{
    use HasFactory;
    protected $table = 'tbl_advance_salary_request';
    protected $primaryKey = 'advance_salary_request_id';
    public $timestamps = false;
}
