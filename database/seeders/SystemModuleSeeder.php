<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemModule;
use Illuminate\Support\Facades\DB;

class SystemModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        SystemModule::Truncate();
        $system_module = SystemModule::get()->toArray();
     
        if(empty($system_module)){

            DB::insert("INSERT INTO tbl_system_modules (module_title, module_url, module_type,module_accessibility) VALUES
                ('Dashboard', '/dashboard', '0',1),
                ('My Attendance', '/my-attendance', '0',1),
                ('My Attendance Request', '/my-attendance-request', '0',1),
                ('My Leaves Request', '/my-leaves', '0',1),
                ('My Interviews', '/my-interviews', '0',1),
                ('My Payslips', '/payslip', '0',1),
                ('My Meetings', '/my-meetings', '0',1),
                ('My Work From Home Requests', '/my-wfh-requests', '0',0),
                ('My Resignation', '/employee-resignation', '0',1),
                ('My Salary Request', '/my-salary-request', '0',1),
                ('My Loan Request', '/my-loan', '0',1),
                ('My Help Desk', '/my-tickets', '0',1),
                ('Manage Employees', '/employees', '1',1),
                ('Employees Managers', '/employees-managers', '1',1),
                ('Employees Birthday', '/employee-birthdays', '1',1),
                ('Employees Work Anniversary', '/employee-work-anniversay', '1',1),
                ('Gadgets Inventory', '/gadgets-inventory', '1',1),
                ('Gadgets Allotment', '/gadgets-allotment', '1',1),
                ('All Meetings', '/meetings', '1',1),
                ('Detailed Attendance', '/attendance', '1',1),
                ('All Attendance Requests', '/attendance-requests', '1',1),
                ('Daily Attendance Report', '/daily-attendance-report', '1',1),
                ('All WFH Requests', '/wfh-requests', '0',0),
                ('All Leaves Requests', '/employees-leaves', '1',1),
                ('Leaves Entitlement', '/manage-leaves', '1',1),
                ('Manage Interviews', '/interviews', '1',1),
                ('Employee OnBoarding', '/employee-onboarding', '1',1),
                ('Manage Payroll', '/payroll', '1',1),
                ('Cash Inventory', '/petty-cash', '1',1),
                ('Daily Expenses', '/daily-expense', '1',1),
                ('Manage Loans', '/loans', '1',1),
                ('Holidays', '/holidays', '1',1),
                ('Manage Departments', '/departments', '1',1),
                ('Manage Designations', '/designations', '1',1),
                ('Manage Vehicles', '/vehicles', '1',1),
                ('Login History', '/login-history', '1',1),
                ('System Settings', '/system-settings', '1',1),
                ('System Error Log', '/system-error-logs', '1',1),
                ('All Resignation Letters', '/resignations', '1',1),
                ('All Alerts', '/alerts', '1',1),
                ('All Requisition', '/requisition', '1',1),
                ('My Requisition', '/my-requisition', '1',1),
                ('All Letters', '/letters', '1',1),
                ('All Advance Salary Request', '/all-salary-request', '1',1),
                ('All Loan Request', '/all-loan-request', '1',1),
                ('Employee Permissions', '/permissions', '1',1),
                ('All Help Desk', '/all-tickets', '1',1),
                ('Manage Tax Slab', '/tax-slab', '1',1),
                ('Income Tax Collection', '/tax-collection', '1',1),
                ('All Rooms', '/rooms', '1',1),
                ('All Locations', '/locations', '1',1),
                ('Interview Candidates', '/candiddate-interviews', '1',1),
                ('Employee Devices', '/employee-devices', '1',1),
                ('Linked Post', '/linked-in-post', '1',1),
                ('Shift Management', '/shift-management', '1',1),
                ('All Reimbursement Request', '/employee-expense', '1',1),
                ('Overtime Sheet', '/overtime-management', '1',1),
                ('Organogram', '/organogram', '1',1),
                ('My Reimbursement Request', '/my-expense', '1',1),
                ('My Shift Time', '/my-shift-time', '1',1),
                ('Employee Letter Template', '/employee-letter', '1',0),
                ('Letter Template', '/letter-template', '1',0),
                ('Attendance Machine Log', '/machine-attendance-logs', '1',1),
                ('Manage Leaves', '/leave-management', '1',1),
                ('Manage Polls', '/polling', '1',1),
                ('Shift Time Request', '/shift-time-request', '1',1),
                ('Employee Bank Detail', '/employee-bank', '1',1),
                ('Monthly Attendance', '/monthly-attendance', '1',1),
                ('Daily Wage', '/daily-wages-payroll', '1',1),
                ('Employment Status', '/employment-status', '1',1),
                ('Attendance Device', '/attendance-devices', '1',1),
                ('Employee Report', '/reports', '1',1),
                ('Attendance Locker', '/attendance-locker', '1',1),
                ('Employee Group', '/employee-group', '1',1),
                ('Leave Policy', '/leave-policy', '1',1),
                ('Attendance Deduction Policy','/deduction-policy', '1',1),
                ('Companies','/companies', '1',1),
                ('All Funds','/funds', '1',1),
                ('Employee Funds','/funds/employee-fund', '1',1)
            ");
        }
      

    }
}
