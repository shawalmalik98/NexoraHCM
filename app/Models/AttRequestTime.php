<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttRequestTime extends Model
{
    use HasFactory;

    protected $table = 'tbl_attendance_request_time';
    protected $primaryKey = 'attendance_request_time_id';
    protected $fillable = ['attendance_req_id', 'attendance_req_emp_id','attendance_checkin_time','attendance_checkout_time','att_req_time_created_at'];
    public $timestamps = false;
}
