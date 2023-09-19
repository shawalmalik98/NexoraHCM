<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{AdvanceSalary, Employees, EmployeeSalary, RequestApproval, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait,AttendanceEmployee};
use App\Helpers\ActivityTracker;

use Illuminate\Support\Facades\Validator;

class AdvanceSalaryController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait , AttendanceEmployee;

    const TITLE_GLOBAL    = "all-salary-request";
    const TITLE_GLOBAL2   = "my-salary-request";

    protected function index(Request $request_data){

        try{
            $title = Self::TITLE_GLOBAL;
      
            $login_user = $this->get_login_user_detail();

            $all_requests = array();
            $responsed_request = array();
            $employees_data = array();

            if($request_data->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($login_user[0]['employee_department'] == 3 || $login_user[0]['employee_department'] == 1){
                
                $limit_query = '500';
                
                if(!empty($request_data->salary_req)){
                    $limit_query = '*';
                }

                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->request_created_date) || !empty($request_data->req_status)){
                    
                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND tbl_advance_salary_request.employee_id = "'.$request_data->emp_name.'"';
                    }
                    if(!empty($request_data->request_created_date)){
                        $created_date = date("d-m-Y", strtotime($request_data->request_created_date));
                        $where_clause .= ' AND request_created_at LIKE "%'.$created_date.'%"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND tbl_advance_salary_request.request_approved_by_finance = "'.$request_data->req_status.'"';
                    }
                
                }

                $employees_data = Employees::where('employee_isactive','=',$status)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

                $all_requests = AdvanceSalary::select(['advance_salary_request_id','tbl_advance_salary_request.employee_id','effective_payroll_period','advance_salary_amount','request_message','request_approved_by_finance','request_approved_date_finance','request_aprrover_message','request_status','request_created_at'])
                ->join('tbl_employees', 'tbl_advance_salary_request.employee_id', '=', 'tbl_employees.employee_id')
                ->whereRaw($where_clause)
                ->orderBy('advance_salary_request_id', 'DESC')
                ->limit($limit_query)
                ->get()->toArray();

                $responsed_request = RequestApproval::where('request_module_id', 4)->pluck('request_id')->toArray();
    
            }
    
            elseif($login_user[0]['employee_department'] != 3 && $login_user[0]['employee_department'] !== 1 && $login_user[0]['employee_is_manager'] == 1){
              
                $login_employee_id = $login_user[0]['employee_id'];
                
                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id, $status);
            
                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->req_status)){

                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND tbl_advance_salary_request.employee_id = "'.$request_data->emp_name.'"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND tbl_advance_salary_request.request_approved_by_finance = "'.$request_data->req_status.'"';
                    }
                }

                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();
                
                $all_requests = AdvanceSalary::join('tbl_employees', 'tbl_advance_salary_request.employee_id', '=', 'tbl_employees.employee_id')->whereIn('tbl_advance_salary_request.employee_id',$total_team_member)->whereRaw($where_clause)->orderBy('advance_salary_request_id','DESC')->get(['advance_salary_request_id','tbl_advance_salary_request.employee_id','effective_payroll_period','advance_salary_amount','request_message','request_approved_by_finance','request_approved_date_finance','request_aprrover_message','request_status','request_created_at'])->toArray();
              
                $responsed_request = RequestApproval::where('request_module_id', 4)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();
            }

            $send_data = compact('title','login_user','all_requests','employees_data','employee_locations','responsed_request');
            
            return view('advance-salary.all-advance-salary', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View All Advance Salary Request', $e);
            $send_data = compact('title');
             return view('errors.500');
        }finally{
            ActivityTracker::track_activity('Access All Advance Salary Request Module');
           
        }

    }

    protected function my_salary_request(){

        try{
            $title = Self::TITLE_GLOBAL2;
      
            $login_user = $this->get_login_user_detail();
            
            $my_requests = AdvanceSalary::where('employee_id' , session('user_session'))->orderBy('advance_salary_request_id', 'DESC')->get()->toArray();
          
            $advsalary_request_id = array_column($my_requests,'advance_salary_request_id');

            $responsed_request = RequestApproval::where('request_module_id', 4)->whereIn('request_id', $advsalary_request_id)->pluck('request_id')->toArray();
            
            $employee_salary = EmployeeSalary::where('salary_employee_id' , session('user_session'))->where('salary_effected_date', '<=' , date('Y-m-d'))->orderBy('salary_id','DESC')->limit(1)->get('salary_amount')->toArray();

            $send_data = compact('title','login_user','my_requests' ,'employee_salary','responsed_request');
            
            return view('advance-salary.advance-salary-request', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('Access Request for Advance Salary Module', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('Access Request for Advance Salary Module');
           
        }
    }

    protected function requestfor_advance_salary(Request $request_data){

        try{
                      
            $deduction_month =  date('m')-1;

            if($deduction_month == '-1'){

                $deduction_month = 11;

            }elseif($deduction_month == '0'){

                $deduction_month = 12;

            }

            $employee_salary = EmployeeSalary::where('salary_employee_id' , session('user_session'))->where('salary_effected_date', '<=' , date('Y-m-d'))->orderBy('salary_id','DESC')->limit(1)->get('salary_amount')->toArray();

            $message = ['before'=>'The payback date must be a date before ' .date( 'F-Y',strtotime('+2 month')),'after'=>'The payback date must be a date after '.date( 'F-Y',strtotime(date('Y-'.$deduction_month.''))),'max' => 'Advance salary amount should not be greater than your gross salary '.$employee_salary[0]['salary_amount']];

            $validator = Validator::make($request_data->all(), [
                'advance_salary' => 'required|min:1|max:'.$employee_salary[0]['salary_amount'].'|numeric',
                'payback_date' => 'required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-'.$deduction_month.'').'',
                'message' => 'required', 

            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $login_user = $this->get_login_user_detail();
                $checking_request = AdvanceSalary::where('employee_id', session('user_session'))->where('effective_payroll_period', $request_data->input('payback_date'))->where('request_approved_by_finance', 1)->get('advance_salary_request_id')->toArray();
                
                $payroll_exists = $this->check_if_payroll_exist($request_data->input('payback_date'),session('user_session'),$custom_message = NULL);

                if($payroll_exists['status'] == TRUE){
                    return response()->json([
                        'msg' => $payroll_exists['msg'],
                        'status' => 'FALSE'
                    ]);
                }

                $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_data->input('payback_date'));

                if($check_attendance_sheet_availability['status'] == FALSE){
                    return response()->json([
                        'msg' => $check_attendance_sheet_availability['msg'],
                        'status' => 'FALSE'
                    ]);
                }
                
                if(empty($checking_request)){
                    $salary_request = new AdvanceSalary();
                    $salary_request->employee_id = $request_data->session()->get('user_session');
                    $salary_request->effective_payroll_period = $request_data->input('payback_date');
                    $salary_request->advance_salary_amount = $request_data->input('advance_salary');
                    $salary_request->request_message = $request_data->input('message');
                    $salary_request->request_approved_by_finance = 1;
                    $salary_request->request_created_at = date('d-m-Y H:i:s');
                    $process = $salary_request->save();
                    if($process == 1){

                        if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                            $to_mngr1  = $login_user[0]['employee_senior_manager'];
                        }
                        if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                            $to_mngr2 = $login_user[0]['employee_junior_manager'];
                        }
        
                        $this->send_notification('New Advance Salary Request',''.$login_user[0]['employee_name'].' submitted a Salary request. Please click here to view.','all-salary-request?request-id='. $salary_request->advance_salary_request_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                        $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Advance Salary Request','You\'ve successfully applied Advance Salary request and has been forwarded to your reporting authorities.');

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Advance salary request has been submitted. Please wait for response',
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
                        'msg' => 'You have already submitted an advance salary request for the month of '.date('F-Y',strtotime($request_data->input('payback_date'))).'.',
                    ]);
                }
            }    
        }catch(\Exception $e){
            $this->create_error_log('Apply Advance Salary Request', $e);
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
            ActivityTracker::track_activity('Access Request for Advance Salary Module');
        }
    }

    protected function advance_salary_request_approval(Request $request){

        try{

            if($request['status'] == "Approve"){

                $validator = Validator::make($request->all(), [
                    'approvermsg' => 'required',
                ]);

                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return response()->json([
                            'msg' => $error,
                            'status' => 'FALSE'
                        ]);
                    }
                }else {
                    
                    $process = '';
                    $login_user = $this->get_login_user_detail();
                    $get_advance_salary = AdvanceSalary::find($request->input('ids'));
                
                    if(!empty($get_advance_salary)){

                        $payroll_exists = $this->check_if_payroll_exist($get_advance_salary->effective_payroll_period,$get_advance_salary->employee_id,$custom_message = NULL);

                        if($payroll_exists['status'] == TRUE){
                            return response()->json([
                                'msg' => $payroll_exists['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($get_advance_salary->effective_payroll_period);

                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        if($get_advance_salary->request_approved_by_finance == 1){
                            $get_advance_salary->request_aprrover_message = $request->input('approvermsg');
                            $get_advance_salary->request_approved_by_finance = 2;
                            $get_advance_salary->request_approved_date_finance = 'Approved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                            $process =  $get_advance_salary->save();
                    
                            if($process == 1){

                                $this->send_notification('Advance Salary Request Approval', 'Finance approved your advance salary request. Please click here to view.','my-salary-request?request-id='. $get_advance_salary->advance_salary_request_id .'',$get_advance_salary->employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($get_advance_salary['employee_id'], 'Advance Salary Request','Your advance salary request has been approved by Finance.');

                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Request aprroved successfully",
                                    
                                ]);
                                
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Request not aprroved successfully. Please try again",
                                ]);
                            }
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Already responded to this request",
                                
                            ]);
                        }   
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Request not found on this id.',
                        ]);
                    } 
                }        
            }
            
            if($request['status'] == "DisApprove"){

                $validator = Validator::make($request->all(), [
                    'disapprovermsg' => 'required',
                ]);

                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return response()->json([
                            'msg' => $error,
                            'status' => 'FALSE'
                        ]);
                    }
                }else {

                    $process = '';
                    $login_user = $this->get_login_user_detail();
                    $get_advance_salary = AdvanceSalary::find($request->input('ids'));
                        
                    if(!empty($get_advance_salary)){

                        if($get_advance_salary->request_approved_by_finance == 1){
                            $get_advance_salary->request_aprrover_message = $request->input('disapprovermsg');
                            $get_advance_salary->request_approved_by_finance = 3;
                            $get_advance_salary->request_approved_date_finance = 'DisApproved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                            $process =  $get_advance_salary->save();
                    
                            if($process == 1){
                    
                                $this->send_notification('Advance Salary Request Disapproval', 'Finance Disapproved your advance salary request. Please click here to view.','my-salary-request?request-id='. $get_advance_salary->advance_salary_request_id .'',$get_advance_salary->employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($get_advance_salary['employee_id'], 'Advance Salary Request','Your adavnce salary request has been disapproved by Finance.');
                                
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not disapproved successfully try again",
                                    
                                ]);
                            }
                        }   
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Request not found on this id.',
                        ]);
                    }           
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' =>"Request disapproved successfully",
                            
                        ]);
                    }
                    else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' =>"Already responded to this request",
                            
                        ]);
                    }
                }   
            }    

            if($request['status'] == "LeadApprove"){

                $process = '';
                $login_user = $this->get_login_user_detail();
                $approval_maker = session('user_session');
                $status = 1;

                $update_request = AdvanceSalary::find($request->input('ids'));

                if(!empty($update_request)){

                    $checking_request = RequestApproval::where('request_module_id', 4)->where('request_approver_id',session('user_session'))->where('request_id',$request->input('ids'))->get('request_approval_id')->toArray();

                    if(empty($checking_request)){

                        if(!empty($update_request) && $update_request->request_approved_by_finance == 1 && in_array($update_request->employee_id,$this->get_employee_hierarchy_unique($approval_maker, $status))){

                            $payroll_exists = $this->check_if_payroll_exist($update_request->effective_payroll_period, $update_request->employee_id,$custom_message = NULL);

                            if($payroll_exists['status'] == TRUE){
                                return response()->json([
                                    'msg' => $payroll_exists['msg'],
                                    'status' => 'FALSE'
                                ]);
                            }

                            $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($update_request->effective_payroll_period);

                            if($check_attendance_sheet_availability['status'] == FALSE){
                                return response()->json([
                                    'msg' => $check_attendance_sheet_availability['msg'],
                                    'status' => 'FALSE'
                                ]);
                            }
                            
                            $salary_request = new RequestApproval();
                            $salary_request->request_module_id = 4;
                            $salary_request->request_id = $request->input('ids');
                            $salary_request->request_approver_id =  $approval_maker;
                            $salary_request->request_approver_status = 2;
                            $salary_request->request_approver_msg = $request['approvermsg'];
                            $salary_request->request_approval_time = date('h:i A d M, Y');
                            $process = $salary_request->save();
                            
                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $update_request['employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

                                $notification_msg  = 'Dear employee, '. $login_user[0]['employee_name']. ' marked your advance salary request(s) as Approve. Please click here to view.';

                                $this->send_notification('Advance Salary Request Approval',$notification_msg,'my-salary-request?request-id='. $update_request->advance_salary_request_id .'', $update_request->employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_request['employee_id'], 'Advance Salary Request','Your advance salary request been approved by your line manager.');

                                $notification_msg_hr = ''.$employee_name[0]['employee_name']. ''."'s". ' Manager Approved their advance salary request(s). Please click here to view.';

                                $this->send_notification('Advance Salary Request Approval ',$notification_msg_hr, 'all-salary-request?request-id='.$update_request->advance_salary_request_id .'',NULL,NULL,NULL,'yes');

                            }else{
                    
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not approved successfully ",
                                    
                                ]);
                            }   
            
                        }
                            
                    } 

                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Request not found on this id.',
                    ]);
                }    

                if($process){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Request approved successfully",
                        
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
                $login_user = $this->get_login_user_detail();
                $approval_maker = session('user_session');
                $status = 1;
                
                $update_request = AdvanceSalary::find($request->input('ids'));

                if(!empty($update_request)){

                    $checking_request = RequestApproval::where('request_module_id', 4)->where('request_approver_id',session('user_session'))->where('request_id',$request->input('ids'))->get('request_approval_id')->toArray();

                    if(empty($checking_request)){

                        if(!empty($update_request) && $update_request->request_approved_by_finance == 1 && in_array($update_request->employee_id,$this->get_employee_hierarchy_unique($approval_maker, $status))){

                            $salary_request = new RequestApproval();
                            $salary_request->request_module_id = 4;
                            $salary_request->request_id = $request->input('ids');
                            $salary_request->request_approver_id =  $approval_maker;
                            $salary_request->request_approver_status = 3;
                            $salary_request->request_approver_msg = $request['disapprovermsg'];
                            $salary_request->request_approval_time = date('h:i A d M, Y');
                            $process = $salary_request->save();
                            
                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $update_request['employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

                                $notification_msg  = 'Dear employee, '. $login_user[0]['employee_name'].' marked your advance salary request(s) as Disapproved. Please click here to view.';

                                $this->send_notification('Advance Salary Request Disapproval',$notification_msg,'my-salary-request?request-id='. $update_request->advance_salary_request_id .'', $update_request->employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_request['employee_id'], 'Advance Salary Request','Your advance salary request has been disapproved by your line manager.');

                                $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Disapproved their advance salary request(s). Please click here to view.';

                                $this->send_notification('Advance Salary Request Disapproval ',$notification_msg_hr, 'all-salary-request?request-id='.$update_request->advance_salary_request_id .'',NULL,NULL,NULL,'yes');

                            }else{
                    
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not disapproved successfully ",
                                    
                                ]);
                            }   
            
                        }

                    }
                    
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Request not found on this id.',
                    ]);
                }      

                if($process){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Request disapproved successfully",
                        
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Already responded to this request",
                        
                    ]);
                }
            }
        
        }catch(\Exception $e){
            $this->create_error_log('Approved and Disapproved Advance Salary Request by Manager', $e);
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
            ActivityTracker::track_activity('Approved and Disapproved Advance Salary Request by Manager');
        }
       
    }

    protected function update_advance_salary_request(Request $request_data){
       
        try{

            if($this->check_request_validation($request_data->request_id,4) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Advance salary request id is invalid',
                ]);
            }

            $deduction_month =  date('m')-1;

            if($deduction_month == '-1'){

                $deduction_month = 11;

            }elseif($deduction_month == '0'){

                $deduction_month = 12;

            }

            $employee_salary = EmployeeSalary::where('salary_employee_id' , session('user_session'))->where('salary_effected_date', '<=' , date('Y-m-d'))->orderBy('salary_id','DESC')->limit(1)->get('salary_amount')->toArray();

            $message = ['before'=>'The payback date must be a date before ' .date( 'F-Y',strtotime('+2 month')),'after'=>'The payback date must be a date after '.date( 'F-Y',strtotime(date('Y-'.$deduction_month.''))),'max' => 'Advance salary amount should not be greater than your gross salary '.$employee_salary[0]['salary_amount']];
            
            $validator = Validator::make($request_data->all(), [
                'advance_salary' => 'required|min:1|max:'.$employee_salary[0]['salary_amount'].'|numeric',
                'payback_date' => 'required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-'.$deduction_month.'').'',
                'message' => 'required',

            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $login_user = $this->get_login_user_detail();

                $advancesalary_update_request = AdvanceSalary::find($request_data->request_id);

                if(!empty($advancesalary_update_request)){

                    $checking_request_responsed = RequestApproval::where('request_module_id',4)->where('request_id',$request_data->request_id)->get('request_approval_id')->toArray();
                
                    if(empty($checking_request_responsed) && $advancesalary_update_request->request_approved_by_finance == 1){

                        $checking_request = AdvanceSalary::where('employee_id', session('user_session'))->where('effective_payroll_period',$request_data->input('payback_date'))->where('advance_salary_request_id','!=',$request_data->request_id)->get('advance_salary_request_id')->toArray();
    
                        if(empty($checking_request)){
                        
                            $advancesalary_update_request->effective_payroll_period = $request_data->input('payback_date');
                            $advancesalary_update_request->advance_salary_amount = $request_data->input('advance_salary');
                            $advancesalary_update_request->request_message = $request_data->input('message');
                            $advancesalary_update_request->request_created_at = date('d-m-Y H:i:s');
                        
                            $process =  $advancesalary_update_request->save();
    
                            if( $process == 1){
                                if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                    $to_mngr1  = $login_user[0]['employee_senior_manager'];
                                }
                                if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                    $to_mngr2 = $login_user[0]['employee_junior_manager'];
                                }
                
                                $this->send_notification('Update Advance Salary Request',''.$login_user[0]['employee_name'].' updated an advance salary request. Please click here to view.','all-salary-request?request-id='.$advancesalary_update_request->advance_salary_request_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                                $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Update Advance Salary Request','your advance salary request has been updated.');
    
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => 'Advance salary request has been updated. Please wait for response',
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
                                'msg' => 'You have already submitted a request for the month of '.date('F-Y',strtotime($request_data->input('payback_date'))).'.',
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
            }   
        }catch(\Exception $e){
            $this->create_error_log('Update Advance Salary Request', $e);
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
            ActivityTracker::track_activity('Update Advance Salary Request');
            
        }
        
        
    }

    protected function delete_advance_salary_request(Request $request_data){
      
        try{
            if($this->check_request_validation($request_data->advance_salary_request_id,4) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Advance salary request id is invalid',
                ]);
            }

            $get_salary_request = AdvanceSalary::find($request_data->advance_salary_request_id);
            if(!empty($get_salary_request)){

                $checking_request_responsed = RequestApproval::where('request_module_id',4)->where('request_id',$request_data->advance_salary_request_id)->get('request_approval_id')->toArray();
                
                if(empty($checking_request_responsed) && $get_salary_request->request_approved_by_finance == 1){

                    $process = AdvanceSalary::where('advance_salary_request_id', $request_data->advance_salary_request_id)->delete();
                    if( $process == 1){
                       
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Advance salary request has been deleted',
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
            $this->create_error_log('Delete Advance Salary Request', $e);
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
            ActivityTracker::track_activity('Delete Advance Salary Request');
            
        }
    }

}
