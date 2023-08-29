<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingUsers extends Model
{
    use HasFactory;
    protected $table = 'tbl_meeting_users';
    protected $primaryKey = 'meet_user_id';
    public $timestamps = false;
}
