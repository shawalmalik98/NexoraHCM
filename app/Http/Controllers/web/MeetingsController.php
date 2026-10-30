<?php

namespace App\Http\Controllers\web;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Employees, Meeting, MeetingUsers, RoomScreen};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait,MailTrait};
use App\Helpers\ActivityTracker;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class MeetingsController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait,MailTrait;
    const MAIN_TITLE = "all-meetings";
    const TITLE_GLOBAL = "my-meetings";

    protected function index(){
        try{
            $title = Self::MAIN_TITLE;
        
            $login_user = $this->get_login_user_detail();
                        
            $meetings = Meeting::join('tbl_employees','tbl_meetings.host_id','=','tbl_employees.employee_id')
            ->orderBy('meeting_id','DESC')
            ->get(['meeting_id','meeting_title','employee_id','meeting_time'])->toArray();

            $send_data = compact('title','login_user', 'meetings');

            return view('meeting.all-meetings', $send_data);

        }
        catch(\Exception $e){
            $this->create_error_log('Access All Meetings Module', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('Access All Meetings Module');
          
        }


    }

    protected function my_meetings(){

        try{
            $title = Self::TITLE_GLOBAL;
        
            $login_user = $this->get_login_user_detail();
            
            $active_employees = Employees::where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();

            $my_meetings_as_host = Meeting::join('tbl_employees','tbl_meetings.host_id','=','tbl_employees.employee_id')
            ->leftjoin('tbl_room_screen','tbl_meetings.meeting_room','=','tbl_room_screen.room_id')
            ->where('host_id', session('user_session'))
            ->orderBy('meeting_id','DESC')
            ->get()
            ->toArray();
            
            $my_meetings_as_invited = MeetingUsers::join('tbl_meetings','tbl_meeting_users.users_meeting_id','=','tbl_meetings.meeting_id')
            ->join('tbl_employees','tbl_meetings.host_id','=','tbl_employees.employee_id')
            ->where('user_id', session('user_session'))
            ->get()
            ->toArray();

            $office_rooms = RoomScreen::get()->toArray();
            
            $meetings = array_merge($my_meetings_as_host , $my_meetings_as_invited);
         
            $send_data = compact('title','login_user', 'active_employees', 'my_meetings_as_host', 'my_meetings_as_invited', 'meetings','office_rooms');
        }
        catch(\Exception $e){
            $this->create_error_log('Access My Meetings Module', $e);
            $send_data = compact('title');
        }
        finally{
            ActivityTracker::track_activity('Access My Meetings Module');
            return view('meeting.my-meetings', $send_data);
        }
    }

    protected function create_meeting(Request $request){
		
		try{

            $validator = Validator::make($request->all(), [
                'meeting_type' => 'required|in:Physical,Online',
                'meeting_room' =>  ($request->input('meeting_type') == 'Physical' ) ? 'required' : '',
                'meeting_link' =>  ($request->input('meeting_type') == 'Online' ) ? 'required|url' : '',
                'meeting_title' => 'required', 
                'meeting_date' => 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+1 month')).'|after:yesterday ', 
                'meeting_time' => 'required|date_format:H:i', 
                'meeting_users' => 'required',
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
                $schedule_new_meeting = new Meeting();
                $schedule_new_meeting->meeting_room = $request->input('meeting_room');
                $schedule_new_meeting->meeting_link = $request->input('meeting_link');
                $schedule_new_meeting->meeting_type = $request->input('meeting_type');
                $schedule_new_meeting->meeting_title = $request->input('meeting_title');
                $schedule_new_meeting->meeting_description = $request->input('meeting_description');
                $schedule_new_meeting->meeting_time = $request->input('meeting_date') .' '. $request->input('meeting_time');
                $schedule_new_meeting->host_id = session('user_session');
                $schedule_new_meeting->meeting_status = '1';
                $schedule_new_meeting->meeting_created_at = date('Y-m-d H:i:s');
                $process = $schedule_new_meeting->save();
                if($process == 1){
                    
                    if(!empty($request->input('meeting_users'))){
                        foreach($request->input('meeting_users') as $user){

                            $addusers = new MeetingUsers();
                            $addusers->users_meeting_id = $schedule_new_meeting->meeting_id;
                            $addusers->user_id = $user;
                            $addusers->save();

                            $this->send_notification('New Meeting Scheduled', 'Dear User, '.$login_user[0]['employee_name'].' scheduled a meeting with you at '.$schedule_new_meeting->meeting_time.'','my-meeting-detail/'. $schedule_new_meeting->meeting_id .'',$user,NULL,NULL,'no');
                        }

                        $this->send_meeting_email($request->input('meeting_users'),'New Meeting Scheduled','<b>'. $login_user[0]['employee_name']. '</b> scheduled a meeting with you at '.date('d F, Y h:i A',strtotime($schedule_new_meeting->meeting_time)).'.', ''.url('/').'/my-meeting-detail/'. $schedule_new_meeting->meeting_id .'',Null);

                    }
                    
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Meeting scheduled successfully.',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Meeting addition failed. Please try again after some time.',
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log('Schedule a meeting', $e);
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
            ActivityTracker::track_activity('Schedule a meeting');
        }
            
	}

    protected function detail($meeting_id){

        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();
            
            $active_employees = Employees::where('employee_isactive', 1)->get()->toArray();

            $meeting_detail = Meeting::where('tbl_meetings.meeting_id', $meeting_id)->get()->toArray();
           
            $meeting_users = MeetingUsers::where('tbl_meeting_users.users_meeting_id', $meeting_id)->get()->toArray();

            $users_id = MeetingUsers::where('tbl_meeting_users.users_meeting_id', $meeting_id)->pluck('user_id')->toArray();

            $send_data = compact('title','login_user', 'meeting_detail', 'meeting_users','active_employees','users_id');
            
            return view('meeting.meeting-detail', $send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View Meeting Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Meeting Detail');
          
        }    
            
	}

    protected function my_meeting_detail($meeting_id){

        try{

            $title = Self::TITLE_GLOBAL;
        
            $login_user = $this->get_login_user_detail();

            $host_id = Meeting::where('tbl_meetings.meeting_id', $meeting_id)->pluck('host_id')->toArray();

            $users_id = MeetingUsers::where('tbl_meeting_users.users_meeting_id', $meeting_id)->pluck('user_id')->toArray();
                
            $meeting_assing = array_merge($host_id , $users_id);

            if(!empty($meeting_assing) && in_array(session('user_session'),$meeting_assing )){

                $active_employees = Employees::where('employee_isactive', 1)->get()->toArray();

                $meeting_detail = Meeting::where('tbl_meetings.meeting_id', $meeting_id)->get()->toArray();

                $meeting_users = MeetingUsers::where('tbl_meeting_users.users_meeting_id', $meeting_id)->get()->toArray();

                $users_id = MeetingUsers::where('tbl_meeting_users.users_meeting_id', $meeting_id)->pluck('user_id')->toArray();

                $office_rooms = RoomScreen::get()->toArray();
    
                $send_data = compact('title','login_user', 'meeting_detail', 'meeting_users','active_employees','users_id','office_rooms');

                return view('meeting.meeting-detail', $send_data);
            }else{
                
                return view('errors.403');
            }
        
          
        }
        catch(\Exception $e){
            $this->create_error_log('View Meeting Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Meeting Detail');
             
        }    
            
	}

    protected function add_another_user_schedule_meeting(Request $meetingData){

        try{
            $validator = Validator::make($meetingData->all(), [
                'meeting_users' => 'required',
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

                $meeting_schedule_time = Meeting::where('meeting_id',$meetingData->update_meeting_id)->get('meeting_time')->toArray();

                $meeting_user = MeetingUsers::where('users_meeting_id',$meetingData->update_meeting_id)->pluck('user_id')->toArray();
            
                MeetingUsers::where('users_meeting_id',$meetingData->update_meeting_id)->delete();

                $process = '';
                foreach($meetingData->input('meeting_users') as $user){

                    $addusers = new MeetingUsers();
                    $addusers->users_meeting_id = $meetingData->update_meeting_id;
                    $addusers->user_id = $user;
                    $process = $addusers->save();

                    if(!in_array($user, $meeting_user)){

                        $this->send_notification('New Meeting Scheduled', 'Dear User, '.$login_user[0]['employee_name'].' scheduled a meeting with you at '. $meeting_schedule_time[0]['meeting_time'].'','my-meeting-detail/'. $meetingData->update_meeting_id .'',$user,NULL,NULL,'no');
                    }

                }
               
                if(count($meetingData->input('meeting_users')) >  count($meeting_user)){
                    $employee_id = array_diff($meetingData->input('meeting_users'),$meeting_user);

                    $this->send_meeting_email($employee_id,'New Meeting Scheduled','<b>'. $login_user[0]['employee_name']. '</b> scheduled a meeting with you at '.date('d F, Y h:i A',strtotime($meeting_schedule_time[0]['meeting_time'])).'.', ''.url('/').'/my-meeting-detail/'. $meetingData->update_meeting_id .'','add_another_user');
                }
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Meeting invited user updated successfully.',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Meeting invited user not updated successfully try again.',
                    ]);
                }
            } 
            
        }catch(\Exception $e){
            $this->create_error_log('Add Another Employee Schedule  meeting', $e);
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
            ActivityTracker::track_activity('Add Another Employee Schedule  Meeting');
        }
        
    }

    protected function cancel_schedule_meeting(Request $meetingData){

        try{

            $validator = Validator::make($meetingData->all(), [
                'conclusion' => 'required',
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
                $meeting = Meeting::find($meetingData->input('meeting_id'));
                
                if($meeting->meeting_status == 1){

                    if($meetingData->meeting_status == 2){
                        $meeting->meeting_status = '2';
                        $meeting->meeting_conclusion  = $meetingData->input('conclusion');
                        $msg = "Completed";
                    }else{
                        $meeting->meeting_status = '3';
                        $meeting->meeting_conclusion  = $meetingData->input('conclusion');
                        $msg = "Cancel";
                    }
                
                    $procces = $meeting->save();
                    
                    if($procces == 1){
            
                        $my_meetings_invited_users = MeetingUsers::join('tbl_meetings','tbl_meeting_users.users_meeting_id','=','tbl_meetings.meeting_id')
                        ->join('tbl_employees','tbl_meeting_users.user_id','=','tbl_employees.employee_id')->
                        where('users_meeting_id',$meetingData->input('meeting_id'))
                        ->get(['meeting_id','meeting_time','employee_id'])
                        ->toArray();
                    
                        foreach($my_meetings_invited_users as $my_meetings_invited_user){
            
                            $this->send_notification('Meeting Scheduled Canceled', 'Dear User, '.$login_user[0]['employee_name'].' cancel a meeting with you at '.$my_meetings_invited_user['meeting_time'].'','my-meetings?meeting_id='. $my_meetings_invited_user['meeting_id'] .'',$my_meetings_invited_user['employee_id'],NULL,NULL,'no');
                        }
                    
                    
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Meeting '.$msg.' successfully.',
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Meeting Not '.$msg.' please try again.',
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You Have Already Responsed this Meeting',
                    ]);
                }
            }   
        }catch(\Exception $e){
            $this->create_error_log('Cancel Schedule a meeting', $e);
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
            ActivityTracker::track_activity('Cancel Schedule a meeting');
        }
      

    }


    protected function meeting_chat(Request $CreateChatRecord){
        
        // $chatID = $CreateChatRecord->input('meeting_chat_id');
        $indexID = $CreateChatRecord->input('meeting_index_id');

        $login_user = $this->get_login_user_detail();

        $database = CustomHelper::firebase_database();
        if($database === null){
            return response()->json(['status' => 'FALSE', 'msg' => 'Chat is not configured.']);
        }
        $NewChatMessage = $database->getReference('chats/MeetingsChat/')->push([
            'message' => $CreateChatRecord->message,
            'sender_id' => $login_user[0]['employee_id'],
            'meeting_id' => $CreateChatRecord->input('meeting_id'),
            'sender_name' => $login_user[0]['employee_name'],
            'sender_avatar' => ($login_user[0]['profile_avatar']) ?? 'Avatar.jpg',
            'date' => date('d F, Y'),
            'time' => date('h:i A')
        ]);
        return $NewChatMessage;
    }

}
