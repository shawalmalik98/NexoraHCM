<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LateComers extends Model
{
    use HasFactory;
    protected $table = 'tbl_continous_latecomers';
    protected $primaryKey = 'continous_latecomers_id';
    public $timestamps = false;
}
