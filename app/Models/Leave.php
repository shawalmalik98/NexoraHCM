<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $guarded = [ "leaves_id" ];
    protected $table = 'tbl_leave_entitlement';
    protected $primaryKey = 'leaves_id';
    public $timestamps = false;

}
