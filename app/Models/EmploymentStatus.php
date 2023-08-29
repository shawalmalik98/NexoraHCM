<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentStatus extends Model
{
    use HasFactory;
    protected $table = 'tbl_employement_status';
    protected $primaryKey = 'employement_status_id';
    public $timestamps = false;
}
