<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employees;

class Interview extends Model
{
    use HasFactory;

    protected $table = 'tbl_interviews';
    protected $primaryKey = 'interview_id';
    public $timestamps = false;
}

