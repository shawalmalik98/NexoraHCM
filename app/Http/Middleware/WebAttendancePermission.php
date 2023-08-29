<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\HrmSetting;
use App\Models\Employees;
use App\Models\WFH;
use App\Models\RequestApproval;
use App\Models\Attendance;

class WebAttendancePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $current_url = url()->full();
        
        $url = explode('/', $current_url);

        $last_element = end($url);

        $setting = HrmSetting::orderBy('setting_id','DESC')->get(['web_attendance'])->toArray();

        $employee_data = Employees::where('employee_code', $request->input('employee_id'))->get(['employee_id'])->toArray();

        $work_from_home_employee = Employees::where('employee_jobtype', '=', 'Home')->where('employee_id', $employee_data[0]['employee_id'])->get(['employee_id'])->toArray();

        $date = date('Y-m-d');
       
        if($last_element == "checkoutattendance"){
            $get_attendance = Attendance::where('att_employee_id',$employee_data[0]['employee_id'])->where('att_for_date',date('d'))->where('att_for_month',date('m'))->where('att_for_year',date('Y'))->get(['attendance_id'])->toArray();

            if(!empty($get_attendance)){
                $date = date('Y-m-d');
            }else{
                $date = date('Y-m-d', strtotime("-1 days"));
            }

        }
       
        $get_wfh_request = WFH::where('wfh_employee_id','=', $employee_data[0]['employee_id'])
            ->where('wfh_request_date','=', $date)
            ->get(['wfh_request_id','wfh_request_approved_by_hr'])->toArray();

        if(!empty($get_wfh_request) && $get_wfh_request[0]['wfh_request_approved_by_hr'] != 2){
            
            $get_wfh_request = RequestApproval::where('request_module_id',3)
            ->where('request_id',$get_wfh_request[0]['wfh_request_id'])
            ->where('request_approver_status',2)
            ->limit(1)
            ->get()->toArray();
        }
        if(empty($work_from_home_employee) && empty($get_wfh_request)){
            if($setting[0]['web_attendance'] == 0){
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'Web attendance isn\'t allowed for onsite employees right now. For further queries kindly contact HR.',
                ]);
            } 
            // if($setting[0]['mobile_attendance'] == 0){
            //     return response()->json([
            //         'status' => 'TRUE',
            //         'msg' => 'Mobile attendance is not accessible right now. Kindly contact hr.',
            //     ]);
            // } 
              
        }
        return $next($request);
    }
}
