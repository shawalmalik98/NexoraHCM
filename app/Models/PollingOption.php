<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollingOption extends Model
{
    use HasFactory;
    protected $table = 'tbl_polling_options';
    protected $primaryKey = 'polling_option_id';
    public $timestamps = false;
}
