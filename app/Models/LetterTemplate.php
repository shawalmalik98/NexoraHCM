<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    use HasFactory;
    protected $table = 'tbl_letter_template';
    protected $primaryKey = 'letter_template_id';
    public $timestamps = false;
}
