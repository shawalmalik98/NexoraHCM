<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWages extends Model
{
    use HasFactory;
    protected $table = 'tbl_daily_wages';
    protected $primaryKey = 'dw_id';
    public $timestamps = false;
}
