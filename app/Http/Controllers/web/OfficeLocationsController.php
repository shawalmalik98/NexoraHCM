<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use App\Models\{OfficeLocation, Employees, RoomScreen, EmployeeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\DB;

class OfficeLocationsController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = 'manage-locations';

    protected function index(){

        try{
            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $locations = OfficeLocation::selectRaw('tbl_office_locations.*, COUNT(e.employee_id) as total_employees')
            ->leftJoin('tbl_employees_location as el', 'tbl_office_locations.location_id', '=', 'el.emp_loc_loc_id')
            ->leftJoin('tbl_employees as e', 'el.emp_loc_employee_id', '=', 'e.employee_id')
            ->groupBy(
                'tbl_office_locations.location_id', 'tbl_office_locations.location_title',
                'tbl_office_locations.location_alt_id', 'tbl_office_locations.location_address',
                'tbl_office_locations.location_latitude', 'tbl_office_locations.location_longitude',
                'tbl_office_locations.location_attendance_radius', 'tbl_office_locations.location_added_at',
                'tbl_office_locations.location_updated_at'
            )
            ->orderByRaw('COUNT(e.employee_id) DESC')
            ->get()
            ->toArray();        
            
            $get_employee_location_id = array_column($locations, 'location_id');

            $employee_location_id = Employees::whereIn('employee_location', $get_employee_location_id)->pluck('employee_location')->toArray();

            $send_data = compact('title' , 'login_user', 'locations','employee_location_id','locations');

            return view('office-locations.location')->with($send_data);

        
        }catch(\Exception $e){

            $this->create_error_log('View All Locations', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Locations');
         
        }
    }

    protected function add_location(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'location_title' => ['required','unique:tbl_office_locations,location_title'],
                'location_address' => ['required'],
                'location_allowed_range' => ['required','numeric','between:0,5000'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $new_location = new OfficeLocation();
                $new_location->location_title = $request->input('location_title');
                $new_location->location_address = $request->input('location_address');
                $new_location->location_latitude = $request->input('location_latitude');
                $new_location->location_longitude = $request->input('location_longitude');
                $new_location->location_attendance_radius = $request->input('location_allowed_range');
                $new_location->location_added_at = date('d-m-Y H:i:s');
                $new_location->location_updated_at = '00-00-00 00:00:00';
                $process = $new_location->save();   
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Location added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Location addition failed. Please try again after some time.",
                    ]);
                }
            }   
             
        }catch(\Exception $e){
            $this->create_error_log("Add Location", $e);
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
            ActivityTracker::track_activity('Add Location'); 
        }

    }
    
    protected function update_location(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'location_title' => ['required','unique:tbl_office_locations,location_title,'.$request->input('location_id').',location_id'],
                'location_address' => ['required'],
                'location_allowed_range' => ['required','numeric','between:0,5000'],
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $update_location = OfficeLocation::find($request->input('location_id'));

                if(!empty($update_location)){
                    
                    $update_location->location_title = $request->input('location_title');
                    $update_location->location_address = $request->input('location_address');
                    $update_location->location_latitude = $request->input('latitude');
                    $update_location->location_longitude = $request->input('longitude');
                    $update_location->location_attendance_radius = $request->input('location_allowed_range');
                    $update_location->location_updated_at = date('d-m-Y H:i:s');
                    $process = $update_location->save();   
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Location updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Location update failed. Please try again after some time.",
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
            $this->create_error_log("Update Location", $e);
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
            ActivityTracker::track_activity('Update Location'); 
        }

    }

    protected function delete_location_request(Request $request_data){
      
        try{
            
            $delete_location_request = OfficeLocation::find($request_data->location_id);

            if(!empty($delete_location_request)){
                
                $check_if_employees_exist_on_location = Employees::where('employee_location', $request_data->location_id)->limit(1)->get(['employee_id'])->toArray();
                if(empty($check_if_employees_exist_on_location)){
                    OfficeLocation::where('location_id', $request_data->location_id)->delete();
                    RoomScreen::where('room_location_id',$request_data->location_id)->delete();
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Location has been deleted',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "This Location can't be delete. Multiple employees registered on this location.",
                    ]);
                }
               
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Request not found on this id.',
                ]);
            }         
        }catch(\Exception $e){
            $this->create_error_log('Delete Location Request', $e);
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
            ActivityTracker::track_activity('Delete Location Request');
            
        }
    }
    
    protected function upload_employee_location_record(Request $request){

    
        set_time_limit(18000);
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv'
        ]);
        if($validator->fails()) {
            foreach($validator->errors()->all() as $error){
                return response()->json([
                    'msg' => $error,
                    'status' => 'FALSE',
                    'validation' => 'TRUE',
                ]);
            }
        }else{
            $filename = $request->file('file')->getPathName();
            
            if($request->file('file')->getSize() > 0)
            {
                $error = 0;
                $success = 0;
                $file = fopen($filename, "r");
                $file2 = fopen($filename, "r");
                $error_logs = array();
                $count_row = 0;

                EmployeeLocation::truncate();
                
                
                // if(empty($error_msg) || $request->input('Force_Approved') == 'TRUE'){
                    $arrays = [];
                    $count_row = 0;
                    while (($getData = fgetcsv($file2)) !== FALSE)
                    {
            
                        // if($count_row != 0){

                            $location_id = iconv('ISO-8859-1', 'UTF-8', $getData[1]);//$getData[1];
                            $employeeCode = iconv('ISO-8859-1', 'UTF-8', $getData[2]);//$getData[2];

                            $get_employee_data = Employees::where('employee_code' , $employeeCode)->get(['employee_id'])->toArray();
                           
                            $location_data = OfficeLocation::where('location_alt_id', $location_id)->get('location_id')->toArray();
                            
                            if(!empty($get_employee_data) && !empty($location_data)){
                                
                                $new_employee_location = new EmployeeLocation();
                                $new_employee_location->emp_loc_employee_id = $get_employee_data[0]['employee_id'];
                                $new_employee_location->emp_loc_loc_id = $location_data[0]['location_id'];
                                $new_employee_location->created_at = date('Y-m-d H:i:s');
                                $new_employee_location->save();
                                
                            }
                            $success++;
                        // }

                    }
                    $count_row ++;
                   
                    fclose($file2);
                    
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Upload Location record',
                        'error_logs' => $error_logs
                    ]);
                // }
                // else{
                //     $msg = "You have $success valid records and $error invalid records.";

                //     return response()->json([
                //         'status' => 'FALSE',
                //         'msg' =>  'Location record upload failed '."<br>".$msg,
                //         'error_logs' => $error_logs
                //     ]);
                // }

            }


        }
    
    }

}
