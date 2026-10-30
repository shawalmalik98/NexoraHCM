<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The admin role is the Super Admin: complete access to every module, employee and setting.
 */
return new class extends Migration
{
    public function up()
    {
        DB::table('tbl_roles')->where('role_key', 'admin')->update([
            'role_name' => 'Super Admin',
            'role_description' => 'Complete access to every module, employee and setting',
        ]);
    }

    public function down()
    {
        DB::table('tbl_roles')->where('role_key', 'admin')->update([
            'role_name' => 'Admin',
            'role_description' => 'Every module and every employee',
        ]);
    }
};
