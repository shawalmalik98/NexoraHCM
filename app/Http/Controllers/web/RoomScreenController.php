<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{RoomScreen, Interview,OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Validation\Rule;

class RoomScreenController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = 'manage-rooms';

    protected function index(){
        try{
            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $rooms = RoomScreen::join('tbl_office_locations','tbl_office_locations.location_id','=','tbl_room_screen.room_location_id')->get(['room_id','room_name','last_updated','location_id','location_title'])->toArray();
           
            $office_location = OfficeLocation::get(['location_id','location_title'])->toArray();
           
            $send_data = compact('title' , 'login_user', 'rooms','office_location');
            
            return view('room-screen.rooms')->with($send_data);
        }
        catch(\Exception $e){

            $this->create_error_log('View All Rooms', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Rooms');
       

        }
    }

    protected function add_room(Request $request){

        try{

            $room_name = $request->input('room_name');
            $location_id = $request->input('location_name');

            if(!empty($location_id)){
                
                $get_room = RoomScreen::where('room_name',$room_name)->where('room_location_id', $location_id)->get(['room_name'])->toArray();
            
                if (!empty($get_room)) {
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>  $get_room[0]['room_name'].' already exists in this location .',
                    ]);
                }
                
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'The office location name field is required'
                ]);

            }    
            $validator = Validator::make($request->all(), [

                'room_name' => [
                    'required',
                    'max:50',
                    'regex:/^[\pL\s\&\,(\).]+$/u',
                    Rule::unique('tbl_room_screen')->where(function ($query) use ($room_name, $location_id) {
                        return $query->where('room_name', $room_name)->where('room_location_id', $location_id);
                    })
                ],
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
               
                $check_exit_location = OfficeLocation::where('location_id',$request->input('location_name'))->get('location_id')->toArray();

                if(!empty($check_exit_location )){
                    $new_room = new RoomScreen();
                    $new_room->room_name = $request->input('room_name');
                    $new_room->room_location_id = $request->input('location_name');
                    $new_room->last_updated = date('Y-m-d H:i:s');
                    $process = $new_room->save();   
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Room added successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Room addition failed. Please try again after some time.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This office location has been deleted.',
                    ]);
                }   
            }   
             
        }catch(\Exception $e){
            $this->create_error_log("Add Room", $e);
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
            ActivityTracker::track_activity('Add Room'); 
        }

    }

    protected function update_room(Request $request){
      
        try{

            $room_name = $request->input('room_name');
            $location_id = $request->input('location_name');
            
            if(!empty($location_id)){

                $get_room = RoomScreen::where('room_name',$room_name)->where('room_location_id', $location_id)->where('room_id','!=',$request->room_id)->get(['room_name'])->toArray();
            
                if (!empty($get_room)) {
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>  $get_room[0]['room_name'].' already exists in this location .',
                    ]);
                }
                
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'The office location name field is required'
                ]);

            }    
            $validator = Validator::make($request->all(), [
                'room_name' => [
                'required',
                'max:50',
                'regex:/^[\pL\s\&\,(\).]+$/u',
                Rule::unique('tbl_room_screen')->ignore($request->input('room_id'),'room_id')->where(function ($query) use ($room_name, $location_id) {
                    return $query->where('room_name', $room_name)->where('room_location_id', $location_id);
                })
                ],
                'room_id' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                $check_exit_location = OfficeLocation::where('location_id',$request->input('location_name'))->get('location_id')->toArray();

                if(!empty($check_exit_location )){
                    $update_room = RoomScreen::find($request->input('room_id'));

                    if(!empty($update_room)){
                        
                        $update_room->room_name = $request->input('room_name');
                        $update_room->last_updated = date('Y-m-d H:i:s');
                        $process = $update_room->save();   
                        if($process == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Room updated successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Room update failed. Please try again after some time.",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Room not found on this id.',
                        ]);
                    }   
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This office location has been deleted.',
                    ]);
                }   
               
            }             
             
        }catch(\Exception $e){
            $this->create_error_log("Update Room", $e);
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
            ActivityTracker::track_activity('Update Room'); 
        }

    }

    protected function delete_room(Request $request_data){
      
        try{
            
            $get_room = RoomScreen::find($request_data->room_id);

            if(!empty($get_room)){

                $check_if_interview_exist_on_room = Interview::where('interview_room_nbr', $get_room->room_id)->get()->toArray();
                if(empty($check_if_interview_exist_on_room)){
   
                    $process = RoomScreen::where('room_id', $request_data->room_id)->delete();
                    if( $process == 1){
                    
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Room request has been deleted',
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
                        'msg' => "This room can't be delete now. Interview(s) scheduled on this room.",
                    ]);
                }    
                        
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Room not found on this id.',
                ]);
            }         
        }catch(\Exception $e){
            $this->create_error_log('Delete Room', $e);
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
            ActivityTracker::track_activity('Delete Room');
            
        }
    }

}
