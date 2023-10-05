<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{ShiftManagement, Employees, Department, ShiftRequest, ShiftRequestLog, OfficeLocation, HrmSetting, Attendance};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, MailTrait};
use App\Helpers\ActivityTracker;
use Barryvdh\DomPDF\Facade\Pdf;

class ShiftController extends Controller
{
    use LoginEmployee, LogGenerateTrait, NotificationTrait, MailTrait;
    const MAIN_TITLE = 'shift-management';
    const SUB_TITLE = 'add-shift';
    const MY_TITLE = 'my-shift-time';
    const TITLE_GLOBAL = 'shift-time-request';

    protected function index(Request $request){

        try{
            
            $title = Self::MAIN_TITLE;
            $login_user = $this->get_login_user_detail();
            $employee_name = '';

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if(empty($request->input('date'))){
                $date = date('Y-m-d');
            }else{
                $date = date('Y-m-d',strtotime($request->input('date'))); 
            }

            $status = 1;
            if(!empty($request->input('is_active'))){

                if($request->input('is_active') == 1){
                    $status = 1;
                }else{
                    $status = 0;
                }
            }

            $where_clause = '1=1';
            if(!empty($request->employee_location)){
                $where_clause   .= ' AND employee_location = "'.$request->employee_location.'"'; 
            }

            if($login_user[0]['employee_department'] != 1 && $login_user[0]['employee_department'] != 2){
                $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'], $status);

                $employees = Employees::whereIn('employee_id', $total_team_member)->whereRaw($where_clause)->get(['employee_id','employee_code','employee_name'])->toArray();
                
            }else{
                $total_team_member = Employees::where('employee_isactive',$status)->whereRaw($where_clause)->pluck('employee_id')->toArray();
                $employees = Employees::where('employee_isactive',$status)->whereRaw($where_clause)->get(['employee_id','employee_code','employee_name'])->toArray();
            }


            if(empty($request->input('emp_name'))){
               
                $fetch_shift_timing = [];
                $employee_arrays = array_chunk($total_team_member , 2000);
                
                foreach($employee_arrays as $employee){
                    
                    $fetch_shift_timings = Employees::
                    join('tbl_shift_management','tbl_employees.employee_id','tbl_shift_management.employee_id')
                    ->leftJoin('tbl_employees as emp_shift', 'emp_shift.employee_id', '=', 'tbl_shift_management.shift_created_by')
                    ->whereRaw($where_clause)
                    ->whereIn('tbl_employees.employee_id', $employee)
                    ->whereIn('shift_start_from_date',function($query) use($date){
                        $query->fromRaw('tbl_shift_management as sub_table')
                        ->selectRaw('MAX(CONVERT(DATE, shift_start_from_date, 120))')
                        ->whereRaw("CONVERT(DATE, shift_start_from_date, 120) <= '".$date."'")
                        ->whereRaw('sub_table.employee_id = tbl_shift_management.employee_id');
                    })->get(['tbl_shift_management.employee_id', 
                        'shift_start_from_date',
                        'shift_start_time',
                        'shift_end_time',
                        'shift_created_by',
                        'shift_id',
                        'tbl_employees.employee_name',
                        'tbl_employees.employee_code',
                        'emp_shift.employee_code as employee_shift_code',
                        'emp_shift.employee_name as employee_shift_name'
                    ])->toArray();
                    
                    $fetch_shift_timing = array_merge($fetch_shift_timing , $fetch_shift_timings);    
                }    

            }else{
               
                $fetch_shift_timing = Employees::
                    join('tbl_shift_management','tbl_employees.employee_id','tbl_shift_management.employee_id')
                    ->where('tbl_employees.employee_id',$request->input('emp_name'))
                    ->whereRaw($where_clause)
                    ->get(['tbl_shift_management.employee_id', 'shift_start_from_date', 'shift_start_time', 'shift_end_time', 'shift_created_by','shift_id','employee_name','employee_code'])->toArray();

                $employee_name = Employees::where('employee_id',$request->input('emp_name'))->whereRaw($where_clause)->get(['employee_name','employee_code'])->toArray();
            }
            
            $send_data = compact('title','login_user','fetch_shift_timing','date','employees','employee_name','employee_locations');

            return view('shift-management.shift-management', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View All Shift Time', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
          
            ActivityTracker::track_activity('View All Shift Time');
           
        }
    }

    protected function download_shift_management(Request $request){

        try{
            
            $security_variable = FALSE;

            $validator = Validator::make($request->all(), [
                'confirmation_code' => ['required']
            ]);
            
            $login_user = $this->get_login_user_detail();

            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];

            if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
                $security_variable = TRUE;
            }
            else{
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/shift-management';</script>";
            }

            $employee_name = '';

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if(empty($request->input('date'))){
                $date = date('Y-m-d');
            }else{
                $date = date('Y-m-d',strtotime($request->input('date'))); 
            }

            $status = 1;
            if(!empty($request->input('is_active'))){

                if($request->input('is_active') == 1){
                    $status = 1;
                }else{
                    $status = 0;
                }
            }

            $where_clause = '1=1';
            if(!empty($request->employee_location)){
                $where_clause   .= ' AND employee_location = "'.$request->employee_location.'"'; 
            }

            if($login_user[0]['employee_department'] != 1 && $login_user[0]['employee_department'] != 2){
                $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'], $status);
                $employees = Employees::whereIn('employee_id',$total_team_member)->whereRaw($where_clause)->get(['employee_id','employee_code','employee_name'])->toArray();
            }else{
                $total_team_member = Employees::where('employee_isactive',$status)->whereRaw($where_clause)->pluck('employee_id')->toArray();
                $employees = Employees::where('employee_isactive',$status)->whereRaw($where_clause)->get(['employee_id','employee_code','employee_name'])->toArray();
            }
            if(empty($request->input('emp_name'))){

                $fetch_shift_timing = Employees::
                    join('tbl_shift_management','tbl_employees.employee_id','tbl_shift_management.employee_id')
                    ->whereRaw($where_clause)
                    ->whereIn('tbl_employees.employee_id',$total_team_member)
                    ->whereIn('shift_start_from_date',function($query) use($date){
                        $query->fromRaw('tbl_shift_management as sub_table')
                        ->selectRaw('MAX(CONVERT(DATE, shift_start_from_date, 120))')
                        ->whereRaw("CONVERT(DATE, shift_start_from_date, 120) <= '".$date."'")
                        ->whereRaw('sub_table.employee_id = tbl_shift_management.employee_id');
                    })->get(['tbl_shift_management.employee_id', 'shift_start_from_date', 'shift_start_time', 'shift_end_time', 'shift_created_by','shift_id','employee_name','employee_code'])->toArray();

            }else{

                $fetch_shift_timing = Employees::
                    join('tbl_shift_management','tbl_employees.employee_id','tbl_shift_management.employee_id')
                    ->where('tbl_employees.employee_id',$request->input('emp_name'))
                    ->whereRaw($where_clause)
                    ->get(['tbl_shift_management.employee_id', 'shift_start_from_date', 'shift_start_time', 'shift_end_time', 'shift_created_by','shift_id','employee_name','employee_code'])->toArray();

                $employee_name = Employees::where('employee_id',$request->input('emp_name'))->whereRaw($where_clause)->get(['employee_name','employee_code'])->toArray();
            }
            
            $send_data = compact('login_user','fetch_shift_timing','date','employees','employee_name','employee_locations');
            if($security_variable == TRUE){
               
                $pdf = Pdf::loadView('shift-management.download-shift-management-report',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Shift Management details'.'.pdf');
            }
        }catch(\Exception $e){
            $this->create_error_log('Download Shift Management Report pdf', $e);
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('Download Shift Management Report pdf');
           
        }
    }

