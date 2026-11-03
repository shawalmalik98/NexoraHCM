<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Polling, Department, Employees, PollingOption, PollingStatus};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\Validator;

class PollingsController extends Controller
{   
    use LoginEmployee, LogGenerateTrait ,NotificationTrait;

    const TITLE_GLOBAL = "polling";

    protected function index(){

        try{
            $title = Self::TITLE_GLOBAL;
      
            $login_user = $this->get_login_user_detail();

            $departments = Department::all()->toArray();

            $pollings = Polling::get()->toArray();
           
            $send_data = compact('title','login_user','departments','pollings');

            return view('polling.polling', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View Polling', $e);
            $send_data = compact('title');
            return view('errors.500');
        }

        finally{
            ActivityTracker::track_activity('Access Polling Module');

        }

    }

    protected function polling_options_log(Request $request){
      
        try{

            $polling_status = PollingStatus::join('tbl_employees','tbl_employees.employee_id' , 'tbl_polling_status.polling_employee_id')
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('polling_option_id',  $request->input('polling_option_id'))->get(['polling_status_id','polling_option_id','polling_employee_id','polling_time','employee_id','employee_code','employee_name','department_name','designation_name'])->toArray();

            $get_polling_options_log = Polling::selectRaw('tbl_polling_options.polling_option_id, polling_options, count(polling_status_id) as vote')->join('tbl_polling_options', 'tbl_polling_options.polling_id', 'tbl_polling.polling_id')
            ->leftjoin('tbl_polling_status','tbl_polling_status.polling_option_id' , 'tbl_polling_options.polling_option_id')
            ->where('tbl_polling.polling_id',$request->input('polling_id'))
            ->groupBy('tbl_polling_options.polling_option_id')
            ->get()
            ->toArray();
        
            $polling_vote = array_sum(array_column($get_polling_options_log, 'vote'));
            
            $i = 0;
            foreach($get_polling_options_log as $get_polling_options){
                if($get_polling_options['vote'] != 0){
                    $get_polling_options_log[$i]['percentage'] = round($get_polling_options['vote']/$polling_vote * 100, 0);
                }else{
                    $get_polling_options_log[$i]['percentage'] = 0;
                }
                
            $i++;
            }

            if(!empty($get_polling_options_log)){

                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Get polling log",
                    'data' => $get_polling_options_log,
                    'employee' => $polling_status,
                ]);
            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "This poll has been deleted.",
                ]);
            }       
            
        }catch(\Exception $e){

            $this->create_error_log('polling options Log', $e);
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

            ActivityTracker::track_activity('polling options Log');

        }

    }

    protected function polling_employee(Request $request){
    
        try{    

            $login_user = $this->get_login_user_detail();

            $polling_option_pluck = PollingOption::join('tbl_polling', 'tbl_polling.polling_id', 'tbl_polling_options.polling_id')
            ->where('tbl_polling_options.polling_id', $request->polling_id)->where('polling_status', '=', 1)->pluck('polling_option_id')->toArray();

            if(!empty($polling_option_pluck)){

                if(in_array($request->input('polling_option_id'), $polling_option_pluck)){

                    $check_polling_department = polling::where('polling_id', $request->polling_id)->get('polling_department')->toArray();

                    if($check_polling_department[0]['polling_department'] == "*" ){
                        $department_check = ["*"];
                    }else{
                        $department_check = explode(',', $check_polling_department[0]['polling_department']);
                    } 

                    if($department_check[0] == '*' || in_array($login_user[0]['employee_department'], $department_check)){

                        // running: started (a vote in the same second as the poll counts) and not ended
                        $pollings = Polling::where('created_at', '<=', date('Y-m-d H:i:s'))->where('polling_duration', '>', date('Y-m-d H:i:s'))
                        ->where('polling_id', $request->polling_id)
                        ->get('polling_id')->toArray();

                        if(!empty($pollings)){

                            $polling_status = PollingStatus::whereIn('polling_option_id', $polling_option_pluck)->where('polling_employee_id', $login_user[0]['employee_id'])->get('polling_status_id')->toArray();
                    
                            if(empty($polling_status)){
                                $Polling_status = new PollingStatus();
                                $Polling_status->polling_option_id = $request->input('polling_option_id');
                                $Polling_status->polling_employee_id =  $login_user[0]['employee_id'];
                                $Polling_status->polling_time = date('Y-m-d H:i:s');

                                $process = $Polling_status->save();   

                                if($process == 1){

                                    $get_polling_employee_log = Polling::selectRaw('polling_title, polling_options, count(polling_status_id) as vote')->join('tbl_polling_options', 'tbl_polling_options.polling_id', 'tbl_polling.polling_id')
                                    ->leftjoin('tbl_polling_status','tbl_polling_status.polling_option_id' , 'tbl_polling_options.polling_option_id')
                                    ->where('tbl_polling.polling_id',$request->input('polling_id'))
                                    ->groupBy('tbl_polling_options.polling_option_id')
                                    ->get()
                                    ->toArray();
                        
                                    $polling_vote = array_sum(array_column($get_polling_employee_log, 'vote'));
                                    
                                    $i = 0;
                                    foreach($get_polling_employee_log as $get_polling_options){
                                        if($get_polling_options['vote'] != 0){
                                            $get_polling_employee_log[$i]['percentage'] = round($get_polling_options['vote']/$polling_vote * 100, 0);
                                        }else{
                                            $get_polling_employee_log[$i]['percentage'] = 0;
                                        }
                                        
                                    $i++;
                                    }
                                    
                                    return response()->json([
                                        'status' => 'TRUE',
                                        'msg' => "polling successfull",
                                        'data' => $get_polling_employee_log,
                                    ]);
                                    
                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => "Poll status Failed. Please try again after some time.",
                                    ]);
                                }
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => 'You have already voted on this poll.',
                                ]);

                            }  
                        }else{

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "This poll has been ended.",
                            ]);
                        }      
                    }else{

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This poll has been deleted.",
                        ]);

                    }      
                }else{

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Option doesn't exist.",
                    ]);
                }   
            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "This poll has been deleted.",
                ]);
            }     
            
        }catch(\Exception $e){

            $this->create_error_log('polling status Log', $e);
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

            ActivityTracker::track_activity('polling options Log');

        }

    }

    protected function polling_detail($polling_id){

        try{
            $title = SELF::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

            $polling_status = PollingStatus::join('tbl_polling_options', 'tbl_polling_options.polling_option_id', 'tbl_polling_status.polling_option_id')->where('tbl_polling_options.polling_id', $polling_id)->count();
            
            $polling_option_check = PollingStatus::selectRaw('polling_options, count(tbl_polling_options.polling_option_id) as totalvote, tbl_polling_options.polling_option_id')->join('tbl_polling_options', 'tbl_polling_options.polling_option_id', 'tbl_polling_status.polling_option_id')->where('tbl_polling_options.polling_id', $polling_id)->groupBy('tbl_polling_options.polling_option_id')->orderByRaw('count(tbl_polling_options.polling_option_id) DESC')->get()->toArray();

            $polling_options = PollingOption::where('polling_id',$polling_id)->get()->toArray();
           
            $polling_details = Polling::where('tbl_polling.polling_id',$polling_id)->get()->toArray();
        
            $send_data = compact('title', 'login_user', 'polling_options', 'polling_details','polling_status','polling_option_check');

            if(empty($polling_details)){

                return abort(404);

            }
            return view('polling.polling-detail', $send_data);
            
        }
        catch(\Exception $e){
            $this->create_error_log('View Polling Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{

            ActivityTracker::track_activity('View Polling Detail');

           
        }
    }

    protected function add_polling(Request $request){
        
        try{  
           
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:100',
                'department' => 'required',
                'options.*' => 'required|max:50',
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
            
                $valid_date = date('Y-m-d H:i:00', strtotime($request->input('polling_duration').' +'.$request->input('days').'days'));

                if(count($request->input('department'))==$request->input('count')){
                    $department = "*";
                }else{
                    $department = implode(',', $request->input('department'));
                }

                $new_Polling = new Polling();
                $new_Polling->polling_title = $request->input('title');
                $new_Polling->polling_department = $department;
                $new_Polling->polling_days = $request->input('days');
                $new_Polling->polling_duration = $valid_date;
                $new_Polling->polling_created_by = session('user_session');
                $new_Polling->polling_status = 1;
                $new_Polling->created_at = date('Y-m-d H:i:s');

                $process = $new_Polling->save();   

                if($process == 1){

                    foreach($request->input('options') as $options){
                        $new_Polling_option = new PollingOption();
                        $new_Polling_option->polling_id =  $new_Polling->polling_id;
                        $new_Polling_option->polling_options = $options;
                        $new_Polling_option->save(); 
                    }
                    
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Poll has been created successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Poll creation failed. Please try again after some time.",
                    ]);
                }
            }   
        }catch(\Exception $e){
            $this->create_error_log("Add Poll", $e);
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
            ActivityTracker::track_activity('Add Poll'); 
        }

    }

    protected function update_polling(Request $request){
        
        try{  
           
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:100',
                'departments' => 'required',
                'options.*' => 'required|max:50',
                'days' => 'required',
                'polling_status' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
            
                $valid_date = date('Y-m-d H:i:00', strtotime($request->input('polling_duration').' +'.$request->input('days').'days'));

                if(count($request->input('departments'))==$request->input('count')){
                    $department = "*";
                }else{
                    $department = implode(',', $request->input('departments'));
                } 
                
                $check_polling_status = Polling::where('polling_id', $request->polling_id)->get(['polling_status','polling_department'])->toArray();

                $polling_option_pluck = PollingOption::where('polling_id', $request->polling_id)->pluck('polling_option_id')->toArray();

                $polling_status = PollingStatus::whereIn('polling_option_id', $polling_option_pluck)->get('polling_status_id')->toArray();

                if(empty($polling_status)){

                    $update_Polling = Polling::find($request->input('polling_id'));

                    if(!empty($update_Polling)){

                        $update_Polling->polling_title = $request->input('title');
                        $update_Polling->polling_department = $department;
                        $update_Polling->polling_days = $request->input('days');
                        $update_Polling->polling_duration = $valid_date;
                        $update_Polling->polling_created_by = session('user_session');
                        $update_Polling->polling_status = $request->input('polling_status');
                        $update_Polling->created_at = date('Y-m-d H:i:s');

                        $process = $update_Polling->save();   

                        if($process == 1){

                            PollingOption::where('polling_id', $request->input('polling_id'))->delete();

                            foreach($request->input('options') as $options){

                                $update_Polling_option = new PollingOption();
                                $update_Polling_option->polling_id =  $request->input('polling_id');
                                $update_Polling_option->polling_options = $options;
                                $update_Polling_option->save(); 
                            }
                            
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Poll updated successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Poll update failed. Please try again after some time.",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Poll not found on this id.',
                        ]);
                    }   
                }else{

                    if($check_polling_status[0]['polling_status'] != $request->input('polling_status') || $check_polling_status[0]['polling_department'] != $department){   

                        $update_Polling = Polling::find($request->input('polling_id'));
                        $update_Polling->polling_department = $department;
                        $update_Polling->polling_status = $request->input('polling_status');
                        $process = $update_Polling->save();  

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Poll has been updated successfully.",
                        ]);

                    }else{

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'You can\'t updated this poll now, employee(s) have voted.',
                        ]);
                        
                    }
                }  
            }   
        }catch(\Exception $e){
            $this->create_error_log("Update Poll", $e);
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
            ActivityTracker::track_activity('Update Poll'); 
        }

    }

    protected function delete_polling(Request $request){

        try{

            $get_poll = Polling::find($request->polling_id);

            if(!empty($get_poll)){

                $polling_option_pluck = PollingOption::where('polling_id', $request->polling_id)->pluck('polling_option_id')->toArray();

                $polling_status = PollingStatus::whereIn('polling_option_id', $polling_option_pluck)->get('polling_status_id')->toArray();

                if(empty($polling_status)){

                    $polling_option_delete = PollingOption::where('polling_id', $request->polling_id)->delete();
                    $process = Polling::where('polling_id', $request->polling_id)->delete();

                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Poll has been deleted',
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
                        'msg' => 'You can\'t delete this poll now, employee(s) have voted.',
                    ]);

                }
                        
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Poll not found on this id.',
                ]);
            }         
        }
        catch(\Exception $e){
            $this->create_error_log("Delete Polling", $e);
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
            ActivityTracker::track_activity('Delete Polling');
        }
    }
}
