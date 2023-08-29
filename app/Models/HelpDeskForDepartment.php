<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpDeskForDepartment extends Model
{
    use HasFactory;
    protected $table = 'tbl_help_desk_for_departments';
    protected $primaryKey = 'hdf_dprt_id';
    public $timestamps = false;
}
