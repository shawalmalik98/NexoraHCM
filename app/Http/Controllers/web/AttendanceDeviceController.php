<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Models\{OfficeLocation,AttendanceDevice};
use App\Helpers\ActivityTracker;

class AttendanceDeviceController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const TITLE_GLOBAL = 'manage-attendance-devices';

    protected function index(){
        try{
            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $attendance_devices = AttendanceDevice::join('tbl_office_locations','tbl_attendance_devices.office_location_id','=','tbl_office_locations.location_id')->get(['attendance_device_id','device_name','device_port','device_message','device_status','location_title','location_id','office_location_id'])->toArray();

            $locations = OfficeLocation::get()->toArray();

            $send_data = compact('title' , 'login_user', 'attendance_devices', 'locations');

            return view('attendance.attendance-devices', $send_data);
            
        }catch(\Exception $e){
            $this->create_error_log('View All Attendance Devices', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View All Attendance Devices');
        
        }
    }

    protected function add_device(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'location_title' => 'required',
                'device_title' => 'required',
                'device_message' => 'required',
                'device_status' => 'required',
                'device_port' => ['required','unique:tbl_attendance_devices,device_port'],

                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $new_device = new AttendanceDevice();
                $new_device->office_location_id	 = $request->input('location_title');
                $new_device->device_name = $request->input('device_title');
                $new_device->device_port = $request->input('device_port');
                $new_device->device_message = $request->input('device_message');
                $new_device->device_status = $request->input('device_status');
                $process = $new_device->save();   
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Attendance device added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Attendance device addition failed. Please try again after some time.",
                    ]);
                }
            }   
             
        }catch(\Exception $e){
            $this->create_error_log("Add Attendance Device", $e);
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
            ActivityTracker::track_activity('Add Attendance Device'); 
        }

    }

    protected function update_device(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'location_title' => 'required',
                'device_title' => 'required',
                'device_message' => 'required',
                'device_status' => 'required',
                'device_port' => ['required','unique:tbl_attendance_devices,device_port, ' .$request->input('attendance_device_id'). ' ,attendance_device_id'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $update_device = AttendanceDevice::find($request->input('attendance_device_id'));

                if(!empty($update_device)){
                    
                    $update_device->office_location_id	 = $request->input('location_title');
                    $update_device->device_name = $request->input('device_title');
                    $update_device->device_port = $request->input('device_port');
                    $update_device->device_message = $request->input('device_message');
                    $update_device->device_status = $request->input('device_status');
                    $process = $update_device->save();   
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Attendance device updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Attendance device update failed. Please try again after some time.",
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
            $this->create_error_log("Update Attendance Device", $e);
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
            ActivityTracker::track_activity('Update Attendance Device'); 
        }

    }

    protected function delete_device(Request $request){
      
        try{
            
            $delete_device = AttendanceDevice::find($request->attendance_device_id);

            if(!empty($delete_device)){
   
                $process = AttendanceDevice::where('attendance_device_id', $request->attendance_device_id)->delete();
                if( $process == 1){
                
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Attendance device has been deleted',
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
                    'msg' => 'Request not found on this id.',
                ]);
            }         
        }catch(\Exception $e){
            $this->create_error_log('Delete Attendance Device', $e);
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
            ActivityTracker::track_activity('Delete Attendance Device');
            
        }
    }
}
