<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftManagement extends Model
{
    use HasFactory;
    protected $table = 'tbl_shift_management';
    protected $primaryKey = 'shift_id';
    public $timestamps = false;
}
