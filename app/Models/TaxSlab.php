<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxSlab extends Model
{
    use HasFactory;
    protected $table = 'tbl_payroll_tax_slab';
    protected $primaryKey = 'pc_tax_slab_id';
    public $timestamps = false;
}
