<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    use HasFactory;
    protected $table = 'tbl_resignation_letters';
    protected $primaryKey = 'resignation_id';
    public $timestamps = false;
}
