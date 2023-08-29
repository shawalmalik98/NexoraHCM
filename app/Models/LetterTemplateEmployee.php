<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterTemplateEmployee extends Model
{
    use HasFactory;
    protected $table = 'tbl_letter_template_employee';
    protected $primaryKey = 'temp_emp_id';
    public $timestamps = false;
}
