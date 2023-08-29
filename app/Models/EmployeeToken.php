<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeToken extends Model
{
    use HasFactory;
    protected $table = 'tbl_employee_token';
    protected $primaryKey = 'employee_token_id';
    public $timestamps = false;

}
