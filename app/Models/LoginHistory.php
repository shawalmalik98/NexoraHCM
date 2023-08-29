<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;
    protected $table = 'tbl_login_history';
    protected $primaryKey = 'login_history_id';
    public $timestamps = false;
}
