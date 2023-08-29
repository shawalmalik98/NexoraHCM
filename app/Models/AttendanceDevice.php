<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDevice extends Model
{
    use HasFactory;
    protected $table = 'tbl_attendance_devices';
    protected $primaryKey = 'attendance_device_id';
    public $timestamps = false;
}
