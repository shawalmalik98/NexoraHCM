<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLocker extends Model
{
    use HasFactory;
    protected $table = 'tbl_attendance_sheet_locking';
    protected $primaryKey = 'sheet_lock_id';
    public $timestamps = false;
}
