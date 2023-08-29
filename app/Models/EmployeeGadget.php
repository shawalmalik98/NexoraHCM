<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeGadget extends Model
{
    use HasFactory;
    protected $table = 'tbl_employees_gadgets';
    protected $primaryKey = 'gadget_id';
    public $timestamps = false;
}
