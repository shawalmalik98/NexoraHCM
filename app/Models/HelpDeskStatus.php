<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpDeskStatus extends Model
{
    use HasFactory;
    protected $table = 'tbl_help_desk_ticket_status';
    protected $primaryKey = 'ticket_status_id';
    public $timestamps = false;
}
