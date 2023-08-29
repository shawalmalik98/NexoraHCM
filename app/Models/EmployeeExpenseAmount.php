<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeExpenseAmount extends Model
{
    use HasFactory;
    protected $table = 'tbl_employee_expense_amount';
    protected $primaryKey = 'emp_expense_amt_id';
    public $timestamps = false;
}
