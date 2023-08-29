<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceMachineLogs extends Model
{
    use HasFactory;
    protected $table = 'tbl_machine_attendance_logs';
    protected $primaryKey = 'attendance_logs_id';
    public $timestamps = false;
}
