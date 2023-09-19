<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{Requisition, RequisitionStatus, Employees, Department, Interview, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait};
use App\Helpers\ActivityTracker;

class RequisitionApiController extends Controller
{
   use LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait;

   const MAIN_TITLE = "all-requisition";
   const TITLE_GLOBAL = "my-requisition";

    protected function index(Request $request){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1';

            if(!empty($request->requisition_department) || !empty($request->emp_status) || !empty($request->emp_name) || !empty($request->requisition_job_type) || !empty($request->requisition_status)){


                if(!empty($request->requisition_department)){
                    $where_clause .= ' AND requisition_department = "'.$request->requisition_department.'"';
                }
                if(!empty($request->emp_name)){
                    $where_clause .= ' AND requisition_added_by = "'.$request->emp_name.'"';
                }
                if(!empty($request->requisition_job_type)){
                    $where_clause .= ' AND requisition_job_type = "'.$request->requisition_job_type.'"';
                }
                if(!empty($request->requisition_status)){
                    $where_clause .= ' AND requisition_status = "'.$request->requisition_status.'"';
                }

            }

            $requistion_status_levels = ['Pending', 'Interview Scheduled','Hold for more interviews','Candidate Shortlisted','Candidate Rejected','Offer Placed','Offer Rejected',"Candidate didn't Appeared",'Candidate Hired','Interview Cancelled'];

            $departments = Department::get(['department_id','department_name'])->toArray();

            if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1){

                $employees_data = Employees::where('employee_isactive','=', $status)->get(['employee_id', 'employee_code','employee_name'])->toArray();

                $employee_active = array_column($employees_data, 'employee_id');

                $requisitions = Requisition::join('tbl_departments','tbl_departments.department_id','=','tbl_requisition.requisition_department')
                ->join('tbl_requisition_status','tbl_requisition_status.main_requisition_id','tbl_requisition.requisition_id')
                ->leftjoin('tbl_interviews','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
                ->leftjoin('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
                ->leftjoin('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
                ->whereIn('requisition_status_id',function($query){
                    $query->from('tbl_requisition_status')
                    ->selectRaw('Max(requisition_status_id)')
                    ->groupBy('main_requisition_id');
                })
                ->whereIn('requisition_added_by',$employee_active)
                ->whereRaw($where_clause)
                ->groupBy('requisition_id')
                ->orderBy("requisition_id","DESC")
                ->get(['requisition_id','department_name','requisition_job_title','requisition_job_type','requisition_expected_salary','requisition_shift_time','requisition_shift_start_time','requisition_shift_end_time','requisition_date_needed','requisition_additional_info','requisition_added_by','create_at','requisition_status','main_interview_id','no_of_position'])->toArray();

            }
            elseif($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 1 && $login_user[0]['employee_is_manager'] == 1){

                $login_employee_id = $login_user[0]['employee_id'];

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id, $status);

                $employees_data = Employees::where('employee_isactive','=', $status)->whereIn('employee_id', $total_team_member)->get(['employee_id', 'employee_code','employee_name'])->toArray();

                $requisitions = Requisition::join('tbl_departments','tbl_departments.department_id','=','tbl_requisition.requisition_department')
                ->join('tbl_requisition_status','tbl_requisition_status.main_requisition_id','tbl_requisition.requisition_id')
                ->leftjoin('tbl_interviews','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
                ->leftjoin('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
                ->leftjoin('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
                ->whereIn('requisition_status_id',function($query){
                    $query->from('tbl_requisition_status')
                    ->selectRaw('Max(requisition_status_id)')
                    ->groupBy('main_requisition_id');
                })
                ->whereIn('requisition_added_by',$total_team_member)
                ->whereRaw($where_clause)
                ->groupBy('requisition_id')
                ->orderBy("requisition_id","DESC" )
                ->get(['requisition_id','department_name','requisition_job_title','requisition_job_type','requisition_expected_salary','requisition_shift_time','requisition_shift_start_time','requisition_shift_end_time','requisition_date_needed','requisition_additional_info','requisition_added_by','create_at','requisition_status','no_of_position'])->toArray();

            }

            $interviews = Interview::selectRaw('count(interview_id) as total, requisition_reference')
            ->groupBy('requisition_reference')->get()->toArray();

            $interviews_total = array_column($interviews, 'total');
            $interview_reference = array_column($interviews, 'requisition_reference');

            $interview_count = array_combine($interview_reference, $interviews_total);

            $data = compact('title','requisitions','departments','employees_data','interviews','interview_count','requistion_status_levels');

            return $this->api_response_json(TRUE,"View All Requisition",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View All Requisition API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('View All Requisition API',  $request->header('Bearer-Token'));

        }
    }

    protected function my_requisition(Request $request){

        try{

            $title = Self::TITLE_GLOBAL;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $requisitions = Requisition::join('tbl_departments','tbl_departments.department_id','=','tbl_requisition.requisition_department')
            ->join('tbl_requisition_status','tbl_requisition_status.main_requisition_id','tbl_requisition.requisition_id')
            ->leftjoin('tbl_interviews','tbl_interviews.requisition_reference','tbl_requisition.requisition_id')
            ->leftjoin('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
            ->leftjoin('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
            ->whereIn('requisition_status_id',function($query){
                $query->from('tbl_requisition_status')
                ->selectRaw('Max(requisition_status_id)')
                ->groupBy('main_requisition_id');
            })
            ->where('requisition_added_by', $login_user[0]['employee_id'])
            ->groupBy('requisition_id')
            ->orderBy("requisition_id","DESC" )
            ->get(['requisition_id','department_name','requisition_job_title','requisition_job_type','requisition_expected_salary','requisition_shift_time','requisition_shift_start_time','requisition_shift_end_time','requisition_date_needed','requisition_additional_info','requisition_added_by','create_at','requisition_status','no_of_position','office_location'])->toArray();

            $interviews = Interview::selectRaw('count(interview_id) as total, requisition_reference')
            ->groupBy('requisition_reference')->get()->toArray();

            $interviews_total = array_column($interviews, 'total');
            $interview_reference = array_column($interviews, 'requisition_reference');

            $interview_count = array_combine($interview_reference,$interviews_total);

            $department_name = Department::where('department_id',$login_user[0]['employee_department'])->get('department_name')->toArray();

            $office_location = OfficeLocation::get(['location_id','location_title'])->toArray();

            $data = compact('title','requisitions','department_name','interviews','interview_count','interview_reference','office_location');

            return $this->api_response_json(TRUE,"My Requisition",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('My Requisition API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('View my Requisition API',  $request->header('Bearer-Token'));
        }

    }

    protected function my_requisition_detail(Request $request){

        try{

            $title = SELF::TITLE_GLOBAL;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $department_name = Department::where('department_id',$login_user[0]['employee_department'])->get('department_name')->toArray();

            $requisitions = Requisition::join('tbl_departments','tbl_departments.department_id','=','tbl_requisition.requisition_department')
            ->join('tbl_requisition_status','tbl_requisition_status.main_requisition_id','tbl_requisition.requisition_id')
            ->join('tbl_office_locations','tbl_office_locations.location_id','tbl_requisition.office_location')
            ->where('main_requisition_id',$request->requisition_id)
            ->whereIn('requisition_status_id',function($query){
                $query->from('tbl_requisition_status')
                ->selectRaw('Max(requisition_status_id)')
                ->groupBy('main_requisition_id');
            })
            ->orderBy("requisition_id","DESC" )
            ->get(['requisition_id','department_name','requisition_job_title','requisition_job_type','requisition_expected_salary','requisition_shift_time','requisition_shift_start_time','requisition_shift_end_time','requisition_date_needed','requisition_additional_info','requisition_added_by','create_at','requisition_status','no_of_position','location_title'])->toArray();

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
            ->where('requisition_reference', '=', $request->requisition_id)
            ->orderBy("interview_id","DESC" )->get(['interview_id','candidate_name','interview_interviewer1','interview_interviewer2','interview_time','interview_date','interview_status_level'])->toArray();

            $data = compact('title', 'requisitions','department_name','interviews');

            return $this->api_response_json(TRUE,"View My Requisition detail",$data,200);

        }
        catch(\Exception $e){

            $this->create_error_log_from_api('View Personal Requisition Detail API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{

            ActivityTracker::track_activity_from_api('View Personal Requisition Detail API',  $request->header('Bearer-Token'));
        }
    }

    protected function add_requisition(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [  
                'job_title' => 'required',
                'job_type' => 'required|in:Intern,Probation,Permanent,PartTime',
                'shift_time' => 'required|in:Morning Shift,Afternoon Shift,Evening Shift,Night Shift,Custom time',
                'shift_start' => ($request->post('shift_time') == 'Custom time' ) ? 'required|date_format:H:i' : '' ,
                'shift_end' => ($request->post('shift_time') == 'Custom time' ) ? 'required|date_format:H:i' : '' ,
                'requisition_date' => 'required|date_format:Y-m-d|after:yesterday',
                'no_of_position' => 'required|min:1|max:200|numeric',
                'office_location' => 'required|integer',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $new_requisition = new Requisition();
                $new_requisition->requisition_department = $login_user[0]['employee_department'];
                $new_requisition->requisition_job_title = $request->post('job_title');
                $new_requisition->requisition_job_type = $request->post('job_type');
                $new_requisition->requisition_expected_salary = $request->post('req_salary');
                $new_requisition->requisition_shift_time = $request->post('shift_time');
                $new_requisition->requisition_shift_start_time = $request->post('shift_start');
                $new_requisition->requisition_shift_end_time = $request->post('shift_end');
                $new_requisition->requisition_date_needed = $request->post('requisition_date');
                $new_requisition->requisition_additional_info = $request->post('req_additional');
                $new_requisition->no_of_position = $request->post('no_of_position');
                $new_requisition->office_location = $request->post('office_location');
                $new_requisition->requisition_added_by = $login_user[0]['employee_id'];
                $new_requisition->create_at = date('Y-m-d H:i:s');

                $process = $new_requisition->save();

                if($process == 1){

                    $requisition_status = new RequisitionStatus();
                    $requisition_status->requisition_status = 'Pending';
                    $requisition_status->main_requisition_id =  $new_requisition->requisition_id ;
                    $requisition_status->requisition_status_created_by = $login_user[0]['employee_id'];
                    $requisition_status->requisition_status_created_at =  date('d-m-Y H:i:s');

                    $requisition_status->save();

                    $this->send_notification_api('New Requisition Request','Dear HR, '.$login_user[0]['employee_name'].', added a new requisition request for their department. Please click here to view.','requisition?requisition-id='.$new_requisition->requisition_id,null,null,null,'yes', $login_user[0]['employee_id']);

                    return $this->api_response_json(TRUE,"Requisition Added Successfully.","",200);

                }else{

                    return $this->api_response_json(FALSE,"Requisition Addition Failed. Please try again after some time.","",200);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Add Requisition API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Add Requisition API',  $request->header('Bearer-Token'));

        }

    }

    protected function update_requisition(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                'job_title' => 'required',
                'job_type' => 'required|in:Intern,Probation,Permanent,PartTime',
                'shift_time' => 'required|in:Morning Shift,Afternoon Shift,Evening Shift,Night Shift,Custom time',
                'shift_start' => ($request->post('shift_time') == 'Custom time' ) ? 'required|date_format:H:i' : '' ,
                'shift_end' => ($request->post('shift_time') == 'Custom time' ) ? 'required|date_format:H:i' : '' ,
                'requisition_date' => 'required|date_format:Y-m-d|after:yesterday',
                'no_of_position' => 'required|min:1|max:200|numeric',
                'office_location' => 'required|integer',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);

                }
            }else {

                $check_if_interview_exists = Interview::where('requisition_reference','=', $request->post('requisition_id') )->get()->toArray();
                if(empty($check_if_interview_exists)){

                    $update_requisition =  Requisition::find($request->post('requisition_id'));
                    if(!empty($update_requisition)){

                        if($update_requisition->requisition_added_by == $login_user[0]['employee_id']) {

                            $update_requisition->requisition_job_title = $request->post('job_title');
                            $update_requisition->requisition_job_type = $request->post('job_type');
                            $update_requisition->requisition_expected_salary = $request->post('requisition_salary');
                            $update_requisition->requisition_shift_time = $request->post('shift_time');
                            $update_requisition->requisition_shift_start_time = $request->post('shift_start');
                            $update_requisition->requisition_shift_end_time = $request->post('shift_end');
                            $update_requisition->requisition_date_needed = $request->post('requisition_date');
                            $update_requisition->requisition_additional_info = $request->post('requisition_additional');
                            $update_requisition->no_of_position = $request->post('no_of_position');
                            $update_requisition->office_location = $request->post('office_location');
                            $update_requisition->requisition_added_by = $login_user[0]['employee_id'];
                            $update_requisition->create_at = date('Y-m-d H:i:s');

                            $process = $update_requisition->save();

                            if($process == 1){
                                $this->send_notification_api('Update Requisition Request','Dear HR, '.$login_user[0]['employee_name'].', update requisition request for their department. Please click here to view.','requisition?requisition-id='.$update_requisition->requisition_id,null,null,null,'yes', $login_user[0]['employee_id']);

                                return $this->api_response_json(TRUE,"Requisition Updated Successfully.","",200);

                            }else{

                                return $this->api_response_json(FALSE,"Requisition updated Failed. Please try again after some time.","",200);
                            }
                        }else{

                            return $this->api_response_json(FALSE,"Requisition request id is invalid.","",200);
                        }
                    }else{

                        return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
                    }
                }else{

                    return $this->api_response_json(FALSE,"You can't update this requisition, Interview(s) are already scheduled on it..","",200);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Updated Requisition API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Updated Requisition API',  $request->header('Bearer-Token'));

        }

    }

    protected function delete_requisition_request(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $get_requisition_request = Requisition::find($request->post('requisition_id'));
            if(!empty($get_requisition_request)){

                if($get_requisition_request->requisition_added_by == $login_user[0]['employee_id']) {

                    $check_if_interview_exists = Interview::where('requisition_reference','=', $request->post('requisition_id'))->get()->toArray();
                    if(empty($check_if_interview_exists)){

                        $process = Requisition::where('requisition_id', $request->post('requisition_id'))->delete();

                        if( $process == 1){

                            return $this->api_response_json(TRUE,"Requisition request has been deleted.","",200);

                        }else{

                            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                        }
                    }else{

                        return $this->api_response_json(FALSE,"You can't delete this requisition, Interview(s) are already scheduled on it.","",200);
                    }
                }else{

                    return $this->api_response_json(FALSE,"Requisition request id is invalid.","",200);
                }
            }else{

                return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Delete Requisition Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('Delete Requisition Request API',  $request->header('Bearer-Token'));
        }
    }


}
