<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_attendance_requests';
    protected $primaryKey = 'attendance_request_id';
    public $timestamps = false;

}
