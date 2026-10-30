<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * "Roles & Permissions" screen as a system module (Admin has it through the Admin role).
 */
return new class extends Migration
{
    public function up()
    {
        if (!DB::table('tbl_system_modules')->where('module_url', '/roles')->exists()) {
            DB::table('tbl_system_modules')->insert([
                'module_title' => 'Roles & Permissions',
                'module_url' => '/roles',
                'module_type' => '1',
                'module_accessibility' => 1,
            ]);
        }
    }

    public function down()
    {
        DB::table('tbl_employee_permission')->where('module_url', '/roles')->delete();
        DB::table('tbl_role_permissions')->where('module_url', '/roles')->delete();
        DB::table('tbl_system_modules')->where('module_url', '/roles')->delete();
    }
};
