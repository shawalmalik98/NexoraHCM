<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollAttempts extends Model
{
    use HasFactory;
    protected $table = 'tbl_payroll_attempts';
    protected $primaryKey = 'attempt_id';
    public $timestamps = false;
}
