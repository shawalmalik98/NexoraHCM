<?php

namespace App\Traits;

use App\Models\Employees;
use App\Models\Attendance;
use App\Models\HrmSetting;
use App\Models\ShiftManagement;
use App\Models\Payroll;
use App\Models\Department;
use App\Models\AttendanceLocker;
use App\Models\WFH;
use App\Models\RequestApproval;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\DB;

trait AttendanceEmployee {

    protected function multiple_check_in($check_in , $login_user , $attendance_source = NULL, $att_request_approval = NULL){
		
		$employee_data = Employees::where('employee_id',$login_user)->get(['employee_id','employee_shift_flexibilty'])->toArray();
		
		$day = date("d", strtotime($check_in));
		$month = date("m", strtotime($check_in));
		$year = date("Y", strtotime($check_in));
		
		$get_attendance = Attendance::where('att_employee_id', $employee_data[0]['employee_id'])->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();
		
		if(!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false ){
			Attendance::where('attendance_id',$get_attendance[0]['attendance_id'])->delete();
		} 
		
		$employee_shift_start = $this->get_employee_today_shift($login_user,"in",date('Y-m-d',strtotime($check_in)));
		$employee_shift_end = $this->get_employee_today_shift($login_user,"out",date('Y-m-d',strtotime($check_in)));

		if(empty($get_attendance) || (!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false)){
			if(!empty($employee_data)){
				if($employee_data[0]['employee_shift_flexibilty'] != '1'){
					$employee_shift_start_hour = date("H", strtotime($employee_shift_start));
					$employee_shift_start_minute = date("i", strtotime($employee_shift_start));
					$exceed = strtotime("+1 hours", strtotime($employee_shift_start));
					$exceed_final = date('H:i', $exceed);
					$request_check_in_time = date("H", strtotime($check_in));
					$check_in_grace_period = HrmSetting::pluck('check_in_flexibility_time')->toArray();
					if($employee_shift_start_hour == $request_check_in_time){
						if(date('i' , strtotime($check_in)) <= $employee_shift_start_minute + $check_in_grace_period[0]){
							$mark_Attendance = new Attendance();
							$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
							$mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_Attendance->att_check_out_time = '00:00:00';
							$mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_out_status = 0;
							$mark_Attendance->att_check_in_source = 3;
							$mark_Attendance->att_shift_start_time = $employee_shift_start;
							$mark_Attendance->att_shift_end_time = $employee_shift_end;
							$mark_Attendance->att_check_overall_status = 'Present';
							
							$mark_Attendance->att_for_date = $day;
							$mark_Attendance->att_for_month = $month;
							$mark_Attendance->att_for_year = $year;
							$mark_Attendance->att_check_in_source = $attendance_source;
							$mark_Attendance->att_request_approval = $att_request_approval;
							$process = $mark_Attendance->save();
							

						}else{
							$mark_Attendance = new Attendance();
							$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
							$mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_Attendance->att_check_out_time = '00:00:00';
							$mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_out_status = 0;
							$mark_Attendance->att_check_in_source = 3;
							$mark_Attendance->att_shift_start_time = $employee_shift_start;
							$mark_Attendance->att_shift_end_time = $employee_shift_end;
							$mark_Attendance->att_check_overall_status = 'Late Coming';
							
							$mark_Attendance->att_for_date = $day;
							$mark_Attendance->att_for_month = $month;
							$mark_Attendance->att_for_year = $year;
							$mark_Attendance->att_check_in_source = $attendance_source;
							$mark_Attendance->att_request_approval = $att_request_approval;
							$process = $mark_Attendance->save();

						}
					}elseif(date('H' , strtotime($check_in)) > $employee_shift_start_hour){
						if(date('H:i', strtotime($check_in)) <= $exceed_final ){
							$mark_Attendance = new Attendance();
							$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
							$mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_Attendance->att_check_out_time = '00:00:00';
							$mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_out_status = 0;
							$mark_Attendance->att_check_in_source = 3;
							$mark_Attendance->att_shift_start_time = $employee_shift_start;
							$mark_Attendance->att_shift_end_time = $employee_shift_end;
							$mark_Attendance->att_check_overall_status = 'Late Coming';
							
							$mark_Attendance->att_for_date = $day;
							$mark_Attendance->att_for_month = $month;
							$mark_Attendance->att_for_year = $year;
							$mark_Attendance->att_check_in_source = $attendance_source;
							$mark_Attendance->att_request_approval = $att_request_approval;
							$process = $mark_Attendance->save();

							
						}else{
							$mark_Attendance = new Attendance();
							$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
							$mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_Attendance->att_check_out_time = '00:00:00';
							$mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_out_status = 0;
							$mark_Attendance->att_check_in_source = 3;
							$mark_Attendance->att_shift_start_time = $employee_shift_start;
							$mark_Attendance->att_shift_end_time = $employee_shift_end;
							$mark_Attendance->att_check_overall_status = 'Half day';
							
							$mark_Attendance->att_for_date = $day;
							$mark_Attendance->att_for_month = $month;
							$mark_Attendance->att_for_year = $year;
							$mark_Attendance->att_check_in_source = $attendance_source;
							$mark_Attendance->att_request_approval = $att_request_approval;
							$process = $mark_Attendance->save();
						}
					}else{
						$mark_Attendance = new Attendance();
						$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
						$mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
						$mark_Attendance->att_check_out_time = '00:00:00';
						$mark_Attendance->att_check_in_status = 1;
						$mark_Attendance->att_check_out_status = 0;
						$mark_Attendance->att_check_in_source = 3;
						$mark_Attendance->att_shift_start_time = $employee_shift_start;
						$mark_Attendance->att_shift_end_time = $employee_shift_end;
						$mark_Attendance->att_check_overall_status = 'Present';
						
						$mark_Attendance->att_for_date = $day;
						$mark_Attendance->att_for_month = $month;
						$mark_Attendance->att_for_year = $year;
						$mark_Attendance->att_check_in_source = $attendance_source;
						$mark_Attendance->att_request_approval = $att_request_approval;
						$process = $mark_Attendance->save();

					}
				}else{
					$mark_Attendance = new Attendance();
					$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
					$mark_Attendance->att_check_in_time = date('H:i:s', strtotime($check_in));
					$mark_Attendance->att_check_out_time = '00:00:00';
					$mark_Attendance->att_check_in_status = 1;
					$mark_Attendance->att_check_out_status = 0;
					$mark_Attendance->att_check_in_source = 3;
					$mark_Attendance->att_shift_start_time = $employee_shift_start;
					$mark_Attendance->att_shift_end_time = $employee_shift_end;
					$mark_Attendance->att_check_overall_status = 'Present';
					
					$mark_Attendance->att_for_date = $day;
					$mark_Attendance->att_for_month = $month;
					$mark_Attendance->att_for_year = $year;
					$mark_Attendance->att_check_in_source = $attendance_source;
					$mark_Attendance->att_request_approval = $att_request_approval;
					$process = $mark_Attendance->save();

				}
			}else{
				
			}    
		}
			
	}

