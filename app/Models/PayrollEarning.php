<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollEarning extends Model
{
    use HasFactory;
    protected $table = 'tbl_payroll_calculation_earning';
    protected $primaryKey = 'pc_earning_id';
    public $timestamps = false;
}
