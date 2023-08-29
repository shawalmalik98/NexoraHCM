<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $table = 'tbl_banks';
    protected $primaryKey = 'bank_id';
    public $timestamps = false;

}
