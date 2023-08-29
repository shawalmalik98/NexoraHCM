<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailEndPoint extends Model
{
    use HasFactory;
    protected $table = 'tbl_email_endpoints';
    protected $primaryKey = 'endpoints_id';
    public $timestamps = false;
}
