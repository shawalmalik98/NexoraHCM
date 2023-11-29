<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendancejob extends Model
{
    use HasFactory;
    protected $table = 'tbl_attendance_job';
    protected $primaryKey = 'attendance_job_id';
    public $timestamps = false;
}
