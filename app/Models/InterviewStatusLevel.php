<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewStatusLevel extends Model
{
    use HasFactory;
    protected $table = 'tbl_interview_status_level';
    protected $primaryKey = 'interview_status_level_id';
    public $timestamps = false;
}
