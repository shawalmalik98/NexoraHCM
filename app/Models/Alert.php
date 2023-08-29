<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    protected $table = 'tbl_alerts';
    protected $primaryKey = 'alert_id';
    public $timestamps = false;
}
