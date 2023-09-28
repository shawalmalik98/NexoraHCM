<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFund extends Model
{
    use HasFactory;
    protected $guarded = [ "ef_id" ];
    protected $table = 'tbl_employees_funds';
    protected $primaryKey = 'ef_id';
    public $timestamps = false;
}
