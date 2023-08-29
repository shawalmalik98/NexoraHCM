<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeGroup extends Model
{
    use HasFactory;
    protected $table = 'tbl_employee_groups';
    protected $primaryKey = 'employee_group_id';
    public $timestamps = false;
}
