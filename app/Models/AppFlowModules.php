<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppFlowModules extends Model
{
    use HasFactory;

    protected $table = 'tbl_approval_flow_modules';
    protected $primaryKey = 'appflow_module_id';
    public $timestamps = false;
}
