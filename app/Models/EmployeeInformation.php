<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeInformation extends Model
{
    use HasFactory;
    protected $table = 'tbl_personal_informations';
    protected $primaryKey = 'information_id';
    public $timestamps = false;
}
