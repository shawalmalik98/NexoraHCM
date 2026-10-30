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

    // leaves of the given type taken from the employee's balance for late comings / half days
    public static function deducted_count($employee_id, $leave_type_id)
    {
        return self::where('ld_employee_id', $employee_id)->where('ld_leave_type_id', $leave_type_id)->count();
    }

    public static function deducted_count_by_name($employee_id, $leave_name)
    {
        return self::join('tbl_leave_management', 'tbl_leave_management.leave_manage_id', '=', 'tbl_leave_deductions.ld_leave_type_id')
            ->where('ld_employee_id', $employee_id)
            ->where('leave_name', $leave_name)
            ->count();
    }
}
