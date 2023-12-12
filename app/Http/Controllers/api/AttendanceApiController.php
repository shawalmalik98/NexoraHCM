<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{Attendance,Employees,LeaveManagement};

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

            if(empty($request->get('from_date')) && empty($request->get('to_date'))){
                if(date('d') >= '26'){
                    $from_date = date('Y-m-26');
                    $to_date = date('Y-m-d');
                }else{
                    $today = new DateTime();
                    $today->modify('first day of previous month');
                    $today->modify('+25 days');
                    $from_date = $today->format('Y-m-d');
                    $to_date = date('Y-m-d');
                }
            }

            if(!empty($request->get('from_date')) && !empty($request->get('to_date'))){
                $from_date = $request->get('from_date');
                $to_date = $request->get('to_date');
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
            $data = compact('attendance','from_date','to_date','month_days','off_days');

            return $this->api_response_json(TRUE,"My Attendance",$data,200);

		
        }catch(\Exception $e){
            
            $this->create_error_log_from_api('View My Attendance API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('View My Attendance API',  $request->header('Bearer-Token'));

        }
    }

    protected function personal_attendace_sap_api(Request $request){
        // try{
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            // dd($login_user);
            // if($request->get('m') != '' && is_numeric($request->get('m')) && $request->get('m') >= 01 && $request->get('m') <= 12 && $request->get('y') != '' && is_numeric($request->get('y')) && $request->get('y') >= 1900 && $request->get('y') <= 3000){

            //     $month_days = cal_days_in_month(CAL_MONTH_GREGORIAN_LONG, $request->get('m'),$request->get('y'));
            //     $month = $request->get('m');
            //     $year =  $request->get('y');

            // }else{
            //     $month_days = date('t');
            //     $month = date('m');
            //     $year =  date('Y');
              
            // }

            if(empty($request->get('from_date')) && empty($request->get('to_date'))){
                if(date('d') >= '26'){
                    $from_date = date('Y-m-26');
                    $to_date = date('Y-m-d');
                }else{
                    $today = new DateTime();
                    $today->modify('first day of previous month');
                    $today->modify('+25 days');
                    $from_date = $today->format('Y-m-d');
                    $to_date = date('Y-m-d');
                }
            }

            if(!empty($request->get('from_date')) && !empty($request->get('to_date'))){
                $from_date = $request->get('from_date');
                $to_date = $request->get('to_date');
            }

            $startDate = new DateTime($from_date);
            $endDate = new DateTime($to_date);

            $interval = $startDate->diff($endDate);
            $month_days = $interval->days + 1;

            // $attendance = Attendance::whereBetween('att_comp_date', [$from_date , $to_date])->where([
            //     'att_employee_id'=>$login_user[0]['employee_id'],
            //     // 'att_for_month'=>$month,
            //     // 'att_for_year'=>$year
            // ])->get()->toArray();

            $attendance = DB::select("
            SELECT emp_code, 
                attendance_day, 
                attendance_date as att_comp_date, 
                YEAR(attendance_date) AS att_for_year, 
                FORMAT(attendance_date, 'MM') AS att_for_month, 
                FORMAT(attendance_date, 'dd') AS att_for_date,   
                time_in as att_check_in_time, 
                time_out as att_check_out_time, 
                attendance_status as att_check_overall_status,
                NULL AS attendance_id,
                NULL AS att_employee_id,
                NULL AS att_check_in_status,
                NULL AS att_check_out_status,
                NULL AS att_shift_start_time,
                NULL AS att_shift_end_time,
                NULL AS att_ci_lat,
                NULL AS att_ci_long,
                NULL AS att_co_lat,
                NULL AS att_co_long,
                NULL AS att_remote_applicable,
                NULL AS att_request_approval,
                NULL AS att_check_in_source,
                NULL AS att_check_out_source,
                NULL AS att_created_at,
                NULL AS att_updated_at,
                NULL AS sap_ci_flag,
                NULL AS sap_co_flag,
                NULL AS sap_ci_timestamp,
                NULL AS sap_co_timestamp,
                NULL AS att_ci_marked_time,
                NULL AS att_co_marked_time

                FROM sap_attendance_report
                WHERE attendance_date >= :fromDate AND attendance_date <= :toDate",
                [
                    'fromDate' => $from_date,
                    'toDate' => $to_date,
                ]
            );

                            
        $attendance = array_filter($attendance, function ($item) use ($login_user) {
            return ltrim($item->emp_code, '0') == ($login_user[0]['employee_code']);

        });

        $attendance = json_decode(json_encode($attendance), TRUE);

           
             
            // $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

            // $t_employee_leaves = Employees::where('employee_isactive',1)->join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where(['att_employee_id'=>$login_user[0]['employee_id'],'att_for_month'=>$month,'att_for_year'=>$year])->where(function($query) use($manage_leaves){
            //     $query->whereIn('att_check_overall_status' ,$manage_leaves)
            //     ->orWhere('att_check_overall_status','RPL');
            // })->count();
           
            $login_user[0]['employee_offdays'] = (!empty($login_user[0]['employee_offdays'])) ? explode(",",$login_user[0]['employee_offdays']):[];
            $off_days =  $login_user[0]['employee_offdays'];   
        
            // $data = compact('title','attendance','month_days','t_employee_leaves','month','year','off_days','manage_leaves');

            // $data = compact('attendance','month_days','month','year','off_days');
            $data = compact('attendance','from_date','to_date','month_days','off_days');

            return $this->api_response_json(TRUE,"My Attendance",$data,200);

		
        // }catch(\Exception $e){
            
        //     $this->create_error_log_from_api('View My Attendance API', $e, $request->header('Bearer-Token'));

        //     return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        // }finally{

        //     ActivityTracker::track_activity_from_api('View My Attendance API',  $request->header('Bearer-Token'));

        // }
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

            ActivityTracker::track_activity_from_api('View My Attendance API',  $request->header('Bearer-Token'));

        }
    }
}
