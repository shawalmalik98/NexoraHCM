<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{Employees, Meeting, MeetingUsers, RoomScreen};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait, AttendanceEmployee};
use App\Helpers\ActivityTracker;

class MeetingsApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait ,NotificationTrait, ApiResponseTrait;

    const TITLE_GLOBAL = "my-meetings";

    protected function my_meetings(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $title = Self::TITLE_GLOBAL;
            
            $active_employees = Employees::where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();

            $my_meetings_as_host = Meeting::join('tbl_employees','tbl_meetings.host_id','=','tbl_employees.employee_id')
            ->leftjoin('tbl_room_screen','tbl_meetings.meeting_room','=','tbl_room_screen.room_id')
            ->where('host_id', $login_user[0]['employee_id'])
            ->orderBy('meeting_id','DESC')
            ->get(['meeting_id','meeting_title','meeting_description','meeting_time','host_id','meeting_created_at','meeting_status','meeting_room','meeting_link','meeting_type','meeting_conclusion','employee_id','employee_code','employee_name'])
            ->toArray();
        
            $my_meetings_as_invited = MeetingUsers::join('tbl_meetings','tbl_meeting_users.users_meeting_id','=','tbl_meetings.meeting_id')
            ->join('tbl_employees','tbl_meetings.host_id','=','tbl_employees.employee_id')
            ->where('user_id', $login_user[0]['employee_id'])
            ->get(['meet_user_id','users_meeting_id','user_id','meeting_id','meeting_title','meeting_description','meeting_time','host_id','meeting_created_at','meeting_status','meeting_room','meeting_link','meeting_type','meeting_conclusion','employee_id','employee_code','employee_name'])
            ->toArray();

            $office_rooms = RoomScreen::get()->toArray();
            
            $meetings = array_merge($my_meetings_as_host , $my_meetings_as_invited);

            $data = compact('title', 'my_meetings_as_host', 'my_meetings_as_invited', 'meetings','office_rooms');

            return $this->api_response_json(TRUE,"My Meetings",$data,200);
        }
        catch(\Exception $e){

            $this->create_error_log_from_api('Access My Meetings Module API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{

            ActivityTracker::track_activity_from_api('View Access My Meetings Module API', $request->header('Bearer-Token')); 
        }
    }

    protected function my_meeting_detail(Request $request){

        try{
        
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $title = Self::TITLE_GLOBAL;

            $host_id = Meeting::where('tbl_meetings.meeting_id', $request->meeting_id)->pluck('host_id')->toArray();

            $users_id = MeetingUsers::where('tbl_meeting_users.users_meeting_id', $request->meeting_id)->pluck('user_id')->toArray();
                
            $meeting_assing = array_merge($host_id , $users_id);

            if(!empty($meeting_assing) && in_array($login_user[0]['employee_id'],$meeting_assing )){

                $active_employees = Employees::where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();

                $meeting_detail = Meeting::where('tbl_meetings.meeting_id', $request->meeting_id)->get()->toArray();

                $meeting_users = MeetingUsers::where('tbl_meeting_users.users_meeting_id', $request->meeting_id)->get()->toArray();

                $users_id = MeetingUsers::where('tbl_meeting_users.users_meeting_id', $request->meeting_id)->pluck('user_id')->toArray();
    
                $data = compact('title', 'meeting_detail', 'meeting_users','users_id');

                return $this->api_response_json(TRUE,"My Meetings detail",$data,200);

            }else{

                return $this->api_response_json(FALSE,"Invalid Meeting Id.","",200);
                
            }
        }
        catch(\Exception $e){

            $this->create_error_log_from_api('View My Meeting Detail API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('View Access My Meeting detail Module API', $request->header('Bearer-Token'));              
        }    
            
	}

    protected function create_meeting(Request $request){
		
		try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $meetings_rooms = RoomScreen::pluck('room_id')->toArray();

            $validator = Validator::make($request->post(), [
                'meeting_type' => 'required|in:Physical,Online',
                'meeting_room' =>  ($request->post('meeting_type') == 'Physical' ) ? 'required|in:'.implode(",",($meetings_rooms)) : '',
                'meeting_link' =>  ($request->post('meeting_type') == 'Online' ) ? 'required|url' : '',
                'meeting_title' => 'required', 
                'meeting_date' => 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+1 month')).'|after:yesterday ', 
                'meeting_time' => 'required|date_format:H:i', 
                'meeting_users' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $schedule_new_meeting = new Meeting();
                $schedule_new_meeting->meeting_room = $request->post('meeting_room');
                $schedule_new_meeting->meeting_link = $request->post('meeting_link');
                $schedule_new_meeting->meeting_type = $request->post('meeting_type');
                $schedule_new_meeting->meeting_title = $request->post('meeting_title');
                $schedule_new_meeting->meeting_description = $request->post('meeting_description');
                $schedule_new_meeting->meeting_time = $request->post('meeting_date') .' '. $request->post('meeting_time');
                $schedule_new_meeting->host_id = $login_user[0]['employee_id'];
                $schedule_new_meeting->meeting_status = '1';
                $schedule_new_meeting->meeting_created_at = date('Y-m-d H:i:s');
                $process = $schedule_new_meeting->save();
                if($process == 1){
                    
                    if(!empty($request->post('meeting_users'))){
                        foreach($request->post('meeting_users') as $user){

                            if($user != $login_user[0]['employee_id']){

                                $addusers = new MeetingUsers();
                                $addusers->users_meeting_id = $schedule_new_meeting->meeting_id;
                                $addusers->user_id = $user;
                                $addusers->save();
    
                                $this->send_notification_api('New Meeting Scheduled', 'Dear User, '.$login_user[0]['employee_name'].' scheduled a meeting with you at '.$schedule_new_meeting->meeting_time.'','my-meeting-detail/'. $schedule_new_meeting->meeting_id .'',$user,NULL,NULL,'no',$login_user[0]['employee_id']);

                            }
                            
                        }
                    }

                    return $this->api_response_json(TRUE,"Meeting Scheduled Successfully.","",200);
                    
                }else{

                    return $this->api_response_json(FALSE,"Meeting Addition Failed. Please try again after some time.","",200);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Schedule a meeting Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('Access Request Schedule a meeting Module API',  $request->header('Bearer-Token'));    
        }
            
	}

    protected function add_another_user_schedule_meeting(Request $request){

        $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

        $validator = Validator::make($request->post(), [
            'meeting_users' => 'required',
        ]);

        if($validator->fails()) {
            foreach($validator->errors()->all() as $error){

                return $this->api_response_json(FALSE,$error,"",200);
            }
        }else {
        
            $meeting_schedule_time = Meeting::where('meeting_id',$request->post('update_meeting_id'))->get('meeting_time')->toArray();

            $meeting_user = MeetingUsers::where('users_meeting_id',$request->post('update_meeting_id'))->pluck('user_id')->toArray();

            MeetingUsers::where('users_meeting_id',$request->post('update_meeting_id'))->delete();

            $process = '';

            foreach($request->post('meeting_users') as $user){

                $addusers = new MeetingUsers();
                $addusers->users_meeting_id = $request->post('update_meeting_id');
                $addusers->user_id = $user;
                $process = $addusers->save();

                if(!in_array($user, $meeting_user)){

                    $this->send_notification_api('New Meeting Scheduled', 'Dear User, '.$login_user[0]['employee_name'].' scheduled a meeting with you at '. $meeting_schedule_time[0]['meeting_time'].'','my-meeting-detail/'. $request->post('update_meeting_id') .'',$user,NULL,NULL,'no',$login_user[0]['employee_id']);
                }

            }
            if($process == 1){

                return $this->api_response_json(TRUE,"Meeting Invited User Updated Successfully.","",200);
            }else{

                return $this->api_response_json(FALSE,"Meeting Invited User Not Updated Successfully Try Again.","",200);
            }
        }   
        
    }

    protected function cancel_schedule_meeting(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                'conclusion' => 'required',
            ]);
    
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $meeting = Meeting::find($request->post('meeting_id'));
                
                if($meeting->meeting_status == 1){

                    if($request->post('meeting_status') == 2){
                        $meeting->meeting_status = '2';
                        $meeting->meeting_conclusion  = $request->post('conclusion');
                        $msg = "Meeting Completed Successfully";
                    }else{
                        $meeting->meeting_status = '3';
                        $meeting->meeting_conclusion  = $request->post('conclusion');
                        $msg = "Meeting Cancel Successfully";
                    }
                
            
                    $procces = $meeting->save();
                    
                    if($procces == 1){
            
                        $my_meetings_invited_users = MeetingUsers::join('tbl_meetings','tbl_meeting_users.users_meeting_id','=','tbl_meetings.meeting_id')
                        ->join('tbl_employees','tbl_meeting_users.user_id','=','tbl_employees.employee_id')->
                        where('users_meeting_id',$request->post('meeting_id'))
                        ->get(['meeting_id','meeting_time','employee_id'])
                        ->toArray();
                    
                        foreach($my_meetings_invited_users as $my_meetings_invited_user){
            
                            $this->send_notification_api('Meeting Scheduled Canceled', 'Dear User, '.$login_user[0]['employee_name'].' cancel a meeting with you at '.$my_meetings_invited_user['meeting_time'].'','my-meetings?meeting_id='. $my_meetings_invited_user['meeting_id'] .'',$my_meetings_invited_user['employee_id'],NULL,NULL,'no',$login_user[0]['employee_id']);
                        }
                    
                        return $this->api_response_json(TRUE,$msg,"",200);

                    }else{

                        return $this->api_response_json(FALSE,"Meeting status failed Please Try Again.","",200);
                    }
                }else{

                    return $this->api_response_json(FALSE,"You Have Already Responsed this Meeting.","",200);
                }
            }   
        }catch(\Exception $e){

            $this->create_error_log_from_api('Complete and Cancel Schedule a meeting API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('Access Request Complete and Cancel Schedule a meeting API', $request->header('Bearer-Token'));    
        }
      
    }

}