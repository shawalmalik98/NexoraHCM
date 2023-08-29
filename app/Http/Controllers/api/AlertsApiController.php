<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{AlertStatus,Alert,Department};

use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait};
use App\Helpers\{ActivityTracker, CustomHelper};

class AlertsApiController extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait;

    const MAIN_TITLE = "Alerts";

    protected function index(Request $request){
        
        try{
            
            $title = Self::MAIN_TITLE;

            $alerts = Alert::join('tbl_employees','tbl_employees.employee_id','tbl_alerts.created_by')->get(['alert_id','title','message','department','scheduled_time','days','valid_dates','employee_name'])->toArray();
            $i = 0;
            foreach($alerts as $alert){

                $department_name = explode("," ,$alert['department']);
                $count = 0;
                $department_name_labels = '';
                foreach ( $department_name as $department ){

                    $count ++ ;

                    $department_name_labels .= CustomHelper::get_department_name($department);

                    if( $count  != count($department_name)){

                        $department_name_labels .= ','; 

                    }

                }
                $alerts[$i]['department'] = $department_name_labels;
                $i++;
            }
                

            $departments = Department::all(['department_id','department_name'])->toArray();
          
            $data = compact('title','alerts','departments');

            return $this->api_response_json(TRUE,"All Alerts",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View All Alerts API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('View All Alerts API',  $request->header('Bearer-Token'));
            
        }
    }

    protected function add_alert(Request $request){
      
        try{  
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                'title' => 'required',
                'department' => 'required',
                'message' => 'required',
                'scheduled_date_option' => 'required',
                'scheduled_date' => ($request->post('scheduled_date_option') == 'Custom Time' ) ? 'required|date_format:Y-m-d\TH:i' : '' ,
                'days' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);

                }
            }else {

                if($request->post('scheduled_date_option') == "Send Now"){
                    $scheduled_date  = date('Y-m-d H:i:00');
                }else{
                    $scheduled_date  = date('Y-m-d H:i:s',strtotime($request->post('scheduled_date')));
                }
            
                $valid_date = date('Y-m-d H:i:00', strtotime($scheduled_date.' +'.$request->post('days').'days'));

                $departments_count = Department::get(['department_id'])->count();

                if(count($request->post('department')) == $departments_count){
                    $department = "*";
                }else{
                    $department = implode(',', $request->post('department'));
                }

                $new_alert = new Alert();
                $new_alert->title = $request->post('title');
                $new_alert->message = $request->post('message');
                $new_alert->department = $department;
                $new_alert->scheduled_time = $scheduled_date;
                $new_alert->days = $request->post('days');
                $new_alert->valid_dates = $valid_date;
                $new_alert->created_by = $login_user[0]['employee_id'];
                $new_alert->created_at = date('Y-m-d H:i:s');
                
                $process = $new_alert->save();   
                if($process == 1){

                    return $this->api_response_json(TRUE,"Alert Added Successfully.","",200);

                }else{

                    return $this->api_response_json(FALSE,"Alert Addition Failed. Please try again after some time.","",200);

                }
            }   
        }catch(\Exception $e){

            $this->create_error_log_from_api('Add alert API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Add Alert API',  $request->header('Bearer-Token'));
            
        }

    }


    protected function update_alert(Request $request){
    
        try{

            $message = ['departments'=>'The department field is required.'];
            $validator = Validator::make($request->all(), [
                'alert_id' => 'required',
                'title' => 'required',
                'departments' => 'required',
                'message' => 'required',
                'scheduled_date_option' => 'required',
                'scheduled_date' => ($request->post('scheduled_date_option') == 'Custom Time' ) ? 'required|date_format:Y-m-d\TH:i' : '' ,
                'days' => 'required',
            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);

                }
            }else {

                if($request->post('scheduled_date_option') == "Send Now"){
                    $scheduled_date  = date('Y-m-d H:i:00');
                }else{
                    $scheduled_date = date('Y-m-d H:i:s',strtotime($request->post('scheduled_date')));
                }

                $departments_count = Department::get(['department_id'])->count();

                if(count($request->post('departments'))==$departments_count){
                    $department = "*";
                }else{
                    $department = implode(',', $request->post('departments'));
                }

                $valid_date = date('Y-m-d H:i:00', strtotime($scheduled_date.' +'.$request->post('days').'days'));

                $update_alert = Alert::find($request->post('alert_id'));

                if(!empty( $update_alert)){

                    $update_alert->title = $request->post('title');
                    $update_alert->message = $request->post('message');
                    $update_alert->department = $department;
                    $update_alert->scheduled_time = $scheduled_date;
                    $update_alert->days = $request->post('days');
                    $update_alert->valid_dates = $valid_date;
                    $update_alert->created_at = date('Y-m-d H:i:s');
                    
                    $process = $update_alert->save();   

                    $delete_alert_status = AlertStatus::where('alert_status_alert_id',$request->post('alert_id'))->delete();

                    if($process == 1){

                        return $this->api_response_json(TRUE,"Alert Updated Successfully.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Alert updation Failed. Please try again after some time.","",200);

                    }
                }else{
                    
                    return $this->api_response_json(FALSE,"Request not found on this ID.","",200);

                }
            }           
        }catch(\Exception $e){

            $this->create_error_log_from_api('Update Alert API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Update Alert API',  $request->header('Bearer-Token'));
        }

    }

    protected function delete_alert(Request $request){

        try{

            $validator = Validator::make($request->post(), [
                'alert_id' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);

                }
            }else{

                $delete_alert_request = Alert::find($request->post('alert_id'));

                if(!empty( $delete_alert_request)){
                    
                    $process =  AlertStatus::where('alert_status_alert_id', $request->post('alert_id'))->delete();
                    $process =  Alert::where('alert_id', $request->post('alert_id'))->delete();

                    if( $process == 1){
                        
                        return $this->api_response_json(TRUE,"Alert request has been deleted.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                        
                    }
                            
                }else{

                    return $this->api_response_json(FALSE,"Request not found on this ID.","",200);

                }  
            }       
        }
        catch(\Exception $e){

            $this->create_error_log_from_api('Delete alert API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }
        finally{

            ActivityTracker::track_activity_from_api('Delete Alert API',  $request->header('Bearer-Token'));
        }
    }

    protected function alert_remove(Request $request){
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $new_alertstatus = new AlertStatus();
            $new_alertstatus->alert_status_alert_id = $request->post('alert_id');
            $new_alertstatus->alert_status_employee_id = $login_user[0]['employee_id'];
            $new_alertstatus->created_at = date('Y-m-d H:i:s');
            $process = $new_alertstatus->save();

            if($process == 1){

                return $this->api_response_json(TRUE,"Alert Removed Successfully.","",200);

            }else{

                return $this->api_response_json(FALSE,"Alert removed Failed. Please try again after some time.","",200);

            }
        }   
        catch(\Exception $e){

            $this->create_error_log_from_api('Alert Remove API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }
        finally{

            ActivityTracker::track_activity_from_api('Alert Remove API',  $request->header('Bearer-Token'));
        }
    }
}
