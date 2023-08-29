<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpDesk extends Model
{
    use HasFactory;
    protected $table = 'tbl_help_desk';
    protected $primaryKey = 'help_desk_id';
    public $timestamps = false;
}
