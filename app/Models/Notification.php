<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'tbl_notifications';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;
}
