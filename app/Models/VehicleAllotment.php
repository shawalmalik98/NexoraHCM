<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAllotment extends Model
{
    use HasFactory;
    protected $table = 'tbl_vehicles_allotment';
    protected $primaryKey = 'allotment_id';
    public $timestamps = false;
}
