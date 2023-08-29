<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RplLeave extends Model
{
    use HasFactory;
    protected $table = 'tbl_rpl_leave_applied';
    protected $primaryKey = 'rpl_leave_id';
    public $timestamps = false;
}
