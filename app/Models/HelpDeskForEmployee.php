<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpDeskForEmployee extends Model
{
    use HasFactory;
    protected $table = 'tbl_help_desk_for_employees';
    protected $primaryKey = 'hdf_emp_id';
    public $timestamps = false;
}