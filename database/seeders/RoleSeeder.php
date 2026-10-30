<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Built-in roles with their default modules (config/roles.php), and the Super Admin role
 * for the Administrator Account. Roles that already have modules are left as they are.
 */
class RoleSeeder extends Seeder
{
    public function run()
    {
        $modules = DB::table('tbl_system_modules')->pluck('module_url')->toArray();

        foreach (config('roles.defaults') as $key => $role) {
            $role_id = DB::table('tbl_roles')->where('role_key', $key)->value('role_id');
            if (!$role_id) {
                $role_id = DB::table('tbl_roles')->insertGetId([
                    'role_key' => $key,
                    'role_name' => $role['name'],
                    'role_description' => $role['description'],
                    'role_is_system' => true,
                ]);
            }

            if (!DB::table('tbl_role_permissions')->where('role_id', $role_id)->exists()) {
                foreach (array_intersect($role['modules'], $modules) as $module_url) {
                    DB::table('tbl_role_permissions')->insertOrIgnore(['role_id' => $role_id, 'module_url' => $module_url]);
                }
            }
        }

        $admin_role = DB::table('tbl_roles')->where('role_key', 'admin')->value('role_id');
        if ($admin_role && DB::table('tbl_employees')->where('employee_id', 1)->exists()) {
            DB::table('tbl_employee_roles')->insertOrIgnore(['employee_id' => 1, 'role_id' => $admin_role]);
        }
    }
}
