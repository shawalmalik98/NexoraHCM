<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{WFH, Employees, Department, RequestApproval};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait, MailTrait};
use App\Helpers\ActivityTracker;

class WFHApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait ,NotificationTrait, ApiResponseTrait, MailTrait;

    const TITLE_GLOBAL = "wfh-request";
    const TITLE_GLOBAL2 = "my-wfh-requests";

    protected function index(Request $request){

        try{
            $title = Self::TITLE_GLOBAL;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $departments = Department::get(['department_id','department_name'])->toArray();

            if($request->get('emp_status') == "2"){
                $status = 0;
            }else{
                $status = 1;
            }
            $employees_data = Employees::where('employee_isactive','=',$status)->get(['employee_id','employee_code','employee_name'])->toArray();

            $all_requests = array();
            $responsed_request = array();

            if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1){

                $limit_query = '300';
                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                if(!empty($request->emp_status) || !empty($request->emp_name) || !empty($request->request_for_date)  || !empty($request->request_submited_date) || !empty($request->req_status)){
                    $limit_query = '';
                    if(!empty($request->emp_name)){
                        $where_clause .= ' AND wfh_employee_id = "'.$request->emp_name.'"';
                    }
                    if(!empty($request->request_for_date)){
                        $where_clause .= ' AND wfh_request_date = "'.$request->request_for_date.'"';
                    }
                    if(!empty($request->request_submited_date)){
                        $submition_date = date("d-m-Y", strtotime($request->request_submited_date));
                        $where_clause .= ' AND wfh_request_submition_date LIKE "%'.$submition_date.'%"';
                    }
                    if(!empty($request->req_status)){
                        $where_clause .= ' AND wfh_request_approved_by_hr = "'.$request->req_status.'"';
                    }
                }

                $all_requests = WFH::join('tbl_employees', 'tbl_work_from_homes.wfh_employee_id', '=', 'tbl_employees.employee_id')
                ->whereRaw($where_clause)
                ->orderBy('wfh_request_id', 'DESC')
                ->get(['wfh_request_id','wfh_employee_id','employee_name', 'wfh_request_date','wfh_request_message','wfh_request_approved_by_hr','wfh_request_approved_date_hr','wfh_request_submition_date'])->toArray();
            }

            elseif($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] !== 1 && $login_user[0]['employee_can_ra'] == 1){

                $login_employee_id = $login_user[0]['employee_id'];

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id,  $status);

                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->get(['employee_id','employee_code','employee_name'])->toArray();

                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';

                if(!empty($request->emp_status) || !empty($request->emp_name) || !empty($request->req_status)){

                    if(!empty($request->emp_name)){
                        $where_clause .= ' AND wfh_employee_id = "'.$request->emp_name.'"';
                    }
                    if(!empty($request->req_status)){
                        $where_clause .= ' AND wfh_request_approved_by_hr = "'.$request->req_status.'"';
                    }
                }

                $all_requests = WFH::join('tbl_employees', 'tbl_work_from_homes.wfh_employee_id', '=', 'tbl_employees.employee_id')->whereIn('wfh_employee_id',$total_team_member)->whereRaw($where_clause)->orderBy('wfh_request_id','DESC')->get(['wfh_request_id','wfh_employee_id','wfh_request_date','wfh_request_message','wfh_request_approved_by_hr','wfh_request_approved_date_hr','wfh_request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_ra','employee_ra2'])->toArray();

                $responsed_request = RequestApproval::where('request_module_id', 3)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();

                if(empty($all_requests)){

                    $all_requests = array();

                }

            }

           $data = compact('title', 'all_requests','employees_data','departments','responsed_request');

           return $this->api_response_json(TRUE,"View All Remote Work Request",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View All Remote Work Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }

        finally{

            ActivityTracker::track_activity_from_api('View All Remote Work Request API',  $request->header('Bearer-Token'));
        }


    }

    protected function my_wfh_requests(Request $request){

        try{
            $title = Self::TITLE_GLOBAL2;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $my_requests = WFH::where('wfh_employee_id' , $login_user[0]['employee_id'])->orderBy('wfh_request_id', 'DESC')->get()->toArray();

            $wfh_request_id = array_column($my_requests,'wfh_request_id');

            $responsed_request = RequestApproval::where('request_module_id',3)->whereIn('request_id', $wfh_request_id)->pluck('request_id')->toArray();

            $data = compact('title','my_requests','responsed_request');

            return $this->api_response_json(TRUE,"My Remote Work Request ",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View My Remote Work Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{
            ActivityTracker::track_activity_from_api('View My Remote Work Request API',  $request->header('Bearer-Token'));
        }
    }


    protected function requestfor_wfh(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                'days_assign' => 'required|in:single,multiple',
                'from_date' => ($request->post('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')).'|after:yesterday ': '' ,
                'to_date' => ($request->post('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|after:from_date' : '' ,
                'request_date' => ($request->post('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')).'|after:yesterday ': '' ,
                'home_work_reason' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);

                }
            }else {

                if($request->post('days_assign') == 'multiple'){
                    $date = $request->post('from_date').'  '.$request->post('to_date');

                    if($request->post('from_date') < $login_user[0]['employee_doj']){

                        return $this->api_response_json(FALSE,"You can't apply remote work request before your joining date.","",200);
                    }

                    $no_of_days = $this->count_days($request->post('from_date'),$request->post('to_date'));

                    if(!empty($no_of_days) &&  $no_of_days >= 30){
                        return $this->api_response_json(FALSE,"You can't apply request more than 30 at a time.","",200);

                    }
                    for($i = 0; $i<$no_of_days; $i++){

                        $request_date = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request->post('from_date'))));

                        $checking_request = WFH::where('wfh_employee_id', $login_user[0]['employee_id'])->whereIn('wfh_request_date',[$request_date])->get(['wfh_request_date'])->toArray();

                        foreach($checking_request as $request_date){

                            $request_apply_date[] =  $request_date['wfh_request_date'];
                        }

                    }

                    if(!empty($checking_request)){

                        $date = implode(' , ',$request_apply_date);
                        return $this->api_response_json(FALSE,"You've already applied requests for these dates ".$date,"",200);

                    }

                }else{

                    if($request->post('request_date') < $login_user[0]['employee_doj']){

                        return $this->api_response_json(FALSE,"You can't apply remote work request before your joining date.","",200);
                    }

                    $date = $request->post('request_date');
                    $checking_request = WFH::where('wfh_employee_id', $login_user[0]['employee_id'])->where('wfh_request_date', $request->post('request_date'))->where('wfh_request_approved_by_hr', '!=', 3)->get()->toArray();

                }

                $checking_request = [];
                
                if($request->post('days_assign') == 'single'){
                    $checking_request = WFH::where('wfh_employee_id', $login_user[0]['employee_id'])->where('wfh_request_date',$request->post('request_date'))->get()->toArray();
                }

                if(empty($checking_request)){

                    if($request->post('days_assign') == 'multiple'){

                        $no_of_days =  $this->count_days($request->post('from_date'),$request->post('to_date'));

                        for($i = 0; $i<$no_of_days; $i++){
                            $wfh_request = new WFH();

                            $dates =  date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request->post('from_date'))));

                            $checking_request_dates = WFH::where('wfh_employee_id', $login_user[0]['employee_id'])->where('wfh_request_date',$dates)->get(['wfh_request_date'])->toArray();

                            if(empty($checking_request_dates)){

                                $wfh_request->wfh_employee_id = $login_user[0]['employee_id'];
                                $wfh_request->wfh_request_date = $dates;
                                $wfh_request->wfh_request_message = $request->post('home_work_reason');
                                $wfh_request->wfh_request_approved_by_hr = '1';
                                $wfh_request->wfh_request_submition_date = date('d-m-Y H:i:s');
                                $process = $wfh_request->save();

                                $month = date('m', strtotime($dates));
                                $default_approved_wfh = WFH::where('wfh_employee_id', $login_user[0]['employee_id'])->whereMonth('wfh_request_date',$month)->count();
                                
                                if($default_approved_wfh == 1){
        
                                    $wfh_request =  WFH::find($wfh_request->wfh_request_id);
                                    $wfh_request->wfh_request_approved_by_hr = '2';
                                    $wfh_request->wfh_request_approved_date_hr = 'Auto approved by system';
                                    $process = $wfh_request->save();  
                                }
                            }
                        }

                    }else{
                        $wfh_request = new WFH();
                        $wfh_request->wfh_employee_id = $login_user[0]['employee_id'];
                        $wfh_request->wfh_request_date = date('Y-m-d',strtotime($request->post('request_date')));
                        $wfh_request->wfh_request_message = $request->post('home_work_reason');
                        $wfh_request->wfh_request_approved_by_hr = '1';
                        $wfh_request->wfh_request_submition_date = date('d-m-Y H:i:s');
                        $process = $wfh_request->save();

                        $month = date('m', strtotime($request->post('request_date')));
                        $default_approved_wfh = WFH::where('wfh_employee_id', $login_user[0]['employee_id'])->whereMonth('wfh_request_date',$month)->count();
                        
                        if($default_approved_wfh == 1){

                            $wfh_request =  WFH::find($wfh_request->wfh_request_id);
                            $wfh_request->wfh_request_approved_by_hr = '2';
                            $wfh_request->wfh_request_approved_date_hr = 'Auto approved by system';
                            $process = $wfh_request->save();  
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
                          
                            $this->send_notification_api('New Remote Work Request',''.$login_user[0]['employee_name'].' applied for remote work request(s) from '.$request->post('from_date'). ' to '.$request->post('to_date').'. Please click here to view.','wfh-requests?employee_id='. $login_user[0]['employee_id'].'&from_date='.$request->post('from_date'). '&to_date='.$request->post('to_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                            $this->send_module_email(5,'New Remote Work Requests','<b>'.$login_user[0]['employee_name']. '</b> applied for remote work request(s) from <b>'.$request->post('from_date'). '</b> to <b>'.$request->post('to_date').'</b>.',''.url('/').'/wfh-requests');
                            

                        }else{
                          
                            $this->send_notification_api('New Remote Work Request',''.$login_user[0]['employee_name'].' applied for remote work request for the date of '.$request->post('request_date').' Please click here to view.','wfh-requests?employee_id='. $login_user[0]['employee_id'].'&date='.    $request->post('request_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                            $this->send_module_email(5,'New Remote Work Request','<b>'.$login_user[0]['employee_name']. '</b> applied for remote work request for the date of <b>'.$request->post('request_date'). '</b>.',''.url('/').'/wfh-requests');
                            
                        }

                        return $this->api_response_json(TRUE,"Remote Work Request has been submitted. Please wait for response.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Remote Work Request Failed. Please try again after some time.","",200);
                    }
                }else{
                    
                    $message = "You have already submitted a request for ".$request->post('request_date');
                    return $this->api_response_json(FALSE,$message,"",200);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Apply Remote Work Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Apply Remote Work Request API',  $request->header('Bearer-Token'));
        }
    }

    protected function update_wfh_request(Request $request){


        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('request_id'),3,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Remote work request id is invalid.","",200);

            }

            $validator = Validator::make($request->post(), [
                'request_date' => 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')).'|after:yesterday ' ,
                'edit_home_work_reason' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);

                }
            }else {

                $wfh_update_request = WFH::find($request->post('request_id'));

                if(!empty($wfh_update_request)){

                    $checking_request_responsed = RequestApproval::where('request_module_id',3)->where('request_id',$request->post('request_id'))->get()->toArray();

                    if(empty($checking_request_responsed) && $wfh_update_request->wfh_request_approved_by_hr == 1){

                        $checking_request = WFH::where('wfh_employee_id', $login_user[0]['employee_id'])->where('wfh_request_date',$request->post('request_date'))->where('wfh_request_id','!=',$request->post('request_id'))->get()->toArray();

                        if(empty($checking_request)){

                            $wfh_update_request->wfh_request_date = date('Y-m-d',strtotime($request->post('request_date')));
                            $wfh_update_request->wfh_request_message = $request->post('edit_home_work_reason');
                            $wfh_update_request->wfh_request_submition_date = date('d-m-Y H:i:s');

                            $process = $wfh_update_request->save();

                            if($process == 1){
                                if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                                    $to_mngr1  = $login_user[0]['employee_ra'];
                                }
                                if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                                    $to_mngr2 = $login_user[0]['employee_ra2'];
                                }

                                $this->send_notification_api('Update Remote Work Request',''.$login_user[0]['employee_name'].' update a remote work request. for the date of '.$request->post('request_date').' Please click here to view.','wfh-requests?employee_id='. $login_user[0]['employee_id'].'&date='.$request->post('request_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                                return $this->api_response_json(TRUE,"Remote Work Request has been updated. Please wait for response.","",200);

                            }else{

                                return $this->api_response_json(FALSE,"Remote Work Request Failed. Please try again after some time.","",200);

                            }

                        }else{

                            $message = "You have already submitted a request for ".$request->post('request_date');
                            return $this->api_response_json(FALSE,$message,"",200);
                        }
                    }else{

                        return $this->api_response_json(FALSE,"You can't edit this request now because your reporting authority has changed its status.","",200);
                    }
                }else{

                    return $this->api_response_json(FALSE,"Request not found on this ID.","",200);

                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Update Remote Work Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }

        finally{

            ActivityTracker::track_activity_from_api('Update Remote Work Request API',  $request->header('Bearer-Token'));

        }

    }

    protected function delete_wfh_request(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('wfh_id'),3,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Remote work request id is invalid.","",200);

            }

            $validator = Validator::make($request->post(), [
                'wfh_id' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);

                }
            }else{

                $wfh_delete_request = WFH::find($request->post('wfh_id'));

                if(!empty($wfh_delete_request)){

                    $checking_request_responsed = RequestApproval::where('request_module_id',3)->where('request_id',$request->post('wfh_id'))->get()->toArray();

                    if(empty($checking_request_responsed) && $wfh_delete_request->wfh_request_approved_by_hr == 1){

                        $process = WFH::where('wfh_request_id', $request->post('wfh_id'))->delete();
                        if( $process == 1){

                            return $this->api_response_json(TRUE,"Remote Work Request has been deleted.","",200);

                        }else{

                            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                        }

                    }else{
                        return $this->api_response_json(FALSE,"You can't delete this request now because your reporting authority has changed its status.","",200);

                    }
                }else{

                    return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Delete Remote Work Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }

        finally{

            ActivityTracker::track_activity_from_api('Delete Remote Work Request API',  $request->header('Bearer-Token'));

        }
    }

    protected function approve_request(Request $request){
		try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            $get_wfh = WFH::find($request->post('wfh_request_id'));

            if(!empty($get_wfh)){

                $get_wfh->wfh_request_approved_by_hr = 2;
                $get_wfh->wfh_request_approved_date_hr = 'Approved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                $process =  $get_wfh->save();

                $day = date("d", strtotime($get_wfh->wfh_request_date));

                $month = date("m", strtotime($get_wfh->wfh_request_date));

                $year = date("Y", strtotime($get_wfh->wfh_request_date));

                if($process == 1){

                    $this->send_notification_api('Work From Home Request Approval', 'HR approved your work from home request for the date of '. $day . '-'. $month .'-'.$year.'. Please click here to view.','my-wfh-requests?request-id='. $get_wfh->wfh_request_id .'',$get_wfh->wfh_employee_id,NULL,NULL,'no', $login_user[0]['employee_id']);

                    return $this->api_response_json(TRUE,"Work From Home request has been Approved.","",200);

                }else{

                    return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                }
            }else{

                return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Access Approve Work From Home Request by HR Module API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Access Approve Work From Home Request by HR Module API',  $request->header('Bearer-Token'));
        }
    }

    protected function disapprove_request(Request $request){

        try{
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $get_wfh = WFH::find($request->post('wfh_request_id'));

            if(!empty($get_wfh)){

                $get_wfh->wfh_request_approved_by_hr = 3;
                $get_wfh->wfh_request_approved_date_hr = 'Dis-Approved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                $process =  $get_wfh->save();

                if($process == 1){

                    $this->send_notification_api('Work From Home Request Approval',  'HR declined your attendance request for the date of '. $get_wfh->wfh_request_date . '. Please click here to view.','my-wfh-requests?request-id='. $get_wfh->wfh_request_id .'',$get_wfh->wfh_employee_id,NULL,NULL,'no', $login_user[0]['employee_id']);

                    return $this->api_response_json(TRUE,"Work From Home request has been Dis-Approved.","",200);

                }else{

                    return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                }
            }else{

                return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Access Dis-Approve Work From Home Request by HR Module API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);


        }finally{

            ActivityTracker::track_activity_from_api('Access Dis-Approve Work From Home Request by HR Module API',  $request->header('Bearer-Token'));

        }

    }


    protected function wfh_today(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
           
            $get_wfh_request_today = WFH::select('*')
                ->where('wfh_employee_id','=',$login_user[0]['employee_id'])
                ->where('wfh_request_date','=',date('Y-m-d'))
                ->get()->toArray();
            
            if(!empty($get_wfh_request_today) && $get_wfh_request_today[0]['wfh_request_approved_by_hr'] != 2){

                $get_wfh_request_today = RequestApproval::where('request_module_id',3)
                ->where('request_id',$get_wfh_request_today[0]['wfh_request_id'])
                ->where('request_approver_status',2)
                ->get()->toArray();

            }
           
            if(!empty($get_wfh_request_today)){

                return $this->api_response_json(TRUE,"","",200);

            }else{

                return $this->api_response_json(FALSE,"","",200);

            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Today WFH API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Today WFH API',  $request->header('Bearer-Token'));

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
