<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $table = 'tbl_loans';
    protected $primaryKey = 'loan_id';
    public $timestamps = false;
}
