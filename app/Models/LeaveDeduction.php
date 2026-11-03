<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveDeduction extends Model
{
    use HasFactory;
    protected $guarded = [ "leave_deduction_id" ];
    protected $table = 'tbl_leave_deductions';
    protected $primaryKey = 'leave_deduction_id';
    public $timestamps = false;

    // leave days of the given type taken from the employee's balance by payroll (absent day = 1, extra late / half day = 0.5)
    public static function deducted_count($employee_id, $leave_type_id)
    {
        return (float) self::where('ld_employee_id', $employee_id)->where('ld_leave_type_id', $leave_type_id)->sum('ld_days');
    }

    public static function deducted_count_by_name($employee_id, $leave_name)
    {
        return self::join('tbl_leave_management', 'tbl_leave_management.leave_manage_id', '=', 'tbl_leave_deductions.ld_leave_type_id')
            ->where('ld_employee_id', $employee_id)
            ->where('leave_name', $leave_name)
            ->sum('ld_days');
    }
}
