<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    use HasFactory;
    protected $table = 'tbl_cron_job';
    protected $primaryKey = 'cron_job_id';
    public $timestamps = false;
}
