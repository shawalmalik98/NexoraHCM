<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDevice extends Model
{
    use HasFactory;
    protected $table = 'tbl_employees_devices';
    protected $primaryKey = 'device_id';
    public $timestamps = false;
}
