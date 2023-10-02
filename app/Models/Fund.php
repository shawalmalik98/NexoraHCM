<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    use HasFactory;
    protected $guarded = [ "fund_id" ];
    protected $table = 'tbl_funds';
    protected $primaryKey = 'fund_id';
    public $timestamps = false;
}
