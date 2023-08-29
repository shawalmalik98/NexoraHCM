<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{AttendanceRequest, Employees, RequestApproval, AttRequestTime, Attendance,LeaveManagement};

use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait, AttendanceEmployee};
use App\Helpers\{ActivityTracker};

class AttendanceRequestApiController extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait, AttendanceEmployee;

    const TITLE_GLOBAL = "attendance-request";
    const TITLE_GLOBAL2 = "my-attendance-request";
    const TITLE_SPECIFIC = 'my-attendance';
    
    protected function my_requests(Request $request){
        try{
            $title = Self::TITLE_GLOBAL2;
      
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            
            $my_requests = AttendanceRequest::where('request_employee_id' , $login_user[0]['employee_id'])->orderBy('attendance_request_id', 'DESC')->get()->toArray();
           
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
            ->where('request_employee_id' , $login_user[0]['employee_id'])
            ->orderBy('attendance_request_id','desc')
            ->get(['attendance_request_id','request_employee_id','request_attendance_date','request_attendance_status','request_attendance_message','request_approved_by_hr','request_approved_date_hr','request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_ra','employee_ra2','attendance_checkin_time','attendance_checkout_time','attendance_request_time_id'])->toArray();

            $attendance_request_id = array_column($my_requests,'attendance_request_id');

            $responsed_request = RequestApproval::where('request_module_id',1)->whereIn('request_id',$attendance_request_id)->pluck('request_id')->toArray();

            $get_manager_response = AttRequestTime::whereIn('attendance_req_id',$attendance_request_id)->where('attendance_req_emp_id','!=',$login_user[0]['employee_id'])->pluck('attendance_req_id')->toArray();

            $data = compact('title','my_requests','responsed_request','get_manager_response');

            return $this->api_response_json(TRUE,"My Attendance Request",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View All My Attendance Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('View Personal Attendance Requests API',  $request->header('Bearer-Token'));

        }
     
    }

    protected function requestfor_attendance(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            $process = '';
            $validator = Validator::make($request->post(), [
                'days_assign' => 'required|in:multiple,single',
                'from_date' => ($request->post('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:to_date' : '' ,
                'to_date' => ($request->post('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before_or_equal:now' : '' ,
                'attendance_date' => ($request->post('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before_or_equal:now': '' ,
                'attendance_type' => 'required|in:Present,Half day,On Time Check In,On Time Check Out',
                'check_in_time' => ($request->post('attendance_type') == 'On Time Check In' ) ? 'required|date_format:H:i:s' : '' ,
                'check_out_time' => ($request->post('attendance_type') == 'On Time Check Out' ) ? 'required|date_format:H:i:s' : '' ,
                'attendance_message' => 'required',
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,'',200);
                }
            }else{
                $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
               
                if($request->post('days_assign') == 'multiple'){
                    $attendance_date = $request->post('from_date').'  '.$request->post('to_date');

                    if($request->post('from_date') < $login_user[0]['employee_doj']){
                        return $this->api_response_json(FALSE,'You can\'t apply attendance request before your joining date.','',200);
                    }
                    
                    $no_of_days = $this->count_days($request->post('from_date'),$request->post('to_date'));
                  
                    if(!empty($no_of_days) &&  $no_of_days > 30){

                        return $this->api_response_json(FALSE,'You can\'t apply request for more than 30 days at a time','',200);

                    }
                    for($i = 0; $i<$no_of_days; $i++){
                    
                        $request_attendance_date = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request->post('from_date'))));

                        $checking_request = AttendanceRequest::where('request_employee_id', $login_user[0]['employee_id'])->whereIn('request_attendance_date',[$request_attendance_date])->get(['request_attendance_date'])->toArray();
                        
                        $payroll_exists = $this->check_if_payroll_exist($request_attendance_date,$login_user[0]['employee_id'],$custom_message = NULL);

                        if($payroll_exists['status'] == TRUE){

                            return $this->api_response_json(FALSE,$payroll_exists['msg'],'',200);

                        }

                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_attendance_date);

                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],'',200);
                        }

                        foreach($checking_request as $request_date){
                            
                            $attendance_request_apply_date[] =  $request_date['request_attendance_date'];
                        }
                    
                    }
    
                    if(!empty($checking_request)){

                        $attendance_date = implode(' , ',$attendance_request_apply_date);

                        return $this->api_response_json(FALSE,"You've already applied requests for these dates ".$attendance_date,'',200);

                    }
                   
                }else{
                   
                    if($request->post('attendance_date') < $login_user[0]['employee_doj']){

                        return $this->api_response_json(FALSE,"You can't apply attendance request before your joining date.",'',200);

                    }

                    $payroll_exists = $this->check_if_payroll_exist($request->post('attendance_date'),$login_user[0]['employee_id'],$custom_message = NULL);

                    if($payroll_exists['status'] == TRUE){

                        return $this->api_response_json(FALSE,$payroll_exists['msg'],'',200);

                    }

                    $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request->post('attendance_date'));

                    if($check_attendance_sheet_availability['status'] == FALSE){
                        return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],'',200);
                    }
                  
                    $attendance_date = $request->post('attendance_date');
                   
                    $checking_request = AttendanceRequest::where('request_employee_id', $login_user[0]['employee_id'])->where('request_attendance_date', $request->post('attendance_date'))->where('request_approved_by_hr', '!=', 3)->get('attendance_request_id')->toArray();   
                    
                    if(!empty($request->post('attendance_type')) && $request->post('attendance_type') == 'On Time Check Out'){
                            
                        $date = explode('-',$request->post('attendance_date'));
                    
                        $get_attendance_check_out_time = Attendance::where('att_employee_id',$login_user[0]['employee_id'])->where('att_for_date',$date[2])->where('att_for_month',$date[1])->where('att_for_year',$date[0])->get('attendance_id')->toArray();
                        if(empty($get_attendance_check_out_time)){

                            return $this->api_response_json(FALSE,"You can't request for check out on this date beacuse you didn't mark the attendance ".$request->post('attendance_date'),'',200);
                        
                        }

                        $get_attendances = Attendance::where('att_employee_id',$login_user[0]['employee_id'])->where(function($query) use ($manage_leaves){
                        $query->where('att_check_overall_status','LIKE', '%Holiday%')
                        ->orwhereIn('att_check_overall_status',$manage_leaves);
                        
                        })
                        ->whereRaw('CONCAT(att_for_year,"-",att_for_month,"-",att_for_date) = "'.$request->post('attendance_date').'"')
                        ->get(['attendance_id','att_check_overall_status'])->toArray();

                        if(!empty($get_attendances)){

                            return $this->api_response_json(FALSE,"You can't request on this date ". $request->post('attendance_date')." beacuse your attendanced already marked as " .$get_attendances[0]['att_check_overall_status'],'',200);
                           
                        }   
                    }
                    
                }
              
               
                if(empty($checking_request)){
                   
                    if($request->post('days_assign') == 'multiple'){
                    
                        $no_of_days =  $this->count_days($request->post('from_date'),$request->post('to_date'));
                        
                        for($i = 0; $i<$no_of_days; $i++){
                            $attendance_request = new AttendanceRequest();
                            $dates =  date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request->post('from_date'))));
                            
                            $checking_request_dates = AttendanceRequest::where('request_employee_id', $login_user[0]['employee_id'])->where('request_attendance_date',$dates)->get(['request_attendance_date'])->toArray();

                            if(!empty($request->post('attendance_type')) && $request->post('attendance_type') == 'On Time Check Out'){

                                $date = explode('-', $dates);
                               
                                $get_attendance_check_out_time = Attendance::where('att_employee_id',$login_user[0]['employee_id'])->where('att_for_date',$date[2])->where('att_for_month',$date[1])->where('att_for_year',$date[0])->get('attendance_id')->toArray();

                               
                                if(empty($get_attendance_check_out_time)){
                                    return $this->api_response_json(FALSE,"You can't request for check out on this date beacuse you didn't mark the attendance ". $dates,'',200);
                                }
    
                                $get_attendances = Attendance::where('att_employee_id',$login_user[0]['employee_id'])->where(function($query) use($manage_leaves){
                                $query->where('att_check_overall_status','LIKE', '%Holiday%')
                                ->orwhereIn('att_check_overall_status',$manage_leaves);
                                
                                })
                                ->whereRaw('CONCAT(att_for_year,"-",att_for_month,"-",att_for_date) = "'.$dates.'"')
                                ->get('attendance_id')->toArray();
                             
                            }
                         
                            if(empty($checking_request_dates) && empty($get_attendances)){
                          
                                $attendance_request->request_employee_id = $login_user[0]['employee_id'];
                                $attendance_request->request_attendance_date = $dates;
                                $attendance_request->request_attendance_status = $request->post('attendance_type');
                                $attendance_request->request_attendance_message = $request->post('attendance_message');
                                $attendance_request->request_approved_by_hr = '1';
                                $attendance_request->request_submition_date = date('h:i:s a d F, Y');

                                $process = $attendance_request->save();
                                
                                $attendance_req_time = new AttRequestTime();
                                
                                if(!empty($request->post('attendance_type')) && $request->post('attendance_type') == 'On Time Check In'){

                                    $attendance_req_time->attendance_req_id  = $attendance_request->attendance_request_id;
                                    $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                                    $attendance_req_time->attendance_checkin_time  = $request->post('check_in_time');
                                    $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');

                                    $attendance_req_time->save();
                                
                                }
                                if(!empty($request->post('attendance_type')) && $request->post('attendance_type') == 'On Time Check Out'){
                                    $attendance_req_time->attendance_req_id  = $attendance_request->attendance_request_id;
                                    $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                                    $attendance_req_time->attendance_checkout_time  = $request->post('check_out_time');
                                    $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');

                                    $attendance_req_time->save();
                                }
                                
                            }
                        }
                    
                    }
                    else{
                        
                        $attendance_request = new AttendanceRequest();
                        $attendance_request->request_employee_id = $login_user[0]['employee_id'];
                        $attendance_request->request_attendance_date = $request->post('attendance_date');
                        $attendance_request->request_attendance_status = $request->post('attendance_type');
                        $attendance_request->request_attendance_message = $request->post('attendance_message');
                        $attendance_request->request_approved_by_hr = '1';
                        $attendance_request->request_submition_date = date('h:i:s a d F, Y');

                        $process = $attendance_request->save();
                        
                        $attendance_req_time = new AttRequestTime();

                        if(!empty($request->post('attendance_type')) && $request->post('attendance_type') == 'On Time Check In'){
                            $attendance_req_time->attendance_req_id  = $attendance_request->attendance_request_id;
                            $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                            $attendance_req_time->attendance_checkin_time  = $request->post('check_in_time');
                            $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');

                            $attendance_req_time->save();
                        
                        }
                        if(!empty($request->post('attendance_type')) && $request->post('attendance_type') == 'On Time Check Out'){
                            $attendance_req_time->attendance_req_id  = $attendance_request->attendance_request_id;
                            $attendance_req_time->attendance_req_emp_id  = $login_user[0]['employee_id'];
                            $attendance_req_time->attendance_checkout_time  = $request->post('check_out_time');
                            $attendance_req_time->att_req_time_created_at  = date('h:i:s a d F, Y');

                            $attendance_req_time->save();
                        }
                    }
                 
                    if($process == 1){

                        if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                            $to_mngr1  = $login_user[0]['employee_ra'];
                        }
                        if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                            $to_mngr2 = $login_user[0]['employee_ra2'];
                        }
        
                         if(!empty($request->post('from_date')) && !empty($request->post('to_date'))){

                            $this->send_notification_api('New Attendance Request ', '' . $login_user[0]['employee_name']. ' applied for attendance request(s) as ('. $request->post('attendance_type') .') from '. $request->post('from_date').' to '.$request->post('to_date').'.  Please click here to view.','attendance-requests?employee_id='.$login_user[0]['employee_id'].'&from_date='.$request->post('from_date').'&to_date='.$request->post('to_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                        }else{
                            $this->send_notification_api('New Attendance Request ', '' . $login_user[0]['employee_name']. ' applied for an attendance request as ('. $request->post('attendance_type') .') for the date of '. $request->post('attendance_date').'. Please click here to view.','attendance-requests?employee_id='.$login_user[0]['employee_id'].'&date='.$request->post('attendance_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                        }

                        
                        
                        return $this->api_response_json(TRUE,"Attendance request has been submitted. Please wait for response",'',200);

                    }else{
                        return $this->api_response_json(FALSE,"You can't apply request because already marked attendance.",'',200);
                    }
                }else{
                    return $this->api_response_json(FALSE,"You've already applied a request for this date. ".$request->post('attendance_date'),'',200);
                
                }
            }

        }catch(\Exception $e){

            $this->create_error_log_from_api('Add New Attendance Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Create an Attendance Request API',  $request->header('Bearer-Token'));

        }
		
    }


    protected function index(Request $request){
        try{
            
            $title = Self::TITLE_GLOBAL;
      
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            
            if($request->get('emp_status') == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $all_requests = array();
            $responsed_request = array();
            $limit_query = '500';
            if(!empty($request->get('attendance'))){
                $limit_query = '*';
            }
           
            if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1){
               
                $employees_data = Employees::where('employee_isactive', $status)->get(['employee_id','employee_code','employee_name'])->toArray();

                $where_query = '1=1 AND employee_isactive = "'.$status.'"';

                if(!empty($request->get('emp_status')) || !empty($request->get('emp_name')) || !empty($request->get('request_for_date'))  || !empty($request->get('request_submited_date')) || !empty($request->get('req_status'))){
                  
                    if(!empty($request->get('emp_name'))){
                        $where_query .= ' AND request_employee_id = "'.$request->get('emp_name').'"';
                    }
                    if(!empty($request->get('request_for_date'))){
                        $where_query .= ' AND request_attendance_date = "'.$request->get('request_for_date').'"';
                    }
                    if(!empty($request->get('request_submited_date'))){
                        $submition_date = date("d F, Y", strtotime($request->get('request_submited_date')));
                        $where_query .= ' AND request_submition_date LIKE "%'.$submition_date.'%"';
                    }
                    if(!empty($request->get('req_status'))){
                        $where_query .= ' AND request_approved_by_hr = '.$request->get('req_status').'';
                    }
                }

                $all_requests = AttendanceRequest::select(['attendance_request_id','request_employee_id','request_attendance_date','request_attendance_status','request_attendance_message','request_approved_by_hr','request_approved_date_hr','request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_ra','employee_ra2','attendance_checkin_time','attendance_checkout_time'])
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
                ->whereRaw($where_query)
                ->orderBy('attendance_request_id','desc')
                ->limit($limit_query)
                ->get()->toArray();
    
            }
           
            elseif($login_user[0]['employee_department'] !== 2 && $login_user[0]['employee_department'] !== 1 &&  $login_user[0]['employee_can_ra'] == 1){
               

                $login_employee_id = $login_user[0]['employee_id'];
            
                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id, $status);
              
                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->get(['employee_id','employee_name','employee_code'])->toArray();

                $where_query = '1=1 AND employee_isactive = "'.$status.'"';

                if(!empty($request->get('emp_status')) || !empty($request->get('emp_name')) || !empty($request->get('req_status'))){
                  
                    if(!empty($request->get('emp_name'))){
                        $where_query .= ' AND request_employee_id = "'.$request->get('emp_name').'"';
                    }

                    if(!empty($request->get('req_status'))){
                        $where_query .= ' AND request_approved_by_hr = '.$request->get('req_status').'';
                    }
                }
                
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
                ->whereRaw($where_query)
                ->orderBy('attendance_request_id','DESC')
                ->limit($limit_query)
                ->get(['attendance_request_id','request_employee_id','request_attendance_date','request_attendance_status','request_attendance_message','request_approved_by_hr','request_approved_date_hr','request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_ra','employee_ra2','attendance_checkin_time','attendance_checkout_time'])->toArray();

                $responsed_request = RequestApproval::where('request_module_id',1)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();
            }

            $data = compact('title','all_requests','employees_data','responsed_request');

            return $this->api_response_json(TRUE,"All Attendance Request",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View All Attendance Request API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('View All Attendance Request API',  $request->header('Bearer-Token'));

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


    protected function update_attendance_request(Request $request){
        
        try{

            $validator = Validator::make($request->post(), [
                'attendance_id' => 'required',
                'edit_attendance_date' => 'required|date_format:Y-m-d|before_or_equal:now',
                'edit_attendance_type' => 'required|in:Present,Half day,On Time Check In,On Time Check Out',
                'check_in_time' => ($request->post('edit_attendance_type') == 'On Time Check In' ) ? 'required|date_format:H:i:s' : '' ,
                'check_out_time' => ($request->post('edit_attendance_type') == 'On Time Check Out' ) ? 'required|date_format:H:i:s' : '' ,
                'edit_attendance_message' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);

                }
            }
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            if($this->check_request_validation($request->post('attendance_id'),1,$login_user[0]['employee_id']) == TRUE){
                return $this->api_response_json(FALSE,"Attendance request id is invalid","",200);
            }

            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

            $attendance_update_request = AttendanceRequest::find($request->post('attendance_id'));

            $status = 1;

            if(!empty($attendance_update_request)){

                if($request->post('edit_attendance_date') < $login_user[0]['employee_doj']){    

                    return $this->api_response_json(FALSE,"You can't edit leave before your joining date.","",200);

                }

                if(!empty($request->post('edit_attendance_type')) && $request->post('edit_attendance_type') == 'On Time Check Out'){
                  
                    $get_attendances = Attendance::where('att_employee_id',$login_user[0]['employee_id'])->where(function($query){
                        $query->where('att_check_overall_status','LIKE', '%Holiday%')
                        ->orwhereIn('att_check_overall_status', $manage_leaves);
                        
                    })
                    ->whereRaw('CONCAT(att_for_year,"-",att_for_month,"-",att_for_date) = "'.$request->post('edit_attendance_date').'"')
                    ->get(['attendance_id','att_check_overall_status'])->toArray();

                    if(!empty($get_attendances)){

                        return $this->api_response_json(FALSE,"You can't update request on this date ". $request->post('attendance_date')." beacuse your attendanced already marked as " .$get_attendances[0]['att_check_overall_status'],"",200);

                    }   
                }
                

                $payroll_exists = $this->check_if_payroll_exist($request->post('edit_attendance_date'),$login_user[0]['employee_id'],$custom_message = NULL);

                if($payroll_exists['status'] == TRUE){
                    return $this->api_response_json(FALSE,$payroll_exists['msg'],'',200);
                }

                $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request->post('edit_attendance_date'));

                if($check_attendance_sheet_availability['status'] == FALSE){
                    return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],'',200);
                }

                $checking_request_responsed = RequestApproval::where('request_module_id',1)->where('request_id',$request->post('attendance_id'))->get()->toArray();
                
                if(empty($checking_request_responsed) && $attendance_update_request->request_approved_by_hr == 1){

                    $checking_request =  AttendanceRequest::where('request_employee_id', $login_user[0]['employee_id'])->where('request_attendance_date',date('Y-m-d',strtotime($request->post('edit_attendance_date'))))->where('attendance_request_id','!=',$request->post('attendance_id'))->get(['request_attendance_date'])->toArray();

                    if(!empty($request->post('edit_attendance_type')) && $request->post('edit_attendance_type') == 'On Time Check Out'){

                        $date = explode('-',$request->post('edit_attendance_date'));
                        $get_attendance_check_out_time = Attendance::where('att_employee_id',$login_user[0]['employee_id'])->where('att_for_date',$date[2])->where('att_for_month',$date[1])->where('att_for_year',$date[0])->get()->toArray();
                      
                        if(empty($get_attendance_check_out_time)){

                            return $this->api_response_json(FALSE,"You can't check out request on this date beacuse you didnt mark the attendance ". $request->post('edit_attendance_date'),"",200);

                        }
                    }
                
                    if(empty($checking_request)){
                    
                        $attendance_update_request->request_attendance_date = date('Y-m-d',strtotime($request->post('edit_attendance_date')));
                        $attendance_update_request->request_attendance_message = $request->post('edit_attendance_message');
                        $attendance_update_request->request_attendance_status = $request->post('edit_attendance_type');
                        $attendance_update_request->request_submition_date =  date('h:i:s a d F, Y');
                    
                        $process = $attendance_update_request->save();
                        
                        $get_manager_response = AttRequestTime::where('attendance_req_id',$request->post('attendance_id'))->where('attendance_req_emp_id','!=',$login_user[0]['employee_id'])->get()->toArray();
                        
                        if(empty($get_manager_response)){
                            
                            if(!empty($request->post('edit_attendance_type')) && $request->post('edit_attendance_type') == 'On Time Check In'){
                                $checkInRequest = AttRequestTime::updateOrCreate([
                                    'attendance_req_id'   => $request->post('attendance_id'),
                                ],[
                                    'attendance_checkin_time'    => $request->post('check_in_time'),
                                    'att_req_time_created_at'    => date('h:i:s a d F, Y'),
                                    'attendance_req_emp_id'      => $login_user[0]['employee_id'],
                                ]);
                               
                            }
                            if(!empty($request->post('edit_attendance_type')) && $request->post('edit_attendance_type') == 'On Time Check Out'){
                                $checkOutRequest = AttRequestTime::updateOrCreate([
                                    'attendance_req_id'   => $request->post('attendance_id'),
                                ],[
                                    'attendance_checkout_time'    => $request->post('check_out_time'),
                                    'att_req_time_created_at'    => date('h:i:s a d F, Y'),
                                    'attendance_req_emp_id'      => $login_user[0]['employee_id'],
                                ]);
                            }  
                        }else{
                            return $this->api_response_json(FALSE,"You can't edit this request now because your reporting authority has changed its time.","",200);

                        }
                        if( $process == 1){
                            
                            if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                                $to_mngr1  = $login_user[0]['employee_ra'];
                            }
                            if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                                $to_mngr2 = $login_user[0]['employee_ra2'];
                            }
            
                            $this->send_notification_api('Update Attendance Request', '' . $login_user[0]['employee_name']. ' update for an attendance request as ('. $request->post('edit_attendance_type') .') for the date of '. $request->post('edit_attendance_date').' Please click here to view.','attendance-requests?employee_id='.$login_user[0]['employee_id'].'&date='.$request->post('edit_attendance_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);
                            
                            return $this->api_response_json(TRUE,"Attendance request has been updated. Please wait for response.","",200);

                        }else{

                            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);

                        }
                        
                    }else{

                        return $this->api_response_json(FALSE,"You have already submitted a request for ".$request->post('edit_attendance_date'),"",200);

                    }
                }else{

                    return $this->api_response_json(FALSE,"You can't edit this request now because your reporting authority has changed its status.","",200);

                }
            }else{

                return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
            }    

        }catch(\Exception $e){

            $this->create_error_log_from_api('Update Attendance Request API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
            
        }

        finally{

            ActivityTracker::track_activity_from_api('Update Attendance Request API',  $request->header('Bearer-Token'));

        }
    }

    protected function delete_attendance_request(Request $request){
      
        try{
            
            $validator = Validator::make($request->post(), [
                'attendance_id' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('attendance_id'),1,$login_user[0]['employee_id']) == TRUE){
                return $this->api_response_json(FALSE,"Attendance request id is invalid","",200);
            }

            $attendance_delete_request = AttendanceRequest::find($request->post('attendance_id'));

            if(!empty($attendance_delete_request)){

                $checking_request = RequestApproval::where('request_module_id',1)->where('request_id',$request->post('attendance_id'))->get()->toArray();

                $get_manager_response = AttRequestTime::where('attendance_req_id',$request->post('attendance_id'))->where('attendance_req_emp_id','!=',$login_user[0]['employee_id'])->get()->toArray();
                
                if(empty($checking_request) && empty($get_manager_response) && $attendance_delete_request->request_approved_by_hr == 1 ){
                    $process = AttRequestTime::where('attendance_req_id',$request->post('attendance_id'))->where('attendance_req_emp_id',$login_user[0]['employee_id'])->delete();
                    $process = AttendanceRequest::where('attendance_request_id', $request->post('attendance_id'))->delete();
                    
                    if($process == 1){
                        
                        return $this->api_response_json(TRUE,"Attendance request has been deleted","",200);

                    }else{
                        return $this->api_response_json(FALSE,"Attendance request has not been deleted.","",200);
                    }
                        
                }else{
                    return $this->api_response_json(FALSE,"You can't delete this request now because your reporting authority has changed it.","",200);
                }
            }else{

                return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
            }          

        }catch(\Exception $e){

            $this->create_error_log_from_api('Delete Attendance Request API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
            
        }

        finally{

            ActivityTracker::track_activity_from_api('Delete Attendance Request API',  $request->header('Bearer-Token'));
            
        }
    }
}
