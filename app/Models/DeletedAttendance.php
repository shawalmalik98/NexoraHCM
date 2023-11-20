<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedAttendance extends Model
{
    use HasFactory;
    protected $table = 'tbl_deleted_attendance';
    protected $primaryKey = 'deleted_attendance_id';
    public $timestamps = false;
}
