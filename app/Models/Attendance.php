<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'tbl_attendance';
    protected $primaryKey = 'attendance_id';
    public $timestamps = false;

    // day / month are stored two digits ('05'), so lookups can compare them as plain strings on the index
    public function setAttForDateAttribute($value)
    {
        $this->attributes['att_for_date'] = self::two_digits($value);
    }

    public function setAttForMonthAttribute($value)
    {
        $this->attributes['att_for_month'] = self::two_digits($value);
    }

    private static function two_digits($value)
    {
        return is_numeric($value) ? str_pad((int) $value, 2, '0', STR_PAD_LEFT) : $value;
    }

}
