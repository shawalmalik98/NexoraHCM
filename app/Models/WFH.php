<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WFH extends Model
{
    use HasFactory;
    protected $table = 'tbl_work_from_homes';
    protected $primaryKey = 'wfh_request_id';
    public $timestamps = false;
}
