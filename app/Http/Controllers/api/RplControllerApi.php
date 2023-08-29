<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee,ApiResponseTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\Validator;
use App\Models\{Attendance,RplLeave,RequestApproval,OfficeLocation,Employees};

class RplControllerApi extends Controller
{
    use LoginEmployee, LogGenerateTrait ,NotificationTrait,AttendanceEmployee,ApiResponseTrait;

    const MY_MAIN_TITLE = "my-rpl-leaves";

   
    protected function my_rpl_leave(Request $request){
        try{
            $title = Self::MY_MAIN_TITLE;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
          
            $my_rpl_leaves = RplLeave::where('rpl_leave_applied_by',$login_user[0]['employee_id'])->get()->toArray();

            $rpl_leave_request_id = array_column($my_rpl_leaves,'rpl_leave_id');

            $responsed_request = RequestApproval::where('request_module_id',7)->whereIn('request_id',$rpl_leave_request_id)->pluck('request_id')->toArray();

            $data = compact('title','my_rpl_leaves','responsed_request');

            return $this->api_response_json(TRUE,"My Rpl Leave Request ",$data,200);
            
           
        }catch(\Exception $e){
         
            $this->create_error_log_from_api('My RPL Leave Module Access Api', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
           
        }finally{
            ActivityTracker::track_activity_from_api('My RPL Leave Module Access Api',  $request->header('Bearer-Token'));

        }
    }

    protected function apply_rpl_leave_request(Request $request){
        try{

           
            $validator = Validator::make($request->post(), [
            
                'rpl_leave_worked' => 'required|date_format:Y-m-d|before_or_equal:'. date('Y-m-d') ,
                'rpl_leave_required' => 'required|date_format:Y-m-d|after:rpl_leave_worked',
                'leave_reason' => 'required',
            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {
             
                $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

                $rpl_worked_date  = $request->post('rpl_leave_worked');
			    $doj = strtotime($login_user[0]['employee_doj']);

			    if($doj <= $rpl_worked_date){

                    $payroll_exists = $this->check_if_payroll_exist($request->post('rpl_leave_required'),$login_user[0]['employee_id'],$custom_message = NULL);

                    if($payroll_exists['status'] == TRUE){
                         return $this->api_response_json(FALSE,$payroll_exists['msg'],"",200);
                    }

                    $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request->post('rpl_leave_required'));

                    if($check_attendance_sheet_availability['status'] == FALSE){
                        return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],'',200);
                    }
                    
                    $checking_rpl_leave_exits = RplLeave::where('rpl_leave_applied_by',$login_user[0]['employee_id'])->where(function($query) use($request){
                        $query->where('rpl_leave_worked_on_date', $request->post('rpl_leave_worked'))->orWhere('rpl_leave_required_for_date',$request->post('rpl_leave_required'));
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
                            $add_rpl_leave->rpl_leave_required_for_date = $request->post('rpl_leave_required');
                            $add_rpl_leave->rpl_leave_applied_reason = $request->post('leave_reason');
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

                               
                                $this->send_notification_api('New RPL Leave Request ', '' . $login_user[0]['employee_name']. ' applied for rpl leave request for the date of '. $request->post('rpl_leave_required').' Please click here to view.','rpl-leaves?employee_id='.$login_user[0]['employee_id'].'&date='.$request->post('rpl_leave_required').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                                return $this->api_response_json(TRUE,"RPL leave request has been submitted. Please wait for response .","",200);

                            }else{
                                return $this->api_response_json(FALSE,"RPL leave request has not been submitted. Please try again .","",200);

                            }    
                        }else{

                            return $this->api_response_json(FALSE,"You have not marked attendance on this date ".$rpl_worked_date,"",200);
                        }

                    }else{
                        $rpl_worked_datee = '';
                        $rpl_leave_required = '';
                        $to =  '';
                        if($rpl_worked_date == $checking_rpl_leave_exits[0]['rpl_leave_worked_on_date'] ){
                            $to = 'to';
                            $rpl_worked_datee = $checking_rpl_leave_exits[0]['rpl_leave_worked_on_date'];
                        }
                        if($request->post('rpl_leave_required') == $checking_rpl_leave_exits[0]['rpl_leave_required_for_date'] ){
                            $rpl_leave_required = $checking_rpl_leave_exits[0]['rpl_leave_required_for_date'];
                        }else{
                            $to = '';
                        }

                        return $this->api_response_json(FALSE,"You have already applied rpl leave request on this date ".$rpl_worked_datee.' '.$to.' '.$rpl_leave_required,"",200);

                    }
                }else{
                    return $this->api_response_json(FALSE,"You can't apply rpl request before your joining date","",200);

                }
              
            }   

        }catch(\Exception $e){
            $this->create_error_log_from_api('Apply an RPL Leave Request Api', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{
            ActivityTracker::track_activity_from_api('Apply an RPL Leave Request Api',  $request->header('Bearer-Token'));

        }
    }
   
    protected function update_rpl_leave_request(Request $request){

        try{


            $validator = Validator::make($request->post(), [
            
                'rpl_leave_worked' => 'required|date_format:Y-m-d|before_or_equal:'. date('Y-m-d') ,
                'rpl_leave_required' => 'required|date_format:Y-m-d|after:rpl_leave_worked',
                'leave_reason' => 'required',
                'rpl_leave_id' => 'required|numeric',
            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {
                $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
       
                if($this->check_request_validation($request->rpl_leave_id,7,$login_user[0]['employee_id']) == TRUE){
                    dd($request->rpl_leave_id);
                    return $this->api_response_json(FALSE,"Rpl Leave request id is invalid","",200);
                 
                }
                $leave_update_request = RplLeave::find($request->rpl_leave_id);
                
                if(!empty($leave_update_request)){
                    $rpl_worked_date  = $request->post('rpl_leave_worked');
                    $doj = strtotime($login_user[0]['employee_doj']);
    
                    if($doj <= $rpl_worked_date){
    
                        $payroll_exists = $this->check_if_payroll_exist($request->post('rpl_leave_required'),$login_user[0]['employee_id'],$custom_message = NULL);
            
                        if($payroll_exists['status'] == TRUE){
                            return $this->api_response_json(FALSE,$payroll_exists['msg'],"",200);
                          
                        }
    
                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($rpl_worked_date);
    
                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],"",200);
                        }
                        
                        $checking_request_responsed = RequestApproval::where('request_module_id',7)->where('request_id',$request->rpl_leave_id)->get()->toArray();
         
                        if(empty($checking_request_responsed) && $leave_update_request->rpl_leave_approved_by_hr == 1){
                            
                            $checking_rpl_leave_exits = RplLeave::where('rpl_leave_applied_by',$login_user[0]['employee_id'])->where(function($query) use($request){
                                $query->where('rpl_leave_worked_on_date', $request->post('rpl_leave_worked'))->orWhere('rpl_leave_required_for_date',$request->post('rpl_leave_required'));
                            })->where('rpl_leave_id','!=',$request->rpl_leave_id)->get(['rpl_leave_required_for_date','rpl_leave_worked_on_date'])->toArray();
                            
                            if(empty($checking_rpl_leave_exits)){
                                $date = date("d", strtotime($rpl_worked_date));
                                $month = date("m", strtotime($rpl_worked_date));
                                $year = date("Y", strtotime($rpl_worked_date));
                            
                                $get_attendance = Attendance::where('att_employee_id',$login_user[0]['employee_id'])
                                ->where('att_for_date',$date)->where('att_for_month',$month)->where('att_for_year',$year)->get('attendance_id')->toArray();
        
                                if(!empty($get_attendance)){
                                
                                    $leave_update_request->rpl_leave_applied_date = date('Y-m-d');
                                    $leave_update_request->rpl_leave_worked_on_date = $rpl_worked_date;
                                    $leave_update_request->rpl_leave_required_for_date = $request->post('rpl_leave_required');
                                    $leave_update_request->rpl_leave_applied_reason = $request->post('leave_reason');
                                    $leave_update_request->rpl_leave_created_at = date('Y-m-d H:i:s');
                                    $process = $leave_update_request->save();
        
                                    if($process == 1){
                        
                                        if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                                            $to_mngr1 = $login_user[0]['employee_ra'];
                                        }
                                        if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                                            $to_mngr2 = $login_user[0]['employee_ra2'];
                                        }
        
                                        $this->send_notification_api('Update RPL Leave Request ', '' . $login_user[0]['employee_name']. '  update an leave request for the date of '. $request->post('rpl_leave_required').' Please click here to view.','rpl-leaves?employee_id='.$login_user[0]['employee_id'].'&date='.$request->post('rpl_leave_required').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);
        
                                        return $this->api_response_json(TRUE,"RPL leave request has been updated. Please wait for response .","",200);
                                      
                                    }else{
                                        
                                        return $this->api_response_json(FALSE,"RPL leave request has not been updated. Please try again .","",200);
                                      
                                    }    
                                }else{
                                    return $this->api_response_json(FALSE,"You have not marked attendance on this date ".$rpl_worked_date,"",200);
                                   
                                }
        
                            }else{
                                $rpl_worked_datee = '';
                                $rpl_leave_required = '';
                                $to =  '';
                                if($rpl_worked_date == $checking_rpl_leave_exits[0]['rpl_leave_worked_on_date'] ){
                                    $to = 'to';
                                    $rpl_worked_datee = $checking_rpl_leave_exits[0]['rpl_leave_worked_on_date'];
                                }
                                if($request->post('rpl_leave_required') == $checking_rpl_leave_exits[0]['rpl_leave_required_for_date'] ){
                                    $rpl_leave_required = $checking_rpl_leave_exits[0]['rpl_leave_required_for_date'];
                                }else{
                                    $to = '';
                                }

                                return $this->api_response_json(FALSE,"You have already applied rpl leave request on this date ".$rpl_worked_datee.' '.$to.' '.$rpl_leave_required,"",200);
                               
                            }
                        }else{
                            return $this->api_response_json(FALSE,"You can\'t edit this request now because your reporting authority has changed its status.","",200);
                        }
                    }else{
                        return $this->api_response_json(FALSE,"You can't apply rpl request before your joining date","",200);
                     
                    }
                }else{
                    return $this->api_response_json(FALSE,"Request id not found.","",200);
                }
                
            }        
        }catch(\Exception $e){
            $this->create_error_log_from_api('Update RPL Leave Request Api', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{
            ActivityTracker::track_activity_from_api('Update RPL Leave Request Api',  $request->header('Bearer-Token'));

        }
       
    }

    protected function delete_rpl_leave_request(Request $request){
      
        try{
            $validator = Validator::make($request->post(), [
                'rpl_leave_id' => 'required',
            ]);

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
           
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }

            if($this->check_request_validation($request->post('rpl_leave_id'),7,$login_user[0]['employee_id']) == TRUE){
                return $this->api_response_json(FALSE,"RPL Leave request id is invalid.","",200);
            }
            $leave_delete_request = RplLeave::find($request->rpl_leave_id);

            if(!empty($leave_delete_request)){
                    
                $checking_request_responsed = RequestApproval::where('request_module_id',7)->where('request_id',$request->rpl_leave_id)->get()->toArray();
               
                if(empty($checking_request_responsed) && $leave_delete_request->rpl_leave_approved_by_hr == 1){

                    $process = RplLeave::where('rpl_leave_id', $request->rpl_leave_id)->delete();

                    if( $process == 1){
                        return $this->api_response_json(TRUE,"RPL leave request has been deleted.","",200);
                        
                    }else{
                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                     
                    } 
                        
                }else{
                    return $this->api_response_json(FALSE,'You can\'t delete this request now because your reporting authority has changed its status.',"",200);
                  
                }
            }else{
                return $this->api_response_json(FALSE,'Request not found on this id.',"",200);
               
            }     

        }catch(\Exception $e){
            $this->create_error_log_from_api('Delete RPL Leave Request Api', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{
            ActivityTracker::track_activity_from_api('Delete RPL Leave Request Api',  $request->header('Bearer-Token'));

        }
    }

}
