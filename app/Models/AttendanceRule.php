<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    use HasFactory;

    protected $table = 'tbl_attendance_deduction_rule';
    protected $primaryKey = 'attendance_deduction_rule_id';
    public $timestamps = false;

}
