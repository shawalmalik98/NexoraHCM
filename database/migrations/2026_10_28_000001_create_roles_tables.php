<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Roles: Admin, HR, Finance, Manager, Employee.
 *
 * An employee's roles = roles assigned to the employee (tbl_employee_roles)
 *                     + roles of the employee's department (tbl_role_departments)
 *                     + Manager when employee_is_manager = 1, + Employee for everyone.
 * An employee's module access = own rows in tbl_employee_permission + the modules of all those roles,
 * each module counted once. Admin has every module.
 *
 * Departments are not linked to any role here; that is set up in Roles & Permissions.
 * The Administrator Account (employee 1) gets the Admin role so the system stays manageable.
 */
return new class extends Migration
{
    private $roles = [
        'admin' => ['Super Admin', 'Complete access to every module, employee and setting', []],
        'hr' => ['HR', 'Employees, attendance, leaves, shifts and HR requests of every employee', [
            '/employees', '/employees-managers', '/employee-birthdays', '/employee-work-anniversay', '/gadgets-inventory', '/gadgets-allotment',
            '/meetings', '/attendance', '/attendance-requests', '/daily-attendance-report', '/wfh-requests', '/employees-leaves', '/manage-leaves',
            '/interviews', '/employee-onboarding', '/holidays', '/departments', '/designations', '/vehicles', '/resignations', '/alerts',
            '/requisition', '/letters', '/all-tickets', '/rooms', '/locations', '/candiddate-interviews', '/employee-devices', '/linked-in-post',
            '/shift-management', '/overtime-management', '/organogram', '/employee-letter', '/letter-template', '/machine-attendance-logs',
            '/leave-management', '/polling', '/shift-time-request', '/monthly-attendance', '/employment-status', '/attendance-devices',
            '/reports', '/attendance-locker', '/employee-group', '/leave-policy', '/employee-location', '/attendance-adjustment',
        ]],
        'finance' => ['Finance', 'Payroll, salary, loans, expenses and tax of every employee', [
            '/payroll', '/petty-cash', '/daily-expense', '/loans', '/all-salary-request', '/all-loan-request', '/tax-slab', '/tax-collection',
            '/employee-expense', '/overtime-management', '/employee-bank', '/daily-wages-payroll', '/deduction-policy', '/funds', '/employee-fund',
        ]],
        'manager' => ['Manager', 'Approvals and records of the employee\'s team', [
            '/attendance', '/attendance-requests', '/wfh-requests', '/employees-leaves', '/shift-management', '/shift-time-request',
            '/overtime-management', '/employee-expense', '/requisition', '/resignations', '/meetings',
        ]],
        'employee' => ['Employee', 'Self service', [
            '/dashboard', '/my-attendance', '/my-attendance-request', '/my-leaves', '/my-interviews', '/payslip', '/my-meetings',
            '/my-wfh-requests', '/employee-resignation', '/my-salary-request', '/my-loan', '/my-tickets', '/my-requisition', '/my-expense',
            '/my-shift-time', '/employee-birthdays', '/employee-work-anniversay', '/organogram',
        ]],
    ];

    public function up()
    {
        if (!Schema::hasTable('tbl_roles')) {
            Schema::create('tbl_roles', function (Blueprint $table) {
                $table->bigIncrements('role_id');
                $table->string('role_key', 50)->unique();
                $table->string('role_name', 100);
                $table->string('role_description', 255)->nullable();
                $table->boolean('role_is_system')->default(false);
                $table->timestamp('role_created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('tbl_role_permissions')) {
            Schema::create('tbl_role_permissions', function (Blueprint $table) {
                $table->bigIncrements('role_permission_id');
                $table->unsignedBigInteger('role_id');
                $table->string('module_url', 191);
                $table->unique(['role_id', 'module_url']);
            });
        }

        if (!Schema::hasTable('tbl_role_departments')) {
            Schema::create('tbl_role_departments', function (Blueprint $table) {
                $table->bigIncrements('role_department_id');
                $table->unsignedBigInteger('role_id');
                $table->integer('department_id');
                $table->unique(['role_id', 'department_id']);
                $table->index('department_id');
            });
        }

        if (!Schema::hasTable('tbl_employee_roles')) {
            Schema::create('tbl_employee_roles', function (Blueprint $table) {
                $table->bigIncrements('employee_role_id');
                $table->integer('employee_id');
                $table->unsignedBigInteger('role_id');
                $table->unique(['employee_id', 'role_id']);
                $table->index('role_id');
            });
        }

        $modules = DB::table('tbl_system_modules')->pluck('module_url')->toArray();
        foreach ($this->roles as $key => [$name, $description, $role_modules]) {
            $role_id = DB::table('tbl_roles')->where('role_key', $key)->value('role_id');
            if ($role_id) {
                continue;
            }
            $role_id = DB::table('tbl_roles')->insertGetId([
                'role_key' => $key, 'role_name' => $name, 'role_description' => $description, 'role_is_system' => true,
            ]);
            foreach (array_intersect($role_modules, $modules) as $module_url) {
                DB::table('tbl_role_permissions')->insertOrIgnore(['role_id' => $role_id, 'module_url' => $module_url]);
            }
        }

        $admin_role = DB::table('tbl_roles')->where('role_key', 'admin')->value('role_id');
        if (DB::table('tbl_employees')->where('employee_id', 1)->exists()) {
            DB::table('tbl_employee_roles')->insertOrIgnore(['employee_id' => 1, 'role_id' => $admin_role]);
        }

        // SAP attendance was removed from the product
        DB::table('tbl_employee_permission')->where('module_url', '/sap-attendance')->delete();
        DB::table('tbl_system_modules')->where('module_url', '/sap-attendance')->delete();
    }

    public function down()
    {
        Schema::dropIfExists('tbl_employee_roles');
        Schema::dropIfExists('tbl_role_departments');
        Schema::dropIfExists('tbl_role_permissions');
        Schema::dropIfExists('tbl_roles');
    }
};
