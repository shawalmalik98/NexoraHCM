<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    use HasFactory;
    protected $table = 'tbl_office_locations';
    protected $primaryKey = 'location_id';
    public $timestamps = false;
}
