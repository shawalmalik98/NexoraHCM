<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\{Alert, Department, Employees, AlertStatus, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Jobs\{PushNotificationJob};

class AlertsController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "Alerts";

    protected function index(){
        
        try{
            $title = Self::MAIN_TITLE;
        
            $login_user = $this->get_login_user_detail();

            $alerts = Alert::leftJoin('tbl_alert_status', 'tbl_alerts.alert_id', '=', 'tbl_alert_status.alert_status_alert_id')
            ->select('tbl_alerts.*', DB::raw('COUNT(tbl_alert_status.alert_status_id) AS status_count'))
            ->groupBy('tbl_alerts.alert_id','tbl_alerts.title','tbl_alerts.message','tbl_alerts.department', 'tbl_alerts.location','tbl_alerts.scheduled_time','tbl_alerts.days','tbl_alerts.valid_dates','tbl_alerts.created_by','tbl_alerts.created_at','tbl_alerts.modified_at')
            ->get()
            ->toArray();

            $office_locations = OfficeLocation::get(['location_id','location_title'])->toArray();
           
            $departments = Department::all()->toArray();
            
            $send_data = compact('title','login_user','alerts','departments','office_locations');

            return view('alerts.alert', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('View All Alerts', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Alerts');  
        }
    }

    protected function add_alert(Request $request){
        
        try{  
           
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'department' => 'required',
                'message' => 'required',
                'scheduled_date_option' => 'required',
                'scheduled_date' => ($request->input('scheduled_date_option') == 'Custom Time' ) ? 'required|date_format:Y-m-d\TH:i' : '' ,
                'days' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                if($request->input('scheduled_date_option') == "Send Now"){
                    $scheduled_date  = date('Y-m-d H:i:00');
                }else{
                    $scheduled_date  = date('Y-m-d H:i:s',strtotime($request->input('scheduled_date')));
                }
            
                $valid_date = date('Y-m-d H:i:00', strtotime($scheduled_date.' +'.$request->input('days').'days'));

                if(!empty($request->input('location'))){
                    if(count($request->input('location'))==$request->input('loc_count')){  
                        $location = "*";
                    }else{
                        $location = implode(',', $request->input('location'));
                    }
                }

                if(count($request->input('department'))==$request->input('count')){
                    $department = "*";
                    $all_employees = Employees::where('employee_isactive', 1)->whereNotNull('employee_fcm_token')->pluck('employee_fcm_token')->toArray();
                }else{
                    $department = implode(',', $request->input('department'));
                    $all_employees = Employees::where('employee_isactive', 1)->whereNotNull('employee_fcm_token')
                    ->whereRaw("CHARINDEX(',' + CAST(tbl_employees.employee_department AS VARCHAR) + ',', ',' + ? + ',') > 0", [$department])
                    // ->whereRaw('FIND_IN_SET(employee_department, ?)', [$department])
                    ->pluck('employee_fcm_token')->toArray();
                }
                
                $new_alert = new Alert();
                $new_alert->title = $request->input('title');
                $new_alert->message = $request->input('message');
                $new_alert->department = $department;
                $new_alert->location = $location ?? 'null';
                $new_alert->scheduled_time = $scheduled_date;
                $new_alert->days = $request->input('days');
                $new_alert->valid_dates = $valid_date;
                $new_alert->created_by = session('user_session');
                $new_alert->created_at = date('Y-m-d H:i:s');
                $process = $new_alert->save();
                if($process == 1){
                    if(!empty($all_employees)){
                        foreach($all_employees as $employee_fcm){
                            if(!empty($employee_fcm)){
                                dispatch(new PushNotificationJob($employee_fcm, $request->input('title'), $request->input('message')));
                            }
                        }
                    }

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Alert added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Alert addition failed. Please try again after some time.",
                    ]);
                }
            }   
        }catch(\Exception $e){
            $this->create_error_log("Add alert", $e);
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
            ActivityTracker::track_activity('Add Alert'); 
        }

    }

    protected function update_alert(Request $request){
        
        try{

            $message = ['departments'=>'The department field is required.'];
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'departments' => 'required',
                'message' => 'required',
                'scheduled_date' => 'required|date_format:Y-m-d\TH:i' ,
                'days' => 'required',
            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                if($request->input('scheduled_date_option2') == "Send Now"){
                    $scheduled_date  = date('Y-m-d H:i:00');
                }else{
                    $scheduled_date = date('Y-m-d H:i:s',strtotime($request->input('scheduled_date')));
                }

                if(!empty($request->input('locations'))){
                    if(count($request->input('locations'))==$request->input('loc_count')){
                        $location = "*";
                    }else{
                        $location = implode(',', $request->input('locations'));
                    }
                }

                if(count($request->input('departments'))==$request->input('count')){
                    $department = "*";
                }else{
                    $department = implode(',', $request->input('departments'));
                }

                $valid_date = date('Y-m-d H:i:00', strtotime($scheduled_date.' +'.$request->input('days').'days'));

                $update_alert = Alert::find($request->input('alert_id'));

                if(!empty($update_alert)){

                    $update_alert->title = $request->input('title');
                    $update_alert->message = $request->input('message');
                    $update_alert->department = $department;
                    $update_alert->location = $location ?? 'null';
                    $update_alert->scheduled_time = $scheduled_date;
                    $update_alert->days = $request->input('days');
                    $update_alert->valid_dates = $valid_date;
                    $update_alert->created_at = date('Y-m-d H:i:s');
                    
                    $process = $update_alert->save();   

                    $delete_alert_status = AlertStatus::where('alert_status_alert_id',$request->input('alert_id'))->delete();

                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Alert updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Alert updation failed. Please try again after some time.",
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
            $this->create_error_log("Update Alert", $e);
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
            ActivityTracker::track_activity('Update Alert'); 
        }

    }

    protected function delete_alert(Request $request){

        try{

            $delete_alert_request = Alert::find($request->alert_id);

            if(!empty( $delete_alert_request)){
   
                $process = AlertStatus::where('alert_status_alert_id', $request->alert_id)->delete();
                $process = Alert::where('alert_id', $request->alert_id)->delete();

                if( $process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Alert has been deleted',
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
                    'msg' => 'Alert not found on this id.',
                ]);
            }         
        }
        catch(\Exception $e){
            $this->create_error_log("Delete alert", $e);
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
            ActivityTracker::track_activity('Delete alert');
        }
    }

    protected function alert_remove($alert_id){
        try{
           
            $new_alertstatus = new AlertStatus();
            $new_alertstatus->alert_status_alert_id = $alert_id;
            $new_alertstatus->alert_status_employee_id = session('user_session');
            $new_alertstatus->created_at = date('Y-m-d H:i:s');
            $process = $new_alertstatus->save();

            if($process == 1){
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Alert removed successfully.",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Alert removed failed. Please try again after some time.",
                ]);
            }
        }   
        catch(\Exception $e){
            $this->create_error_log("alert remove", $e);
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
            ActivityTracker::track_activity('alert remove');
        }
    }

    protected function alert_mark_as_read(Request $request){
   
        try{
            $alert_employees = AlertStatus::where('alert_status_alert_id',$request->alert_id)->pluck('alert_status_employee_id')->toArray();
         
            if(!empty($alert_employees)){
            
                $employees =  Employees::selectRaw('CONVERT(NVARCHAR(50), created_at, 106) as created_at,employee_code,employee_name')->join('tbl_alert_status', 'tbl_employees.employee_id', '=', 'tbl_alert_status.alert_status_employee_id')->where('alert_status_alert_id',$request->alert_id)->whereIn('employee_id',$alert_employees)->get()->toArray();
               
                return response()->json([
                    'data' => $employees,
                    'status' => 'TRUE'
                ]);
            }else{
                return response()->json([
                    'msg' =>'No record found',
                    'status' => 'FALSE'
                ]);
            }
        }   
        catch(\Exception $e){
            $this->create_error_log("Mark as Read Alert View", $e);
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
            ActivityTracker::track_activity('Mark as Read Alert View');
        }
        
    }

}
