<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes on the columns the app joins and filters on. Every table only had a
 * primary key, so each lookup by employee / module / date was a full scan.
 */
return new class extends Migration
{
    private $indexes = [
        'tbl_employees' => [
            ['employee_status'], ['employee_department'], ['employee_designation'], ['employee_company'],
            ['employee_location'], ['employee_senior_manager'], ['employee_junior_manager'],
            ['employee_isactive'], ['employee_code'], ['employee_group'],
        ],
        'tbl_attendance' => [
            ['att_employee_id', 'att_for_year', 'att_for_month', 'att_for_date'],
            ['att_for_year', 'att_for_month', 'att_for_date'],
            ['att_employee_id', 'att_comp_date'],
            ['att_comp_date'],
        ],
        'tbl_personal_informations' => [['employeeid']],
        'tbl_employee_permission' => [['employee_id', 'module_url']],
        'tbl_system_modules' => [['module_url']],
        'tbl_leaves_applied' => [['leave_applied_by'], ['leave_approved_by_hr'], ['leave_applied_for_date']],
        'tbl_leave_entitlement' => [['leaves_employee_id', 'leave_type_id']],
        'tbl_notifications' => [
            ['notification_to_employee'], ['notification_to_manager1'], ['notification_to_manager2'],
            ['notification_to_hr'], ['notification_from'],
        ],
        'tbl_notification_status' => [['viewed_by', 'viewed_notification']],
        'tbl_activity_log' => [['activity_login_history_id']],
        'tbl_login_history' => [['login_history_employee_id'], ['login_history_session_token']],
        'tbl_request_approval' => [['request_module_id', 'request_id'], ['request_approver_id']],
        'tbl_advance_salary_request' => [['employee_id'], ['effective_payroll_period']],
        'tbl_work_from_homes' => [['wfh_employee_id'], ['wfh_request_date']],
        'tbl_attendance_requests' => [['request_employee_id'], ['request_attendance_date']],
        'tbl_loan_request' => [['request_employee_id']],
        'tbl_loans' => [['loan_request_id'], ['employee_code']],
        'tbl_loan_installments' => [['loan_installment_loan_id'], ['loan_installment_payroll_id']],
        'tbl_employee_salary' => [['salary_employee_id']],
        'tbl_payroll_calculation' => [['pc_employee_id']],
        'tbl_payroll_calculation_deduction' => [['pc_deduction_pc_id']],
        'tbl_payroll_calculation_earning' => [['pc_earning_pc_id']],
        'tbl_payroll_attempts' => [['employee_id']],
        'tbl_employees_location' => [['emp_loc_employee_id'], ['emp_loc_loc_id']],
        'tbl_employement_period' => [['employee_id']],
        'tbl_shift_management' => [['employee_id']],
        'tbl_shift_request' => [['request_employee_id']],
        'tbl_shift_request_time_log' => [['shift_request_id'], ['shift_employee_id']],
        'tbl_mobile_access' => [['mobile_employee_id']],
        'tbl_employee_token' => [['employee_token_emp_id']],
        'tbl_employees_devices' => [['device_employee_id']],
        'tbl_alert_status' => [['alert_status_employee_id', 'alert_status_alert_id']],
        'tbl_meeting_users' => [['users_meeting_id'], ['user_id']],
        'tbl_meetings' => [['host_id']],
        'tbl_over_time' => [['ovt_emp_id'], ['ovt_payroll_id']],
        'tbl_employee_expense' => [['emp_expense_employee']],
        'tbl_employee_expense_amount' => [['emp_expense_exp_id']],
        'tbl_resignation_letters' => [['resignation_employee']],
        'tbl_daily_wages' => [['dw_employee_id']],
        'tbl_machine_attendance_logs' => [['machine_id']],
        'tbl_designations' => [['department_id']],
        'tbl_employee_documents' => [['employee_id']],
        'tbl_letters' => [['letter_employee_id']],
        'tbl_mac_address' => [['mac_address_employee_id']],
        'tbl_attendance_access' => [['access_employee_id']],
        'tbl_attendance_request_time' => [['attendance_req_id']],
        'tbl_polling_status' => [['polling_employee_id'], ['polling_option_id']],
        'tbl_polling_options' => [['polling_id']],
        'tbl_help_desk_for_employees' => [['hdf_employee_id'], ['hd_id']],
        'tbl_help_desk_for_departments' => [['hdf_department_id'], ['hd_id']],
        'tbl_help_desk_ticket_status' => [['help_desk_id']],
        'tbl_gadget_allotment' => [['allotment_employee_id']],
        'tbl_vehicles_allotment' => [['allotment_employee_id']],
        'tbl_employees_funds' => [['ef_employee_id']],
        'tbl_continous_latecomers' => [['employee_id']],
        'tbl_deleted_attendance' => [['att_emp_id']],
        'tbl_offline_attendance_object' => [['att_offline_emp_id']],
        'tbl_interview_status' => [['main_interview_id']],
        'tbl_requisition_status' => [['main_requisition_id']],
        'tbl_salary' => [['salary_employee_id']],
        'tbl_earning' => [['earning_salary_id']],
        'tbl_deduction' => [['deduction_salary_id']],
    ];

    public function up()
    {
        foreach ($this->indexes as $table => $sets) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($sets as $columns) {
                if (!Schema::hasColumns($table, $columns)) {
                    continue;
                }
                $name = $this->indexName($table, $columns);
                if ($this->indexExists($table, $name)) {
                    continue;
                }
                $cols = implode(', ', array_map(function ($column) use ($table) {
                    return $this->indexColumn($table, $column);
                }, $columns));
                DB::statement("ALTER TABLE `$table` ADD INDEX `$name` ($cols)");
            }
        }
    }

    public function down()
    {
        foreach ($this->indexes as $table => $sets) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($sets as $columns) {
                $name = $this->indexName($table, $columns);
                if ($this->indexExists($table, $name)) {
                    DB::statement("ALTER TABLE `$table` DROP INDEX `$name`");
                }
            }
        }
    }

    private function indexName($table, array $columns)
    {
        $name = 'perf_' . substr($table, 4) . '_' . implode('_', $columns);
        return strlen($name) > 64 ? substr($name, 0, 55) . '_' . substr(md5($name), 0, 8) : $name;
    }

    private function indexExists($table, $name)
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $name)
            ->exists();
    }

    // long text columns can only be indexed by prefix
    private function indexColumn($table, $column)
    {
        $type = DB::table('information_schema.columns')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->value('data_type');

        return in_array($type, ['text', 'mediumtext', 'longtext']) ? "`$column`(191)" : "`$column`";
    }
};
