<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Models\{Attendance, Employees, MacAddress, WFH, HrmSetting, Notification, LateComers,RequestApproval,LeaveManagement};
use App\Helpers\ActivityTracker;
use App\Traits\{LogGenerateTrait,LoginEmployee,ApiResponseTrait,AttendanceEmployee,MachineAttendanceTrait};
use Illuminate\Support\Facades\DB;

class MarkAttendanceApiController extends Controller
{
	use LogGenerateTrait, LoginEmployee, ApiResponseTrait, AttendanceEmployee, MachineAttendanceTrait;

    const SERVER_ERROR = 'Unknown error occured on server. Please try again after some time.';

    protected function check_in(Request $request){

		try{
			
			$login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

			$employee_ID = 	$login_user[0]['employee_id'];
					
			$current_date = strtotime(date('Y-m-d'));
			$doj = strtotime($login_user[0]['employee_doj']);
			
			if($current_date >=  $doj){
				$manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
				
				$remote_applicable = $this->remote_work_request_attendance($employee_ID,date('Y-m-d'));
				
				$employee_shift_start = $this->get_employee_today_shift($employee_ID,"in",date('Y-m-d'));
				$employee_shift_end = $this->get_employee_today_shift($employee_ID,"out",date('Y-m-d'));

				
			
				$day = date('d',strtotime("-1 days"));
				$month = date('m',strtotime("-1 days"));
				$year = date('Y',strtotime("-1 days"));

				$get_yesterday_attendance = Attendance::where('att_employee_id', $employee_ID)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->where('att_check_out_time', '00:00:00')->get()->toArray();
			
				if(!empty($get_yesterday_attendance)){
					
					$employee_official_time = $this->calculate_minutes($employee_shift_start , $employee_shift_end);
					
					$yesterday_full_date = $get_yesterday_attendance[0]['att_for_year'] .'-'. $get_yesterday_attendance[0]['att_for_month'] .'-'. $get_yesterday_attendance[0]['att_for_date']. ' '. $get_yesterday_attendance[0]['att_check_in_time']; 
					$current_shift_time = $this->calculate_minutes_with_date($yesterday_full_date ,date('Y-m-d H:i:s') );
					
					$diff  = $employee_official_time - $current_shift_time;

					if(abs($diff) <= 300 || $current_shift_time < 180){
						return $this->api_response_json(FALSE,"You've already checked in for today's Shifts","",200);
					}

				}
				
				$day = date("d");
				$month = date("m");
				$year = date("Y");

				$get_attendance = Attendance::where('att_employee_id', $employee_ID)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get()->toArray();
			
				if(!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false ){
					Attendance::where('attendance_id',$get_attendance[0]['attendance_id'])->delete();
				} 
				
				if(empty($get_attendance) || (!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false)){

					$employee_data = Employees::where('employee_id', $login_user[0]['employee_id'])->get(['employee_id','employee_shift_flexibilty'])->toArray();

					if(!empty($employee_data)){
						if($employee_data[0]['employee_shift_flexibilty'] != '1'){
							$employee_shift_start_hour = date("H", strtotime($employee_shift_start));
							$employee_shift_start_minute = date("i", strtotime($employee_shift_start));
							$exceed = strtotime("+1 hours", strtotime($employee_shift_start));
							$exceed_final = date('H:i', $exceed);
							$check_in_grace_period = HrmSetting::pluck('check_in_flexibility_time')->toArray();
							if($employee_shift_start_hour == date('H')){
								if(date('i') <= $employee_shift_start_minute + $check_in_grace_period[0]){
									$mark_Attendance = new Attendance();
									$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
									$mark_Attendance->att_check_in_time = date('H:i:s');
									$mark_Attendance->att_check_out_time = '00:00:00';
									$mark_Attendance->att_check_in_status = 1;
									$mark_Attendance->att_check_out_status = 0;
									$mark_Attendance->att_shift_start_time = $employee_shift_start;
									$mark_Attendance->att_shift_end_time = $employee_shift_end;
									$mark_Attendance->att_check_overall_status = 'Present';
									$mark_Attendance->att_for_date = $day;
									$mark_Attendance->att_for_month = $month;
									$mark_Attendance->att_for_year = $year;
									$mark_Attendance->att_check_in_source = 2;
									$mark_Attendance->att_remote_applicable = $remote_applicable;
									$process = $mark_Attendance->save();
									
									if($process == 1){

										return $this->api_response_json(TRUE,"Check In Successfully","",200);

									}else{

										return $this->api_response_json(FALSE,Self::SERVER_ERROR,"",500);

									}
								}else{
									$mark_Attendance = new Attendance();
									$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
									$mark_Attendance->att_check_in_time = date('H:i:s');
									$mark_Attendance->att_check_out_time = '00:00:00';
									$mark_Attendance->att_check_in_status = 1;
									$mark_Attendance->att_check_out_status = 0;
									$mark_Attendance->att_shift_start_time = $employee_shift_start;
									$mark_Attendance->att_shift_end_time = $employee_shift_end;
									$mark_Attendance->att_check_overall_status = 'Late Coming';
									$mark_Attendance->att_for_date = $day;
									$mark_Attendance->att_for_month = $month;
									$mark_Attendance->att_for_year = $year;
									$mark_Attendance->att_check_in_source = 2;
									$mark_Attendance->att_remote_applicable = $remote_applicable;
									$process = $mark_Attendance->save();

									if($process == 1){

										return $this->api_response_json(TRUE,"Check In Successfully","",200);


									}else{

										return $this->api_response_json(FALSE,Self::SERVER_ERROR,"",500);

									}
								}
							}elseif(date('H') > $employee_shift_start_hour){
								if(date('H:i') <= $exceed_final ){
									$mark_Attendance = new Attendance();
									$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
									$mark_Attendance->att_check_in_time = date('H:i:s');
									$mark_Attendance->att_check_out_time = '00:00:00';
									$mark_Attendance->att_check_in_status = 1;
									$mark_Attendance->att_check_out_status = 0;
									$mark_Attendance->att_shift_start_time = $employee_shift_start;
									$mark_Attendance->att_shift_end_time = $employee_shift_end;
									$mark_Attendance->att_check_overall_status = 'Late Coming';
									$mark_Attendance->att_for_date = $day;
									$mark_Attendance->att_for_month = $month;
									$mark_Attendance->att_for_year = $year;
									$mark_Attendance->att_check_in_source = 2;
									$mark_Attendance->att_remote_applicable = $remote_applicable;
									$process = $mark_Attendance->save();

									if($process == 1){

										return $this->api_response_json(TRUE,"Check In Successfully","",200);
										

									}else{

										return $this->api_response_json(FALSE,Self::SERVER_ERROR,"",500);

									}
								}else{
									$mark_Attendance = new Attendance();
									$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
									$mark_Attendance->att_check_in_time = date('H:i:s');
									$mark_Attendance->att_check_out_time = '00:00:00';
									$mark_Attendance->att_check_in_status = 1;
									$mark_Attendance->att_check_out_status = 0;
									$mark_Attendance->att_shift_start_time = $employee_shift_start;
									$mark_Attendance->att_shift_end_time = $employee_shift_end;
									$mark_Attendance->att_check_overall_status = 'Half day';
									$mark_Attendance->att_for_date = $day;
									$mark_Attendance->att_for_month = $month;
									$mark_Attendance->att_for_year = $year;
									$mark_Attendance->att_check_in_source = 2;
									$mark_Attendance->att_remote_applicable = $remote_applicable;
									$process = $mark_Attendance->save();

									if($process == 1){

										return $this->api_response_json(TRUE,"Check In Successfully","",200);


									}else{

										return $this->api_response_json(FALSE,Self::SERVER_ERROR,"",500);
										
										
									}
								}
							}else{
								$mark_Attendance = new Attendance();
								$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
								$mark_Attendance->att_check_in_time = date('H:i:s');
								$mark_Attendance->att_check_out_time = '00:00:00';
								$mark_Attendance->att_check_in_status = 1;
								$mark_Attendance->att_check_out_status = 0;
								$mark_Attendance->att_shift_start_time = $employee_shift_start;
								$mark_Attendance->att_shift_end_time = $employee_shift_end;
								$mark_Attendance->att_check_overall_status = 'Present';
								$mark_Attendance->att_for_date = $day;
								$mark_Attendance->att_for_month = $month;
								$mark_Attendance->att_for_year = $year;
								$mark_Attendance->att_check_in_source = 2;
								$mark_Attendance->att_remote_applicable = $remote_applicable;
								$process = $mark_Attendance->save();
								if($process == 1){

									return $this->api_response_json(TRUE,"Check In Successfully","",200);

								}else{

									return $this->api_response_json(FALSE,Self::SERVER_ERROR,"",500);

								}
							}
						}else{
							$mark_Attendance = new Attendance();
							$mark_Attendance->att_employee_id = $employee_data[0]['employee_id'];
							$mark_Attendance->att_check_in_time = date('H:i:s');
							$mark_Attendance->att_check_out_time = '00:00:00';
							$mark_Attendance->att_check_in_status = 1;
							$mark_Attendance->att_check_out_status = 0;
							$mark_Attendance->att_shift_start_time = $employee_shift_start;
							$mark_Attendance->att_shift_end_time = $employee_shift_end;
							$mark_Attendance->att_check_overall_status = 'Present';
							$mark_Attendance->att_for_date = $day;
							$mark_Attendance->att_for_month = $month;
							$mark_Attendance->att_for_year = $year;
							$mark_Attendance->att_check_in_source = 2;
							$mark_Attendance->att_remote_applicable = $remote_applicable;
							$process = $mark_Attendance->save();
							if($process == 1){

								return $this->api_response_json(TRUE,"Check In Successfully","",200);

							}else{

								return $this->api_response_json(FALSE,Self::SERVER_ERROR,"",500);
							}
						}
					}else{

						return $this->api_response_json(FALSE,"Unable to mark Attendance. Your shift timings not found.","",200);
					}    
				}else{
					
					if(in_array($get_attendance[0]['att_check_overall_status'],$manage_leaves) !== false){

						return $this->api_response_json(FALSE,"You can't check in, Your " . $get_attendance[0]['att_check_overall_status'] . " request is marked today","",200);

					}else{

						return $this->api_response_json(FALSE,"You've already checked in for today's shift","",200);
					}
				}
			}else{

				return $this->api_response_json(FALSE,"You can't mark attendance before your joining date.","",200);
			}
			
			
		}catch(\Exception $e){
            $this->create_error_log_from_api('Employees Attendance CheckIn API', $e , $request->header('Bearer-Token'));

			return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }finally{

            ActivityTracker::track_activity_from_api('Employees Attendance CheckIn API', $request->header('Bearer-Token'));

		}
	
       
    }
	
