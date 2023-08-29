<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSheet extends Model
{
    use HasFactory;
    protected $table = 'tbl_expense_sheet';
    protected $primaryKey = 'expense_id';
    public $timestamps = false;

}
