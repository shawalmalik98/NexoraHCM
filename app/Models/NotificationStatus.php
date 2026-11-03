<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationStatus extends Model
{
    use HasFactory;
    protected $table = 'tbl_notification_status';
    protected $primaryKey = 'viewed_id';
    protected $fillable = ['viewed_notification', 'viewed_by'];
    public $timestamps = false;
}
