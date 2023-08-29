<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;
    protected $table = 'tbl_designations';
    protected $primaryKey = 'designation_id';
    public $timestamps = false;
}