	protected function check_out(Request $request){
		try{

			
			$login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

			$employee_id = $login_user[0]['employee_id'];
		
			$login_user = Employees::where('employee_id' , $employee_id)->where('employee_isactive', 1)->get()->toArray();
		
			if(!empty($login_user)){
				$employee_id = 	$login_user[0]['employee_id'];
				
				$current_date  = strtotime(date('Y-m-d'));
				$doj  = strtotime($login_user[0]['employee_doj']);
				
				if($current_date >=  $doj){

					$manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

					$day = date("d");
					$month = date("m");
					$year = date("Y");

					$get_attendance = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get()->toArray();

					

					if(!empty($get_attendance)){

						$employee_shift_start = $this->get_employee_today_shift($login_user[0]['employee_id'],"in",date('Y-m-d'));
						$employee_shift_end = $this->get_employee_today_shift($login_user[0]['employee_id'],"out",date('Y-m-d'));
					
						// if($get_attendance[0]['att_check_out_time'] == '00:00:00' && ($get_attendance[0]['att_check_out_status'] == 0 || $get_attendance[0]['att_check_out_status'] == NULL) && in_array($get_attendance[0]['att_check_overall_status'],$manage_leaves) == false){
						
							$check_out = Attendance::find($get_attendance[0]['attendance_id']);

							$check_out->att_check_out_time = date('H:i:s');

							$check_out->att_check_out_status = 1;
							$check_out->att_check_out_source = 2;
							
							$employee_official_time = $this->calculate_minutes($employee_shift_start, $employee_shift_end);
							$current_shift_time = $this->calculate_minutes($get_attendance[0]['att_check_in_time'] , date('H:i:s'));
							$diff  = $employee_official_time - $current_shift_time;
							
							$check_out_grace_period = HrmSetting::pluck('check_out_flexibility_time')->toArray();
								
							if($diff >= 60){

								$check_out->att_check_overall_status = "Half day";

							}elseif($diff > $check_out_grace_period[0] && $diff < 60){

								$check_out->att_check_overall_status = "Early out";
		
							}elseif(abs($diff) >= 300 ){
								
								return $this->api_response_json(FALSE,"You are not allowed to checkout because you exceed 5 Hours from your shift.","",200);

							}

							$check_out->att_updated_at = date('Y-m-d H:i:s');
							$check_out->save();
							
							return $this->api_response_json(TRUE,"Check Out Successfully","",200);
						// }else{

						// 	if(in_array($get_attendance[0]['att_check_overall_status'],$manage_leaves) !== false){

						// 		return $this->api_response_json(FALSE,"You can't check out, Your " . $get_attendance[0]['att_check_overall_status'] . " request is marked today","",200);

						// 	}else{

						// 		return $this->api_response_json(FALSE,"You have already checked out for today's shift","",200);

						// 	}
						// }
						
					}else{
				
						$day = date('d',strtotime("-1 days"));

						$month = date('m',strtotime("-1 days"));

						$year = date('Y',strtotime("-1 days"));

						$get_attendance_yesterday = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get()->toArray();
						
						if(!empty($get_attendance_yesterday)){

							$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d'));
						    $employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d',strtotime("-1 days")));

							if($get_attendance_yesterday[0]['att_check_out_time'] == '00:00:00' && ($get_attendance_yesterday[0]['att_check_out_status'] == 0 || $get_attendance_yesterday[0]['att_check_out_status'] == NULL)){

								$check_out = Attendance::find($get_attendance_yesterday[0]['attendance_id']);

								$check_out->att_check_out_time = date('H:i:s');

								$check_out->att_check_out_status = 1;
								$check_out->att_check_out_source = 2;
								
								$employee_official_time = $this->calculate_minutes($employee_shift_start, $employee_shift_end);
								
								$yesterday_full_date = $get_attendance_yesterday[0]['att_for_year'] .'-'. $get_attendance_yesterday[0]['att_for_month'] .'-'. $get_attendance_yesterday[0]['att_for_date']. ' '. $get_attendance_yesterday[0]['att_check_in_time']; 
								$current_shift_time = $this->calculate_minutes_with_date($yesterday_full_date , date('Y-m-d H:i:s') );
								
								$check_out_grace_period = HrmSetting::pluck('check_out_flexibility_time')->toArray();
								
								$diff  = $employee_official_time - $current_shift_time;
								
								if($diff >= 60){

									$check_out->att_check_overall_status = "Half day";

								}elseif($diff > $check_out_grace_period[0] && $diff < 60){

									$check_out->att_check_overall_status = "Early out";
			
								}elseif(abs($diff) >= 300 ){
									
									return $this->api_response_json(FALSE,"You are not allowed to checkout because you exceed 5 Hours from your shift.","",200);

								}

								$check_out->att_updated_at = date('Y-m-d H:i:s');
								$check_out->save();

								return $this->api_response_json(TRUE,"Check Out Successfully","",200);
							}else{
								return $this->api_response_json(FALSE,"Kindly checkin first for today's shift","",200);

							}
							
						}else{

							return $this->api_response_json(FALSE,"Kindly checkin first for today's shift","",200);

						}
					}
						
				}else{
					
					return $this->api_response_json(FALSE,"You can't mark attendance before your joining date","",200);
				}

			}else{

				return $this->api_response_json(FALSE,"Wrong employee ID entered. Please try again.","",200);

			}
			
		}catch(\Exception $e){

            $this->create_error_log_from_api('Employees Attendance CheckOut API', $e, $request->header('Bearer-Token'));

			return $this->api_response_json(TRUE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{
			
            ActivityTracker::track_activity_from_api('Employees Attendance CheckOut API', $request->header('Bearer-Token'));
			
		}
	
		
	}
	

	protected function offline_attendance(Request $request){
		
		try{

			//return $this->api_response_json(FALSE,"This feature is not enable in this version of mobile app.","",500);
			
			$validator = Validator::make($request->post(), [
                'attendance_object' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

					return $this->api_response_json(FALSE,$error,"",200);
					
				}
			}
			
		
			$attendance_object  = json_decode($request->post('attendance_object'), true);
			
			$login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
			if(count($attendance_object) > 0 ){
				DB::table('tbl_offline_attendance_object')->insert([
					'att_offline_object' => $request->post('attendance_object'),
					
				]);
				
				foreach($attendance_object as $attendance){
					if(array_key_exists("checkin",$attendance)){
						$this->multiple_check_in_mobile($attendance['formatted_date'],$login_user[0]['employee_id'], 2);
						
					}
					if(array_key_exists("checkout",$attendance)){
						
						$this->multiple_check_out_mobile($attendance['formatted_date'], $login_user[0]['employee_id'],2);
	
					}
				}
				
				return $this->api_response_json(TRUE,"Attendance pushed to server successfully.","",200);


			}else{

				return $this->api_response_json(FALSE,"No record found.","",200);

			}

		}catch(\Exception $e){

			$this->create_error_log_from_api('Offline Attendance API', $e, $request->header('Bearer-Token'));
			
			return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

		}finally{
			
			ActivityTracker::track_activity_from_api('Offline Attendance API', $request->header('Bearer-Token'));
			
		}
	}

}
