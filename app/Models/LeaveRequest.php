<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $table = 'tbl_leaves_applied';
    protected $primaryKey = 'leave_applied_id';
    public $timestamps = false;
}
