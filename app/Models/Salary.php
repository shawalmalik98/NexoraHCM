<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $table = 'tbl_salary';
    protected $primaryKey = 'salary_id';
    public $timestamps = false;
}
