<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $table = 'tbl_holidays';
    protected $primaryKey = 'holiday_id';
    public $timestamps = false;
}
