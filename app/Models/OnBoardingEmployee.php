<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnBoardingEmployee extends Model
{
    use HasFactory;
    protected $table = 'tbl_employee_onboarding';
    protected $primaryKey = 'onboarding_id';
    public $timestamps = false;
}
