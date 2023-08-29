<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;
    protected $table = 'tbl_letters';
    protected $primaryKey = 'letter_id';
    public $timestamps = false;
}
