<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewStatus extends Model
{
    use HasFactory;
    protected $table = 'tbl_interview_status';
    protected $primaryKey = 'interview_status_id';
    public $timestamps = false;
}
