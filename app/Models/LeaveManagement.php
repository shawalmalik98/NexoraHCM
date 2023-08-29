<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveManagement extends Model
{
    use HasFactory;
    protected $guarded = [ "leave_manage_id" ];
    protected $table = 'tbl_leave_management';
    protected $primaryKey = 'leave_manage_id';
    public $timestamps = false;
}
