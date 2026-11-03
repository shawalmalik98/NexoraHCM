<?php

namespace App\Http\Controllers\web;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use App\Models\{OfficeLocation, Employees, RoomScreen, EmployeeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;

class OfficeLocationsController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = 'manage-locations';

    protected function index(Request $request){

        try{
            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $search = trim((string) $request->input('search'));

            $pagination = OfficeLocation::selectRaw('tbl_office_locations.*, COUNT(e.employee_id) as total_employees')
            ->leftJoin('tbl_employees_location as el', 'tbl_office_locations.location_id', '=', 'el.emp_loc_loc_id')
            ->leftJoin('tbl_employees as e', 'el.emp_loc_employee_id', '=', 'e.employee_id')
            ->when($search !== '', function($query) use($search){
                $query->where(function($query) use($search){
                    $query->where('tbl_office_locations.location_title','like','%'.$search.'%')
                        ->orWhere('tbl_office_locations.location_address','like','%'.$search.'%')
                        ->orWhere('tbl_office_locations.distributor_code','like','%'.$search.'%');
                });
            })
            ->groupBy(
                'tbl_office_locations.location_id', 'tbl_office_locations.location_title',
                'tbl_office_locations.location_alt_id', 'tbl_office_locations.distributor_code',
                'tbl_office_locations.location_address',
                'tbl_office_locations.location_latitude', 'tbl_office_locations.location_longitude',
                'tbl_office_locations.location_attendance_radius', 
                'tbl_office_locations.location_added_at',
                'tbl_office_locations.location_updated_at',
                // 'tbl_office_locations.last_updated_by'
            )
            ->orderByRaw('COUNT(e.employee_id) DESC')
            ->orderBy('tbl_office_locations.location_id')
            ->paginate(CustomHelper::per_page())->withQueryString();

            $locations = $pagination->getCollection()->toArray();

            $get_employee_location_id = array_column($locations, 'location_id');

            $employee_location_id = Employees::whereIn('employee_location', $get_employee_location_id)->distinct()->pluck('employee_location')->toArray();

            $send_data = compact('title' , 'login_user', 'locations','employee_location_id','locations','pagination');

            if($request->download == 'TRUE'){

                $locations = OfficeLocation::selectRaw("
                    location_id as 'Location ID',
                    location_title as 'Location Title',
                    location_address as 'Location Address',
                    location_latitude as 'Latitude',
                    location_longitude as 'Longitude',
                    location_attendance_radius as 'Allowed Range (meters)',
                    location_added_at as 'Location Created At',
                    COUNT(e.employee_id) as 'Tagged Employees'
                ")
                ->leftJoin('tbl_employees_location as el', 'tbl_office_locations.location_id', '=', 'el.emp_loc_loc_id')
                ->leftJoin('tbl_employees as e', 'el.emp_loc_employee_id', '=', 'e.employee_id')
                ->groupBy(
                    'location_id',
                    'location_title',
                    'location_address',
                    'location_latitude',
                    'location_longitude',
                    'location_attendance_radius',
                    'location_added_at'
                )
                ->orderByRaw('COUNT(e.employee_id) DESC')
                ->get()
                ->toArray();

                $header_style = (new Style())->setFontBold();

                return (new FastExcel($locations))->headerStyle($header_style)->download('Office-Locations.xlsx');
            }else{
                return view('office-locations.location')->with($send_data);
            }
        
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
                $new_location->distributor_code = $request->input('distributor_code');
                $new_location->location_added_at = date('Y-m-d H:i:s');
                $new_location->location_updated_at = '00-00-00 00:00:00';
                $new_location->last_updated_by = session('user_session');
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
                    $update_location->distributor_code = $request->input('distributor_code');
                    $update_location->location_updated_at = date('Y-m-d H:i:s');
                    $update_location->last_updated_by = session('user_session');
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
            ActivityTracker::track_activity('Delete Location Request');
            
        }
    }
    
    // protected function update_employee_location_record(Request $request){

    
    //     set_time_limit(18000);
    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|file|mimes:csv'
    //     ]);
    //     if($validator->fails()) {
    //         foreach($validator->errors()->all() as $error){
    //             return response()->json([
    //                 'msg' => $error,
    //                 'status' => 'FALSE',
    //                 'validation' => 'TRUE',
    //             ]);
    //         }
    //     }else{
    //         $filename = $request->file('file')->getPathName();
            
    //         if($request->file('file')->getSize() > 0)
    //         {
    //             $error = 0;
    //             $success = 0;
    //             $file = fopen($filename, "r");
    //             $file2 = fopen($filename, "r");
    //             $error_logs = array();
    //             $count_row = 0;

    //             // EmployeeLocation::truncate();
                
                
    //             // if(empty($error_msg) || $request->input('Force_Approved') == 'TRUE'){
    //                 $arrays = [];
    //                 $count_row = 0;
    //                 while (($getData = fgetcsv($file2)) !== FALSE)
    //                 {
            
    //                     // if($count_row != 0){
                            
    //                         $employeeCode = preg_replace('/^[\x00-\x1F\x80-\xFF]+/u', '', iconv('ISO-8859-1', 'UTF-8', $getData[0]));//$getData[0];
    //                         $location_title = preg_replace('/^[\x00-\x1F\x80-\xFF]+/u', '', iconv('ISO-8859-1', 'UTF-8', $getData[1]));//$getData[1];
    //                         $location_latitude = preg_replace('/^[\x00-\x1F\x80-\xFF]+/u', '', iconv('ISO-8859-1', 'UTF-8', $getData[2]));//$getData[3];
            
    //                         // if($distributor_code != '' || $distributor_code != '0' || $distributor_code != NULL){
    //                             $get_employee_data = Employees::where('employee_code' , $employeeCode)->get(['employee_id'])->toArray();
                           
    //                             $location_data = OfficeLocation::where('location_title', $location_title)->where('location_latitude', $location_latitude)->get('location_id')->toArray();
    //                             if(!empty($get_employee_data)){
                                    
    //                                 // EmployeeLocation::where('emp_loc_employee_id', $get_employee_data[0]['employee_id'])->delete();
    //                                 if(!empty($get_employee_data) && !empty($location_data)){
                                        
    //                                     $new_employee_location = new EmployeeLocation();
    //                                     $new_employee_location->emp_loc_employee_id = $get_employee_data[0]['employee_id'];
    //                                     $new_employee_location->emp_loc_loc_id = $location_data[0]['location_id'];
    //                                     $new_employee_location->created_at = date('Y-m-d H:i:s');
    //                                     $new_employee_location->save();
                                        
    //                                 }
    //                                 $success++;
                                    
    //                             }
    //                         // }

                            
    //                     // }

    //                 }
    //                 $count_row ++;
                   
    //                 fclose($file2);
                    
    //                 return response()->json([
    //                     'status' => 'TRUE',
    //                     'msg' => 'Upload Location record',
    //                     'error_logs' => $error_logs
    //                 ]);
    //             // }
    //             // else{
    //             //     $msg = "You have $success valid records and $error invalid records.";

    //             //     return response()->json([
    //             //         'status' => 'FALSE',
    //             //         'msg' =>  'Location record upload failed '."<br>".$msg,
    //             //         'error_logs' => $error_logs
    //             //     ]);
    //             // }

    //         }


    //     }
    
    // }

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

                // OfficeLocation::truncate();
                
                
                // if(empty($error_msg) || $request->input('Location_Approved') == 'TRUE'){
                    $arrays = [];
                    $count_row = 0;
                    while (($getData = fgetcsv($file2)) !== FALSE)
                    {
            
                        // if($count_row != 0){

                            // $dist_code = iconv('ISO-8859-1', 'UTF-8', $getData[0]);//$getData[1];
                            $dist_name = iconv('ISO-8859-1', 'UTF-8', $getData[0]);//$getData[2];
                            $lat = iconv('ISO-8859-1', 'UTF-8', $getData[1]);//$getData[3];
                            $long = iconv('ISO-8859-1', 'UTF-8', $getData[2]);//$getData[4];
                            $radius = '999';

                            // if(1 == 1){
                                
                                $new_employee_location = new OfficeLocation();
                                $new_employee_location->location_title = $dist_name;
                                $new_employee_location->location_address = $dist_name;
                                $new_employee_location->location_latitude = $lat;
                                $new_employee_location->location_longitude = $long;
                                // $new_employee_location->distributor_code = $dist_code;
                                $new_employee_location->location_attendance_radius = $radius;
                                $new_employee_location->location_added_at = date('Y-m-d H:i:s');
                                $new_employee_location->save();
                                

                                // $success++;
                            // }
                            // 
                        //  }

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
