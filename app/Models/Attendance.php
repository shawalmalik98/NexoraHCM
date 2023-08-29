<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'tbl_attendance';
    protected $primaryKey = 'attendance_id';
    public $timestamps = false;

}
