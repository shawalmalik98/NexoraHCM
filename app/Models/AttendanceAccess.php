<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceAccess extends Model
{
    use HasFactory;
    protected $table = 'tbl_attendance_access';
    protected $primaryKey = 'attendance_access_id';
    public $timestamps = false;
}
