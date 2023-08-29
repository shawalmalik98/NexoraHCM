<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareAttReport extends Model
{
    use HasFactory;
    protected $table = 'tbl_attendance_report';
    protected $primaryKey = 'att_report_id';
    public $timestamps = false;
}
