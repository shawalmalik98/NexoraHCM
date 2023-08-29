<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverTime extends Model
{
    use HasFactory;
    protected $table = 'tbl_over_time';
    protected $primaryKey = 'over_time_id';
    public $timestamps = false;
}
