<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\{Employees, AttendanceRequest, WFH, LeaveRequest, AdvanceSalary, LoanRequest, EmployeeExpense, EmploymentPeriod, Attendance, RplLeave};

trait LoginEmployee {

	public static function get_login_user_detail(){

		$get_login_user = Employees::join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id')
		->where('employee_id', session('user_session'))
		->get(['employee_id','employee_code','employee_email','employee_name','employee_doj','employee_designation','employee_department','employee_offday1','employee_shift_flexibilty','employee_ra','employee_ra2','employee_can_ra','employee_isactive','employee_password','dob','employee_jobtype','profile_avatar','employee_earning_type'])
		->toArray();

		return $get_login_user;

	}


    public static function get_token_user_detail($token){

		$get_login_user = Employees::join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id')
		->join('tbl_employee_token','tbl_employee_token.employee_token_emp_id','tbl_employees.employee_id')
        ->where('employee_token',$token)
		->get(['employee_id','employee_code','employee_email','employee_name','employee_doj','employee_designation','employee_department','employee_offday1','employee_shift_flexibilty','employee_ra','employee_ra2','employee_can_ra','employee_isactive','dob','employee_jobtype','profile_avatar'])
		->toArray();

		return $get_login_user;

	}

    public function employee_half_day_shift_time($employees_shift_start,$shift_time){
         
        $employees_shift_start_parsed = date_parse($employees_shift_start);
        $employees_shift_start_seconds = $employees_shift_start_parsed['hour'] * 3600 + $employees_shift_start_parsed['minute'] * 60 + $employees_shift_start_parsed['second'];

        $employees_shift_whole_time = $shift_time;
        $employees_shift_time_parsed = date_parse($employees_shift_whole_time);
        $employees_shift_time_parsed_seconds = $employees_shift_time_parsed['hour'] * 3600 + $employees_shift_time_parsed['minute'] * 60 + $employees_shift_time_parsed['second'];
        
        $t_shift_time_second = $employees_shift_time_parsed_seconds/2;


        $total_time = $t_shift_time_second + $employees_shift_start_seconds;

        $hours = floor($total_time / 3600) % 24;
        $minutes = floor(($total_time / 60) % 60);
        $seconds =$total_time % 60;
        
        $employees_shift_end_time = sprintf("%02d:%02d:%02d",  $hours,   $minutes,  $seconds);
       
        return  $employees_shift_end_time;
    }

    public function get_employee_hierarchy($manager_id, $status) {
    
        if($status != 1){
            $status == 0;
        }

        $all_company_employees = Employees::where('employee_isactive', $status)->get(['employee_name','employee_id','employee_ra','employee_ra2','employee_can_ra'])->toArray();
        $my_all_employees = array();
        if(!empty($all_company_employees)){
            foreach ($all_company_employees as $employee) {
                if ($employee['employee_ra'] == $manager_id || $employee['employee_ra2'] == $manager_id) {
                    $my_all_employees[] = $employee;
                    if($employee['employee_can_ra'] == 1 && $employee['employee_ra'] != $employee['employee_id']){
                        $my_all_employees = array_merge($my_all_employees, $this->get_employee_hierarchy($employee['employee_id'], $status));
                    }
                }
            }
        }   
        return $my_all_employees;
    }

    public function get_employee_hierarchy_unique($login_employee_id, $status){
        $all_team_members = $this->get_employee_hierarchy($login_employee_id, $status);
        $all_members = array_column($all_team_members,'employee_id');
        $all_team_members = array_unique($all_members);
        return $all_team_members;
    }

    public static function get_all_date_from_month($month,$year,$format){
      
        $total_days = date('t',strtotime($year.'-'.$month.'-01'));
      
        $dates = array();
        for($i=1; $i<=$total_days; $i++)
        {
            
            $time= mktime(12, 0, 0, $month,$i,$year);
            if (date($month, $time) == $month){

                if($format == 1){
                    if($i <= 9){
                        $dates[]= date($year.'-'.$month.'-0'.$i, $time);
                    }else{
                        $dates[]= date($year.'-'.$month.'-'.$i, $time);
                    }
                }else{
                    if($i <= 9){
                        $dates[]= date('0'.$i.'-'.$month.'-'. $year, $time);
                    }else{
                        $dates[]= date($i.'-'.$month.'-'. $year, $time);
                    }
                }
            }
               
        }

        return  $dates;
        
    }

