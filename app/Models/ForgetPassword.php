<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgetPassword extends Model
{
    use HasFactory;
    protected $table = 'tbl_forget_password';
    protected $primaryKey = 'forget_password_id';
    public $timestamps = false;

}
