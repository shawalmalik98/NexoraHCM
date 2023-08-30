<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Requisition, RequisitionStatus, Employees, Department, Interview, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, MailTrait};
use App\Helpers\ActivityTracker;

class RequisitionController extends Controller
{
    use LoginEmployee, LogGenerateTrait, NotificationTrait, MailTrait;
    const MAIN_TITLE = "all-requisition";
    const TITLE_GLOBAL = "my-requisition";

    protected function index(Request $request){
        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1';
            $where_clause_to = '1=1';
            if(!empty($request->employee_location) || !empty($request->requisition_department) || !empty($request->emp_status) || !empty($request->emp_name) || !empty($request->requisition_job_type) || !empty($request->requisition_status)){

                if(!empty($request->employee_location)){
                    $where_clause_to   .= ' AND employee_location = "'.$request->employee_location.'"'; 
                }
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

            $departments = Department::get()->toArray();

            if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1){

                $employees_data = Employees::where('employee_isactive','=', $status)->whereRaw($where_clause_to)->get(['employee_id', 'employee_code','employee_name','employee_location'])->toArray();

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
            elseif($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 1 && $login_user[0]['employee_can_ra'] == 1){

                $login_employee_id = $login_user[0]['employee_id'];

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id, $status);
                
                $employees_data = Employees::where('employee_isactive','=', $status)->whereIn('employee_id', $total_team_member)->whereRaw($where_clause_to)->get(['employee_id', 'employee_code','employee_name','employee_location'])->toArray();

                $total_team_member = array_column($employees_data, 'employee_id');
                
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

            $send_data = compact('title','login_user','requisitions','departments','employees_data','interviews','interview_count','requistion_status_levels','employee_locations');

            return view('requisition.all-requisition', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View All Requisition', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View All Requisition');
           
        }
    }

    protected function requisition_detail($requisition_id){

        try{
            $title = SELF::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

            $requistion_check = [''];

            if($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 1){

                $login_employee_id = $login_user[0]['employee_id'];

                $status = 1;

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id, $status);

                $requistion_check = Requisition::whereIn('requisition_added_by',$total_team_member)->where('requisition_id',$requisition_id)->get(['requisition_id'])->toArray();

            }

            $department_name = Department::where('department_id',$login_user[0]['employee_department'])->get('department_name')->toArray();

            $employees = Employees::where('employee_isactive', 1)->get()->toArray();

            $requisitions = Requisition::join('tbl_departments','tbl_departments.department_id','=','tbl_requisition.requisition_department')
            ->join('tbl_requisition_status','tbl_requisition_status.main_requisition_id','tbl_requisition.requisition_id')
            ->join('tbl_office_locations','tbl_office_locations.location_id','tbl_requisition.office_location')
            ->where('main_requisition_id',$requisition_id)
            ->whereIn('requisition_status_id',function($query){
                $query->from('tbl_requisition_status')
                ->selectRaw('Max(requisition_status_id)')
                ->groupBy('main_requisition_id');
            })
            ->orderBy("requisition_id","DESC" )
            ->get(['requisition_id','department_name','requisition_job_title','requisition_job_type','requisition_expected_salary','requisition_shift_time','requisition_shift_start_time','requisition_shift_end_time','requisition_date_needed','requisition_additional_info','requisition_added_by','create_at','requisition_status','no_of_position','location_title'])->toArray();

            $requistion_status_level = ['Interview Scheduled','Hold for more interviews','Candidate Shortlisted','Candidate Rejected','Offer Placed','Offer Rejected',"Candidate didn't Appeared",'Candidate Hired','Interview Cancelled'];

            $requisitions_status = RequisitionStatus::join('tbl_requisition', 'tbl_requisition.requisition_id', '=', 'tbl_requisition_status.main_requisition_id')->where('main_requisition_id',$requisition_id)->get()->toArray();

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
            ->where('requisition_reference', '=', $requisition_id )
            ->orderBy("interview_id","DESC" )->get(['interview_id','candidate_name','interview_interviewer1','interview_interviewer2','interview_time','interview_date','interview_status_level'])->toArray();

            $send_data = compact('title', 'login_user', 'requisitions','employees','department_name','interviews','requisitions_status','requistion_status_level');

            if(empty($requistion_check)){

                return abort(404);

            }

            return view('requisition.requisition-detail', $send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View Personal Requisition Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Personal Requisition Detail');

            
        }
    }


