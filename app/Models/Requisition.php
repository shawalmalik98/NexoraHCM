<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;
    protected $table = 'tbl_requisition';
    protected $primaryKey = 'requisition_id';
    public $timestamps = false;
}
