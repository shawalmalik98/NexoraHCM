<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentPeriod extends Model
{
    use HasFactory;
    protected $table = 'tbl_employement_period';
    protected $primaryKey = 'employement_period_id';
    public $timestamps = false;
}
