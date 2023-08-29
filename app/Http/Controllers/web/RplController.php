<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\Validator;
use App\Models\{Attendance,RplLeave,RequestApproval,OfficeLocation,Employees};

class RplController extends Controller
{
    //
    use LoginEmployee, LogGenerateTrait , NotificationTrait, AttendanceEmployee;

    const MAIN_TITLE = "rpl-leaves";
    const MY_MAIN_TITLE = "my-rpl-leaves";

    protected function index(Request $request_data){
        try{
            $title = Self::MAIN_TITLE;
            
            $login_user = $this->get_login_user_detail();

            if($request_data->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }
            
           $rpl_leaves = array();
           $responsed_request = array();
           $employees_data = array();

            $limit_query = '500';
            
            if(!empty($request_data->leave)){
                $limit_query = '*';
            }

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1){
                
                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->request_for_date)  || !empty($request_data->request_submited_date) || !empty($request_data->req_status)){
                    
                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND rpl_leave_applied_by = "'.$request_data->emp_name.'"';
                    }
                    if(!empty($request_data->request_worked_on_date)){
                        $where_clause .= ' AND rpl_leave_worked_on_date = "'.$request_data->request_worked_on_date.'"';
                    }
                    if(!empty($request_data->request_for_required_date)){
                        $where_clause .= ' AND rpl_leave_required_for_date = "'.$request_data->request_for_required_date.'"';
                    }
                    if(!empty($request_data->request_submited_date)){
                        $where_clause .= ' AND rpl_leave_applied_date = "'.$request_data->request_submited_date.'"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND rpl_leave_approved_by_hr = '.$request_data->req_status.'';
                    }
                }

                $employees_data = Employees::where('employee_isactive', $status)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

               $rpl_leaves = RplLeave::select(['rpl_leave_id','rpl_leave_applied_by','rpl_leave_applied_date','rpl_leave_worked_on_date','rpl_leave_required_for_date','rpl_leave_applied_reason','rpl_leave_approved_by_hr','rpl_leave_approved_date_hr','employee_id','rpl_leave_created_at'])
                ->join('tbl_employees','tbl_rpl_leave_applied.rpl_leave_applied_by','=','tbl_employees.employee_id')
                ->whereRaw($where_clause)
                ->orderBy('rpl_leave_id','desc')
                ->limit($limit_query)
                ->get()->toArray();

                $responsed_request = RequestApproval::where('request_module_id',7)->pluck('request_id')->toArray();

            }
          
            elseif($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] !== 1 && $login_user[0]['employee_can_ra'] == 1){

                $login_employee_id = $login_user[0]['employee_id'];

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id,  $status);

                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->req_status)){
                    
                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND leave_applied_by = "'.$request_data->emp_name.'"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND leave_approved_by_hr = '.$request_data->req_status.'';
                    }
                }
                
                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

               $rpl_leaves = RplLeave::join('tbl_employees', 'tbl_rpl_leave_applied.rpl_leave_applied_by', '=', 'tbl_employees.employee_id')->whereIn('rpl_leave_applied_by',$total_team_member)->whereRaw($where_clause)->orderBy('rpl_leave_id','DESC')->limit($limit_query)->get(['rpl_leave_id','rpl_leave_applied_by','rpl_leave_applied_date','rpl_leave_worked_on_date','rpl_leave_required_for_date','rpl_leave_applied_reason','rpl_leave_approved_by_hr','rpl_leave_approved_date_hr','employee_id','rpl_leave_created_at'])->toArray();

                
                $responsed_request = RequestApproval::where('request_module_id',7)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();

                if(empty($rpl_leaves)){
                   $rpl_leaves = array();
                }


            }

            $dates = array_column($rpl_leaves,'rpl_leave_required_for_date');
   

            $send_data = compact('title','login_user','employees_data','rpl_leaves','employee_locations','responsed_request','dates');
           
            if($login_user[0]['employee_department'] == 1 ||$login_user[0]['employee_department'] == 2 || ($login_user[0]['employee_department'] == 7 && $login_user[0]['employee_can_ra'] == 1) ){
                return view('leave.all-rpl-employees-request', $send_data);
            }else{
                return view('errors.403');
            }
          
        }catch(\Exception $e){
            $this->create_error_log('Access All Employees RPL Leaves Request Module', $e);
            $send_data = compact('title','login_user');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('Access All Employees RPL Leaves Request Module');
           
        }
    }
    protected function my_rpl_leave(){
        try{
            $title = Self::MY_MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $my_rpl_leaves = RplLeave::where('rpl_leave_applied_by',$login_user[0]['employee_id'])->get()->toArray();

            $rpl_leave_request_id = array_column($my_rpl_leaves,'rpl_leave_id');

            $responsed_request = RequestApproval::where('request_module_id',7)->whereIn('request_id',$rpl_leave_request_id)->pluck('request_id')->toArray();

            $send_data = compact('login_user','title','my_rpl_leaves','responsed_request');

            if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 7 ){
                return view('leave.my-rpl-leave', $send_data);
            }else{
                return view('errors.403');
            }
           
        }catch(\Exception $e){
            $this->create_error_log("My RPL Leave Module Access", $e);
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
            ActivityTracker::track_activity('My RPL Leave Module Access'); 
        }
    }

    protected function apply_rpl_leave_request(Request $leave_data){
        try{

           
            $validator = Validator::make($leave_data->all(), [
            
                'rpl_leave_worked' => 'required|date_format:Y-m-d|before_or_equal:'. date('Y-m-d') ,
                'rpl_leave_required' => 'required|date_format:Y-m-d|after:rpl_leave_worked',
                'leave_reason' => 'required',
            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
              
                $login_user = $this->get_login_user_detail();

                $rpl_worked_date  = $leave_data->input('rpl_leave_worked');
			    $doj = strtotime($login_user[0]['employee_doj']);

			    if($doj <= $rpl_worked_date){

                    $payroll_exists = $this->check_if_payroll_exist($leave_data->input('rpl_leave_required'),$login_user[0]['employee_id'],$custom_message = NULL);
        
                    if($payroll_exists['status'] == TRUE){
                        return response()->json([
                            'msg' => $payroll_exists['msg'],
                            'status' => 'FALSE'
                        ]);
                    }

                    $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($leave_data->input('rpl_leave_required'));

                    if($check_attendance_sheet_availability['status'] == FALSE){
                        return response()->json([
                            'msg' => $check_attendance_sheet_availability['msg'],
                            'status' => 'FALSE'
                        ]);
                    }
                    
                    $checking_rpl_leave_exits = RplLeave::where('rpl_leave_applied_by',session('user_session'))->where(function($query) use($leave_data){
                        $query->where('rpl_leave_worked_on_date', $leave_data->input('rpl_leave_worked'))->orWhere('rpl_leave_required_for_date',$leave_data->input('rpl_leave_required'));
                    })->where('rpl_leave_approved_by_hr','!=',3)->get(['rpl_leave_required_for_date','rpl_leave_worked_on_date'])->toArray();

                    
                    if(empty($checking_rpl_leave_exits)){
                        $date = date("d", strtotime($rpl_worked_date));
                        $month = date("m", strtotime($rpl_worked_date));
                        $year = date("Y", strtotime($rpl_worked_date));
                    
                        $get_attendance = Attendance::where('att_employee_id',$login_user[0]['employee_id'])
                        ->where('att_for_date',$date)->where('att_for_month',$month)->where('att_for_year',$year)->get('attendance_id')->toArray();

                        if(!empty($get_attendance)){
                        
                            $add_rpl_leave =  new RplLeave();
                            $add_rpl_leave->rpl_leave_applied_by = $login_user[0]['employee_id'];
                            $add_rpl_leave->rpl_leave_applied_date = date('Y-m-d');
                            $add_rpl_leave->rpl_leave_worked_on_date = $rpl_worked_date;
                            $add_rpl_leave->rpl_leave_required_for_date = $leave_data->input('rpl_leave_required');
                            $add_rpl_leave->rpl_leave_applied_reason = $leave_data->input('leave_reason');
                            $add_rpl_leave->rpl_leave_approved_by_hr = 1;
                            $add_rpl_leave->rpl_leave_created_at = date('Y-m-d H:i:s');
                            $process = $add_rpl_leave->save();

                            if($process == 1){
                
                                if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                                    $to_mngr1 = $login_user[0]['employee_ra'];
                                }
                                if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                                    $to_mngr2 = $login_user[0]['employee_ra2'];
                                }

                                $this->send_notification('New RPL Leave Request ', '' . $login_user[0]['employee_name']. ' applied for rpl leave request for the date of '. $leave_data->input('rpl_leave_required').' Please click here to view.','rpl-leaves?employee_id='.session('user_session').'&date='.$leave_data->input('rpl_leave_required').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                                $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'RPL Leave Request','You\'ve successfully applied rpl leave request and has been forwarded to your reporting authorities.');
                
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "RPL leave request has been submitted. Please wait for response .",
                                ]);
                            }else{
                               
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "RPL leave request has not been submitted. Please try again .",
                                ]);
                            }    
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>  "You have not marked attendance on this date ".$rpl_worked_date,
                            ]);
                        }

                    }else{
                        $rpl_worked_datee = '';
                        $rpl_leave_required = '';
                        $to =  '';
                        if($rpl_worked_date == $checking_rpl_leave_exits[0]['rpl_leave_worked_on_date'] ){
                            $to = 'to';
                            $rpl_worked_datee = $checking_rpl_leave_exits[0]['rpl_leave_worked_on_date'];
                        }
                        if($leave_data->input('rpl_leave_required') == $checking_rpl_leave_exits[0]['rpl_leave_required_for_date'] ){
                            $rpl_leave_required = $checking_rpl_leave_exits[0]['rpl_leave_required_for_date'];
                        }else{
                            $to = '';
                        }
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' =>  "You have already applied rpl leave request on this date ".$rpl_worked_datee.' '.$to.' '.$rpl_leave_required,
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>  "You can't apply rpl request before your joining date",
                    ]);
                }
              
            }   

        }catch(\Exception $e){
            $this->create_error_log('Add New RPL Leave Request', $e);
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
            ActivityTracker::track_activity('Create an RPL Leave Request');
        }
    }

    protected function update_rpl_leave_request(Request $leave_data){

        try{


            $validator = Validator::make($leave_data->all(), [
            
                'rpl_leave_worked' => 'required|date_format:Y-m-d|before_or_equal:'. date('Y-m-d') ,
                'rpl_leave_required' => 'required|date_format:Y-m-d|after:rpl_leave_worked',
                'leave_reason' => 'required',
                'rpl_leave_id' => 'required|numeric',
            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                $login_user = $this->get_login_user_detail();

                if($this->check_request_validation($leave_data->rpl_leave_id,7) == TRUE){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Rpl Leave request id is invalid',
                    ]);
                }
                $leave_update_request = RplLeave::find($leave_data->rpl_leave_id);
                
                if(!empty($leave_update_request)){
                    $rpl_worked_date  = $leave_data->input('rpl_leave_worked');
                    $doj = strtotime($login_user[0]['employee_doj']);
    
                    if($doj <= $rpl_worked_date){
    
                        $payroll_exists = $this->check_if_payroll_exist($leave_data->input('rpl_leave_required'),$login_user[0]['employee_id'],$custom_message = NULL);
            
                        if($payroll_exists['status'] == TRUE){
                            return response()->json([
                                'msg' => $payroll_exists['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
    
                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($rpl_worked_date);
    
                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
                        
                        $checking_request_responsed = RequestApproval::where('request_module_id',7)->where('request_id',$leave_data->rpl_leave_id)->get()->toArray();
         
                        if(empty($checking_request_responsed) && $leave_update_request->rpl_leave_approved_by_hr == 1){
                            
                            $checking_rpl_leave_exits = RplLeave::where('rpl_leave_applied_by',session('user_session'))->where(function($query) use($leave_data){
                                $query->where('rpl_leave_worked_on_date', $leave_data->input('rpl_leave_worked'))->orWhere('rpl_leave_required_for_date',$leave_data->input('rpl_leave_required'));
                            })->where('rpl_leave_id','!=',$leave_data->rpl_leave_id)->get(['rpl_leave_required_for_date','rpl_leave_worked_on_date'])->toArray();
                            
                           
                            if(empty($checking_rpl_leave_exits)){
        
                                $date = date("d", strtotime($rpl_worked_date));
                                $month = date("m", strtotime($rpl_worked_date));
                                $year = date("Y", strtotime($rpl_worked_date));
                            
                                $get_attendance = Attendance::where('att_employee_id',$login_user[0]['employee_id'])
                                ->where('att_for_date',$date)->where('att_for_month',$month)->where('att_for_year',$year)->get('attendance_id')->toArray();
        
                                if(!empty($get_attendance)){
                                
                                    $leave_update_request->rpl_leave_applied_date = date('Y-m-d');
                                    $leave_update_request->rpl_leave_worked_on_date = $rpl_worked_date;
                                    $leave_update_request->rpl_leave_required_for_date = $leave_data->input('rpl_leave_required');
                                    $leave_update_request->rpl_leave_applied_reason = $leave_data->input('leave_reason');
                                    $leave_update_request->rpl_leave_created_at = date('Y-m-d H:i:s');
                                    $process = $leave_update_request->save();
        
                                    if($process == 1){
                        
                                        if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                                            $to_mngr1 = $login_user[0]['employee_ra'];
                                        }
                                        if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                                            $to_mngr2 = $login_user[0]['employee_ra2'];
                                        }
        
                                        $this->send_notification('Update RPL Leave Request ', '' . $login_user[0]['employee_name']. '  update an leave request for the date of '. $leave_data->input('rpl_leave_required').' Please click here to view.','rpl-leaves?employee_id='.session('user_session').'&date='.$leave_data->input('rpl_leave_required').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');
        
                                        $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Update RPL Leave Request','Your rpl leave request has been updated.');
                        
                                        return response()->json([
                                            'status' => 'TRUE',
                                            'msg' => "RPL leave request has been updated. Please wait for response .",
                                        ]);
                                    }else{
                                    
                                        return response()->json([
                                            'status' => 'FALSE',
                                            'msg' => "RPL leave request has not been updated. Please try again .",
                                        ]);
                                    }    
                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' =>  "You have not marked attendance on this date ".$rpl_worked_date,
                                    ]);
                                }
        
                            }else{
                                $rpl_worked_datee = '';
                                $rpl_leave_required = '';
                                $to =  '';
                                if($rpl_worked_date == $checking_rpl_leave_exits[0]['rpl_leave_worked_on_date'] ){
                                    $to = 'to';
                                    $rpl_worked_datee = $checking_rpl_leave_exits[0]['rpl_leave_worked_on_date'];
                                }
                                if($leave_data->input('rpl_leave_required') == $checking_rpl_leave_exits[0]['rpl_leave_required_for_date'] ){
                                    $rpl_leave_required = $checking_rpl_leave_exits[0]['rpl_leave_required_for_date'];
                                }else{
                                    $to = '';
                                }
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>  "You have already applied rpl leave request on this date ".$rpl_worked_datee.' '.$to.' '.$rpl_leave_required,
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
                            'msg' =>  "You can't apply rpl request before your joining date",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>  "Request id not found.",
                    ]);
                }
                
            }        
        }catch(\Exception $e){
            $this->create_error_log('Update RPL Leave Request', $e);
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
            ActivityTracker::track_activity('Update RPL Leave Request');
            
        }
       
    }

    protected function delete_rpl_leave_request(Request $request_data){
      
        try{
            if($this->check_request_validation($request_data->rpl_leave_id,7) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'RPL leave request id is invalid',
                ]);
            }
            $leave_delete_request = RplLeave::find($request_data->rpl_leave_id);

            if(!empty($leave_delete_request)){
                    
                $checking_request_responsed = RequestApproval::where('request_module_id',7)->where('request_id',$request_data->rpl_leave_id)->get()->toArray();
               
                if(empty($checking_request_responsed) && $leave_delete_request->rpl_leave_approved_by_hr == 1){

                    $process = RplLeave::where('rpl_leave_id', $request_data->rpl_leave_id)->delete();

                    if( $process == 1){
                    
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'RPL leave request has been deleted',
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
            $this->create_error_log('Delete RPL Leave Request', $e);
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
            ActivityTracker::track_activity('Delete RPL Leave Request');
            
        }
    }

    protected function rpl_leave_request(Request $request){
       
        try{

            if($request['status'] == "DisApprove"){
                $process = '';
        
                $leave_request_ids = explode(",",$request['ids']);
            
                $login_user = $this->get_login_user_detail();
        
                foreach($leave_request_ids as $leave_request_id){
                
                    $update_leave = RplLeave::find($leave_request_id);

                    if(!empty($update_leave)){
                    
                        $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$update_leave->rpl_leave_applied_by);
                        
                        if($request_approve_permission != null){
                            return response()->json([
                                'msg' => $request_approve_permission['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
                        if($update_leave->rpl_leave_approved_by_hr == 1){

                            $update_leave->rpl_leave_approved_by_hr = 3;
                            $update_leave->rpl_leave_approved_date_hr = 'Dis-Approved By : '.$login_user[0]['employee_name'].', at : '.date('h:i A d M, Y').'';
                            $process = $update_leave->save();
                            if($process == 1){
                    
                                $this->send_notification('RPL Leave Request Disapproval ', ' HR disapproved your rpl leave request(s) for '.$update_leave['rpl_leave_required_for_date'].'. Please click here to view.','my-rpl-leave?request-id='. $update_leave->rpl_leave_id .'', $update_leave['rpl_leave_applied_by'],NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_leave['rpl_leave_applied_by'], 'RPL Leave Request','Your RPL Leave request has been disapproved by HR.');
                            
                            }else{
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg'   =>"Request(s) disapproved not successfully ",
                                    
                                ]);
                            }
                        }
                    }    
                
    
                }
                    
                if($process){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg'   =>"Request(s) disapproved successfully ",
                        
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg'   =>"Already responded to this request.",
                        
                    ]);
                }
            }    

            if($request['status'] == "Approve"){
                $process = '';
        
                $leave_request_ids = explode(",",$request['ids']) ;
                
                $login_user = $this->get_login_user_detail();
    
                foreach($leave_request_ids as $leave_request_id){
                
                    $update_leave = RplLeave::find($leave_request_id);
                   
                    if(!empty($update_leave)){

                        $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$update_leave->rpl_leave_applied_by);
                        
                        if($request_approve_permission != null){
                            return response()->json([
                                'msg' => $request_approve_permission['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        $payroll_exists = $this->check_if_payroll_exist($update_leave->rpl_leave_required_for_date,$update_leave->rpl_leave_applied_by,$custom_message = 'True');

                        if($payroll_exists['status'] == TRUE){
                            return response()->json([
                                'msg' => $payroll_exists['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($update_leave->rpl_leave_required_for_date);

                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        if($update_leave->rpl_leave_approved_by_hr == 1){
                            
                            $update_leave->rpl_leave_approved_by_hr = 2;
                            $update_leave->rpl_leave_approved_date_hr = 'Approved By : '.$login_user[0]['employee_name'].', at : '.date('h:i A d M, Y').'';
                            $process = $update_leave->save();
            
                            $get_attendance = Attendance::where('att_employee_id',$update_leave['rpl_leave_applied_by'])->where('att_for_date',date("d", strtotime($update_leave->rpl_leave_required_for_date)))->where('att_for_month', date("m", strtotime($update_leave->rpl_leave_required_for_date)))->where('att_for_year', date("Y", strtotime($update_leave->rpl_leave_required_for_date)))->get()->toArray();
                            
                            $employee_shift_start = $this->get_employee_today_shift($update_leave['rpl_leave_applied_by'],"in",$update_leave['rpl_leave_required_for_date']);
                            $employee_shift_end = $this->get_employee_today_shift($update_leave['rpl_leave_applied_by'],"out",$update_leave['rpl_leave_required_for_date']);
                            
                            if($process == 1){
                    
                                if(empty($get_attendance)){
                                    
                                    $update_attendance = new Attendance();
                                    $update_attendance->att_employee_id = $update_leave['rpl_leave_applied_by'];
                                    $update_attendance->att_check_in_time = '00:00:00';
                                    $update_attendance->att_check_out_time = '00:00:00';
                                    $update_attendance->att_check_in_status = 0;
                                    $update_attendance->att_check_out_status = 0;
                                    $update_attendance->att_check_out_source = 1;
                                    $update_attendance->att_shift_start_time = $employee_shift_start;
                                    $update_attendance->att_shift_end_time = $employee_shift_end;
                                    $update_attendance->att_check_overall_status = 'RPL';
                                    $update_attendance->att_for_date = date("d", strtotime($update_leave['rpl_leave_required_for_date']));
                                    $update_attendance->att_for_month = date("m", strtotime($update_leave['rpl_leave_required_for_date']));
                                    $update_attendance->att_for_year = date("Y", strtotime($update_leave['rpl_leave_required_for_date']));
                                    $update_attendance->att_request_approval = 3;
                                    $process = $update_attendance->save();
                                }else{
                                    
                                    $update_attendance = Attendance::find($get_attendance[0]['attendance_id']);
                                    $update_attendance->att_check_in_time = '00:00:00';
                                    $update_attendance->att_check_out_time = '00:00:00';
                                    $update_attendance->att_check_in_status = 0;
                                    $update_attendance->att_check_out_status = 0;
                                    $update_attendance->att_check_out_source = 1;
                                    $update_attendance->att_shift_start_time = $employee_shift_start;
                                    $update_attendance->att_shift_end_time = $employee_shift_end;
                                    $update_attendance->att_request_approval = 3;
                                    $update_attendance->att_check_overall_status = 'RPL';
                                    $process = $update_attendance->save();
                                }
                            
                                $this->send_notification('RPL Leave Request Approval ', ' HR approved your rpl leave request(s) for '.$update_leave['rpl_leave_required_for_date'].'. Please click here to view.','my-rpl-leave?request-id='. $update_leave->rpl_leave_id .'', $update_leave['rpl_leave_applied_by'],NULL,NULL,'no');
                                
                                $this->send_push_notification_mobile_app($update_leave['rpl_leave_applied_by'], 'RPL Leave Request','Your RPL Leave request has been approved by HR.');

                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Request(s) not approved successfully",
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
                        'msg' => "Already responded to this request.",
                        
                    ]);
                }
            }

            if($request['status'] == "LeadApprove"){

                $process = '';
        
                $leave_request_ids = explode(",",$request['ids']) ;
            
                $login_user = $this->get_login_user_detail();
                
                $approval_maker = session('user_session');

                $status = 1;

               
                foreach($leave_request_ids as $leave_request_id){
                    
                    $update_leave = RplLeave::find($leave_request_id);

                    $checking_request = RequestApproval::where('request_module_id',7)->where('request_approver_id',session('user_session'))->where('request_id',$leave_request_id)->get()->toArray();

                    if(!empty($update_leave)){

                        $payroll_exists = $this->check_if_payroll_exist($update_leave->rpl_leave_required_for_date,$update_leave->rpl_leave_applied_by,$custom_message = 'True');

                        if($payroll_exists['status'] == TRUE){
                            return response()->json([
                                'msg' => $payroll_exists['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($update_leave->rpl_leave_required_for_date);

                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
                    }
                
                
                    if(empty($checking_request)){
    
                        if(!empty($update_leave) && $update_leave->rpl_leave_approved_by_hr == 1 && in_array($update_leave->rpl_leave_applied_by,$this->get_employee_hierarchy_unique($approval_maker,$status))){
                            
                            $leave_approval = new RequestApproval();
                            $leave_approval->request_module_id = 7;
                            $leave_approval->request_id = $leave_request_id;
                            $leave_approval->request_approver_id =  $approval_maker;
                            $leave_approval->request_approver_status = 2;
                            $leave_approval->request_approver_msg = $request['approvermsg'];
                            $leave_approval->request_approval_time = date('h:i A d M, Y');
                            $process = $leave_approval->save();
                            
                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $update_leave['rpl_leave_applied_by'])->get(['employee_id','employee_code','employee_name'])->toArray();
                            
                                $notification_msg = 'Dear employee, ' .$login_user[0]['employee_name']. ' marked your rpl leave request(s) as approved for the date of '.$update_leave['rpl_leave_required_for_date']. ' Please click here to view.';
                            
                                $this->send_notification('RPL Leave Request Approval ', $notification_msg , 'my-rpl-leave?request-id='. $update_leave->rpl_leave_id .'',$update_leave['rpl_leave_applied_by'],NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_leave['rpl_leave_applied_by'], 'RPL Leave Request','Your RPL Leave request been approved by your line manager.');
                            
                                $notification_msg_hr= ''.$employee_name[0]['employee_name'].''."'s".' Manager approved their rpl leave request(s) for the date of '.$update_leave['rpl_leave_required_for_date'].'. Please click here to view.';

                                $this->send_notification('RPL Leave Request Approval ',  $notification_msg_hr , 'rpl-leaves?request-id='. $update_leave->rpl_leave_id .'',NULL,NULL,NULL,'yes');
                            
        
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
                        'msg' => "Request(s) approved successfully ",
                        
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
        
                $leave_request_ids = explode(",",$request['ids']);

                $login_user = $this->get_login_user_detail();

                $approval_maker = session('user_session');

                $status = 1;
    
                foreach($leave_request_ids as  $leave_request_id){
                    
                    $checking_request = RequestApproval::where('request_module_id',7)->where('request_approver_id',session('user_session'))->where('request_id',$leave_request_id)->get()->toArray();

                
                    if(empty($checking_request)){

                        $update_leave = RplLeave::find($leave_request_id);
                    
                        if(!empty($update_leave) && $update_leave->rpl_leave_approved_by_hr == 1 && in_array($update_leave->rpl_leave_applied_by,$this->get_employee_hierarchy_unique($approval_maker,  $status))){
                            
                            $leave_approval = new RequestApproval();
                            $leave_approval->request_module_id = 7;
                            $leave_approval->request_id = $leave_request_id;
                            $leave_approval->request_approver_id =  $approval_maker;
                            $leave_approval->request_approver_status = 3;
                            $leave_approval->request_approver_msg = $request['disapprovermsg'];
                            $leave_approval->request_approval_time = date('h:i A d M, Y');
                            $process = $leave_approval->save();
                            
                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $update_leave['rpl_leave_applied_by'])->get(['employee_id','employee_code','employee_name'])->toArray();
                        
                                $notification_msg = 'Dear employee, ' . $login_user[0]['employee_name']. ' marked your rpl leave request(s) as Disapproved for the date of '.$update_leave['rpl_leave_required_for_date']. ' Please click here to view.';
                            
                                $this->send_notification('RPL Leave Request Disapproval ', $notification_msg ,'my-rpl-leave?request-id='. $update_leave->rpl_leave_id .'',$update_leave['rpl_leave_applied_by'],NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_leave['rpl_leave_applied_by'], 'RPL Leave Request Disapproval','Your RPL Leave request has been disapproved by your line manager.');
                            
                                $notification_msg_hr= ''.$employee_name[0]['employee_name'].''."'s".' Manager Disapproved their rpl leave request(s) for the date of '.$update_leave['rpl_leave_required_for_date'].'. Please click here to view.';

                                $this->send_notification('RPL Leave Request Disapproval ',  $notification_msg_hr , 'rpl-leaves?request-id='. $update_leave->rpl_leave_id .'',NULL,NULL,NULL,'yes');
                            
                            }else{
                
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Request(s) not disapproved successfully",
                                    
                                ]);
                            }
                        }
                    }
                }
                    
                if($process){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Request(s) disapproved successfully ",
                        
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Already responded to this request",
                        
                    ]);
                }
        
            } 

        }catch(\Exception $e){
            $this->create_error_log('Approved And Disapproved RPL Leave Request by Manager', $e);
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
            ActivityTracker::track_activity('Approved And Disapproved RPL Leave Request by Manager');
            
        }
       
 
    }
   
}
