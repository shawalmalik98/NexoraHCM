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
}
