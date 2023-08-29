<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{
    use HasFactory;
    protected $table = 'tbl_email_configuration';
    protected $primaryKey = 'email_config_id';
    public $timestamps = false;
}
