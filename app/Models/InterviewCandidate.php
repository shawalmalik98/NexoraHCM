<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewCandidate extends Model
{
    use HasFactory;
    protected $table = 'tbl_interview_candidate';
    protected $primaryKey = 'candidate_id';
    public $timestamps = false;
}
