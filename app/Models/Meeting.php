<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    protected $table = 'tbl_meetings';
    protected $primaryKey = 'meeting_id';
    public $timestamps = false;
}
