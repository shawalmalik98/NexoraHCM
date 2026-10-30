<?php

namespace App\Http\Controllers\web;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{WFH, Employees, Department, RequestApproval, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee, MailTrait};
use App\Helpers\ActivityTracker;

class WFHController extends Controller
{
    use LoginEmployee, LogGenerateTrait ,NotificationTrait ,AttendanceEmployee, MailTrait;

    const TITLE_GLOBAL = "wfh-request";
    const TITLE_GLOBAL2 = "my-wfh-requests";

    protected function index(Request $request_data){

        try{
            $title = Self::TITLE_GLOBAL;
      
            $login_user = $this->get_login_user_detail();

            $departments = Department::get(['department_id','department_name'])->toArray();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($request_data->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }
            
            $all_requests = array();
            $responsed_request = array();
            // $dates = array();

            if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null)){
             
                $limit_query = '300';
                $where_clause = "1=1 AND employee_isactive = '".$status."'";
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->request_for_date)  || !empty($request_data->request_submited_date) || !empty($request_data->req_status)){
                    $limit_query = '';
                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request_data->employee_location).'';
                        $where_clause_to   .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request_data->employee_location).''; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND wfh_employee_id = '.\DB::getPdo()->quote((string) $request_data->emp_name).'';
                    }
                    if(!empty($request_data->request_for_date)){
                        $where_clause .= ' AND wfh_request_date = '.\DB::getPdo()->quote((string) $request_data->request_for_date).'';
                    }
                    if(!empty($request_data->request_submited_date)){
                        $submition_date = date("d-m-Y", strtotime($request_data->request_submited_date));
                        $where_clause .= ' AND wfh_request_submition_date LIKE "%'.$submition_date.'%"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND wfh_request_approved_by_hr = '.\DB::getPdo()->quote((string) $request_data->req_status).'';
                    }
                }

                $employees_data = []; // the name filter searches employees through /employee-search

                $search = trim((string) request()->input('search'));
                $pagination = WFH::
                join('tbl_employees', 'tbl_work_from_homes.wfh_employee_id', '=', 'tbl_employees.employee_id')
                ->whereRaw($where_clause)
                ->orderBy('wfh_request_id', 'DESC')
                ->when($search !== '', function($query) use($search){
                        $query->where(function($query) use($search){
                            $query->where('employee_name','like','%'.$search.'%')
                                ->orWhere('employee_code','like','%'.$search.'%');
                        });
                    })
                    ->paginate(CustomHelper::per_page(), ['wfh_request_id','wfh_employee_id','wfh_request_date','wfh_request_message','wfh_request_approved_by_hr','wfh_request_approved_date_hr','wfh_request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_senior_manager','employee_junior_manager'])->withQueryString();
                $all_requests = $pagination->getCollection()->toArray();
                
                $responsed_request = RequestApproval::where('request_module_id', 3)->pluck('request_id')->toArray();
                
            }
    
            elseif(!CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) && !CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) && $login_user[0]['employee_is_manager'] == 1){
              
                $login_employee_id = $login_user[0]['employee_id'];

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id,  $status);    
                
                $where_clause = "1=1 AND employee_isactive = '".$status."'";
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->req_status)){
                    
                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request_data->employee_location).'';
                        $where_clause_to   .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request_data->employee_location).''; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND wfh_employee_id = '.\DB::getPdo()->quote((string) $request_data->emp_name).'';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND wfh_request_approved_by_hr = '.\DB::getPdo()->quote((string) $request_data->req_status).'';
                    }
                }

                $employees_data = []; // the name filter searches employees through /employee-search
               
                $all_requests = WFH::join('tbl_employees', 'tbl_work_from_homes.wfh_employee_id', '=', 'tbl_employees.employee_id')->whereIn('wfh_employee_id',$total_team_member)->whereRaw($where_clause)->orderBy('wfh_request_id','DESC')->get(['wfh_request_id','wfh_employee_id','wfh_request_date','wfh_request_message','wfh_request_approved_by_hr','wfh_request_approved_date_hr','wfh_request_submition_date','employee_id','employee_code','employee_name','employee_department','employee_senior_manager','employee_junior_manager'])->toArray();
            
                $responsed_request = RequestApproval::where('request_module_id', 3)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();
              
                if(empty($all_requests)){
                 
                    $all_requests = array();
                  
                }
                
            }

            if(!empty($request_data->from_date) && !empty($request_data->to_date)){
                $no_of_days = $this->count_days($request_data->from_date,$request_data->to_date);

                for($i = 0; $i<$no_of_days; $i++){
                 
                    $dates[] =  date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request_data->from_date)));
                }
            }else{
                $dates[] = $request_data->date;
            }

           $pagination = $pagination ?? null;
           $send_data = compact('title','login_user','all_requests','employees_data','departments','responsed_request','employee_locations','dates','pagination');

           return view('wfh.all-request', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View All Remote Work Request', $e);
            $send_data = compact('title');
            return view('errors.500');
        }

        finally{
            ActivityTracker::track_activity('Access All Remote Work Request Module');

        }

    }

    protected function all_request_wfh(Request $request_data){
        try{

            $validator = Validator::make($request_data->all(), [
                'request_date' => 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')).'|after:yesterday ',
                'home_work_reason' => 'required',
                'department' => 'required'
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

                if(!empty($request_data->input('employee'))){

                    $employees_data = Employees::where('employee_isactive','=', 1)->whereIn('employee_id', $request_data->input('employee') )->get()->toArray();

                    $process = 1;

                    $wfh_insert = 1;

                    foreach($employees_data as $employees){

                        $message = 'Remote work access given by : '.$login_user[0]['employee_name'].', at : '.date('d M, Y h:i:s A').'';

                        $checking_request = WFH::where('wfh_employee_id', (string) $employees['employee_id'])->where('wfh_request_date',$request_data->input('request_date'))->get()->toArray();
                        if(empty($checking_request)){
                            $wfh_request = new WFH();
                            $wfh_request->wfh_employee_id = $employees['employee_id'];
                            $wfh_request->wfh_request_date = date('Y-m-d',strtotime($request_data->input('request_date')));
                            $wfh_request->wfh_request_message = $request_data->input('home_work_reason');
                            $wfh_request->wfh_request_approved_by_hr =  2;
                            $wfh_request->wfh_request_approved_date_hr = $message;
                            $wfh_request->wfh_request_submition_date = date('d-m-Y H:i:s');
                            $wfh_request->save();

                            $wfh_insert += 1;

                            $this->send_notification('Remote Work Facility','Dear employee, HR gives you a remote work facility for the date of '.date('d F, Y',strtotime($request_data->input('request_date'))).'. Due to some official reason.','my-wfh-requests?request-id='. $wfh_request->wfh_request_id .'',$employees['employee_id'],NULL,NULL,'no');
                        }else{

                            $message = 'Remote work access given by : '.$login_user[0]['employee_name'].', at : '.date('d M, Y h:i:s A').'';
                            
                            $get_wfh = WFH::find($checking_request[0]['wfh_request_id']);  
                          
                            if($get_wfh->wfh_request_approved_by_hr == 1 || $get_wfh->wfh_request_approved_by_hr == 3){

                                $get_wfh->wfh_request_approved_by_hr = 2;
                                $get_wfh->wfh_request_approved_date_hr = $message;
                                $get_wfh->save();

                                $wfh_insert += 1;

                                $this->send_notification('Remote Work Facility','Dear employee, HR gives you a remote work facility for the date of '. date('d F, Y',strtotime($get_wfh['wfh_request_date'])).'. Due to some official reason.','my-wfh-requests?request-id='. $get_wfh->wfh_request_id .'',$get_wfh->wfh_employee_id,NULL,NULL,'no');
                            }
                        }

                    }
                    if($wfh_insert > 1){

                        return response()->json([
                                'status' => 'TRUE',
                                'msg' => 'All remote work request has been submitted. Please wait for response',
                            ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'You have already submitted a request for '.$request_data->input('request_date').'.',
                        ]);
                    }
                }else{
                    
                    $employees_data = Employees::where('employee_isactive','=', 1)->whereIn('employee_department', $request_data->input('department') )->get()->toArray();

                    $process = 1;
                    $wfh_insert = 1; 

                    foreach($employees_data as $employees){

                        $message = 'Remote work access given by : '.$login_user[0]['employee_name'].', at : '.date('d M, Y h:i:s A').'';

                        $checking_request = WFH::where('wfh_employee_id', (string) $employees['employee_id'])->where('wfh_request_date',$request_data->input('request_date'))->get()->toArray();
                        if(empty($checking_request)){
                            $wfh_request = new WFH();
                            $wfh_request->wfh_employee_id = $employees['employee_id'];
                            $wfh_request->wfh_request_date = date('Y-m-d',strtotime($request_data->input('request_date')));
                            $wfh_request->wfh_request_message = $request_data->input('home_work_reason');
                            $wfh_request->wfh_request_approved_by_hr =  2;
                            $wfh_request->wfh_request_approved_date_hr = $message;
                            $wfh_request->wfh_request_submition_date = date('d-m-Y H:i:s');
                            $wfh_request->save();

                            $wfh_insert += 1;

                            $this->send_notification('Remote Work Facility', 'Dear employee, HR gives you a remote work facility for the date of '.date('d F, Y',strtotime($request_data->input('request_date'))).'. Due to some official reason.','my-wfh-requests?request-id='. $wfh_request->wfh_request_id .'',$employees['employee_id'],NULL,NULL,'no');
                        }else{

                            $message = 'Remote work access given by : '.$login_user[0]['employee_name'].', at : '.date('d M, Y h:i:s A').'';
                            
                            $get_wfh = WFH::find($checking_request[0]['wfh_request_id']);  
                            
                            if($get_wfh->wfh_request_approved_by_hr == 1 || $get_wfh->wfh_request_approved_by_hr == 3){

                                $get_wfh->wfh_request_approved_by_hr = 2;
                                $get_wfh->wfh_request_approved_date_hr = $message;
                                $get_wfh->save();

                                $wfh_insert += 1;

                                $this->send_notification('Remote Work Facility', 'Dear employee, HR gives you a remote work facility for the date of '.date('d F, Y',strtotime($get_wfh['wfh_request_date'])).'. Due to some official reason.','my-wfh-requests?request-id='. $get_wfh->wfh_request_id .'',$get_wfh->wfh_employee_id,NULL,NULL,'no');
                            }   
                        }
                    }
                    if($wfh_insert > 1){
                        return response()->json([
                                'status' => 'TRUE',
                                'msg' => 'All remote Work request has been submitted. Please wait for response',
                            ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'You have already submitted a request for '.$request_data->input('request_date').'.',
                        ]);
                    }
                }

            }    
        }catch(\Exception $e){
            $this->create_error_log('All Remote Work Request', $e);
            if(!config('app.debug')){
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
            ActivityTracker::track_activity('Access All Request for Remote Work Module');
        }
    }


    protected function my_wfh_requests(){

        try{
            $title = Self::TITLE_GLOBAL2;
      
            $login_user = $this->get_login_user_detail();
            
            $my_requests = WFH::where('wfh_employee_id' , (string) session('user_session'))->orderBy('wfh_request_id', 'DESC')->get()->toArray();

            $wfh_request_id = array_column($my_requests,'wfh_request_id');

            $responsed_request = RequestApproval::where('request_module_id',3)->whereIn('request_id', $wfh_request_id)->pluck('request_id')->toArray();
            
            $send_data = compact('title','login_user','my_requests','responsed_request');

            return view('wfh.personal-request', $send_data);
            
        }catch(\Exception $e){
            $this->create_error_log('Access Request for Work From Home Module', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('Access Request for Work From Home Module');
      
        }
    }
  

    protected function requestfor_wfh(Request $request_data){
        try{

            $validator = Validator::make($request_data->all(), [
                'days_assign' => 'required',
                'from_date' => ($request_data->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')).'|after:yesterday ': '' ,
                'to_date' => ($request_data->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d' : '' , 
                'request_date' => ($request_data->input('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')).'|after:yesterday ': '' ,
                'home_work_reason' => 'required',
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

                if(!empty($request_data->input('from_date')) && !empty($request_data->input('to_date'))){
                    $date = $request_data->input('from_date').'  '.$request_data->input('to_date');

                    if($request_data->input('from_date') < $login_user[0]['employee_doj']){
                        return response()->json([
                            'msg' => 'You can\'t apply attendance request before your joining date.',
                            'status' => 'FALSE'
                        ]);
                    }
                    
                    $no_of_days = $this->count_days($request_data->input('from_date'),$request_data->input('to_date'));
                  
                    if(!empty($no_of_days) &&  $no_of_days >= 30){
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You can't apply request more than 30 at a time ",
                        ]);
                    }
                    for($i = 0; $i<$no_of_days; $i++){
                    
                        $request_date = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request_data->input('from_date'))));

                        $checking_request = WFH::where('wfh_employee_id', (string) session('user_session'))->whereIn('wfh_request_date',[$request_date])->get(['wfh_request_date'])->toArray();
                        
                        foreach($checking_request as $request_date){
                            
                            $request_apply_date[] =  $request_date['wfh_request_date'];
                        }
                    
                    }
    
                    if(!empty($checking_request)){

                        $date = implode(' , ',$request_apply_date);

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You've already applied requests for these dates ".$date,
                        ]);
                    }
                   
                }
            
                else{

                    if($request_data->input('request_date') < $login_user[0]['employee_doj']){
                        return response()->json([
                            'msg' => 'You can\'t apply remote work request before your joining date.',
                            'status' => 'FALSE'
                        ]);
                    }

                    $date = $request_data->input('request_date');
                    $checking_request = WFH::where('wfh_employee_id', (string) session('user_session'))->where('wfh_request_date', $request_data->input('request_date'))->where('wfh_request_approved_by_hr', '!=', 3)->get()->toArray();
                    
                }

                $checking_request = WFH::where('wfh_employee_id', (string) session('user_session'))->where('wfh_request_date',$request_data->input('request_date'))->get()->toArray();
                if(empty($checking_request)){

                    if(!empty($request_data->input('from_date')) && !empty($request_data->input('to_date'))){
                    
                        $no_of_days =  $this->count_days($request_data->input('from_date'),$request_data->input('to_date'));
                        
                        for($i = 0; $i<$no_of_days; $i++){
                            $wfh_request = new WFH();

                            $dates =  date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request_data->input('from_date'))));
                            
                            $checking_request_dates = WFH::where('wfh_employee_id', (string) session('user_session'))->where('wfh_request_date',$dates)->get(['wfh_request_date'])->toArray();

                            if(empty($checking_request_dates)){
                                
                                $wfh_request->wfh_employee_id = $request_data->session()->get('user_session');
                                $wfh_request->wfh_request_date = $dates;
                                $wfh_request->wfh_request_message = $request_data->input('home_work_reason');
                                $wfh_request->wfh_request_approved_by_hr = '1';
                                $wfh_request->wfh_request_submition_date = date('d-m-Y H:i:s');
                                $process = $wfh_request->save();   
                                
                                if(!CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null)){
                                    $month = date('m', strtotime($dates)); $wfh_year = date('Y', strtotime($dates));
                                    
                                    $default_approved_wfh = WFH::where('wfh_employee_id', (string) session('user_session'))->whereMonth('wfh_request_date',$month)->whereYear('wfh_request_date',$wfh_year)->count();
                                    
                                    if($default_approved_wfh == 1){

                                        $wfh_request =  WFH::find($wfh_request->wfh_request_id);
                                        $wfh_request->wfh_request_approved_by_hr = '2';
                                        $wfh_request->wfh_request_approved_date_hr = 'Auto approved by system';
                                        $process = $wfh_request->save();  
                                    }       
                                }                       
                            }
                        }
                    
                    }else{
                        $wfh_request = new WFH();
                        $wfh_request->wfh_employee_id = $request_data->session()->get('user_session');
                        $wfh_request->wfh_request_date = $request_data->input('request_date');
                        $wfh_request->wfh_request_message = $request_data->input('home_work_reason');
                        $wfh_request->wfh_request_approved_by_hr = '1';
                        $wfh_request->wfh_request_submition_date = date('d-m-Y H:i:s');
                        
                        $process = $wfh_request->save();  

                        if(!CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null)){
                            $month = date('m', strtotime($request_data->input('request_date'))); $wfh_year = date('Y', strtotime($request_data->input('request_date')));
                            $default_approved_wfh = WFH::where('wfh_employee_id', (string) session('user_session'))->whereMonth('wfh_request_date',$month)->whereYear('wfh_request_date',$wfh_year)->count();
                            
                            if($default_approved_wfh == 1){
    
                                $wfh_request =  WFH::find($wfh_request->wfh_request_id);
                                $wfh_request->wfh_request_approved_by_hr = '2';
                                $wfh_request->wfh_request_approved_date_hr = 'Auto approved by system';
                                $process = $wfh_request->save();  
                            }
                        }
                       

                    }   
                  
                    if($process == 1){

                        if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                            $to_mngr1  = $login_user[0]['employee_senior_manager'];
                        }
                        if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                            $to_mngr2 = $login_user[0]['employee_junior_manager'];
                        }

                        if(!empty($request_data->input('from_date')) && !empty($request_data->input('to_date'))){
                          
                            $this->send_notification('New Remote Work Request',''.$login_user[0]['employee_name'].' applied for remote work request(s) from '.$request_data->input('from_date'). ' to '.$request_data->input('to_date').'. Please click here to view.','wfh-requests?employee_id='. session('user_session').'&from_date='.$request_data->input('from_date'). '&to_date='.$request_data->input('to_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                            $this->send_module_email(5,'New Remote Work Requests','<b>'.$login_user[0]['employee_name']. '</b> applied for remote work request(s) from <b>'.$request_data->input('from_date'). '</b> to <b>'.$request_data->input('to_date').'</b>.',''.url('/').'/wfh-requests?employee_id='. session('user_session').'&from_date='.$request_data->input('from_date'). '&to_date='.$request_data->input('to_date'));

                            $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Remote Work Request','You\'ve successfully applied Remote Work request(s) and has been forwarded to your reporting authorities.');

                        }else{
                          
                            $this->send_notification('New Remote Work Request',''.$login_user[0]['employee_name'].' applied for remote work request for the date of '.$request_data->input('request_date').' Please click here to view.','wfh-requests?employee_id='. session('user_session').'&date='. $request_data->input('request_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');
                            
                            $this->send_module_email(5,'New Remote Work Request','<b>'.$login_user[0]['employee_name']. '</b> applied for remote work request for the date of <b>'.$request_data->input('request_date'). '</b>.',''.url('/').'/wfh-requests?employee_id='. session('user_session').'&date='. $request_data->input('request_date'));

                            $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Remote Work Request','You\'ve successfully applied Remote Work request and has been forwarded to your reporting authorities.');

                        }

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Remote work request has been submitted. Please wait for response',
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
                        'msg' => 'You have already submitted a request for '.$request_data->input('request_date').'.',
                    ]);
                }
            }    
        }catch(\Exception $e){
            $this->create_error_log('Apply Remote Work Request', $e);
            if(!config('app.debug')){
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
            ActivityTracker::track_activity('Access Request for Remote Work Module');
        }
    }

    protected function wfh_request(Request $request){

        try{

            // only the module's role holders, or the requester's own managers, may respond
            $request_employees = WFH::whereIn('wfh_request_id', explode(',', (string) $request->input('ids')))->pluck('wfh_employee_id')->toArray();
            if(!$this->can_respond_for($request_employees, ['hr'])){
                return $this->not_allowed_response();
            }

            if($request['status'] == "Approve"){
                $process = '';
                $wfh_request_ids = explode(",",$request['ids']) ;
                $login_user = $this->get_login_user_detail();

                foreach( $wfh_request_ids as $wfh_request_id){
                    $get_wfh = WFH::find($wfh_request_id);

                    if(!empty($get_wfh)){

                        $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$get_wfh->wfh_employee_id);
                        
                        if($request_approve_permission != null){
                            return response()->json([
                                'msg' => $request_approve_permission['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        if($get_wfh->wfh_request_approved_by_hr == 1){
                            $get_wfh->wfh_request_approved_by_hr = 2;
                            $get_wfh->wfh_request_approved_date_hr = 'Approved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                            $process =  $get_wfh->save();
                    
                            $day = date("d", strtotime($get_wfh->wfh_request_date));
                    
                            $month = date("m", strtotime($get_wfh->wfh_request_date));
                        
                            $year = date("Y", strtotime($get_wfh->wfh_request_date));
                    
                            if($process == 1){                                          

                                $this->send_notification('Remote Work Request Approval', 'HR approved your Remote Work Request for the date of '. $year . '-'. $month .'-'. $day .'. Please click here to view.','my-wfh-requests?request-id='. $get_wfh->wfh_request_id .'',$get_wfh->wfh_employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($get_wfh['wfh_employee_id'], 'Remote Work Request','Your remote work Request has been approved by HR.');

                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not aprroved successfully try again",
                                    
                                ]);
                            }
                        }   
                    }     
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
            
            if($request['status'] == "DisApprove"){

                $process = '';
                $wfh_request_ids = explode(",",$request['ids']) ;
                $login_user = $this->get_login_user_detail();

                foreach( $wfh_request_ids as $wfh_request_id){
                    $get_wfh = WFH::find($wfh_request_id);

                    if(!empty($get_wfh)){
                        
                        $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$get_wfh->wfh_employee_id);
                        
                        if($request_approve_permission != null){
                            return response()->json([
                                'msg' => $request_approve_permission['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
                        if($get_wfh->wfh_request_approved_by_hr == 1){
                            $get_wfh->wfh_request_approved_by_hr = 3;
                            $get_wfh->wfh_request_approved_date_hr = 'Disapproved By : '.$login_user[0]['employee_name'].', at : ' . date('h:i A d M, Y');
                            $process =  $get_wfh->save();
                    
                            if($process == 1){
                
                                $this->send_notification('Remote Work Request Disapproval',  'HR disapproved your Remote Work Request for the date of '. $get_wfh->wfh_request_date . '. Please click here to view.','my-wfh-requests?request-id='. $get_wfh->wfh_request_id .'',$get_wfh->wfh_employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($get_wfh['wfh_employee_id'], 'Remote Work Request','Your remote work request has been disapproved by HR.');

                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not disapproved successfully try again",
                                    
                                ]);
                            }
                        }   
                    }       
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

            if($request['status'] == "LeadApprove"){
                $process = '';
                $login_user = $this->get_login_user_detail();
                $wfh_request_ids = explode(",",$request['ids']) ;
                $approval_maker = session('user_session');
                $status = 1;

                foreach( $wfh_request_ids as $wfh_request_id){
                    
                    $checking_request = RequestApproval::where('request_module_id',3)->where('request_approver_id',session('user_session'))->where('request_id', $wfh_request_id)->get()->toArray();

                    if(empty($checking_request)){

                        $update_request = WFH::find($wfh_request_id);
    
                        if(!empty($update_request) && $update_request->wfh_request_approved_by_hr == 1 && in_array($update_request->wfh_employee_id,$this->get_employee_hierarchy_unique($approval_maker,  $status))){

                            $wfh_approval = new RequestApproval();
                            $wfh_approval->request_module_id = 3;
                            $wfh_approval->request_id = $wfh_request_id;
                            $wfh_approval->request_approver_id =  $approval_maker;
                            $wfh_approval->request_approver_status = 2;
                            $wfh_approval->request_approver_msg = $request['approvermsg'];
                            $wfh_approval->request_approval_time = date('h:i A d M, Y');
                            $process = $wfh_approval->save();
                            
                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $update_request['wfh_employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

                                $notification_msg  = 'Dear employee, '. $login_user[0]['employee_name']. ' marked your remote work request(s) as approve  for the date of '.$update_request['wfh_request_date'].'. Please click here to view.';

                                $this->send_notification('Remote Work Request Approval',$notification_msg,'my-wfh-requests?request-id='. $update_request->wfh_request_id .'', $update_request['wfh_employee_id'],NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_request['wfh_employee_id'], 'Remote Work Request','Your remote work request been approved by your line manager.');

                                $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Approved their remote work request(s) for for the date of ' .$update_request['wfh_request_date']. ' Please click here to view.';

                                $this->send_notification('Remote Work Request Approval ',$notification_msg_hr, 'wfh-requests?request-id='.$update_request->wfh_request_id .'',NULL,NULL,NULL,'yes');

                            }else{
                    
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not approved successfully ",
                                    
                                ]);
                            }   
                
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
                $wfh_request_ids = explode(",",$request['ids']) ;
                $approval_maker = session('user_session');
                $status = 1;

                foreach( $wfh_request_ids as $wfh_request_id){
                
                    $checking_request = RequestApproval::where('request_module_id',3)->where('request_approver_id',session('user_session'))->where('request_id', $wfh_request_id)->get()->toArray();

                    if(empty($checking_request)){

                        $update_request = WFH::find($wfh_request_id);
        
                        if(!empty($update_request) && $update_request->wfh_request_approved_by_hr == 1 && in_array($update_request->wfh_employee_id,$this->get_employee_hierarchy_unique($approval_maker,  $status))){

                            $wfh_approval = new RequestApproval();
                            $wfh_approval->request_module_id = 3;
                            $wfh_approval->request_id = $wfh_request_id;
                            $wfh_approval->request_approver_id =  $approval_maker;
                            $wfh_approval->request_approver_status = 3;
                            $wfh_approval->request_approver_msg = $request['disapprovermsg'];
                            $wfh_approval->request_approval_time = date('h:i A d M, Y');
                            $process = $wfh_approval->save();
                            
                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $update_request['wfh_employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

                                $notification_msg  = 'Dear employee, '. $login_user[0]['employee_name']. ' marked your remote work request(s) as Disapproved  for the date of '.$update_request['wfh_request_date'].'. Please click here to view.';

                                $this->send_notification('Remote Work Request Disapproval',$notification_msg,'my-wfh-requests?request-id='. $update_request->wfh_request_id .'', $update_request['wfh_employee_id'],NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($update_request['wfh_employee_id'], 'Remote Work Request','Your remote work request has been disapproved by your line manager.');

                                $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Disapproved  their remote work request(s) for for the date of ' .$update_request['wfh_request_date']. ' Please click here to view.';

                                $this->send_notification('Remote Work Request Disapproval ',$notification_msg_hr, 'wfh-requests?request-id='.$update_request->wfh_request_id .'',NULL,NULL,NULL,'yes');

                            }else{
                    
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not disapproved successfully ",
                                    
                                ]);
                            }   
            
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
            $this->create_error_log('Approved And Disapproved Work From Home Request by Manager', $e);
            if(!config('app.debug')){
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
            ActivityTracker::track_activity('Approved And Disapproved Work From Home Request by Manager');
        }
       
    }

    protected function update_wfh_request(Request $request_data){
       
        try{

            if($this->check_request_validation($request_data->request_id,3) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Remote work request id is invalid',
                ]);
            }

            $validator = Validator::make($request_data->all(), [
                'request_date' => 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')).'|after:yesterday ' ,
                'edit_home_work_reason' => 'required',
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

                $wfh_update_request = WFH::find($request_data->request_id);

                if(!empty($wfh_update_request)){

                    $checking_request_responsed = RequestApproval::where('request_module_id',3)->where('request_id',$request_data->request_id)->get()->toArray();
                
                    if(empty($checking_request_responsed) && $wfh_update_request->wfh_request_approved_by_hr == 1){

                        $checking_request = WFH::where('wfh_employee_id', (string) session('user_session'))->where('wfh_request_date',$request_data->input('request_date'))->where('wfh_request_id','!=',$request_data->request_id)->get()->toArray();

                        if(empty($checking_request)){
                        
                            $wfh_update_request->wfh_request_date = date('Y-m-d',strtotime($request_data->input('request_date')));
                            $wfh_update_request->wfh_request_message = $request_data->input('edit_home_work_reason');
                            $wfh_update_request->wfh_request_submition_date = date('d-m-Y H:i:s');
                        
                            $process = $wfh_update_request->save();

                            if( $process == 1){
                                if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                    $to_mngr1  = $login_user[0]['employee_senior_manager'];
                                }
                                if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                    $to_mngr2 = $login_user[0]['employee_junior_manager'];
                                }
                
                                $this->send_notification('Update Remote Work Request',''.$login_user[0]['employee_name'].' update a remote work request. for the date of '.$request_data->input('request_date').' Please click here to view.','wfh-requests?employee_id='. session('user_session').'&date='.    $request_data->input('request_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                                $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Update Remote Work Request','Your remote work Request has been updated.');

                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => 'Remote work request has been updated. Please wait for response',
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
                                'msg' => 'You have already submitted a request for '.$request_data->input('request_date').'.',
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
            $this->create_error_log('Update Remote Work Request', $e);
            if(!config('app.debug')){
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
            ActivityTracker::track_activity('Update Remote Work Request');
            
        }
         
    }

    protected function delete_wfh_request(Request $request_data){
      
        try{
            if($this->check_request_validation($request_data->wfh_id,3) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Remote work request id is invalid',
                ]);
            }
            $wfh_delete_request = WFH::find($request_data->wfh_id);

            if(!empty($wfh_delete_request)){

                $checking_request_responsed = RequestApproval::where('request_module_id',3)->where('request_id',$request_data->wfh_id)->get()->toArray();
                
                if(empty($checking_request_responsed) && $wfh_delete_request->wfh_request_approved_by_hr == 1){
                    
                    $process = WFH::where('wfh_request_id', $request_data->wfh_id)->delete();
                    if( $process == 1){
                    
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Remote work request has been deleted',
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
            $this->create_error_log('Delete Remote Work Request', $e);
            if(!config('app.debug')){
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
            ActivityTracker::track_activity('Delete Remote Work Request');
            
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
