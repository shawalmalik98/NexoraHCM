<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeExpense extends Model
{
    use HasFactory;
    protected $table = 'tbl_employee_expense';
    protected $primaryKey = 'emp_expense_id';
    public $timestamps = false;
}
