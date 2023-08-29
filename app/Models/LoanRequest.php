<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    use HasFactory;
    protected $table = 'tbl_loan_request';
    protected $primaryKey = 'loan_request_id';
    public $timestamps = false;
}
