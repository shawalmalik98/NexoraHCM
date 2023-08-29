<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrmSetting extends Model
{
    use HasFactory;
    protected $table = 'tbl_hrm_settings';
    protected $primaryKey = 'setting_id';
    public $timestamps = false;
}
