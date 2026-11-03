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

    // status of a check in at $time: within the grace minutes of the shift start = Present, up to an hour late = Late Coming,
    // later = Half day. A flexible shift is always Present.
    protected function check_in_status_at($flexible, $shift_start, $time){

        if($flexible == '1'){
            return 'Present';
        }

        $grace = (int) (HrmSetting::value('check_in_flexibility_time') ?? 0);
        $late_minutes = (strtotime(date('Y-m-d', $time).' '.$shift_start) <= $time) ? ($time - strtotime(date('Y-m-d', $time).' '.$shift_start)) / 60 : 0;

        if($late_minutes <= $grace){
            return 'Present';
        }
        return ($late_minutes <= 60) ? 'Late Coming' : 'Half day';
    }

    // status after a check out: leaving an hour or more before the shift's length is done = Half day, leaving early beyond the
    // check out grace = Early out (a half day check in stays Half day), otherwise the check in status.
    protected function check_out_status($check_in_status, $shift_start, $shift_end, $worked_minutes){

        $shift_minutes = $this->calculate_minutes($shift_start, $shift_end);
        $short_minutes = $shift_minutes - $worked_minutes;
        $grace = (int) (HrmSetting::value('check_out_flexibility_time') ?? 0);

        if($short_minutes >= 60){
            return 'Half day';
        }
        if($short_minutes > $grace){
            return ($check_in_status == 'Half day') ? 'Half day' : 'Early out';
        }
        return $check_in_status;
    }

    // true when the employee has a check in for the day of $datetime (before 4 AM yesterday's check in also counts)
    protected function has_checked_in($employee_id, $datetime){
        $dates = [date('Y-m-d', strtotime($datetime))];
        if((int) date('H', strtotime($datetime)) < 4){
            $dates[] = date('Y-m-d', strtotime('-1 days', strtotime($datetime)));
        }
        foreach($dates as $date){
            $checked_in = Attendance::where('att_employee_id', $employee_id)->where('att_for_date', date('d', strtotime($date)))->where('att_for_month', date('m', strtotime($date)))->where('att_for_year', date('Y', strtotime($date)))->where('att_check_in_status', 1)->exists();
            if($checked_in){
                return true;
            }
        }
        return false;
    }

    protected function check_in(Request $request){

		try{

			$login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

			$employee_ID = $login_user[0]['employee_id'];

			if(strtotime(date('Y-m-d')) < strtotime($login_user[0]['employee_doj'])){
				return $this->api_response_json(FALSE,"You can't mark attendance before your joining date.","",200);
			}

			// the phone must be at the office (unless remote work / home job / geofencing off)
			$location_error = $this->mobile_attendance_location_error($employee_ID, $request->post('location_latitude'), $request->post('location_longitude'), date('Y-m-d'));
			if($location_error !== null){
				return $this->api_response_json(FALSE,$location_error,"",200);
			}

			$manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
			$remote_applicable = $this->remote_work_request_attendance($employee_ID,date('Y-m-d'));
			$employee_shift_start = $this->get_employee_today_shift($employee_ID,"in",date('Y-m-d'));
			$employee_shift_end = $this->get_employee_today_shift($employee_ID,"out",date('Y-m-d'));

			// yesterday's shift still open (night shift): no new check in yet
			$get_yesterday_attendance = Attendance::where('att_employee_id', $employee_ID)->where('att_for_date',date('d',strtotime("-1 days")))->where('att_for_month',date('m',strtotime("-1 days")))->where('att_for_year',date('Y',strtotime("-1 days")))->where('att_check_out_time', '00:00:00')->get()->toArray();
			if(!empty($get_yesterday_attendance)){
				$employee_official_time = $this->calculate_minutes($employee_shift_start , $employee_shift_end);
				$yesterday_full_date = $get_yesterday_attendance[0]['att_for_year'] .'-'. $get_yesterday_attendance[0]['att_for_month'] .'-'. $get_yesterday_attendance[0]['att_for_date']. ' '. $get_yesterday_attendance[0]['att_check_in_time'];
				$current_shift_time = $this->calculate_minutes_with_date($yesterday_full_date ,date('Y-m-d H:i:s'));
				if(abs($employee_official_time - $current_shift_time) <= 300 || $current_shift_time < 180){
					return $this->api_response_json(FALSE,"You've already checked in for today's shift","",200);
				}
			}

			$day = date("d");
			$month = date("m");
			$year = date("Y");

			// check in hours from config/attendance.php (MOBILE_CHECK_IN_FROM / MOBILE_CHECK_IN_UNTIL)
			$check_in_until = config('attendance.mobile_check_in_until');
			$check_in_from = config('attendance.mobile_check_in_from');

			$get_attendance = Attendance::where('att_employee_id', $employee_ID)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get()->toArray();
			$is_holiday_row = !empty($get_attendance) && strpos($get_attendance[0]['att_check_overall_status'], 'Holiday') !== false;

			if(!empty($get_attendance) && !$is_holiday_row && in_array($get_attendance[0]['att_check_overall_status'],$manage_leaves)){
				return $this->api_response_json(FALSE,"You can't check in, Your " . $get_attendance[0]['att_check_overall_status'] . " request is marked today","",200);
			}

			// check in after 1 PM (MOBILE_CHECK_IN_UNTIL) switched off for now
			// if($check_in_until !== null && $check_in_until !== '' && (int) date('H') >= (int) $check_in_until){
			// 	return $this->api_response_json(FALSE,"Sorry ! According to policy you can't mark Check In after ".date('g A', mktime((int) $check_in_until, 0)).".","",500);
			// }
			if($check_in_from !== null && $check_in_from !== '' && (int) date('H') < (int) $check_in_from){
				return $this->api_response_json(FALSE,"Sorry ! According to policy you can't mark Check In before ".date('g A', mktime((int) $check_in_from, 0)).".","",500);
			}

			if(empty($get_attendance) || $is_holiday_row){

				$employee_data = Employees::where('employee_id', $employee_ID)->get(['employee_id','employee_shift_flexibilty'])->toArray();

				if(empty($employee_data)){
					return $this->api_response_json(FALSE,"Unable to mark Attendance. Your shift timings not found.","",200);
				}

				// working on a holiday: the holiday row makes way for the attendance
				if($is_holiday_row){
					Attendance::where('attendance_id',$get_attendance[0]['attendance_id'])->delete();
				}

				$mark_attendance = new Attendance();
				$mark_attendance->att_employee_id = $employee_ID;
				$mark_attendance->att_check_in_time = date('H:i:s');
				$mark_attendance->att_check_out_time = '00:00:00';
				$mark_attendance->att_check_in_status = 1;
				$mark_attendance->att_check_out_status = 0;
				$mark_attendance->att_shift_start_time = $employee_shift_start;
				$mark_attendance->att_shift_end_time = $employee_shift_end;
				$mark_attendance->att_check_overall_status = $this->check_in_status_at($employee_data[0]['employee_shift_flexibilty'], $employee_shift_start, time());
				$mark_attendance->att_for_date = $day;
				$mark_attendance->att_for_month = $month;
				$mark_attendance->att_for_year = $year;
				$mark_attendance->att_comp_date = $year . '-' . $month . '-' . $day;
				$mark_attendance->att_check_in_source = 2;
				$mark_attendance->att_ci_lat = $request->post('location_latitude');
				$mark_attendance->att_ci_long = $request->post('location_longitude');
				$mark_attendance->att_remote_applicable = $remote_applicable;
				$mark_attendance->att_ci_marked_time = date('Y-m-d H:i:s');

				if($mark_attendance->save()){
					return $this->api_response_json(TRUE,"Check In Successfully","",200);
				}
				return $this->api_response_json(FALSE,Self::SERVER_ERROR,"",500);
			}

			// a row without a check in (checked out first) or an earlier check in time
			if($get_attendance[0]['att_check_in_status'] == '0' || date('H:i:s') < $get_attendance[0]['att_check_in_time']){

				$check_in = Attendance::find($get_attendance[0]['attendance_id']);
				$check_in->att_check_in_time = date('H:i:s');
				$check_in->att_check_in_status = 1;
				$check_in->att_check_in_source = 2;
				$check_in->att_ci_lat = $request->post('location_latitude');
				$check_in->att_ci_long = $request->post('location_longitude');
				$check_in->att_ci_marked_time = date('Y-m-d H:i:s');
				$check_in->save();

				return $this->api_response_json(TRUE,"Check In Successfully","",200);
			}

			return $this->api_response_json(FALSE,"You've already checked in for today's shift","",200);

		}catch(\Exception $e){
            $this->create_error_log_from_api('Employees Attendance CheckIn API', $e , $request->header('Bearer-Token'));

			return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }finally{

            ActivityTracker::track_activity_from_api('Employees Attendance CheckIn API', $request->header('Bearer-Token'), $request->header('apk-version'));

		}
    }

	protected function check_out(Request $request){
		try{

			$login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

			$login_user = Employees::where('employee_id' , $login_user[0]['employee_id'])->where('employee_isactive', 1)->get()->toArray();

			if(empty($login_user)){
				return $this->api_response_json(FALSE,"Wrong employee ID entered. Please try again.","",200);
			}

			$employee_id = $login_user[0]['employee_id'];

			if(strtotime(date('Y-m-d')) < strtotime($login_user[0]['employee_doj'])){
				return $this->api_response_json(FALSE,"You can't mark attendance before your joining date","",200);
			}

			// the phone must be at the office (unless remote work / home job / geofencing off)
			$location_error = $this->mobile_attendance_location_error($employee_id, $request->post('location_latitude'), $request->post('location_longitude'), date('Y-m-d'));
			if($location_error !== null){
				return $this->api_response_json(FALSE,$location_error,"",200);
			}

			$manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

			$day = date("d");
			$month = date("m");
			$year = date("Y");

			$get_attendance = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get()->toArray();

			$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d'));
			$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d'));

			// before 4 AM with no row today: the check out belongs to yesterday's shift (night shift)
			$checkout_date = date('Y-m-d');
			if(empty($get_attendance) && (int) date('H') < 4){
				$yesterday = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',date('d',strtotime("-1 days")))->where('att_for_month',date('m',strtotime("-1 days")))->where('att_for_year',date('Y',strtotime("-1 days")))->get()->toArray();
				if(!empty($yesterday)){
					$get_attendance = $yesterday;
					$checkout_date = date('Y-m-d', strtotime("-1 days"));
					$employee_shift_start = $this->get_employee_today_shift($employee_id,"in",$checkout_date);
					$employee_shift_end = $this->get_employee_today_shift($employee_id,"out",$checkout_date);
				}
			}

			if(!empty($get_attendance)){

				if(in_array($get_attendance[0]['att_check_overall_status'],$manage_leaves)){
					return $this->api_response_json(FALSE,"You can't check out, Your " . $get_attendance[0]['att_check_overall_status'] . " request is marked today","",200);
				}

				// a row without a check in (absent / holiday row): check in first
				if($get_attendance[0]['att_check_in_status'] != 1){
					return $this->api_response_json(FALSE,"Please check in first. You can check out after checking in.","",200);
				}

				$check_out = Attendance::find($get_attendance[0]['attendance_id']);
				$check_out->att_check_out_time = date('H:i:s');
				$check_out->att_check_out_status = 1;
				$check_out->att_check_out_source = 2;

				// worked time and status (a later check out of the same day works it out again)
				if($get_attendance[0]['att_check_in_status'] == 1 && in_array($get_attendance[0]['att_check_overall_status'], ['Present', 'Late Coming', 'Half day', 'Early out'])){
					$employee_flexibility = Employees::where('employee_id', $employee_id)->value('employee_shift_flexibilty');
					$check_in_at = strtotime($checkout_date.' '.$get_attendance[0]['att_check_in_time']);
					$check_in_status = $this->check_in_status_at($employee_flexibility, $employee_shift_start, $check_in_at);
					$worked_minutes = (time() - $check_in_at) / 60;
					$check_out->att_check_overall_status = $this->check_out_status($check_in_status, $employee_shift_start, $employee_shift_end, $worked_minutes);
				}

				$check_out->att_updated_at = date('Y-m-d H:i:s');
				$check_out->att_co_lat = $request->post('location_latitude');
				$check_out->att_co_long = $request->post('location_longitude');
				$check_out->att_co_marked_time = date('Y-m-d H:i:s');
				$check_out->save();

				return $this->api_response_json(TRUE,"Check Out Successfully","",200);
			}

			// no check in today: check in first
			return $this->api_response_json(FALSE,"Please check in first. You can check out after checking in.","",200);

		}catch(\Exception $e){

            $this->create_error_log_from_api('Employees Attendance CheckOut API', $e, $request->header('Bearer-Token'));

			return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

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
					'att_offline_emp_id' => $login_user[0]['employee_id'],
					'att_created_at' => date('Y-m-d H:i:s'),
				]);

				$rejected = [];
				foreach($attendance_object as $attendance){
					if(empty($attendance['formatted_date'])){
						continue;
					}
					// same office location rule as the online check in / out, with the location saved on the phone
					$location_error = $this->mobile_attendance_location_error($login_user[0]['employee_id'], $attendance['location_latitude'] ?? null, $attendance['location_longitude'] ?? null, date('Y-m-d', strtotime($attendance['formatted_date'])));
					if($location_error !== null){
						$rejected[] = date('d M Y h:i A', strtotime($attendance['formatted_date'])).': '.$location_error;
						continue;
					}
					if(array_key_exists("checkin",$attendance)){
						$this->multiple_check_in_mobile($attendance['formatted_date'],$login_user[0]['employee_id'], 2 , ($attendance['location_latitude']) ?? NULL , ($attendance['location_longitude']) ?? NULL);
					}
					if(array_key_exists("checkout",$attendance)){
						// check out only after a check in (same day, or yesterday's shift before 4 AM)
						if(!$this->has_checked_in($login_user[0]['employee_id'], $attendance['formatted_date'])){
							$rejected[] = date('d M Y h:i A', strtotime($attendance['formatted_date'])).': Please check in first. You can check out after checking in.';
							continue;
						}
						$this->multiple_check_out_mobile($attendance['formatted_date'], $login_user[0]['employee_id'],2 , ($attendance['location_latitude']) ?? NULL , ($attendance['location_longitude']) ?? NULL);
					}
				}

				if(!empty($rejected)){
					return $this->api_response_json(FALSE,"Some attendance was not marked. See the details for each entry.",$rejected,200);
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