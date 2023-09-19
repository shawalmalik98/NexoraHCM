<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use App\Models\{Interview, Employees, HrmSetting,Department, OnBoardingEmployee, InterviewCandidate, Notification,RoomScreen,InterviewStatusLevel,InterviewStatus, Requisition, RequisitionStatus};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait,MailTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\DB;


use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class InterviewsController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait,MailTrait;

    const MAIN_TITLE = 'manage-interviews';
    const TITLE_GLOBAL = 'my-interviews';
    const INTERVIEW_CANDIDATE = 'interview-candidate-list';

    protected function manage_all(Request $request){
       
        try{
           
            $title = SELF::MAIN_TITLE;
                    
            $login_user = $this->get_login_user_detail();
            
            $departments = Department::get()->toArray();

            $requisitions = Requisition::join('tbl_departments','tbl_departments.department_id','=','tbl_requisition.requisition_department')->get(['requisition_id','department_name','requisition_job_title','requisition_added_by','requisition_department'])->toArray();

            $interview_candidate_name = InterviewCandidate::join('tbl_interviews','tbl_interview_candidate.candidate_id','tbl_interviews.interview_candidate_id')->get(['candidate_id', 'candidate_name','interview_candidate_cv','interview_current_salary','interview_expected_salary','candidate_contact_no','interview_jobtitle','candidate_email'])->toArray();

            $candidate_interviews = InterviewCandidate::distinct()->get('candidate_name');
         
            $notice_period = ['Immediately','5 Days','10 Days','15 Days','1 Month','2 Month','6 Month'];

            $employees = Employees::where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();

            $interview_status_level = InterviewStatusLevel::get()->toArray();

            $office_rooms = RoomScreen::get(['room_id','room_name'])->toArray();
        
            $where_clause = '1=1 ';
            if(!empty($request->interview_name) || !empty($request->interview_date) || !empty($request->interview_status) || !empty($request->interview_type) || !empty($request->interview_month)){
               
                if(!empty($request->interview_name)){
                    $where_clause .= 'AND (interview_interviewer1 = "'.$request->interview_name.'" OR interview_interviewer2 = "'.$request->interview_name.'")';
                }
               
                if(!empty($request->interview_date)){
                    $scheduled_time = date("Y-m-d", strtotime($request->interview_date));
                    $where_clause .= 'AND interview_date LIKE "%'.  $scheduled_time.'%"';
                }else{
                    $where_clause .= 'AND interview_date LIKE "%'.  $request->interview_month.'%"';
                }

                if(!empty($request->interview_status)){
                    $where_clause .= ' AND interview_status_level = "'.$request->interview_status.'"';
                }
                if(!empty($request->interview_type)){
                    $where_clause .= ' AND interview_type = "'.$request->interview_type.'"';
                }
              
            }else{
                $where_clause .= ' AND MONTH(interview_date) = MONTH(GETDATE())
                and YEAR(interview_date) = YEAR(GETDATE())';
            }
            
            $interviews =  Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
            ->join('tbl_requisition','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
            ->join('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
            ->join('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
            ->whereIn('interview_status_id',function($query){
                $query->from('tbl_interview_status')
                ->selectRaw('Max(interview_status_id)')
                ->groupBy('main_interview_id');
            })->whereRaw($where_clause)
            ->orderBy("interview_id","DESC" )->get(['interview_id','candidate_id','candidate_name','candidate_contact_no','interview_interviewer1','interview_interviewer2','interview_time','department_name','interview_interviewer_remarks','interview_date','department_id','interview_status_level','interview_type'])->toArray();

            $send_data = compact('title', 'login_user',  'interview_candidate_name', 'notice_period', 'interviews','employees','departments','office_rooms', 'requisitions','candidate_interviews','interview_status_level');
            
            return view('interview.all-interviews', $send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View All Interviews', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View All Interviews');
         
        }
    }

    protected function schedule_interview(Request $interviewData){
		
        try{

            if(!empty($interviewData->input('room_no'))){
                $interview_room_name = RoomScreen::where('room_id',$interviewData->input('room_no'))->get('room_name')->toArray();

                if(empty($interview_room_name)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This room name has been deleted.',
                    ]);
                }
            }

            $filename = '';

            if($interviewData->hasFile('candidate_cv')){

                $filename = $interviewData->file('candidate_cv')->getClientOriginalExtension();
            }

            if($interviewData->input("interview_type") == "Physical"){
                $interview_type = 'room_no';
            }else{
                $interview_type = 'interview_link';
            }

            $validator = Validator::make($interviewData->all(), [
                'interview_date' => 'required|date_format:Y-m-d|after:yesterday',
            ]);  
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            if($interviewData->input('interview_date') == date('Y-m-d')){
                $interview_time_validation = 'required|date_format:H:i|after:'.now()->format('H:i');
            }else{
                $interview_time_validation = 'required|date_format:H:i';
            }

            if(!empty($interviewData->input('candidate_email'))){
                $employee_email_check = Employees::where('employee_isactive',1)->where(function ($query) use ($interviewData) {
                    $query->where('employee_personal_email',$interviewData->input('candidate_email'))
                    ->orWhere('employee_work_email',$interviewData->input('candidate_email'));
                })
                
                ->get('employee_id')->toArray();
                if(!empty($employee_email_check)){
                    return response()->json([
                        'msg' =>'This email is already registered in employees.',
                        'status' => 'FALSE'
                    ]);
                }
            }

            if($interviewData->reSchedule == 1){
                
                $validator = Validator::make($interviewData->all(), [
                    'interviewer_1' => 'required',
                    'interview_date' => 'required|date_format:Y-m-d|after:yesterday', 
                    'interview_time' =>  $interview_time_validation,
                    'duration' =>  'required|numeric',
                    'candidate_name' => 'required|regex:/^[\pL\s\&\,(\)]+$/u', 
                    'interview_department' => 'required', 
                    'interview_jobtitle' => 'required', 
                    $interview_type => 'required',
                    'interview_round' => 'required',
                    'contact_number' => ['required','unique:tbl_interview_candidate,candidate_contact_no,'.$interviewData->input('candidate_id').',candidate_id', 'numeric'],
                    'candidate_email' => ['nullable','unique:tbl_interview_candidate,candidate_email,'.$interviewData->input('candidate_id').',candidate_id', 'email','regex:/^[^@]+@\w+(\.\w+)+$/'],
                    'requisition_reference' => 'required',
                    
                ]);
            }else{
               
                $validator = Validator::make($interviewData->all(), [
                    'interviewer_1' => 'required',
                    'interview_date' => 'required|date_format:Y-m-d|after:yesterday', 
                    'interview_time' =>  $interview_time_validation,
                    'duration' =>  'required|numeric',
                    'candidate_name' => 'required|regex:/^[\pL\s\&\,(\)]+$/u', 
                    'interview_department' => 'required', 
                    'interview_jobtitle' => 'required', 
                     $interview_type => 'required',
                    'interview_round' => 'required',
                    'contact_number' => ['required','unique:tbl_interview_candidate,candidate_contact_no','numeric'],
                    'requisition_reference' => 'required',
                    'candidate_email' => 'nullable|email|regex:/^[^@]+@\w+(\.\w+)+$/',
                    'candidate_cv' => ['required','max:10000',($filename == 'txt') ? 
                    File::types(['jpg', 'jpeg', 'png','docx','pdf','txt'])
                    :
                    File::types(['jpg', 'jpeg', 'png','xlsx','docx','pdf'])],
                    
                ]);

                if($interviewData->new_candidate == 'new' ){
                  
                    $validator = Validator::make($interviewData->all(), [
                        'interviewer_1' => 'required',
                        'interview_date' => 'required|date_format:Y-m-d|after:yesterday',   
                        'interview_time' =>  $interview_time_validation,
                        'duration' =>  'required|numeric',
                        'candidate_name' => 'required|regex:/^[\pL\s\&\,(\)]+$/u', 
                        'interview_department' => 'required', 
                        'interview_jobtitle' => 'required', 
                         $interview_type => 'required',
                        'interview_round' => 'required',
                        'contact_number' => ['required','unique:tbl_interview_candidate,candidate_contact_no','numeric'],
                        'requisition_reference' => 'required',
                        'candidate_email' => 'nullable|unique:tbl_interview_candidate,candidate_email|email|regex:/^[^@]+@\w+(\.\w+)+$/',
                        'candidate_cv' => ['required','max:10000',($filename == 'txt') ? 
                        File::types(['jpg', 'jpeg', 'png','docx','pdf','txt'])
                        :
                        File::types(['jpg', 'jpeg', 'png','xlsx','docx','pdf'])],
                        
                    ]);
                }else{
                  
                    $validator = Validator::make($interviewData->all(), [
                        'interviewer_1' => 'required',
                        'interview_date' => 'required|date_format:Y-m-d|after:yesterday', 
                        'interview_time' =>  $interview_time_validation,
                        'duration' =>  'required|numeric',
                        'candidate_name' => 'required|regex:/^[\pL\s\&\,(\)]+$/u', 
                        'interview_department' => 'required', 
                        'interview_jobtitle' => 'required', 
                        $interview_type => 'required',
                        'interview_round' => 'required',
                        'contact_number' => ['required','unique:tbl_interview_candidate,candidate_contact_no,'.$interviewData->input('candidate_id').',candidate_id', 'numeric'],
                        'requisition_reference' => 'required',
                        'candidate_email' => ['nullable','unique:tbl_interview_candidate,candidate_email,'.$interviewData->input('candidate_id').',candidate_id', 'email','regex:/^[^@]+@\w+(\.\w+)+$/'],
                        'candidate_cv' => ['max:10000',($filename == 'txt') ? 
                        File::types(['jpg', 'jpeg', 'png','docx','pdf','txt'])
                        :
                        File::types(['jpg', 'jpeg', 'png','docx','pdf'])],
                        
                    ]);
                }   
            }
           
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $check_requisition = Requisition::where('requisition_id', $interviewData->input("requisition_reference"))->get(['requisition_id'])->toArray();

                if(empty($check_requisition)){

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This requisition has been deleted.',
                    ]);
                }

                if(!empty($interviewData->input('interviewer_1'))){

                    $employee_interviewer_1 = Employees::where('employee_isactive','=', 1)->where('employee_id', $interviewData->input('interviewer_1'))->get(['employee_id','employee_code','employee_name'])->toArray();

                    if(empty($employee_interviewer_1)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Selected interviewer 1st doesn't exist in active employees.",
                        ]);

                    }
                }
                
                if(!empty($interviewData->input('interviewer_2'))){

                    $employee_interviewer_2 = Employees::where('employee_isactive','=', 1)->where('employee_id', $interviewData->input('interviewer_2'))->get(['employee_id','employee_code','employee_name'])->toArray();

                    if(empty($employee_interviewer_2)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Selected interviewer 2nd doesn't exist in active employees.",
                        ]);

                    }
                }    

                $same_day_interviews = Interview::select('interview_id','interview_time','interview_duration','interview_interviewer1','interview_interviewer2','interview_room_nbr')
                ->where(function ($query) use ($interviewData) {
                    $query->where('interview_interviewer1' ,'=',$interviewData->input('interviewer_1'));
                    $query->orWhere('interview_interviewer2' ,'=',$interviewData->input('interviewer_1'));
                    $query->orWhere('interview_interviewer1' ,'=',$interviewData->input('interviewer_2'));
                    $query->orWhere('interview_interviewer2' ,'=',$interviewData->input('interviewer_2'));
                })
                ->where('interview_date',$interviewData->input("interview_date"))
                ->get()->toArray();
                
                $from_times = [];
                $interviewer_name = [];
                if(!empty($same_day_interviews)){

                    foreach($same_day_interviews as $interview){
                        
                        $from_time = $interview['interview_time']; 
                        $to_time = $interview['interview_duration'];
                        
                        for($i = 0; $i <= $to_time; $i++){
                            array_push($from_times, date('H:i', strtotime($from_time.' +'.$i.' minutes')));
                        }
                        
                    }
                }
                
                $from_time_input = $interviewData->input('interview_time'); 
                $to_time_input = $interviewData->input('duration'); 
                
                $from_times_input = [];
                for($i = 0; $i<=$to_time_input; $i++){
                    array_push($from_times_input, date('H:i', strtotime( $from_time_input.' +'.$i.' minutes')));
                }
            
                $same_day_and_time_interviews = array_intersect($from_times, $from_times_input);
              
                if($interviewData->input("interview_type") == "Physical"){
                    
                    $interview_room = DB::select("SELECT ADDTIME(interview_time, SEC_TO_TIME(interview_duration*60)) as total_time FROM tbl_interviews where interview_room_nbr = '".$interviewData->input('room_no')."' && interview_date = '".$interviewData->input("interview_date")."' &&  interview_time <= '".$interviewData->input("interview_time")."' && ADDTIME(interview_time, SEC_TO_TIME(interview_duration*60))  >= '".$interviewData->input("interview_time")."'");

                    $interview_room_allot = json_decode(json_encode($interview_room), TRUE);
                    
                    if(!empty($interview_room_allot)){
                       
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This ".$interview_room_name[0]['room_name']." already assign other interviewer",
                        ]);
                    }
                }
                if(empty($same_day_and_time_interviews)){

                    if($interviewData->input('reSchedule') != 1 && $interviewData->input('new_candidate') == 'new'){

                        if($interviewData->input('interviewer_1') == $interviewData->input('interviewer_2')){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'You have assigned same interviewers. Same employee can\'t be marked as both interviewers.',
                            ]);
                        }
                    
                        $candidate = new InterviewCandidate();
                        $candidate->candidate_name = $interviewData->input('candidate_name');
                        $candidate->interview_noticepriod = $interviewData->input('notice_period');
                        $candidate->interview_current_salary = $interviewData->input('current_salary');
                        $candidate->interview_expected_salary = $interviewData->input('expected_salary');
                        $candidate->candidate_contact_no = $interviewData->input('contact_number');
                        $candidate->candidate_email = $interviewData->input('candidate_email');
                        $candidate->candidate_created_at = date('d-m-Y H:i:s');

                        if($interviewData->hasFile('candidate_cv')){
                            
                            $fileNameToStore = $interviewData->input('candidate_name').''.time().'.'.$filename;
                            
                            $interviewData->file('candidate_cv')->storeAs('portal_documents/interviews/',$fileNameToStore);
                
                            $candidate->interview_candidate_cv = $fileNameToStore;
                
                        }
                        
                        $process = $candidate->save();
                        $candidate_id = $candidate->candidate_id;
                    }else{
                      
                        $candidate_id = $interviewData->input('candidate_id');
                        
                        $candidate = InterviewCandidate::find($candidate_id);
                
                        $candidate->candidate_name = $interviewData->input('candidate_name');
                        $candidate->interview_noticepriod = $interviewData->input('notice_period');
                        $candidate->interview_current_salary = $interviewData->input('current_salary');
                        $candidate->interview_expected_salary = $interviewData->input('expected_salary');
                        $candidate->candidate_contact_no = $interviewData->input('contact_number');
                        $candidate->candidate_email = $interviewData->input('candidate_email');
                        $candidate->candidate_created_at = date('d-m-Y H:i:s');

                        if($interviewData->hasFile('candidate_cv')){

                            if(!empty($candidate->interview_candidate_cv)){

                                if (file_exists(storage_path('app/portal_documents/interviews/'.$candidate->interview_candidate_cv))) {
                
                                    unlink(storage_path('app/portal_documents/interviews/'.$candidate->interview_candidate_cv));
                                }
                            }   
                            
                            $fileNameToStore = $interviewData->input('candidate_name').''.time().'.'.$filename;
                        
                            $interviewData->file('candidate_cv')->storeAs('portal_documents/interviews/',$fileNameToStore);
                
                            $candidate->interview_candidate_cv = $fileNameToStore;
                            
                        }
                        
                        $process = $candidate->save();
                    }
                
                    if($process == 1){

                        $add_interviewData = new Interview();
                        $add_interviewData->interview_candidate_id = $candidate_id ;
                        $add_interviewData->requisition_reference = $interviewData->input("requisition_reference");
                        $add_interviewData->interview_type = $interviewData->input("interview_type");
                        $add_interviewData->interview_link = $interviewData->input("interview_link");
                        $add_interviewData->interview_date = date('Y-m-d',strtotime($interviewData->input('interview_date')));
                        $add_interviewData->interview_time = $interviewData->input('interview_time');
                        $add_interviewData->interview_duration = $interviewData->input('duration');
                        $add_interviewData->interview_room_nbr = $interviewData->input('room_no');
                        $add_interviewData->interview_interviewer1 = $interviewData->input('interviewer_1');
                        $add_interviewData->interview_interviewer2 = $interviewData->input('interviewer_2');
                        $add_interviewData->interview_round = $interviewData->input('interview_round');
                        $add_interviewData->interview_additional_remarks = $interviewData->input('additional_remarks');
                        $add_interviewData->interview_department = $interviewData->input('interview_department');
                        $add_interviewData->interview_jobtitle = $interviewData->input('interview_jobtitle');
                        $add_interviewData->interview_created_by = session('user_session');
                        $add_interviewData->interview_scheduled_time = date('d-m-Y H:i:s');
                        if($interviewData->input('interviewer_1') != $interviewData->input('interviewer_2')){
        
                            $process = $add_interviewData->save();
        
                            if($process == 1){

                                $interview_status = new InterviewStatus();
                                $interview_status->interview_status_level_id = 1;
                                $interview_status->main_interview_id =  $add_interviewData->interview_id;
                                $interview_status->interview_status_created_by = session('user_session');
                                $interview_status->interview_status_created_at =  date('d-m-Y H:i:s');
                
                                $interview_status->save();

                                $requisition_status = new RequisitionStatus();
                                $requisition_status->main_requisition_id =  $interviewData->input("requisition_reference");
                                $requisition_status->requisition_status = 'Interview Scheduled';
                                $requisition_status->requisition_status_created_by = session('user_session');
                                $requisition_status->requisition_status_created_at = date('d-m-Y H:i:s');
                                $requisition_status->save();

                                    $this->send_notification('Interview Scheduling','Dear Employee, HR schedule an interview that you have to take on '.$interviewData->input('interview_date').'','my-interviews?interview_id='. $add_interviewData->interview_id .'',$interviewData->input('interviewer_1'),NULL,NULL,'no');
                                    if(!empty($interviewData->input('candidate_email'))){

                                        $comapny_address = HrmSetting::pluck('company_address')->toArray();
                                        
                                        $this->send_candidate_email($interviewData->input('candidate_email'),'Interview Scheduling','Based on our telephonic conversation, I have scheduled your interview with the following details. <br><br><strong>Date:</strong>'.' '.date('l jS F Y h:i:s').'<br><br><strong>Location:</strong>'.' '.$comapny_address[0].'.');
                                    }
                                   
                                    
                                    $this->send_interviewer_email($interviewData->input('interviewer_1'),'Interview Scheduling','HR schedule an interview that you have to take on ' . $interviewData->input('interview_date') . '.', ''.url('/').'/my-interviews?interview_id='. $add_interviewData->interview_id);

                                    $this->send_push_notification_mobile_app($interviewData->input('interviewer_1'),'Interview Scheduling','Dear Employee, HR schedule an interview that you have to take on ' . $interviewData->input('interview_date') . '.');

                                if(!empty($interviewData->input('interviewer_2'))){

                                    $this->send_notification('Interview Scheduling','HR schedule an interview that you have to take on '.$interviewData->input('interview_date').'','my-interviews?interview_id='. $add_interviewData->interview_id .'',$interviewData->input('interviewer_2'),NULL,NULL,'no');

                                    $this->send_interviewer_email($interviewData->input('interviewer_2'),'Interview Scheduling','Dear Employee, HR schedule an interview that you have to take on ' . $interviewData->input('interview_date') . '.', ''.url('/').'/my-interviews?interview_id='. $add_interviewData->interview_id);

                                    $this->send_push_notification_mobile_app($interviewData->input('interviewer_1'),'Interview Scheduling','Dear Employee, HR schedule an interview that you have to take on ' . $interviewData->input('interview_date') . '.');
                                }
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => 'Interview scheduled successfully.',
                                ]);
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => 'Interview addition failed. Please try again after some time.',
                                ]);
                            }
                        }
                    }   
                   
                }else{
                    
                    $interview_name1 = DB::table('tbl_interviews as i1')->select('e1.employee_name','e2.employee_name')->
                    join('tbl_employees as e1', 'i1.interview_interviewer2', '=', 'e1.employee_id')
                    ->join('tbl_employees as e2', 'i1.interview_interviewer1', '=', 'e2.employee_id')
                    
                    ->where(function ($query) use ($interviewData) {
                        $query->where('interview_interviewer1' ,'=',$interviewData->input('interviewer_1'));
                        $query->orWhere('interview_interviewer1' ,'=',$interviewData->input('interviewer_2'));
                        
                    })
                    ->where('interview_date',$interviewData->input('interview_date'))
                    ->get();

                    $interview_name1 = json_decode(json_encode($interview_name1), TRUE);
                 
                    $interview_name2 =DB::table('tbl_interviews as i1')->select('e1.employee_name','e2.employee_name')->
                    join('tbl_employees as e1', 'i1.interview_interviewer1', '=', 'e1.employee_id')
                    ->leftjoin('tbl_employees as e2', 'i1.interview_interviewer2', '=', 'e2.employee_id')
                    ->where(function ($query) use ($interviewData) {
                        $query->where('interview_interviewer2' ,'=',$interviewData->input('interviewer_2'));
                        $query->orWhere('interview_interviewer2' ,'=',$interviewData->input('interviewer_1'));
                    
                    })
                    ->where('interview_date',$interviewData->input('interview_date'))
                    ->get();
                    $interview_name2 = json_decode(json_encode($interview_name2), TRUE);
                 
                    $name1 = '';
                    $name2 = '';
                    $and = '';
                    if(!empty($interview_name1)){
                        $and = 'and';
                        $name1 =  $interview_name1['0']['employee_name']; 
                    }
                    if(!empty($interview_name2)){
                        $and = 'and';
                        $name2 =  $interview_name2['0']['employee_name'];
                    
                    }

                
                    return response()->json([
                        'status' => 'FALSE',
                        "msg" => "You have already scheduled an interview for this time slot for ".$name1. ' '.$and.' ' .$name2.""
                    ]);
                }
            }    

        }catch(\Exception $e){
            $this->create_error_log("Schedule an Interview", $e);
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
            ActivityTracker::track_activity('Schedule an Interview');
        }   

    }

    protected function detail($interview_id){

        try{

            $title = SELF::MAIN_TITLE;
                        
            $login_user = $this->get_login_user_detail();
            
            $notice_period = ['Immediately','5 Days','10 Days','15 Days','1 Month','2 Month','6 Month'];

            $interview = Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
            ->join('tbl_requisition','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
            ->join('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
            ->leftJoin('tbl_room_screen','tbl_room_screen.room_id','tbl_interviews.interview_room_nbr')
            ->join('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
            ->whereIn('interview_status_id',function($query){
                $query->from('tbl_interview_status')
                ->selectRaw('Max(interview_status_id)')
                ->groupBy('main_interview_id');
            })
            ->where('interview_id',$interview_id)->get()->toArray();
          
            $departments = Department::get()->toArray();

            $requisitions = Requisition::join('tbl_departments','tbl_departments.department_id','=','tbl_requisition.requisition_department')->get(['requisition_id','department_name','requisition_job_title','requisition_added_by'])->toArray();

            $employees = Employees::where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();
           
            $interview_round = ['1st','2nd','3rd','4th','5th','6th','7th','8th'];
            $next_interview_round = array_search($interview[0]['interview_round'],$interview_round);

            $candidate_id = $interview['0']['candidate_id'];

            $office_rooms = RoomScreen::get()->toArray();
            
            $employee_onboarding = OnBoardingEmployee::join('tbl_interview_candidate','tbl_employee_onboarding.onboarding_candidate_id','tbl_interview_candidate.candidate_id')->where('onboarding_candidate_id', $candidate_id)->get('onboarding_candidate_id')->toArray();

            $interview_status_levels = InterviewStatusLevel::get()->toArray();
   
            $interview_status = InterviewStatus::join('tbl_interview_status_level','tbl_interview_status.interview_status_level_id','tbl_interview_status_level.interview_status_level_id')->where('main_interview_id',$interview_id)->get()->toArray();
            
            $send_data = compact('title', 'login_user',  'notice_period', 'interview','employees','departments','interview_round','next_interview_round','employee_onboarding','office_rooms','interview_status_levels','interview_status','requisitions');
            
            return view('interview.interview-detail')->with($send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View Interview Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Interview Detail');
          
        }
    }

    protected function mydetail($interview_id){

       try{
            $title = SELF::TITLE_GLOBAL;
                            
            $login_user = $this->get_login_user_detail();
            
            $departments = Department::get()->toArray();
  
            $employees = Employees::where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();
            
            $interview = Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
            ->join('tbl_requisition','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
            ->join('tbl_room_screen','tbl_room_screen.room_id','tbl_interviews.interview_room_nbr')
            ->join('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
            ->join('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
            ->whereIn('interview_status_id',function($query){
                $query->from('tbl_interview_status')
                ->selectRaw('Max(interview_status_id)')
                ->groupBy('main_interview_id');
            })
            ->where('interview_id',$interview_id)->get()->toArray();

            $send_data = compact('title', 'login_user','interview','employees','departments');

            if(!empty($interview)){
                if($interview[0]['interview_interviewer1'] == session('user_session') || $interview[0]['interview_interviewer2'] == session('user_session')){
                    return view('interview.my-interview-detail')->with($send_data);
                }else{
                    return view('errors.403');
                }
            }else{
                return view('errors.403');
            }
        }
        catch(\Exception $e){
                $this->create_error_log('View Personal Interview Detail', $e);
                $send_data = compact('title');
                return view('errors.500');
        }
        finally{  
            ActivityTracker::track_activity('View Personal Interview Detail');
        }
    }

    protected function my_interviews(Request $request){

        try{
            $title = SELF::TITLE_GLOBAL;
                            
            $login_user = $this->get_login_user_detail();

            $interview_status_level = InterviewStatusLevel::get()->toArray();

            $where_clause = '1=1 ';
            if(!empty($request->candidate_name) || !empty($request->interview_date) || !empty($request->interview_status) || !empty($request->interview_type)){
               
                if(!empty($request->candidate_name)){
                    $where_clause .= 'AND candidate_id = "'.$request->candidate_name.'"';
                }
                if(!empty($request->interview_date)){
                    $scheduled_time = date("Y-m-d", strtotime($request->interview_date));
                    $where_clause .= 'AND interview_date LIKE "%'.  $scheduled_time.'%"';
                }
                if(!empty($request->interview_status)){
                    $where_clause .= ' AND interview_status_level = "'.$request->interview_status.'"';
                }
                if(!empty($request->interview_type)){
                    $where_clause .= ' AND interview_type = "'.$request->interview_type.'"';
                }
            }

            $interviews =  Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
            ->join('tbl_requisition','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
            ->join('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
            ->join('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
            ->whereIn('interview_status_id',function($query){
                $query->from('tbl_interview_status')
                ->selectRaw('Max(interview_status_id)')
                ->groupBy('main_interview_id');
            })
            ->where(function($query) {
                $query->where('interview_interviewer1','=',session('user_session'))->orWhere('interview_interviewer2','=',session('user_session'));
            })
            ->whereRaw($where_clause)
            ->orderBy("interview_id","DESC" )->get(['interview_id', 'candidate_id', 'candidate_name','interview_interviewer1','interview_interviewer2','interview_time','department_name','interview_interviewer_remarks', 'interview_interviewer_remarks2','interview_date','department_id','interview_status_level','interview_type'])->toArray();
            
            $interviews_candidate_names =  Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id') 
            ->where(function($query) {
                $query->where('interview_interviewer1','=',session('user_session'))->orWhere('interview_interviewer2','=',session('user_session'));
            })->orderBy("interview_id","DESC" )->get(['candidate_id', 'candidate_name'])->toArray();
            
            $send_data = compact('title', 'login_user', 'interviews','interview_status_level','interviews_candidate_names');
            
            return view('interview.my-interviews')->with($send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View Personal Interviews', $e);
            $send_data = compact('title');
            return view('errors.500');

        }
        finally{
            ActivityTracker::track_activity('View Personal Interviews');
        
        }
    }
    
    protected function update_interview_details(Request $interviewData){
		
        try{

            if(!empty($interviewData->input('room_no'))){
                $interview_room_name_update = RoomScreen::where('room_id',$interviewData->input('room_no'))->get('room_name')->toArray();

                if(empty($interview_room_name_update)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This room name has been deleted.',
                    ]);
                }
            }

            $filename = '';

            if($interviewData->hasFile('candidate_cv')){
                $filename = $interviewData->file('candidate_cv')->getClientOriginalExtension();
            }

            
            if($interviewData->input("interview_type") == "Physical"){
                $interview_type = 'room_no';
            }else{
                $interview_type = 'interview_link';
            }

            $validator = Validator::make($interviewData->all(), [
                'interview_date' => 'required|date_format:Y-m-d|after:yesterday',
            ]);  
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            if($interviewData->input('interview_date') == date('Y-m-d')){
                $interview_time_validation = 'required|date_format:H:i|after:'.now()->format('H:i');
            }else{
                $interview_time_validation = 'required|date_format:H:i';
            }

            if(!empty($interviewData->input('candidate_email'))){
                $employee_email_check = Employees::where('employee_isactive',1)->where(function ($query) use ($interviewData) {
                    $query->where('employee_personal_email',$interviewData->input('candidate_email'))
                    ->orWhere('employee_work_email',$interviewData->input('candidate_email'));
                })
                
                ->get('employee_id')->toArray();
               
                if(!empty($employee_email_check)){
                    return response()->json([
                        'msg' =>'This email is already registered in employees.',
                        'status' => 'FALSE'
                    ]);
                }
            }

            $validator = Validator::make($interviewData->all(), [
                'interviewer_1' => 'required',
                'interview_date' => 'required|date_format:Y-m-d|after:yesterday', 
                'interview_time' =>  $interview_time_validation,
                'duration' =>  'required|numeric',
                'candidate_name' => 'required|regex:/^[\pL\s\&\,(\)]+$/u', 
                'interview_department' => 'required', 
                'interview_jobtitle' => 'required', 
                $interview_type => 'required',
                'interview_round' => 'required',
                'contact_number' =>  ['required','unique:tbl_interview_candidate,candidate_contact_no,'.$interviewData->input('candidate_id').',candidate_id', 'numeric'],
                'requisition_reference' => 'required',
                'candidate_email' => ['nullable','unique:tbl_interview_candidate,candidate_email,'.$interviewData->input('candidate_id').',candidate_id', 'email','regex:/^[^@]+@\w+(\.\w+)+$/'],
                'candidate_cv' => ['max:10000',($filename == 'txt') ? 
                File::types(['jpg', 'jpeg', 'png','docx','pdf','txt'])
                :
                File::types(['jpg', 'jpeg', 'png','docx','pdf'])],
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                if(!empty($interviewData->input('interviewer_1'))){

                    $employee_update_interviewer_1 = Employees::where('employee_isactive','=', 1)->where('employee_id', $interviewData->input('interviewer_1'))->get(['employee_id','employee_code','employee_name'])->toArray();

                    if(empty($employee_update_interviewer_1)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Selected interviewer 1st doesn't exist in active employees.",
                        ]);

                    }
                }
                
                if(!empty($interviewData->input('interviewer_2'))){

                    $employee_update_interviewer_2 = Employees::where('employee_isactive','=', 1)->where('employee_id', $interviewData->input('interviewer_2'))->get(['employee_id','employee_code','employee_name'])->toArray();

                    if(empty($employee_update_interviewer_2)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Selected interviewer 2nd doesn't exist in active employees.",
                        ]);

                    }
                }    

                $update_interview_candidate = InterviewCandidate::find($interviewData->input('candidate_id'));
                $update_interview = Interview::find($interviewData->input('interview_id'));
    
                if($interviewData->input('interviewer_1') != $interviewData->input('interviewer_2')){

                    if($interviewData->hasFile('candidate_cv')){

                        if (file_exists(storage_path('app/portal_documents/interviews/'.$update_interview_candidate->interview_candidate_cv))) {
                    
                            unlink(storage_path('app/portal_documents/interviews/'.$update_interview_candidate->interview_candidate_cv));
                        }

                        $fileNameToStore = $interviewData->input('candidate_name').''.time().'.'.$filename;
                            
                        $interviewData->file('candidate_cv')->storeAs('portal_documents/interviews/',$fileNameToStore);
            
                        $update_interview_candidate->interview_candidate_cv = $fileNameToStore;
            
                    }

                    if($update_interview->interview_interviewer1 != $interviewData->input('interviewer_1')){
                    
                        $this->send_notification('Interview Scheduling','Dear Employee, HR schedule an interview that you have to take on '.$interviewData->input('interview_date').'','my-interviews?interview_id='. $interviewData->input('interview_id') .'',$interviewData->input('interviewer_1'),NULL,NULL,'no');
            
                        $this->send_notification('Interview Rights Removal','Dear Employee, HR cancelled an interview for '.$interviewData->input('interview_date').'','#',$update_interview->interview_interviewer1,NULL,NULL,'no');
            
                    }
            
                    if($update_interview->interview_interviewer2 != $interviewData->input('interviewer_2')){
            
                    
                        $this->send_notification('Interview Scheduling','Dear Employee, HR schedule an interview that you have to take on '.$interviewData->input('interview_date').'','my-interviews?interview_id='. $interviewData->input('interview_id') .'',$interviewData->input('interviewer_2'),NULL,NULL,'no');


                        $this->send_notification('Interview Rights Removal','Dear Employee, HR cancelled an interview for '.$interviewData->input('interview_date').'','#',$update_interview->interview_interviewer1,NULL,NULL,'no');
                    }
            
                    if($update_interview->interview_interviewer1 == $interviewData->input('interviewer_1')){
                        if($update_interview->interview_date != $interviewData->input('interview_date') || $update_interview->interview_time != $interviewData->input('interview_time')){

                            $this->send_notification('Interview Details Updated','Dear Employee, HR updated an interview timings which was sceduled for '.$update_interview->interview_date.'. Interview ID # '.$interviewData->input('interview_id').'','my-interviews?interview_id='. $interviewData->input('interview_id') .'',$interviewData->input('interviewer_1'),NULL,NULL,'no');

                        }
                    }

                    if($update_interview->interview_interviewer2 == $interviewData->input('interviewer_2')){
                        if($update_interview->interview_date != $interviewData->input('interview_date') || $update_interview->interview_time != $interviewData->input('interview_time')){
            
                            $this->send_notification('Interview Details Updated','Dear Employee, HR updated an interview timings which was sceduled for '.$update_interview->interview_date.'. Interview ID # '.$interviewData->input('interview_id').'','my-interviews?interview_id='. $interviewData->input('interview_id') .'',$interviewData->input('interviewer_2'),NULL,NULL,'no');
            
                        }
                    }

                    $update_interview->interview_candidate_id = $interviewData->input('candidate_id');
                    $update_interview->requisition_reference = $interviewData->input("requisition_reference");
                    $update_interview->interview_type = $interviewData->input("interview_type");
                    $update_interview->interview_link = $interviewData->input("interview_link");
                    $update_interview->interview_date = date('Y-m-d',strtotime($interviewData->input('interview_date')));
                    $update_interview->interview_time = $interviewData->input('interview_time');
                    $update_interview->interview_duration = $interviewData->input('duration');
                    $update_interview->interview_room_nbr = $interviewData->input('room_no');
                    $update_interview->interview_interviewer1 = $interviewData->input('interviewer_1');
                    $update_interview->interview_interviewer2 = $interviewData->input('interviewer_2');
                    $update_interview->interview_round = $interviewData->input('interview_round');
                    $update_interview->interview_additional_remarks = $interviewData->input('additional_remarks');
                    $update_interview->interview_department = $interviewData->input('interview_department');
                    $update_interview->interview_jobtitle = $interviewData->input('interview_jobtitle');
                    $update_interview->interview_scheduled_time = date('d-m-Y H:i:s');


                    $update_interview_candidate->candidate_name = $interviewData->input('candidate_name');
                    $update_interview_candidate->interview_noticepriod = $interviewData->input('notice_period');
                    $update_interview_candidate->interview_current_salary = $interviewData->input('current_salary');
                    $update_interview_candidate->interview_expected_salary = $interviewData->input('expected_salary');
                    $update_interview_candidate->candidate_contact_no = $interviewData->input('contact_number');
                    $update_interview_candidate->candidate_email = $interviewData->input('candidate_email');
                    $update_interview_candidate->candidate_created_at = date('d-m-Y H:i:s');
                    $update_interview_candidate->save();
                    $process = $update_interview->save();


                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Interview details updated successfully.',
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Interview addition failed. Please try again after some time.',
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You have assigned same interviewers. One employee can\'t be marked as both.',
                    ]);
                }
            }   
           
           
        }
        catch(\Exception $e){
            $this->create_error_log("Update Interview Details", $e);
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
            ActivityTracker::track_activity('Update Interview Details'); 
        }   
    }

    protected function changesecond_interviewer(Request $interview_data){
		
		try{

            $validator = Validator::make($interview_data->all(), [
                'interviewer_2' => 'required',
            ]);  
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{

                $login_user = $this->get_login_user_detail();

                if(!empty($interview_data->input('interviewer_2'))){

                    $employee_change_interviewer_2 = Employees::where('employee_isactive','=', 1)->where('employee_id', $interview_data->input('interviewer_2'))->get(['employee_id','employee_code','employee_name'])->toArray();

                    if(empty($employee_change_interviewer_2)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Selected interviewer 2nd doesn't exist in active employees.",
                        ]);

                    }
                }   

                $update_interview = Interview::find($interview_data->input('interview_id'));
                
                if($update_interview->interview_interviewer1 == $interview_data->input('interviewer_2')){

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => '1st and 2nd interviewer can\'t be same',
                    ]);

                }else{

                    if(!empty($update_interview->interview_interviewer2)){
                    
                        $this->send_notification('Interview Rights Removal','Dear Employee, '.$login_user[0]['employee_name'].' removed you from 2nd Interviewer position in interview # '.$interview_data->input('interview_id').'','#',$update_interview->interview_interviewer2,NULL,NULL,'no');

                    }
                
                    $this->send_notification('Interview Scheduling','Dear Employee, '.$login_user[0]['employee_name'].' assign you an interview that you have to take on '.$update_interview->interview_date.' '.$update_interview->interview_time.'','my-interviews?interview_id='. $interview_data->input('interview_id') .'',$interview_data->input('interviewer_2'),NULL,NULL,'no');
                
                    $this->send_notification('2nd Interviewer Update','Dear HR, '.$login_user[0]['employee_name'].' changed 2nd interviewer on interview # '.$interview_data->input('interview_id').'. Please click here to view.','interviewdetail/'.$interview_data->input('interview_id'),NULL,NULL,NULL,'yes');

                    $update_interview->interview_interviewer2 = $interview_data->input('interviewer_2');
                    $process = $update_interview->save();
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Interviewer changed successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Interviewer failed. Please try again after some time.",
                        ]);
                    }

                }
            }   
        }
        catch(\Exception $e){
            $this->create_error_log("Change 2nd Interviewer", $e);
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
            ActivityTracker::track_activity('Change 2nd Interviewer'); 
        }   
    }

    protected function submit_interviewer_remarks(Request $remarks_data){
		
		try{

            $validator = Validator::make($remarks_data->all(), [
                'my_remarks' => 'required',
            ]);
                
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $submit_remarks = Interview::find($remarks_data->input('interview_id'));

                if(empty($submit_remarks->interview_interviewer_remarks)){  

                    $submit_remarks->interview_interviewer_remarks = $remarks_data->input('my_remarks');
                
                    $process = $submit_remarks->save();
                    
                    if($process == 1){

                        $this->send_notification('Interview Remarks','Dear HR, 1st interviewer of interview # '.$remarks_data->input('interview_id').' submitted a feedback. Please click here to view.','interviews/interviewdetail/'.$remarks_data->input('interview_id'),NULL,NULL,NULL,'yes');

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Remarks submitted successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Remarks submit failed. Please try again after some time.",
                        ]);
                    }
                }
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Remarks already submitted.',
                ]);    
            }   
        }
        catch(\Exception $e){
            $this->create_error_log("Submit Interviewer Remarks", $e);
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
            ActivityTracker::track_activity('Submit Interviewer Remarks'); 
        }   
		
    }

  
    protected function submit_second_interviewer_remarks(Request $remarks_data){
		
		try{
            $login_user = $this->get_login_user_detail();

            $validator = Validator::make($remarks_data->all(), [
                'my_remarks' => 'required',
            ]);
                
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
            
                $submit_remarks = Interview::find($remarks_data->input('interview_id'));

                if($login_user[0]['employee_id'] == $submit_remarks->interview_interviewer2){

                    if(empty($submit_remarks->interview_interviewer_remarks2)){  
                        $submit_remarks->interview_interviewer_remarks2 = $remarks_data->input('my_remarks');
                        $submit_remarks->interview_interviewer2 = session('user_session');
                        $process = $submit_remarks->save();
                        if($process == 1){
    
                            $this->send_notification('Interview Remarks','Dear HR, 2nd interviewer of interview # '.$remarks_data->input('interview_id').' submitted a feedback. Please click here to view.','interviews/interviewdetail/'.$remarks_data->input('interview_id'),NULL,NULL,NULL,'yes');
    
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Remarks submitted successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Remarks submit failed. Please try again after some time.",
                            ]);
                        }
                    }
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Remarks already submitted.',
                    ]);    
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You are not part of this interview.',
                    ]);   
                }
               
            }    
        }
        catch(\Exception $e){
            $this->create_error_log("Submit Interviewer Remarks", $e);
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
            ActivityTracker::track_activity('Submit Interviewer Remarks');
        }   
            
    }

    protected function submit_interviewer_chat(Request $request_data){
		
		try{

            $interviewer1 = $request_data->input('interviewer1');
            $interviewer2 = $request_data->input('interviewer2');
    
            $login_user = $this->get_login_user_detail();
    
            $factory = (new Factory)
                ->withServiceAccount(__DIR__.'/firebase-secretkey.json')
                ->withDatabaseUri('https://sourcecode-apps-d94da-hcm.firebaseio.com/');
    
            $database = $factory->createDatabase();
            $NewChatMessage = $database->getReference('chats/InterviewsChat/')->push([
                'message' => $request_data->message,
                'sender_id' => $login_user[0]['employee_id'],
                'interview_id' => $request_data->input('interview_id'),
                'sender_name' => $login_user[0]['employee_name'],
                'sender_avatar' => ($login_user[0]['profile_avatar']) ?? 'source-code.png',
                'date' => date('d F, Y'),
                'time' => date('h:i A')
            ]);
            
            return $NewChatMessage;
        }catch(\Exception $e){
            $this->create_error_log("Submit Interview Chat", $e);
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
            ActivityTracker::track_activity('Submit Interview Chat');
        }   
    }

    protected function interviewer_employee_hired(Request $request_data){
       
        try{

            $validator = Validator::make($request_data->all(), [
                'joining_date' => 'required|date_format:Y-m-d|after:yesterday', 
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $checking_onboarding = OnBoardingEmployee::where('onboarding_candidate_id', $request_data->input('candidate_id'))->get(['onboarding_candidate_id'])->toArray();

                if(empty($checking_onboarding)){


                    $hired_employee = new OnBoardingEmployee();
                    $hired_employee->onboarding_candidate_id = $request_data->input('candidate_id');
                    $hired_employee->department_name = $request_data->input('deprt_id');
                    $hired_employee->doj = $request_data->input('joining_date');
                    $hired_employee->employee_hired_created_at = date('d-m-Y H:i:s');

                    $process = $hired_employee->save();
                    
                    if($process == 1){

                        $itEmployees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('department_name','=','IT')->where('employee_isactive','=',1)->get(['employee_id'])->toArray();
                    
                        $department_managers = Employees::where('employee_isactive','=',1)->where('employee_department','=',$request_data->input('deprt_id'))->where('employee_is_manager',1)->get('employee_id')->toArray();
                    
                    
                        if(!empty($itEmployees) || !empty($department_managers)){

                            foreach($itEmployees as $employee_it){

                                $this->send_notification('Employee On Board','Dear Employee, HR hired an employee named '.$request_data->input('candidate_name').' in '.$request_data->input('deprt_name').' Department, with the joining date '. $request_data->input('joining_date').'','#',$employee_it['employee_id'],NULL,NULL,'no');
                            
                            }
                        
                            foreach($department_managers as $department_manager){

                                $this->send_notification('Employee On Board','Dear Employee, New Employee  named '.$request_data->input('candidate_name').' is on board for your '.$request_data->input('deprt_name').' department with the joining date '. $request_data->input('joining_date').'','#',$department_manager['employee_id'],NULL,NULL,'no');
                            
                            }

                        }
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Candidate added in on board employees",
                        ]);

                        
                    }else{
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Candidate didn't added in on board employees try again.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'False',
                        'msg' => "Candidate already exits in on board employee",
                    ]);
                }
            }
        }
        catch(\Exception $e){
            $this->create_error_log('Add New Employee OnBaording Process', $e);
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
            ActivityTracker::track_activity('Add New Employee OnBaording Process');
        }
    }

    
    protected function interviewer_employee_status(Request $request_data){
      
        try{
          
            $validator = Validator::make($request_data->all(), [
                'interviewer_status' => 'required', 
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                $check_interview_status = InterviewStatus::where('main_interview_id',$request_data->input('interview_id'))->orderBy('interview_status_id','DESC')->get('interview_status_level_id')->toArray();
                
              
                if($check_interview_status[0]['interview_status_level_id'] != $request_data->input('interviewer_status')){
                    $interview_employee = new InterviewStatus();    
                    $interview_employee->interview_status_level_id = $request_data->input('interviewer_status');
                    $interview_employee->main_interview_id = $request_data->input('interview_id');
                    $interview_employee->interview_status_created_by = $request_data->input('employee_id');
                    $interview_employee->interview_status_created_at =  date('d-m-Y H:i:s');
    
                    $process = $interview_employee->save();
    
                    $requisition_status = InterviewStatusLevel::where('interview_status_level_id', $request_data->input('interviewer_status'))->get(['interview_status_level_id', 'interview_status_level'])->toArray();
    
                    $update_requisition_status = new RequisitionStatus();
                    $update_requisition_status->main_requisition_id =  $request_data->input("requisition_reference_id");
                    $update_requisition_status->requisition_status = $requisition_status[0]['interview_status_level'];
                    $update_requisition_status->requisition_status_created_by = $request_data->input('employee_id');
                    $update_requisition_status->requisition_status_created_at = date('d-m-Y H:i:s');
                    $process = $update_requisition_status->save();
                   
                    if($process == 1){
    
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Interview status successfully updated.",
                        ]);
                        
                        
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Interview status not successfully update try again.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Interview is already on this status. Please select different.",
                    ]);
                }
               
            }
        }
        catch(\Exception $e){
            $this->create_error_log('Interviewer Employee Status', $e);
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
            ActivityTracker::track_activity('Interviewer Employee Status');
        }
    }

    protected function download_candidate_cv(Request $request){
        try{
            if(!empty($request->path)){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
                return response()->download(storage_path('app/portal_documents/'.$request->path));
            }else{
                return redirect(url());
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
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
            ActivityTracker::track_activity($request->title);
        }
    }


    protected function candidate_interviews_list(){

        try{

            $title = SELF::INTERVIEW_CANDIDATE;
                    
            $login_user = $this->get_login_user_detail();

            $interview_candidates = InterviewCandidate::join('tbl_interviews','tbl_interview_candidate.candidate_id','tbl_interviews.interview_candidate_id')
            ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
            ->whereIn('interview_id',function($query){
                $query->from('tbl_interviews')
                ->selectRaw('Max(interview_id)')
                ->groupBy('interview_candidate_id');
            })
            ->orderBy('candidate_id','DESC')->get(['candidate_id','candidate_name','interview_jobtitle', 'department_name', 'interview_candidate_cv'])->toArray();
          
            $interview_candidate_count = InterviewCandidate::count();
            
            $interview_candidate_onboard_count = OnBoardingEmployee::where('onboard_status',1)->count();
            
            $send_data = compact('title', 'login_user','interview_candidates','interview_candidate_count','interview_candidate_onboard_count');
            
            return view('interview.candidate-interviews-list', $send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View All Interviews Candidate', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View All Interviews Candidate');
          
        }

    }

    
    protected function interview_candidate_detail($candidate_id){

      
        try{

            $title = SELF::INTERVIEW_CANDIDATE;
                    
            $login_user = $this->get_login_user_detail();

            $interview_candidates = Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id')
                ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
                ->join('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
                ->join('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
                ->whereIn('interview_status_id',function($query){
                    $query->from('tbl_interview_status')
                    ->selectRaw('Max(interview_status_id)')
                    ->groupBy('main_interview_id');
                })
                ->where('interview_candidate_id',$candidate_id)
                ->orderBy('interview_id','DESC')->get(['interview_id','candidate_name','interview_interviewer1','interview_interviewer2','interview_time','department_name','interview_interviewer_remarks','interview_date','department_id','interview_status_level'])->toArray();

            $send_data = compact('title', 'login_user','interview_candidates');

            return view('interview.candidate-interview-detail', $send_data);

           
        }
        catch(\Exception $e){
            $this->create_error_log('View Interviews Candidate Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Interviews Candidate Detail');
          
        }
    }
}
