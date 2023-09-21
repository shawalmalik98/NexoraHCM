<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Crypt, Schema};
use App\Models\{Attendance, Employees, MacAddress, WFH, HrmSetting, LateComers, RequestApproval,LeaveManagement};
use App\Helpers\ActivityTracker;
use App\Traits\{LogGenerateTrait, AttendanceEmployee, LoginEmployee};

class MarkAttendanceController extends Controller
{
	use LogGenerateTrait, AttendanceEmployee, LoginEmployee;

    const SERVER_ERROR = 'Unknown error occured on server. Please try again after some time.';
	
	protected function index(){
		try{
			return view('attendance.attendance-marking');
		}catch(\Exception $e){
            $this->create_error_log('Employees Attendance Marking Access', $e);
			return view('errors.500');
        }finally{
            ActivityTracker::track_activity('Employees Attendance Marking');
			
		}
	}

    protected function check_in(Request $attendance_marker){
		try{

			$employee_id = $attendance_marker->input('employee_id');
			$employee_password = $attendance_marker->input('employee_password');
		
			if(!empty($employee_id) && !empty($employee_password)){

				$get_employee_decoded = Employees::where('employee_code' , $employee_id)->where('employee_isactive', 1)->get(['employee_id','employee_doj','employee_jobtype','employee_shift_flexibilty','employee_password'])->toArray();
				$leave_names = LeaveManagement::pluck('leave_name')->toArray();
				if(!empty($get_employee_decoded)){
					if(Crypt::decryptString($get_employee_decoded[0]['employee_password']) == $employee_password){
						
						$current_date  = strtotime(date('Y-m-d'));
						$doj = strtotime($get_employee_decoded[0]['employee_doj']);

						if($current_date >= $doj){

							$remote_applicable = $this->remote_work_request_attendance($get_employee_decoded[0]['employee_id'],date('Y-m-d'));
						
							$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d'));
							$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d'));
							

							$get_wfh_request = WFH::where('wfh_employee_id','=',$get_employee_decoded[0]['employee_id'])->where('wfh_request_date','=',date('Y-m-d'))->get(['wfh_request_approved_by_hr','wfh_request_id'])->toArray();

							if(!empty($get_wfh_request) && $get_wfh_request[0]['wfh_request_approved_by_hr'] != 2){
								$get_wfh_request = RequestApproval::where('request_module_id',3)
								->where('request_id',$get_wfh_request[0]['wfh_request_id'])
								->where('request_approver_status',2)
								->limit(1)
								->get()->toArray();
							}
						
							$get_ip_address = MacAddress::where('mac_address', $attendance_marker->ip())->get()->toArray();
						
							if(!empty($get_ip_address) || (empty($get_ip_address) && !empty($get_wfh_request)) || ($get_employee_decoded[0]['employee_jobtype'] == "Home")){
							
								$day = date('d',strtotime("-1 days"));
								$month = date('m',strtotime("-1 days"));
								$year = date('Y',strtotime("-1 days"));
			
								$get_yesterday_attendance = Attendance::where('att_employee_id', $get_employee_decoded[0]['employee_id'])->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->where('att_check_out_time', '00:00:00')->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time'])->toArray();
							
								if(!empty($get_yesterday_attendance)){
									
									$employee_official_time = $this->calculate_minutes($employee_shift_start , $employee_shift_end);
									
									$yesterday_full_date = $get_yesterday_attendance[0]['att_for_year'] .'-'. $get_yesterday_attendance[0]['att_for_month'] .'-'. $get_yesterday_attendance[0]['att_for_date']. ' '. $get_yesterday_attendance[0]['att_check_in_time']; 
									$current_shift_time = $this->calculate_minutes_with_date( $yesterday_full_date ,date('Y-m-d H:i:s') );
									
									$diff = $employee_official_time - $current_shift_time;

									if(abs($diff) <= 300 || $current_shift_time < 180){
										return response()->json([
											'status' => 'FALSE',
											'msg' => "You've already checked in for today's shift.",
										]);
									}
			
								}
								
								$day = date("d");
								$month = date("m");
								$year = date("Y");
								
								$get_attendance = Attendance::where('att_employee_id', $get_employee_decoded[0]['employee_id'])->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time'])->toArray();
								
								
								if(!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false ){
									Attendance::where('attendance_id',$get_attendance[0]['attendance_id'])->delete();
								} 
								
								if(empty($get_attendance) || (!empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false)){
									if(!empty($get_employee_decoded)){
										if($get_employee_decoded[0]['employee_shift_flexibilty'] != '1'){
											$employee_shift_start_hour = date("H", strtotime($employee_shift_start));
											$employee_shift_start_minute = date("i", strtotime($employee_shift_start));
											$exceed = strtotime("+1 hours", strtotime($employee_shift_start));
											$exceed_final = date('H:i', $exceed);
											$check_in_grace_period = HrmSetting::where('setting_id', 1)->pluck('check_in_flexibility_time')->toArray();
											if($employee_shift_start_hour == date('H')){
												if(date('i') <= $employee_shift_start_minute + $check_in_grace_period[0]){
													$mark_Attendance = new Attendance();
													$mark_Attendance->att_employee_id = $get_employee_decoded[0]['employee_id'];
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
													$mark_Attendance->att_remote_applicable = $remote_applicable;
													$process = $mark_Attendance->save();
													
													if($process == 1){
														return response()->json([
															'status' => 'CheckIN',
														]);
													}else{
														return response()->json([
															'status' => 'FALSE',
															'msg' => Self::SERVER_ERROR,
														]);
													}
												}else{
													$mark_Attendance = new Attendance();
													$mark_Attendance->att_employee_id = $get_employee_decoded[0]['employee_id'];
													$mark_Attendance->att_check_in_time = date('H:i:s');
													$mark_Attendance->att_check_out_time = '00:00:00';
													$mark_Attendance->att_check_in_status = 1;
													$mark_Attendance->att_check_out_status = 0;
													$mark_Attendance->att_shift_start_time  = $employee_shift_start;
													$mark_Attendance->att_shift_end_time  = $employee_shift_end ;
													$mark_Attendance->att_check_overall_status = 'Late Coming';
													$mark_Attendance->att_for_date = $day;
													$mark_Attendance->att_for_month = $month;
													$mark_Attendance->att_for_year = $year;
													$mark_Attendance->att_remote_applicable = $remote_applicable;
													$process = $mark_Attendance->save();

													if($process == 1){
														return response()->json([
															'status' => 'CheckIN',
														]);
													}else{
														return response()->json([
															'status' => 'FALSE',
															'msg' => Self::SERVER_ERROR,
														]);
													}
												}
											}elseif(date('H') > $employee_shift_start_hour){
												if(date('H:i') <= $exceed_final ){
													$mark_Attendance = new Attendance();
													$mark_Attendance->att_employee_id = $get_employee_decoded[0]['employee_id'];
													$mark_Attendance->att_check_in_time = date('H:i:s');
													$mark_Attendance->att_check_out_time = '00:00:00';
													$mark_Attendance->att_check_in_status = 1;
													$mark_Attendance->att_check_out_status = 0;
													$mark_Attendance->att_shift_start_time  = $employee_shift_start;
													$mark_Attendance->att_shift_end_time  = $employee_shift_end ;
													$mark_Attendance->att_check_overall_status = 'Late Coming';
													$mark_Attendance->att_for_date = $day;
													$mark_Attendance->att_for_month = $month;
													$mark_Attendance->att_for_year = $year;
													$mark_Attendance->att_remote_applicable = $remote_applicable;
													$process = $mark_Attendance->save();

													if($process == 1){
														return response()->json([
															'status' => 'CheckIN',
														]);
													}else{
														return response()->json([
															'status' => 'FALSE',
															'msg' => Self::SERVER_ERROR,
														]);
													}
												}else{
													$mark_Attendance = new Attendance();
													$mark_Attendance->att_employee_id = $get_employee_decoded[0]['employee_id'];
													$mark_Attendance->att_check_in_time = date('H:i:s');
													$mark_Attendance->att_check_out_time = '00:00:00';
													$mark_Attendance->att_check_in_status = 1;
													$mark_Attendance->att_check_out_status = 0;
													$mark_Attendance->att_shift_start_time  = $employee_shift_start;
													$mark_Attendance->att_shift_end_time  = $employee_shift_end ;
													$mark_Attendance->att_check_overall_status = 'Half day';
													$mark_Attendance->att_for_date = $day;
													$mark_Attendance->att_for_month = $month;
													$mark_Attendance->att_for_year = $year;
													$mark_Attendance->att_remote_applicable = $remote_applicable;
													$process = $mark_Attendance->save();

													if($process == 1){
														return response()->json([
															'status' => 'CheckIN',
														]);
													}else{
														return response()->json([
															'status' => 'FALSE',
															'msg' => Self::SERVER_ERROR,
														]);
													}
												}
											}else{
												$mark_Attendance = new Attendance();
												$mark_Attendance->att_employee_id = $get_employee_decoded[0]['employee_id'];
												$mark_Attendance->att_check_in_time = date('H:i:s');
												$mark_Attendance->att_check_out_time = '00:00:00';
												$mark_Attendance->att_check_in_status = 1;
												$mark_Attendance->att_check_out_status = 0;
												$mark_Attendance->att_shift_start_time  = $employee_shift_start;
												$mark_Attendance->att_shift_end_time  = $employee_shift_end ;
												$mark_Attendance->att_check_overall_status = 'Present';
												$mark_Attendance->att_for_date = $day;
												$mark_Attendance->att_for_month = $month;
												$mark_Attendance->att_for_year = $year;
												$mark_Attendance->att_remote_applicable = $remote_applicable;
												$process = $mark_Attendance->save();
												if($process == 1){
													return response()->json([
														'status' => 'CheckIN',
													]);
												}else{
													return response()->json([
														'status' => 'FALSE',
														'msg' => Self::SERVER_ERROR,
													]);
												}
											}
										}else{
											$mark_Attendance = new Attendance();
											$mark_Attendance->att_employee_id = $get_employee_decoded[0]['employee_id'];
											$mark_Attendance->att_check_in_time = date('H:i:s');
											$mark_Attendance->att_check_out_time = '00:00:00';
											$mark_Attendance->att_check_in_status = 1;
											$mark_Attendance->att_check_out_status = 0;
											$mark_Attendance->att_shift_start_time  = $employee_shift_start;
											$mark_Attendance->att_shift_end_time  = $employee_shift_end ;
											$mark_Attendance->att_check_overall_status = 'Present';
											$mark_Attendance->att_for_date = $day;
											$mark_Attendance->att_for_month = $month;
											$mark_Attendance->att_for_year = $year;
											$mark_Attendance->att_remote_applicable = $remote_applicable;
											$process = $mark_Attendance->save();
											if($process == 1){
												return response()->json([
													'status' => 'CheckIN',
												]);
											}else{
												return response()->json([
													'status' => 'FALSE',
													'msg' => Self::SERVER_ERROR,
												]);
											}
										}
									}else{
										return response()->json([
											'status' => 'FALSE',
											'msg' => 'Unable to mark Attendance. Your shift timings not found.',
										]);
									}    
								}else{
									
									if((in_array($get_attendance[0]['att_check_overall_status'],$leave_names)) !== false){
										return response()->json([
											'status' => 'FALSE',
											'msg' =>  "You can't check in, Your " . $get_attendance[0]['att_check_overall_status'] . " request is marked today",
										]);
									}else{
										return response()->json([
											'status' => 'FALSE',
											'msg' =>  "You've already checked in for today's shift",
										]);
									}
								}
							}else{
								return response()->json([
									'status' => 'FALSE',
									'msg' => 'You can\'t mark attendance from this device',
								]);
							}
						}else{
							
							return response()->json([
								'status' => 'FALSE',
								'msg' =>  "You can't mark attendance before your joining date",
							]);
						}
					}
					else{
							return response()->json([
								'status' => 'FALSE',
								'msg' =>  "Wrong password entered. Please try again.",
							]);
					}
				}else{
					return response()->json([
						'status' => 'FALSE',
						'msg' =>  "Wrong employee id entered. Please try again.",
					]);
				}
			}else{
				return response()->json([
					'status' => 'FALSE',
					'msg' =>  "Employee id and password is required.",
				]);
			}
			
		}catch(\Exception $e){
            $this->create_error_log('Employees Attendance CheckIn', $e);
			if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
					'msg' => Self::SERVER_ERROR,
                   
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
					'msg' => Self::SERVER_ERROR,
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
               
            }
			
        }finally{
            ActivityTracker::track_activity('Employees Attendance CheckIn');
		}
	
       
    }
	
	protected function check_out(Request $attendance_marker){
		try{

			$get_ip_address = MacAddress::where('mac_address', $attendance_marker->ip())->get()->toArray();

			// if(!empty($get_ip_address)){
			
				$employee_id = $attendance_marker->input('employee_id');
				$employee_password = $attendance_marker->input('employee_password');
				if(!empty($employee_id) && !empty($employee_password)){

					$get_employee_decoded = Employees::where('employee_code' , $employee_id)->where('employee_isactive', 1)->get(['employee_id','employee_doj','employee_jobtype','employee_shift_flexibilty','employee_password'])->toArray();
					$leave_names = LeaveManagement::pluck('leave_name')->toArray();
					if(!empty($get_employee_decoded)){
						$employee_id = $get_employee_decoded[0]['employee_id'];
						if(Crypt::decryptString($get_employee_decoded[0]['employee_password']) == $employee_password){
							
							$current_date = strtotime(date('Y-m-d'));
							$doj = strtotime($get_employee_decoded[0]['employee_doj']);
							
							if($current_date >= $doj){
								$day = date("d");
								$month = date("m");
								$year = date("Y");

								$get_attendance = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();

								if(!empty($get_attendance)){
									$date = date('Y-m-d');
								}else{

									$date = date('Y-m-d',strtotime("-1 days"));
								}
								
								$get_wfh_request = WFH::where('wfh_employee_id','=',$get_employee_decoded[0]['employee_id'])
								->where('wfh_request_date','=',$date)
								->get(['wfh_request_id','wfh_request_approved_by_hr'])->toArray();

								if(!empty($get_wfh_request) && $get_wfh_request[0]['wfh_request_approved_by_hr'] != 2){
									$get_wfh_request = RequestApproval::where('request_module_id',3)
									->where('request_id',$get_wfh_request[0]['wfh_request_id'])
									->where('request_approver_status',2)
									->limit(1)
									->get()->toArray();
								}
				
								$get_ip_address = MacAddress::where('mac_address', $attendance_marker->ip())->get()->toArray();
								
								if(!empty($get_ip_address) || (empty($get_ip_address) && !empty($get_wfh_request)) || ($get_employee_decoded[0]['employee_jobtype'] == "Home")){

									if(!empty($get_attendance)){

										$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d'));
										$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d'));
									
										if($get_attendance[0]['att_check_out_time'] == '00:00:00' && ($get_attendance[0]['att_check_out_status'] == '0' || $get_attendance[0]['att_check_out_status'] == NULL) && in_array($get_attendance[0]['att_check_overall_status'],$leave_names) == false){

											$check_out = Attendance::find($get_attendance[0]['attendance_id']);

											$check_out->att_check_out_time = date('H:i:s');

											$check_out->att_check_out_status = 1;
											$check_out->att_check_out_source = 1;
											
											$check_out->att_updated_at = date('Y-m-d H:i:s');
											
											
											$employee_official_time = $this->calculate_minutes($employee_shift_start , $employee_shift_end);
											$current_shift_time = $this->calculate_minutes($get_attendance[0]['att_check_in_time'] , date('H:i:s'));
											$diff  = $employee_official_time - $current_shift_time;
											
											$check_out_grace_period = HrmSetting::where('setting_id', 1)->pluck('check_out_flexibility_time')->toArray();
											
											
											if($diff >= 60){

												$check_out->att_check_overall_status = "Half day";

											}elseif($diff > $check_out_grace_period[0] && $diff < 60){

												$check_out->att_check_overall_status = "Early out";
											}             
											elseif(abs($diff) >= 300 ){
												return response()->json([
													'status' => 'FALSE',
													'msg' => "You are not allowed to checkout because you exceed 5 Hours from your shift",
												]);
											}

											$check_out->save();

											return response()->json([
												'status' => 'CheckOUT',
											]);

										}else{

											if((in_array($get_attendance[0]['att_check_overall_status'],$leave_names)) !== false){
												return response()->json([
													'status' => 'FALSE',
													'msg' => "You can't check out, Your " . $get_attendance[0]['att_check_overall_status'] . " request is marked today",
												]);
											}else{
												return response()->json([
													'status' => 'FALSE',
													'msg' => "You have already checked out for today's shift",
												]);
											}
										}
									}else{
								
										$day = date('d',strtotime("-1 days"));

										$month = date('m',strtotime("-1 days"));

										$year = date('Y',strtotime("-1 days"));

										$get_attendance_yesterday = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['att_employee_id','attendance_id','att_for_date','att_for_month','att_for_year','att_check_overall_status','att_check_out_time','att_check_in_time','att_check_out_status'])->toArray();

										if(!empty($get_attendance_yesterday)){

											$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d',strtotime("-1 days")));
											$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d',strtotime("-1 days")));

											if($get_attendance_yesterday[0]['att_check_out_time'] == '00:00:00' && ($get_attendance_yesterday[0]['att_check_out_status'] == '0' || $get_attendance_yesterday[0]['att_check_out_status'] == NULL)){

												$check_out = Attendance::find($get_attendance_yesterday[0]['attendance_id']);

												$check_out->att_check_out_time = date('H:i:s');

												$check_out->att_check_out_status = 1;
												$check_out->att_check_out_source = 1;

												$check_out->att_updated_at = date('Y-m-d H:i:s');
												
												$employee_official_time = $this->calculate_minutes($employee_shift_start , $employee_shift_end);
												
												$yesterday_full_date = $get_attendance_yesterday[0]['att_for_year'] .'-'. $get_attendance_yesterday[0]['att_for_month'] .'-'. $get_attendance_yesterday[0]['att_for_date']. ' '. $get_attendance_yesterday[0]['att_check_in_time']; 
												$current_shift_time = $this->calculate_minutes_with_date($yesterday_full_date , date('Y-m-d H:i:s') );
												
												$diff  = $employee_official_time - $current_shift_time;
												if($diff >= 60){
													
													$check_out->att_check_overall_status = "Half day";  

												}elseif($diff > $check_out_grace_period[0] && $diff < 60){

													$check_out->att_check_overall_status = "Early out"; 
													
												}elseif(abs($diff) >= 300 ){
													
													return response()->json([
														'status' => 'FALSE',
														'msg' => "You are not allowed to checkout because you exceed 5 Hours from your shift.",
														
													]);
												}

												$check_out->save();

												return response()->json([
													'status' => 'CheckOUT',
													
													
												]);

											}else{

												return response()->json([
													'status' => 'FALSE',
													'msg' => "Kindly checkin first for today's shift",
												]);

											}
										}else{
											return response()->json([
												'status' => 'FALSE',
												'msg' => "Kindly checkin first for today's shift",
											]);
										}
									}
									
									
								}else{
									return response()->json([
										'status' => 'FALSE',
										'msg' => 'You can\'t mark attendance from this device',
									]);
								}
							}else{
							
								return response()->json([
									'status' => 'FALSE',
									'msg' =>  "You can't mark attendance before your joining date",
								]);
							}

						}	
						else{
							return response()->json([
								'status' => 'FALSE',
								'msg' => 'Wrong password entered. Please try again.',
							]);
						

						}

					}else{

						return response()->json([
							'status' => 'FALSE',
							'msg' => "Wrong employee id entered. Please try again.",
							
						]);

					}

				}else{

					return response()->json([
						'status' => 'FALSE',
						'msg' => "'Employee id and password is required.",
					]);
				}
			
		}catch(\Exception $e){
            $this->create_error_log('Employees Attendance CheckOut', $e);
			if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => Self::SERVER_ERROR,
                   
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
					'msg' => Self::SERVER_ERROR,
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
               
            }
			
        }finally{
            ActivityTracker::track_activity('Employees Attendance CheckOut');
			
		}
	
		
	}
	
}
