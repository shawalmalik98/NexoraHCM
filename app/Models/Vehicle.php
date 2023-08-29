<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $table = 'tbl_vehicles';
    protected $primaryKey = 'vehicle_id';
    public $timestamps = false;

}