    protected function multiple_check_in_update($check_in,$login_user){
			
		$employee_data = Employees::where('employee_id',$login_user)->get(['employee_id','employee_shift_flexibilty'])->toArray();
	
		$day = date("d", strtotime($check_in));
		$month = date("m", strtotime($check_in));
		$year = date("Y", strtotime($check_in));

		$employee_shift_start = $this->get_employee_today_shift($login_user,"in",date('Y-m-d',strtotime($check_in)));
		$employee_shift_end = $this->get_employee_today_shift($login_user,"out",date('Y-m-d',strtotime($check_in)));
		
		$get_attendance = Attendance::where('att_employee_id', $employee_data[0]['employee_id'])->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();
		
		if(!empty($get_attendance) || (!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false)){
			if(!empty($employee_data)){
				if($employee_data[0]['employee_shift_flexibilty'] != '1'){
					$employee_shift_start_hour = date("H", strtotime($employee_shift_start));
					$employee_shift_start_minute = date("i", strtotime($employee_shift_start));
					$exceed = strtotime("+1 hours", strtotime($employee_shift_start));
					$exceed_final = date('H:i', $exceed);
					$check_in_grace_period = HrmSetting::pluck('check_in_flexibility_time')->toArray();
					$request_check_in_time = date("H", strtotime($check_in));

					if($employee_shift_start_hour == $request_check_in_time){
						if(date('i' , strtotime($check_in)) <= $employee_shift_start_minute + $check_in_grace_period[0]){
                            $mark_Attendance = Attendance::find($get_attendance[0]['attendance_id']);
							$mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_Attendance->att_check_overall_status = 'Present';
                            $mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_in_source = 3;
							$mark_Attendance->att_request_approval = 1;
							$process = $mark_Attendance->save();
						

						}else{
						
                            $mark_Attendance = Attendance::find($get_attendance[0]['attendance_id']);
							$mark_Attendance->att_check_in_time =date('H:i:s',strtotime($check_in));
							$mark_Attendance->att_check_overall_status = 'Late Coming';
                            $mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_in_source = 3;
							$mark_Attendance->att_request_approval = 1;
							$process = $mark_Attendance->save();

						}
					}elseif(date('H' , strtotime($check_in)) > $employee_shift_start_hour){
						if(date('H:i', strtotime($check_in)) <= $exceed_final ){

                            $mark_Attendance = Attendance::find($get_attendance[0]['attendance_id']);
							$mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_Attendance->att_check_overall_status = 'Late Coming';
                            $mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_in_source = 3;
							$mark_Attendance->att_request_approval = 1;
							$process = $mark_Attendance->save();

						}else{
                            $mark_Attendance = Attendance::find($get_attendance[0]['attendance_id']);
							$mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_Attendance->att_check_overall_status = 'Half day';
                            $mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_in_source = 3;
							$mark_Attendance->att_request_approval = 1;
							$process = $mark_Attendance->save();

						}
					}else{
                        $mark_Attendance = Attendance::find($get_attendance[0]['attendance_id']);
                        $mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
                        $mark_Attendance->att_check_overall_status = 'Present';
                        $mark_Attendance->att_check_in_status = 1;
						$mark_Attendance->att_check_in_source = 3;
						$mark_Attendance->att_request_approval = 1;
                        $process = $mark_Attendance->save();

					}
				}else{
                    $mark_Attendance = Attendance::find($get_attendance[0]['attendance_id']);
                    $mark_Attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
                    $mark_Attendance->att_check_overall_status = 'Present';
                    $mark_Attendance->att_check_in_status = 1;
					$mark_Attendance->att_check_in_source = 3;
					$mark_Attendance->att_request_approval = 1;
                    $process = $mark_Attendance->save();

					
				}
			}else{
				
			}    
		}
			
	}