    protected function add_shift(Request $request){

        try{

            $title = Self::SUB_TITLE;
            $login_user = $this->get_login_user_detail();
            $all_department = Department::get(['department_id','department_name'])->toArray();

            if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2){
                $employees = Employees::where('employee_isactive',1)->get(['employee_id','employee_name','employee_code'])->toArray();
            }else{
                $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'], 1);
                $employees = Employees::where('employee_isactive',1)->whereIn('employee_id', $total_team_member)->get(['employee_id','employee_name','employee_code'])->toArray();
            }
            $send_data = compact('title','login_user','all_department','employees');
            return view('shift-management.add-shift', $send_data);
        }catch(\Exception $e){
            $this->create_error_log('Add Form Shift Time', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('Add Form Shift Time');
    
        }
    }

    protected function get_employee_shift(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'department' => 'required_without:employee_id',
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }
            if(!empty($request->input('employee_id'))){

                $fetch_employee = Employees::
                leftjoin('tbl_shift_management','tbl_employees.employee_id','tbl_shift_management.employee_id')
                ->where('employee_isactive',1)
                ->whereIn('tbl_employees.employee_id',$request->input('employee_id'))
                ->where(function($query_main){
                    $query_main->whereIn('shift_start_from_date',function($query){
                        $query->fromRaw('tbl_shift_management as sub_table')
                        ->selectRaw('MAX(CONVERT(DATE, shift_start_from_date, 120))')
                        ->whereRaw("CONVERT(DATE, shift_start_from_date, 120) <= '".date('Y-m-d')."'")
                        ->whereRaw('sub_table.employee_id = tbl_shift_management.employee_id');
                    });
                    $query_main->orWhere('shift_start_from_date',null);
                })
                ->get(['tbl_shift_management.employee_id', 'shift_start_from_date', 'shift_start_time', 'shift_end_time','tbl_employees.employee_id','employee_name','employee_code'])->toArray();

                $date_max = date('Y-m-d'); 

                return response()->json([
                    'status' => 'TRUE',
                    'message' => 'Get employee shift',
                    'data' => $fetch_employee,
                    'date_max' => date('Y-m-d')
                ]);

            }else{

                $fetch_employee = Employees::
                leftjoin('tbl_shift_management','tbl_employees.employee_id','tbl_shift_management.employee_id')
                ->where('employee_isactive',1)
                ->whereIn('employee_department',$request->input('department'))
                ->where(function($query_main){
                    $query_main->whereIn('shift_start_from_date',function($query){
                        $query->fromRaw('tbl_shift_management as sub_table')
                        ->selectRaw('MAX(CONVERT(DATE, shift_start_from_date, 120))')
                        ->whereRaw("CONVERT(DATE, shift_start_from_date, 120) <= '".date('Y-m-d')."'")
                        ->whereRaw('sub_table.employee_id = tbl_shift_management.employee_id');
                    });
                })
                ->get(['tbl_shift_management.employee_id', 'shift_start_from_date', 'shift_start_time', 'shift_end_time','tbl_employees.employee_id','employee_name','employee_code'])->toArray();

                return response()->json([
                    'status' => 'TRUE',
                    'message' => 'Get employee shift',
                    'data' => $fetch_employee,
                    'date_max' => date('Y-m-d')
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Get Employee Shift', $e);
        }finally{
            ActivityTracker::track_activity('Get Employee Shift');
        }
        
    }

    protected function add_employee_shift(Request $request){
        
        try{
            $validator = Validator::make($request->all(), [

                'employee_id' => ['required','array'],
                'employee_id.*' =>  ['required'],

                'effected_date' => ['required','array'],
                'effected_date.*' =>  ['required','date','after:yesterday'],

                'shift_start_time' => ['required','array'],
                'shift_start_time.*' =>  ['required'],

                'shift_end_time' => ['required','array'],
                'shift_end_time.*' =>  ['required'],

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }
        
            $login_user = $this->get_login_user_detail();

            if($login_user[0]['employee_department'] != 1 && $login_user[0]['employee_department'] != 2){
                $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'], 1);
                $employee_shift_updated_array = $request->input('employee_id');
                $result_diff = array_diff($employee_shift_updated_array,$total_team_member);
                if(!empty($result_diff)){
                    $employee_name = Employees::where('employee_id',$result_diff[0])->get(['employee_name'])->toArray();
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>  $employee_name[0]['employee_name']." has not in your reporting.",
                    ]);
                }   
            }

            $i = 0;
            foreach($request->input('effected_date') as $effected_date){

                if($effected_date == date('Y-m-d')){
                    $get_todays_attendance = Attendance::where('att_for_date', date('d'))->where('att_for_month', date('m'))->where('att_for_year', date('Y'))->where('att_employee_id', $request->input('employee_id')[$i])->get(['attendance_id','att_employee_id'])->toArray();

                    if(empty($get_todays_attendance)){
                        $effected_date = $effected_date;
                    }
                    else{
                        $effected_date = date('Y-m-d', strtotime(date('Y-m-d') . '+1 days'));
                    }
                }

                $delete_record_of_same_day = ShiftManagement::where('employee_id',$request->input('employee_id')[$i])->where('shift_start_from_date',$effected_date)->delete();

                $add_shift = new ShiftManagement();
                $add_shift->employee_id = $request->input('employee_id')[$i];
                $add_shift->shift_start_from_date = $effected_date;
                $add_shift->shift_start_time = $request->input('shift_start_time')[$i].':00';
                $add_shift->shift_end_time = $request->input('shift_end_time')[$i].':00';
                $add_shift->shift_created_by = $login_user[0]['employee_id'];
                $add_shift->save();
                
                $employee_name = Employees::where('employee_id',$request->input('employee_id')[$i])->get(['employee_name','employee_id'])->toArray();
                $notification_message = "Dear employee, your shift timing has been updated which is effecting from ".date('jS M, Y',strtotime($effected_date)).'. Please click here to view.';
                $this->send_notification('Shift Time Update', $notification_message ,'/my-shift-time?m='.date('m',strtotime($effected_date)).'&y='.date('Y',strtotime($effected_date)),$request->input('employee_id')[$i],NULL,NULL,'No');
                
                if($login_user[0]['employee_department'] != 1 && $login_user[0]['employee_department'] != 2){
    
                    $notification_message = $employee_name[0]['employee_name']."'s manager updated their shift timings which is effecting from ".date('jS M, Y',strtotime($effected_date));
                    $this->send_notification('Shift Time Update', $notification_message ,'shift-management?emp_name='.$request->input('employee_id')[$i].'&shift-id='.$add_shift->shift_id,NULL,NULL,NULL,'Yes');

                }
                $i++;
            }
            return response()->json([
                'status' => 'TRUE',
                'msg' => 'Employee(s) shift timings updated successfully'
            ]);
        }catch(\Exception $e){
            $this->create_error_log('Add employee shift', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            
            }
        }finally{
            ActivityTracker::track_activity('Add employee shift');
        }
    }

