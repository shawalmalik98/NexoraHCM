<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Business card QR code (SVG) of an employee, kept out of tbl_employees because of its size
class EmployeeQrCode extends Model
{
    use HasFactory;
    protected $table = 'tbl_employee_qr_codes';
    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['employee_id', 'qr_svg'];

}