	protected function multiple_check_out($check_out_time,$actual_check_out,$login_user){

		$login_user = Employees::where('employee_id',$login_user)->get(['employee_id','employee_shift_flexibilty'])->toArray();
	
		if(!empty($login_user)){
			
			$employee_id = 	$login_user[0]['employee_id'];
				
			$day = date("d", strtotime($check_out_time));
			$month = date("m", strtotime($check_out_time));
			$year = date("Y", strtotime($check_out_time));

			$employee_shift_start = $this->get_employee_today_shift($login_user,"in",date('Y-m-d',strtotime($check_out_time)));
			$employee_shift_end = $this->get_employee_today_shift($login_user,"out",date('Y-m-d',strtotime($check_out_time)));
		
			$get_attendance = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();
			
			if(!empty($get_attendance)){
			
				if($get_attendance[0]['att_check_out_time'] == '00:00:00' || $get_attendance[0]['att_check_out_status'] == '0' || $actual_check_out != '00:00:00'){

					$check_out = Attendance::find($get_attendance[0]['attendance_id']);

				
					$check_out->att_check_out_time = date('H:i:s', strtotime($check_out_time));

					$check_out->att_check_out_status = 1;
					$check_out->att_check_out_source = 3;
					$check_out->att_request_approval = '1';
					
					$employee_official_time = $this->calculate_minutes($employee_shift_start,$employee_shift_end);
					$current_shift_time = $this->calculate_minutes($get_attendance[0]['att_check_in_time'] , date('H:i:s', strtotime($check_out_time)));
					$diff  = $employee_official_time - $current_shift_time;
					
					$check_out_grace_period = HrmSetting::pluck('check_out_flexibility_time')->toArray();
					
					if($current_shift_time >= $employee_official_time){

						$check_out->att_check_overall_status = "Present";

					}elseif($diff >= 120){

						$check_out->att_check_overall_status = "Half day";

					}elseif($diff > $check_out_grace_period[0] && $diff < 120){

						$check_out->att_check_overall_status = "Early out";
					}

					$check_out->save();

				}
			}
		}
	}

    protected function calculate_minutes($time_start,$time_end){

        if(strtotime($time_start)>strtotime($time_end)){
            $time_start =  (substr($time_start,0,2) - 12) . (substr($time_start,2));
            $time_end =  (substr($time_end,0,2) + 12) . (substr($time_end,2));
        }
        return round((strtotime($time_end) - strtotime($time_start))/(60), 1);
    }

	protected function calculate_minutes_with_date($time_start, $time_end){

		$time_start = strtotime($time_start);
		$time_end = strtotime($time_end);
		$totalSecondsDiff = abs($time_start-$time_end); 
		return $totalSecondsDiff/60;
	
	}

	public static function get_employee_today_shift($employee_id,$ckeck_type,$date){


        $date = date('Y-m-d',strtotime($date)); 
        if($ckeck_type == "in"){
            $column_get = 'shift_start_time';
        }else{
            $column_get = 'shift_end_time';
        }

        $fetch_employee_today_shift = ShiftManagement::
        where('employee_id',$employee_id)
        ->whereIn('shift_start_from_date',function($query) use($date) {
			$query->fromRaw('tbl_shift_management as sub_table')
			->selectRaw('MAX(DATE(shift_start_from_date))')
			->whereRaw("DATE(shift_start_from_date) <= '".$date."'")
			->whereRaw('sub_table.employee_id = tbl_shift_management.employee_id');
        })
        ->get([$column_get])->toArray();
		if(!empty($fetch_employee_today_shift)){
			return $fetch_employee_today_shift[0][$column_get];
		}else{
			return "09:00:00";
		}

        
    }

	protected function check_if_payroll_exist($attendance_date , $employee_id, $custom_message){
       
		$get_employee_payroll = Payroll::where('pc_employee_id',$employee_id)->where('pc_date_from','<=',$attendance_date)->where('pc_date_to','>=',$attendance_date)->get()->toArray();
		
		if(!empty($get_employee_payroll)){

            $employee_detail = Employees::where('employee_id', $employee_id)->get(['employee_name'])->toArray();
			
            if(!empty($custom_message)){
                return [
                    'status' => TRUE,
                    'msg' => 'You can\'t update request of '.$employee_detail[0]['employee_name'].' for the date of '.$attendance_date.' because payroll is already generated for this time period.'
                ];
            }else{
                return[
                    'status' => TRUE,
                    'msg'=>'You can\'t apply any request for this date '. $attendance_date .'. payroll is already generated.',
                ];
            }
        }else{

            return[
				'status' => FALSE,
				
			];
        }
    }