    protected function my_shift_time(Request $request){
        try{
            $title = Self::MY_TITLE;
            $login_user = $this->get_login_user_detail();
            
            if(empty($request->input('m')) && empty($request->input('y'))){
                $month = date('m');
                $year = date('Y');
            }else{
                $month = $request->input('m');
                $year = $request->input('y');
            }
            
            $tota_days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
            $employee_shift = ShiftManagement::where('employee_id',$login_user[0]['employee_id'])->orderBy('shift_start_from_date','DESC')->get(['shift_start_from_date','shift_start_time','shift_end_time'])->toArray();
            $shift_calender = [];
            for($i=1;$i<=$tota_days;$i++){
                $date = $year.'-'.$month.'-'.$i;
                foreach($employee_shift as $shift){
                    if(strtotime($shift['shift_start_from_date']) <= strtotime($date)){
                        $shift_record = ['shift_start_from_date' => $date, 'shift_start_time' => $shift['shift_start_time'] , 'shift_end_time' => $shift['shift_end_time'] ];
                        array_push($shift_calender,$shift_record);
                        break;
                    }
                }
            }

            $shift_requests = ShiftRequest::leftJoin('tbl_employees','tbl_employees.employee_id','=','request_approved_by_hr')->where('request_employee_id',$login_user[0]['employee_id'])->orderBy('shift_request_id','DESC')->get()->toArray();
            $send_data = compact('title','login_user','month','year','shift_calender','shift_requests');
            return view('shift-management.my-shift-time', $send_data);
        }catch(\Exception $e){
            $this->create_error_log('My Shift Time', $e);
            $send_data = compact('title','login_user');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('My Shift Time');
     
        }
    }

