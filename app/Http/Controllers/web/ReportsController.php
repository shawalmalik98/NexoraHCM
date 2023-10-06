<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Employees, Leave, Notification, EmployeeInformation, EmployeeDocument, Department, Designation, Bank, Permission, EmployeeSalary, OnBoardingEmployee, GadgetAllotment, Vehicle, InterviewCandidate, Interview, SystemModule, OfficeLocation, EmployeeDevice,ShiftManagement, HrmSetting,Holiday,Attendance,VehicleAllotment,Resignation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    use LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee;
    const EMPLOYEEREPORT = "employee-report";
    const ATTENDANCEREPORT = "attendance-report";

    protected function employee_demographics(Request $request){
        if(!empty($request->q)){
            if($request->q == 'Active'){
                $where_clause = 'employee_isactive = 1';
            }elseif($request->q == 'Deactive'){
                $where_clause = 'employee_isactive = 0';
            }else{
                $where_clause = 'employee_isactive != 2';
            }
        }else{
            $where_clause = 'employee_isactive != 2';
        }
        $title = Self::EMPLOYEEREPORT;
        $login_user = $this->get_login_user_detail();
        $get_total_employees = Employees::count();
        $get_active_employees = Employees::where('employee_isactive', 1)->count();
        $get_deactive_employees = Employees::where('employee_isactive', 0)->count();
        $get_onboard_employees = OnBoardingEmployee::where('onboard_status', 0)->count();
        $get_resignated_employees = Resignation::join('tbl_employees','tbl_resignation_letters.resignation_employee','=','tbl_employees.employee_id')->where('resignation_status', 2)->where('employee_isactive', 1)->count();
        $get_estimated_salary = EmployeeSalary::join('tbl_employees','tbl_employee_salary.salary_employee_id','=','tbl_employees.employee_id')->where('employee_isactive', 1)->sum('salary_amount');
        $head_count_office_wise = Employees::selectRaw("tbl_office_locations.location_title , COUNT(tbl_employees.employee_id) as total_employees , COUNT(CASE WHEN tbl_employees.employee_gender = 'Male' THEN 0 END) AS male_count , COUNT(CASE WHEN tbl_employees.employee_gender = 'Female' THEN 0 END) AS female_count")->join('tbl_office_locations','tbl_employees.employee_location','=','tbl_office_locations.location_id')->groupBy('tbl_employees.employee_location','tbl_office_locations.location_title')->whereRaw($where_clause)->get()->toArray();
        $head_count_by_employeement_wise = Employees::selectRaw('COUNT(tbl_employees.employee_id) as total_employee , employement_status_name as employement_type')->join('tbl_employement_status','tbl_employees.employee_status','=','tbl_employement_status.employement_status_id')->where('employee_isactive',1)->groupBy('employee_status','employement_status_name')->get()->toArray();
        $age_range = DB::select('SELECT 
        COUNT(CASE WHEN DATEDIFF(YEAR, p.dob , GETDATE()) >= 0 AND DATEDIFF(YEAR, p.dob , GETDATE()) <= 10 THEN 0 END) as between_10_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.dob , GETDATE()) >= 11 AND DATEDIFF(YEAR, p.dob , GETDATE()) <= 20 THEN 0 END) as between_20_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.dob , GETDATE()) >= 21 AND DATEDIFF(YEAR, p.dob , GETDATE()) <= 30 THEN 0 END) as between_30_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.dob , GETDATE()) >= 31 AND DATEDIFF(YEAR, p.dob , GETDATE()) <= 40 THEN 0 END) as between_40_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.dob , GETDATE()) >= 41 AND DATEDIFF(YEAR, p.dob , GETDATE()) <= 50 THEN 0 END) as between_50_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.dob , GETDATE()) >= 51 AND DATEDIFF(YEAR, p.dob , GETDATE()) <= 60 THEN 0 END) as between_60_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.dob , GETDATE()) >= 61 AND DATEDIFF(YEAR, p.dob , GETDATE()) <= 70 THEN 0 END) as between_70_years , 
        COUNT(CASE WHEN DATEDIFF(YEAR, p.dob , GETDATE()) >= 71 AND DATEDIFF(YEAR, p.dob , GETDATE()) <= 70 THEN 0 END) as between_80_years
        FROM tbl_personal_informations p INNER JOIN tbl_employees ON p.employeeid = tbl_employees.employee_id where ' . $where_clause);
        $age_range_employees = json_decode(json_encode($age_range), TRUE);
        $tenure_wise = DB::select("SELECT
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 0 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 1 THEN 0 END) as below_1_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 0 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 1 AND p.employee_gender = 'Female' THEN 0 END) as below_1_years_female ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 0 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 1 AND p.employee_gender = 'Male' THEN 0 END) as below_1_years_male ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 1 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 2 THEN 0 END) as below_2_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 1 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 2 AND p.employee_gender = 'Female' THEN 0 END) as below_2_years_female ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 1 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 2 AND p.employee_gender = 'Male' THEN 0 END) as below_2_years_male ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 2 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 3 THEN 0 END) as below_3_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 2 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 3 AND p.employee_gender = 'Female' THEN 0 END) as below_3_years_female ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 2 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 3 AND p.employee_gender = 'Male' THEN 0 END) as below_3_years_male ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 3 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 4 THEN 0 END) as below_4_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 3 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 4 AND p.employee_gender = 'Female' THEN 0 END) as below_4_years_female ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 3 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 4 AND p.employee_gender = 'Male' THEN 0 END) as below_4_years_male ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 4 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 5 THEN 0 END) as below_5_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 4 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 5 AND p.employee_gender = 'Female' THEN 0 END) as below_5_years_female ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 4 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 5 AND p.employee_gender = 'Male' THEN 0 END) as below_5_years_male ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 5 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 6 THEN 0 END) as below_6_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 5 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 6 AND p.employee_gender = 'Female' THEN 0 END) as below_6_years_female ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 5 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 6 AND p.employee_gender = 'Male' THEN 0 END) as below_6_years_male ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 6 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 7 THEN 0 END) as below_7_years ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 6 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 7 AND p.employee_gender = 'Female' THEN 0 END) as below_7_years_female ,
        COUNT(CASE WHEN DATEDIFF(YEAR, p.employee_doj , GETDATE()) >= 6 AND DATEDIFF(YEAR, p.employee_doj , GETDATE()) < 7 AND p.employee_gender = 'Male' THEN 0 END) as below_7_years_male
        FROM tbl_employees p");
        $tenure_wise_employees = json_decode(json_encode($tenure_wise), TRUE);
        $get_employees_designation_wise = DB::select('SELECT COUNT(e.employee_id) total_employees , d.designation_name FROM tbl_employees e INNER JOIN tbl_designations d ON e.employee_designation = d.designation_id GROUP BY d.designation_name');
        $get_designation_wise_employees = json_decode(json_encode($get_employees_designation_wise), TRUE);
        $get_employee_count_hiring_wise = DB::select('SELECT COUNT(e.employee_id) as total_empoloyees , YEAR(e.employee_doj) as year FROM tbl_employees e GROUP BY YEAR(e.employee_doj)');
        $get_employee_count_hiring_wise = json_decode(json_encode($get_employee_count_hiring_wise), TRUE);
        $get_managers_wise_employee = DB::select("SELECT COUNT(employee_id) total_employees , CASE WHEN employee_is_manager = '1' THEN 'Managers' ELSE 'Non Managers' END manager_type FROM tbl_employees WHERE employee_isactive = 1 GROUP BY employee_is_manager");
        $get_managers_wise_employee = json_decode(json_encode($get_managers_wise_employee), TRUE);
        $send_data = compact('title','login_user','get_total_employees','get_active_employees','get_deactive_employees','get_onboard_employees','get_resignated_employees','get_estimated_salary','head_count_office_wise','head_count_by_employeement_wise','age_range_employees','tenure_wise_employees','get_designation_wise_employees','get_employee_count_hiring_wise','get_managers_wise_employee');
        return view('reports.employee-report', $send_data);
    }

    protected function attendance_report(){
        $title = Self::ATTENDANCEREPORT;
        $login_user = $this->get_login_user_detail();
        $get_attendance_stats = DB::select("SELECT COUNT(CASE WHEN att_check_overall_status = 'Present' THEN 0 END) present_att, COUNT(CASE WHEN att_check_overall_status = 'Absent' THEN 0 END) present_absent, COUNT(CASE WHEN att_check_overall_status = 'Late Coming' THEN 0 END) late, COUNT(CASE WHEN att_check_overall_status = 'Early Out' THEN 0 END) early_out, COUNT(CASE WHEN att_check_overall_status = 'Half day' THEN 0 END) half_day, COUNT(CASE WHEN att_check_overall_status LIKE '%Holiday%' THEN 0 END) holiday, COUNT(CASE WHEN att_check_overall_status LIKE '%Leave%' THEN 0 END) leave_att FROM tbl_attendance");
        $get_attendance_stats = json_decode(json_encode($get_attendance_stats), TRUE);

        $get_check_in_device_wise = DB::select("SELECT (CASE WHEN att_check_in_source = '1' THEN 'Web Application' WHEN att_check_in_source = '2' THEN 'Mobile Application' WHEN att_check_in_source = '3' THEN 'Updated Attendance'  WHEN att_check_in_source = '4' THEN 'Machine Attendance' ELSE 'Not Defined' END) AS attendance_source, COUNT(attendance_id) as count_attendance FROM tbl_attendance WHERE att_check_in_status = 1 GROUP BY att_check_in_source");
        $get_check_in_device_wise = json_decode(json_encode($get_check_in_device_wise), TRUE);

        $get_check_out_device_wise = DB::select("SELECT (CASE WHEN att_check_out_source = '1' THEN 'Web Application' WHEN att_check_out_source = '2' THEN 'Mobile Application' WHEN att_check_out_source = '3' THEN 'Updated Attendance'  WHEN att_check_out_source = '4' THEN 'Machine Attendance' ELSE 'Not Defined' END) AS attendance_source, COUNT(attendance_id) as count_attendance FROM tbl_attendance WHERE att_check_out_status = 1 GROUP BY att_check_out_source");
        $get_check_out_device_wise = json_decode(json_encode($get_check_out_device_wise), TRUE);

        $get_over_all_att_device_wise = DB::select("SELECT (CASE WHEN att_check_out_source = '1' OR att_check_in_source = '1' THEN 'Web Application' WHEN att_check_out_source = '2' OR att_check_in_source = '2' THEN 'Mobile Application' WHEN att_check_out_source = '3' OR att_check_in_source = '3' THEN 'Updated Attendance' WHEN att_check_out_source = '4' OR att_check_in_source = '4' THEN 'Machine Attendance' ELSE 'Not Defined' END) AS attendance_source, COUNT(attendance_id) count_attendance FROM tbl_attendance WHERE att_check_out_status = 1 GROUP BY attendance_source");
        $get_over_all_att_device_wise = json_decode(json_encode($get_over_all_att_device_wise), TRUE);
        // dd($get_over_all_att_device_wise);
        $send_data = compact('title','login_user','get_attendance_stats','get_check_in_device_wise','get_check_out_device_wise','get_over_all_att_device_wise');
        return view('reports.attendance-report', $send_data);
    }
}
