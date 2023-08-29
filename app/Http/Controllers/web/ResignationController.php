<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Resignation,Employees, Department, Designation, RequestApproval, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, MailTrait};
use App\Helpers\ActivityTracker;

class ResignationController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait, MailTrait;
    
    const Resignation = "resignation";
    const AllResignation = "all-resignation";

    protected function all_employee_resignation(Request $request_data){
    
        try{
            
            $title = Self::AllResignation;
            
            $login_user = $this->get_login_user_detail();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            $all_resignations = array();
            $responsed_request = array();
            $employees_data = array();

            if($request_data->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }
           
            if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1){

                $where_clause = '1=1';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location)){
                    $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                    $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                }
                if(!empty($request_data->emp_status)){
                    $where_clause .= ' AND employee_isactive = "'.$status.'"';
                }
                if(!empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->req_status)){
                    $limit_query = '';
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND resignation_employee = "'.$request_data->emp_name.'"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND resignation_status = '.$request_data->req_status.'';
                    }
                
                }

                $employees_data = Employees::where('employee_isactive','=',$status)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();
               
                $all_resignations = Resignation::join('tbl_employees', 'tbl_resignation_letters.resignation_employee', '=', 'tbl_employees.employee_id')
                ->whereRaw($where_clause)
                ->Orderby('resignation_id', 'DESC')->get()->toArray();

                $responsed_request = RequestApproval::where('request_module_id', 6)->pluck('request_id')->toArray();

              
            }
           
            elseif($login_user[0]['employee_department'] !== 2 && $login_user[0]['employee_department'] !== 1 &&   $login_user[0]['employee_can_ra'] == 1){
               
                $login_employee_id = $login_user[0]['employee_id'];

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id,  $status);
            
                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request->req_status)){
                    
                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND resignation_employee = "'.$request_data->emp_name.'"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND resignation_status = '.$request_data->req_status.'';
                    }
                }

                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

                $all_resignations = Resignation::join('tbl_employees', 'tbl_resignation_letters.resignation_employee', '=', 'tbl_employees.employee_id')->whereIn('resignation_employee',$total_team_member)->whereRaw($where_clause)->orderBy('resignation_id','DESC')->get()->toArray();

                $responsed_request = RequestApproval::where('request_module_id', 6)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();

                if(empty($all_resignations)){
                 
                    $all_resignations = array();
                  
                }
    
            }

            $resignations_employee = Resignation::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_resignation_letters.resignation_employee')->where('resignation_status','=', 2)->get(['resignation_id','employee_name'])->toArray();

            $send_data = compact('title','login_user','all_resignations','resignations_employee','responsed_request','employees_data','employee_locations');
            
            return view('resignation.all-employee-resignation',$send_data);

        }catch(\Exception $e){

            $this->create_error_log('View All Resignation', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('Access All Resignation Module');
           
        }
    }
    
    protected  function employee_resignation(){

        try{

            $title = Self::Resignation;

            $login_user = $this->get_login_user_detail();

            $resignations = Resignation::where('resignation_employee', session('user_session'))->orderBy('resignation_id','DESC')->get()->toArray();

            $department_name = Department::where('department_id',$login_user[0]['employee_department'])->get('department_name')->toArray();

            $designation_name = Designation::where('designation_id',$login_user[0]['employee_designation'])->get('designation_name')->toArray();
        
            $send_data = compact('title','login_user', 'resignations','department_name','designation_name');

            return view('resignation.employee-resignation',$send_data);
            
        }catch(\Exception $e){

            $this->create_error_log('View Employee Resignation', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('Access Employee Resignation Module');
           
        }
    }

    protected  function my_resignation(){

        try{

            $title = Self::Resignation;

            $login_user = $this->get_login_user_detail();

            $resignations = Resignation::where('resignation_employee', session('user_session'))->orderBy('resignation_id','DESC')->get()->toArray();

            $resignation_request_id = array_column($resignations,'resignation_id');

            $responsed_request = RequestApproval::where('request_module_id',6)->whereIn('request_id', $resignation_request_id)->pluck('request_id')->toArray();
        
            $send_data = compact('title','login_user', 'resignations','responsed_request');

            return view('resignation.my-resignation',$send_data);
            
        }catch(\Exception $e){

            $this->create_error_log('View My Employee Resignation', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('Access My Employee Resignation Module');
           
        }
    }

    protected  function resignation_detail($resignation_id){

        try{

            $title = Self::Resignation;

            $login_user = $this->get_login_user_detail();

            $resignations = Resignation::where('resignation_employee', session('user_session'))->where('resignation_id',$resignation_id)->orderBy('resignation_id','DESC')->get()->toArray();

            $department_name = Department::where('department_id',$login_user[0]['employee_department'])->get('department_name')->toArray();

            $designation_name = Designation::where('designation_id',$login_user[0]['employee_designation'])->get('designation_name')->toArray();
        
            $send_data = compact('title','login_user', 'resignations','department_name','designation_name');

            return view('resignation.my-resignation-detail',$send_data);
            
        }catch(\Exception $e){

            $this->create_error_log('View Employee Resignation Detail', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('Access Employee Resignation Detail Module');
           
        }
    }


    protected function add_employee_resignation(Request $request_data){
     
        try{

            $validator = Validator::make($request_data->all(), [
                'resignation_message' => 'required',
                'from_date' => 'required|date_format:Y-m-d|after_or_equal:today|before:to_date',
                'to_date' => 'required|date_format:Y-m-d|after:from_date',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $resignation_exist = Resignation::where('resignation_employee', $request_data->session()->get('user_session'))->where('resignation_status', '!=', 3)->get()->toArray();

                if(!empty($resignation_exist)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Resignation Already Placed.',
                    ]);
                } 

                $login_user = $this->get_login_user_detail();
            
                $resignation = new Resignation();
                $resignation->resignation_employee = $request_data->session()->get('user_session');
                $resignation->resignation_description = $request_data->input('resignation_message');
                $resignation->resignation_from_date = $request_data->input('from_date');
                $resignation->resignation_to_date = $request_data->input('to_date');
                $resignation->resignation_status = 1;
                
                $process = $resignation->save();
                if($process == 1){

                    if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                        $to_mngr1  = $login_user[0]['employee_ra'];
                    }
                    if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                        $to_mngr2 = $login_user[0]['employee_ra2'];
                    }

            
                    $this->send_notification('Employee Resignation', ''.$login_user[0]['employee_name'].' placed a Resignation. Please click here to view.','resignations?resignation-id='. $resignation->resignation_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');
                    $this->send_module_email(4,'Employee Resignation','<b>'.$login_user[0]['employee_name']. '</b> placed a resignation.',''.url('/').'/resignations?resignation-id='.$resignation->resignation_id);
                    $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Resignation Request','You\'ve successfully applied resignation request and has been forwarded to your reporting authorities.');

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Your resignation has been submitted. Please wait for response',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Something went wrong. Please try again.',
                    ]);
                }
            }    
        }catch(\Exception $e){
            $this->create_error_log('Apply Resignation', $e);
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
            ActivityTracker::track_activity('Add Resignation Module');
        }
    }

    protected function reject_resignation(Request $request){

        try{
            $login_user = $this->get_login_user_detail();

            $resignation = Resignation::find($request->input('resignation_id'));

            $resignation->resignation_status = 3;
        
            $process =  $resignation->save();
    
            if($process == 1){

                $this->send_notification('Resignation DisApproval',  'HR dis-approved your resignation Please click here to view.','employee-resignation?resignation-id='. $resignation->resignation_id .'',$resignation->resignation_employee,NULL,NULL,'no');

                $this->send_push_notification_mobile_app($resignation['resignation_employee'], 'Resignation Request','Your resignation request has been disapproved by HR.');

                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'Resignation letter has been rejected',
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Something went wrong. Please try again.',
                ]);
            
            }
        }catch(\Exception $e){
            $this->create_error_log('Access Dis-Approve Resignation by HR Module', $e);
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
            ActivityTracker::track_activity('Access Dis-Approve Resignation by HR Module');
        }
    }

    protected function resignation_request_approval(Request $request){

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
                    $get_resignation = Resignation::find($request->input('ids'));

                    if(!empty($get_resignation)){

                        if($get_resignation->resignation_status == 1){
                            $get_resignation->request_aprroved_message = $request->input('approvermsg');
                            $get_resignation->request_approved_date_hr = 'Approved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                            $get_resignation->resignation_status = 2;
                            $process =  $get_resignation->save();

                            if($process == 1){

                                $this->send_notification('Resignation Letter Approval', 'HR approved your resignation letter Please click here to view.','employee-resignation?resignation_id='. $get_resignation->resignation_id .'',$get_resignation->resignation_employee,NULL,NULL,'no');
                                
                                $this->send_push_notification_mobile_app($get_resignation['resignation_employee'], 'Resignation Request','Your resignation request has been approved by HR.');

                            }else{
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Resignation not aprroved successfully try again!",
                                    
                                ]);
                            }
                        }
                        
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Resignation not found on this id.',
                        ]);
                    }  
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' =>"Request aprroved successfully",
                            
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
                    $get_resignation = Resignation::find($request->input('ids'));

                    if(!empty($get_resignation)){

                        if($get_resignation->resignation_status == 1){
                            $get_resignation->request_aprroved_message = $request->input('disapprovermsg');
                            $get_resignation->request_approved_date_hr = 'Disapproved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                            $get_resignation->resignation_status = 3;
                            $process =  $get_resignation->save();

                            if($process == 1){

                                $this->send_notification('Resignation Letter Disapproval', 'HR disapproved your resignation letter Please click here to view.','employee-resignation?resignation-id='. $get_resignation->resignation_id .'',$get_resignation->resignation_employee,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($get_resignation['resignation_employee'], 'Resignation Request','Your resignation request has been disapproved by HR.');

                            }else{
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Resignation not disapproved successfully try again!",
                                    
                                ]);
                            }
                        }
                        
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Resignation not found on this id.',
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
                $get_resignation = Resignation::find($request->input('ids'));
                $status = 1;

                $checking_request = RequestApproval::where('request_module_id', 6)->where('request_approver_id',session('user_session'))->where('request_id',$request->input('ids'))->get()->toArray();

                if(empty($checking_request)){

                    $get_resignation = Resignation::find($request->input('ids'));

                    if(!empty($get_resignation) && $get_resignation->resignation_status == 1 && in_array($get_resignation->resignation_employee,$this->get_employee_hierarchy_unique($approval_maker, $status))){

                        $Resignation_request = new RequestApproval();
                        $Resignation_request->request_module_id = 6;
                        $Resignation_request->request_id = $request->input('ids');
                        $Resignation_request->request_approver_id =  $approval_maker;
                        $Resignation_request->request_approver_status = 2;
                        $Resignation_request->request_approver_msg = $request['approvermsg'];
                        $Resignation_request->request_approval_time = date('h:i A d M, Y');
                        $process = $Resignation_request->save();
                        
                        if($process == 1){

                            $employee_name = Employees::where('employee_id', $get_resignation['resignation_employee'])->get(['employee_id','employee_code','employee_name'])->toArray();

                            $notification_msg  = 'Dear employee, '. $login_user[0]['employee_name']. ' Approved your resignation letter for the date of '. $get_resignation->resignation_from_date.'. Please click here to view.';

                            $this->send_notification('Resignation Request Approval',$notification_msg,'employee-resignation?resignation_id='.$get_resignation->resignation_id .'',$get_resignation->resignation_employee,NULL,NULL,'no');

                            $this->send_push_notification_mobile_app($get_resignation['resignation_employee'], 'Resignation Request','Your resignation request been approved by your line manager.');

                            $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Approved their resignation letter for the date of ' .$get_resignation->resignation_from_date. ' Please click here to view.';

                            $this->send_notification('Resignation Request Approval ',$notification_msg_hr, 'resignations?resignation-id='.$get_resignation->resignation_id .'',NULL,NULL,NULL,'yes');

                        }else{
                
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>"Request not approved Successfully ",
                                
                            ]);
                        }   
        
                    }
                        
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
                $get_resignation = Resignation::find($request->input('ids'));
                $status = 1;

                $checking_request = RequestApproval::where('request_module_id', 6)->where('request_approver_id',session('user_session'))->where('request_id',$request->input('ids'))->get()->toArray();

                if(empty($checking_request)){

                    $get_resignation = Resignation::find($request->input('ids'));

                    if(!empty($get_resignation) && $get_resignation->resignation_status == 1 && in_array($get_resignation->resignation_employee,$this->get_employee_hierarchy_unique($approval_maker, $status))){

                        $Resignation_request = new RequestApproval();
                        $Resignation_request->request_module_id = 6;
                        $Resignation_request->request_id = $request->input('ids');
                        $Resignation_request->request_approver_id =  $approval_maker;
                        $Resignation_request->request_approver_status = 3;
                        $Resignation_request->request_approver_msg = $request['disapprovermsg'];
                        $Resignation_request->request_approval_time = date('h:i A d M, Y');
                        $process = $Resignation_request->save();
                        
                        if($process == 1){

                            $employee_name = Employees::where('employee_id', $get_resignation['resignation_employee'])->get(['employee_id','employee_code','employee_name'])->toArray();

                            $notification_msg  = 'Dear employee, '. $login_user[0]['employee_name']. ' Disapproved your resignation letter for the date of '. $get_resignation->resignation_from_date.'. Please click here to view.';

                            $this->send_notification('Resignation Letter Disapproval',$notification_msg,'employee-resignation?resignation_id='.$get_resignation->resignation_id .'',$get_resignation->	resignation_employee,NULL,NULL,'no');

                            $this->send_push_notification_mobile_app($get_resignation['resignation_employee'], 'Resignation Request','Your resignation request has been disapproved by your line manager.');

                            $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Disapproved their resignation letter for the date of ' .$get_resignation->resignation_from_date. ' Please click here to view.';

                            $this->send_notification('Resignation Request Disapproval ',$notification_msg_hr, 'resignations?resignation-id='.$get_resignation->resignation_id .'',NULL,NULL,NULL,'yes');

                        }else{
                
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>"Request not disapproved successfully ",
                                
                            ]);
                        }   
        
                    }
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
            $this->create_error_log('Approved And Disapproved Resignation Letter by Manager', $e);
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
            ActivityTracker::track_activity('Approved And Disapproved Resignation Letter by Manager');
        }
    
    }

}
