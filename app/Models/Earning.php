<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    use HasFactory;
    protected $table = 'tbl_earning';
    protected $primaryKey = 'earning_id';
    public $timestamps = false;
}