	public function hr_employees_activity_blocker($login_user_id,$serving_employee_id){
	
		$login_user_data = Employees::where('employee_id', $login_user_id)->get()->toArray();
		if (!empty($login_user_data) && (CustomHelper::dept_has_role($login_user_data[0]['employee_department'], 'hr', $login_user_data[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user_data[0]['employee_department'], 'hr', $login_user_data[0]['employee_id'] ?? null))) {
			// HR staff requests are handled only by the head of that employee's department
			$serving_department = Employees::where('employee_id', $serving_employee_id)->value('employee_department');
			$serving_department_hod = Department::where('department_id', $serving_department)->value('department_hod');
			if($serving_department_hod != $login_user_id && CustomHelper::has_role($serving_employee_id, 'hr')){
				return [
					'status' => FALSE,
					'msg' => 'You can\'t update request kindly contact your HOD'
				];
			}
		}
	}

	protected function check_if_attendance_sheet_locked($attendance_date){

		$attendance_date = date('Y-m-d', strtotime($attendance_date));
       
		$check_attendance_sheet = AttendanceLocker::where('from_date', '<=', $attendance_date)
        ->where('to_date', '>=', $attendance_date)
        ->get()
        ->toArray();

		if(!empty($check_attendance_sheet)){
			
			return [
				'status' => FALSE,
				'msg' => 'Attendance sheet has been locked for the date of ' . date('d M, Y', strtotime($attendance_date)) . '. You can\'t update attendance / apply for any kind of request for this period.'
			];

        }else{

            return[
				'status' => TRUE,
			];
        }
    }

	protected function get_monthly_attendance($leave_name,$lastDate,$year,$month,$employement_status,$where_clause_two){
		$all_attendances = DB::select("SELECT employee_id,employee_code,employee_name,employee_offdays,
		employee_location,employee_isactive,
		SUM(att_check_overall_status = 'Present') AS total_present,
		SUM(att_check_overall_status = 'Early out' OR att_check_overall_status = 'Late Coming') AS total_late_early,
		SUM(att_check_overall_status LIKE '%Holiday%') AS total_holiday,
		SUM(att_check_overall_status IN ($leave_name)) AS total_leave,
		SUM(att_check_overall_status = 'Half day') AS total_half_day,
		SUM(att_check_overall_status = 'Present') + SUM(att_check_overall_status = 'Early out' OR att_check_overall_status = 'Late Coming') + SUM(att_check_overall_status LIKE '%Holiday%') + SUM(att_check_overall_status IN ($leave_name)) + SUM(att_check_overall_status = 'Half day') as total_lumpsum_days,
		DAY(LAST_DAY('".$lastDate."')) AS total_month_days,
			(
				DAY(LAST_DAY('".$lastDate."')) - ((SUM(att_check_overall_status = 'Present') + SUM(att_check_overall_status = 'Early out' OR att_check_overall_status = 'Late Coming') + SUM(att_check_overall_status LIKE '%Holiday%')
				+ SUM(att_check_overall_status IN ($leave_name)) 
				+ SUM(att_check_overall_status = 'Half day'))+ (SELECT COUNT(DISTINCT DATE_FORMAT(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date), '%Y-%m-%d')) FROM tbl_attendance
				WHERE att_for_year ".CustomHelper::sql_attendance_eq($year)." 
				AND att_for_month ".CustomHelper::sql_attendance_eq($month)."
				AND FIND_IN_SET(DAYNAME(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)),REPLACE(employee_offdays, ' ', '')))
				- (SELECT COUNT(DISTINCT DATE_FORMAT(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date), '%Y-%m-%d')) 
				FROM tbl_attendance
				WHERE att_for_year ".CustomHelper::sql_attendance_eq($year)." 
				AND att_for_month ".CustomHelper::sql_attendance_eq($month)." 
				AND FIND_IN_SET(DAYNAME(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)), REPLACE(employee_offdays, ' ', '')) 
				AND (att_check_overall_status IN ('Present','Half day','Early out','Late Coming',$leave_name)
				OR att_check_overall_status LIKE '%Holiday%')
				AND employee_id = tbl_attendance.att_employee_id))  
				) AS total_absent,
			(  
			(SELECT COUNT(DISTINCT DATE_FORMAT(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date), '%Y-%m-%d')) FROM tbl_attendance
			WHERE att_for_year ".CustomHelper::sql_attendance_eq($year)."
				AND att_for_month ".CustomHelper::sql_attendance_eq($month)."
			AND FIND_IN_SET(DAYNAME(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)),REPLACE(employee_offdays, ' ', '')))-
			(SELECT COUNT(DISTINCT DATE_FORMAT(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date), '%Y-%m-%d')) FROM tbl_attendance
			WHERE  att_for_year ".CustomHelper::sql_attendance_eq($year)."
				AND att_for_month ".CustomHelper::sql_attendance_eq($month)."
			AND FIND_IN_SET(DAYNAME(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)), REPLACE(employee_offdays, ' ', ''))
			AND (att_check_overall_status IN ('Present','Half day','Early out','Late Coming',$leave_name)
			OR att_check_overall_status LIKE '%Holiday%')
			AND employee_id = tbl_attendance.att_employee_id)
			) AS total_offdays
	
		FROM tbl_attendance
		INNER JOIN tbl_employees ON tbl_attendance.att_employee_id = tbl_employees.employee_id
		WHERE att_for_month ".CustomHelper::sql_attendance_eq($month)." AND att_for_year ".CustomHelper::sql_attendance_eq($year)."
		AND employee_isactive = $employement_status
		$where_clause_two
		GROUP BY employee_id" );

		return $all_attendances;
			  
	}

	protected function get_monthly_attendance_all_employees($from_date , $to_date , $where_clause){

		// numbers 0..999 used to walk the date range (replaces SQL Server master..spt_values);
		// every use filters number <= DATEDIFF(to_date, from_date), so larger numbers are dropped up front
		$range_days = min(999, max(0, (int) round((strtotime($to_date) - strtotime($from_date)) / 86400)));
		$nums = "(SELECT number FROM (SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS number FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones, (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens, (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) hundreds) AS all_nums WHERE number <= $range_days) AS nums";
		// first day counted for an employee: joining date when it falls inside the range
		$emp_start = "(CASE WHEN e.employee_doj > '$from_date' THEN e.employee_doj ELSE '$from_date' END)";
		$emp_start_ge = "(CASE WHEN e.employee_doj >= '$from_date' THEN e.employee_doj ELSE '$from_date' END)";
		// the day (number days after $from_date) is an off day on which the employee still worked
		// Per employee off-day figures, read from tbl_attendance once (joined below as `oa`) instead of one
		// correlated subquery per day and column:
		//  worked_offdays   = off days in [from_date, to_date] with a check in or check out
		//  attended_offdays = off days in [employee start, to_date] that have any attendance row (att_comp_date)
		$att_day = "DATE(CONCAT(t.att_for_year, '-', t.att_for_month, '-', t.att_for_date))";
		$oa_emp_start = "(CASE WHEN oe.employee_doj > '$from_date' THEN oe.employee_doj ELSE '$from_date' END)";
		$offday_attendance = "
			SELECT t.att_employee_id,
				COUNT(DISTINCT CASE WHEN (t.att_check_in_status = 1 OR t.att_check_out_status = 1)
					AND $att_day BETWEEN '$from_date' AND '$to_date'
					AND oe.employee_offdays LIKE CONCAT('%', DAYNAME($att_day), '%')
					THEN $att_day END) AS worked_offdays,
				COUNT(DISTINCT CASE WHEN DATE(t.att_comp_date) BETWEEN $oa_emp_start AND '$to_date'
					AND oe.employee_offdays LIKE CONCAT('%', DAYNAME(DATE(t.att_comp_date)), '%')
					THEN DATE(t.att_comp_date) END) AS attended_offdays
			FROM tbl_attendance t
			INNER JOIN tbl_employees oe ON oe.employee_id = t.att_employee_id
			WHERE oe.employee_isactive = 1
				AND (t.att_comp_date BETWEEN '$from_date' AND '$to_date 23:59:59' OR t.att_comp_date IS NULL OR t.att_comp_date NOT LIKE '____-__-__%')
			GROUP BY t.att_employee_id";
		// off days in [employee start, to_date] without any attendance row
		$absent_offdays = "(
			(SELECT COUNT(*) FROM $nums
				WHERE number BETWEEN 0 AND DATEDIFF('$to_date', $emp_start)
					AND e.employee_offdays LIKE CONCAT('%', DAYNAME(DATE_ADD($emp_start, INTERVAL number DAY)), '%'))
			- IFNULL(oa.attended_offdays, 0)
		)";
		$worked_offdays = "IFNULL(oa.worked_offdays, 0)";

		$all_attendances = DB::select("
				SELECT
					e.employee_id,
					e.employee_code,
					e.employee_name,
					e.employee_new_code,
					DATE_FORMAT(e.employee_doj, '%d-%m-%Y') as 'employee_doj',
					dep.department_name,
					des.designation_name,
					e.business_unit,
					CONCAT(rep.employee_code, ' ', rep.employee_name, ' (', e.employee_offdays, ')') as 'Shift',

					(
						DATEDIFF('$to_date',
							CASE
								WHEN e.employee_doj >= '$from_date' THEN e.employee_doj
								ELSE '$from_date'
							END
						) + 1
						-
						(
							SUM(CASE
								WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day', 'Casual Leave', 'Sick Leave', 'Annual Leave')
								OR att_check_overall_status LIKE '%Holiday%'
								THEN 1
								ELSE 0
							END) +
							$absent_offdays
						)
					) AS 'absent',
					(
						SUM(CASE
							WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day') THEN 1
							ELSE 0
						END) - $worked_offdays
					) - IFNULL(Ex.Extraday, 0) AS 'DA',
					(
						DATEDIFF(
							CASE
								WHEN CURDATE() < '$to_date' THEN CURDATE()
								ELSE '$to_date'
							END,
							$emp_start_ge
						) + 1
					) -
					(
						DATEDIFF('$to_date', $emp_start_ge) + 1
						-
						(
							SUM(CASE
								WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day' , 'Casual Leave', 'Sick Leave', 'Annual Leave')
								OR att_check_overall_status LIKE '%Holiday%'
								THEN 1
								ELSE 0
							END) +
							$absent_offdays
						)
					) AS salary_days,
					SUM(CASE WHEN att_check_overall_status = 'leaves'
					OR l.leave_name IS NOT NULL THEN 1 ELSE 0 END) AS total_leaves,

					(
						SELECT COUNT(*)
						FROM $nums
						WHERE number BETWEEN 0 AND DATEDIFF('$to_date', $emp_start)
							AND e.employee_offdays LIKE CONCAT('%', DAYNAME(DATE_ADD($emp_start, INTERVAL number DAY)), '%')
					) + IFNULL(Ex.HolidayCount, 0) as 'off_days_count',
					(
						SUM(CASE WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day') THEN 1 ELSE 0 END) - $worked_offdays
					) - IFNULL(Ex.Extraday, 0)  AS 'Patrol',
					(
						DATEDIFF(
							CASE
								WHEN CURDATE() < '$to_date' THEN CURDATE()
								ELSE '$to_date'
							END,
							$emp_start_ge
						) + 1
					) AS 'month_days',
					$worked_offdays + IFNULL(Ex.Extraday, 0) AS 'extra_working'
				 
			FROM tbl_employees e
			LEFT JOIN tbl_attendance a ON e.employee_id = a.att_employee_id 
				AND CONCAT(a.att_for_year, '-', a.att_for_month, '-', a.att_for_date) BETWEEN '$from_date' AND '$to_date'
			LEFT JOIN tbl_leave_management l ON a.att_check_overall_status = l.leave_name
			LEFT JOIN tbl_departments dep ON e.employee_department = dep.department_id 
			LEFT JOIN tbl_designations des ON e.employee_designation = des.designation_id 
			LEFT JOIN tbl_employees rep ON e.employee_senior_manager = rep.employee_id
			LEFT JOIN ($offday_attendance) AS oa ON oa.att_employee_id = e.employee_id
			LEFT JOIN (
				SELECT employee_code,
				COUNT(CASE WHEN a.att_check_overall_status NOT LIKE '%Holiday%' THEN holiday_date END) AS Extraday,
				COUNT(holiday_date) AS HolidayCount
				FROM (
					SELECT 
						h.holiday_date,
						a.employee_designation,
						h.holiday_name,
						a.employee_code,
						a.att_check_overall_status,
						a.att_comp_date
					FROM tbl_holidays AS h 
					JOIN (
						SELECT e.employee_designation, employee_code, att_check_overall_status, att_comp_date 
						FROM tbl_attendance t 
						INNER JOIN tbl_employees e ON t.att_employee_id = e.employee_id
						INNER JOIN tbl_departments d ON e.employee_department = d.department_id
						INNER JOIN tbl_designations de ON e.employee_designation = de.designation_id  
					) AS a  ON h.holiday_date = a.att_comp_date 
						AND (FIND_IN_SET(CAST(a.employee_designation AS CHAR), h.designation_name) > 0 OR h.designation_name = '*')
				) AS a
				
				where a.holiday_date between '$from_date' and '$to_date' 

				GROUP BY employee_code
			) AS Ex ON e.employee_code = Ex.employee_code
			WHERE $where_clause AND e.employee_isactive = 1 
			GROUP BY 
				e.employee_code, 
				e.employee_name, 
				e.employee_new_code,
				e.business_unit, 
				dep.department_name, 
				des.designation_name, 
				e.employee_offdays,
				e.employee_id,
				rep.employee_code,
				rep.employee_name,
				e.employee_doj,
				Ex.Extraday,
				Ex.HolidayCount,
				oa.worked_offdays,
				oa.attended_offdays

			ORDER BY e.employee_code ASC
		");


		// $all_attendances = DB::select("
		// SELECT 
		// e.employee_id,
		// e.employee_code, 
		// e.employee_name,
		// FORMAT(CAST(e.employee_doj AS DATE), 'dd-MM-yyyy') as 'employee_doj',
		// dep.department_name, 
		// des.designation_name,
		// e.business_unit,
		// CONCAT(rep.employee_code , ' ' , rep.employee_name ,' (', e.employee_offdays,')') as 'Shift',
		
		// (
		// 	DATEDIFF(DAY, 
		// 		CASE 
		// 			WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
		// 			ELSE '$from_date' 
		// 		END, '$to_date'
		   
		// 	) + 1
		// 	- 
		// 	(
		// 		SUM(CASE 
		// 			WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day') THEN 1 
		// 			ELSE 0 
		// 		END) + 
		// 		(
		// 			SELECT COUNT(*) 
		// 			FROM (
		// 				SELECT DATEADD(DAY, number, 
		// 					CASE 
		// 						WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
		// 						ELSE '$from_date' 
		// 					END
		// 				) AS DateInMonth
		// 				FROM master.dbo.spt_values
		// 				WHERE TYPE = 'P'
		// 					AND number BETWEEN 0 AND DATEDIFF(DAY,
		// 						CASE 
		// 							WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
		// 							ELSE '$from_date' 
		// 						END, 
		// 						'$to_date'
		// 					)
		// 					AND NOT EXISTS (
		// 						SELECT 1 FROM tbl_attendance 
		// 						WHERE 
		// 							att_comp_date = DATEADD(DAY, number, 
		// 								CASE 
		// 									WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
		// 									ELSE '$from_date' 
		// 								END
		// 							) AND
		// 							att_employee_id = e.employee_id
		// 					)
		// 			) AS DateRange
		// 			WHERE e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DateInMonth) + '%'
		// 		)
		// 	)
		// ) AS 'absent',
		
		// 	(SUM(CASE WHEN att_check_overall_status = 'Present' OR att_check_overall_status = 'Late Coming' OR att_check_overall_status = 'Early out'
		// OR att_check_overall_status = 'Half day' THEN 1 ELSE 0 END)) - (
		// 	SELECT COUNT(DateInMonth)
		// 	FROM (
		// 		SELECT 
		// 			DATEADD(DAY, number, '$from_date') AS DateInMonth
		// 		FROM master.dbo.spt_values
		// 		WHERE TYPE = 'P'
		// 			AND number BETWEEN 0 AND DATEDIFF(DAY, '$from_date', '$to_date')
		// 			AND EXISTS (
		// 				SELECT 1 
		// 				FROM tbl_attendance 
		// 				WHERE 
		// 					att_for_year = YEAR(DATEADD(DAY, number, '$from_date')) AND
		// 					att_for_month = MONTH(DATEADD(DAY, number, '$from_date')) AND
		// 					att_for_date = DAY(DATEADD(DAY, number, '$from_date')) AND
		// 					(
		// 						att_check_in_status = 1 OR
		// 						att_check_out_status = 1
		// 					)
		// 					AND att_employee_id = e.employee_id
							
		// 			)
		// 			AND e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DATEADD(DAY, number, '$from_date')) + '%'
		// 	) AS DateRange
		// ) AS 'DA',

		// (
		// 	DATEDIFF(DAY, 
		// 		CASE 
		// 			WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
		// 			ELSE '$from_date' 
		// 		END, 
		// 		CASE 
		// 			WHEN GETDATE() < '$to_date' THEN GETDATE()
		// 			ELSE '$to_date'
		// 		END
		// 	) + 1
		// ) - 
		// (
		// 	DATEDIFF(DAY, 
		// 		CASE 
		// 			WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
		// 			ELSE '$from_date' 
		// 		END, '$to_date'
		// 	) + 1
		// 	- 
		// 	(
		// 		SUM(CASE 
		// 			WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day') THEN 1 
		// 			ELSE 0 
		// 		END) + 
		// 		(
		// 			SELECT COUNT(*) 
		// 			FROM (
		// 				SELECT DATEADD(DAY, number, 
		// 					CASE 
		// 						WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
		// 						ELSE '$from_date' 
		// 					END
		// 				) AS DateInMonth
		// 				FROM master.dbo.spt_values
		// 				WHERE TYPE = 'P'
		// 					AND number BETWEEN 0 AND DATEDIFF(DAY,
		// 						CASE 
		// 							WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
		// 							ELSE '$from_date' 
		// 						END, 
		// 						'$to_date'
		// 					)
		// 					AND NOT EXISTS (
		// 						SELECT 1 FROM tbl_attendance 
		// 						WHERE 
		// 							att_comp_date = DATEADD(DAY, number, 
		// 								CASE 
		// 									WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
		// 									ELSE '$from_date' 
		// 								END
		// 							) AND
		// 							att_employee_id = e.employee_id
		// 					)
		// 			) AS DateRange
		// 			WHERE e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DateInMonth) + '%'
		// 		)
		// 	)
		// ) AS salary_days,
		
		// SUM(CASE WHEN att_check_overall_status = 'leaves' 
		// OR l.leave_name IS NOT NULL THEN 1 ELSE 0 END) AS total_leaves,
		
		// (SELECT COUNT(*) 
		// FROM (
		//    SELECT DATEADD(DAY, number, 
		// 	   CASE 
		// 		   WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
		// 		   ELSE '$from_date' 
		// 	   END
		//    ) AS DateInMonth
		//    FROM master.dbo.spt_values
		//    WHERE TYPE = 'P'
		//    AND number BETWEEN 0 AND DATEDIFF(DAY, 
		// 	   CASE 
		// 		   WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
		// 		   ELSE '$from_date' 
		// 	   END, '$to_date'

		//    )
		// 		) AS DateRange
		// 		WHERE 
		// 		e.employee_offdays like '%'+DATENAME(WEEKDAY, DateInMonth)+'%'
		// 	) as 'off_days_count',
					
		// 	(SUM(CASE WHEN att_check_overall_status = 'Present' OR att_check_overall_status = 'Late Coming' OR att_check_overall_status = 'Early out'
		// 	OR att_check_overall_status = 'Half day' THEN 1 ELSE 0 END)) - (
		// 		SELECT COUNT(DateInMonth)
		// 		FROM (
		// 			SELECT 
		// 				DATEADD(DAY, number, '$from_date') AS DateInMonth
		// 			FROM master.dbo.spt_values
		// 			WHERE TYPE = 'P'
		// 				AND number BETWEEN 0 AND DATEDIFF(DAY, '$from_date', '$to_date')
		// 				AND EXISTS (
		// 					SELECT 1 
		// 					FROM tbl_attendance 
		// 					WHERE 
		// 						att_for_year = YEAR(DATEADD(DAY, number, '$from_date')) AND
		// 						att_for_month = MONTH(DATEADD(DAY, number, '$from_date')) AND
		// 						att_for_date = DAY(DATEADD(DAY, number, '$from_date')) AND
		// 						(
		// 							att_check_in_status = 1 OR
		// 							att_check_out_status = 1
		// 						)
		// 						AND att_employee_id = e.employee_id
								
		// 				)
		// 				AND e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DATEADD(DAY, number, '$from_date')) + '%'
		// 		) AS DateRange
		// 	) AS 'Patrol',

		// 	(
		// 		DATEDIFF(DAY, 
		// 			CASE 
		// 				WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
		// 				ELSE '$from_date' 
		// 			END, 
		// 			CASE 
		// 				WHEN GETDATE() < '$to_date' THEN GETDATE()
		// 				ELSE '$to_date'
		// 			END
		// 		) + 1
		// 	) AS 'month_days',

		// 	(
		// 		SELECT COUNT(DateInMonth)
		// 		FROM (
		// 			SELECT 
		// 				DATEADD(DAY, number, '$from_date') AS DateInMonth
		// 			FROM master.dbo.spt_values
		// 			WHERE TYPE = 'P'
		// 				AND number BETWEEN 0 AND DATEDIFF(DAY, '$from_date', '$to_date')
		// 				AND EXISTS (
		// 					SELECT 1 
		// 					FROM tbl_attendance 
		// 					WHERE 
		// 						att_for_year = YEAR(DATEADD(DAY, number, '$from_date')) AND
		// 						att_for_month = MONTH(DATEADD(DAY, number, '$from_date')) AND
		// 						att_for_date = DAY(DATEADD(DAY, number, '$from_date')) AND
		// 						(
		// 							att_check_in_status = 1 OR
		// 							att_check_out_status = 1
		// 						)
		// 						AND att_employee_id = e.employee_id
								
		// 				)
		// 				AND e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DATEADD(DAY, number, '$from_date')) + '%'
		// 		) AS DateRange
		// 	) AS 'extra_working'

		// 	FROM tbl_employees e
		// 	LEFT JOIN tbl_attendance a ON e.employee_id = a.att_employee_id 
		// 	AND CONCAT(a.att_for_year,'-',a.att_for_month,'-',a.att_for_date) >= '$from_date' 
		// 	AND CONCAT(a.att_for_year,'-',a.att_for_month,'-',a.att_for_date) <= '$to_date' 
		// 	LEFT JOIN tbl_leave_management l ON a.att_check_overall_status = l.leave_name
		// 	LEFT JOIN tbl_departments dep ON e.employee_department = dep.department_id 
		// 	LEFT JOIN tbl_designations des ON e.employee_designation = des.designation_id 
		// 	LEFT JOIN tbl_employees rep ON e.employee_senior_manager = rep.employee_id
		// 	WHERE $where_clause AND e.employee_isactive = 1
		// 	GROUP BY 
		// 	e.employee_code, 
		// 	e.employee_name, 
		// 	e.business_unit, 
		// 	dep.department_name, 
		// 	des.designation_name, 
		// 	e.employee_offdays,
		// 	e.employee_id,
		// 	rep.employee_code,
		// 	rep.employee_name,
		// 	e.employee_doj

		// 	ORDER BY 
    	// 		e.employee_code ASC
	
		// ");

		return $all_attendances;
	}

	// Mobile check in / out location rule, checked on the server (the app's own check can be bypassed).
	// Applies when geofencing is on for the employee, the job type is not Home and there is no approved remote work for
	// the day: the phone must be within the attendance radius of one of the employee's office locations.
	// Returns null when allowed, otherwise the message for the app. Locations without coordinates (0 / empty) are not checked.
	protected function mobile_attendance_location_error($employee_id, $latitude, $longitude, $date){

		$employee = Employees::where('employee_id', $employee_id)->first(['employee_geofencing_enability','employee_jobtype']);

		if(empty($employee) || $employee->employee_geofencing_enability != 1 || $employee->employee_jobtype == 'Home' || $this->remote_work_request_attendance($employee_id, $date) == 1){
			return null;
		}

		$locations = DB::table('tbl_employees_location')
			->join('tbl_office_locations', 'tbl_office_locations.location_id', '=', 'tbl_employees_location.emp_loc_loc_id')
			->where('emp_loc_employee_id', $employee_id)
			->get(['location_title','location_latitude','location_longitude','location_attendance_radius'])
			->filter(fn($location) => is_numeric($location->location_latitude) && is_numeric($location->location_longitude) && ((float) $location->location_latitude != 0 || (float) $location->location_longitude != 0));

		if($locations->isEmpty()){
			return null;
		}

		if(!is_numeric($latitude) || !is_numeric($longitude) || ((float) $latitude == 0 && (float) $longitude == 0)){
			return "Your location is required to mark attendance. Please turn on location and try again.";
		}

		$nearest = null;
		foreach($locations as $location){
			$distance = $this->distance_in_meters($latitude, $longitude, $location->location_latitude, $location->location_longitude);
			if($distance <= (float) $location->location_attendance_radius){
				return null;
			}
			if($nearest === null || $distance < $nearest['distance']){
				$nearest = ['distance' => $distance, 'title' => $location->location_title, 'radius' => (float) $location->location_attendance_radius];
			}
		}

		return "You are ".number_format($nearest['distance'])." m away from ".$nearest['title'].". Attendance can be marked within ".number_format($nearest['radius'])." m of your office.";
	}

	// distance between two points on earth (haversine), in meters
	protected function distance_in_meters($latitude_from, $longitude_from, $latitude_to, $longitude_to){

		$earth_radius = 6371000;
		$latitude_delta = deg2rad((float) $latitude_to - (float) $latitude_from);
		$longitude_delta = deg2rad((float) $longitude_to - (float) $longitude_from);
		$a = sin($latitude_delta / 2) ** 2 + cos(deg2rad((float) $latitude_from)) * cos(deg2rad((float) $latitude_to)) * sin($longitude_delta / 2) ** 2;

		return $earth_radius * 2 * atan2(sqrt($a), sqrt(1 - $a));
	}

	protected function remote_work_request_attendance($employee_id,$date){
		
		$get_wfh_request = WFH::where('wfh_employee_id','=',(string) $employee_id)->where('wfh_request_date','=',$date)->get(['wfh_request_id','wfh_request_approved_by_hr'])->toArray();
	
		// HR disapproved: no remote work, even if a manager approved it before
		if(!empty($get_wfh_request) && $get_wfh_request[0]['wfh_request_approved_by_hr'] == 3){
			return 0;
		}

		if(!empty($get_wfh_request) && $get_wfh_request[0]['wfh_request_approved_by_hr'] != 2){
			$get_wfh_request = RequestApproval::where('request_module_id',3)
			->where('request_id',$get_wfh_request[0]['wfh_request_id'])
			->where('request_approver_status',2)
			->limit(1)
			->get()->toArray();
		}
		
		if(!empty($get_wfh_request)){
			return 1;
		}else{
			return 0;
		}

	}

	

}

