<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{InterviewStatusLevel, Interview, Employees};
use App\Traits\{LoginEmployee, LogGenerateTrait, ApiResponseTrait, NotificationTrait};
use App\Helpers\ActivityTracker;

class InterviewsApiController extends Controller
{   
    use LoginEmployee, LogGenerateTrait, ApiResponseTrait, NotificationTrait;

    const TITLE_GLOBAL = "my-interviews";
    
    protected function my_interviews(Request $request){
        try{
            $title = SELF::TITLE_GLOBAL;
                            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $interview_status_level = InterviewStatusLevel::get()->toArray();

            $where_clause = '1=1 ';
            if(!empty($request->get('candidate_name')) || !empty($request->get('interview_date')) || !empty($request->get('interview_status')) || !empty($request->get('interview_type'))){
               
                if(!empty($request->get('candidate_name'))){
                    $where_clause .= 'AND candidate_id = "'.$request->get('candidate_name').'"';
                }
                if(!empty($request->get('interview_date'))){
                    $scheduled_time = date("Y-m-d", strtotime($request->get('interview_date')));
                    $where_clause .= 'AND interview_date LIKE "%'.  $scheduled_time.'%"';
                }
                if(!empty($request->get('interview_status'))){
                    $where_clause .= ' AND interview_status_level = "'.$request->get('interview_status').'"';
                }
                if(!empty($request->get('interview_type'))){
                    $where_clause .= ' AND interview_type = "'.$request->get('interview_type').'"';
                }
            }
            
            $interviews =  Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
            ->join('tbl_requisition','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
            ->join('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
            ->join('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
            ->leftjoin('tbl_employees as e1','e1.employee_id','=','tbl_interviews.interview_interviewer1')
            ->leftjoin('tbl_employees as e2','e2.employee_id','=','tbl_interviews.interview_interviewer2')
            ->whereIn('interview_status_id',function($query){
                $query->from('tbl_interview_status')
                ->selectRaw('Max(interview_status_id)')
                ->groupBy('main_interview_id');
            })
            ->where(function($query) use($login_user) {
                $query->where('interview_interviewer1','=',$login_user[0]['employee_id'])->orWhere('interview_interviewer2','=',$login_user[0]['employee_id']);
            })
            ->whereRaw($where_clause)
            ->orderBy("interview_id","DESC" )->get(['interview_id', 'candidate_id', 'candidate_name','interview_interviewer1','interview_interviewer2','interview_time','department_name','interview_interviewer_remarks', 'interview_interviewer_remarks2','interview_date','department_id','interview_status_level','interview_type','e1.employee_name as interviewer_1','e2.employee_name as interviewer_2'])->toArray();
            
            $interviews_candidate_names =  Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id') 
            ->where(function($query) use($login_user) {
                $query->where('interview_interviewer1','=',$login_user[0]['employee_id'])->orWhere('interview_interviewer2','=',$login_user[0]['employee_id']);
            })->orderBy("interview_id","DESC" )->get(['candidate_id', 'candidate_name'])->toArray();
            
            $data = compact('title', 'interviews','interview_status_level','interviews_candidate_names');
            
            return $this->api_response_json(TRUE,"My Interviews",$data,200);
        }
        catch(\Exception $e){
             
            $this->create_error_log_from_api('View Personal Interviews API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{

            ActivityTracker::track_activity_from_api('View Personal Interviews API',  $request->header('Bearer-Token'));

        }
    }

    protected function mydetail(Request $request,$interview_id){

        try{
             $title = SELF::TITLE_GLOBAL;
                             
             $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
             
             $interview = Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id')
             ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
             ->join('tbl_requisition','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
             ->leftjoin('tbl_employees as ec','ec.employee_id','=','tbl_interviews.interview_created_by')
             ->leftjoin('tbl_employees as e1','e1.employee_id','=','tbl_interviews.interview_interviewer1')
             ->leftjoin('tbl_employees as e2','e2.employee_id','=','tbl_interviews.interview_interviewer2')
             ->join('tbl_room_screen','tbl_room_screen.room_id','tbl_interviews.interview_room_nbr')
             ->join('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
             ->join('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
             ->whereIn('interview_status_id',function($query){
                $query->from('tbl_interview_status')
                ->selectRaw('Max(interview_status_id)')
                ->groupBy('main_interview_id');
             })
             ->where('interview_id',$interview_id)
             ->get(['interview_id','requisition_reference','requisition_job_title','interview_date','interview_time','interview_duration','department_name','interview_jobtitle','e1.employee_name as interviewer_1','e2.employee_name as interviewer_2','ec.employee_name as interview_created_by','interview_round','candidate_name','interview_candidate_cv','candidate_contact_no','interview_current_salary','interview_expected_salary','interview_noticepriod','interview_scheduled_time','interview_type','interview_link','interview_additional_remarks','interview_interviewer_remarks','interview_interviewer_remarks2','interview_interviewer1','interview_interviewer2','room_name','interview_status_level'])->toArray();

             $employees = Employees::where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();

             $data = compact('title','interview','employees');
 
             if(!empty($interview)){
                 if($interview[0]['interview_interviewer1'] == $login_user[0]['employee_id'] || $interview[0]['interview_interviewer2'] == $login_user[0]['employee_id']){
                     
                     return $this->api_response_json(TRUE,"My Interviews",$data,200);
                 }else{
                    return $this->api_response_json(FALSE,"My Interviews","",200);
                 }
             }else{
                return $this->api_response_json(FALSE,"My Interviews","",200);
             }

             return $this->api_response_json(TRUE,"My Interviews",$data,200);
         }
         catch(\Exception $e){

                 $this->create_error_log_from_api('View Personal Interview Detail API', $e, $request->header('Bearer-Token'));

                 return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getLine(),500);
         }
         finally{  

             ActivityTracker::track_activity_from_api('View Personal Interview Detail API',  $request->header('Bearer-Token'));

         }
    }

    protected function submit_interviewer_remarks(Request $request){
		
		try{

            $validator = Validator::make($request->post(), [
                'my_remarks' => 'required',
                'interview_id' => 'required',
            ]);
                
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {
                
                $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

                $submit_remarks = Interview::find($request->post('interview_id'));

                if(!empty($submit_remarks)){  

                    if($submit_remarks->interview_interviewer1 == $login_user[0]['employee_id']){
                        if(empty($submit_remarks->interview_interviewer_remarks)){
                            $submit_remarks->interview_interviewer_remarks = $request->post('my_remarks');
                            $process = $submit_remarks->save();
                            if($process == 1){
                                $this->send_notification_api('Interview Remarks','Dear HR, 1st interviewer of interview # '.$request->post('interview_id').' submitted a feedback. Please click here to view.','interviews/interviewdetail/'.$request->post('interview_id'),NULL,NULL,NULL,'yes',$login_user[0]['employee_id']);

                                return $this->api_response_json(TRUE,"Remarks submitted successfully.","",200);
                            }else{
                                return $this->api_response_json(FALSE,"Remarks submit failed. Please try again after some time.","",200);
                            }
                        }else{
                            return $this->api_response_json(FALSE,"Remarks Already Submitted","",200);
                        }
                    }elseif($submit_remarks->interview_interviewer2 == $login_user[0]['employee_id']){
                        if(empty($submit_remarks->interview_interviewer_remarks2)){
                            $submit_remarks->interview_interviewer_remarks2 = $request->post('my_remarks');
                            $process = $submit_remarks->save();
                            if($process == 1){
                                $this->send_notification_api('Interview Remarks','Dear HR, 2nd interviewer of interview # '.$request->post('interview_id').' submitted a feedback. Please click here to view.','interviews/interviewdetail/'.$request->post('interview_id'),NULL,NULL,NULL,'yes',$login_user[0]['employee_id']);

                                return $this->api_response_json(TRUE,"Remarks submitted successfully.","",200);
                            }else{
                                return $this->api_response_json(FALSE,"Remarks submit failed. Please try again after some time.","",200);
                            }
                        }else{
                            return $this->api_response_json(FALSE,"Remarks Already Submitted","",200);
                        }
                    }else{
                        return $this->api_response_json(FALSE,"You are not part of this interview","",200);
                    }
                    
                }else{
                    return $this->api_response_json(FALSE,"Invalid Interview Id","",200);
                }
            }    
        }catch(\Exception $e){

            $this->create_error_log_from_api('Submit Interviewer Remarks API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{
            ActivityTracker::track_activity_from_api('Submit Interviewer Remarks API',  $request->header('Bearer-Token'));
        }   
            
    }

    protected function changesecond_interviewer(Request $request){
		
		try{

            $validator = Validator::make($request->post(), [
                'interviewer_2' => 'required',
                'interview_id' => 'required'
            ]);  
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else{

                $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

                if(!empty($request->post('interviewer_2'))){

                    $employee_change_interviewer_2 = Employees::where('employee_isactive','=', 1)->where('employee_id', $request->post('interviewer_2'))->get(['employee_id','employee_code','employee_name'])->toArray();

                    if(empty($employee_change_interviewer_2)){
                        return $this->api_response_json(FALSE,"Selected interviewer 2nd doesn't exist in active employees.","",200);
                    }
                }   

                $update_interview = Interview::find($request->post('interview_id'));
                
                if($update_interview->interview_interviewer1 != $login_user[0]['employee_id']){

                    return $this->api_response_json(FALSE,"You can't update interviewer.","",200);

                }
                if($update_interview->interview_interviewer1 == $request->post('interviewer_2')){

                    return $this->api_response_json(FALSE,"1st and 2nd interviewer can't be same","",200);

                }else{

                    if(!empty($update_interview->interview_interviewer2)){
                    
                        $this->send_notification_api('Interview Rights Removal','Dear Employee, '.$login_user[0]['employee_name'].' removed you from 2nd Interviewer position in interview # '.$request->post('interview_id').'','#',$update_interview->interview_interviewer2,NULL,NULL,'no',$login_user[0]['employee_id']);

                    }
                
                    $this->send_notification_api('Interview Scheduling','Dear Employee, '.$login_user[0]['employee_name'].' assign you an interview that you have to take on '.$update_interview->interview_date.' '.$update_interview->interview_time.'','my-interviews?interview_id='. $request->post('interview_id') .'',$request->post('interviewer_2'),NULL,NULL,'no',$login_user[0]['employee_id']);
                
                    $this->send_notification_api('2nd Interviewer Update','Dear HR, '.$login_user[0]['employee_name'].' changed 2nd interviewer on interview # '.$request->post('interview_id').'. Please click here to view.','interviewdetail/'.$request->post('interview_id'),NULL,NULL,NULL,'yes',$login_user[0]['employee_id']);

                    $update_interview->interview_interviewer2 = $request->post('interviewer_2');
                    $process = $update_interview->save();
                    if($process == 1){
                        return $this->api_response_json(TRUE,"Interviewer changed successfully.","",200);
                    }else{
                        return $this->api_response_json(FALSE,"Interviewer failed. Please try again after some time.","",200);
                    }
                }
            }   
          
        }catch(\Exception $e){

            $this->create_error_log_from_api('Change 2nd Interviewer API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{
            ActivityTracker::track_activity_from_api('Change 2nd Interviewer API',  $request->header('Bearer-Token'));
        }
    }
}
