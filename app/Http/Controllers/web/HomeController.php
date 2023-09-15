<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Employees, Leave, EmployeeInformation, Attendance, Interview, Alert, AlertStatus, LateComers, ExpenseSheet, Meeting, MeetingUsers,HrmSetting,Department,LeaveRequest,WFH,InterviewStatus, Polling , PollingOption, PollingStatus,LeaveManagement};
use Illuminate\Support\Facades\Validator;
use App\Traits\{LogGenerateTrait, NotificationTrait, LoginEmployee};
use App\Helpers\ActivityTracker;

use DateTime;
use DateInterval;
use DatePeriod;

class HomeController extends Controller
{
    use LoginEmployee, NotificationTrait, LogGenerateTrait;

    const MAIN_TITLE = "index";
    const MAIN_TITLE2 = 'link-post';


    protected function index(Request $request){
        try{
            
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();

            $active_employees = Employees::where('employee_isactive', 1)->count();

            $active_male_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Male')->count();

            $active_female_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Female')->count();

            $deactive_employees = Employees::where('employee_isactive', 0)->count();

            $deactive_male_employees = Employees::where('employee_isactive', 0)->where('employee_gender','Male')->count();

            $deactive_female_employees = Employees::where('employee_isactive', 0)->where('employee_gender','Female')->count();

            $total_employees =  $active_employees + $deactive_employees;

            $permanent_employees = Employees::where('employee_isactive', 1)->where('employee_status',3)->count();

            $permanent_male_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Male')->where('employee_status',3)->count();

            $permanent_female_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Female')->where('employee_status',3)->count();
           
            $probation_employees = Employees::where('employee_isactive', 1)->where('employee_status',2)->count();

            $probation_male_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Male')->where('employee_status',2)->count();

            $probation_female_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Female')->where('employee_status',2)->count();

            $setting = HrmSetting::orderBy('setting_id','DESC')->get(['setting_id','linked_post1','linked_post2'])->toArray();

            $leaves_entitiled = LeaveManagement::join('tbl_leave_entitlement','tbl_leave_management.leave_manage_id','=','tbl_leave_entitlement.leave_type_id')->WhereIN('leave_type_id',array(1,2,3))->where('leaves_employee_id', session('user_session'))->get('leave_allotement')->toArray();

            $annual_used = Attendance::where('att_employee_id', session('user_session'))->where('att_check_overall_status','Annual Leave')->count();

            $casual_used = Attendance::where('att_employee_id', session('user_session'))->where('att_check_overall_status','Casual Leave')->count();

            $sick_used = Attendance::where('att_employee_id', session('user_session'))->where('att_check_overall_status','Sick Leave')->count();

            $todays_interviews = Interview::where('interview_date',date('Y-m-d'))->count();

            if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2){
                $where_clause = "interview_date LIKE '%" . date("Y-m-d") . "%'";
            }else{
                $where_clause = 'interview_date LIKE  "%'.date("Y-m-d").'%"  and ( interview_interviewer1 = '.session('user_session').' or interview_interviewer2 = '.session('user_session').' )';
            }
            
            $interviews = Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','=','tbl_interview_candidate.candidate_id')->whereRaw($where_clause)->get(['interview_id','candidate_name','interview_date','interview_time','interview_interviewer1','interview_interviewer2'])->toArray();
 
            $alert_status = AlertStatus::where('alert_status_employee_id', session('user_session'))->pluck('alert_status_alert_id')->toArray();
           
            $alerts = Alert::whereRaw("CONVERT(DATETIME, scheduled_time, 120) < '" . date('Y-m-d H:i:s') . "' and CONVERT(DATETIME, valid_dates, 120) > '" . date('Y-m-d H:i:s') . "'")
            ->where(function($query) use($login_user) {
                $query->where('department', 'LIKE', '%' .$login_user[0]['employee_department']. '%')->orWhere('department', 'LIKE', '%*%');
            })
            ->whereNotIn('alert_id', $alert_status)
            ->limit(3)->get(['alert_id','title','message'])->toArray();
            
            $birthdays = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=','tbl_personal_informations.employeeid')
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_isactive',1)
            ->whereNotNull('dob')
            ->whereMonth('dob','=',date('m'))
            ->orderByRaw('DAY(dob)')
            ->get(['employee_id','department_name as employee_department','designation_name as employee_designation','dob'])
            ->toArray();

            $senior_manager = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')->where('employee_id','=',$login_user[0]['employee_ra'])->get(['employee_name','profile_avatar'])->toArray();
                
            $junior_manager = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')->where('employee_id','=',$login_user[0]['employee_ra2'])->get(['employee_name','profile_avatar'])->toArray();

            $bad_attendance_employees = LateComers::join('tbl_employees','tbl_continous_latecomers.employee_id','=','tbl_employees.employee_id')->where('employee_isactive', 1)->where('create_date' , date('Y-m-d'))->get(['tbl_employees.employee_id','attendance_status','create_date'])->toArray();

            $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'],1);
           
            if($login_user[0]['employee_can_ra'] == 1){

                $my_team_mates = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')
                ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->where(function($query) use($login_user,$total_team_member) {
                    $query->where('employee_id','!=',$login_user[0]['employee_id'])->whereIn('employee_id',array_slice($total_team_member, 0, 2000));
                })->get(['employee_name','department_name as employee_department','designation_name as employee_designation','profile_avatar'])->toArray();

            }else{
                $my_team_mates = [];
            }
          
            $current_date = date('Y-m-d');
            $next_date = date('Y-m-d', strtotime("+1 day"));
            

            $meeting_count_user = Meeting::join('tbl_meeting_users','tbl_meeting_users.users_meeting_id','=','tbl_meetings.meeting_id')
            ->where(function ($query) use ($current_date,$next_date) {
                $query->where('meeting_time','like','%'.$current_date.'%')
                ->orWhere('meeting_time','like','%'.$next_date.'%');
            })->where('user_id',session('user_session'))->where('meeting_status',1)->count();
           

            $meeting_count_host = Meeting::where(function ($query) use ($current_date,$next_date) {
                $query->where('meeting_time','like','%'.$current_date.'%')
                ->orWhere('meeting_time','like','%'.$next_date.'%');
            })->where('host_id',session('user_session'))->where('meeting_status',1)->count();
           
            $total_count = $meeting_count_user + $meeting_count_host;

            $departments = Department::get()->toArray();

            $polling_option_pluck = PollingStatus::where('polling_employee_id', $login_user[0]['employee_id'])->pluck('polling_option_id')->toArray();

            $employee_voting = PollingOption::whereIn('polling_option_id', $polling_option_pluck)->pluck('polling_id')->toArray();
           
            $pollings = Polling::whereRaw("CONVERT(DATETIME, created_at, 120) < '" . date('Y-m-d H:i:s') . "' and CONVERT(DATETIME, polling_duration, 120) > '" . date('Y-m-d H:i:s') . "'")
            ->where(function($query) use($login_user) {
                $query->where('polling_department', 'LIKE', '%' .$login_user[0]['employee_department']. '%')->orWhere('polling_department', 'LIKE', '%*%');
                })
            ->where('polling_status', '=', 1)
            ->whereNotIn('polling_id',function($query) use($login_user){
                $query->from('tbl_polling_status')
                ->select('tbl_polling_options.polling_id')
                ->join('tbl_polling_options','tbl_polling_options.polling_option_id','=','tbl_polling_status.polling_option_id')
                ->where('polling_employee_id',$login_user[0]['employee_id']);
            })
            ->orderBy('polling_id', 'ASC')
            ->limit(1)
            ->get(['polling_id','polling_title'])->toArray();

            $i = 0;
            foreach($pollings as $polling){
              
              $polling_options = Polling::selectRaw('tbl_polling_options.polling_option_id,polling_options, count(polling_status_id) as vote')->join('tbl_polling_options', 'tbl_polling_options.polling_id', 'tbl_polling.polling_id')
                    ->leftjoin('tbl_polling_status','tbl_polling_status.polling_option_id' , 'tbl_polling_options.polling_option_id')
                    ->where('tbl_polling.polling_id',$polling['polling_id'])
                    ->groupBy('tbl_polling_options.polling_option_id')
                    ->get()
                    ->toArray();
        
                    $polling_vote = array_sum(array_column($polling_options, 'vote'));
                    
                    $j = 0;
                    foreach($polling_options as $get_polling_option){
                        if($get_polling_option['vote'] != 0){
                            $polling_options[$j]['percentage'] = round($get_polling_option['vote']/$polling_vote * 100, 0);
                        }else{
                            $polling_options[$j]['percentage'] = 0;
                        }
                        
                    $j++;
                    }
              $pollings[$i]['polling_options'] = $polling_options;
              $i++;
            }
          
            $voted_pollings = Polling::whereRaw("CONVERT(DATETIME, created_at, 120) < '".date('Y-m-d H:i:s')."' and CONVERT(DATETIME, polling_duration, 120) > '".date('Y-m-d H:i:s')."'")
            ->where(function($query) use($login_user) {
                $query->where('polling_department', 'LIKE', '%' .$login_user[0]['employee_department']. '%')->orWhere('polling_department', 'LIKE', '%*%');
                })
            ->where('polling_status', '=', 1)
            ->whereIn('polling_id',function($query) use($login_user){
                $query->from('tbl_polling_status')
                ->select('tbl_polling_options.polling_id')
                ->join('tbl_polling_options','tbl_polling_options.polling_option_id','=','tbl_polling_status.polling_option_id')
                ->where('polling_employee_id',$login_user[0]['employee_id']);
            })
            ->orderBy('polling_id', 'ASC')
            ->limit(1)
            ->get(['polling_id','polling_title'])->toArray();

            $i = 0;
            foreach($voted_pollings as $polling){
              
              $polling_options = Polling::selectRaw('tbl_polling_options.polling_option_id,polling_options, count(polling_status_id) as vote')->join('tbl_polling_options', 'tbl_polling_options.polling_id', 'tbl_polling.polling_id')
                    ->leftjoin('tbl_polling_status','tbl_polling_status.polling_option_id' , 'tbl_polling_options.polling_option_id')
                    ->where('tbl_polling.polling_id',$polling['polling_id'])
                    ->groupBy('tbl_polling_options.polling_option_id')
                    ->get()
                    ->toArray();
        
                    $polling_vote = array_sum(array_column($polling_options, 'vote'));
                    
                    $j = 0;
                    foreach($polling_options as $get_polling_option){
                        if($get_polling_option['vote'] != 0){
                            $polling_options[$j]['percentage'] = round($get_polling_option['vote']/$polling_vote * 100, 0);
                        }else{
                            $polling_options[$j]['percentage'] = 0;
                        }
                        
                    $j++;
                    }
              $voted_pollings[$i]['polling_options'] = $polling_options;
              $i++;
            }

            $probation_employees_detail = Employees::where('employee_isactive', 1)->where('employee_status',2)->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->get(['employee_id','employee_code','employee_name','employee_doj','department_name','designation_name'])->toArray();

            $get_check_in_details = $this->get_latest_attendance_record();
    
            $send_data = compact('title','login_user','active_employees','deactive_employees','leaves_entitiled','annual_used','casual_used','sick_used','todays_interviews', 'birthdays','senior_manager','junior_manager','interviews','alerts','bad_attendance_employees','permanent_employees','probation_employees','total_employees','my_team_mates','total_count','active_male_employees','active_female_employees','deactive_male_employees','deactive_female_employees','permanent_male_employees','permanent_female_employees','probation_male_employees','probation_female_employees','setting','departments','pollings','employee_voting','probation_employees_detail','voted_pollings','dynamic_years','get_check_in_details');
            
            return view('dashboard.dashboard', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('Employees Dashboard', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Access Dashboard Module');
          
        }
    }

    protected function expense_graph(Request $request){
        try{
          
            $from = date($request->year.'-'.$request->month.'-d',strtotime('first day of this month'));
            $to = date($request->year.'-'.$request->month.'-d',strtotime('last day of this month'));

            $start    = new DateTime($from);
            $start->modify('first day of this month');
            $end      = new DateTime($to);
            $end->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 day');
            $period   = new DatePeriod($start, $interval, $end);
            $all_dates_of_month = [];
            $all_dates_of_month_for_graph = [];


            foreach ($period as $dt) {

                array_push($all_dates_of_month, $dt->format("Y-m-d"));
                array_push($all_dates_of_month_for_graph, $dt->format("d-M"));
            }

            $all_dates_of_month_for_graph[0] = '';
            $all_dates_of_month_for_graph[count($all_dates_of_month_for_graph)-1] = '';


            $fetch_expense = ExpenseSheet::selectRaw('sum(expense_amount) as expense_amount,expense_date')->whereRaw('STR_TO_DATE(expense_date,"%Y-%m-%d") between "'.$from.'" and  "'.$to.'"')
            ->groupBy('expense_date')->orderByRaw('STR_TO_DATE(expense_date,"%Y-%m-%d")')->get()->toArray();

            $values = [];

            foreach($all_dates_of_month as $dates){

                if(!empty($fetch_expense)){

                    if($dates==$fetch_expense[0]['expense_date']){

                        array_push($values,$fetch_expense[0]['expense_amount']);
                        unset($fetch_expense[0]);
                        $fetch_expense = array_values($fetch_expense);
    
                    }else{
    
                        array_push($values,0);
    
                    }
                }else{
                    array_push($values,0);
                }
                


            }

            $total_expense =  number_format(array_sum($values));
            return response()->json([
                'values' => $values,
                'date_time' => $all_dates_of_month_for_graph,
                'total_expense' => $total_expense
            ]);


        }catch(\Exception $e){

            $this->create_error_log('Expense Graph Dashboard', $e);

        }finally{

            if($request->filter_expense_month == 'yes'){
                ActivityTracker::track_activity('Expense Graph Dashboard');
            }

        }
    }


    protected function department_employee_graph(Request $request){

        try{

            $fetch_department_employee_count = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->selectRaw("sum(case when `employee_isactive` = 1 then 1 else 0 end ) as 'active',sum(case when `employee_isactive` = 0 then 1 else 0 end ) as 'deactive',count(employee_isactive) as 'total',department_name")
            ->groupBy('department_name')
            ->get()
            ->toArray();

            for($i=0;$i<count($fetch_department_employee_count);$i++){
                $fetch_department_employee_count[$i]['department_name'] = substr($fetch_department_employee_count[$i]['department_name'],0,10);
            } 
            
            return response()->json([
                'active' => array_column($fetch_department_employee_count, 'active'),
                'deactive' =>  array_column($fetch_department_employee_count, 'deactive'),
                'total' => array_column($fetch_department_employee_count, 'total'),
                'department' => array_column($fetch_department_employee_count, 'department_name'),
                'max_y' => max(array_column($fetch_department_employee_count, 'total')) + (max(array_column($fetch_department_employee_count, 'total')) * 0.1)
            ]);

        }catch(\Exception $e){
            $this->create_error_log('Department Employee Graph Dashboard', $e);
        }finally{
            ActivityTracker::track_activity('Department Employee Graph Dashboard');
        }
    }

    protected function linked_post_index(){

        try{

            $title = Self::MAIN_TITLE2;

            $login_user = $this->get_login_user_detail();
    
            $setting = HrmSetting::orderBy('setting_id','DESC')->get(['setting_id','linked_post1','linked_post2'])->toArray();
    
            $send_data = compact('title','login_user','setting');
            
            return view('linked-post.add-linked-post',$send_data);
          
        }catch(\Exception $e){

            $this->create_error_log('Add Linked Post', $e);
            $send_data = compact('title','login_user');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Add Linked Post');
          

        } 
       
    }

    protected function linked_post_add(Request $request_data){

        try{

            $validator = Validator::make($request_data->all(), [
                'setting_id' => ['required','numeric'],
                'linked_post_1' => ['required'],
                'linked_post_2' => ['required'],
            ]);
    
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
    
                $update_hrm_setting = HrmSetting::find($request_data->input('setting_id'));
                $update_hrm_setting->linked_post1 = $request_data->input('linked_post_1');
                $update_hrm_setting->linked_post2 = $request_data->input('linked_post_2');
                $process = $update_hrm_setting->save();
        
                if($process == 1){

                    $this->send_notification('Company\'s New LinkedIn Post','Dear All, We\'ve posted something new on our company\'s linkedin account. We encourage you to take a moment to read it and engage with the content. Thankyou !','/','*','','','no');
                       
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'LinkedIn post added successfully.',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'LinkedIn post not added successfully.',
                    ]);
                }
        
            }

        }catch(\Exception $e){

            $this->create_error_log('Add LinkedIn Post', $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Something went wrong. Please try again.",
            ]);

        }finally{

            ActivityTracker::track_activity('Add LinkedIn Post');
          

        } 
    }

    protected function leave_request_graph(Request $request_data){
      
        try{
            $leave_request_total =$this->leave_request_count($request_data->year,$request_data->department,$request_data->emp_id,array(1,2,3));

            $leave_request_aprove =$this->leave_request_count($request_data->year,$request_data->department,$request_data->emp_id,array(2));
    
            $leave_request_disaprove =$this->leave_request_count($request_data->year,$request_data->department,$request_data->emp_id,array(3));
           
            $leave_request_year = array_column($leave_request_total,'leave_applied_for_date');
          
            $leave_request_count = array_column($leave_request_total,'leave_applied_id');
            
            $leave_request_approve_year = array_column($leave_request_aprove,'leave_applied_for_date');
            $leave_request_approve_count = array_column($leave_request_aprove,'leave_applied_id');
    
            $leave_request_disapprove_year = array_column($leave_request_disaprove,'leave_applied_for_date');
            $leave_request_disapprove_count = array_column($leave_request_disaprove,'leave_applied_id');
    
    
            $total_result = array();
            $total_approve_result = array();
            $total_disapprove_result = array();
    
            for($i=1;$i<=12;$i++){
                if($i <= 9){
                    $values =  $request_data->year.'-0'.$i;
                }else{
                    $values = $request_data->year.'-'.$i;
                }
    
                if(in_array($values, $leave_request_year)){
                    array_push($total_result,$leave_request_count[array_search($values, $leave_request_year)]);
                    
                }else{
                    array_push($total_result,0);
                  
                } 
    
                if(in_array($values,$leave_request_approve_year)){
                    array_push($total_approve_result,$leave_request_approve_count[array_search($values, $leave_request_approve_year)]);
                }else{
                    array_push($total_approve_result,0);
                } 
    
                if(in_array($values,$leave_request_disapprove_year)){
                    array_push($total_disapprove_result,$leave_request_disapprove_count[array_search($values,$leave_request_disapprove_year)]);
                }else{
                    array_push($total_disapprove_result,0);
                } 
    
                
            }
          
            return response()->json([
                'total_request' => $total_result,
                'total_approve_request' => $total_approve_result,
                'total_disapprove_request' => $total_disapprove_result,
                
            ]);
        }catch(\Exception $e){


            $this->create_error_log('Leave Request Graph Dashboard', $e);
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
            if($request_data->filter_leave_request == 'yes'){
                ActivityTracker::track_activity('Leave Request Graph Dashboard');
            }
          

        }
       
    }

    public function leave_request_count($year,$department_id,$employee_id,$status){
        try{
            $leave_request_total = array();
            if($department_id == null){
                $employees = Employees::where('employee_isactive',1)->pluck('employee_id')->toArray();
            }else{
                if($employee_id != null){
                    $employees = Employees::where('employee_isactive',1)->where('employee_id',$employee_id)->pluck('employee_id')->toArray();
                }else{
                    $employees = Employees::where('employee_isactive',1)->where('employee_department',$department_id)->pluck('employee_id')->toArray();
                }  
            }
    
            if($status == 2){
                $where_clause = 2;
            }elseif($status == 3){
                $where_clause = 3;
            }else{
                $where_clause = $status;
            }
    
            $leave_request_total = LeaveRequest::selectRaw('DATE_FORMAT(leave_applied_for_date,"%Y-%m") as leave_applied_for_date,count(leave_applied_id) as leave_applied_id')->whereRaw('DATE_FORMAT(leave_applied_for_date,"%Y") = "'.$year.'"')->groupByRaw('DATE_FORMAT(leave_applied_for_date,"%Y-%m")')->whereIn('leave_applied_by',$employees)->whereIn('leave_approved_by_hr',$where_clause)->get()->toArray(); 
            
            

        }catch(\Exception $e){

            $this->create_error_log('Leave Request Graph Dashboard ', $e);
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

            return  $leave_request_total;
        }
     

    }

    protected function wfh_request_graph(Request $request_data){
       
        try{
           
            $wfh_request_total = $this->wfh_request_count($request_data->year,$request_data->department,$request_data->emp_id,array(1,2,3));
            
            $wfh_request_aprove = $this->wfh_request_count($request_data->year,$request_data->department,$request_data->emp_id,array(2));
    
            $wfh_request_disaprove = $this->wfh_request_count($request_data->year,$request_data->department,$request_data->emp_id,array(3));
           
            $wfh_request_year = array_column($wfh_request_total,'wfh_request_date');
          
            $wfh_request_count = array_column($wfh_request_total,'total_count');
            
            $wfh_request_approve_year = array_column($wfh_request_aprove,'wfh_request_date');
            $wfh_request_approve_count = array_column($wfh_request_aprove,'total_count');
    
            $wfh_request_disapprove_year = array_column($wfh_request_disaprove,'wfh_request_date');
            $wfh_request_disapprove_count = array_column($wfh_request_disaprove,'total_count');
    
           
            $total_result = array();
            $total_approve_result = array();
            $total_disapprove_result = array();
    
            for($i=1;$i<=12;$i++){
                if($i <= 9){
                    $values =  $request_data->year.'-0'.$i;
                }else{
                    $values = $request_data->year.'-'.$i;
                }
    
                if(in_array($values, $wfh_request_year)){
                    array_push($total_result,$wfh_request_count[array_search($values, $wfh_request_year)]);
                    
                }else{
                    array_push($total_result,0);
                  
                } 
    
                if(in_array($values,$wfh_request_approve_year)){
                    array_push($total_approve_result,$wfh_request_approve_count[array_search($values, $wfh_request_approve_year)]);
                }else{
                    array_push($total_approve_result,0);
                } 
    
                if(in_array($values,$wfh_request_disapprove_year)){
                    array_push($total_disapprove_result,$wfh_request_disapprove_count[array_search($values,$wfh_request_disapprove_year)]);
                }else{
                    array_push($total_disapprove_result,0);
                } 
    
                
            }
          
        }catch(\Exception $e){

            $this->create_error_log('Wfh Request Graph Dashboard', $e);
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

            if($request_data->filter_wfh == 'yes'){
                ActivityTracker::track_activity('Wfh Request Graph Dashboard');
            
            }
           
            return response()->json([
                'total_request' => $total_result,
                'total_approve_request' => $total_approve_result,
                'total_disapprove_request' => $total_disapprove_result,
                
            ]);

        }
       
    }

    public function wfh_request_count($year,$department_id,$employee_id,$status){

        try{
            $wfh_request_total = array();
            if($department_id == null){
                $employees = Employees::where('employee_isactive',1)->pluck('employee_id')->toArray();
            }else{
                if($employee_id != null){
                    $employees = Employees::where('employee_isactive',1)->where('employee_id',$employee_id)->pluck('employee_id')->toArray();
                }else{
                    $employees = Employees::where('employee_isactive',1)->where('employee_department',$department_id)->pluck('employee_id')->toArray();
                }
            }
    
            if($status == 2){
                $where_clause = 2;
            }elseif($status == 3){
                $where_clause = 3;
            }else{
                $where_clause = $status;
            }
    
            $wfh_request_total = WFH::selectRaw('DATE_FORMAT(wfh_request_date,"%Y-%m") as wfh_request_date,count(wfh_request_id) as total_count')->whereRaw('DATE_FORMAT(wfh_request_date,"%Y") = "'.$year.'"')->groupByRaw('DATE_FORMAT(wfh_request_date,"%Y-%m")')->whereIn('wfh_employee_id',$employees)->whereIn('wfh_request_approved_by_hr',$where_clause)->get()->toArray(); 
            
           
        }catch(\Exception $e){

            $this->create_error_log('WFH Request Graph Dashboard ', $e);
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

            return  $wfh_request_total;

        }
     

    }

    protected function interview_graph(Request $request_data){
       
        try{
            $overall_interview = $this->interview_status_count($request_data->year,$request_data->month,array(1,5,8,9));
      
            $scheduled_interview = $this->interview_status_count($request_data->year,$request_data->month,array(1));
    
            $offerPlaced_interview = $this->interview_status_count($request_data->year,$request_data->month,array(5));
           
            $hired_interview = $this->interview_status_count($request_data->year,$request_data->month,array(8));
    
            $cancelled_interview = $this->interview_status_count($request_data->year,$request_data->month,array(9));
            
            
        }catch(\Exception $e){

            $this->create_error_log('Interview Graph Dashboard Access', $e);
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

            if($request_data->filter_interview == 'yes'){
                ActivityTracker::track_activity('Interview Graph Dashboard Access');
            }
 
            return response()->json([
                'overall_interviews' => $overall_interview,
                'scheduled_interview' => $scheduled_interview,
                'offerPlaced_interview' => $offerPlaced_interview,
                'hired_interview' => $hired_interview,
                'cancelled_interview' => $cancelled_interview,
               
                
            ]);

        }
    }
  
    protected function interview_status_count($year,$month,$status){

        try{
            if($month == null){
            
                $where_month = '1 = 1';
            }else{
                $where_month = 'DATE_FORMAT(STR_TO_DATE(interview_status_created_at,"%d-%m-%Y %H:%i:%s"),"%m") = "'.$month.'"';
            }
            if($status == 1){
                $where_clause = 1;
            }elseif($status == 5){
                $where_clause = 5;
            }elseif($status == 8){
                $where_clause = 8;
            }elseif($status == 9){
                $where_clause = 9;
            }else{
                $where_clause = $status;
            }
            
            $interview_status = InterviewStatus::join('tbl_interview_status_level','tbl_interview_status.interview_status_level_id','tbl_interview_status_level.interview_status_level_id')->whereRaw('DATE_FORMAT(STR_TO_DATE(interview_status_created_at,"%d-%m-%Y %H:%i:%s"),"%Y") = "'.$year.'"')->whereRaw($where_month)->whereIn('tbl_interview_status.interview_status_level_id',$where_clause)->count();
            

        }catch(\Exception $e){

            $this->create_error_log('Interview Graph Dashboard ', $e);
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
            return  $interview_status;

        }
      
    }

    protected function employee_monthly_strength(Request $request_data){
        try{

        
            $total_present_result = array();
            $total_leave_result = array();
            $total_absent_result = array();
            $employee_monthly_strength = $this->employee_monthly_strength_count($request_data->year,$request_data->department,$request_data->emp_id);
        
            
            $total_presnt = array_column($employee_monthly_strength['present'],'total_present');
            $total_presnt_year = array_column($employee_monthly_strength['present'],'date');
        
            $total_leave = array_column($employee_monthly_strength['leave'],'total_leave');
            $total_leave_year = array_column($employee_monthly_strength['leave'],'date');
            
            $total_absent = $employee_monthly_strength['absent'];
            if(!empty($employee_monthly_strength['absent'])){
                $total_absent = array_combine(range(1, count($employee_monthly_strength['absent'])), array_values($employee_monthly_strength['absent']));
            
            }
        
            for($i=1;$i<=12;$i++){
                if($i <= 9){
                    $values =  $request_data->year.'-0'.$i;
                }else{
                    $values = $request_data->year.'-'.$i;
                }
            
                if(in_array($values, $total_presnt_year)){
                    array_push($total_present_result,$total_presnt[array_search($values, $total_presnt_year)]);
                    
                }else{
                    array_push($total_present_result,0);
                
                } 
        
                if(in_array($values,$total_leave_year)){
                    array_push($total_leave_result,$total_leave[array_search($values, $total_leave_year)]);
                    
                }else{
                    array_push($total_leave_result,0);
                } 
        
                if(array_key_exists($i,$total_absent)){
                    
                    if(in_array($values,$total_presnt_year)){
                        array_push($total_absent_result,$total_absent[$i]);
                        
                    }
                }else{
                    array_push($total_absent_result,0);
                }    

               
            }

            return response()->json([
                'total_present' => $total_present_result,
                'total_leave' => $total_leave_result,
                'total_absent' => $total_absent_result,
                
            ]);
            

        }catch(\Exception $e){

            $this->create_error_log('Employee Monthly Strength Graph Dashboard ', $e);
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

            if($request_data->filter_emp_strength == 'yes'){
                ActivityTracker::track_activity('Employee Monthly Strength Graph Dashboard ');
            }
          
        }
    }
    
    public function employee_monthly_strength_count($year,$department_id,$employee_id){
        try{
        
            if($department_id == null){
                $employees = Employees::where('employee_isactive',1)->pluck('employee_id')->toArray();
            }else{
                if($employee_id != null){
                    $employees = Employees::where('employee_isactive',1)->where('employee_id',$employee_id)->pluck('employee_id')->toArray();
                }else{
                    $employees = Employees::where('employee_isactive',1)->where('employee_department',$department_id)->pluck('employee_id')->toArray();
                }  
            }

            $leaves_name = LeaveManagement::pluck('leave_name')->toArray();
        
            $t_employee_present = Attendance::selectRaw('CONCAT(att_for_year,"-",att_for_month) as date,count(*) as total_present')->whereIn('att_employee_id',$employees)->where(['att_for_year'=>$year])->whereIn('att_check_overall_status' ,array('Present','Late Coming','Half Day','Early Out'))->groupBy('att_for_month')->get()->toArray();
        
            $t_employee_leaves = Attendance::selectRaw('CONCAT(att_for_year,"-",att_for_month) as date, count(*) as total_leave')->whereIn('att_employee_id',$employees)->where(['att_for_year'=>$year])->whereIn('att_check_overall_status', $leaves_name)->groupBy('att_for_month')->get()->toArray();
                
                $month = date('m');
                
                $total_absent = [];
                $employee_records = Employees::where('employee_isactive',1)->whereIn('employee_id',$employees)->get(['employee_offday1','employee_id'])->toArray();
        
                
                for($month_count = 1; $month_count <=$month; $month_count++){
                    $month_dates =   $this->get_all_date_from_month($month_count,$year,$format=1);
                    $offDays = 0;
                    $absent = 0;
                    foreach($employee_records as $employee_record){
                        $employee_attendance =  Attendance::selectRaw('CONCAT(att_for_year,"-",att_for_month,"-",att_for_date) as date')->where('att_employee_id',$employee_record['employee_id'])->where(['att_for_year'=>$year])->get()->toArray();
                        $employee_attendance  =  array_column($employee_attendance,'date');
                        $employee_record['employee_offday1'] = 
                        $employee_record['employee_offday1'] = (!empty($employee_record['employee_offday1'])) ? explode(",",$employee_record['employee_offday1']):[];

                        foreach($month_dates as $date){
                            $date = date("Y-m-d", strtotime($date));
                            if($date <= date('Y-m-d')){
                                if(!in_array($date,$employee_attendance)){
                                
                                    $nameOfDay = date("l", strtotime(date($date)));
                                    if(in_array($nameOfDay,$employee_record['employee_offday1'])){
            
                                        $offDays++;
                                    }else{
                                        $absent++;
                                    }
                                        
                                }
                            }
                            
                        }
                    }
                    
                    array_push($total_absent,$absent);
                
                }
            
        
        }catch(\Exception $e){
        
        
            $this->create_error_log('Employee Monthly  Strength Graph Dashboard ', $e);
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
        
            return ['present' => $t_employee_present ,'leave' =>$t_employee_leaves,'offdays' => $offDays,'absent'=>$total_absent];
        
        }
    
    
    }

    protected function procced_to_dashboard(){
        try{
          
            session()->put([
                'welcome_alert'=> "Welcome New Employee",
            ]);
        }catch(\Exception $e){
            
            $this->create_error_log('Procced to Dashboard', $e);
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
        
            ActivityTracker::track_activity('Procced to Dashboard');
            return redirect('/');
        
        }
    }

    protected function get_user_detail($employee_id){

		$fetch_lead_detail	= Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('employee_id','=',$employee_id)->get(['employee_department'])->toArray();

        if(!empty($fetch_lead_detail)){
            
            $data = $fetch_lead_detail[0];
            return response()->json([
                'msg' => 'Employee detail fetch successfully',
                'status' => 'TRUE',
                'data' => $data
            ]);
            
        }else{
            return response()->json([
                'msg' => 'Failed to fetch employee',
                'status' => 'FALSE'
            ]);
        }

    }
}
