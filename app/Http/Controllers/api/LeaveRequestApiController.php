<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use App\Models\{Notification, Employees, LeaveRequest, Attendance, HrmSetting, Leave, RequestApproval,LeaveManagement};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait, AttendanceEmployee};
use App\Helpers\ActivityTracker;

class LeaveRequestApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait, ApiResponseTrait, AttendanceEmployee;

    const TITLE_GLOBAL = "employees-leaves";
    const TITLE_GLOBAL2 = "my-leaves"; 

    protected function index(Request $request){
        try{
           
            $title = Self::TITLE_GLOBAL;
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($request->get('emp_status') == "2"){
                $status = 0;
            }else{
                $status = 1;
            }
            $employees_data = Employees::where('employee_isactive', $status)->get(['employee_id','employee_code','employee_name'])->toArray();
            
            $my_leaves = array();
            $responsed_request = array();

            $limit_query = '500';
            
            if(!empty($request->get('leave'))){
                $limit_query = '*';
            }

            if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1){

            
                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                if(!empty($request->get('emp_status')) || !empty($request->get('emp_name')) || !empty($request->get('request_for_date')) || !empty($request->get('request_submited_date')) || !empty($request->get('req_status'))){
                
                    if(!empty($request->get('emp_name'))){
                        $where_clause .= ' AND leave_applied_by = "'.$request->get('emp_name').'"';
                    }
                    if(!empty($request->get('request_for_date'))){
                        $where_clause .= ' AND leave_applied_for_date = "'.$request->get('request_for_date').'"';
                    }
                    if(!empty($request->get('request_submited_date'))){
                        $submition_date = date("d-m-Y", strtotime($request->get('request_submited_date')));
                        $where_clause .= ' AND leave_applied_date LIKE "%'.$submition_date.'%"';
                    }
                    if(!empty($request->get('req_status'))){
                        $where_clause .= ' AND leave_approved_by_hr = '.$request->get('req_status').'';
                    }
                }

                $my_leaves = LeaveRequest::select(['leave_applied_id','leave_applied_by','leave_applied_date','leave_applied_for_date','leave_applied_reason','leave_applied_type','leave_applied_attachment','leave_approved_by_hr','leave_approved_date_hr','employee_id','leave_name'])
                ->join('tbl_employees','tbl_leaves_applied.leave_applied_by','=','tbl_employees.employee_id')->join('tbl_leave_management', 'tbl_leaves_applied.leave_applied_type','=', 'tbl_leave_management.leave_manage_id')
                ->whereRaw($where_clause)
                ->orderBy('leave_applied_id','desc')
                ->limit($limit_query)
                ->get()->toArray();
            

            }
            
            elseif($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] !== 1 && $login_user[0]['employee_is_manager'] == 1){

                $login_employee_id = $login_user[0]['employee_id'];

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id,  $status);

                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->get(['employee_id','employee_code','employee_name'])->toArray();

                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';

                if(!empty($request->get('emp_status')) || !empty($request->get('emp_name')) || !empty($request->get('req_status'))){
                
                    if(!empty($request->get('emp_name'))){
                        $where_clause .= ' AND leave_applied_by = "'.$request->get('emp_name').'"';
                    }
                    if(!empty($request->get('req_status'))){
                        $where_clause .= ' AND leave_approved_by_hr = '.$request->get('req_status').'';
                    }
                }
               
                $my_leaves = LeaveRequest::join('tbl_employees', 'tbl_leaves_applied.leave_applied_by', '=', 'tbl_employees.employee_id')->whereIn('leave_applied_by',$total_team_member)->whereRaw($where_clause)->orderBy('leave_applied_id','DESC')->limit($limit_query)->get(['leave_applied_id','leave_applied_by','leave_applied_date','leave_applied_for_date','leave_applied_reason','leave_applied_type','leave_applied_attachment','leave_approved_by_hr','leave_approved_date_hr','employee_id'])->toArray();

                $responsed_request = RequestApproval::where('request_module_id',2)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();

                if(empty($my_leaves)){
                    $my_leaves = array();
                }


            }

            $data = compact('title','employees_data','my_leaves','responsed_request');

            return $this->api_response_json(TRUE,"View All Leave Request",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View All Leave Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }

        finally{

            ActivityTracker::track_activity_from_api('Access All Employees Leaves Request Module',  $request->header('Bearer-Token'));
        }

    }

    protected function my_leaves(Request $request){
       
        try{
            $title = Self::TITLE_GLOBAL2;
        
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
           
            $my_leaves = LeaveRequest::join('tbl_employees', 'tbl_leaves_applied.leave_applied_by', '=', 'tbl_employees.employee_id')->join('tbl_leave_management', 'tbl_leaves_applied.leave_applied_type','=', 'tbl_leave_management.leave_manage_id')->where('leave_applied_by', $login_user[0]['employee_id'])->orderBy('leave_applied_id','DESC')->get(['leave_applied_id','leave_applied_for_date','leave_applied_reason','leave_applied_date','leave_approved_by_hr','leave_approved_date_hr','leave_applied_attachment','leave_name','leave_manage_id'])->toArray();

            $leave_request_id = array_column($my_leaves,'leave_applied_id');
           
            $responsed_request = RequestApproval::where('request_module_id',2)->whereIn('request_id',$leave_request_id)->pluck('request_id')->toArray();
            
            $data = compact('title','my_leaves','responsed_request');
           
            return $this->api_response_json(TRUE,"My Leave Request ",$data,200);
        }
        catch(\Exception $e){
             
            $this->create_error_log_from_api('View All My Leave Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{

            ActivityTracker::track_activity_from_api('View All My Leave Request API',  $request->header('Bearer-Token'));

        }
        
    }

    protected function applyfor_personalleave(Request $request){
        
        return $this->api_response_json(FALSE,"You can't apply leaves at this moment. Please contact HR.","",200);
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $filename = '';

            if($request->hasFile('leave_applied_attachment')){
                $filename = $request->file('leave_applied_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->post(), [
            
                'days_assign' => 'required|in:multiple,single',
                'from_date' => ($request->post('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')). '': '' ,
                'to_date' => ($request->post('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|after:from_date' : '' ,
                'leave_date' => ($request->post('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')). '': '' ,
                'leave_type' => 'required',
                'leave_reason' => 'required',
                'leave_applied_attachment' => ['max:10000', ($filename == 'txt') ? 
                File::types(['jpg','png','jpeg','docx','pdf','txt'])
                :
                File::types(['jpg','png','jpeg','docx','pdf'])],

            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $leave_date;
                $reamining_leaves;
                
                $leave_name = LeaveManagement::where('leave_manage_id',$request->post('leave_type'))->get(['leave_manage_id','leave_name','leave_visibilty'])->toArray();

                if(!empty($leave_name)){
                    $leave_alloted = Leave::where('leaves_employee_id',$login_user[0]['employee_id'])->where('leave_type_id',$request->post('leave_type'))->get(['leave_allotement'])->toArray();

                    if($leave_name[0]['leave_visibilty'] == 1){
                    
                        if(empty($leave_alloted)){
                            $leave_alloted[0]['leave_allotement'] = 0;
                        }
        
                        $no_of_days = 1;
                        if($request->post('days_assign') == 'multiple'){
                            $no_of_days = $this->count_days($request->post('from_date'),$request->post('to_date'));
                        }
                        
                        $leave_request_applied = LeaveRequest::where('leave_applied_by',$login_user[0]['employee_id'])
                            ->where('leave_applied_type', $request->post('leave_type'))
                            ->where('leave_approved_by_hr',1)
                            ->count();
                            
                        $get_availed_leaves = Attendance::where('att_employee_id',$login_user[0]['employee_id'])->where('att_check_overall_status', $leave_name[0]['leave_name'])->count();
                       
                        $total_leaves = $get_availed_leaves + $leave_request_applied;
                    
                        $reamining_leaves = $leave_alloted[0]['leave_allotement'] - $total_leaves;
                    
                        if(!empty($leave_alloted) && $reamining_leaves < $no_of_days){
                            if($reamining_leaves < 0){
                                $reamining_leaves = 0;
                            }
                            $message = 'You have only ' . $reamining_leaves . ' ' . $leave_name[0]['leave_name']. '(s) reamining to apply';
                            return $this->api_response_json(FALSE,$message,"",200);

                        }
                        
                        if($request->post('days_assign') == 'multiple'){
                            $leave_date = $request->post('from_date').'  '.$request->post('to_date');

                            if($request->post('from_date') < $login_user[0]['employee_doj']){

                                return $this->api_response_json(FALSE,"You can't apply leave before your joining date.","",200);
                            }

                            $no_of_days =  $this->count_days($request->post('from_date'),$request->post('to_date'));
                        
                            for($i = 0; $i<$no_of_days; $i++){
                            
                                $request_leave_date = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request->post('from_date'))));

                                $checking_applied_leave_request = LeaveRequest::where('leave_applied_by', $login_user[0]['employee_id'])->whereIn('leave_applied_for_date',[$request_leave_date])->get(['leave_applied_for_date'])->toArray();
                                
                                if($request->post('leave_type') == "3"){
                                    $employement_status_check = $this->check_if_employee_permanent($request_leave_date,$login_user[0]['employee_id'],$custom_message = NULL);
    
                                    if(!$employement_status_check['status']){
                                        return response()->json([
                                            'msg' => $employement_status_check['msg'],
                                            'status' => 'FALSE'
                                        ]);
                                    }
                                }

                                $payroll_exists = $this->check_if_payroll_exist($request_leave_date,$login_user[0]['employee_id'],$custom_message = NULL);

                                if($payroll_exists['status'] == TRUE){

                                    return $this->api_response_json(FALSE,$payroll_exists['msg'],"",200);

                                }

                                $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_leave_date);

                                if($check_attendance_sheet_availability['status'] == FALSE){
                                    return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],'',200);
                                }

                                foreach($checking_applied_leave_request as $request_date){
                                
                                    $leave_request_apply_date[] =  $request_date['leave_applied_for_date'];
                                }
                                
                            }
                        
                            if(!empty($leave_request_apply_date)){

                                $leave_date = implode(' , ',$leave_request_apply_date);

                                $message = 'You have already submitted a leave request for '.$leave_date.'.';
                                return $this->api_response_json(FALSE,$message,"",200);

                            }
                        
                        }
                        else{

                            if($request->post('leave_date') < $login_user[0]['employee_doj']){

                                return $this->api_response_json(FALSE,"You can't apply leave before your joining date.","",200);
                                
                            }

                            if($request->post('leave_type') == "3"){
                                $employement_status_check = $this->check_if_employee_permanent($request->post('leave_date'),$login_user[0]['employee_id'],$custom_message = NULL);

                                if(!$employement_status_check['status']){
                                    return response()->json([
                                        'msg' => $employement_status_check['msg'],
                                        'status' => 'FALSE'
                                    ]);
                                }
                            }

                            $payroll_exists = $this->check_if_payroll_exist($request->post('leave_date'),$login_user[0]['employee_id'],$custom_message = NULL);

                            if($payroll_exists['status'] == TRUE){

                                return $this->api_response_json(FALSE,$payroll_exists['msg'],"",200);

                            }

                            $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request->post('leave_date'));

                            if($check_attendance_sheet_availability['status'] == FALSE){
                                return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],'',200);
                            }

                            $leave_date = $request->post('leave_date');
                            $checking_applied_leave_request = LeaveRequest::where('leave_applied_by', $login_user[0]['employee_id'])->where('leave_applied_for_date', $request->post('leave_date'))->where('leave_approved_by_hr', '!=', 3)->get()->toArray();
                            
                        }
                    
                        if(empty($checking_applied_leave_request)){
                        
                            if($request->post('days_assign') == 'multiple'){
                                
                                $no_of_days = $this->count_days($request->post('from_date'),$request->post('to_date'));
                                
                                for($i = 0; $i<$no_of_days; $i++){
                                    $apply_leave = new LeaveRequest();
                                    
                                    $dates = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request->post('from_date'))));
                                    
                                    $checking_request_dates = LeaveRequest::where('leave_applied_by', $login_user[0]['employee_id'])->where('leave_applied_for_date',$dates)->get(['leave_applied_for_date'])->toArray();
                                    
                                    if(empty($checking_request_dates)){

                                        $apply_leave->leave_applied_by = $login_user[0]['employee_id'];
                                        $apply_leave->leave_applied_date = date('d-m-Y H:i:s');
                                        $apply_leave->leave_applied_for_date = $dates;
                                        $apply_leave->leave_applied_reason = $request->post('leave_reason');
                                        $apply_leave->leave_applied_type = $request->post('leave_type');
                                        $apply_leave->leave_approved_by_hr = 1;

                                        if($request->hasFile('leave_applied_attachment')){
                        
                                            $fileNameToStore = $request->file('leave_applied_attachment')->getClientOriginalName();
                                            
                                            $request->file('leave_applied_attachment')->storeAs('portal_documents/leave_attachment/',$fileNameToStore);
                                
                                            $apply_leave->leave_applied_attachment = $fileNameToStore;  

                                        }
                                        $process = $apply_leave->save();
                                    }
                                
                                }
                                
                            }
                            
                            else{
                                $apply_leave = new LeaveRequest();
                                $apply_leave->leave_applied_by = $login_user[0]['employee_id'];
                                $apply_leave->leave_applied_date = date('d-m-Y H:i:s');
                                $apply_leave->leave_applied_for_date = $request->post('leave_date');
                                $apply_leave->leave_applied_reason = $request->post('leave_reason');
                                $apply_leave->leave_applied_type = $request->post('leave_type');
                                $apply_leave->leave_approved_by_hr = 1;

                                if($request->hasFile('leave_applied_attachment')){
                
                                    $fileNameToStore = $request->file('leave_applied_attachment')->getClientOriginalName();
                                    
                                    $request->file('leave_applied_attachment')->storeAs('portal_documents/leave_attachment/',$fileNameToStore);
                        
                                    $apply_leave->leave_applied_attachment = $fileNameToStore;
                        
                                }

                                $process = $apply_leave->save();
                            
                            }
                            
                            if($process == 1){
                

                                if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                    $to_mngr1 = $login_user[0]['employee_senior_manager'];
                                }
                                if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                    $to_mngr2 = $login_user[0]['employee_junior_manager'];
                                }
                
                                if(!empty($request->post('from_date')) && !empty($request->post('to_date'))){
                                    $this->send_notification_api('New Leave Request ', '' . $login_user[0]['employee_name']. ' applied for leave request(s) as ('. $leave_name[0]['leave_name'] .') from '. $request->post('from_date').' to '.$request->post('to_date').'. Please click here to view.','employees-leaves?employee_id='.$login_user[0]['employee_id'].'&from_date='.$request->post('from_date').'&to_date='.$request->post('to_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);
        
                                }else{
                                    $this->send_notification_api('New Leave Request ', '' . $login_user[0]['employee_name']. ' applied for leave request as ('. $leave_name[0]['leave_name'] .') for the date of '. $request->post('leave_date').'. Please click here to view.','employees-leaves?employee_id='.$login_user[0]['employee_id'].'&date='.$request->post('leave_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                                }
                                
                                return $this->api_response_json(TRUE,"Leave request has been submitted. Please wait for response","",200);

                            }else{

                                return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);

                            }
                        }else{

                            $message = 'You have already submitted a leave request for this date '.$leave_date.'.';
                            return $this->api_response_json(FALSE,$message,"",200);

                        }
                    }else{
                        $message = 'You can\'t apply '.$leave_name[0]['leave_name'].' at the moment.';
                        return $this->api_response_json(FALSE,$message,"",200);
                    }
                }else{
                   
                    $message = 'You can\'t apply this leave at the moment.';
                    return $this->api_response_json(FALSE,$message,"",200);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Add New Leave Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('Create an Leave Request API',  $request->header('Bearer-Token'));

        }
		
		
    }

    protected function update_leave_request(Request $request){
       

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            
            if($this->check_request_validation($request->post('leave_id'),2,$login_user[0]['employee_id']) == TRUE){
                
                return $this->api_response_json(FALSE,"Leave request id is invalid.","",200);

            }

            $filename = '';

            if($request->hasFile('leave_attachment')){
                $filename = $request->file('leave_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->post(), [
                'leave_date' => 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')). '' ,
                'edit_leave_type' => 'required',
                'edit_leave_reason' => 'required',
                'leave_applied_attachment' => ['max:10000', ($filename == 'txt') ? 
                File::types(['jpg','png','jpeg','docx','pdf','txt'])
                :
                File::types(['jpg','png','jpeg','docx','pdf'])],
            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else{

                $reamining_leaves;
                $no_of_days = 1;

                if($request->post('leave_date') < $login_user[0]['employee_doj']){

                    return $this->api_response_json(FALSE,"You can't edit leave before your joining date.","",200);
                }

                $leave_name = LeaveManagement::where('leave_manage_id',$request->post('edit_leave_type'))->get(['leave_manage_id','leave_name','leave_visibilty'])->toArray();

                if(!empty($leave_name)){
                
                    $leave_alloted = Leave::where('leaves_employee_id',$login_user[0]['employee_id'])->where('leave_type_id',$request->post('edit_leave_type'))->get(['leave_allotement'])->toArray();
    
                    if($leave_name[0]['leave_visibilty'] == 1){
                        
                        if(empty($leave_alloted)){
                            $leave_alloted[0]['leave_allotement'] = 0;
                        }
    
                        $leave_request_applied = LeaveRequest::where('leave_applied_by',$login_user[0]['employee_id'])
                            ->where('leave_applied_type',$request->post('edit_leave_type'))
                            ->where('leave_applied_id','!=',$request->leave_id)
                            ->where('leave_approved_by_hr',1)
                            ->count();
    
                        $get_availed_leaves = Attendance::where('att_employee_id',$login_user[0]['employee_id'])->where('att_check_overall_status', $leave_name[0]['leave_name'])->count();
                
                        $total_leaves = $get_availed_leaves + $leave_request_applied;
                        
                        $reamining_leaves =  $leave_alloted[0]['leave_allotement'] - $total_leaves;
    
    
                        if(!empty($leave_alloted) && $reamining_leaves < $no_of_days){
                            if($reamining_leaves < 0){
                                $reamining_leaves = 0;
                            }
        
                            $message = 'You have only ' . $reamining_leaves . ' ' . $leave_name[0]['leave_name']. '(s) reamining to apply';
                            return $this->api_response_json(FALSE,$message,"",200);
        
                        }
                
                        $leave_update_request = LeaveRequest::find($request->post('leave_id'));
        
                        if(!empty($leave_update_request)){
                            
                            if($request->post('edit_leave_type') == "3"){
                                $employement_status_check = $this->check_if_employee_permanent($request->post('leave_date'),$login_user[0]['employee_id'],$custom_message = NULL);

                                if(!$employement_status_check['status']){
                                    return response()->json([
                                        'message' => $employement_status_check['msg'],
                                        'status' => 'FALSE'
                                    ]);
                                }
                            }
                            $payroll_exists = $this->check_if_payroll_exist($request->post('leave_date'),$login_user[0]['employee_id'],$custom_message = NULL);
                            
                            if($payroll_exists['status'] == TRUE){
        
                                return $this->api_response_json(FALSE,$payroll_exists['msg'],"",200);
        
                            }

                            $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request->post('leave_date'));

                            if($check_attendance_sheet_availability['status'] == FALSE){
                                return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],'',200);
                            }
        
                            $checking_request_responsed = RequestApproval::where('request_module_id',2)->where('request_id',$request->post('leave_id'))->get()->toArray();
                        
                            if(empty($checking_request_responsed) && $leave_update_request->leave_approved_by_hr == 1){
        
                                $checking_request =  $checking_request_dates = LeaveRequest::where('leave_applied_by', $login_user[0]['employee_id'])->where('leave_applied_for_date',date('Y-m-d',strtotime($request->post('leave_date'))))->where('leave_applied_id','!=',$request->post('leave_id'))->get(['leave_applied_for_date'])->toArray();
                                
                                if(empty($checking_request)){
                                
                                    $leave_update_request->leave_applied_for_date = date('Y-m-d',strtotime($request->post('leave_date')));
                                    $leave_update_request->leave_applied_reason = $request->post('edit_leave_reason');
                                    $leave_update_request->leave_applied_type = $request->post('edit_leave_type');
                                    $leave_update_request->leave_applied_date = date('d-m-Y H:i:s');
        
                                    if($request->hasFile('leave_applied_attachment')){
        
                                        if(!empty($leave_update_request->leave_applied_attachment)){
        
                                            if (file_exists(storage_path('app/portal_documents/leave_attachment/'.$leave_update_request->leave_applied_attachment))) {
                            
                                                unlink(storage_path('app/portal_documents/leave_attachment/'.$leave_update_request->leave_applied_attachment));
                                            }
                                        }   
                    
                                        $fileNameToStore = $request->file('leave_applied_attachment')->getClientOriginalName();
                                        
                                        $request->file('leave_applied_attachment')->storeAs('portal_documents/leave_attachment/',$fileNameToStore);
                            
                                        $leave_update_request->leave_applied_attachment = $fileNameToStore;
                            
                                    }
                                
                                    $process = $leave_update_request->save();
        
                                    if( $process == 1){
                                        if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                            $to_mngr1 = $login_user[0]['employee_senior_manager'];
                                        }
                                        if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                            $to_mngr2 = $login_user[0]['employee_junior_manager'];
                                        }
                        
                                        $this->send_notification_api('Update Leave Request ', '' . $login_user[0]['employee_name']. '  update an leave request as ('. $leave_name[0]['leave_name'] .') for the date of '. $request->post('leave_date').' Please click here to view.','employees-leaves?employee_id='.$login_user[0]['employee_id'].'&date='.$request->post('leave_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);
                                    
                                        
                                        return $this->api_response_json(TRUE,'Leave request has been updated. Please wait for response',"",200);
        
                                    }else{
        
                                        return $this->api_response_json(FALSE,'Something went wrong. Please try again.',"",200);
        
                                    }
                                    
                                }else{
        
                                    $message = 'You have already submitted a request for this date '.$request->post('leave_date').'.';
                                    return $this->api_response_json(FALSE,$message,"",200);
        
                                }
                            }else{
        
                                return $this->api_response_json(FALSE,'You can\'t edit this request now because your reporting authority has changed its status.',"",200);
        
                            }
                        }else{
        
                            return $this->api_response_json(FALSE,'Request not found on this ID.',"",200);
                        } 
                    }else{
                        $message = 'You can\'t apply '.$leave_name[0]['leave_name'].' at the moment.';
                        return $this->api_response_json(FALSE,$message,"",200);
                    }
                }else{
                    $message = 'You can\'t apply this leave at the moment.';
                    return $this->api_response_json(FALSE,$message,"",200);
                }
            }           
        }catch(\Exception $e){

            $this->create_error_log_from_api('Update Leave Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
            
        }

        finally{

            ActivityTracker::track_activity_from_api('Update Leave Request API',  $request->header('Bearer-Token'));

            
        }
    }

    protected function delete_leave_request(Request $request){
      
        try{

            $validator = Validator::make($request->post(), [
                'leave_id' => 'required',
            ]);

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
           
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }
            if($this->check_request_validation($request->post('leave_id'),2,$login_user[0]['employee_id']) == TRUE){
                return $this->api_response_json(FALSE,"Leave request id is invalid.","",200);
            }
            $leave_delete_request = LeaveRequest::find($request->post('leave_id'));

            if(!empty($leave_delete_request)){
                    
                $checking_request_responsed = RequestApproval::where('request_module_id',2)->where('request_id',$request->post('leave_id'))->get()->toArray();
            
                if(empty($checking_request_responsed) && $leave_delete_request->leave_approved_by_hr == 1){

                    if(!empty($leave_delete_request->leave_applied_attachment)){

                        if (file_exists(storage_path('app/portal_documents/leave_attachment/'.$leave_delete_request ->leave_applied_attachment))) {
                    
                            unlink(storage_path('app/portal_documents/leave_attachment/'.$leave_delete_request ->leave_applied_attachment));
                        }
                    }

                    $process = LeaveRequest::where('leave_applied_id', $request->post('leave_id'))->delete();

                    if( $process == 1){
                        
                        return $this->api_response_json(TRUE,"Leave request has been deleted.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                    } 
                        
                }else{

                    return $this->api_response_json(FALSE,"'You can\'t delete this request now because your reporting authority has changed its status.","",200);

                }
            }else{

                return $this->api_response_json(FALSE,"'Request not found on this ID.","",200);

            }          

        }catch(\Exception $e){

            $this->create_error_log_from_api('Delete Leave Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
            
        }

        finally{

            ActivityTracker::track_activity_from_api('Delete Leave Request API',  $request->header('Bearer-Token'));

        }
    }

    protected function download_leave_file(Request $request){
       
        try{
            if(!empty($request->get('path'))){
                
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->get('path')));
               
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->get('path')));
                }else{
                    return $this->api_response_json(FALSE,"File Not Found.","",200);
                }
                return response()->download(storage_path('app/portal_documents/'.$request->get('path')));

            }else{

                return $this->api_response_json(FALSE,"Path Not Found.","",200);
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('download-leave-file Api', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
            
        }finally{

            ActivityTracker::track_activity_from_api('download-leave-file Api',  $request->header('Bearer-Token'));

        }
    }

    protected function count_days($from_date, $to_date){

        $from_date = strtotime($from_date);
        $to_date = strtotime($to_date);
        
        $timeDiff = abs($to_date - $from_date);
        
        $numberDays = $timeDiff/86400;
        
        $no_of_days = intval($numberDays + 1);
        
        return $no_of_days;
    }
}
