<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    use HasFactory;
    protected $table = 'tbl_loan_installments';
    protected $primaryKey = 'loan_installment_id';
    public $timestamps = false;
}
