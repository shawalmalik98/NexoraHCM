<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemModule extends Model
{
    use HasFactory;
    protected $table = 'tbl_system_modules';
    protected $primaryKey = 'module_id';
    public $timestamps = false;
}
