<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;
    protected $table = 'tbl_employee_documents';
    protected $primaryKey = 'document_id';
    public $timestamps = false;
}
