<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppFlowDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_approval_flow_details';
    protected $primaryKey = 'appflow_detail_id';
    public $timestamps = false;
}
