<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollingStatus extends Model
{
    use HasFactory;
    protected $table = 'tbl_polling_status';
    protected $primaryKey = 'polling_status_id';
    public $timestamps = false;
}
