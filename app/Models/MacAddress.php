<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MacAddress extends Model
{
    use HasFactory;
    protected $table = 'tbl_mac_address';
    protected $primaryKey = 'address_id';
    public $timestamps = false;
}
