<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailRecipient extends Model
{
    use HasFactory;
    protected $table = 'tbl_email_recipients';
    protected $primaryKey = 'recipient_id';
    public $timestamps = false;
}
