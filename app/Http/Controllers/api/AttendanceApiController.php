<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{Attendance,Employees,LeaveManagement,HrmSetting};
use Illuminate\Support\Facades\Validator;

use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait};
use App\Helpers\{ActivityTracker, CustomHelper};
use DatePeriod,DateTime,DateInterval;
use Illuminate\Support\Facades\DB;

 

class AttendanceApiController extends Controller
{    
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait;

    const TITLE_GLOBAL2 = "my-attendance";

    protected function personal_attendace(Request $request){
        try{
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            
            // if($request->get('m') != '' && is_numeric($request->get('m')) && $request->get('m') >= 01 && $request->get('m') <= 12 && $request->get('y') != '' && is_numeric($request->get('y')) && $request->get('y') >= 1900 && $request->get('y') <= 3000){

            //     $month_days = cal_days_in_month(CAL_MONTH_GREGORIAN_LONG, $request->get('m'),$request->get('y'));
            //     $month = $request->get('m');
            //     $year =  $request->get('y');

            // }else{
            //     $month_days = date('t');
            //     $month = date('m');
            //     $year =  date('Y');
              
            // }

            // default: the current payroll period so far (it starts on the payroll start day from the settings, e.g. 25th)
            $cycle_day = (int) (HrmSetting::value('payroll_start_date') ?: 1);
            $from_date = ((int) date('d') >= $cycle_day) ? date('Y-m-').sprintf('%02d', $cycle_day) : date('Y-m-', strtotime('first day of previous month')).sprintf('%02d', $cycle_day);
            $to_date = date('Y-m-d');

            if(!empty($request->get('from_date')) || !empty($request->get('to_date'))){
                $validator = Validator::make($request->all(), [
                    'from_date' => 'required|date_format:Y-m-d',
                    'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
                ]);
                if($validator->fails()){
                    return $this->api_response_json(FALSE,$validator->errors()->first(),"",200);
                }
                $from_date = $request->get('from_date');
                $to_date = $request->get('to_date');
                if((new DateTime($from_date))->diff(new DateTime($to_date))->days > 62){
                    return $this->api_response_json(FALSE,"Please select up to 62 days.","",200);
                }
            }

            $startDate = new DateTime($from_date);
            $endDate = new DateTime($to_date);

            $interval = $startDate->diff($endDate);
            $month_days = $interval->days + 1;

            // dd($numberOfDays);

            $attendance = Attendance::whereBetween('att_comp_date', [$from_date , $to_date])->where([
                'att_employee_id'=>$login_user[0]['employee_id'],
                // 'att_for_month'=>$month,
                // 'att_for_year'=>$year
            ])->get()->toArray();

            // dd($attendance);
             
            // $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

            // $t_employee_leaves = Employees::where('employee_isactive',1)->join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where(['att_employee_id'=>$login_user[0]['employee_id'],'att_for_month'=>$month,'att_for_year'=>$year])->where(function($query) use($manage_leaves){
            //     $query->whereIn('att_check_overall_status' ,$manage_leaves)
            //     ->orWhere('att_check_overall_status','RPL');
            // })->count();
           
            $login_user[0]['employee_offdays'] = (!empty($login_user[0]['employee_offdays'])) ? explode(",",$login_user[0]['employee_offdays']):[];
            $off_days =  $login_user[0]['employee_offdays'];   
        
            // $data = compact('title','attendance','month_days','t_employee_leaves','month','year','off_days','manage_leaves');

            // $data = compact('attendance','month_days','month','year','off_days');
            // day by day: the marked status, else Off Day / Absent (not counted before joining or after today)
            $by_date = [];
            foreach($attendance as $row){
                $by_date[$row['att_comp_date']] = $row;
            }
            $days = [];
            $summary = ['present' => 0, 'late' => 0, 'half_day' => 0, 'early_out' => 0, 'absent' => 0, 'leave' => 0, 'holiday' => 0, 'off_day' => 0];
            $leave_names = LeaveManagement::pluck('leave_name')->toArray();
            for($date = $from_date; $date <= $to_date; $date = date('Y-m-d', strtotime($date.' +1 day'))){
                $row = $by_date[$date] ?? null;
                if($row){
                    $status = $row['att_check_overall_status'];
                }elseif($date < $login_user[0]['employee_doj'] || $date > date('Y-m-d')){
                    $status = '-';
                }elseif(in_array(date('l', strtotime($date)), array_map('trim', $off_days))){
                    $status = 'Off Day';
                }else{
                    $status = ($date == date('Y-m-d')) ? 'Not marked yet' : 'Absent';
                }

                if($status == 'Present' || $status == 'RPL'){ $summary['present']++; }
                elseif($status == 'Late Coming'){ $summary['late']++; }
                elseif($status == 'Half day'){ $summary['half_day']++; }
                elseif($status == 'Early out'){ $summary['early_out']++; }
                elseif($status == 'Absent'){ $summary['absent']++; }
                elseif($status == 'Off Day'){ $summary['off_day']++; }
                elseif(strpos($status, 'Holiday') !== false){ $summary['holiday']++; }
                elseif(in_array($status, $leave_names)){ $summary['leave']++; }

                $days[] = [
                    'date' => $date,
                    'day' => date('l', strtotime($date)),
                    'status' => $status,
                    'check_in_time' => ($row && $row['att_check_in_status'] == 1) ? $row['att_check_in_time'] : null,
                    'check_out_time' => ($row && $row['att_check_out_status'] == 1) ? $row['att_check_out_time'] : null,
                    'shift_start' => $row['att_shift_start_time'] ?? null,
                    'shift_end' => $row['att_shift_end_time'] ?? null,
                ];
            }

            $data = compact('attendance','from_date','to_date','month_days','off_days','days','summary');

            return $this->api_response_json(TRUE,"My Attendance",$data,200);

		
        }catch(\Exception $e){
            
            $this->create_error_log_from_api('View My Attendance API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('View My Attendance API',  $request->header('Bearer-Token'), $request->header('apk-version'));

        }
    }


    protected function get_todays_attendance(Request $request){
        try{
      
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $get_todays_attendance = Attendance::where([
                'att_employee_id' => $login_user[0]['employee_id'],
                'att_for_month'=>date('m'),
                'att_for_year'=>date('Y'),
                'att_for_date'=>date('d')
            ])->get(['att_check_in_time as check_in_time','att_check_in_status as check_in_status','att_check_out_time as check_out_time','att_check_out_status as check_out_status','att_check_overall_status as attendance_status'])->toArray();

            return $this->api_response_json(TRUE,"My Today's Attendance",$get_todays_attendance,200);

		
        }catch(\Exception $e){
            
            $this->create_error_log_from_api('View My Attendance API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('View My Attendance API',  $request->header('Bearer-Token'), $request->header('apk-version'));

        }
    }
}
