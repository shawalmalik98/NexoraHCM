<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Roles are given to employees one by one by management, never by department: every employee has the Employee
 * (self service) role and any other role is assigned in Roles & Permissions. Adds the C-level, executive and
 * department manager roles of config/roles.php, renames HR / Finance to HR Manager / Finance Manager, and drops the
 * department -> role links (not turned into employee roles: being in Finance must not make someone Finance Manager).
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tbl_roles')) {
            return;
        }

        (new Database\Seeders\RoleSeeder)->run();

        DB::table('tbl_roles')->where('role_key', 'hr')->where('role_name', 'HR')->update(['role_name' => 'HR Manager']);
        DB::table('tbl_roles')->where('role_key', 'finance')->where('role_name', 'Finance')->update(['role_name' => 'Finance Manager']);
        DB::table('tbl_roles')->where('role_key', 'employee')->where('role_description', 'Self service')->update(['role_description' => 'Self service, every employee has it']);

        if (Schema::hasTable('tbl_role_departments')) {
            DB::table('tbl_role_departments')->delete();
        }
    }

    public function down()
    {
        DB::table('tbl_roles')->where('role_key', 'hr')->where('role_name', 'HR Manager')->update(['role_name' => 'HR']);
        DB::table('tbl_roles')->where('role_key', 'finance')->where('role_name', 'Finance Manager')->update(['role_name' => 'Finance']);
    }
};
