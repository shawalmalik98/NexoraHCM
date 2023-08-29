<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleImages extends Model
{
    use HasFactory;
    protected $table = 'tbl_vehicles_images';
    protected $primaryKey = 'vehicle_img_id';
    public $timestamps = false;
}
