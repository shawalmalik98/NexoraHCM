<?php

namespace App\Traits;

use App\Models\Employees;
use App\Models\Attendance;
use App\Models\HrmSetting;
use App\Models\LeaveManagement;

trait MachineAttendanceTrait {

    protected function multiple_check_in_machine($check_in , $employee_id , $attendance_source = NULL){
		
		$employee_data = Employees::where('employee_id',$employee_id)->get(['employee_id','employee_shift_flexibilty','employee_doj'])->toArray();
		
		if(!empty($employee_data)){
			$current_date  = strtotime(date('Y-m-d'));
			$doj = strtotime($employee_data[0]['employee_doj']);

			if($current_date >= $doj){
				$day = date("d", strtotime($check_in));
				$month = date("m", strtotime($check_in));
				$year = date("Y", strtotime($check_in));
				
				$get_attendance = Attendance::where('att_employee_id', $employee_data[0]['employee_id'])->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();
				
				if(!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false ){
					Attendance::where('attendance_id',$get_attendance[0]['attendance_id'])->delete();
				} 
				
				$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d',strtotime($check_in)));
				$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d',strtotime($check_in)));
	
				if(empty($get_attendance) || (!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false)){
					if($employee_data[0]['employee_shift_flexibilty'] != '1'){
						$employee_shift_start_hour = date("H", strtotime($employee_shift_start));
						$employee_shift_start_minute = date("i", strtotime($employee_shift_start));
						$exceed = strtotime("+2 hours", strtotime($employee_shift_start));
						$exceed_final = date('H:i', $exceed);
						$request_check_in_time = date("H", strtotime($check_in));
						$check_in_grace_period = HrmSetting::pluck('check_in_flexibility_time')->toArray();
						if($employee_shift_start_hour == $request_check_in_time){
							if(date('i' , strtotime($check_in)) <= $employee_shift_start_minute + $check_in_grace_period[0]){
								$mark_attendance = new Attendance();
								$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
								$mark_attendance->att_check_out_time = '00:00:00';
								$mark_attendance->att_check_in_status = 1;
								$mark_attendance->att_check_out_status = 0;
								$mark_attendance->att_shift_start_time = $employee_shift_start;
								$mark_attendance->att_shift_end_time = $employee_shift_end;
								$mark_attendance->att_check_overall_status = 'Present';
								$mark_attendance->att_for_date = $day;
								$mark_attendance->att_for_month = $month;
								$mark_attendance->att_for_year = $year;
								$mark_attendance->att_check_in_source = $attendance_source;
								$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
								$mark_attendance->save();
								
	
							}else{
								$mark_attendance = new Attendance();
								$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
								$mark_attendance->att_check_out_time = '00:00:00';
								$mark_attendance->att_check_in_status = 1;
								$mark_attendance->att_check_out_status = 0;
								$mark_attendance->att_shift_start_time = $employee_shift_start;
								$mark_attendance->att_shift_end_time = $employee_shift_end;
								$mark_attendance->att_check_overall_status = 'Late Coming';
								$mark_attendance->att_for_date = $day;
								$mark_attendance->att_for_month = $month;
								$mark_attendance->att_for_year = $year;
								$mark_attendance->att_check_in_source = $attendance_source;
								$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
								$mark_attendance->save();
	
							}
						}elseif(date('H' , strtotime($check_in)) > $employee_shift_start_hour){
							if(date('H:i', strtotime($check_in)) <= $exceed_final ){
								$mark_attendance = new Attendance();
								$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
								$mark_attendance->att_check_out_time = '00:00:00';
								$mark_attendance->att_check_in_status = 1;
								$mark_attendance->att_check_out_status = 0;
								$mark_attendance->att_shift_start_time = $employee_shift_start;
								$mark_attendance->att_shift_end_time = $employee_shift_end;
								$mark_attendance->att_check_overall_status = 'Late Coming';
								$mark_attendance->att_for_date = $day;
								$mark_attendance->att_for_month = $month;
								$mark_attendance->att_for_year = $year;
								$mark_attendance->att_check_in_source = $attendance_source;
								$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
								$mark_attendance->save();
								
							}else{
								$mark_attendance = new Attendance();
								$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
								$mark_attendance->att_check_out_time = '00:00:00';
								$mark_attendance->att_check_in_status = 1;
								$mark_attendance->att_check_out_status = 0;
								$mark_attendance->att_shift_start_time = $employee_shift_start;
								$mark_attendance->att_shift_end_time = $employee_shift_end;
								$mark_attendance->att_check_overall_status = 'Half day';
								$mark_attendance->att_for_date = $day;
								$mark_attendance->att_for_month = $month;
								$mark_attendance->att_for_year = $year;
								$mark_attendance->att_check_in_source = $attendance_source;
								$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
								$mark_attendance->save();
							}
						}else{
							$mark_attendance = new Attendance();
							$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
							$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_attendance->att_check_out_time = '00:00:00';
							$mark_attendance->att_check_in_status = 1;
							$mark_attendance->att_check_out_status = 0;
							$mark_attendance->att_shift_start_time = $employee_shift_start;
							$mark_attendance->att_shift_end_time = $employee_shift_end;
							$mark_attendance->att_check_overall_status = 'Present';
							$mark_attendance->att_for_date = $day;
							$mark_attendance->att_for_month = $month;
							$mark_attendance->att_for_year = $year;
							$mark_attendance->att_check_in_source = $attendance_source;
							$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
							$mark_attendance->save();
	
						}
					}else{
						$mark_attendance = new Attendance();
						$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
						$mark_attendance->att_check_in_time = date('H:i:s', strtotime($check_in));
						$mark_attendance->att_check_out_time = '00:00:00';
						$mark_attendance->att_check_in_status = 1;
						$mark_attendance->att_check_out_status = 0;
						$mark_attendance->att_shift_start_time = $employee_shift_start;
						$mark_attendance->att_shift_end_time = $employee_shift_end;
						$mark_attendance->att_check_overall_status = 'Present';
						$mark_attendance->att_for_date = $day;
						$mark_attendance->att_for_month = $month;
						$mark_attendance->att_for_year = $year;
						$mark_attendance->att_check_in_source = $attendance_source;
						$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
						$mark_attendance->save();
	
					}
				}
	
			}
					
		}
	}

