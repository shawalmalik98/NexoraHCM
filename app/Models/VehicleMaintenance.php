<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMaintenance extends Model
{
    use HasFactory;
    protected $table = 'tbl_vehicles_maintenance';
    protected $primaryKey = 'maintenance_id';
    public $timestamps = false;
}
