<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polling extends Model
{
    use HasFactory;
    protected $table = 'tbl_polling';
    protected $primaryKey = 'polling_id';
    public $timestamps = false;
}