    public function check_request_validation($request_id,$module_id,$employee_id=null){

        $employee_id = $employee_id ?? session('user_session');
        
        if($module_id == 1){ //Attendance
            $request_data = AttendanceRequest::where('attendance_request_id',$request_id)->where('request_employee_id',$employee_id)->get(['attendance_request_id'])->toArray();
        }

        if($module_id == 2){ //Leave
            $request_data = LeaveRequest::where('leave_applied_id',$request_id)->where('leave_applied_by',$employee_id)->get(['leave_applied_id'])->toArray();
        }

        if($module_id == 3){ //Work From Home
            $request_data = WFH::where('wfh_request_id',$request_id)->where('wfh_employee_id',$employee_id)->get(['wfh_request_id'])->toArray();
        }

        if($module_id == 4){ //AdvanceSalary
            $request_data = AdvanceSalary::where('advance_salary_request_id',$request_id)->where('employee_id',$employee_id)->get(['advance_salary_request_id'])->toArray();
        }

        if($module_id == 5){ //Loan
            $request_data = LoanRequest::where('loan_request_id',$request_id)->where('request_employee_id',$employee_id)->get(['loan_request_id'])->toArray();
        }

        if($module_id == 6){ //Reimbursement
            $request_data = EmployeeExpense::where('emp_expense_id',$request_id)->where('emp_expense_employee',$employee_id)->get(['emp_expense_id'])->toArray();
        }

        if($module_id == 7){ //RplLeave
            $request_data = RplLeave::where('rpl_leave_id',$request_id)->where('rpl_leave_applied_by',$employee_id)->get(['rpl_leave_id'])->toArray();
        }

        if(empty($request_data)){
            return TRUE;
        }else{
            return FALSE;
        }

       
    }

    public static function dynamic_year_drop_down(){
		
		$currentYear = date('Y');
		$startYear = 2020;
		$years = range($startYear, $currentYear);

		return $years;
		
	}


    protected function check_if_employee_permanent($leave_date, $employee_id, $custom_message){
       
        $leave_date = date_format(date_create($leave_date),"Y-m-d");
        $employment_status = EmploymentPeriod::where('employee_id', $employee_id)
        ->where('effective_date','<=',$leave_date )
        ->orderBy('employement_period_id','DESC')
        ->limit(1)
        ->get(['employee_id','employement_status_id','effective_date'])->toArray();

    
        if(empty($employment_status) || $leave_date < $employment_status[0]['effective_date'] || $employment_status[0]['employement_status_id'] != 3){
		
            $employee_detail = Employees::where('employee_id', $employee_id)->get(['employee_name'])->toArray();
			
            if(!empty($custom_message)){
                return [
                    'status' => FALSE,
                    'msg' => $employee_detail[0]['employee_name'].' can\'t avail Annual Leave(s) until he/she is permanent'
                ];
            }else{
                return[
                    'status' => FALSE,
                    'msg'=>'You can\'t avail Annual Leave(s) until you\'re permanent',
                ];
            }
        }else{
            return[
				'status' => TRUE,
				
			];
        }
    }

    function removeSpecialCharacters($filename) {
        $pattern = '/[^a-zA-Z0-9.\-_]/';
        $cleanedFilename = preg_replace($pattern, '', $filename);
    
        return $cleanedFilename;
    }

    public function get_latest_attendance_record(){
        $get_login_user_id = session('user_session');
        $get_todays_checkin = Attendance::selectRaw('att_check_in_status, att_check_in_time, att_check_out_status, att_check_out_time, att_for_date + CHAR(45) + att_for_month + CHAR(45) + att_for_year as attendance_date')->where('att_for_date', date('d'))->where('att_for_month', date('m'))->where('att_for_year', date('Y'))->where('att_employee_id', $get_login_user_id)->get()->toArray();
        if(empty($get_todays_checkin)){
            $get_yesterdays_checkin = Attendance::selectRaw('att_check_in_status, att_check_in_time, att_check_out_status, att_check_out_time, att_for_date + CHAR(45) + att_for_month + CHAR(45) + att_for_year as attendance_date')->where('att_for_date', date('d',strtotime("-1 days")))->where('att_for_month', date('m',strtotime("-1 days")))->where('att_for_year', date('Y',strtotime("-1 days")))->where('att_employee_id', $get_login_user_id)->get()->toArray();
            if(!empty($get_yesterdays_checkin)){
                $letest_check_in['attendance_record'] = $get_yesterdays_checkin[0];
            }else{
                $letest_check_in['attendance_record'] = '00:00:00';
            }
        }else{
            $letest_check_in['attendance_record'] = $get_todays_checkin[0];
        }
        return $letest_check_in;
    }
    
}
