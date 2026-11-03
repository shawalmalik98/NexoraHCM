<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvidentFund extends Model
{
    use HasFactory;
    protected $table = 'tbl_provident_fund';
    protected $primaryKey = 'pf_id';
    public $timestamps = false;
}
