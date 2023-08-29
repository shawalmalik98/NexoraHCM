<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'tbl_activity_log';
    protected $primaryKey = 'activity_id';
    public $timestamps = false;
}