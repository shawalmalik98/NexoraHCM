<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertStatus extends Model
{
    use HasFactory;
    protected $table = 'tbl_alert_status';
    protected $primaryKey = 'alert_status_id';
    public $timestamps = false;
}
