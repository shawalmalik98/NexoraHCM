<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GadgetAllotment extends Model
{
    use HasFactory;
    protected $table = 'tbl_gadget_allotment';
    protected $primaryKey = 'allotment_id';
    public $timestamps = false;
}


