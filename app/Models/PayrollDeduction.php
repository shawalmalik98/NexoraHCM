<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDeduction extends Model
{
    use HasFactory;
    protected $table = 'tbl_payroll_calculation_deduction';
    protected $primaryKey = 'pc_deduction_id';
    public $timestamps = false;
}