    protected function add_shift_time_request(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'shift_start' => 'required',
                'shift_end' => 'required',
                'effect_date' => 'required|date_format:Y-m-d|after:yesterday',
                'reason' => 'required'
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }   

            $login_user = $this->get_login_user_detail();

            if($request->input('effect_date') == date('Y-m-d')){
                $get_todays_attendance = Attendance::where('att_for_date', date('d'))->where('att_for_month', date('m'))->where('att_for_year', date('Y'))->where('att_employee_id', $login_user[0]['employee_id'])->get(['attendance_id'])->toArray();
                if(!empty($get_todays_attendance)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t apply shift time request for today. You\'ve already marked your today\'s attendance.',
                    ]);
                }
            }
            
            $check_resquest_exist = ShiftRequest::where('request_employee_id',$login_user[0]['employee_id'])->where('request_shift_effect_date',$request->input('effect_date'))->get()->toArray();

            if(empty($check_resquest_exist)){
                $add_shift_request = new ShiftRequest();
                $add_shift_request->request_employee_id = $login_user[0]['employee_id'];
                $add_shift_request->request_shift_start = $request->input('shift_start');
                $add_shift_request->request_shift_end = $request->input('shift_end');
                $add_shift_request->request_shift_effect_date = $request->input('effect_date');
                $add_shift_request->request_status = 1;
                $add_shift_request->request_submit_time = date('Y-m-d H:i:s');
                $add_shift_request->request_shift_reason = $request->input('reason'); 
                $add_shift_request->save();

                $add_request_log = new ShiftRequestLog();
                $add_request_log->shift_request_id = $add_shift_request->shift_request_id;
                $add_request_log->shift_employee_id = $login_user[0]['employee_id'];
                $add_request_log->shift_log_type = 1;
                $add_request_log->shift_log_effect_date =  $request->input('effect_date');
                $add_request_log->shift_log_start = $request->input('shift_start');
                $add_request_log->shift_log_end = $request->input('shift_end');
                $add_request_log->shift_log_create_at = date('Y-m-d H:i:s');
                $add_request_log->save();
                    
                if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                    $to_mngr1  = $login_user[0]['employee_senior_manager'];
                }
                if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                    $to_mngr2 = $login_user[0]['employee_junior_manager'];
                }

                $this->send_notification('Shift Time Request ', '' . $login_user[0]['employee_name']. ' applied for a shift time changing request. Please click here to view. ','shift-time-request?request-id='.$add_shift_request->shift_request_id,NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                $this->send_module_email(2,'Shift Time Change Request','<b>'.$login_user[0]['employee_name']. '</b> applied for shift time change request.',''.url('/').'/shift-time-request?request-id='.$add_shift_request->shift_request_id);

                $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Shift Time Change Request','You\'ve successfully applied for a shift time changing request and has been forwarded to your reporting authorities.');
                
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Shift request has been submitted. Please wait for response",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'You have already submitted a request for this date '.$request->input('effect_date').'.',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Employee shift request', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            
            }
        }finally{
            ActivityTracker::track_activity('Employee shift request');
        }
    } 
    
    protected function update_shift_time_request(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'request_id' => 'required',
                'shift_start' => 'required',
                'shift_end' => 'required',
                'effect_date' => 'required|date_format:Y-m-d|after:yesterday',
                'reason' => 'required'
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            } 

            $login_user = $this->get_login_user_detail();

            if($request->input('effect_date') == date('Y-m-d')){
                $get_todays_attendance = Attendance::where('att_for_date', date('d'))->where('att_for_month', date('m'))->where('att_for_year', date('Y'))->where('att_employee_id', $login_user[0]['employee_id'])->get(['attendance_id'])->toArray();
                if(!empty($get_todays_attendance)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t apply shift time request for today. You\'ve already marked your today\'s attendance.',
                    ]);
                }
            }
                
            $get_request = ShiftRequest::where('request_employee_id',$login_user[0]['employee_id'])->where('shift_request_id',$request->input('request_id'))->get()->toArray();

            if(!empty($get_request)){

                if($get_request[0]['request_status'] == 1){
                    
                    $update_shift = ShiftRequest::find($request->input('request_id'));
                    $update_shift->request_shift_start = $request->input('shift_start');
                    $update_shift->request_shift_end = $request->input('shift_end');
                    $update_shift->request_shift_effect_date = $request->input('effect_date');
                    $update_shift->request_shift_reason = $request->input('reason'); 
                    $update_shift->save();

                    $delete_log = ShiftRequestLog::where('shift_request_id',$request->input('request_id'))->delete();

                    $add_request_log = new ShiftRequestLog();
                    $add_request_log->shift_request_id = $request->input('request_id');
                    $add_request_log->shift_employee_id = $login_user[0]['employee_id'];
                    $add_request_log->shift_log_type = 1;
                    $add_request_log->shift_log_effect_date =  $request->input('effect_date');
                    $add_request_log->shift_log_start = $request->input('shift_start');
                    $add_request_log->shift_log_end = $request->input('shift_end');
                    $add_request_log->shift_log_create_at = date('Y-m-d H:i:s');
                    $add_request_log->save();

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Shift request has been updated. Please wait for response',
                    ]);

                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t update this request now because your reporting authority has changed its status.'
                    ]);
                }

                

            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Shift request id is invalid',
                ]);
            }

        }catch(\Exception $e){
            $this->create_error_log('Update shift request', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            
            }
        }finally{
            ActivityTracker::track_activity('Update shift request');
        }


    }

    protected function delete_shift_time_request(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'request_id' => 'required',
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();

            $get_request = ShiftRequest::where('request_employee_id',$login_user[0]['employee_id'])->where('shift_request_id',$request->input('request_id'))->get()->toArray();

            if(!empty($get_request)){

                if($get_request[0]['request_status'] == 1){

                    $delete_log = ShiftRequestLog::where('shift_request_id',$request->input('request_id'))->delete();
                    $delete_request = ShiftRequest::where('request_employee_id',$login_user[0]['employee_id'])->where('shift_request_id',$request->input('request_id'))->delete();

                    if($delete_request == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Shift request has been deleted',
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Something went wrong. Please try again.',
                        ]);
                    }
                    
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t delete this request now because your reporting authority has changed its status.',
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Shift request id is invalid',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Delete shift request', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            
            }
        }finally{
            ActivityTracker::track_activity('Delete shift request');
        }
    }

    protected function shift_time_request(Request $request){
        try{
            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $all_shift_requests = ShiftRequest::leftJoin('tbl_employees','tbl_employees.employee_id','=','request_approved_by_hr')
            ->orderBy('shift_request_id','DESC')->get()->toArray();

            $send_data = compact('title' , 'login_user','all_shift_requests');

            return view('shift-management.all-shift-request', $send_data);
        }
        catch(\Exception $e){

            $this->create_error_log('View shift request', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View shift request');

        }
    }

    protected function disapproved_shift_time_request(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'disapproved_id' => 'required',
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();

            $get_request = ShiftRequest::where('shift_request_id',$request->input('disapproved_id'))->get()->toArray();

            if(!empty($get_request)){

                if($get_request[0]['request_status'] == 1){
                    
                    $disapproved_shift = ShiftRequest::find($request->input('disapproved_id'));
                    $disapproved_shift->request_status = 3;
                    $disapproved_shift->request_approved_by_hr = $login_user[0]['employee_id'];
                    $disapproved_shift->request_approved_time = date('Y-m-d H:i:s');
                    $process = $disapproved_shift->save();

                    if($process == 1){

                        $notification_message = "Your reporting authority has been disapproved shift request";
                        $this->send_notification('Shift Request Disapproval', $notification_message ,'/my-shift-time?request-id='.$request->input('disapproved_id'),$disapproved_shift->request_employee_id,NULL,NULL,'No');

                        return response()->json([
                            'status' => 'TRUE',
                            'msg'   =>"Request disapproved successfully ",
                        ]);

                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Something went wrong. Please try again.',
                        ]);
                    }
                    
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Already responsed this request',
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Shift request id is invalid',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Disapproved shift request', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            
            }
        }finally{
            ActivityTracker::track_activity('Disapproved shift request');
        }
    }

    protected function approved_shift_time_request(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'approved_id' => 'required',
                'shift_start' => 'required',
                'shift_end' => 'required',
                'effect_date' => 'required|date_format:Y-m-d|after:yesterday',
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();

            $get_request = ShiftRequest::where('shift_request_id',$request->input('approved_id'))->get()->toArray();

            if(!empty($get_request)){

                if($get_request[0]['request_status'] == 1){

                    if($request->input('effect_date') == date('Y-m-d')){
                        $get_todays_attendance = Attendance::where('att_for_date', date('d'))->where('att_for_month', date('m'))->where('att_for_year', date('Y'))->where('att_employee_id', $get_request[0]['request_employee_id'])->get(['attendance_id'])->toArray();
                        if(!empty($get_todays_attendance)){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'You can\'t approve this request. Attendance already for for today\'s shift by this employee.',
                            ]);
                        }
                    }

                    $log_update = false;

                    $request_update_log = ShiftRequest::where('shift_request_id',$request->input('approved_id'))->where('request_shift_start',$request->input('shift_start'))->where('request_shift_end',$request->input('shift_end'))->where('request_shift_effect_date',$request->input('effect_date'))->get()->toArray();

                    if(empty($request_update_log)){
                        $log_update = true;
                    }
                    
                    $approved_shift = ShiftRequest::find($request->input('approved_id'));
                    $approved_shift->request_shift_start = $request->input('shift_start');
                    $approved_shift->request_shift_end = $request->input('shift_end');
                    $approved_shift->request_shift_effect_date = $request->input('effect_date');
                    $approved_shift->request_status = 2;
                    $approved_shift->request_approved_by_hr = $login_user[0]['employee_id'];
                    $approved_shift->request_approved_time = date('Y-m-d H:i:s');
                    $process = $approved_shift->save();

                    if($process == 1){

                        $delete_record_of_same_day = ShiftManagement::where('employee_id',$approved_shift->request_employee_id)->where('shift_start_from_date',$request->input('effect_date'))->delete();

                        $add_shift = new ShiftManagement();
                        $add_shift->employee_id = $approved_shift->request_employee_id;
                        $add_shift->shift_start_from_date = $request->input('effect_date');
                        $add_shift->shift_start_time = $request->input('shift_start');
                        $add_shift->shift_end_time = $request->input('shift_end');
                        $add_shift->shift_created_by = $login_user[0]['employee_id'];
                        $add_shift->save();

                        if($log_update){
                            $add_request_log = new ShiftRequestLog();
                            $add_request_log->shift_request_id = $request->input('approved_id');
                            $add_request_log->shift_employee_id = $login_user[0]['employee_id'];
                            $add_request_log->shift_log_type = 2;
                            $add_request_log->shift_log_effect_date =  $request->input('effect_date');
                            $add_request_log->shift_log_start = $request->input('shift_start');
                            $add_request_log->shift_log_end = $request->input('shift_end');
                            $add_request_log->shift_log_create_at = date('Y-m-d H:i:s');
                            $add_request_log->save();
                        }
                        

                        $notification_message = "Your reporting authority has approved your shift time changing request. Please click here to view.";
                        $this->send_notification('Shift Request Approval', $notification_message ,'/my-shift-time?request-id='.$request->input('approved_id'),$approved_shift->request_employee_id,NULL,NULL,'No');

                        return response()->json([
                            'status' => 'TRUE',
                            'msg'   =>"Request approved successfully.",
                        ]);

                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Something went wrong. Please try again.',
                        ]);
                    }
                    
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Already responded to this request.',
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Shift request id is invalid',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Approved shift request', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            
            }
        }finally{
            ActivityTracker::track_activity('Approved shift request');
        }
    }

    protected function log_shift_time(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'request_id' => 'required',
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $shift_request_log = ShiftRequestLog::
            selectRaw('employee_name,employee_code,shift_log_type,shift_log_start,shift_log_end,date_format(shift_log_create_at,"%d-%M-%Y %H:%i:%s") as shift_log_create_at')
            ->join('tbl_employees','tbl_employees.employee_id','=','shift_employee_id')
            ->where('shift_request_id',$request->input('request_id'))->get()->toArray();

            if(!empty($shift_request_log)){
                return response()->json([
                    'status' => 'TRUE',
                    'data' => $shift_request_log
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' =>  "Something went wrong. Please try again.",
                ]);
            }
            
        }catch(\Exception $e){
            $this->create_error_log('Log shift request', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            
            }
        }finally{
            ActivityTracker::track_activity('Log shift request');
        }
    }

}