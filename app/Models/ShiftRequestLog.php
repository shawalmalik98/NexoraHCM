<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRequestLog extends Model
{
    use HasFactory;
    protected $table = 'tbl_shift_request_time_log';
    protected $primaryKey = 'shift_log_id';
    public $timestamps = false;
}
