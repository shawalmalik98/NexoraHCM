<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use App\Models\{Employees, HelpDesk, HelpDeskStatus, Department,HelpDeskForDepartment,HelpDeskForEmployee, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait};
use App\Helpers\ActivityTracker;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class HelpdeskController extends Controller
{
    use LoginEmployee, LogGenerateTrait, NotificationTrait;

    const TITLE_GLOBAL = 'help-desk';
    const TITLE_GLOBAL1 = 'my-help-desk';
   
    protected function all_help_desks(Request $request_data){

        try{

            $title = SELF::TITLE_GLOBAL;
                
            $login_user = $this->get_login_user_detail();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();
            
            if($request_data->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }
            
            $where_clause = "1=1 AND employee_isactive = '".$status."'";
            $where_clause_to = '1=1';
            if(!empty($request_data->employee_location) || !empty(is_numeric($request_data->emp_status)) || !empty(is_numeric($request_data->emp_name))  || !empty($request_data->request_submited_date)){
                $limit_query = '';
                if(!empty($request_data->employee_location)){
                    $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                    $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                }
                if(!empty($request_data->emp_name)){
                    $where_clause .= ' AND help_desk_loged_by = "'.$request_data->emp_name.'"';
                }
               
                if(!empty($request_data->request_submited_date)){
                    $submition_date = date("Y-m-d", strtotime($request_data->request_submited_date));
                    $where_clause .= ' AND created_time LIKE "%'.$submition_date.'%"';
                }
            }

            $employees_data = Employees::where('employee_isactive','=',$status)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name'])->toArray();

            $get_my_help_desks = HelpDesk::join('tbl_employees', 'tbl_help_desk.help_desk_loged_by', '=', 'tbl_employees.employee_id')
            ->join('tbl_help_desk_ticket_status', 'tbl_help_desk.help_desk_id', '=', 'tbl_help_desk_ticket_status.help_desk_id')->whereIn('ticket_status_id',function ($query) {
                $query->selectRaw('max(ticket_status_id)')
                ->from('tbl_help_desk_ticket_status')
                ->groupBy('help_desk_id');
            })->whereRaw($where_clause)->get()->toArray();
     
            $send_data = compact('title', 'login_user','get_my_help_desks','employees_data','employee_locations');
            
            return view('help-desk.help-desk')->with($send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View My Help Desk ', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
		    ActivityTracker::track_activity('View My Help Desk');
          
        }
    }

    protected function my_help_desks(){
        try{
            $title = SELF::TITLE_GLOBAL1;
                
            $login_user = $this->get_login_user_detail();
            $departments = Department::get()->toArray();

            $get_my_help_desks = HelpDesk::join('tbl_help_desk_ticket_status', 'tbl_help_desk.help_desk_id', '=', 'tbl_help_desk_ticket_status.help_desk_id')->
            where(function ($query) use ($login_user){
                $query->whereIn('tbl_help_desk.help_desk_id',function ($query) use($login_user){
                    $query->select('hd_id')
                    ->from('tbl_help_desk_for_departments')
                    ->join('tbl_employees','tbl_employees.employee_department','=','tbl_help_desk_for_departments.hdf_department_id')
                    ->where('hdf_department_id','=',$login_user[0]['employee_department'])
                    ->where('employee_is_manager',1)
                    ->where('employee_id',session('user_session'));

                })->orWhereIN('tbl_help_desk.help_desk_id',function ($query) use($login_user){
                    $query->select('hd_id')
                    ->from('tbl_help_desk_for_employees')
                    ->where('hdf_employee_id',session('user_session'));
                })->orWhere('help_desk_loged_by',session('user_session'));
            })->whereIn('ticket_status_id',function ($query) {
                    $query->selectRaw('max(ticket_status_id)')
                    ->from('tbl_help_desk_ticket_status')
                    ->groupBy('help_desk_id');
                })->get()->toArray();
         
         
            $send_data = compact('title', 'login_user','get_my_help_desks','departments');
                
            return view('help-desk.my-help-desk')->with($send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View My Help Desk ', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
		    ActivityTracker::track_activity('View My Help Desk ');
          
        }
    }

    protected function add_help_desk(Request $request){

        try{

            $filename = '';

            if($request->hasFile('attachment')){
                $filename = $request->file('attachment')->getClientOriginalExtension();
            }
    
            $validator = Validator::make($request->all(), [
                'message' => 'required',
                'department_id' => 'required',
                'attachment' => [ 'max:10000', ($filename == 'txt' ) ? File::types(['jpg','png','docx','pdf','txt']) : File::types(['jpg','png','docx','pdf'])],
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
                
                if(!empty($request->input('department_id')) && empty($request->input('employee_id'))){

                    $department_id = $request->department_id;
                    
                    $employees_can_ra_depart = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->where('employee_is_manager',1)->whereIn('department_id',$department_id)->where('employee_id','!=',session('user_session'))->get(['employee_id'])->toArray();
                    
                    if(empty($employees_can_ra_depart)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'There are no employees at manager level in this department',
                        ]);
                    }

                }

                $help_desk = new HelpDesk();

                if($request->hasFile('attachment')){

                    $fileNameToStore = $request->file('attachment')->getClientOriginalName();
                    
                    $request->file('attachment')->storeAs('portal_documents/helpDesk',$fileNameToStore);
        
                    $help_desk->help_attachment = $fileNameToStore;
        
                }


                $help_desk->help_desk_loged_by = session('user_session');
                $help_desk->help_message =$request->input('message') ; 
                $help_desk->created_time = date('Y-m-d H:i:s');

                $process = $help_desk->save();

                if($process == 1){
                    
                    $help_desk_status = new HelpDeskStatus();
                    $help_desk_status->help_desk_id = $help_desk->help_desk_id;
                    $help_desk_status->ticket_status = 1;
                    $help_desk_status->ticket_status_changed_by = session('user_session');
                    $help_desk_status->ticket_status_created_time = date('Y-m-d H:i:s');
                    $help_desk_status->save();

                    if(!empty($request->input('department_id')) && empty($request->input('employee_id'))){

                        foreach($department_id as $deprt_id){
                            $help_desk_for_deprt = new HelpDeskForDepartment();
                            $help_desk_for_deprt->hdf_department_id = $deprt_id;
                            $help_desk_for_deprt->hd_id  = $help_desk->help_desk_id;
                            $help_desk_for_deprt->created_time = date('Y-m-d H:i:s');
                            $help_desk_for_deprt->save();
                        
                        }

                
                        foreach($employees_can_ra_depart as $employee){

                            $this->send_notification('New Complaint Added', ''.$login_user[0]['employee_name'].' added a complaint. Please click here to view.','my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no');
                        }

                    }else{
                        $department_id = $request->department_id;
                        $employee_ids = $request->employee_id;
            
                        foreach($employee_ids as $employee_id){
                            $help_desk_for_employee = new HelpDeskForEmployee();
                            $help_desk_for_employee->hdf_employee_id = $employee_id;
                            $help_desk_for_employee->hd_id = $help_desk->help_desk_id;
                            $help_desk_for_employee->created_time = date('Y-m-d H:i:s');
                            $help_desk_for_employee->save();
                        
                        }

                        $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->whereIn('employee_id',$employee_ids)->whereIn('department_id',$department_id)->get(['employee_id'])->toArray();
                    
                        foreach($employees as $employee){

                            $this->send_notification('New Complaint Added', ''.$login_user[0]['employee_name'].' added a complaint. Please click here to view.','my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no');               
                        }

                    }

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Ticket generated successfully.',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Ticket not generated successfully try again!',
                    ]);
                }
            }    
        }catch(\Exception $e){
            $this->create_error_log("Add HelpDesk", $e);
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
            ActivityTracker::track_activity('Add helpDesk'); 
        }

    }

    protected function my_help_desk_detail($help_desk_id){

        try{
            
            $flag = false;
            $title = SELF::TITLE_GLOBAL1;
            
            $login_user = $this->get_login_user_detail();

            $departments = Department::get()->toArray();

            $help_desk = HelpDesk::where('tbl_help_desk.help_desk_id', $help_desk_id)->get()->toArray();

            $help_desk_status = HelpDeskStatus::where('tbl_help_desk_ticket_status.help_desk_id', $help_desk_id)->get()->toArray();
            
            $help_desk_departments = HelpDeskForDepartment::where('tbl_help_desk_for_departments.hd_id', $help_desk_id)->pluck('hdf_department_id')->toArray();

            $help_desk_employees = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $help_desk_id)->get()->toArray();

            $help_desk_employees_selected = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $help_desk_id)->pluck('hdf_employee_id')->toArray();

            $employee_department_ids = Employees::where('employee_isactive', 1)->whereIn('employee_id',$help_desk_employees_selected)->pluck('employee_department')->toArray();

            $get_employees = Employees::where('employee_isactive', 1)->whereIn('employee_department',$employee_department_ids)->get(['employee_id','employee_name','employee_code'])->toArray();
       
            $employee_department_can_ra = Employees::where('employee_isactive', 1)->whereIn('employee_department',$help_desk_departments)->where('employee_is_manager', 1)->get(['employee_id'])->toArray();
           
            $employee_access = array_column($get_employees,'employee_id');

            $employee_depart_access = array_column($employee_department_can_ra,'employee_id');
            
            $help_desk_access = array_merge($employee_access,$employee_depart_access,[$help_desk[0]['help_desk_loged_by']]);
            
           if(!empty($help_desk_access) && in_array(session('user_session'),$help_desk_access)){
           
                $send_data = compact('title','login_user','help_desk','help_desk_status','help_desk_departments','help_desk_employees','employee_department_can_ra','departments','help_desk_employees_selected','employee_department_ids','get_employees');
                
                return view('help-desk.help-desk-detail',$send_data );
           }else{
                return view('errors.403');
           }

           

        }catch(\Exception $e){
            $this->create_error_log("My HelpDesk Detail", $e);
            return view('errors.500');
        }finally{
             ActivityTracker::track_activity('My HelpDesk Detail'); 
          
        }

    }

    protected function help_desk_detail($help_desk_id){

        try{
            
            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $departments = Department::get()->toArray();

            $help_desk = HelpDesk::where('tbl_help_desk.help_desk_id', $help_desk_id)->get()->toArray();

            $help_desk_status = HelpDeskStatus::where('tbl_help_desk_ticket_status.help_desk_id', $help_desk_id)->get()->toArray();
            
            $help_desk_departments = HelpDeskForDepartment::where('tbl_help_desk_for_departments.hd_id', $help_desk_id)->pluck('hdf_department_id')->toArray();

            $help_desk_employees = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $help_desk_id)->get()->toArray();

            $help_desk_employees_selected = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $help_desk_id)->pluck('hdf_employee_id')->toArray();

            $employee_department_ids = Employees::where('employee_isactive', 1)->whereIn('employee_id',$help_desk_employees_selected)->pluck('employee_department')->toArray();

            $get_employees = Employees::where('employee_isactive', 1)->whereIn('employee_department',$employee_department_ids)->get(['employee_id','employee_name','employee_code'])->toArray();
       
            $employee_department_can_ra = Employees::where('employee_isactive', 1)->whereIn('employee_department',$help_desk_departments)->where('employee_is_manager', 1)->where('employee_id','!=',session('user_session'))->get(['employee_id'])->toArray();
           
            $send_data = compact('title','login_user','help_desk','help_desk_status','help_desk_departments','help_desk_employees','employee_department_can_ra','departments','help_desk_employees_selected','employee_department_ids','get_employees');
            
            return view('help-desk.help-desk-detail',$send_data );

        }catch(\Exception $e){
            $this->create_error_log("HelpDesk Detail", $e);
            return view('errors.500');
        }finally{
             ActivityTracker::track_activity('HelpDesk Detail'); 
          
        }

    }

    protected function ticket_status(Request $request_data){
       
      
        try{

            $validator = Validator::make($request_data->all(), [
                'hd_id'=> 'required|integer',
                'ticket_status' => 'required|integer',
                'department_name.*' => 'required|integer',
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
            

            $last_status = HelpDeskStatus::where('help_desk_id',$request_data->hd_id)->orderBy('ticket_status_id','DESC')->get(['ticket_status'])->toArray();

            if(!empty($last_status) && $last_status[0]['ticket_status'] == $request_data->ticket_status){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Ticket already on same status.",
                ]);
            }

            $help_desk_status = new HelpDeskStatus();
            $help_desk_status->help_desk_id = $request_data->hd_id;
            $help_desk_status->ticket_status = $request_data->ticket_status;
            $help_desk_status->ticket_status_changed_by = session('user_session');
            $help_desk_status->ticket_status_created_time = date('Y-m-d H:i:s');
            $process =  $help_desk_status->save(); 

            if($process == 1){                

                if($request_data->input('ticket_status') == 1 || $request_data->input('ticket_status') == 3  || $request_data->input('ticket_status') == 5 ){
                    
                    $employees = Employees::join('tbl_help_desk_for_departments','tbl_help_desk_for_departments.hdf_department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->where('employee_is_manager',1)->where('hd_id',$request_data->hd_id)->where('employee_id','!=',session('user_session'))->get('employee_id')->toArray();
                
                    if(empty($employees)){
                        
                        $employees = Employees::join('tbl_help_desk_for_employees','tbl_help_desk_for_employees.hdf_employee_id','=','tbl_employees.employee_id')->where('employee_isactive',1)->where('employee_is_manager',1)->where('hd_id',$request_data->hd_id)->where('employee_id','!=',session('user_session'))->get('employee_id')->toArray();
                       
                    }
    
                    foreach($employees as $employee){
                        
                        if($request_data->input('ticket_status') == 1){
                            $title = "Complaint Reopen again";
                            $notify_msg = "Same complaint again added. Please click here to view.";
    
                            
                        }
                        elseif($request_data->input('ticket_status') == 3){
                            $title = "Complaint rejected";
                            $notify_msg = "Rejected a complaint. Please click here to view.";
    
                            
                        }
                        elseif($request_data->input('ticket_status') == 5){
                            $title = "Complaint query resolved";
                            $notify_msg = "Complaint query resolved. Please click here to view.";
                        }
    
                        $this->send_notification($title, ''.$login_user[0]['employee_name']. ' '. $notify_msg,'my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no');               
                    }
    
                }else{
                    $host_id = HelpDesk::where('help_desk_id',$request_data->hd_id)->get('help_desk_loged_by')->toArray();
               
                    if($request_data->input('ticket_status') == 2){
                        $title = "Ticket open";
                        $notify_msg = "Open your complaint ticket. Please click here to view.";
                    }elseif($request_data->input('ticket_status') == 4){
                        
                        $title = "Complaint query resolved";
                        $notify_msg = "Complaint query resolved. Please click here to view.";
                    }

                    $this->send_notification($title, ''.$login_user[0]['employee_name']. ' '. $notify_msg,'my-tickets?help-desk-id='.$host_id[0]['help_desk_loged_by'] .'',$host_id[0]['help_desk_loged_by'],NULL,NULL,'no');               
                }
               
               
               
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Ticket status updated successfully.",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Ticket status not updated successfully try again.",
                ]);
            }
    
        }catch(\Exception $e){
            $this->create_error_log("Ticket Status Change HelpDesk Detail ", $e);
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
            ActivityTracker::track_activity('Ticket Status Change HelpDesk Detail'); 
        }
    }

    protected function update_help_desk_employees(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required',
                'department_name' => 'required',
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
        
                $department_id = $request->department_name;
            
                $employee_ids = $request->employee_id;
                
                $process = '';
                
                $help_desk_employees_selected = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $request->hd_id)->pluck('hdf_employee_id')->toArray();

                HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id',$request->hd_id)->delete();

                foreach($employee_ids as $employee_id){
                    $help_desk_for_employee = new HelpDeskForEmployee();
                    $help_desk_for_employee->hdf_employee_id = $employee_id;
                    $help_desk_for_employee->hd_id = $request->hd_id;
                    $help_desk_for_employee->created_time = date('Y-m-d H:i:s');
                    $process = $help_desk_for_employee->save();
                
                }

                $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->whereIn('employee_id',$employee_ids)->whereIn('department_id',$department_id)->get(['employee_id'])->toArray();

                
                foreach($employees as $employee){
                    
                    if(!in_array($employee,$help_desk_employees_selected)){
                        $this->send_notification('New Complaint Added', ''.$login_user[0]['employee_name'].' Added a Complaint. Please click here to view.','my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no');   
                    }            
                }
            
        
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Help desk employee updated successfully.',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Help desk employee not updated successfully try again.',
                    ]);
                }
            }   
          
        }catch(\Exception $e){
            $this->create_error_log("Help Desk Employee Updated Detail", $e);
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
            ActivityTracker::track_activity('Help Desk Employee Updated Detail');
        }
    
    }

    protected function update_help_desk_departments(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'department_name' => 'required',
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
                
                $department_id = $request->department_name;
                
                if(!empty($request->input('department_name'))){

                    $employees_can_ra_depart = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->where('employee_is_manager',1)->whereIn('department_id',$department_id)->where('employee_id','!=',session('user_session'))->get(['employee_id'])->toArray();
                    
                    if(empty($employees_can_ra_depart)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'There are no employees at manager level in this department',
                        ]);
                    }
                }

                
                $process = '';
                
            
                $help_desk_departments = HelpDeskForDepartment::where('tbl_help_desk_for_departments.hd_id',$request->hd_id)->pluck('hdf_department_id')->toArray();

                HelpDeskForDepartment::where('tbl_help_desk_for_departments.hd_id',$request->hd_id)->delete();

                foreach($department_id as $deprt_id){
                    $help_desk_for_deprt = new HelpDeskForDepartment();
                    $help_desk_for_deprt->hdf_department_id = $deprt_id;
                    $help_desk_for_deprt->hd_id  = $request->hd_id;
                    $help_desk_for_deprt->created_time = date('Y-m-d H:i:s');
                    $process = $help_desk_for_deprt->save();
                
                }

        
                foreach($employees_can_ra_depart as $employee){
                    if(!in_array($employee,$help_desk_departments)){
                        $this->send_notification('New Complaint Added', ''.$login_user[0]['employee_name'].' Added a Complaint. Please click here to view.','my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no');
                    } 
                }
        
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Help desk department updated successfully.',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Help desk department not updated successfully try again.',
                    ]);
                }
            }   
          
        }catch(\Exception $e){
            $this->create_error_log("Help Desk Department Updated Detail", $e);
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
            ActivityTracker::track_activity('Help Desk Department Updated Detail'); 
          
        }
    
    }

    protected function help_desk_chat(Request $CreateChatRecord){
        $login_user = $this->get_login_user_detail();

        $factory = (new Factory)
            ->withServiceAccount(__DIR__.'/firebase-secretkey.json')
            ->withDatabaseUri('https://sourcecode-apps-d94da-hcm.firebaseio.com/');

        $database = $factory->createDatabase();
        $NewChatMessage = $database->getReference('chats/HelpDeskChat/')->push([
            'message' => $CreateChatRecord->message,
            'sender_id' => $login_user[0]['employee_id'],
            'help_desk_id' => $CreateChatRecord->input('hd_id'),
            'sender_name' => $login_user[0]['employee_name'],
            'sender_avatar' => ($login_user[0]['profile_avatar']) ?? 'source-code.png',
            'date' => date('d F, Y'),
            'time' => date('h:i A')
        ]);
        return $NewChatMessage;
    }


    public function get_employee_department(Request $request){
             
        try{
            $add_department_hd = $request['department'];
            $help_desk_id = $request['help_desk_id'];
            $result = array();
            if(!empty($add_department_hd) && empty($help_desk_id)){

                $employees = Employees::where('employee_isactive',1)->whereIn('employee_department', $add_department_hd)->get(['employee_id','employee_code','employee_name'])->where('employee_id','!=',session('user_session'))->toArray();
                
                $result[] = '<option value="">Select Employee</option>';
                foreach($employees as $employee){
                
                    $result[] .= '<option value="'.$employee['employee_id'].'">'.$employee['employee_code'].' - '.$employee['employee_name'].'</option>';
                
                  
                }
            }elseif(!empty($help_desk_id) && !empty($add_department_hd) ){
               

                $help_desk_employees_selected = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $help_desk_id)->pluck('hdf_employee_id')->toArray();
                
                $get_employees = Employees::where('employee_isactive', 1)->whereIn('employee_department',$add_department_hd)->get(['employee_id','employee_name','employee_code','employee_department'])->toArray();


                $result[] = '<option value="">Select Employee</option>';
                foreach($get_employees as $employee){
                    if(in_array($employee['employee_id'],$help_desk_employees_selected)){
                        $result[] .= '<option value="'.$employee['employee_id'].'" selected>'.$employee['employee_code'].' - '.$employee['employee_name'].'</option>';
                    }else{
                        $result[] .= '<option value="'.$employee['employee_id'].'">'.$employee['employee_code'].' - '.$employee['employee_name'].'</option>';
                    }
                }
            }
           
            else{
                $result[] = '<option value="">First Select Department</option>';
            }

            return $result;
            
        }
        catch(\Exception $e){
            $this->create_error_log("Fetch Employee Selected Department", $e);
            return $result;
         
        }
        finally{

            ActivityTracker::track_activity('Fetch Employee Selected Department');
            
        }
    }

    protected function download_help_desk_attachment(Request $request){

        try{
            if(!empty($request->path)){
                
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
               
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
                return response()->download(storage_path('app/portal_documents/'.$request->path));
            }else{
                return redirect(url());
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
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
            ActivityTracker::track_activity($request->title);
        }
    }

    protected function download_my_help_desk_attachment(Request $request){

        try{
            if(!empty($request->path)){
                
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
               
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
                return response()->download(storage_path('app/portal_documents/'.$request->path));
            }else{
                return redirect(url());
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' =>  "Something went wrong. Please try again.",
            ]);
        }finally{
            ActivityTracker::track_activity($request->title);
        }
    }


    

    
}
