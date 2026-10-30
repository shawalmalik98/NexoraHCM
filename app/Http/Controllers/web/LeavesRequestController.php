<?php

namespace App\Http\Controllers\web;

use App\Models\LeaveDeduction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use App\Models\{Notification, Employees, LeaveRequest, Attendance, Leave, RequestApproval, OfficeLocation,LeaveManagement};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee};
use App\Helpers\{ActivityTracker, CustomHelper};

class LeavesRequestController extends Controller
{
    use LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee;

    const TITLE_GLOBAL = "employees-leaves";
    const TITLE_GLOBAL2 = "my-leaves"; 

    protected function index(Request $request_data){
    
        try{
            $title = Self::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            if($request_data->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }
            
            $employees_data = [];
            
            $my_leaves = array();
            $responsed_request = array();

            $limit_query = '500';
            
            if(!empty($request_data->leave)){
                $limit_query = '*';
            }

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null)){
                
                $where_clause = '1=1 AND employee_isactive = '.$status.'';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->request_for_date)  || !empty($request_data->request_submited_date) || !empty($request_data->req_status)){
                    
                    if(!empty($request_data->employee_location)){
                        $where_clause .= " AND employee_location = ".\DB::getPdo()->quote((string) $request_data->employee_location)."";
                        $where_clause_to   .= " AND employee_location = ".\DB::getPdo()->quote((string) $request_data->employee_location).""; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= " AND leave_applied_by = ".\DB::getPdo()->quote((string) $request_data->emp_name)."";
                    }
                    if(!empty($request_data->request_for_date)){
                        $where_clause .= " AND leave_applied_for_date = ".\DB::getPdo()->quote((string) $request_data->request_for_date)." ";
                    }
                    if(!empty($request_data->request_submited_date)){
                        $submition_date = date("d-m-Y", strtotime($request_data->request_submited_date));
                        $where_clause .= " AND leave_applied_date LIKE '%".$submition_date."%'";
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= " AND leave_approved_by_hr = ".\DB::getPdo()->quote((string) $request_data->req_status)."";
                    }
                }

                $employees_data = []; // the name filter searches employees through /employee-search

                $search = trim((string) request()->input('search'));
                $pagination = LeaveRequest::select([\DB::raw('STRAIGHT_JOIN leave_applied_id'),'leave_applied_by','leave_applied_date','leave_applied_for_date','leave_applied_reason','leave_applied_type','leave_applied_attachment','leave_approved_by_hr','leave_approved_date_hr','employee_id','leave_name','employee_code','employee_name','department_name','designation_name'])
                ->join('tbl_employees','tbl_leaves_applied.leave_applied_by','=','tbl_employees.employee_id')->join('tbl_leave_management', 'tbl_leaves_applied.leave_applied_type','=', 'tbl_leave_management.leave_manage_id')
                ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
                ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->whereRaw($where_clause)
                ->orderBy('leave_applied_id','desc')
                ->when($search !== '', function($query) use($search){
                        $query->where(function($query) use($search){
                            $query->where('employee_name','like','%'.$search.'%')
                                ->orWhere('employee_code','like','%'.$search.'%');
                        });
                    })
                    ->paginate(CustomHelper::per_page(), ['*'])->withQueryString();
                $my_leaves = $pagination->getCollection()->toArray();

                $responsed_request = RequestApproval::where('request_module_id',2)->pluck('request_id')->toArray();

            }
          
            elseif(!CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) && !CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) && !CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) && $login_user[0]['employee_is_manager'] == 1){

                $login_employee_id = $login_user[0]['employee_id'];

                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id,  $status);

                $where_clause = '1=1 AND employee_isactive = '.$status.'';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->req_status)){
                    
                    if(!empty($request_data->employee_location)){
                        $where_clause .= " AND employee_location = ".\DB::getPdo()->quote((string) $request_data->employee_location)."";
                        $where_clause_to   .= " AND employee_location = ".\DB::getPdo()->quote((string) $request_data->employee_location)."";
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= " AND leave_applied_by = ".\DB::getPdo()->quote((string) $request_data->emp_name)."";
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= " AND leave_approved_by_hr = ".\DB::getPdo()->quote((string) $request_data->req_status)."";
                    }
                }
                
                $employees_data = []; // the name filter searches employees through /employee-search

                $search = trim((string) request()->input('search'));
                $pagination = LeaveRequest::join('tbl_employees', 'tbl_leaves_applied.leave_applied_by', '=', 'tbl_employees.employee_id')
                ->join('tbl_leave_management', 'tbl_leaves_applied.leave_applied_type','=', 'tbl_leave_management.leave_manage_id')
                ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
                ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->whereIn('leave_applied_by',$total_team_member)->whereRaw($where_clause)->orderBy('leave_applied_id','DESC')->when($search !== '', function($query) use($search){
                        $query->where(function($query) use($search){
                            $query->where('employee_name','like','%'.$search.'%')
                                ->orWhere('employee_code','like','%'.$search.'%');
                        });
                    })
                    ->paginate(CustomHelper::per_page(), [\DB::raw('STRAIGHT_JOIN leave_applied_id'),'leave_applied_by','leave_applied_date','leave_applied_for_date','leave_applied_reason','leave_applied_type','leave_applied_attachment','leave_approved_by_hr','leave_approved_date_hr','employee_id','leave_name','employee_code','employee_name','department_name','designation_name'])->withQueryString();
                $my_leaves = $pagination->getCollection()->toArray();

                $responsed_request = RequestApproval::where('request_module_id',2)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();

                if(empty($my_leaves)){
                    $my_leaves = array();
                }


            }

            if(!empty($request_data->from_date) && !empty($request_data->to_date)){
                $no_of_days = $this->count_days($request_data->from_date,$request_data->to_date);

                for($i = 0; $i<$no_of_days; $i++){
                 
                    $dates[] =  date('Y-m-d', strtotime('+'.$i.' day',  strtotime($request_data->from_date)));
                }
            }else{
                $dates[] = $request_data->date;
            }


            $pagination = $pagination ?? null;
            $send_data = compact('title','login_user','employees_data','my_leaves','employee_locations','responsed_request','dates','pagination');
            
            return view('leave.all-employees-request', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('Access All Employees Leaves Request Module', $e);
            $send_data = compact('title','login_user');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('Access All Employees Leaves Request Module');
           
        }

    }

    protected function my_leaves(){
        try{
            $title = Self::TITLE_GLOBAL2;
        
            $login_user = $this->get_login_user_detail();

            $my_leaves = LeaveRequest::join('tbl_employees', 'tbl_leaves_applied.leave_applied_by', '=', 'tbl_employees.employee_id')->join('tbl_leave_management', 'tbl_leaves_applied.leave_applied_type','=', 'tbl_leave_management.leave_manage_id')->where('leave_applied_by', (string) session('user_session'))->orderBy('leave_applied_id','DESC')->get(['leave_applied_id','leave_applied_for_date','leave_applied_type','leave_applied_reason','leave_applied_date','leave_approved_by_hr','leave_approved_date_hr','leave_applied_attachment','leave_manage_id','leave_name'])->toArray();

            $leave_request_id = array_column($my_leaves,'leave_applied_id');
            
            $responsed_request = RequestApproval::where('request_module_id',2)->whereIn('request_id',$leave_request_id)->pluck('request_id')->toArray();
      
            $manage_leaves = LeaveManagement::get(['leave_manage_id','leave_name'])->toArray();
           
            $send_data = compact('title','login_user','my_leaves','responsed_request','manage_leaves');

            return view('leave.my-request', $send_data);
       }
       catch(\Exception $e){
            $this->create_error_log('View All My Leave Request', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Personal Leave Requests');
           
        }
       
    }

    protected function applyfor_personalleave(Request $leave_data){
        try{

            $filename = '';

            if($leave_data->hasFile('leave_applied_attachment')){
                $filename = $leave_data->file('leave_applied_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($leave_data->all(), [
            
                'days_assign' => 'required',
                'from_date' => ($leave_data->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')). '': '' ,
                'to_date' => ($leave_data->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|after:from_date' : '' ,
                'leave_date' => ($leave_data->input('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')). '': '' ,
                'leave_type' => 'required',
                'leave_reason' => 'required',
                'leave_applied_attachment' => ['max:10000', ($filename == 'txt') ? 
                File::types(['jpg','png','jpeg','docx','pdf','txt'])
                :
                File::types(['jpg','png','jpeg','docx','pdf'])],

            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
             
                $leave_date;
                $reamining_leaves;

                $login_user = $this->get_login_user_detail();
               
                $leave_name = LeaveManagement::where('leave_manage_id',$leave_data->input('leave_type'))->get(['leave_manage_id','leave_name','leave_visibilty'])->toArray();

                if(!empty($leave_name)){
                    
                    $leave_alloted = Leave::where('leaves_employee_id',session('user_session'))->where('leave_type_id',$leave_data->input('leave_type'))->get(['leave_allotement'])->toArray();

                    if($leave_name[0]['leave_visibilty'] == 1){
                    
                        if(empty($leave_alloted)){
                            $leave_alloted[0]['leave_allotement'] = 0;
                        }
        
                        $no_of_days = 1;
                        if(!empty($leave_data->input('from_date')) && !empty($leave_data->input('to_date'))){
                            $no_of_days = $this->count_days($leave_data->input('from_date'),$leave_data->input('to_date'));
                        }
                        
                        $leave_request_applied = LeaveRequest::where('leave_applied_by',(string) session('user_session'))
                            ->where('leave_applied_type', $leave_data->input('leave_type'))
                            ->where('leave_approved_by_hr',1)
                            ->count();
                            
                    
                        $get_availed_leaves = Attendance::where('att_employee_id',session('user_session'))->where('att_check_overall_status', $leave_name[0]['leave_name'])->count() + LeaveDeduction::deducted_count(session('user_session'), $leave_name[0]['leave_manage_id']);
                
                        $total_leaves = $get_availed_leaves + $leave_request_applied;
                    
                        $reamining_leaves = $leave_alloted[0]['leave_allotement'] - $total_leaves;
                    
                        if(!empty($leave_alloted) && $reamining_leaves < $no_of_days){
                            if($reamining_leaves < 0){
                                $reamining_leaves = 0;
                            }
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'You have only ' . $reamining_leaves . ' ' .$leave_name[0]['leave_name']. '(s) reamining to apply',
                            ]);
        
                        }
                        
                        if(!empty($leave_data->input('from_date')) && !empty($leave_data->input('to_date'))){
                            $leave_date = $leave_data->input('from_date').'  '.$leave_data->input('to_date');
        
                            if($leave_data->input('from_date') < $login_user[0]['employee_doj']){
                                return response()->json([
                                    'msg' => 'You can\'t apply leave before your joining date.',
                                    'status' => 'FALSE'
                                ]);
                            }
        
                            $no_of_days =  $this->count_days($leave_data->input('from_date'),$leave_data->input('to_date'));
                        
                            for($i = 0; $i<$no_of_days; $i++){
                            
                                $request_leave_date = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($leave_data->input('from_date'))));
        
                                $checking_applied_leave_request = LeaveRequest::where('leave_applied_by', (string) session('user_session'))->whereIn('leave_applied_for_date',[$request_leave_date])->get(['leave_applied_for_date'])->toArray();

                                // if($leave_data->leave_type == "3"){
                                //     $employement_status_check = $this->check_if_employee_permanent($request_leave_date,session('user_session'),$custom_message = NULL);
                                //     if(!$employement_status_check['status']){
                                //         return response()->json([
                                //             'msg' => $employement_status_check['msg'],
                                //             'status' => 'FALSE'
                                //         ]);
                                //     }
                                // }

                                // $payroll_exists = $this->check_if_payroll_exist($request_leave_date,session('user_session'),$custom_message = NULL);
        
                                // if($payroll_exists['status'] == TRUE){
                                //     return response()->json([
                                //         'msg' => $payroll_exists['msg'],
                                //         'status' => 'FALSE'
                                //     ]);
                                // }

                                $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_leave_date);
                               
                                if($check_attendance_sheet_availability['status'] == FALSE){
                                    return response()->json([
                                        'msg' => 'Leave sheet has been locked for the date of ' . date('d M, Y', strtotime($request_leave_date)) . '. You can\'t add leave / apply for any kind of request for this period.',
                                        'status' => 'FALSE'
                                    ]);
                                }

                                foreach($checking_applied_leave_request as $request_date){
                                
                                    $leave_request_apply_date[] =  $request_date['leave_applied_for_date'];
                                }
                                
                            }
                        
                            if(!empty($leave_request_apply_date)){
        
                                $leave_date = implode(' , ',$leave_request_apply_date);
        
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => 'You have already submitted a leave request for '.$leave_date.'.',
                                ]);
                            }
                        }
                        else{
        
                            if($leave_data->input('leave_date') < $login_user[0]['employee_doj']){
                                return response()->json([
                                    'msg' => 'You can\'t apply leave before your joining date.',
                                    'status' => 'FALSE'
                                ]);
                            }

                            // if($leave_data->leave_type == "3"){
                            //     $employement_status_check = $this->check_if_employee_permanent($leave_data->input('leave_date'),session('user_session'),$custom_message = NULL);
                            //     if(!$employement_status_check['status']){
                            //         return response()->json([
                            //             'msg' => $employement_status_check['msg'],
                            //             'status' => 'FALSE'
                            //         ]);
                            //     }
                            // }

                            // $payroll_exists = $this->check_if_payroll_exist($leave_data->input('leave_date'),session('user_session'),$custom_message = NULL);

                            // if($payroll_exists['status'] == TRUE){
                            //     return response()->json([
                            //         'msg' => $payroll_exists['msg'],
                            //         'status' => 'FALSE'
                            //     ]);
                            // }

                            $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($leave_data->input('leave_date'));

                            if($check_attendance_sheet_availability['status'] == FALSE){
                                return response()->json([
                                    'msg' => 'Leave sheet has been locked for the date of ' . date('d M, Y', strtotime($leave_data->input('leave_date'))) . '. You can\'t add leave / apply for any kind of request for this period.',
                                    'status' => 'FALSE'
                                ]);
                            }
        
                            $leave_date = $leave_data->input('leave_date');
                            $checking_applied_leave_request = LeaveRequest::where('leave_applied_by', (string) session('user_session'))->where('leave_applied_for_date', $leave_data->input('leave_date'))->where('leave_approved_by_hr', '!=', 3)->get()->toArray();
                            
                        }
                    
                        if(empty($checking_applied_leave_request)){
                            $all_leave_request = LeaveRequest::orderBy('leave_applied_id','DESC')->limit(1)->get(['leave_applied_id'])->toArray();
                            if(empty($all_leave_request)){
                                $leave_applied_id = '1';
                            }else{
                                $leave_applied_id = ++$all_leave_request[0]['leave_applied_id'];
                            }
                            if(!empty($leave_data->input('from_date')) && !empty($leave_data->input('to_date'))){
                                
                                $no_of_days = $this->count_days($leave_data->input('from_date'),$leave_data->input('to_date'));
                                
                                for($i = 0; $i<$no_of_days; $i++){
                                    $apply_leave = new LeaveRequest();
                                    
                                    $dates = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($leave_data->input('from_date'))));
                                    
                                    $checking_request_dates = LeaveRequest::where('leave_applied_by', (string) session('user_session'))->where('leave_applied_for_date',$dates)->get(['leave_applied_for_date'])->toArray();
                                    
                                    if(empty($checking_request_dates)){
                                        
                                        $apply_leave->leave_applied_by = $leave_data->session()->get('user_session');
                                        $apply_leave->leave_applied_date = date('d-m-Y H:i:s');
                                        $apply_leave->leave_applied_for_date = $dates;
                                        $apply_leave->leave_applied_reason = $leave_data->input('leave_reason');
                                        $apply_leave->leave_applied_type = $leave_data->input('leave_type');
                                        $apply_leave->leave_approved_by_hr = 1;
        
                                        if($leave_data->hasFile('leave_applied_attachment')){
                        
                                            $fileNameToStore =  $leave_applied_id.$leave_data->file('leave_applied_attachment')->getClientOriginalName();
                                            
                                            $leave_data->file('leave_applied_attachment')->storeAs('portal_documents/leave_attachment/',$fileNameToStore);
                                
                                            $apply_leave->leave_applied_attachment = $fileNameToStore;  
        
                                        }
                                        $process = $apply_leave->save();
                                    }
                                
                                }
                                
                            }
                            else{
                                $apply_leave = new LeaveRequest();
                                $apply_leave->leave_applied_by = $leave_data->session()->get('user_session');
                                $apply_leave->leave_applied_date = date('d-m-Y H:i:s');
                                $apply_leave->leave_applied_for_date = $leave_data->input('leave_date');
                                $apply_leave->leave_applied_reason = $leave_data->input('leave_reason');
                                $apply_leave->leave_applied_type = $leave_data->input('leave_type');
                                $apply_leave->leave_approved_by_hr = 1;
        
                                if($leave_data->hasFile('leave_applied_attachment')){
                
                                    $fileNameToStore =  $leave_applied_id.$leave_data->file('leave_applied_attachment')->getClientOriginalName();
                                    
                                    $leave_data->file('leave_applied_attachment')->storeAs('portal_documents/leave_attachment/',$fileNameToStore);
                        
                                    $apply_leave->leave_applied_attachment = $fileNameToStore;
                        
                                }
        
                                $process = $apply_leave->save();
                            
                            }
                            
                            if($process == 1){
                
        
                                // if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                //     $to_mngr1 = $login_user[0]['employee_senior_manager'];
                                // }
                                // if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                //     $to_mngr2 = $login_user[0]['employee_junior_manager'];
                                // }

                                // if(!empty($leave_data->input('from_date')) && !empty($leave_data->input('to_date'))){

                                //     $this->send_notification('New Leave Request ', '' . $login_user[0]['employee_name']. ' applied for leave request(s) as ('. $leave_name[0]['leave_name'] .') from '. $leave_data->input('from_date').' to '.$leave_data->input('to_date').'. Please click here to view.','employees-leaves?employee_id='.session('user_session').'&from_date='.$leave_data->input('from_date').'&to_date='.$leave_data->input('to_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                                //     $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Leave Request','You\'ve successfully applied leave request(s) and has been forwarded to your reporting authorities.');

        
                                // }else{
                                //     $this->send_notification('New Leave Request ', '' . $login_user[0]['employee_name']. ' applied for leave request as ('. $leave_name[0]['leave_name'] .') for the date of '. $leave_data->input('leave_date').'. Please click here to view.','employees-leaves?employee_id='.session('user_session').'&date='.$leave_data->input('leave_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');

                                //     $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Leave Request','You\'ve successfully applied leave request and has been forwarded to your reporting authorities.');

                                // }
                
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Leave request has been submitted. Please wait for response",
                                ]);
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Error",
                                ]);
                            }
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'You have already submitted a leave request for this date '.$leave_date.'.',
                            ]);
                        }
                    }else{

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'You can\'t apply '.$leave_name[0]['leave_name'].' at the moment.'
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t apply this leave at the moment.',
                    ]);
                }
               
            }   

        }catch(\Exception $e){
            $this->create_error_log('Add New Leave Request', $e);
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
            ActivityTracker::track_activity('Create an Leave Request');
        }
    }


    protected function leave_request(Request $request){
        
       
        try{

            // only the module's role holders, or the requester's own managers, may respond
            $request_employees = LeaveRequest::whereIn('leave_applied_id', explode(',', (string) $request->input('ids')))->pluck('leave_applied_by')->toArray();
            if(!$this->can_respond_for($request_employees, ['hr'])){
                return $this->not_allowed_response();
            }

            if($request['status'] == "DisApprove"){

                $validator = Validator::make($request->all(), [
                    'disapprovermsg' => 'required',
                ]);
                  
                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return response()->json([
                            'msg' => $error,
                            'status' => 'FALSE'
                        ]);
                    }
                }else {

                    $process = '';
            
                    $leave_request_ids = explode(",",$request['ids']);
                
                    $login_user = $this->get_login_user_detail();
            
                    foreach($leave_request_ids as $leave_request_id){
                    
                        $update_leave = LeaveRequest::find($leave_request_id);

                        if(!empty($update_leave)){
                        
                            // $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$update_leave->leave_applied_by);
                            
                            // if($request_approve_permission != null){
                            //     return response()->json([
                            //         'msg' => $request_approve_permission['msg'],
                            //         'status' => 'FALSE'
                            //     ]);
                            // }

                            if($update_leave->leave_approved_by_hr == 1){

                                $update_leave->leave_approved_by_hr = 3;
                                $update_leave->leave_approved_date_hr = 'Dis-Approved By : '.$login_user[0]['employee_code'].' '.$login_user[0]['employee_name'].', at : '.date('h:i A d M, Y').'';
                                $process = $update_leave->save();

                                if($process == 1){

                                    $leave_approval = new RequestApproval();
                                    $leave_approval->request_module_id = 2;
                                    $leave_approval->request_id = $leave_request_id;
                                    $leave_approval->request_approver_id =  $login_user[0]['employee_id'];
                                    $leave_approval->request_approver_status = 3;
                                    $leave_approval->request_approver_msg = $request['disapprovermsg'];
                                    $leave_approval->request_approval_time = date('h:i A d M, Y');
                                    $leave_approval->save();
                        
                                    // $this->send_notification('Leave Request Disapproval ', ' HR disapproved your leave request(s) for '.$update_leave['leave_applied_for_date'].'. Please click here to view.','my-leaves?request-id='. $update_leave->leave_applied_id .'', $update_leave['leave_applied_by'],NULL,NULL,'no');

                                    // $this->send_push_notification_mobile_app($update_leave['leave_applied_by'], 'Leave Request','Your Leave request has been disapproved by HR.');
                                
                                }else{
                                    return response()->json([
                                        'status' => 'TRUE',
                                        'msg'   =>"Request(s) disapproved not successfully ",
                                        
                                    ]);
                                }
                            }
                        }    
                    
        
                    }
                        
                    if($process){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg'   =>"Request(s) disapproved successfully ",
                            
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg'   =>"Already responded to this request.",
                            
                        ]);
                    }
                }   
            }    

            if($request['status'] == "Approve"){

                $validator = Validator::make($request->all(), [
                    'approvermsg' => 'required',
                ]);
                  
                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return response()->json([
                            'msg' => $error,
                            'status' => 'FALSE'
                        ]);
                    }
                }else {
     
                    $process = '';
            
                    $leave_request_ids = explode(",",$request['ids']) ;
                    
                    $login_user = $this->get_login_user_detail();
        
                    $leave_applied_by = LeaveRequest::join('tbl_employees','tbl_leaves_applied.leave_applied_by','=','tbl_employees.employee_id')->join('tbl_leave_management', 'tbl_leaves_applied.leave_applied_type','=', 'tbl_leave_management.leave_manage_id')->whereIn('leave_applied_id', $leave_request_ids)->orderBy('leave_applied_id','DESC')->get(['leave_applied_id','leave_applied_by','leave_applied_type','employee_name','leave_manage_id','leave_name'])->toArray();

                    foreach($leave_applied_by as $single_leave){
                        
                        $leave_name = LeaveManagement::where('leave_manage_id',$single_leave['leave_manage_id'])->get('leave_name')->toArray();
                            
                        $leave_alloted = Leave::where('leaves_employee_id',$single_leave['leave_applied_by'])->where('leave_type_id',$single_leave['leave_manage_id'])->get(['leave_allotement'])->toArray();

                        $leave_request_applied = LeaveRequest::whereIn('leave_applied_id',$leave_request_ids)
                        ->where('leave_applied_by',(string) $single_leave['leave_applied_by'])
                        ->where('leave_applied_type',$single_leave['leave_manage_id'])
                        ->where('leave_approved_by_hr',1)
                        ->count();
                        
                        $get_availed_leaves =  Attendance::where('att_employee_id',$single_leave['leave_applied_by'])
                        ->where('att_check_overall_status', $single_leave['leave_name']) 
                        ->count() + LeaveDeduction::deducted_count($single_leave['leave_applied_by'], $single_leave['leave_manage_id']);

                        $reamining_leaves = $leave_alloted[0]['leave_allotement'] - $get_availed_leaves;
                        

                        if(!empty($leave_alloted) &&  $reamining_leaves < $leave_request_applied){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "There are only ". $reamining_leaves .' '.$single_leave['leave_name'] ."(s) remaining of ".$single_leave['employee_name'],
                            ]);
                        }

                    }
                    
                    foreach($leave_request_ids as $leave_request_id){
                    
                        $update_leave = LeaveRequest::find($leave_request_id);
                        
                        if(!empty($update_leave)){

                            $leave_name = LeaveManagement::where('leave_manage_id',$update_leave['leave_applied_type'])->get('leave_name')->toArray();
                            
                            // $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$update_leave->leave_applied_by);
                            
                            // if($request_approve_permission != null){
                            //     return response()->json([
                            //         'msg' => $request_approve_permission['msg'],
                            //         'status' => 'FALSE'
                            //     ]);
                            // }

                            // if($update_leave->leave_applied_type == '3'){
                            //     $employement_status_check = $this->check_if_employee_permanent( $update_leave->leave_applied_for_date,$update_leave->leave_applied_by,$custom_message = 'msg');
                            //     if(!$employement_status_check['status']){
                            //         return response()->json([
                            //             'msg' => $employement_status_check['msg'],
                            //             'status' => 'FALSE'
                            //         ]);
                            //     }
                            // }

                            // $payroll_exists = $this->check_if_payroll_exist($update_leave->leave_applied_for_date,$update_leave->leave_applied_by,$custom_message = 'True');

                            // if($payroll_exists['status'] == TRUE){
                            //     return response()->json([
                            //         'msg' => $payroll_exists['msg'],
                            //         'status' => 'FALSE'
                            //     ]);
                            // }

                            $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($update_leave->leave_applied_for_date);

                            if($check_attendance_sheet_availability['status'] == FALSE){
                                return response()->json([
                                    'msg' => 'Leave sheet has been locked for the date of ' . date('d M, Y', strtotime($update_leave->leave_applied_for_date)) . '. You can\'t approved leave request for this period.',
                                    'status' => 'FALSE'
                                ]);
                            }

                            if($update_leave->leave_approved_by_hr == 1){
                                
                                $update_leave->leave_approved_by_hr = 2;
                                $update_leave->leave_approved_date_hr = 'Approved By : '.$login_user[0]['employee_code'].' '.$login_user[0]['employee_name'].', at : '.date('h:i A d M, Y').'';
                                $process = $update_leave->save();
                
                                $get_attendance = Attendance::where('att_employee_id',$update_leave['leave_applied_by'])->where('att_for_date',date("d", strtotime($update_leave['leave_applied_for_date'])))->where('att_for_month', date("m", strtotime($update_leave['leave_applied_for_date'])))->where('att_for_year', date("Y", strtotime($update_leave['leave_applied_for_date'])))->get()->toArray();
                                
                                $employee_shift_start = $this->get_employee_today_shift($update_leave['leave_applied_by'],"in",$update_leave['leave_applied_for_date']);
                                $employee_shift_end = $this->get_employee_today_shift($update_leave['leave_applied_by'],"out",$update_leave['leave_applied_for_date']);
                                
                                if($process == 1){
                        
                                    if(empty($get_attendance)){
                                        
                                        $update_attendance = new Attendance();
                                        $update_attendance->att_employee_id = $update_leave['leave_applied_by'];
                                        $update_attendance->att_check_in_time = '00:00:00';
                                        $update_attendance->att_check_out_time = '00:00:00';
                                        $update_attendance->att_check_in_status = 0;
                                        $update_attendance->att_check_out_status = 0;
                                        $update_attendance->att_check_out_source = 1;
                                        $update_attendance->att_shift_start_time = $employee_shift_start;
                                        $update_attendance->att_shift_end_time = $employee_shift_end;
                                        $update_attendance->att_check_overall_status = $leave_name[0]['leave_name'];
                                        $update_attendance->att_for_date = date("d", strtotime($update_leave['leave_applied_for_date']));
                                        $update_attendance->att_for_month = date("m", strtotime($update_leave['leave_applied_for_date']));
                                        $update_attendance->att_for_year = date("Y", strtotime($update_leave['leave_applied_for_date']));
                                        $update_attendance->att_comp_date = date("Y-m-d", strtotime($update_leave['leave_applied_for_date']));
                                        $update_attendance->att_request_approval = 2;
                                        $process = $update_attendance->save();

                                    }else{
                                        
                                        $update_attendance = Attendance::find($get_attendance[0]['attendance_id']);
                                        $update_attendance->att_check_in_time = '00:00:00';
                                        $update_attendance->att_check_out_time = '00:00:00';
                                        $update_attendance->att_check_in_status = 0;
                                        $update_attendance->att_check_out_status = 0;
                                        $update_attendance->att_check_out_source = 1;
                                        $update_attendance->att_shift_start_time = $employee_shift_start;
                                        $update_attendance->att_shift_end_time = $employee_shift_end;
                                        $update_attendance->att_request_approval = 2;
                                        $update_attendance->att_check_overall_status = $leave_name[0]['leave_name'];
                                        $process = $update_attendance->save();
                                    }
                                    
                                    $leave_approval = new RequestApproval();
                                    $leave_approval->request_module_id = 2;
                                    $leave_approval->request_id = $leave_request_id;
                                    $leave_approval->request_approver_id =  $login_user[0]['employee_id'];
                                    $leave_approval->request_approver_status = 2;
                                    $leave_approval->request_approver_msg = $request['approvermsg'];
                                    $leave_approval->request_approval_time = date('h:i A d M, Y');
                                    $leave_approval->save();

                                    // $this->send_notification('Leave Request Approval ', ' HR approved your leave request(s) for '.$update_leave['leave_applied_for_date'].'. Please click here to view.','my-leaves?request-id='. $update_leave->leave_applied_id .'', $update_leave['leave_applied_by'],NULL,NULL,'no');
                                    
                                    // $this->send_push_notification_mobile_app($update_leave['leave_applied_by'], 'Leave Request','Your Leave request has been approved by HR.');

                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => "Request(s) not approved successfully",
                                    ]);
                                }
                            }
                        }
                        
                    }
                            
                    if($process){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Request(s) approved successfully",
                            
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Already responded to this request.",
                            
                        ]);
                    }
                }   
            }

            // if($request['status'] == "LeadApprove"){

            //     $process = '';
        
            //     $leave_request_ids = explode(",",$request['ids']) ;
            
            //     $login_user = $this->get_login_user_detail();
                
            //     $approval_maker = session('user_session');

            //     $status = 1;

            //     $leave_applied_by = LeaveRequest::join('tbl_employees','tbl_leaves_applied.leave_applied_by','=','tbl_employees.employee_id')->join('tbl_leave_management', 'tbl_leaves_applied.leave_applied_type','=', 'tbl_leave_management.leave_manage_id')->whereIn('leave_applied_id', $leave_request_ids)->orderBy('leave_applied_id','DESC')->get(['leave_applied_id','leave_applied_by','leave_applied_type','employee_name','leave_manage_id','leave_name'])->toArray();
            
            //     foreach($leave_applied_by as $single_leave){
                    
            //         $leave_name = LeaveManagement::where('leave_manage_id',$single_leave['leave_manage_id'])->get('leave_name')->toArray();
                    
            //         $leave_alloted = Leave::where('leaves_employee_id',$single_leave['leave_applied_by'])->where('leave_type_id',$single_leave['leave_manage_id'])->get(['leave_allotement'])->toArray();

            //         $leave_request_applied = LeaveRequest::whereIn('leave_applied_id',$leave_request_ids)
            //         ->where('leave_applied_by',(string) $single_leave['leave_applied_by'])
            //         ->where('leave_applied_type',$single_leave['leave_manage_id'])
            //         ->where('leave_approved_by_hr',1)
            //         ->count();
                
            //         $get_availed_leaves =  Attendance::where('att_employee_id',$single_leave['leave_applied_by'])
            //         ->where('att_check_overall_status', $single_leave['leave_name']) 
            //         ->count();

            //         $reamining_leaves = $leave_alloted[0]['leave_allotement'] - $get_availed_leaves;
                    
            //         if(!empty($leave_alloted) &&  $reamining_leaves < $leave_request_applied){
            //             return response()->json([
            //                 'status' => 'FALSE',
            //                 'msg' => "There are only ". $reamining_leaves .' '.$single_leave['leave_name'] ."(s) remaining of ".$single_leave['employee_name'],
            //             ]);
            //         }

            //     }
        
            //     foreach($leave_request_ids as $leave_request_id){
                    
            //         $update_leave = LeaveRequest::find($leave_request_id);

            //         $checking_request = RequestApproval::where('request_module_id',2)->where('request_approver_id',session('user_session'))->where('request_id',$leave_request_id)->get()->toArray();

            //         if(!empty($update_leave)){

            //             if($update_leave->leave_applied_type == '3'){
            //                 $employement_status_check = $this->check_if_employee_permanent( $update_leave->leave_applied_for_date,$update_leave->leave_applied_by,$custom_message = 'msg');
            //                 if(!$employement_status_check['status']){
            //                     return response()->json([
            //                         'msg' => $employement_status_check['msg'],
            //                         'status' => 'FALSE'
            //                     ]);
            //                 }
            //             }
            //             $payroll_exists = $this->check_if_payroll_exist($update_leave->leave_applied_for_date,$update_leave->leave_applied_by,$custom_message = 'True');

            //             if($payroll_exists['status'] == TRUE){
            //                 return response()->json([
            //                     'msg' => $payroll_exists['msg'],
            //                     'status' => 'FALSE'
            //                 ]);
            //             }

            //             $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($update_leave->leave_applied_for_date);

            //             if($check_attendance_sheet_availability['status'] == FALSE){
            //                 return response()->json([
            //                     'msg' => $check_attendance_sheet_availability['msg'],
            //                     'status' => 'FALSE'
            //                 ]);
            //             }
            //         }
                
            //         if(empty($checking_request)){
    
            //             if(!empty($update_leave) && $update_leave->leave_approved_by_hr == 1 && in_array($update_leave->leave_applied_by,$this->get_employee_hierarchy_unique($approval_maker,  $status))){
                             
            //                 $update_leave->leave_approved_by_hr = 2;
            //                 $update_leave->leave_approved_date_hr = 'Approved By : '.$login_user[0]['employee_name'].', at : '.date('h:i A d M, Y').'';
            //                 $process = $update_leave->save();
            
            //                 $get_attendance = Attendance::where('att_employee_id',$update_leave['leave_applied_by'])->where('att_for_date',date("d", strtotime($update_leave['leave_applied_for_date'])))->where('att_for_month', date("m", strtotime($update_leave['leave_applied_for_date'])))->where('att_for_year', date("Y", strtotime($update_leave['leave_applied_for_date'])))->get()->toArray();
                            
            //                 $employee_shift_start = $this->get_employee_today_shift($update_leave['leave_applied_by'],"in",$update_leave['leave_applied_for_date']);
            //                 $employee_shift_end = $this->get_employee_today_shift($update_leave['leave_applied_by'],"out",$update_leave['leave_applied_for_date']);
                            
            //                 if($process == 1){
                    
            //                     if(empty($get_attendance)){
                                    
            //                         $update_attendance = new Attendance();
            //                         $update_attendance->att_employee_id = $update_leave['leave_applied_by'];
            //                         $update_attendance->att_check_in_time = '00:00:00';
            //                         $update_attendance->att_check_out_time = '00:00:00';
            //                         $update_attendance->att_check_in_status = 0;
            //                         $update_attendance->att_check_out_status = 0;
            //                         $update_attendance->att_check_out_source = 1;
            //                         $update_attendance->att_shift_start_time = $employee_shift_start;
            //                         $update_attendance->att_shift_end_time = $employee_shift_end;
            //                         $update_attendance->att_check_overall_status = $leave_name[0]['leave_name'];
            //                         $update_attendance->att_for_date = date("d", strtotime($update_leave['leave_applied_for_date']));
            //                         $update_attendance->att_for_month = date("m", strtotime($update_leave['leave_applied_for_date']));
            //                         $update_attendance->att_for_year = date("Y", strtotime($update_leave['leave_applied_for_date']));
            //                         $update_attendance->att_comp_date = date("Y-m-d", strtotime($update_leave['leave_applied_for_date']));
            //                         $update_attendance->att_request_approval = 2;
            //                         $process = $update_attendance->save();

            //                     }else{
                                    
            //                         $update_attendance = Attendance::find($get_attendance[0]['attendance_id']);
            //                         $update_attendance->att_check_in_time = '00:00:00';
            //                         $update_attendance->att_check_out_time = '00:00:00';
            //                         $update_attendance->att_check_in_status = 0;
            //                         $update_attendance->att_check_out_status = 0;
            //                         $update_attendance->att_check_out_source = 1;
            //                         $update_attendance->att_shift_start_time = $employee_shift_start;
            //                         $update_attendance->att_shift_end_time = $employee_shift_end;
            //                         $update_attendance->att_request_approval = 2;
            //                         $update_attendance->att_check_overall_status = $leave_name[0]['leave_name'];
            //                         $process = $update_attendance->save();
            //                     }

            //                     $leave_approval = new RequestApproval();
            //                     $leave_approval->request_module_id = 2;
            //                     $leave_approval->request_id = $leave_request_id;
            //                     $leave_approval->request_approver_id =  $approval_maker;
            //                     $leave_approval->request_approver_status = 2;
            //                     $leave_approval->request_approver_msg = $request['approvermsg'];
            //                     $leave_approval->request_approval_time = date('h:i A d M, Y');
            //                     $leave_approval->save();
                            
            //                     $employee_name = Employees::where('employee_id', $update_leave['leave_applied_by'])->get(['employee_id','employee_code','employee_name'])->toArray();
                            
            //                     $notification_msg = 'Dear employee, ' .$login_user[0]['employee_name']. ' marked your leave request(s) as approved for the date of '.$update_leave['leave_applied_for_date']. ' Please click here to view.';
                            
            //                     $this->send_notification('Leave Request Approval ', $notification_msg , 'my-leaves?request-id='. $update_leave->leave_applied_id .'',$update_leave['leave_applied_by'],NULL,NULL,'no');

            //                     // $this->send_push_notification_mobile_app($update_leave['leave_applied_by'], 'Leave Request','Your Leave request been approved by your line manager.');
                            
            //                     $notification_msg_hr= ''.$employee_name[0]['employee_name'].''."'s".' Manager approved their leave request(s) for the date of '.$update_leave['leave_applied_for_date'].'. Please click here to view.';

            //                     $this->send_notification('Leave Request Approval ',  $notification_msg_hr , 'employees-leaves?request-id='. $update_leave->leave_applied_id .'',NULL,NULL,NULL,'yes');

            //                 }else{
            //                     return response()->json([
            //                         'status' => 'FALSE',
            //                         'msg' => "Request(s) not approved successfully",
            //                     ]);
            //                 }

            //                 // $leave_approval = new RequestApproval();
            //                 // $leave_approval->request_module_id = 2;
            //                 // $leave_approval->request_id = $leave_request_id;
            //                 // $leave_approval->request_approver_id =  $approval_maker;
            //                 // $leave_approval->request_approver_status = 2;
            //                 // $leave_approval->request_approver_msg = $request['approvermsg'];
            //                 // $leave_approval->request_approval_time = date('h:i A d M, Y');
            //                 // $process = $leave_approval->save();
                            
            //                 // if($process == 1){

            //                 //     $employee_name = Employees::where('employee_id', $update_leave['leave_applied_by'])->get(['employee_id','employee_code','employee_name'])->toArray();
                            
            //                 //     $notification_msg = 'Dear employee, ' .$login_user[0]['employee_name']. ' marked your leave request(s) as approved for the date of '.$update_leave['leave_applied_for_date']. ' Please click here to view.';
                            
            //                 //     $this->send_notification('Leave Request Approval ', $notification_msg , 'my-leaves?request-id='. $update_leave->leave_applied_id .'',$update_leave['leave_applied_by'],NULL,NULL,'no');

            //                 //     $this->send_push_notification_mobile_app($update_leave['leave_applied_by'], 'Leave Request','Your Leave request been approved by your line manager.');
                            
            //                 //     $notification_msg_hr= ''.$employee_name[0]['employee_name'].''."'s".' Manager approved their leave request(s) for the date of '.$update_leave['leave_applied_for_date'].'. Please click here to view.';

            //                 //     $this->send_notification('Leave Request Approval ',  $notification_msg_hr , 'employees-leaves?request-id='. $update_leave->leave_applied_id .'',NULL,NULL,NULL,'yes');
                            
        
            //                 // }else{
                
            //                 //     return response()->json([
            //                 //         'status' => 'FALSE',
            //                 //         'msg' => "Request(s) not approved successfully ",
                                    
            //                 //     ]);
            //                 // }
            //             }
            //         }
            //     }
                    
            //     if($process){
            //         return response()->json([
            //             'status' => 'TRUE',
            //             'msg' => "Request(s) approved successfully ",
                        
            //         ]);
            //     }else{
            //         return response()->json([
            //             'status' => 'FALSE',
            //             'msg' => "Already responded to this request",
                        
            //         ]);
            //     }
            // }

            // if($request['status'] == "LeadDisApprove"){

            //     $process = '';
        
            //     $leave_request_ids = explode(",",$request['ids']);

            //     $login_user = $this->get_login_user_detail();

            //     $approval_maker = session('user_session');

            //     $status = 1;
    
            //     foreach($leave_request_ids as  $leave_request_id){
                    
            //         $checking_request = RequestApproval::where('request_module_id',2)->where('request_approver_id',session('user_session'))->where('request_id',$leave_request_id)->get()->toArray();

                
            //         if(empty($checking_request)){

            //             $update_leave = LeaveRequest::find($leave_request_id);
                    
            //             if(!empty($update_leave) && $update_leave->leave_approved_by_hr == 1 && in_array($update_leave->leave_applied_by,$this->get_employee_hierarchy_unique($approval_maker,  $status))){
                            
            //                 $update_leave->leave_approved_by_hr = 3;
            //                 $update_leave->leave_approved_date_hr = 'Dis-Approved By : '.$login_user[0]['employee_name'].', at : '.date('h:i A d M, Y').'';
            //                 $process = $update_leave->save();
                            
            //                 if($process == 1){

            //                     $leave_approval = new RequestApproval();
            //                     $leave_approval->request_module_id = 2;
            //                     $leave_approval->request_id = $leave_request_id;
            //                     $leave_approval->request_approver_id =  $approval_maker;
            //                     $leave_approval->request_approver_status = 3;
            //                     $leave_approval->request_approver_msg = $request['disapprovermsg'];
            //                     $leave_approval->request_approval_time = date('h:i A d M, Y');
            //                     $leave_approval->save();
                    
            //                     $employee_name = Employees::where('employee_id', $update_leave['leave_applied_by'])->get(['employee_id','employee_code','employee_name'])->toArray();
                        
            //                     $notification_msg = 'Dear employee, ' . $login_user[0]['employee_name']. ' marked your leave request(s) as Disapproved for the date of '.$update_leave['leave_applied_for_date']. ' Please click here to view.';
                            
            //                     $this->send_notification('Leave Request Disapproval ', $notification_msg ,'my-leaves?request-id='. $update_leave->leave_applied_id .'',$update_leave['leave_applied_by'],NULL,NULL,'no');

            //                     // $this->send_push_notification_mobile_app($update_leave['leave_applied_by'], 'Leave Request Disapproval','Your Leave request has been disapproved by your line manager.');
                            
            //                     $notification_msg_hr= ''.$employee_name[0]['employee_name'].''."'s".' Manager Disapproved their leave request(s) for the date of '.$update_leave['leave_applied_for_date'].'. Please click here to view.';

            //                     $this->send_notification('Leave Request Disapproval ',  $notification_msg_hr , 'employees-leaves?request-id='. $update_leave->leave_applied_id .'',NULL,NULL,NULL,'yes');
                            
            //                 }else{
            //                     return response()->json([
            //                         'status' => 'TRUE',
            //                         'msg'   =>"Request(s) disapproved not successfully ",
                                    
            //                     ]);
            //                 }

            //                 // $leave_approval = new RequestApproval();
            //                 // $leave_approval->request_module_id = 2;
            //                 // $leave_approval->request_id = $leave_request_id;
            //                 // $leave_approval->request_approver_id =  $approval_maker;
            //                 // $leave_approval->request_approver_status = 3;
            //                 // $leave_approval->request_approver_msg = $request['disapprovermsg'];
            //                 // $leave_approval->request_approval_time = date('h:i A d M, Y');
            //                 // $process = $leave_approval->save();
                            
            //                 // if($process == 1){

            //                 //     $employee_name = Employees::where('employee_id', $update_leave['leave_applied_by'])->get(['employee_id','employee_code','employee_name'])->toArray();
                        
            //                 //     $notification_msg = 'Dear employee, ' . $login_user[0]['employee_name']. ' marked your leave request(s) as Disapproved for the date of '.$update_leave['leave_applied_for_date']. ' Please click here to view.';
                            
            //                 //     $this->send_notification('Leave Request Disapproval ', $notification_msg ,'my-leaves?request-id='. $update_leave->leave_applied_id .'',$update_leave['leave_applied_by'],NULL,NULL,'no');

            //                 //     $this->send_push_notification_mobile_app($update_leave['leave_applied_by'], 'Leave Request Disapproval','Your Leave request has been disapproved by your line manager.');
                            
            //                 //     $notification_msg_hr= ''.$employee_name[0]['employee_name'].''."'s".' Manager Disapproved their leave request(s) for the date of '.$update_leave['leave_applied_for_date'].'. Please click here to view.';

            //                 //     $this->send_notification('Leave Request Disapproval ',  $notification_msg_hr , 'employees-leaves?request-id='. $update_leave->leave_applied_id .'',NULL,NULL,NULL,'yes');
                            
            //                 // }else{
                
            //                 //     return response()->json([
            //                 //         'status' => 'FALSE',
            //                 //         'msg' => "Request(s) not disapproved successfully",
                                    
            //                 //     ]);
            //                 // }
            //             }
            //         }
            //     }
                    
            //     if($process){
            //         return response()->json([
            //             'status' => 'TRUE',
            //             'msg' => "Request(s) disapproved successfully ",
                        
            //         ]);
            //     }else{
            //         return response()->json([
            //             'status' => 'FALSE',
            //             'msg' => "Already responded to this request",
                        
            //         ]);
            //     }
        
            // } 

        }catch(\Exception $e){
            $this->create_error_log('Approved And Disapproved Leave Request by Manager', $e);
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
            ActivityTracker::track_activity('Approved And Disapproved Leave Request by Manager');
            
        }
       
 
    }

    protected function update_leave_request(Request $request_data){
        try{

            if($this->check_request_validation($request_data->leave_id,2) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Leave request id is invalid',
                ]);
            }

            $filename = '';

            if($request_data->hasFile('leave_attachment')){
                $filename = $request_data->file('leave_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($request_data->all(), [
                'leave_date' => 'required|date_format:Y-m-d|before:'. date('Y-m-d',strtotime('+2 month')).'' ,
                'edit_leave_type' => 'required',
                'edit_leave_reason' => 'required',
                'leave_applied_attachment' => ['max:10000', ($filename == 'txt') ? 
                File::types(['jpg','png','jpeg','docx','pdf','txt'])
                :
                File::types(['jpg','png','jpeg','docx','pdf'])],
            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{

                $reamining_leaves;
                $no_of_days = 1;

                $login_user = $this->get_login_user_detail();

                if($request_data->input('leave_date') < $login_user[0]['employee_doj']){
                    return response()->json([
                        'msg' => 'You can\'t edit leave before your joining date.',
                        'status' => 'FALSE'
                    ]);
                }

                $leave_name = LeaveManagement::where('leave_manage_id',$request_data->input('edit_leave_type'))->get(['leave_manage_id','leave_name','leave_visibilty'])->toArray();

                if(!empty($leave_name)){
                   
                    $leave_alloted = Leave::where('leaves_employee_id',session('user_session'))->where('leave_type_id',$request_data->input('edit_leave_type'))->get(['leave_allotement'])->toArray();
    
                    if($leave_name[0]['leave_visibilty'] == 1){
                        
                        if(empty($leave_alloted)){
                            $leave_alloted[0]['leave_allotement'] = 0;
                        }
    
                        $leave_request_applied = LeaveRequest::where('leave_applied_by',(string) session('user_session'))
                            ->where('leave_applied_type', $request_data->input('edit_leave_type'))
                            ->where('leave_applied_id','!=',$request_data->leave_id)
                            ->where('leave_approved_by_hr',1)
                            ->count();
    
                        $get_availed_leaves = Attendance::where('att_employee_id',session('user_session'))->where('att_check_overall_status', $leave_name[0]['leave_name'])->count() + LeaveDeduction::deducted_count(session('user_session'), $leave_name[0]['leave_manage_id']);
                
                        $total_leaves = $get_availed_leaves + $leave_request_applied;
                        
                        $reamining_leaves =  $leave_alloted[0]['leave_allotement'] - $total_leaves;
                    
                        if(!empty($leave_alloted) && $reamining_leaves < $no_of_days){
                            if($reamining_leaves < 0){
                                $reamining_leaves = 0;
                            }
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'You have only ' . $reamining_leaves . ' ' . $leave_name[0]['leave_name']. '(s) reamining to apply',
                            ]);
    
                        }
                    
                        $leave_update_request = LeaveRequest::find($request_data->leave_id);
    
                        if(!empty($leave_update_request)){
                            
                            // if($request_data->edit_leave_type == "3"){
                            //     $employement_status_check = $this->check_if_employee_permanent($request_data->input('leave_date'),session('user_session'),$custom_message = NULL);
                            //     if(!$employement_status_check['status']){
                            //         return response()->json([
                            //             'msg' => $employement_status_check['msg'],
                            //             'status' => 'FALSE'
                            //         ]);
                            //     }
                            // }

                            // $payroll_exists = $this->check_if_payroll_exist($request_data->input('leave_date'),session('user_session'),$custom_message = NULL);
    
                            // if($payroll_exists['status'] == TRUE){
                            //     return response()->json([
                            //         'msg' => $payroll_exists['msg'],
                            //         'status' => 'FALSE'
                            //     ]);
                            // }

                            $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request_data->input('leave_date'));

                            if($check_attendance_sheet_availability['status'] == FALSE){
                                return response()->json([
                                    'msg' => 'Leave sheet has been locked for the date of ' . date('d M, Y', strtotime($request_data->input('leave_date'))) . '. You can\'t update leave / apply for any kind of request for this period.',
                                    'status' => 'FALSE'
                                ]);
                            }
    
                            $checking_request_responsed = RequestApproval::where('request_module_id',2)->where('request_id',$request_data->leave_id)->get()->toArray();
                        
                            if(empty($checking_request_responsed) && $leave_update_request->leave_approved_by_hr == 1){
    
                                $checking_request =  LeaveRequest::where('leave_applied_by', (string) session('user_session'))->where('leave_applied_for_date',date('Y-m-d',strtotime($request_data->input('leave_date'))))->where('leave_applied_id','!=',$request_data->leave_id)->get(['leave_applied_for_date'])->toArray();
                                
                                if(empty($checking_request)){
                                
                                    $leave_update_request->leave_applied_for_date = date('Y-m-d',strtotime($request_data->input('leave_date')));
                                    $leave_update_request->leave_applied_reason = $request_data->input('edit_leave_reason');
                                    $leave_update_request->leave_applied_type = $request_data->input('edit_leave_type');
                                    $leave_update_request->leave_applied_date = date('d-m-Y H:i:s');
    
                                    if($request_data->hasFile('leave_applied_attachment')){
    
                                        if(!empty($leave_update_request->leave_applied_attachment)){
    
                                            if (file_exists(storage_path('app/portal_documents/leave_attachment/'.$leave_update_request->leave_applied_attachment))) {
                            
                                                unlink(storage_path('app/portal_documents/leave_attachment/'.$leave_update_request->leave_applied_attachment));
                                            }
                                        }   
                    
                                        $fileNameToStore = $request_data->leave_id.$request_data->file('leave_applied_attachment')->getClientOriginalName();
                                        
                                        $request_data->file('leave_applied_attachment')->storeAs('portal_documents/leave_attachment/',$fileNameToStore);
                            
                                        $leave_update_request->leave_applied_attachment = $fileNameToStore;
                            
                                    }
                                
                                    $process = $leave_update_request->save();
    
                                    if($process == 1){

                                        // if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                        //     $to_mngr1 = $login_user[0]['employee_senior_manager'];
                                        // }
                                        // if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                        //     $to_mngr2 = $login_user[0]['employee_junior_manager'];
                                        // }
                        
                                        // $this->send_notification('Update Leave Request ', '' . $login_user[0]['employee_name']. '  update an leave request as ('.  $leave_name[0]['leave_name'] .') for the date of '. $request_data->input('leave_date').' Please click here to view.','employees-leaves?employee_id='.session('user_session').'&date='.$request_data->input('leave_date').'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');
                                      
                                        // $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Update Leave Request','Your leave request has been updated.');
                                    
                                        return response()->json([
                                            'status' => 'TRUE',
                                            'msg' => 'Leave request has been updated. Please wait for response',
                                        ]);

                                    }else{
                                        return response()->json([
                                            'status' => 'FALSE',
                                            'msg' => 'Leave request failed. Please try again.',
                                        ]);
                                    }
                                    
                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => 'You have already submitted a request for this date '.$request_data->input('leave_date').'.',
                                    ]);
                                }
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => 'You can\'t edit this request now because your reporting authority has changed its status.',
                                    "autoreload" => "TRUE",
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
                            'msg' => 'You can\'t  apply '.$leave_name[0]['leave_name'].' at the moment.'
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t apply this leave at the moment.',
                    ]);
                }

               
            }          
        }catch(\Exception $e){
            $this->create_error_log('Update Leave Request', $e);
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
            ActivityTracker::track_activity('Update Leave Request');
            
        }
    }

    protected function delete_leave_request(Request $request_data){
      
        try{
            if($this->check_request_validation($request_data->leave_id,2) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Leave request id is invalid',
                ]);
            }
            $leave_delete_request = LeaveRequest::find($request_data->leave_id);

            if(!empty($leave_delete_request)){
                    
                $checking_request_responsed = RequestApproval::where('request_module_id',2)->where('request_id',$request_data->leave_id)->get()->toArray();
            
                if(empty($checking_request_responsed) && $leave_delete_request->leave_approved_by_hr == 1){

                    if(!empty($leave_delete_request->leave_applied_attachment)){

                        if (file_exists(storage_path('app/portal_documents/leave_attachment/'.$leave_delete_request ->leave_applied_attachment))) {
                    
                            unlink(storage_path('app/portal_documents/leave_attachment/'.$leave_delete_request ->leave_applied_attachment));
                        }
                    }

                    $process = LeaveRequest::where('leave_applied_id', $request_data->leave_id)->delete();

                    if( $process == 1){
                    
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Leave request has been deleted',
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
                        'msg' => 'You can\'t delete this request now because your reporting authority has changed its status.',
                        "autoreload" => "TRUE",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Request not found on this id.',
                ]);
            }     

        }catch(\Exception $e){
            $this->create_error_log('Delete Leave Request', $e);
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
            ActivityTracker::track_activity('Delete Leave Request');
            
        }
    }

    protected function count_days($from_date, $to_date){

        $from_date = strtotime($from_date);
        $to_date = strtotime($to_date);
        
        $timeDiff = abs($to_date - $from_date);
        
        $numberDays = $timeDiff/86400;
        
        $no_of_days = intval($numberDays + 1);
        
        return $no_of_days;
    }

    protected function download_leave_file(Request $request){
       
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
                return redirect('/');
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
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
            ActivityTracker::track_activity($request->title);
        }
    }


}