    protected function my_requisition(Request $request){
        try{
            $title = Self::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

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
            ->groupBy('requisition_id','department_name','requisition_job_title','requisition_job_type','requisition_expected_salary','requisition_shift_time','requisition_shift_start_time','requisition_shift_end_time','requisition_date_needed','requisition_additional_info','requisition_added_by','create_at','requisition_status','requisition_status','interview_status_level','no_of_position','office_location')
            ->orderBy("requisition_id","DESC" )
            ->get(['requisition_id','department_name','requisition_job_title','requisition_job_type','requisition_expected_salary','requisition_shift_time','requisition_shift_start_time','requisition_shift_end_time','requisition_date_needed','requisition_additional_info','requisition_added_by','create_at','requisition_status','requisition_status','interview_status_level','no_of_position','office_location'])->toArray();

            $interviews = Interview::selectRaw('count(interview_id) as total, requisition_reference')
            ->groupBy('requisition_reference')->get()->toArray();

            $interviews_total = array_column($interviews, 'total');
            $interview_reference = array_column($interviews, 'requisition_reference');

            $interview_count = array_combine($interview_reference,$interviews_total);

            $department_name = Department::where('department_id',$login_user[0]['employee_department'])->get('department_name')->toArray();

            $get_all_departments = Department::get(['department_id','department_name'])->toArray();

            $office_location = OfficeLocation::get()->toArray();

            $send_data = compact('title','login_user','requisitions','department_name','interviews','interview_count','interview_reference','get_all_departments','office_location');

            return view('requisition.my-requisition', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View My Requisition', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View my Requisition');
         
        }
    }

    protected function my_requisition_detail($requisition_id){

        try{
            $title = SELF::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

            $requistion_check = Requisition::where('requisition_added_by', $login_user[0]['employee_id'])->where('requisition_id',$requisition_id)->get(['requisition_id'])->toArray();

            $department_name = Department::where('department_id',$login_user[0]['employee_department'])->get('department_name')->toArray();

            $requisitions = Requisition::join('tbl_departments','tbl_departments.department_id','=','tbl_requisition.requisition_department')
            ->join('tbl_requisition_status','tbl_requisition_status.main_requisition_id','tbl_requisition.requisition_id')
            ->join('tbl_office_locations','tbl_office_locations.location_id','tbl_requisition.office_location')
            ->where('main_requisition_id',$requisition_id)
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
            ->where('requisition_reference', '=', $requisition_id )
            ->orderBy("interview_id","DESC" )->get(['interview_id','candidate_name','interview_interviewer1','interview_interviewer2','interview_time','interview_date','interview_status_level'])->toArray();

            $send_data = compact('title', 'login_user', 'requisitions','department_name','interviews');

            if(empty($requistion_check)){

                return abort(404);

            }
            return view('requisition.my-requisition-detail', $send_data);

        }
        catch(\Exception $e){
            $this->create_error_log('View Personal Requisition Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Personal Requisition Detail');

            
        }
    }

    protected function add_requisition(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'requisition_department' => 'required',
                'job_title' => 'required',
                'job_type' => 'required',
                'shift_time' => 'required',
                'shift_start' => ($request->input('shift_time') == 'Custom time' ) ? 'required|date_format:H:i' : '' ,
                'shift_end' => ($request->input('shift_time') == 'Custom time' ) ? 'required|date_format:H:i' : '' ,
                'requisition_date' => 'required|date_format:Y-m-d|after:yesterday',
                'no_of_position' => 'required|min:1|max:200|numeric',
                'office_location' => 'required',
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

                $new_requisition = new Requisition();
                $new_requisition->requisition_department = $request->input('requisition_department');
                $new_requisition->requisition_job_title = $request->input('job_title');
                $new_requisition->requisition_job_type = $request->input('job_type');
                $new_requisition->requisition_expected_salary = $request->input('req_salary');
                $new_requisition->requisition_shift_time = $request->input('shift_time');
                $new_requisition->requisition_shift_start_time = $request->input('shift_start');
                $new_requisition->requisition_shift_end_time = $request->input('shift_end');
                $new_requisition->requisition_date_needed = $request->input('requisition_date');
                $new_requisition->requisition_additional_info = $request->input('req_additional');
                $new_requisition->no_of_position = $request->input('no_of_position');
                $new_requisition->office_location = $request->input('office_location');
                $new_requisition->requisition_added_by = session('user_session');
                $new_requisition->create_at = date('Y-m-d H:i:s');

                $process = $new_requisition->save();

                if($process == 1){

                    $requisition_status = new RequisitionStatus();
                    $requisition_status->requisition_status = 'Pending';
                    $requisition_status->main_requisition_id =  $new_requisition->requisition_id ;
                    $requisition_status->requisition_status_created_by = session('user_session');
                    $requisition_status->requisition_status_created_at =  date('d-m-Y H:i:s');

                    $requisition_status->save();

                    $this->send_notification('New Requisition Request','Dear HR, '.$login_user[0]['employee_name'].', added a new requisition request for their department. Please click here to view.','requisition?requisition-id='.$new_requisition->requisition_id,null,null,null,'yes');
                    
                    $this->send_module_email(3,'New Requisition Request','<b>'.$login_user[0]['employee_name']. '</b> added a new requisition request for their department.', ''.url('/').'/requisition?requisition-id='.$new_requisition->requisition_id);

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Requisition added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Requisition addition failed. Please try again after some time.",
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log("Add Requisition", $e);
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
            ActivityTracker::track_activity('Add Requisition');
        }

    }