	protected function multiple_check_out_machine($check_out_time , $employee_id , $attendance_source = NULL){
		
		$employee_data = Employees::where('employee_id',$employee_id)->get(['employee_id','employee_doj'])->toArray();
	
		if(!empty($employee_data)){

			$current_date  = strtotime(date('Y-m-d'));
			$doj = strtotime($employee_data[0]['employee_doj']);

			if($current_date >= $doj){
			
				$day = date("d", strtotime($check_out_time));
				$month = date("m", strtotime($check_out_time));
				$year = date("Y", strtotime($check_out_time));
				
				$manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

				// $get_attendance = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->whereNot('att_check_overall_status','LIKE', '%Holiday%')
				// ->whereNotIn('att_check_overall_status',$manage_leaves)->where('att_request_approval', '!=', 1)->where('att_check_in_source','!=', 3)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();

				$get_attendance = Attendance::where('att_employee_id', $employee_id)
				->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)
				->where('att_check_overall_status', 'NOT LIKE', '%Holiday%')
				// ->whereNotIn('att_check_overall_status', $manage_leaves)
				->where(function ($query) {
					$query->whereNotIn('att_request_approval',[1,2,3])
						->orWhereNull('att_request_approval');
				})
				->where('att_check_in_source', '!=', 3)
				->get()
				->toArray();

				if(!empty($get_attendance)){

					$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d',strtotime($check_out_time)));
					$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d',strtotime($check_out_time)));

					$check_in_time = $get_attendance[0]['att_for_year'].'-'.$get_attendance[0]['att_for_month'].'-'.$get_attendance[0]['att_for_date'].' '.$get_attendance[0]['att_check_in_time'];
					
					if(strtotime($check_out_time) < strtotime($check_in_time) == FALSE ){
						
						if($get_attendance[0]['att_check_out_time'] == '00:00:00' &&  ($get_attendance[0]['att_check_out_status'] == '0' || $get_attendance[0]['att_check_out_status'] == NULL)){

							$check_out = Attendance::find($get_attendance[0]['attendance_id']);

							$check_out->att_check_out_time = date('H:i:s', strtotime($check_out_time));

							$check_out->att_check_out_status = 1;
							$check_out->att_check_out_source = 4;
							
							$check_out->att_updated_at = date('Y-m-d H:i:s', strtotime($check_out_time));
							$employee_official_time = $this->calculate_minutes_machine($employee_shift_start,$employee_shift_end);
							$current_shift_time = $this->calculate_minutes_machine($get_attendance[0]['att_check_in_time'] , date('H:i:s', strtotime($check_out_time)));
							$diff = $employee_official_time - $current_shift_time;
							
							$check_out_grace_period = HrmSetting::pluck('check_out_flexibility_time')->toArray();
							
							if($diff >= 120){

								$check_out->att_check_overall_status = "Half day";

							}elseif($diff > $check_out_grace_period[0] && $diff < 120){

								$check_out->att_check_overall_status = "Early out";
							}

							$check_out->att_check_out_source = $attendance_source;
							
							$check_out->save();

						}
					}
				}else{
			
					$day = date('d',strtotime("-1 days", strtotime($check_out_time)));

					$month = date('m',strtotime("-1 days", strtotime($check_out_time)));

					$year = date('Y',strtotime("-1 days", strtotime($check_out_time)));

					// $get_attendance_yesterday = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->whereNot('att_check_overall_status','LIKE', '%Holiday%')
					// ->whereNotIn('att_check_overall_status',$manage_leaves)->where('att_request_approval', '!=', 1)->where('att_check_in_source','!=', 3)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();

					$get_attendance_yesterday = Attendance::where('att_employee_id', $employee_id)
					->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)
					->where('att_check_overall_status', 'NOT LIKE', '%Holiday%')
					// ->whereNotIn('att_check_overall_status', $manage_leaves)
					->where(function ($query) {
						$query->whereNotIn('att_request_approval',[1,2,3])
							->orWhereNull('att_request_approval');
					})
					->where('att_check_in_source', '!=', 3)
					->get()
					->toArray();

					if(!empty($get_attendance_yesterday)){

						$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d',strtotime("-1 days",strtotime($check_out_time))));
						$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d',strtotime("-1 days",strtotime($check_out_time))));

						if($get_attendance_yesterday[0]['att_check_out_time'] == '00:00:00' && ($get_attendance_yesterday[0]['att_check_out_status'] == '0' || $get_attendance_yesterday[0]['att_check_out_status'] == NULL)){

							$check_out = Attendance::find($get_attendance_yesterday[0]['attendance_id']);

							$check_out->att_check_out_time = date('H:i:s', strtotime($check_out_time));

							$check_out->att_check_out_status = 1;
							$check_out->att_check_out_source = $attendance_source;
							
							$check_out->att_updated_at = date('Y-m-d H:i:s', strtotime($check_out_time));
							
							$employee_official_time = $this->calculate_minutes_machine($employee_shift_start , $employee_shift_end);
							
							$yesterday_full_date = $get_attendance_yesterday[0]['att_for_year'] .'-'. $get_attendance_yesterday[0]['att_for_month'] .'-'. $get_attendance_yesterday[0]['att_for_date']. ' '. $get_attendance_yesterday[0]['att_check_in_time'];

							$current_shift_time = $this->calculate_minutes_with_date_machine($yesterday_full_date , date('Y-m-d H:i:s', strtotime($check_out_time)));
							
							$diff = $employee_official_time - $current_shift_time;

							$check_out_grace_period = HrmSetting::pluck('check_out_flexibility_time')->toArray();

							if($diff >= 120){
								
								$check_out->att_check_overall_status = "Half day";  

							}elseif($diff > $check_out_grace_period[0] && $diff < 120){

								$check_out->att_check_overall_status = "Early out"; 
								
							}

							$check_out->save();
							
						}
					}
				}
			}
		}
	}

	protected function multiple_check_in_mobile($check_in , $employee_id , $attendance_source = NULL){
		
		$employee_data = Employees::where('employee_id',$employee_id)->get(['employee_id','employee_shift_flexibilty','employee_doj'])->toArray();
		
		if(!empty($employee_data)){
			$current_date  = strtotime(date('Y-m-d'));
			$doj = strtotime($employee_data[0]['employee_doj']);

			if($current_date >= $doj){

			
				$day = date("d", strtotime($check_in));
				$month = date("m", strtotime($check_in));
				$year = date("Y", strtotime($check_in));
				
				$get_attendance = Attendance::where('att_employee_id', $employee_data[0]['employee_id'])->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();
				
				if(!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false ){
					Attendance::where('attendance_id',$get_attendance[0]['attendance_id'])->delete();
				} 
				
				$remote_applicable = $this->remote_work_request_attendance($employee_id,date('Y-m-d', strtotime($check_in)));

				$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d',strtotime($check_in)));
				$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d',strtotime($check_in)));
	
				if(empty($get_attendance) || (!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false)){
					if($employee_data[0]['employee_shift_flexibilty'] != '1'){
						$employee_shift_start_hour = date("H", strtotime($employee_shift_start));
						$employee_shift_start_minute = date("i", strtotime($employee_shift_start));
						$exceed = strtotime("+1 hours", strtotime($employee_shift_start));
						$exceed_final = date('H:i', $exceed);
						$request_check_in_time = date("H", strtotime($check_in));
						$check_in_grace_period = HrmSetting::pluck('check_in_flexibility_time')->toArray();
						if($employee_shift_start_hour == $request_check_in_time){
							if(date('i' , strtotime($check_in)) <= $employee_shift_start_minute + $check_in_grace_period[0]){
								$mark_attendance = new Attendance();
								$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
								$mark_attendance->att_check_out_time = '00:00:00';
								$mark_attendance->att_check_in_status = 1;
								$mark_attendance->att_check_out_status = 0;
								$mark_attendance->att_shift_start_time = $employee_shift_start;
								$mark_attendance->att_shift_end_time = $employee_shift_end;
								$mark_attendance->att_check_overall_status = 'Present';
								$mark_attendance->att_for_date = $day;
								$mark_attendance->att_for_month = $month;
								$mark_attendance->att_for_year = $year;
								$mark_attendance->att_check_in_source = $attendance_source;
								$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
								$mark_attendance->att_remote_applicable = $remote_applicable;
								$mark_attendance->save();
								
	
							}else{
								$mark_attendance = new Attendance();
								$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
								$mark_attendance->att_check_out_time = '00:00:00';
								$mark_attendance->att_check_in_status = 1;
								$mark_attendance->att_check_out_status = 0;
								$mark_attendance->att_shift_start_time = $employee_shift_start;
								$mark_attendance->att_shift_end_time = $employee_shift_end;
								$mark_attendance->att_check_overall_status = 'Late Coming';
								$mark_attendance->att_for_date = $day;
								$mark_attendance->att_for_month = $month;
								$mark_attendance->att_for_year = $year;
								$mark_attendance->att_check_in_source = $attendance_source;
								$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
								$mark_attendance->att_remote_applicable = $remote_applicable;
								$mark_attendance->save();
	
							}
						}elseif(date('H' , strtotime($check_in)) > $employee_shift_start_hour){
							if(date('H:i', strtotime($check_in)) <= $exceed_final ){
								$mark_attendance = new Attendance();
								$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
								$mark_attendance->att_check_out_time = '00:00:00';
								$mark_attendance->att_check_in_status = 1;
								$mark_attendance->att_check_out_status = 0;
								$mark_attendance->att_shift_start_time = $employee_shift_start;
								$mark_attendance->att_shift_end_time = $employee_shift_end;
								$mark_attendance->att_check_overall_status = 'Late Coming';
								$mark_attendance->att_for_date = $day;
								$mark_attendance->att_for_month = $month;
								$mark_attendance->att_for_year = $year;
								$mark_attendance->att_check_in_source = $attendance_source;
								$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
								$mark_attendance->att_remote_applicable = $remote_applicable;
								$mark_attendance->save();
								
							}else{
								$mark_attendance = new Attendance();
								$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
								$mark_attendance->att_check_out_time = '00:00:00';
								$mark_attendance->att_check_in_status = 1;
								$mark_attendance->att_check_out_status = 0;
								$mark_attendance->att_shift_start_time = $employee_shift_start;
								$mark_attendance->att_shift_end_time = $employee_shift_end;
								$mark_attendance->att_check_overall_status = 'Half day';
								$mark_attendance->att_for_date = $day;
								$mark_attendance->att_for_month = $month;
								$mark_attendance->att_for_year = $year;
								$mark_attendance->att_check_in_source = $attendance_source;
								$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
								$mark_attendance->att_remote_applicable = $remote_applicable;
								$mark_attendance->save();
							}
						}else{
							$mark_attendance = new Attendance();
							$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
							$mark_attendance->att_check_in_time = date('H:i:s',strtotime($check_in));
							$mark_attendance->att_check_out_time = '00:00:00';
							$mark_attendance->att_check_in_status = 1;
							$mark_attendance->att_check_out_status = 0;
							$mark_attendance->att_shift_start_time = $employee_shift_start;
							$mark_attendance->att_shift_end_time = $employee_shift_end;
							$mark_attendance->att_check_overall_status = 'Present';
							$mark_attendance->att_for_date = $day;
							$mark_attendance->att_for_month = $month;
							$mark_attendance->att_for_year = $year;
							$mark_attendance->att_check_in_source = $attendance_source;
							$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
							$mark_attendance->att_remote_applicable = $remote_applicable;
							$mark_attendance->save();
	
						}
					}else{
						$mark_attendance = new Attendance();
						$mark_attendance->att_employee_id = $employee_data[0]['employee_id'];
						$mark_attendance->att_check_in_time = date('H:i:s', strtotime($check_in));
						$mark_attendance->att_check_out_time = '00:00:00';
						$mark_attendance->att_check_in_status = 1;
						$mark_attendance->att_check_out_status = 0;
						$mark_attendance->att_shift_start_time = $employee_shift_start;
						$mark_attendance->att_shift_end_time = $employee_shift_end;
						$mark_attendance->att_check_overall_status = 'Present';
						$mark_attendance->att_for_date = $day;
						$mark_attendance->att_for_month = $month;
						$mark_attendance->att_for_year = $year;
						$mark_attendance->att_check_in_source = $attendance_source;
						$mark_attendance->att_created_at = date('Y-m-d H:i:s', strtotime($check_in));
						$mark_attendance->att_remote_applicable = $remote_applicable;
						$mark_attendance->save();
	
					}
				}
	
			}
					
		}
	}

	protected function multiple_check_out_mobile($check_out_time , $employee_id , $attendance_source = NULL){
		
		$employee_data = Employees::where('employee_id',$employee_id)->get(['employee_id','employee_doj'])->toArray();
		
		if(!empty($employee_data)){

			$current_date  = strtotime(date('Y-m-d'));
			$doj = strtotime($employee_data[0]['employee_doj']);

			if($current_date >= $doj){
			
				$day = date("d", strtotime($check_out_time));
				$month = date("m", strtotime($check_out_time));
				$year = date("Y", strtotime($check_out_time));
				
				$manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

				$get_attendance = Attendance::where('att_employee_id', $employee_id)
				->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)
				// ->where('att_check_overall_status', 'NOT LIKE', '%Holiday%')
				// ->whereNotIn('att_check_overall_status', $manage_leaves)
				// ->where(function ($query) {
				// 	$query->where('att_request_approval', '!=', 1)
				// 		->orWhereNull('att_request_approval');
				// })
				// ->where('att_check_in_source', '!=', 3)
				->get()
				->toArray();

				if(!empty($get_attendance)){

					$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d',strtotime($check_out_time)));
					$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d',strtotime($check_out_time)));

					$check_in_time = $get_attendance[0]['att_for_year'].'-'.$get_attendance[0]['att_for_month'].'-'.$get_attendance[0]['att_for_date'].' '.$get_attendance[0]['att_check_in_time'];
					
					if(strtotime($check_out_time) < strtotime($check_in_time) == FALSE ){
						
						if($get_attendance[0]['att_check_out_time'] == '00:00:00' &&  ($get_attendance[0]['att_check_out_status'] == '0' || $get_attendance[0]['att_check_out_status'] == NULL)){

							$check_out = Attendance::find($get_attendance[0]['attendance_id']);

							$check_out->att_check_out_time = date('H:i:s', strtotime($check_out_time));

							$check_out->att_check_out_status = 1;
							$check_out->att_check_out_source = 4;
							
							$check_out->att_updated_at = date('Y-m-d H:i:s', strtotime($check_out_time));
							$employee_official_time = $this->calculate_minutes_machine($employee_shift_start,$employee_shift_end);
							$current_shift_time = $this->calculate_minutes_machine($get_attendance[0]['att_check_in_time'] , date('H:i:s', strtotime($check_out_time)));
							$diff = $employee_official_time - $current_shift_time;
							
							$check_out_grace_period = HrmSetting::pluck('check_out_flexibility_time')->toArray();
							
							if($diff >= 120){

								$check_out->att_check_overall_status = "Half day";

							}elseif($diff > $check_out_grace_period[0] && $diff < 120){

								$check_out->att_check_overall_status = "Early out";
							}

							$check_out->att_check_out_source = $attendance_source;
							
							$check_out->save();

						}
					}
				}else{
			
					$day = date('d',strtotime("-1 days", strtotime($check_out_time)));

					$month = date('m',strtotime("-1 days", strtotime($check_out_time)));

					$year = date('Y',strtotime("-1 days", strtotime($check_out_time)));

				
					$get_attendance_yesterday = Attendance::where('att_employee_id', $employee_id)
					->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)
					// ->where('att_check_overall_status', 'NOT LIKE', '%Holiday%')
					// ->whereNotIn('att_check_overall_status', $manage_leaves)
					// ->where(function ($query) {
					// 	$query->where('att_request_approval', '!=', 1)
					// 		->orWhereNull('att_request_approval');
					// })
					// ->where('att_check_in_source', '!=', 3)
					->get()
					->toArray();

					if(!empty($get_attendance_yesterday)){

						$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d',strtotime("-1 days",strtotime($check_out_time))));
						$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d',strtotime("-1 days",strtotime($check_out_time))));

						if($get_attendance_yesterday[0]['att_check_out_time'] == '00:00:00' && ($get_attendance_yesterday[0]['att_check_out_status'] == '0' || $get_attendance_yesterday[0]['att_check_out_status'] == NULL)){

							$check_out = Attendance::find($get_attendance_yesterday[0]['attendance_id']);

							$check_out->att_check_out_time = date('H:i:s', strtotime($check_out_time));

							$check_out->att_check_out_status = 1;
							$check_out->att_check_out_source = $attendance_source;
							
							$check_out->att_updated_at = date('Y-m-d H:i:s', strtotime($check_out_time));
							
							$employee_official_time = $this->calculate_minutes_machine($employee_shift_start , $employee_shift_end);
							
							$yesterday_full_date = $get_attendance_yesterday[0]['att_for_year'] .'-'. $get_attendance_yesterday[0]['att_for_month'] .'-'. $get_attendance_yesterday[0]['att_for_date']. ' '. $get_attendance_yesterday[0]['att_check_in_time'];

							$current_shift_time = $this->calculate_minutes_with_date_machine($yesterday_full_date , date('Y-m-d H:i:s', strtotime($check_out_time)));
							
							$diff = $employee_official_time - $current_shift_time;

							$check_out_grace_period = HrmSetting::pluck('check_out_flexibility_time')->toArray();

							if($diff >= 120){
								
								$check_out->att_check_overall_status = "Half day";  

							}elseif($diff > $check_out_grace_period[0] && $diff < 120){

								$check_out->att_check_overall_status = "Early out"; 
								
							}

							$check_out->save();
							
						}
					}
				}
			}
		}
	}

    protected function calculate_minutes_machine($time_start,$time_end){

        if(strtotime($time_start)>strtotime($time_end)){
            $time_start =  (substr($time_start,0,2) - 12) . (substr($time_start,2));
            $time_end =  (substr($time_end,0,2) + 12) . (substr($time_end,2));
        }
        return round((strtotime($time_end) - strtotime($time_start))/(60), 1);
    }

	protected function calculate_minutes_with_date_machine($time_start, $time_end){

		$time_start = strtotime($time_start);
		$time_end = strtotime($time_end);
		$total_seconds_diff = abs($time_start - $time_end);
		return $total_seconds_diff/60;
	
	}






}

