<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{AttendanceRequest, Attendance, Employees, RequestApproval, AttRequestTime, Payroll, OfficeLocation, LeaveManagement};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee};
use App\Helpers\{CustomHelper, ActivityTracker};
use Illuminate\Support\Facades\Validator;


class AttendanceRequestController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait , AttendanceEmployee;
  

    const TITLE_GLOBAL = "attendance-request";
    const TITLE_GLOBAL2 = "my-attendance-request";
    const TITLE_SPECIFIC = 'my-attendance';
    

    protected function index(Request $request){
        
        try{
           

            $title = Self::TITLE_GLOBAL;
      
            $login_user = $this->get_login_user_detail();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();
            
            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $all_requests = array();
            $responsed_request = array();
            // $dates = array();
            $limit_query = '500';
            if(!empty($request->attendance)){
                $limit_query = '*';
            }
           
            if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1){
               
                $where_clause = "1=1 AND employee_isactive = '".$status."'";
                $where_clause_to = '1=1';
                if(!empty($request->employee_location) || !empty($request->emp_status) || !empty($request->emp_name) || !empty($request->request_for_date)  || !empty($request->request_submited_date) || !empty($request->req_status)){
                    
                    if(!empty($request->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request->employee_location.'"'; 
                    }
                    if(!empty($request->emp_name)){
                        $where_clause .= ' AND request_employee_id = "'.$request->emp_name.'"';
                    }
                    if(!empty($request->request_for_date)){
                        $where_clause .= ' AND request_attendance_date = "'.$request->request_for_date.'"';
                    }
                    if(!empty($request->request_submited_date)){
                        $submition_date = date("d F, Y", strtotime($request->request_submited_date));
                        $where_clause .= ' AND request_submition_date LIKE "%'.$submition_date.'%"';
                    }
                    if(!empty($request->req_status)){
                        $where_clause .= ' AND request_approved_by_hr = '.$request->req_status.'';
                    }
                }

                $employees_data = Employees::where('employee_isactive', $status)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

                $all_requests = AttendanceRequest::select(['attendance_request_id','request_employee_id','request_attendance_date','request_attendance_status','request_attendance_message','request_approved_by_hr','request_approved_date_hr','request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_senior_manager','employee_junior_manager','attendance_checkin_time','attendance_checkout_time'])
                ->join('tbl_employees','tbl_attendance_requests.request_employee_id','=','tbl_employees.employee_id')
                ->leftJoin('tbl_attendance_request_time','tbl_attendance_requests.attendance_request_id','=','tbl_attendance_request_time.attendance_req_id')
                ->where(function($query){
                    $query->whereIn('attendance_request_time_id',function ($query) {
                        $query->selectRaw('max(attendance_request_time_id)')
                        ->from('tbl_attendance_request_time')
                        ->groupBy('attendance_req_id');
                    });
                    $query->orWhere('attendance_req_id',null);
                })
                ->whereRaw($where_clause)
                ->orderBy('attendance_request_id','desc')
                ->limit($limit_query)
                ->get()->toArray();


                $responsed_request = RequestApproval::where('request_module_id',1)->pluck('request_id')->toArray();
            }
           
            elseif($login_user[0]['employee_department'] !== 2 && $login_user[0]['employee_department'] !== 1 &&  $login_user[0]['employee_is_manager'] == 1){
               
                $login_employee_id = $login_user[0]['employee_id'];
            
                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id, $status);
              
                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                $where_clause_to = '1=1';
                if(!empty($request->employee_location) || !empty($request->emp_status) || !empty($request->emp_name) || !empty($request->req_status)){
                    
                    if(!empty($request->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request->employee_location.'"'; 
                    }

                    if(!empty($request->emp_name)){
                        $where_clause .= ' AND request_employee_id = "'.$request->emp_name.'"';
                    }

                    if(!empty($request->req_status)){
                        $where_clause .= ' AND request_approved_by_hr = '.$request->req_status.'';
                    }
                }

                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->whereRaw($where_clause_to)->get(['employee_id','employee_name','employee_code','employee_location'])->toArray();
                
                $all_requests = AttendanceRequest::join('tbl_employees', 'tbl_attendance_requests.request_employee_id', '=', 'tbl_employees.employee_id')
                ->leftJoin('tbl_attendance_request_time','tbl_attendance_requests.attendance_request_id','=','tbl_attendance_request_time.attendance_req_id')
                ->where(function($query){
                    $query->whereIn('attendance_request_time_id',function ($query) {
                        $query->selectRaw('max(attendance_request_time_id)')
                        ->from('tbl_attendance_request_time')
                        ->groupBy('attendance_req_id');
                    });
                    $query->orWhere('attendance_req_id',null);
                })
                ->whereIn('request_employee_id',$total_team_member)
                ->whereRaw($where_clause)
                ->orderBy('attendance_request_id','DESC')
                ->limit($limit_query)
                ->get(['attendance_request_id','request_employee_id','request_attendance_date','request_attendance_status','request_attendance_message','request_approved_by_hr','request_approved_date_hr','request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_senior_manager','employee_junior_manager','attendance_checkin_time','attendance_checkout_time'])->toArray();

                $responsed_request = RequestApproval::where('request_module_id',1)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();
            }

            if(!empty($request->from_date) && !empty($request->to_date)){
                $no_of_days = $this->count_days($request->from_date,$request->to_date);
                for($i = 0; $i<$no_of_days; $i++){
                    $dates[] =  date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request->from_date)));
                }
            }else{
                $dates[] = $request->date;
            }

            
            $send_data = compact('title','login_user','employees_data','all_requests','responsed_request','employee_locations','dates');
            
            return view('attendance.all-employees-request', $send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View All Attendance Request', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View All Attendance Requests');
        }
       
    }
    

    protected function my_requests(){
        try{
            $title = Self::TITLE_GLOBAL2;
      
            $login_user = $this->get_login_user_detail();

            $my_requests = AttendanceRequest::join('tbl_employees','tbl_attendance_requests.request_employee_id','=','tbl_employees.employee_id')
            ->leftJoin('tbl_attendance_request_time','tbl_attendance_requests.attendance_request_id','=','tbl_attendance_request_time.attendance_req_id')
            ->where(function($query){
                $query->whereIn('attendance_request_time_id',function ($query) {
                    $query->selectRaw('max(attendance_request_time_id)')
                    ->from('tbl_attendance_request_time')
                    ->groupBy('attendance_req_id');
                });
                $query->orWhere('attendance_req_id',null);
            })
            ->where('request_employee_id' , session('user_session'))
            ->orderBy('attendance_request_id','desc')
            ->get(['attendance_request_id','request_employee_id','request_attendance_date','request_attendance_status','request_attendance_message','request_approved_by_hr','request_approved_date_hr','request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_senior_manager','employee_junior_manager','attendance_checkin_time','attendance_checkout_time','attendance_request_time_id'])->toArray();

            $attendance_request_id = array_column($my_requests,'attendance_request_id');

            $responsed_request = RequestApproval::where('request_module_id',1)->whereIn('request_id',$attendance_request_id)->pluck('request_id')->toArray();

            $get_manager_response = AttRequestTime::whereIn('attendance_req_id',$attendance_request_id)->where('attendance_req_emp_id','!=',$login_user[0]['employee_id'])->pluck('attendance_req_id')->toArray();
    
            $send_data = compact('title','login_user','my_requests','responsed_request','get_manager_response');

            return view('attendance.my-request', $send_data);
            
        }catch(\Exception $e){
            $this->create_error_log('View All My Attendance Request', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Personal Attendance Requests');
          
        }
     
    }
   
    protected function requestfor_attendance(Request $request_data){
        try{
          
            $login_user = $this->get_login_user_detail();
            $process = '';
            $validator = Validator::make($request_data->all(), [
                'days_assign' => 'required',
                'from_date' => ($request_data->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:to_date' : '' ,
                'to_date' => ($request_data->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before_or_equal:now' : '' ,
                'attendance_date' => ($request_data->input('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before_or_equal:now' : '' ,
                'attendance_type' => 'required',
                'check_in_time' => ($request_data->input('attendance_type') == 'On Time Check In' ) ? 'required' : '' ,
                'check_out_time' => ($request_data->input('attendance_type') == 'On Time Check Out' ) ? 'required' : '' ,
                'attendance_message' => 'required',
            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }
            else{
                
                $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

                if(!empty($request_data->input('from_date')) && !empty($request_data->input('to_date'))){
                    $attendance_date = $request_data->input('from_date').'  '.$request_data->input('to_date');

                    if($request_data->input('from_date') < $login_user[0]['employee_doj']){
                        return response()->json([
                            'msg' => 'You can\'t apply attendance request before your joining date.',
                            'status' => 'FALSE'
                        ]);
                    }
                    
                    $no_of_days = $this->count_days($request_data->input('from_date'),$request_data->input('to_date'));
                  
                    if(!empty($no_of_days) &&  $no_of_days > 30){
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You can't apply request for more than 30 days at a time",
                        ]);
                    }
                    for($i = 0; $i<$no_of_days; $i++){
                    
                        $request_attendance_date = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request_data->input('from_date'))));

                        $checking_request = AttendanceRequest::where('request_employee_id', session('user_session'))->whereIn('request_attendance_date',[$request_attendance_date])->get(['request_attendance_date'])->toArray();
                        
                        $payroll_exists = $this->check_if_payroll_exist($request_attendance_date,session('user_session'),$custom_message = NULL);

                        if($payroll_exists['status'] == TRUE){
                            return response()->json([
                                'msg' => $payroll_exists['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_attendance_date);

                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        foreach($checking_request as $request_date){
                            
                            $attendance_request_apply_date[] =  $request_date['request_attendance_date'];
                        }
                    
                    }
    
                    if(!empty($checking_request)){

                        $attendance_date = implode(' , ',$attendance_request_apply_date);

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You've already applied requests for these dates ".$attendance_date,
                        ]);
                    }
                   
                }else{

                    if($request_data->input('attendance_date') < $login_user[0]['employee_doj']){
                        return response()->json([
                            'msg' => 'You can\'t apply attendance request before your joining date.',
                            'status' => 'FALSE'
                        ]);
                    }

                    $payroll_exists = $this->check_if_payroll_exist($request_data->input('attendance_date'),session('user_session'),$custom_message = NULL);

                    if($payroll_exists['status'] == TRUE){
                        return response()->json([
                            'msg' => $payroll_exists['msg'],
                            'status' => 'FALSE'
                        ]);
                    }

                    $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_data->input('attendance_date'));

                    if($check_attendance_sheet_availability['status'] == FALSE){
                        return response()->json([
                            'msg' => $check_attendance_sheet_availability['msg'],
                            'status' => 'FALSE'
                        ]);
                    }

                    $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_data->input('attendance_date'));

                    if($check_attendance_sheet_availability['status'] == FALSE){
                        return response()->json([
                            'msg' => $check_attendance_sheet_availability['msg'],
                            'status' => 'FALSE'
                        ]);
                    }
                  
                    $attendance_date = $request_data->input('attendance_date');
                   
                    $checking_request = AttendanceRequest::where('request_employee_id', session('user_session'))->where('request_attendance_date', $request_data->input('attendance_date'))->where('request_approved_by_hr', '!=', 3)->get('attendance_request_id')->toArray();   
                    
                    if(!empty($request_data->input('attendance_type')) && $request_data->input('attendance_type') == 'On Time Check Out'){
                            
                        $date = explode('-',$request_data->input('attendance_date'));
                    
                        $get_attendance_check_out_time = Attendance::where('att_employee_id',$request_data->session()->get('user_session'))->where('att_for_date',$date[2])->where('att_for_month',$date[1])->where('att_for_year',$date[0])->get('attendance_id')->toArray();
                        if(empty($get_attendance_check_out_time)){
                            return response()->json([
                                'status' => 'False',
                                'msg' => "You can't request for check out on ". $request_data->input('attendance_date') ." beacuse you didn't mark the attendance.",
                            ]);
                        }

                       
                        $get_attendances = Attendance::where('att_employee_id',session('user_session'))->where(function($query) use($manage_leaves){
                        $query->where('att_check_overall_status','LIKE', '%Holiday%')
                        ->orwhereIn('att_check_overall_status',$manage_leaves);
                        })
                        ->whereRaw('CONCAT(att_for_year,"-",att_for_month,"-",att_for_date) = "'.$request_data->input('attendance_date').'"')
                        ->get(['attendance_id','att_check_overall_status'])->toArray();

                        if(!empty($get_attendances)){
                            return response()->json([
                                'status' => 'False',
                                'msg' => "You can't request on this date ". $request_data->input('attendance_date')." beacuse your attendanced already marked as " .$get_attendances[0]['att_check_overall_status'],
                            ]);
                        }   
                    }
                    
                }
              
               
                if(empty($checking_request)){
                   
                    if(!empty($request_data->input('from_date')) && !empty($request_data->input('to_date'))){
                    
                        $no_of_days =  $this->count_days($request_data->input('from_date'),$request_data->input('to_date'));
                        
                        for($i = 0; $i<$no_of_days; $i++){
                            $attendance_request = new AttendanceRequest();
                            $dates =  date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request_data->input('from_date'))));
                            
                            $checking_request_dates = AttendanceRequest::where('request_employee_id', session('user_session'))->where('request_attendance_date',$dates)->get(['request_attendance_date'])->toArray();

                            if(!empty($request_data->input('attendance_type')) && $request_data->input('attendance_type') == 'On Time Check Out'){

                                $date = explode('-', $dates);
                               
                                $get_attendance_check_out_time = Attendance::where('att_employee_id',$request_data->session()->get('user_session'))->where('att_for_date',$date[2])->where('att_for_month',$date[1])->where('att_for_year',$date[0])->get('attendance_id')->toArray();

                               
                                if(empty($get_attendance_check_out_time)){
                                    return response()->json([
                                        'status' => 'False',
                                        'msg' => "You can't request for check out on this date ".$dates." beacuse you didn't mark the attendance.",
                                    ]);
                                }
    
                                $get_attendances = Attendance::where('att_employee_id',session('user_session'))->where(function($query) use($manage_leaves){
                                $query->where('att_check_overall_status','LIKE', '%Holiday%')
                                ->orwhereIn('att_check_overall_status',$manage_leaves); 
                                })
                                ->whereRaw('CONCAT(att_for_year,"-",att_for_month,"-",att_for_date) = "'.$dates.'"')
                                ->get('attendance_id')->toArray();
        
                             
                            }
                         
                            if(empty($checking_request_dates) && empty($get_attendances)){
                          
                                $attendance_request->request_employee_id = session()->get('user_session');
                                $attendance_request->request_attendance_date = $dates;
                                $attendance_request->request_attendance_status = $request_data->input('attendance_type');
                                $attendance_request->request_attendance_message = $request_data->input('attendance_message');
                                $attendance_request->request_approved_by_hr = '1';
                                $attendance_request->request_submition_date = date('h:i:s a d F, Y');

                                $process = $attendance_request->save();
                                
                                $attendance_req_time = new AttRequestTime();
                                
                                if(!empty($request_data->input('attendance_type')) && $request_data->input('attendance_type') == 'On Time Check In'){

                                    $attendance_req_time->attendance_req_id  = $attendance_request->attendance_request_id;
                                    $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                                    $attendance_req_time->attendance_checkin_time  = $request_data->input('check_in_time');
                                    $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');

                                    $attendance_req_time->save();
                                
                                }
                                if(!empty($request_data->input('attendance_type')) && $request_data->input('attendance_type') == 'On Time Check Out'){
                                    $attendance_req_time->attendance_req_id  = $attendance_request->attendance_request_id;
                                    $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                                    $attendance_req_time->attendance_checkout_time  = $request_data->input('check_out_time');
                                    $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');

                                    $attendance_req_time->save();
                                }
                                
                            }
                        }
                    
                    }
                    else{
                        
                        $attendance_request = new AttendanceRequest();
                        $attendance_request->request_employee_id = $request_data->session()->get('user_session');
                        $attendance_request->request_attendance_date = $request_data->input('attendance_date');
                        $attendance_request->request_attendance_status = $request_data->input('attendance_type');
                        $attendance_request->request_attendance_message = $request_data->input('attendance_message');
                        $attendance_request->request_approved_by_hr = '1';
                        $attendance_request->request_submition_date = date('h:i:s a d F, Y');

                        $process = $attendance_request->save();
                        
                        $attendance_req_time = new AttRequestTime();

                        if(!empty($request_data->input('attendance_type')) && $request_data->input('attendance_type') == 'On Time Check In'){
                            $attendance_req_time->attendance_req_id  = $attendance_request->attendance_request_id;
                            $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                            $attendance_req_time->attendance_checkin_time  = $request_data->input('check_in_time');
                            $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');

                            $attendance_req_time->save();
                        
                        }
                        if(!empty($request_data->input('attendance_type')) && $request_data->input('attendance_type') == 'On Time Check Out'){
                            $attendance_req_time->attendance_req_id  = $attendance_request->attendance_request_id;
                            $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                            $attendance_req_time->attendance_checkout_time  = $request_data->input('check_out_time');
                            $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');

                            $attendance_req_time->save();
                        }
                    }
                 
                    if($process == 1){

                        if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                            $to_mngr1 = $login_user[0]['employee_senior_manager'];
                        }
                        if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                            $to_mngr2 = $login_user[0]['employee_junior_manager'];
                        }
                        
                        if(!empty($request_data->input('from_date')) && !empty($request_data->input('to_date'))){
                            $this->send_notification('New Attendance Request ', '' . $login_user[0]['employee_name']. ' applied for attendance request(s) as ('. $request_data->input('attendance_type') .') from '. $request_data->input('from_date').' to '.$request_data->input('to_date').'. Please click here to view.','attendance-requests?employee_id='.session('user_session').'&from_date='.$request_data->input('from_date').'&to_date='.$request_data->input('to_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                            $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Attendance Request','You\'ve successfully applied attendance request(s) and has been forwarded to your reporting authorities.');

                        }else{
                            $this->send_notification('New Attendance Request ', '' . $login_user[0]['employee_name']. ' applied for an attendance request as ('. $request_data->input('attendance_type') .') for the date of '. $request_data->input('attendance_date').'. Please click here to view.','attendance-requests?employee_id='.session('user_session').'&date='.$request_data->input('attendance_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                            $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Attendance Request','You\'ve successfully applied an attendance request and has been forwarded to your reporting authorities.');

                        }
                        
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Attendance request has been submitted. Please wait for response",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You can't apply request because already marked attendance.",
                        ]);
                    }
                }else{

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You've already applied a request for this date. ".$request_data->input('attendance_date'),
                    ]);
                
                }
            }

        }catch(\Exception $e){
            $this->create_error_log('Add New Attendance Request', $e);
            
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
        }
        finally{
            ActivityTracker::track_activity('Create an Attendance Request');
        }
		
    }

    protected function attendance_request(Request $request){
  
       
        try{

            if($request['status'] == "Approve"){

                $process = '';

                $attendance_request_ids = explode(",",$request['ids']) ;

                $login_user = $this->get_login_user_detail();

                foreach($attendance_request_ids as $attendance_request_id){

                    $get_attendance = AttendanceRequest::find($attendance_request_id);
                
                    $attendance_req_time = AttRequestTime::where('attendance_req_id',$attendance_request_id)->orderBy('attendance_request_time_id','DESC')->limit(1)->get(['attendance_checkin_time','attendance_checkout_time'])->toArray();
                    
                    if(!empty($get_attendance)){

                        $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$get_attendance->request_employee_id);
                        
                        if($request_approve_permission != null){
                            return response()->json([
                                'msg' => $request_approve_permission['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
                        $payroll_exists = $this->check_if_payroll_exist($get_attendance->request_attendance_date,$get_attendance->request_employee_id,$custom_message = 'True');

                        if($payroll_exists['status'] == TRUE){
                            return response()->json([
                                'msg' => $payroll_exists['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($get_attendance->request_attendance_date);

                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        if($get_attendance->request_approved_by_hr == 1){
                        
                            $get_attendance->request_approved_by_hr = 2;
                            $get_attendance->request_approved_date_hr = 'Approved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                            $get_attendance->save();
                        
                            $day = date("d", strtotime($get_attendance->request_attendance_date));
                
                            $month = date("m", strtotime($get_attendance->request_attendance_date));
                        
                            $year = date("Y", strtotime($get_attendance->request_attendance_date));
                            
                            $fetch_attendance_for_request_decoded = Attendance::where('att_employee_id',$get_attendance->request_employee_id)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get()->toArray();

                
                            if(empty($fetch_attendance_for_request_decoded)){

                                $employee_shift_start = $this->get_employee_today_shift($get_attendance->request_employee_id,"in",$get_attendance->request_attendance_date);
                    
                                $employee_shift_end = $this->get_employee_today_shift($get_attendance->request_employee_id,"out",$get_attendance->request_attendance_date);

                                $employees_shift_end_time =  $employee_shift_end;
                                

                                if($get_attendance->request_attendance_status == "Half day"){
                                    
                                    $shift_time = CustomHelper::calculate_shift_timings_shift_management($employee_shift_start,$employee_shift_end);
                                    
                                    $employees_shift_end_time = $this->employee_half_day_shift_time($employee_shift_start,$shift_time);
                                    
                                }

                                if($get_attendance->request_attendance_status == "On Time Check In"){

                                    $check_in_time = $get_attendance->request_attendance_date.' '.$attendance_req_time[0]['attendance_checkin_time'];
                                    
                                    $this->multiple_check_in($check_in_time,$get_attendance->request_employee_id, 3, 1);
                                    
                                    $process = 1;
                            
                                }

                                if($get_attendance->request_attendance_status == "Half day" || $get_attendance->request_attendance_status == "Official Tour" || $get_attendance->request_attendance_status == "Late Coming" || $get_attendance->request_attendance_status == "Present"
                                ){
    
                                    $create_attendance = new Attendance();
                                    $create_attendance->att_employee_id = $get_attendance->request_employee_id;
                                    $create_attendance->att_check_in_time = $employee_shift_start;
                                    $create_attendance->att_check_out_time =  $employees_shift_end_time;
                                    $create_attendance->att_check_in_status = 1;
                                    $create_attendance->att_check_out_status = 1;
                                    $create_attendance->att_check_in_source = 3;
                                    $create_attendance->att_check_out_source = 3;
                                    $create_attendance->att_shift_start_time = $employee_shift_start;
                                    $create_attendance->att_shift_end_time = $employee_shift_end;
                                    if($get_attendance->request_attendance_status == "Official Tour"){
                                        $create_attendance->att_check_overall_status = 'Present';
                                    }else{
                                        $create_attendance->att_check_overall_status = $get_attendance->request_attendance_status;
                                    }
                                    $create_attendance->att_for_date = $day;
                                    $create_attendance->att_for_month = $month;
                                    $create_attendance->att_for_year = $year;
                                    $create_attendance->att_request_approval = 1;
                                    $process = $create_attendance->save();
                                }

                                if($process == 1 || $process == null){
                
                                    $this->send_notification('Attendance Request Approval ', ' HR approved your attendance request(s) for the date of '. $day . '-'. $month .'-'.$year.'. Please click here to view. ', 'my-attendance-request?request-id='. $get_attendance->attendance_request_id .'',$get_attendance->request_employee_id,NULL,NULL,'no');

                                    $this->send_push_notification_mobile_app($get_attendance['request_employee_id'], 'Attendance Request','Your attendance request has been approved by HR.');
                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => "Request not approved" 
                                    ]);
                                }
                
                            }else{
                                
                                $update_attendance = Attendance::find($fetch_attendance_for_request_decoded[0]['attendance_id']);
                            
                                $employee_shift_start = $this->get_employee_today_shift($get_attendance->request_employee_id,"in",$get_attendance->request_attendance_date);
                                $employee_shift_end = $this->get_employee_today_shift($get_attendance->request_employee_id,"out",$get_attendance->request_attendance_date);
                                $employees_shift_end_time =  $employee_shift_end;

                                if($get_attendance->request_attendance_status == "Half day"){
                                    
                                    $shift_time = CustomHelper::calculate_shift_timings_shift_management($employee_shift_start,$employee_shift_end);
                                    $employees_shift_end_time = $this->employee_half_day_shift_time($employee_shift_start,$shift_time);
                                }

                                if($get_attendance->request_attendance_status == "On Time Check In"){
                                    
                                    $check_in_time = $get_attendance->request_attendance_date.' '.$attendance_req_time[0]['attendance_checkin_time'];
                                    $this->multiple_check_in_update($check_in_time,$get_attendance->request_employee_id);

                                    $process = 1;
                                }

                                if($get_attendance->request_attendance_status == "On Time Check Out"){
                                    $check_out_time = $get_attendance->request_attendance_date.' '.$attendance_req_time[0]['attendance_checkout_time'];
                                
                                    $this->multiple_check_out($check_out_time,$update_attendance->att_check_out_time,$get_attendance->request_employee_id);

                                    $process = 1;
                                
                                }

                                if($get_attendance->request_attendance_status == "Half day" || $get_attendance->request_attendance_status == "Official Tour" || $get_attendance->request_attendance_status == "Late Coming" ||$get_attendance->request_attendance_status == "Present" 
                                ){
                                    
                                    $update_attendance->att_check_in_time = $employee_shift_start;
                                    $update_attendance->att_check_out_time =  $employees_shift_end_time;
                                    $update_attendance->att_request_approval = 1;
                                    $update_attendance->att_check_in_source = 3;
                                    $update_attendance->att_check_out_source = 3;
                                    $update_attendance->att_check_in_status = 1;
                                    $update_attendance->att_check_out_status = 1;
                                    $update_attendance->att_request_approval = 1;
                                    if($get_attendance->request_attendance_status == "Official Tour"){
                                        $update_attendance->att_check_overall_status = 'Present';
                                    }else{
                                        $update_attendance->att_check_overall_status = $get_attendance->request_attendance_status;
                                    }
                                    $process = $update_attendance->save();
                                }
                                    
                                if($process == 1 || $process == null){
                                    
                                    $this->send_notification('Attendance Request Approval', 'HR approved your attendance request(s) for the date of '. $day . '-'. $month .'-'.$year.'. Please click here to view.','my-attendance-request?request-id='. $get_attendance->attendance_request_id .'',$get_attendance->request_employee_id,NULL,NULL,'no');

                                    $this->send_push_notification_mobile_app($update_attendance['att_employee_id'], 'Attendance Request','Your attendance request(s) has been approved by HR.');
                                
                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => "Request(s) not approved successfully" 
                                    ]);
                                }
                            }
                        }
                    }         
                
                }
            
                if($process == 1 || ($process == null && $process != "")){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' =>"Request(s) aprroved successfully",
                        
                    ]);
                }
                else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>"Already responded to this request",
                        
                    ]);
                }
            }

            if($request['status'] == "DisApprove"){

                $process = '';

                $attendance_request_ids = explode(",",$request['ids']) ;

                $login_user = $this->get_login_user_detail();
        
                foreach( $attendance_request_ids as  $attendance_request_id){
                    $update_request = AttendanceRequest::find($attendance_request_id);

                    if(!empty($update_request)){

                        $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$update_request->request_employee_id);
                        
                        if($request_approve_permission != null){
                            return response()->json([
                                'msg' => $request_approve_permission['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        if($update_request->request_approved_by_hr == 1 ){
                            
                            $update_request->request_approved_by_hr = 3;
                            $update_request->request_approved_date_hr = 'Disapproved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                            $process = $update_request->save();
                            if($process == 1){
                            
                                $this->send_notification('Attendance Request Disapproval ', ' HR disapproved your attendance request(s) for the date of '. $update_request->request_attendance_date . '. Please click here to view. ','my-attendance-request?request-id='. $update_request->attendance_request_id .'',$update_request->request_employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_request['request_employee_id'], 'Attendance Request','Your attendance request has been disapproved by HR.');

                    
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Request(s) not disapproved successfully try again",
                                ]);
                            }
                        }
                    }   
                }
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' =>"Request(s) disaprroved successfully",
                        
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>"Already responded to this request",
                        
                    ]);
                }
            }    

            if($request['status'] == "LeadApprove"){

                $process = '';
                $attendance_request_ids = explode(",",$request['ids']);
                $login_user = $this->get_login_user_detail();
                $approval_maker = session('user_session');
                $status = 1;
            
                foreach($attendance_request_ids as $attendance_request_id){
                
                    $checking_request = RequestApproval::where('request_module_id',1)->where('request_approver_id',session('user_session'))->where('request_id',$attendance_request_id)->get()->toArray();

                    if(empty($checking_request)){

                        $update_request = AttendanceRequest::find($attendance_request_id);

                    
                        if(!empty($update_request) && $update_request->request_approved_by_hr == 1 && in_array($update_request->request_employee_id,$this->get_employee_hierarchy_unique($approval_maker, $status))){
                            $payroll_exists = $this->check_if_payroll_exist($update_request->request_attendance_date,$update_request->request_employee_id,$custom_message = 'True');

                            if($payroll_exists['status'] == TRUE){
                                return response()->json([
                                    'msg' => $payroll_exists['msg'],
                                    'status' => 'FALSE'
                                ]);
                            }

                            $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($update_request->request_attendance_date);

                            if($check_attendance_sheet_availability['status'] == FALSE){
                                return response()->json([
                                    'msg' => $check_attendance_sheet_availability['msg'],
                                    'status' => 'FALSE'
                                ]);
                            }
                            
                            $attendance_approval = new RequestApproval();
                            $attendance_approval->request_module_id = 1;
                            $attendance_approval->request_id = $attendance_request_id;
                            $attendance_approval->request_approver_id =  $approval_maker;
                            $attendance_approval->request_approver_status = 2;
                            $attendance_approval->request_approver_msg = $request['approvermsg'];
                            $attendance_approval->request_approval_time = date('h:i A d M, Y');
                            $process = $attendance_approval->save();
                            
                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $update_request['request_employee_id'])->get(['employee_name'])->toArray();
                        
                                $notification_msg = 'Dear employee, '. $login_user[0]['employee_name']. ' marked your attendance request(s) as approve for the date of ' .$update_request['request_attendance_date']. ' Please click here to view.';
                    
                                $this->send_notification('Attendance Request Approval ',$notification_msg, 'my-attendance-request?request-id='.$update_request->attendance_request_id .'',$update_request['request_employee_id'],NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_request['request_employee_id'], 'Attendance Request','Your attendance request been approved by your line manager.');

                                $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Approved their attendance request(s) for for the date of ' .$update_request['request_attendance_date']. ' Please click here to view.';

                                $this->send_notification('Attendance Request Approval ',$notification_msg_hr, 'attendance-requests?request-id='.$update_request->attendance_request_id .'',NULL,NULL,NULL,'yes');

        
                            }else{
                
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Request(s) not approved successfully ",
                                ]);
                
                            }
                        }
                    }
                
                
                }

                if($process){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Request(s) approved successfully",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Already responded to this request",
                    ]);
                } 
            }   

            if($request['status'] == "LeadDisApprove"){

                $process = '';
                $attendance_request_ids = explode(",",$request['ids']) ;
                $login_user = $this->get_login_user_detail();
                $approval_maker = session('user_session');
                $status = 1;
                    
                foreach( $attendance_request_ids as  $attendance_request_id){
                    $checking_request = RequestApproval::where('request_module_id',1)->where('request_approver_id',session('user_session'))->where('request_id',$attendance_request_id)->get()->toArray();

                    
                    if(empty($checking_request)){

                        $update_request = AttendanceRequest::find($attendance_request_id);
                    
                        if(!empty($update_request) && $update_request->request_approved_by_hr == 1 && in_array($update_request->request_employee_id,$this->get_employee_hierarchy_unique($approval_maker,  $status))){
                            
                            $attendance_approval = new RequestApproval();
                            $attendance_approval->request_module_id = 1;
                            $attendance_approval->request_id = $attendance_request_id;
                            $attendance_approval->request_approver_id =  $approval_maker;
                            $attendance_approval->request_approver_status = 3;
                            $attendance_approval->request_approver_msg = $request['disapprovermsg'];
                            $attendance_approval->request_approval_time = date('h:i A d M, Y');
                            $process = $attendance_approval->save();
                            
                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $update_request['request_employee_id'])->get(['employee_name'])->toArray();
                                
                                $notification_msg = 'Dear employee, ' .$login_user[0]['employee_name']. ' marked your attendance request(s) as disapprove  for the date of '.$update_request['request_attendance_date']. ' Please click here to view.';
                            
                                $this->send_notification('Attendance Request Disapproval ', $notification_msg, 'my-attendance-request?request-id='.$update_request->attendance_request_id . '' ,$update_request['request_employee_id'],NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_request['request_employee_id'], 'Attendance Request','Your attendance request has been disapproved by your line manager.');
                                
                                $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Disapproved their attendance request(s) for for the date of ' .$update_request['request_attendance_date']. ' Please click here to view.';

                                $this->send_notification('Attendance Request Disapproval ',$notification_msg_hr, 'attendance-requests?request-id='.$update_request->attendance_request_id .'',NULL,NULL,NULL,'yes');
        
                            }else{
                
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request(s) not disapproved successfully ",
                                ]);
                
                            }
                        }
                    }
                }
                    

                if($process){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' =>"Request(s) disapproved successfully",
                        
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>"Already responded to this request",
                        
                    ]);
                }
            }   

        }catch(\Exception $e){
            $this->create_error_log('Approved And Disapproved Attendance Request by Manager', $e);
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
            ActivityTracker::track_activity('Approved And Disapproved Attendance Request by Manager');
            
        }
        
    }

  
    protected function update_attendance_request(Request $request_data){
       
        try{  
            if($this->check_request_validation($request_data->attendance_id,1) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Attendance request id is invalid',
                ]);
            }
            $login_user = $this->get_login_user_detail();

            $attendance_update_request = AttendanceRequest::find($request_data->attendance_id);

            $status = 1;

            if(!empty($attendance_update_request)){
                
                $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

                if($request_data->input('edit_attendance_date') < $login_user[0]['employee_doj']){
                    return response()->json([
                        'msg' => 'You can\'t edit leave before your joining date.',
                        'status' => 'FALSE'
                    ]);
                }

                if(!empty($request_data->input('edit_attendance_type')) && $request_data->input('edit_attendance_type') == 'On Time Check Out'){
                  
                    $get_attendances = Attendance::where('att_employee_id',session('user_session'))->where(function($query) use($manage_leaves){
                        $query->where('att_check_overall_status','LIKE', '%Holiday%')
                        ->orwhereIn('att_check_overall_status',$manage_leaves);
                        
                    })
                    ->whereRaw('CONCAT(att_for_year,"-",att_for_month,"-",att_for_date) = "'.$request_data->input('edit_attendance_date').'"')
                    ->get(['attendance_id','att_check_overall_status'])->toArray();

                    if(!empty($get_attendances)){
                        return response()->json([
                            'status' => 'False',
                            'msg' => "You can't update request on this date ". $request_data->input('attendance_date')." beacuse your attendanced already marked as " .$get_attendances[0]['att_check_overall_status'],
                        ]);
                    }   
                }
                

                $payroll_exists = $this->check_if_payroll_exist($request_data->input('edit_attendance_date'),session('user_session'),$custom_message = NULL);

                if($payroll_exists['status'] == TRUE){
                    return response()->json([
                        'msg' => $payroll_exists['msg'],
                        'status' => 'FALSE'
                    ]);
                }

                $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_data->input('edit_attendance_date'));

                if($check_attendance_sheet_availability['status'] == FALSE){
                    return response()->json([
                        'msg' => $check_attendance_sheet_availability['msg'],
                        'status' => 'FALSE'
                    ]);
                }

                $checking_request_responsed = RequestApproval::where('request_module_id',1)->where('request_id',$request_data->attendance_id)->get()->toArray();
                
                if(empty($checking_request_responsed) && $attendance_update_request->request_approved_by_hr == 1){

                    $checking_request =  AttendanceRequest::where('request_employee_id', session('user_session'))->where('request_attendance_date',date('Y-m-d',strtotime($request_data->input('edit_attendance_date'))))->where('attendance_request_id','!=',$request_data->attendance_id)->get(['request_attendance_date'])->toArray();

                    if(!empty($request_data->input('edit_attendance_type')) && $request_data->input('edit_attendance_type') == 'On Time Check Out'){

                        $date = explode('-',$request_data->input('edit_attendance_date'));
                        $get_attendance_check_out_time = Attendance::where('att_employee_id',$request_data->session()->get('user_session'))->where('att_for_date',$date[2])->where('att_for_month',$date[1])->where('att_for_year',$date[0])->get(['attendance_id'])->toArray();
                      
                        if(empty($get_attendance_check_out_time)){
                            return response()->json([
                                'status' => 'False',
                                'msg' => "You can't check out request on this date beacuse you didnt mark the attendance ". $request_data->input('edit_attendance_date'),
                            ]);
                        }
                    }
                
                    if(empty($checking_request)){
                    
                        $attendance_update_request->request_attendance_date = date('Y-m-d',strtotime($request_data->input('edit_attendance_date')));
                        $attendance_update_request->request_attendance_message = $request_data->input('edit_attendance_message');
                        $attendance_update_request->request_attendance_status = $request_data->input('edit_attendance_type');
                        $attendance_update_request->request_submition_date =  date('h:i:s a d F, Y');
                    
                        $process = $attendance_update_request->save();
                        
                        $get_manager_response = AttRequestTime::where('attendance_req_id',$request_data->attendance_id)->where('attendance_req_emp_id','!=',$login_user[0]['employee_id'])->get()->toArray();
                        
                        if(empty($get_manager_response)){
                            
                            if(!empty($request_data->input('edit_attendance_type')) && $request_data->input('edit_attendance_type') == 'On Time Check In'){
                                $checkInRequest = AttRequestTime::updateOrCreate([
                                    'attendance_req_id' => $request_data->input('attendance_id'),
                                ],[
                                    'attendance_checkin_time' => $request_data->input('check_in_time'),
                                    'att_req_time_created_at' => date('h:i:s a d F, Y'),
                                    'attendance_req_emp_id' => $login_user[0]['employee_id'],
                                ]);
                               
                            }
                            if(!empty($request_data->input('edit_attendance_type')) && $request_data->input('edit_attendance_type') == 'On Time Check Out'){
                                $checkOutRequest = AttRequestTime::updateOrCreate([
                                    'attendance_req_id' => $request_data->input('attendance_id'),
                                ],[
                                    'attendance_checkout_time' => $request_data->input('check_out_time'),
                                    'att_req_time_created_at' => date('h:i:s a d F, Y'),
                                    'attendance_req_emp_id' => $login_user[0]['employee_id'],
                                ]);
                            }  
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'You can\'t edit this request now because your reporting authority has changed its time.',
                                "autoreload" => "TRUE",
                            ]);
                        }
                        if( $process == 1){
                            
                            if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                $to_mngr1  = $login_user[0]['employee_senior_manager'];
                            }
                            if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                $to_mngr2 = $login_user[0]['employee_junior_manager'];
                            }
            
                            $this->send_notification('Update Attendance Request ', ''. $login_user[0]['employee_name'].' update an attendance request as ('. $request_data->input('edit_attendance_type') .') for the date of '. $request_data->input('edit_attendance_date').'. Please click here to view.','attendance-requests?employee_id='.session('user_session').'&date='.$request_data->input('edit_attendance_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');
                            
                            $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Update Attendance Request','Your attendance request has been updated.');

                            
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => 'Attendance request has been updated. Please wait for response',
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
                            'msg' => 'You have already submitted a request for '.$request_data->input('edit_attendance_date').'.',
                            
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t edit this request now because your reporting authority has changed its status.',
                        "autoreload" => "TRUE",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Request not found on this id.',
                ]);
            }         

        }catch(\Exception $e){
            $this->create_error_log('Update Attendance Request', $e);
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
            
        }

        finally{
            ActivityTracker::track_activity('Update Attendance Request');
            
        }
    }

    protected function delete_attendance_request(Request $request_data){
       
        try{
            if($this->check_request_validation($request_data->attendance_id,1) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Attendance request id is invalid',
                ]);
            }
            $login_user = $this->get_login_user_detail();

            $attendance_delete_request = AttendanceRequest::find($request_data->attendance_id);

            if(!empty($attendance_delete_request)){

                $checking_request = RequestApproval::where('request_module_id',1)->where('request_id',$request_data->attendance_id)->get()->toArray();

                $get_manager_response = AttRequestTime::where('attendance_req_id',$request_data->attendance_id)->where('attendance_req_emp_id','!=',$login_user[0]['employee_id'])->get()->toArray();
                
                if(empty($checking_request) && empty($get_manager_response) && $attendance_delete_request->request_approved_by_hr == 1 ){
                    $process = AttRequestTime::where('attendance_req_id',$request_data->attendance_id)->where('attendance_req_emp_id',$login_user[0]['employee_id'])->delete();
                    $process = AttendanceRequest::where('attendance_request_id', $request_data->attendance_id)->delete();
                    
                    if($process == 1){
                    
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Attendance request has been deleted',
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Attendance request has not been deleted.',
                        ]);
                    }
                        
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t delete this request now because your reporting authority has changed it.',
                        "autoreload" => "TRUE",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Request not found on this id.',
                ]);
            }        

        }catch(\Exception $e){
            $this->create_error_log('Delete Attendance Request', $e);
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
            
        }

        finally{
            ActivityTracker::track_activity('Delete Attendance Request');
            
        }
    }

    protected function attendance_check_in_time(Request $request_data){
    
        try{
            $login_user = $this->get_login_user_detail();

            $attendance_update_request = AttendanceRequest::find($request_data->input('attendance_req_id'));
           
            $checking_request = AttRequestTime::where('attendance_req_id',$request_data->input('attendance_req_id'))->where('attendance_req_emp_id',$login_user[0]['employee_id'])->get()->toArray();
           
            $employee_depart = AttRequestTime::join('tbl_employees', 'tbl_attendance_request_time.attendance_req_emp_id', '=', 'tbl_employees.employee_id')->where('attendance_req_id',$request_data->input('attendance_req_id'))->whereIn('employee_department',array(1,2))->get(['employee_department','employee_id'])->toArray();

            $status = 1;

            if(empty($checking_request)){

                $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$attendance_update_request->request_employee_id);
                        
                if($request_approve_permission != null){
                    return response()->json([
                        'msg' => $request_approve_permission['msg'],
                        'status' => 'FALSE'
                    ]);
                }
               
                if($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2){

                    if($attendance_update_request->request_approved_by_hr == 1 ){
                        $attendance_req_time = new AttRequestTime();
                        $attendance_req_time->attendance_req_id = $request_data->input('attendance_req_id');
                        $attendance_req_time->attendance_req_emp_id = $login_user[0]['employee_id'];
                        $attendance_req_time->attendance_checkin_time = $request_data->input('check_in_time');
                        $attendance_req_time->att_req_time_created_at = date('h:i:s a d F, Y');
                        $process = $attendance_req_time->save();

                        if($process == 1){

                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Attendance request check-in time updated successfully",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Attendance request check-in time not updated successfully. Please try again.",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You can't update the check-in time beacuse (HR) already responded",
                        ]);
                    }
                }else{

                    if(in_array($attendance_update_request->request_employee_id,$this->get_employee_hierarchy_unique($login_user[0]['employee_id'],  $status))){

                        if(empty($employee_depart) && $attendance_update_request->request_approved_by_hr == 1 ){
                            $attendance_req_time = new AttRequestTime();             
                            $attendance_req_time->attendance_req_id  = $request_data->input('attendance_req_id');
                            $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                            $attendance_req_time->attendance_checkin_time  = $request_data->input('check_in_time');
                            $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');
                            $process = $attendance_req_time->save();

                            if($process == 1){

                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Attendance request check-in time updated successfully",
                                ]);
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Attendance request check-in time not updated successfully. Please try again.",
                                ]);
                            }
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "You can't update the check-in time beacuse (HR) already responded",
                            ]);
                        }
                    }
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You are not reporting authority of this employee.",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "You've already responded to this request",
                ]);
            }
                
            
        }catch(\Exception $e){
            $this->create_error_log('Attendance Request Check In Time Add', $e);
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
        }

        finally{
            ActivityTracker::track_activity('Attendance Request Check In Time Add');
            
        }
    }

    protected function attendance_check_out_time(Request $request_data){
       
        try{
            $login_user = $this->get_login_user_detail();

            $attendance_update_request = AttendanceRequest::find($request_data->input('attendance_req_id'));

            $checking_request = AttRequestTime::where('attendance_req_id',$request_data->input('attendance_req_id'))->where('attendance_req_emp_id',$login_user[0]['employee_id'])->get()->toArray();

            $employee_depart = AttRequestTime::join('tbl_employees', 'tbl_attendance_request_time.attendance_req_emp_id', '=', 'tbl_employees.employee_id')->where('attendance_req_id',$request_data->input('attendance_req_id'))->whereIn('employee_department',array(1,2))->get(['employee_department','employee_id'])->toArray();

            $status = 1;

            if(empty($checking_request)){

                $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$attendance_update_request->request_employee_id);
                        
                if($request_approve_permission != null){
                    return response()->json([
                        'msg' => $request_approve_permission['msg'],
                        'status' => 'FALSE'
                    ]);
                }
              
                if($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2){

                    if($attendance_update_request->request_approved_by_hr == 1){
                        $attendance_req_time = new AttRequestTime();             
                        $attendance_req_time->attendance_req_id  = $request_data->input('attendance_req_id');
                        $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                        $attendance_req_time->attendance_checkout_time  = $request_data->input('check_out_time');
                        $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');
                        $process = $attendance_req_time->save();
            
                        if($process == 1){
            
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Attendance request checkout time updated successfully",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Attendance request checkout time not updated successfully try again!",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You can't update the check out time beacuse (Hr) already responsed",
                        ]);
                    }
                }
                else{
                    if(in_array($attendance_update_request->request_employee_id,$this->get_employee_hierarchy_unique($login_user[0]['employee_id'],  $status))){
                        if(empty($employee_depart) && $attendance_update_request->request_approved_by_hr == 1){
                            $attendance_req_time = new AttRequestTime();             
                            $attendance_req_time->attendance_req_id  = $request_data->input('attendance_req_id');
                            $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                            $attendance_req_time->attendance_checkout_time  = $request_data->input('check_out_time');
                            $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');
                            $process = $attendance_req_time->save();
                
                            if($process == 1){
                
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Attendance request checkout time updated successfully",
                                ]);
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Attendance request checkout time not updated successfully try again!",
                                ]);
                            }
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "You can't update the checkout time beacuse (Hr) already responsed",
                            ]);
                        }
                    }
                   
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You are not reporting authority of this employee.",
                    ]);
                }
               
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Already responsed this checkout time request",
                ]);
            }
           
        }catch(\Exception $e){
            $this->create_error_log('Attendance Request Check Out Time Add', $e);
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
            
        }

        finally{
            ActivityTracker::track_activity('Attendance Request Check Out Time Add');
            
        }
    }


    protected function request_check_in_time_log(Request $request_data){
        try{

            $request_checkIn_time = AttRequestTime::join('tbl_employees', 'tbl_attendance_request_time.attendance_req_emp_id', '=', 'tbl_employees.employee_id')->where('attendance_req_id',$request_data['request_id'])->get(['employee_code','employee_name','attendance_checkin_time','att_req_time_created_at'])->toArray();

        
            if(!empty($request_checkIn_time)){
                return response()->json([
                    'status' => 'TRUE',
                    'data'  => $request_checkIn_time,
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                ]);
            }

        }catch(\Exception $e){
             $this->create_error_log('View Request Response CheckIn Time Log', $e);
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
             ActivityTracker::track_activity('View Request Response CheckIn Time Log');
        }
    }

    protected function request_check_out_time_log(Request $request_data){
        try{

            $request_checkOut_time = AttRequestTime::join('tbl_employees', 'tbl_attendance_request_time.attendance_req_emp_id', '=', 'tbl_employees.employee_id')->where('attendance_req_id',$request_data['request_id'])->get(['employee_code','employee_name','attendance_checkout_time','att_req_time_created_at'])->toArray();

        
            if(!empty($request_checkOut_time)){
                return response()->json([
                    'status' => 'TRUE',
                    'data'  => $request_checkOut_time,
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                ]);
            }

        }catch(\Exception $e){
             $this->create_error_log('View Request Response CheckOut Time Log', $e);
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
             ActivityTracker::track_activity('View Request Response CheckOut Time Log');
        }
    }

    protected function count_days($from_date,$to_date){

        $from_date = strtotime($from_date);
        $to_date = strtotime($to_date);
        
        $timeDiff = abs($to_date - $from_date);
        
        $numberDays = $timeDiff/86400;
        
        $no_of_days = intval($numberDays + 1);
        
        return $no_of_days;
    }
    
}