    protected function update_requisition(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'job_title' => 'required',
                'job_type' => 'required',
                'shift_time' => 'required',
                'shift_start' => ($request->input('shift_time') == 'Custom time' ) ? 'required|date_format:H:i' : '' ,
                'shift_end' => ($request->input('shift_time') == 'Custom time' ) ? 'required|date_format:H:i' : '' ,
                'requisition_date' => 'required|date_format:Y-m-d|after:yesterday',
                'no_of_position' => 'required|min:1|max:200|numeric',
                'office_location' => 'required',
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

                $check_if_interview_exists = Interview::where('requisition_reference','=', $request->input('requisition_id') )->get(['interview_id'])->toArray();
                if(empty($check_if_interview_exists)){

                    $update_requisition = Requisition::find($request->input('requisition_id'));
                    if(!empty($update_requisition)){

                        if($update_requisition->requisition_added_by == session('user_session')) {

                            $update_requisition->requisition_job_title = $request->input('job_title');
                            $update_requisition->requisition_job_type = $request->input('job_type');
                            $update_requisition->requisition_expected_salary = $request->input('requisition_salary');
                            $update_requisition->requisition_shift_time = $request->input('shift_time');
                            $update_requisition->requisition_shift_start_time = $request->input('shift_start');
                            $update_requisition->requisition_shift_end_time = $request->input('shift_end');
                            $update_requisition->requisition_date_needed = $request->input('requisition_date');
                            $update_requisition->requisition_additional_info = $request->input('requisition_additional');
                            $update_requisition->no_of_position = $request->input('no_of_position');
                            $update_requisition->office_location = $request->input('office_location');
                            $update_requisition->requisition_added_by = session('user_session');
                            $update_requisition->create_at = date('Y-m-d H:i:s');

                            $process = $update_requisition->save();

                            if($process == 1){
                                $this->send_notification('Update Requisition Request','Dear HR, '.$login_user[0]['employee_name'].' update requisition request for their department. Please click here to view.','requisition?requisition-id='.$update_requisition->requisition_id,null,null,null,'yes');

                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Requisition updated successfully.",
                                ]);
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Requisition updated failed. Please try again after some time.",
                                ]);
                            }
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'Requisition request id is invalid.',
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Request not found on this id.',
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t update this requisition, Interview(s) are already scheduled on it..',
                        "autoreload" => "TRUE",
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log("Updated Requisition", $e);
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
            ActivityTracker::track_activity('Updated Requisition');
        }

    }

    protected function delete_requisition_request(Request $request_data){

        try{

            $get_requisition_request = Requisition::find($request_data->requisition_id);
            if(!empty($get_requisition_request)){

                if($get_requisition_request->requisition_added_by == session('user_session')) {

                    $check_if_interview_exists = Interview::where('requisition_reference','=', $request_data->input('requisition_id'))->get(['interview_id'])->toArray();
                    if(empty($check_if_interview_exists)){

                        $process = Requisition::where('requisition_id', $request_data->requisition_id)->delete();

                        if( $process == 1){

                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => 'Requisition request has been deleted',
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
                            'msg' => 'You can\'t delete this requisition, Interview(s) are already scheduled on it.',
                            "autoreload" => "TRUE",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Requisition request id is invalid.',
                    ]);
                }

            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Request not found on this id.',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Delete Requisition Request', $e);
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
            ActivityTracker::track_activity('Delete Requisition Request');

        }
    }

    protected function requisition_status(Request $request_data){

        try{

            $validator = Validator::make($request_data->all(), [
                'requisition_status' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $check_requisition_status = Requisition::where('requisition_id', $request_data->input('requisition_id'))->get(['requisition_id'])->toarray();

                if(empty($check_requisition_status)){

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This Requisition has been deleted.',
                    ]);

                }

                $requisition_status = new RequisitionStatus();
                $requisition_status->requisition_status = $request_data->input('requisition_status');
                $requisition_status->main_requisition_id = $request_data->input('requisition_id');
                $requisition_status->requisition_status_created_by = $request_data->input('employee_id');
                $requisition_status->requisition_status_created_at =  date('d-m-Y H:i:s');

                $process = $requisition_status->save();

                if($process == 1){

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Requisition status successfully updated.",
                    ]);


                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Requisition status not successfully update try again.",
                    ]);
                }

            }
        }
        catch(\Exception $e){
            $this->create_error_log('Requisition Status', $e);
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
            ActivityTracker::track_activity('Requisition Status');
        }
    }
}
