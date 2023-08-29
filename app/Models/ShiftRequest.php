<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRequest extends Model
{
    use HasFactory;
    protected $table = 'tbl_shift_request';
    protected $primaryKey = 'shift_request_id';
    public $timestamps = false;
}
