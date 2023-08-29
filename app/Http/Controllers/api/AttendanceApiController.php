<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{Attendance,Employees,LeaveManagement};

use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait};
use App\Helpers\{ActivityTracker, CustomHelper};

class AttendanceApiController extends Controller
{    
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait;

    const TITLE_GLOBAL2 = "my-attendance";

    protected function personal_attendace(Request $request){
        try{
            
            $title = Self::TITLE_GLOBAL2;
      
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            
            if($request->get('m') != '' && is_numeric($request->get('m')) && $request->get('m') >= 01 && $request->get('m') <= 12 && $request->get('y') != '' && is_numeric($request->get('y')) && $request->get('y') >= 1900 && $request->get('y') <= 3000){

                $month_days = cal_days_in_month(CAL_MONTH_GREGORIAN_LONG, $request->get('m'),$request->get('y'));
                $month = $request->get('m');
                $year =  $request->get('y');

            }else{
                $month_days = date('t');
                $month = date('m');
                $year =  date('Y');
              
            }

            $attendance = Attendance::where(['att_employee_id'=>$login_user[0]['employee_id'],'att_for_month'=>$month,'att_for_year'=>$year])->get()->toArray();
             
            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

            $t_employee_leaves = Employees::where('employee_isactive',1)->join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where(['att_employee_id'=>$login_user[0]['employee_id'],'att_for_month'=>$month,'att_for_year'=>$year])->where(function($query) use($manage_leaves){
                $query->whereIn('att_check_overall_status' ,$manage_leaves)
                ->orWhere('att_check_overall_status','RPL');
            })->count();
           
            $login_user[0]['employee_offday1'] = (!empty($login_user[0]['employee_offday1'])) ? explode(",",$login_user[0]['employee_offday1']):[];
            $off_days =  $login_user[0]['employee_offday1'];   
        
            $data = compact('title','attendance','month_days','t_employee_leaves','month','year','off_days','manage_leaves');

            return $this->api_response_json(TRUE,"My Attendance",$data,200);

		
        }catch(\Exception $e){
            
            $this->create_error_log_from_api('View My Attendance API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('View My Attendance API',  $request->header('Bearer-Token'));

        }
    }
}
