<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionStatus extends Model
{
    use HasFactory;
    protected $table = 'tbl_requisition_status';
    protected $primaryKey = 'requisition_status_id';
    public $timestamps = false;
}
