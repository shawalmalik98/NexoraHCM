<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomScreen extends Model
{
    use HasFactory;
    protected $table = 'tbl_room_screen';
    protected $primaryKey = 'room_id';
    public $timestamps = false;
}
