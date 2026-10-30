<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Attendance day / month are varchar; the app writes them two digits ('05') but older imports wrote '5'.
 * With one format, lookups compare strings on the (employee, year, month, day) index instead of numbers,
 * which can't use it. The Attendance model pads new values.
 */
return new class extends Migration
{
    public function up()
    {
        DB::statement("UPDATE tbl_attendance SET att_for_date = LPAD(att_for_date, 2, '0') WHERE CHAR_LENGTH(att_for_date) = 1");
        DB::statement("UPDATE tbl_attendance SET att_for_month = LPAD(att_for_month, 2, '0') WHERE CHAR_LENGTH(att_for_month) = 1");
    }

    public function down()
    {
        // padded values are valid for the old code as well
    }
};
