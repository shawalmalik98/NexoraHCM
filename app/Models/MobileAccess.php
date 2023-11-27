<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileAccess extends Model
{
    use HasFactory;
    protected $table = 'tbl_mobile_access';
    protected $primaryKey = 'mobile_access_id';
    public $timestamps = false;
}
