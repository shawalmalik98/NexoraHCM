<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

use App\Models\{Department, HelpDesk, Employees, HelpDeskForEmployee, HelpDeskStatus, HelpDeskForDepartment};

use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait};
use App\Helpers\ActivityTracker;

class HelpdeskApiController extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait;

    const TITLE_GLOBAL = 'help-desk';
    const TITLE_GLOBAL1 = 'my-help-desk';

    protected function my_help_desks(Request $request){
        try{
            $title = SELF::TITLE_GLOBAL1;
                
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            $departments = Department::get(['department_id','department_name'])->toArray();

            $get_my_help_desks = HelpDesk::join('tbl_help_desk_ticket_status', 'tbl_help_desk.help_desk_id', '=', 'tbl_help_desk_ticket_status.help_desk_id')->
            where(function ($query) use ($login_user){
                $query->whereIn('tbl_help_desk.help_desk_id',function ($query) use($login_user){
                    $query->select('hd_id')
                    ->from('tbl_help_desk_for_departments')
                    ->join('tbl_employees','tbl_employees.employee_department','=','tbl_help_desk_for_departments.hdf_department_id')
                    ->where('hdf_department_id','=',$login_user[0]['employee_department'])
                    ->where('employee_is_manager',1)
                    ->where('employee_id',$login_user[0]['employee_id']);

                })->orWhereIN('tbl_help_desk.help_desk_id',function ($query) use($login_user){
                    $query->select('hd_id')
                    ->from('tbl_help_desk_for_employees')
                    ->where('hdf_employee_id',$login_user[0]['employee_id']);
                })->orWhere('help_desk_loged_by',$login_user[0]['employee_id']);
            })->whereIn('ticket_status_id',function ($query) {
                    $query->selectRaw('max(ticket_status_id)')
                    ->from('tbl_help_desk_ticket_status')
                    ->groupBy('help_desk_id');
                })->get()->toArray();
         
         
            $data = compact('title','get_my_help_desks','departments');

            return $this->api_response_json(TRUE,"View My Help Desk",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View My Help Desk API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('View My Help Desk API',  $request->header('Bearer-Token'));
            
        }
    }


    public function get_employee_department(Request $request){
             
        try{

            $validator = Validator::make($request->post(), [
                'department' => 'required|array',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }

            $add_department_hd = $request->post('department');
            $help_desk_id = $request->post('help_desk_id');
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if(!empty($add_department_hd) && empty($help_desk_id)){

                $employees = Employees::where('employee_isactive',1)->whereIn('employee_department', $add_department_hd)->get(['employee_id','employee_code','employee_name'])->where('employee_id','!=',$login_user[0]['employee_id'])->toArray();
                
                $result = $employees;
            }elseif(!empty($help_desk_id) && !empty($add_department_hd) ){
               

                $help_desk_employees_selected = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $help_desk_id)->pluck('hdf_employee_id')->toArray();
                
                $get_employees = Employees::where('employee_isactive', 1)->whereIn('employee_department',$add_department_hd)->get(['employee_id','employee_name','employee_code'])->toArray();

                $i = 0; 
                foreach($get_employees as $employee){
                    if(in_array($employee['employee_id'],$help_desk_employees_selected)){
                        $get_employees[$i]['selected'] = true;
                    }else{
                        $get_employees[$i]['selected'] = false;
                    }
                    $i++;
                }
                $result = $get_employees;
            }
           
            else{
                $result = [];
            }
            return $this->api_response_json(TRUE,"Employee Department",$result,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('Fetch Employee Selected Department API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Fetch Employee Selected Department API',  $request->header('Bearer-Token'));
            
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
                'department_id' => 'required|array',
                'department_id.*' => 'required|integer',
                'employee_id' => 'array|nullable',
                'employee_id.*' => 'required|integer',
                'attachment' => [ 'max:10000', ($filename == 'txt' ) ? File::types(['jpg','png','docx','pdf','txt']) : File::types(['jpg','png','docx','pdf'])],
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {
            
                $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
                
                if(!empty($request->post('department_id')) && empty($request->post('employee_id'))){

                    $department_id = $request->post('department_id');
                    
                    $employees_can_ra_depart = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->where('employee_is_manager',1)->whereIn('department_id',$department_id)->where('employee_id','!=',$login_user[0]['employee_id'])->get(['employee_id'])->toArray();
                    
                    if(empty($employees_can_ra_depart)){

                        return $this->api_response_json(FALSE,"There are no employees at manager level in this department","",200);

                    }

                }

                $help_desk = new HelpDesk();

                if($request->hasFile('attachment')){

                    $fileNameToStore = $request->file('attachment')->getClientOriginalName();
                    
                    $request->file('attachment')->storeAs('portal_documents/helpDesk',$fileNameToStore);
        
                    $help_desk->help_attachment = $fileNameToStore;
        
                }

                $help_desk->help_desk_loged_by = $login_user[0]['employee_id'];
                $help_desk->help_message =$request->post('message') ; 
                $help_desk->created_time = date('Y-m-d H:i:s');

                $process = $help_desk->save();

                if($process == 1){
                    
                    $help_desk_status = new HelpDeskStatus();
                    $help_desk_status->help_desk_id = $help_desk->help_desk_id;
                    $help_desk_status->ticket_status = 1;
                    $help_desk_status->ticket_status_changed_by = $login_user[0]['employee_id'];
                    $help_desk_status->ticket_status_created_time = date('Y-m-d H:i:s');
                    $help_desk_status->save();

                    if(!empty($request->post('department_id')) && empty($request->post('employee_id'))){

                        foreach($department_id as $deprt_id){
                            $help_desk_for_deprt = new HelpDeskForDepartment();
                            $help_desk_for_deprt->hdf_department_id = $deprt_id;
                            $help_desk_for_deprt->hd_id  = $help_desk->help_desk_id;
                            $help_desk_for_deprt->created_time = date('Y-m-d h:i:s');
                            $help_desk_for_deprt->save();
                        }
                
                        foreach($employees_can_ra_depart as $employee){

                            $this->send_notification_api('New Complaint Added', ''.$login_user[0]['employee_name'].' added a complaint. Please click here to view.','my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no',$login_user[0]['employee_id']);
                        }

                    }else{
                        $department_id = $request->post('department_id');
                        $employee_ids = $request->post('employee_id');
            
                        foreach($employee_ids as $employee_id){
                            $help_desk_for_employee = new HelpDeskForEmployee();
                            $help_desk_for_employee->hdf_employee_id = $employee_id;
                            $help_desk_for_employee->hd_id = $help_desk->help_desk_id;
                            $help_desk_for_employee->created_time = date('Y-m-d H:i:s');
                            $help_desk_for_employee->save();
                        
                        }

                        $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->whereIn('employee_id',$employee_ids)->whereIn('department_id',$department_id)->get(['employee_id'])->toArray();
                    
                        foreach($employees as $employee){

                            $this->send_notification_api('New Complaint Added', ''.$login_user[0]['employee_name'].' added a complaint. Please click here to view.','my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no',$login_user[0]['employee_id']);            
                        }

                    }

                    return $this->api_response_json(TRUE,"Ticket Generated Successfully.","",200);

                }else{

                    return $this->api_response_json(FALSE,"Ticket Not Generated. Try Again!","",200);
                    
                }
            }    
    
        }catch(\Exception $e){

            $this->create_error_log_from_api('Add HelpDesk API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Add HelpDesk API',  $request->header('Bearer-Token'));
            
        }

    }

    protected function my_help_desk_detail(Request $request, $help_desk_id){

        try{
            
            $flag = false;
            $title = SELF::TITLE_GLOBAL1;
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $departments = Department::get(['department_id','department_name'])->toArray();

            $help_desk = HelpDesk::where('tbl_help_desk.help_desk_id', $help_desk_id)
            ->join('tbl_employees','tbl_employees.employee_id','=','help_desk_loged_by')
            ->get(['employee_name','created_time','help_desk_loged_by','help_message','help_attachment'])->toArray();


            $help_desk_status = HelpDeskStatus::where('tbl_help_desk_ticket_status.help_desk_id', $help_desk_id)
            ->join('tbl_employees','tbl_employees.employee_id','=','ticket_status_changed_by')
            ->get(['employee_name','ticket_status_created_time','ticket_status'])->toArray();
            
            $help_desk_departments = HelpDeskForDepartment::where('tbl_help_desk_for_departments.hd_id', $help_desk_id)->pluck('hdf_department_id')->toArray();

            $help_desk_employees = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $help_desk_id)->get()->toArray();

            $help_desk_employees_selected = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $help_desk_id)->pluck('hdf_employee_id')->toArray();

            $employee_department_ids = Employees::where('employee_isactive', 1)->whereIn('employee_id',$help_desk_employees_selected)->pluck('employee_department')->toArray();

       
            $get_employees = Employees::where('employee_isactive', 1)->whereIn('employee_department',$employee_department_ids)->get(['employee_id','employee_name','employee_code'])->toArray();
       

            $employee_department_can_ra = Employees::where('employee_isactive', 1)->whereIn('employee_department',$help_desk_departments)->where('employee_is_manager', 1)->get(['employee_id'])->toArray();
           

            $employee_access = array_column($get_employees,'employee_id');

            $employee_depart_access = array_column($employee_department_can_ra,'employee_id');
            
            $help_desk_access = array_merge($employee_access,$employee_depart_access,[$help_desk[0]['help_desk_loged_by']]);
            
           if(!empty($help_desk_access) && in_array($login_user[0]['employee_id'],$help_desk_access)){
           
                $data = compact('title','help_desk','help_desk_status','help_desk_departments','help_desk_employees','employee_department_can_ra','departments','help_desk_employees_selected','employee_department_ids','get_employees');

                return $this->api_response_json(TRUE,"'My HelpDesk Detail",$data,200);
           }else{

                return $this->api_response_json(FALSE,"'My HelpDesk Detail","",200);
           }

        }catch(\Exception $e){

            $this->create_error_log_from_api('My HelpDesk Detail API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('My HelpDesk Detail API',  $request->header('Bearer-Token'));
            
        }

    }

    protected function update_help_desk_employees(Request $request){

        try{

            $validator = Validator::make($request->post(), [
                'hd_id'=> 'required|integer',
                'employee_id' => 'required|array',
                'employee_id.*' => 'required|integer',
                'department_name' => 'required|array',
                'department_name.*' => 'required|integer',
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
        
                $department_id = $request->post('department_name');
            
                $employee_ids = $request->post('employee_id');
                
                $process = '';
                
                $help_desk_employees_selected = HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id', $request->post('hd_id'))->pluck('hdf_employee_id')->toArray();

                HelpDeskForEmployee::where('tbl_help_desk_for_employees.hd_id',$request->post('hd_id'))->delete();

                foreach($employee_ids as $employee_id){
                    $help_desk_for_employee = new HelpDeskForEmployee();
                    $help_desk_for_employee->hdf_employee_id = $employee_id;
                    $help_desk_for_employee->hd_id = $request->post('hd_id');
                    $help_desk_for_employee->created_time = date('Y-m-d H:i:s');
                    $process = $help_desk_for_employee->save();
                
                }

                $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->whereIn('employee_id',$employee_ids)->whereIn('department_id',$department_id)->get(['employee_id'])->toArray();

                foreach($employees as $employee){
                    
                    if(!in_array($employee,$help_desk_employees_selected)){
                        $this->send_notification_api('New Complaint Added', ''.$login_user[0]['employee_name'].' Added a Complaint. Please click here to view.','my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no',$login_user[0]['employee_id']);   
                    }            
                }
        
                if($process == 1){
                    return $this->api_response_json(TRUE,"Help Desk Employee Updated Successfully.","",200);
                }else{
                    return $this->api_response_json(FALSE,"'Help Desk Employee Not Updated Successfully Try Again.","",200);
                }
            }   
          
        }catch(\Exception $e){

            $this->create_error_log_from_api('Help Desk Employee Updated API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Help Desk Employee Updated API',  $request->header('Bearer-Token'));
            
        }
    
    }

    protected function update_help_desk_departments(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'hd_id'=> 'required|integer',
                'department_name' => 'required|array',
                'department_name.*' => 'required|integer',
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
                
                $department_id = $request->post('department_name');
                
                if(!empty($request->post('department_name'))){

                    $employees_can_ra_depart = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->where('employee_is_manager',1)->whereIn('department_id',$department_id)->where('employee_id','!=',$login_user[0]['employee_id'])->get(['employee_id'])->toArray();
                    
                    if(empty($employees_can_ra_depart)){
                        return $this->api_response_json(FALSE,"There are no employees at manager level in this department.","",200);
                    }
                }

                $process = '';
            
                $help_desk_departments = HelpDeskForDepartment::where('tbl_help_desk_for_departments.hd_id',$request->post('hd_id'))->pluck('hdf_department_id')->toArray();

                HelpDeskForDepartment::where('tbl_help_desk_for_departments.hd_id',$request->post('hd_id'))->delete();

                foreach($department_id as $deprt_id){
                    $help_desk_for_deprt = new HelpDeskForDepartment();
                    $help_desk_for_deprt->hdf_department_id = $deprt_id;
                    $help_desk_for_deprt->hd_id  = $request->post('hd_id');
                    $help_desk_for_deprt->created_time = date('Y-m-d H:i:s');
                    $process = $help_desk_for_deprt->save();
                
                }

                foreach($employees_can_ra_depart as $employee){
                    if(!in_array($employee,$help_desk_departments)){
                        $this->send_notification_api('New Complaint Added', ''.$login_user[0]['employee_name'].' Added a Complaint. Please click here to view.','my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no',$login_user[0]['employee_id']);
                    } 
                }
        
                if($process == 1){
                    return $this->api_response_json(TRUE,"Help Desk Department Updated Successfully.","",200);
                }else{
                    return $this->api_response_json(FALSE,"Help Desk Department Updated Successfully Try Again.","",200);
                }
            }   

        }catch(\Exception $e){

            $this->create_error_log_from_api('Help Desk Department Updated Detail API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Help Desk Department Updated Detail API',  $request->header('Bearer-Token'));
            
        }
    
    }

    protected function ticket_status(Request $request){
       
      
        try{
            $validator = Validator::make($request->all(), [
                'hd_id'=> 'required|integer',
                'ticket_status' => 'required|integer|in:1,3,5',
                'department_name.*' => 'required|integer',
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            // only the ticket owner, the people / departments it is assigned to, or Admin may change its status
            $employee_id = $login_user[0]['employee_id'] ?? 0;
            $ticket_owner = HelpDesk::where('help_desk_id', $request->post('hd_id'))->value('help_desk_loged_by');
            $involved = $ticket_owner == $employee_id
                || HelpDeskForEmployee::where('hd_id', $request->post('hd_id'))->where('hdf_employee_id', $employee_id)->exists()
                || HelpDeskForDepartment::where('hd_id', $request->post('hd_id'))->where('hdf_department_id', $login_user[0]['employee_department'] ?? 0)->exists()
                || \App\Helpers\CustomHelper::has_role($employee_id, 'admin');
            if(empty($ticket_owner) || !$involved){
                return $this->api_response_json(FALSE,"You are not allowed to change this ticket.","",200);
            }

            $last_status = HelpDeskStatus::where('help_desk_id',$request->post('hd_id'))->orderBy('ticket_status_id','DESC')->get(['ticket_status'])->toArray();

            if(!empty($last_status) && $last_status[0]['ticket_status'] == $request->post('ticket_status')){
                return $this->api_response_json(FALSE,"Ticket already on same status.","",200);
            }

            $help_desk_status = new HelpDeskStatus();
            $help_desk_status->help_desk_id = $request->post('hd_id');
            $help_desk_status->ticket_status = $request->post('ticket_status');
            $help_desk_status->ticket_status_changed_by = $login_user[0]['employee_id'];
            $help_desk_status->ticket_status_created_time = date('Y-m-d H:i:s');
            $process =  $help_desk_status->save(); 

            if($process == 1){                

                if($request->post('ticket_status') == 1 || $request->post('ticket_status') == 3  || $request->post('ticket_status') == 5 ){
                    
                    $employees = Employees::join('tbl_help_desk_for_departments','tbl_help_desk_for_departments.hdf_department_id','=','tbl_employees.employee_department')->where('employee_isactive',1)->where('employee_is_manager',1)->where('hd_id',$request->post('hd_id'))->where('employee_id','!=', $login_user[0]['employee_id'])->get('employee_id')->toArray();
                
                    if(empty($employees)){
                        
                        $employees = Employees::join('tbl_help_desk_for_employees','tbl_help_desk_for_employees.hdf_employee_id','=','tbl_employees.employee_id')->where('employee_isactive',1)->where('employee_is_manager',1)->where('hd_id',$request->hd_id)->where('employee_id','!=', $login_user[0]['employee_id'])->get('employee_id')->toArray();
                       
                    }
    
                    foreach($employees as $employee){
                        
                        if($request->post('ticket_status') == 1){
                            $title = "Complaint ReOpen Again";
                            $notify_msg = "Same Complaint Again Added. Please click here to view.";
    
                            
                        }
                        elseif($request->post('ticket_status') == 3){
                            $title = "Complaint Rejected";
                            $notify_msg = "Rejected a Complaint. Please click here to view.";
    
                            
                        }
                        elseif($request->post('ticket_status') == 5){
                            $title = "Complaint Query Resolved";
                            $notify_msg = "Complaint Query Resolved. Please click here to view.";
                        }
    
                        $this->send_notification_api($title, ''.$login_user[0]['employee_name']. ' '. $notify_msg,'my-tickets?help-desk-id='.$employee['employee_id'] .'',$employee['employee_id'],NULL,NULL,'no',$login_user[0]['employee_id']);               
                    }
    
                }else{
                    $host_id = HelpDesk::where('help_desk_id',$request->post('hd_id'))->get('help_desk_loged_by')->toArray();
               
                    if($request->post('ticket_status') == 2){
                        $title = "Ticket Open";
                        $notify_msg = "Open Your Complaint Ticket. Please click here to view.";
                    }elseif($request->post('ticket_status') == 4){
                        
                        $title = "Complaint Query Resolved";
                        $notify_msg = "Complaint Query Resolved. Please click here to view.";
                    }

                    $this->send_notification_api($title, ''.$login_user[0]['employee_name']. ' '. $notify_msg,'my-tickets?help-desk-id='.$host_id[0]['help_desk_loged_by'] .'',$host_id[0]['help_desk_loged_by'],NULL,NULL,'no',$login_user[0]['employee_id']);                         
                }
               
                return $this->api_response_json(TRUE,"Ticket Status Updated Successfully.","",200);
            }else{
                
                return $this->api_response_json(FALSE,"Ticket Status not Updated Successfully Try Again.","",200);

            }

        }catch(\Exception $e){

            $this->create_error_log_from_api('Ticket Status Change HelpDesk Detail API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Ticket Status Change HelpDesk Detail API',  $request->header('Bearer-Token'));
            
        }
    }
}
