<?php

namespace App\Http\Controllers\web;

use App\Models\LeaveDeduction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{AttRequestTime, AttendanceRequest, Attendance, AttendanceAccess, Employees, Department, Designation, HrmSetting, LeaveRequest, Leave, OfficeLocation, ShareAttReport, LeaveManagement, AttendanceLocker, DeletedAttendance, EmployeeGroup};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee};
use App\Helpers\ActivityTracker;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\CustomHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use DatePeriod, DateTime, DateInterval;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use ZipArchive;

use Exception;


class AttendanceController extends Controller
{   
    use LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee;

    const TITLE_GLOBAL = "manage-attendance";
    const TITLE_GLOBAL2 = "my-attendance";
    const TITLE_GLOBAL3 = "daily-attendance";
    const TITLE_GLOBAL4 = "monthly-attendance";
    const TITLE_GLOBAL6 = "attendance-adjustment";
    const SERVER_ERROR = 'Unknown error occured on server. Please try again after some time.';
    
    protected function all_attendance(Request $request){
        try{
            set_time_limit(1800);

            $title = Self::TITLE_GLOBAL;
      
            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();
        
            if(!empty($request->is_active == "2")){
                $employement_status = 0;
            }elseif(!empty($request->is_active == "0")){
                $employement_status = 0;
            }else{
                $employement_status = 1;
            }

            $where_clause = '1=1';
            $attendance_access = array();
            if(!empty($request->employee_location)){
                $where_clause   .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request->employee_location).''; 
            }

            if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'finance', $login_user[0]['employee_id'] ?? null) ){
                $all_employees = Employees::where('employee_isactive',$employement_status)->whereRaw($where_clause)->limit(1000)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();
            }
            else{
                if($login_user[0]['employee_is_manager'] == 1){
                    $all_employees = Employees::where('employee_isactive',$employement_status)->where(function($query){
                        $query->where('employee_senior_manager',session('user_session'));
                        $query->orWhere('employee_junior_manager',session('user_session'));
                    })
                    ->whereRaw($where_clause)->limit(10)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

                    $attendance_access = AttendanceAccess::where('access_employee_id',session('user_session'))->get('attendance_access')->toArray();

                }else{
                    $all_employees = Employees::where('employee_id',session('user_session'))->limit(1)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();
                   
                }
            }
          
            $department = $reporting_authority = $emp_name = '';
            if($request->input('dept') != '' && is_numeric($request->input('dept'))){
                $department = $request->input('dept');
            }

            if($request->input('ra') != '' && is_numeric($request->input('ra'))){
                $reporting_authority = $request->input('ra');
            }
            
            if($request->input('emp_name') != '' && is_numeric($request->input('emp_name'))){
                $emp_name = $request->input('emp_name');
            }

            if($request->input('m') != '' && is_numeric($request->input('m')) && $request->input('m') >= 01 && $request->input('m') <= 12){
                $month = $request->input('m');
                $month_days = cal_days_in_month(CAL_MONTH_GREGORIAN_LONG, $request->input('m') , $request->input('y'));
            }else{
                $month = date('m');
                $month_days = date('t');
            }
            
            if($request->input('y') != '' && is_numeric($request->input('y')) && $request->input('y') >= 1900 && $request->input('y') <= 3000){
                $year = $request->input('y');
            }else{
                $year = date('Y');
            }
            
            // employees of the grid, page by page: same scope (HR all / manager team / own) and filters as before
            $search = trim((string) $request->input('search'));
            $grid_employees = Employees::where('employee_isactive',$employement_status)->whereRaw($where_clause);
            if(!(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'finance', $login_user[0]['employee_id'] ?? null))){
                if($login_user[0]['employee_is_manager'] == 1){
                    $grid_employees->where(function($query){
                        $query->where('employee_senior_manager',session('user_session'));
                        $query->orWhere('employee_junior_manager',session('user_session'));
                    });
                }else{
                    $grid_employees->where('employee_id',session('user_session'));
                }
            }
            if(!empty($emp_name)){
                $grid_employees->where('employee_id',$emp_name);
            }elseif(!empty($reporting_authority) && !empty($department)){
                $grid_employees->where('employee_department',$department)->where(function($query) use($reporting_authority){
                    $query->where('employee_senior_manager',$reporting_authority)
                        ->orWhere('employee_junior_manager',$reporting_authority)
                        ->orWhere('employee_id',$reporting_authority);
                });
            }elseif(!empty($department)){
                $grid_employees->where('employee_department',$department);
            }
            if($search !== ''){
                $grid_employees->where(function($query) use($search){
                    $query->where('employee_name','like','%'.$search.'%')->orWhere('employee_code','like','%'.$search.'%');
                });
            }
            $pagination = $grid_employees->orderBy('employee_id')->paginate(CustomHelper::per_page(), ['employee_id'])->withQueryString();

            $all_attendances =  $this->all_attendance_employee($employement_status , $month , $year ,$department , $reporting_authority , $emp_name , $pagination->pluck('employee_id')->toArray());

            $all_department = Department::get(['department_id','department_name'])->toArray();
            
            $active_employees = []; // the share dialog searches employees through /employee-search

            $manage_leaves = LeaveManagement::get(['leave_manage_id','leave_name'])->toArray();

            $leaves_name = LeaveManagement::pluck('leave_name')->toArray();

           
            $send_data = compact('title','login_user','all_attendances','month_days','all_department','all_employees','month','year','employee_locations','active_employees','manage_leaves','leaves_name','dynamic_years','attendance_access','pagination');
            
            return view('attendance.all-employees-attendance',$send_data);
        }catch(\Exception $e){
            $this->create_error_log('View All Employees Attendance', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('View All Employees Attendance');
         
        }
    }

    protected function personal_attendace(Request $request){
        try{
        
            $title = Self::TITLE_GLOBAL2;
      
            $login_user = $this->get_login_user_detail();

            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

            if($request->input('from_date') == '' && $request->input('to_date') == ''){
                $month_count = 1;
            }


            if($request->input('from_date') && $request->input('to_date')){
                $validator = Validator::make($request->all(), [
                    'from_date' => 'required|date_format:Y-m-d|before:to_date',
                    'to_date' => 'required|date_format:Y-m-d|after:from_date',
                ]);

                $validator->after(function ($validator) use ($request) {
                    if ($this->if_to_date_greater_than_today($request->input('to_date'))) {
                        $validator->errors()->add('to_date', 'The to date must not be greater than today\'s date.');
                    }
                });
        
                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        
                        return Redirect::back()->with('error', $error);
                    }
                }else {

                    if($request->input('from_date') < $login_user[0]['employee_doj']){

                        return Redirect::back()->with('error', 'You can\'t apply filter before your joining date');
                    }
                    $start_date = new DateTime($request->input('from_date'));
                    $end_date = new DateTime($request->input('to_date'));

                    $interval =  $start_date->diff($end_date);
                      
                    $daysDifference = $interval->days;
                   
                    if($daysDifference < 40){
                        $from_date =  $request->input('from_date');
                        $to_date =  $request->input('to_date');

                        $month_count = 0;
                        $period = new DatePeriod(
                            new DateTime($request->input('from_date')),
                            new DateInterval('P1D'),
                            new DateTime($request->input('to_date'))
                        );

                        $month_days = 0;
                        foreach ($period as $key => $value) {
                            $dates[] = $value->format('d-m-Y');
                            $month_days++;
                        }

                        if (!in_array($to_date, $dates)){
                                
                            $dates[] = date('d-m-Y', strtotime($to_date));
                        }
                        if($month_days == 0){
                            $month_days = date('t', strtotime($request->input('from_date')));
                        }
                 
                        $attendance = Attendance::where('att_employee_id',session('user_session'))
                        ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) >= '".$from_date."'")
                        ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) <= '".$to_date."'")
                        ->get()->toArray();

                        $t_employee_leaves = Employees::where('employee_isactive',1)->join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where('att_employee_id',session('user_session'))
                        ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) >= '".$from_date."'")
                        ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) <= '".$to_date."'")
                        ->where(function($query) use($manage_leaves){
                            $query->whereIn('att_check_overall_status' ,$manage_leaves)
                            ->orWhere('att_check_overall_status','RPL');
                        })->count();
                   }else{

                        return Redirect::back()->with('error', 'Date Range Exceeds 40 Days.Please try again!');
                    
                   }  

                }
            }else{

                if(empty($request->from_date) && empty($request->to_date)){
                    if(date('d') >= '21'){
                        $from_date = date('Y-m-') . '20';
                        $to_date = date('Y-m-d');
                    }else{
                        $today = new DateTime();
                        $today->modify('first day of previous month');
                        $today->modify('+20 days');
                        $from_date = $today->format('Y-m-d');
                        $to_date = date('Y-m-d');
                    }
                }

                $month_count = 0;
                $period = new DatePeriod(
                    new DateTime($from_date),
                    new DateInterval('P1D'),
                    new DateTime($to_date)
                );

                $month_days = 0;
                foreach ($period as $key => $value) {
                    $dates[] = $value->format('d-m-Y');
                    $month_days++;
                }

                if (!in_array($to_date, $dates)){
                        
                    $dates[] = date('d-m-Y', strtotime($to_date));
                }
                if($month_days == 0){
                    $month_days = date('t', strtotime($request->input('from_date')));
                }
            
                $attendance = Attendance::where('att_employee_id',session('user_session'))
                ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) >= '".$from_date."'")
                ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) <= '".$to_date."'")
                ->get()->toArray();

                $t_employee_leaves = Employees::where('employee_isactive',1)->join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where('att_employee_id',session('user_session'))
                ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) >= '".$from_date."'")
                ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) <= '".$to_date."'")
                ->where(function($query) use($manage_leaves){
                    $query->whereIn('att_check_overall_status' ,$manage_leaves)
                    ->orWhere('att_check_overall_status','RPL');
                })->count();


            }
          
            $leave_name = implode(',',$manage_leaves);

            $send_data = compact('title','login_user','attendance','month_days','t_employee_leaves','leave_name','manage_leaves','dates','month_count','from_date','to_date');

            return view('attendance.my-attendance',$send_data); 
        }catch(\Exception $e){
            $this->create_error_log('View My Personal Attendance', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View My Personal Attendance');
        }
    }

    protected function attendance_adjustment(Request $request){
        try{
        
            $title = Self::TITLE_GLOBAL6;
            
            $login_user = $this->get_login_user_detail();

            $status = 1;

            if($request->input('from_date') == '' && $request->input('to_date') == ''){
                $month_count = 1;
            }
            
            if($request->input('from_date') && $request->input('to_date')){
                $validator = Validator::make($request->all(), [
                    'from_date' => 'required|date_format:Y-m-d|before:to_date',
                    'to_date' => 'required|date_format:Y-m-d|after:from_date',
                    'employee_id' => 'required|numeric',
                    'att_status' => 'required',
                ]);

                $validator->after(function ($validator) use ($request) {
                    if ($this->if_to_date_greater_than_today($request->input('to_date'))) {
                        $validator->errors()->add('to_date', 'The to date must not be greater than today\'s date.');
                    }
                });
        
                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        
                        return Redirect::back()->with('error', $error);
                    }
                }else {

                    if($request->input('from_date') < $login_user[0]['employee_doj']){

                        return Redirect::back()->with('error', 'You can\'t apply filter before your joining date');
                    }
                    $start_date = new DateTime($request->input('from_date'));
                    $end_date = new DateTime($request->input('to_date'));

                    $interval =  $start_date->diff($end_date);
                      
                    $daysDifference = $interval->days;
                   
                    if($daysDifference < 40){

                        $from_date =  $request->input('from_date');
                        $to_date =  $request->input('to_date');
                      
                        $employee_id = $request->input('employee_id');

                        $attendance_status = $request->input('att_status');
                        
                        $where_clause = '1=1';
                    
                        // only an employee the logged in user may adjust
                        $all_employees = $this->adjustment_employees($login_user, $status);

                        if(!in_array((int) $employee_id, array_map('intval', array_column($all_employees, 'employee_id')), true)){
                            return view('errors.attendance-restriction');
                        }
                        
                        $month_count = 0;
                        $period = new DatePeriod(
                            new DateTime($request->input('from_date')),
                            new DateInterval('P1D'),
                            new DateTime($request->input('to_date'))
                        );

                        $month_days = 0;
                        foreach ($period as $key => $value) {
                            $dates[] = $value->format('d-m-Y');
                            $month_days++;
                        }

                        if (!in_array($to_date, $dates)){
                                
                            $dates[] = date('d-m-Y', strtotime($to_date));
                        }
                        if($month_days == 0){
                            $month_days = date('t', strtotime($request->input('from_date')));
                        }

                        $employee_data = Employees::where('employee_isactive','=', $status)->where('employee_id', $employee_id)->get(['employee_id','employee_name','employee_code','employee_new_code'])->toArray();

                        if(!empty($employee_data)){
                            $att_login_employee =  $employee_data[0]['employee_id'];

                            $attendance = DB::select("SELECT
                                employee_id,
                                employee_name,
                                employee_code,
                                employee_new_code,
                                DATE(dates.date_between) as 'Date',
                                DAYNAME(dates.date_between) as 'Day',
                                CASE
                                    WHEN emp.employee_doj <= dates.date_between THEN
                                        CASE
                                            WHEN att.att_check_overall_status IS NULL AND LOCATE(DAYNAME(dates.date_between), REPLACE(emp.employee_offdays, ' ', '')) > 0 THEN 'Off Day'
                                            WHEN att.att_check_overall_status IS NULL THEN 'Absent' ELSE 'Present'
                                        END
                                    ELSE '-'
                                END AS 'Attendance Status',
                                CASE
                                    WHEN emp.employee_doj <= dates.date_between THEN IFNULL(att.att_check_in_time, '')
                                    ELSE '-'
                                END AS 'Check In',
                                CASE
                                    WHEN emp.employee_doj <= dates.date_between THEN IFNULL(att.att_check_out_time, '')
                                    ELSE '-'
                                END AS 'Check Out'
                                FROM (
                                    SELECT DATE_ADD('$from_date', INTERVAL n DAY) AS date_between
                                    FROM (
                                        SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS n
                                        FROM (SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) ones,
                                            (SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) tens,
                                            (SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) hundreds
                                    ) AS numbers
                                    WHERE DATE_ADD('$from_date', INTERVAL n DAY) BETWEEN '$from_date' AND '$to_date'
                                ) AS dates
                                LEFT JOIN tbl_attendance AS att ON DATE(dates.date_between) = att.att_comp_date AND att.att_employee_id = $att_login_employee
                                LEFT JOIN tbl_employees AS emp ON $att_login_employee = emp.employee_id
                                WHERE att.att_check_overall_status IS NULL
                                ORDER BY dates.date_between ASC");
                            $attendance = json_decode(json_encode($attendance), TRUE);

                        }else{
                            $attendance = [];
                        }    


                    }else{

                        return Redirect::back()->with('error', 'Date Range Exceeds 40 Days.Please try again!');
                    
                    }  

                }
            }
            else{
                $all_employees = [];
                if(empty($request->from_date) && empty($request->to_date)){
                    // current payroll period so far (it starts on the payroll start day from the settings)
                    $cycle_day = (int) (HrmSetting::value('payroll_start_date') ?: 1);
                    $from_date = ((int) date('d') >= $cycle_day) ? date('Y-m-').sprintf('%02d', $cycle_day) : date('Y-m-', strtotime('first day of previous month')).sprintf('%02d', $cycle_day);
                    $to_date = date('Y-m-d');
                }
                
                $month_count = 0;
                $period = new DatePeriod(
                    new DateTime($from_date),
                    new DateInterval('P1D'),
                    new DateTime($to_date)
                );

                $month_days = 0;
                foreach ($period as $key => $value) {
                    $dates[] = $value->format('d-m-Y');
                    $month_days++;
                }

                if (!in_array($to_date, $dates)){
                        
                    $dates[] = date('d-m-Y', strtotime($to_date));
                }
                if($month_days == 0){
                    $month_days = date('t', strtotime($request->input('from_date'))); 
                }
                $all_employees = $this->adjustment_employees($login_user, $status);

                // nothing is listed until an employee is picked in the filter (no adjusting your own attendance)
                $employee_data = [];

                if (!empty($employee_data)) {

                 $employee_dsr = $employee_data[0]['employee_id'];

                    $attendance = DB::select("SELECT
                    employee_id,
                    employee_name,
                    employee_code,
                    employee_new_code,
                    DATE(dates.date_between) as 'Date',
                    DAYNAME(dates.date_between) as 'Day',
                    CASE
                        WHEN emp.employee_doj <= dates.date_between THEN
                            CASE
                                WHEN att.att_check_overall_status IS NULL AND LOCATE(DAYNAME(dates.date_between), REPLACE(emp.employee_offdays, ' ', '')) > 0 THEN 'Off Day'
                                WHEN att.att_check_overall_status IS NULL THEN 'Absent' ELSE 'Present'
                            END
                        ELSE '-'
                    END AS 'Attendance Status',
                    CASE
                        WHEN emp.employee_doj <= dates.date_between THEN IFNULL(att.att_check_in_time, '')
                        ELSE '-'
                    END AS 'Check In',
                    CASE
                        WHEN emp.employee_doj <= dates.date_between THEN IFNULL(att.att_check_out_time, '')
                        ELSE '-'
                    END AS 'Check Out'
                    FROM (
                    SELECT DATE_ADD('$from_date', INTERVAL n DAY) AS date_between
                    FROM (
                        SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS n
                        FROM (SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) ones,
                            (SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) tens,
                                (SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) hundreds
                        ) AS numbers
                        WHERE DATE_ADD('$from_date', INTERVAL n DAY) BETWEEN '$from_date' AND '$to_date'
                    ) AS dates
                    LEFT JOIN tbl_attendance AS att ON DATE(dates.date_between) = att.att_comp_date AND att.att_employee_id = $employee_dsr
                    LEFT JOIN tbl_employees AS emp ON $employee_dsr  = emp.employee_id
                    WHERE att.att_check_overall_status IS NULL
                    ORDER BY dates.date_between ASC");
                    $attendance = json_decode(json_encode($attendance), TRUE);

                }else{
                    $attendance = [];
                } 
           
            }

            $send_data = compact('title','login_user','attendance','month_days','dates','month_count','from_date','to_date','all_employees','employee_data');

            return view('attendance.att-adjustment-screen',$send_data); 
        }catch(\Exception $e){
            $this->create_error_log('View all Attendance adjustment', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View all Attendance adjustment');
        }
    }

    // employees whose absent days the logged in user may adjust: everyone for HR / admin, the team for a manager
    protected function adjustment_employees($login_user, $status = 1){
        $query = Employees::where('employee_isactive', '=', $status)->where('employee_id', '!=', $login_user[0]['employee_id']);
        if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::has_role($login_user[0]['employee_id'], 'admin')){
            return $query->orderBy('employee_name')->get(['employee_id','employee_name','employee_code','employee_new_code'])->toArray();
        }
        if($login_user[0]['employee_is_manager'] == 1){
            return $query->whereIn('employee_id', $this->get_employee_hierarchy_unique($login_user[0]['employee_id'], $status))->orderBy('employee_name')->get(['employee_id','employee_name','employee_code','employee_new_code'])->toArray();
        }
        return [];
    }

    protected function update_attendance_adjustment(Request $request_data){

        try{

            $validator = Validator::make($request_data->all(), [
                'data.*.attendance_remarks' => 'required',
                'data.*.employee_code' => 'required',
                'data.*.attendance_date' => 'required|date_format:Y-m-d|before_or_equal:now',
            ]);
              
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }
            else
            {

                $process = '';

                $attendance_data = $request_data->input('data');

                if (!empty($attendance_data)) {

                    foreach ($attendance_data as $data) {

                        $attendance_date = $data['attendance_date'];
                        $employee_code = $data['employee_code'];
                        $attendance_remarks = $data['attendance_remarks'];
                        
                        if(!in_array((int) $employee_code, array_map('intval', array_column($this->adjustment_employees($this->get_login_user_detail(), 1), 'employee_id')), true)){
                            return response()->json([
                                'msg' => 'You can\'t adjust the attendance of this employee.',
                                'status' => 'FALSE'
                            ]);
                        }

                        $get_employee_decoded = Employees::where('employee_id',$employee_code)->first();

                        $doj  = strtotime($get_employee_decoded->employee_doj);
        
                        if(strtotime($attendance_date) < $doj){
                            return response()->json([
                                'msg' => 'You can\'t apply attendance request before your joining date.',
                                'status' => 'FALSE'
                            ]);
                        }
        
                        $payroll_exists = $this->check_if_payroll_exist($attendance_date,$employee_code,$custom_message = NULL);
        
                        if($payroll_exists['status'] == TRUE){
                            return response()->json([
                                'msg' => $payroll_exists['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
        
                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($attendance_date);
        
                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
                        
                        $day = date("d", strtotime($attendance_date));
                        $month = date("m", strtotime($attendance_date));
                        $year = date("Y", strtotime($attendance_date));
                    
                        $attendance = Attendance::where('att_employee_id', $employee_code)
                            ->where('att_for_date', $day)
                            ->where('att_for_month', $month)
                            ->where('att_for_year', $year)
                            ->get(['attendance_id'])
                            ->toArray();
                    
                        if(empty($attendance)) {

                            $employee_shift_start = $this->get_employee_today_shift($employee_code, "in", $attendance_date);
                            $employees_shift_end_time = $this->get_employee_today_shift($employee_code, "out", $attendance_date);
                            
                            $create_attendance = new Attendance();
                            $create_attendance->att_check_in_time = $employee_shift_start;
                            $create_attendance->att_check_out_time = $employees_shift_end_time;
                            $create_attendance->att_employee_id = $employee_code;
                            $create_attendance->att_check_in_status = 1;
                            $create_attendance->att_check_out_status = 1;
                            $create_attendance->att_check_in_source = 3;
                            $create_attendance->att_check_out_source = 3;
                            $create_attendance->att_shift_start_time = $employee_shift_start;
                            $create_attendance->att_shift_end_time = $employees_shift_end_time;
                            $create_attendance->att_check_overall_status = 'Present';
                            $create_attendance->att_for_date = $day;
                            $create_attendance->att_for_month = $month;
                            $create_attendance->att_for_year = $year;
                            $create_attendance->att_comp_date = $year . '-' . $month . '-' . $day;
                            $create_attendance->att_request_approval = 3;
                            $create_attendance->att_updated_by = session('user_session');
                            $create_attendance->att_updated_remarks = $attendance_remarks;
                            $create_attendance->att_updated_at = date('Y-m-d H:i:s');
                            $process = $create_attendance->save();
                            
                        } else {
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Attendance already exists for employee $employee_code on $attendance_date.",
                            ]);
                        }

                    }
                }else {
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Attendance data is invalid",
                    ]);
                }
                    
                if($process == 1){

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Attendance adjustment approved successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You can't approve attendance adjustment because already marked attendance.",
                    ]);
                }
                
            }

        }catch(\Exception $e){
            $this->create_error_log('Add New Attendance Adjustment', $e);
            
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
            ActivityTracker::track_activity('Create an Attendance Adjustment');
        }

    }
    

    


    protected function all_attendance_employee($employement_status = NULL, $month = NULL , $year = NULL , $department = NULL , $reporting_authority = NULL ,$emp_name = NULL, $employee_list = NULL){
		
        try{
            $result = [];
            
            if(!empty($emp_name)){
                $all_employees = Employees::where('employee_isactive', $employement_status)
                ->where('employee_id',$emp_name)
                ->get(['employee_id','employee_code','employee_name','employee_offdays'])->toArray();
            }
            elseif(!empty($reporting_authority) && !empty($department) ){
            
                $all_employees =  Employees::select('employee_id', 'employee_code', 'employee_name', 'employee_offdays')->where('employee_department',$department)->where('employee_isactive', $employement_status)->where(function ($query) use ($reporting_authority) {
                    $query->where('employee_senior_manager',$reporting_authority)
                        ->orWhere('employee_junior_manager',$reporting_authority);
                    })
                ->orWhere('employee_id',$reporting_authority)
                ->get()->toArray();

            
            }elseif(!empty($department)){
            
                $all_employees = Employees::where('employee_isactive',$employement_status)
                ->where('employee_department',$department)
                ->get(['employee_id','employee_code','employee_name','employee_offdays'])->toArray();
            }
            elseif(!empty($employee_list)){
                $all_employees = Employees::where('employee_isactive', $employement_status)
                ->whereIn('employee_id',$employee_list)
                ->get(['employee_id','employee_code','employee_name','employee_offdays'])->toArray();
            }
            else{

                $all_employees = Employees::where('employee_isactive',$employement_status)
                ->get(['employee_id','employee_code','employee_name','employee_offdays'])->toArray();

            }
        
            if(!empty($all_employees)){

                // one query for the whole month instead of one per employee
                $attendance_by_employee = [];
                $listed_employee_ids = array_values(array_intersect(array_column($all_employees, 'employee_id'), (array) $employee_list));
                foreach(array_chunk($listed_employee_ids, 1000) as $employee_ids_chunk){
                    $month_attendance = Attendance::whereIn('att_employee_id',$employee_ids_chunk)->where('att_for_month',$month)->where('att_for_year',$year)->orderBy('attendance_id')->get()->toArray();
                    foreach($month_attendance as $attendance_row){
                        $attendance_by_employee[$attendance_row['att_employee_id']][] = $attendance_row;
                    }
                }

                foreach($all_employees as $employee){

                    if(in_array($employee['employee_id'],$employee_list)){

                        $all_attendances = $attendance_by_employee[$employee['employee_id']] ?? [];

                        $atts = array('attendance' => $all_attendances);
                        $result[] = array_merge( $employee , $atts );
                    }

                }
            
                return $result;

            }else{

                return array();

            }
        }catch(\Exception $e){
            $this->create_error_log('All  Attendance Employees Filter Function', $e);
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

            ActivityTracker::track_activity('All  Attendance Employees Filter Function');
    
        }
        
    }

    protected function update_employee_attendance(Request $attendance_data){
      
		try{
            $login_user = $this->get_login_user_detail();

            $validator = Validator::make($attendance_data->all(), [
                'attendance_date' => 'required|date_format:Y-m-d|before:tomorrow',
                'employee_code' => 'required',
                'attendance_status' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                
                $attendance_access = AttendanceAccess::where('access_employee_id',session('user_session'))->get('attendance_access')->toArray();
                if(!empty($attendance_access) && $attendance_access[0]['attendance_access'] == 1){
                    $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$attendance_data->input('employee_code'));
                            
                    if($request_approve_permission != null){
                        return response()->json([
                            'msg' => $request_approve_permission['msg'],
                            'status' => 'FALSE'
                        ]);
                    }

                    $payroll_exists = $this->check_if_payroll_exist($attendance_data->input('attendance_date'),$attendance_data->input('employee_code'),$custom_message = 'msg');

                    if($payroll_exists['status'] == TRUE){
                        return response()->json([
                            'msg' => $payroll_exists['msg'],
                            'status' => 'FALSE'
                        ]);
                    }

                    $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($attendance_data->input('attendance_date'));

                    if($check_attendance_sheet_availability['status'] == FALSE){
                        return response()->json([
                            'msg' => $check_attendance_sheet_availability['msg'],
                            'status' => 'FALSE'
                        ]);
                    }


                    $get_employee_decoded = Employees::where('employee_id',$attendance_data->input('employee_code'))->get(['employee_name','employee_doj'])->toArray();
                    $att_applying_date  = strtotime($attendance_data->input('attendance_date'));
                    $doj  = strtotime($get_employee_decoded[0]['employee_doj']);

                
                    if($att_applying_date >=  $doj){
                        $day = date("d", strtotime($attendance_data->input('attendance_date')));

                        $month = date("m", strtotime($attendance_data->input('attendance_date')));
                        
                        $year = date("Y", strtotime($attendance_data->input('attendance_date')));
                
                        $get_attendance = Attendance::where('att_employee_id',$attendance_data->input('employee_code'))->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['attendance_id'])->toArray();
                
                        $employee_shift_start = $this->get_employee_today_shift($attendance_data->input('employee_code'),"in",$attendance_data->input('attendance_date'));
                        $employees_shift_end_time = $this->get_employee_today_shift($attendance_data->input('employee_code'),"out",$attendance_data->input('attendance_date'));

                        if($attendance_data->input('attendance_status') == "Half day"){
                        
                            $shift_time =   CustomHelper::calculate_shift_timings_shift_management($employee_shift_start,$employees_shift_end_time);
                            
                            $employees_shift_end_time = $this->employee_half_day_shift_time($employee_shift_start, $shift_time);
            
                        }
                        if(empty($get_attendance)){
                
                            $update_attendance = new Attendance();
                            $update_attendance->att_employee_id = $attendance_data->input('employee_code');
                            $update_attendance->att_check_in_time =$employee_shift_start;
                            $update_attendance->att_check_out_time = $employees_shift_end_time;
                            $update_attendance->att_check_in_status = 1;
                            $update_attendance->att_check_out_status = 1;
                            $update_attendance->att_shift_start_time = $employee_shift_start;
                            $update_attendance->att_shift_end_time = $employees_shift_end_time;
                            $update_attendance->att_check_overall_status = $attendance_data->input('attendance_status');
                            $update_attendance->att_for_date = $day;
                            $update_attendance->att_for_month = $month;
                            $update_attendance->att_for_year = $year;
                            $update_attendance->att_check_in_source = 3;
                            $update_attendance->att_check_out_source = 3;
                            $process = $update_attendance->save();
                            if($process == 1){

                                $this->send_notification('Attendance Update','HR marked your attendance as '.$attendance_data->input('attendance_status').' for '. $day . '-'. $month .'-'. $year .'. Please click here to view.','my-attendance',$attendance_data->input('employee_code'),NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($attendance_data->input('employee_code'), 'Attendance Update','HR marked your attendance as '.$attendance_data->input('attendance_status').' for '. $day . '-'. $month .'-'. $year.'.');
                                
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Attendance marked successfully. Please wait",
                                ]);
                            
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Something went wrong. Please try again.",
                                ]);
                            }
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>'Attendance already marked. Please edit from table.',
                            ]);
                        }
                    }  return response()->json([
                        'status' => 'FALSE',
                        'msg' =>  "You can't update attendance of ".$get_employee_decoded[0]['employee_name']." before their joining date",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You don't have access to update attendance.",
                    
                    ]);
                } 
            }   
                
        }catch(\Exception $e){

            $this->create_error_log('Mark Employee Attendance', $e);
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

            ActivityTracker::track_activity('Mark Employee Attendance');
        
        }
		
    }

    protected function update_marked_attendance(Request $attendance_data){
       
		try{
            $login_user = $this->get_login_user_detail();
            $validator = Validator::make($attendance_data->all(), [
                'check_in_time' => 'required|date_format:H:i:s',
                'check_out_time' => 'required|date_format:H:i:s',
                'attendance_status' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                $attendance_access = AttendanceAccess::where('access_employee_id',session('user_session'))->get('attendance_access')->toArray();
                if(!empty($attendance_access) && $attendance_access[0]['attendance_access'] == 1){
                    $update_attendance = Attendance::find($attendance_data->input('attenadnce_id'));
                    if(!empty($update_attendance)){
                        $attendance_date = $update_attendance->att_for_date . '-'. $update_attendance->att_for_month .'-'. $update_attendance->att_for_year;
                
                        $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$update_attendance->att_employee_id);
                                
                        if($request_approve_permission != null){
                            return response()->json([
                                'msg' => $request_approve_permission['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
                    
                        $payroll_exists = $this->check_if_payroll_exist($attendance_date ,$update_attendance->att_employee_id,$custom_message = 'msg');
        
                        if($payroll_exists['status'] == TRUE){
                            return response()->json([
                                'msg' => $payroll_exists['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($attendance_date);

                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }
                        
                        $get_employee_decoded = Employees::where('employee_id',$update_attendance->att_employee_id)->get(['employee_name'])->toArray();
                        $att_check_in_status = 1;
                        $att_check_out_status = 1;
                        $att_check_in_time =  $attendance_data->input('check_in_time');
                        $att_check_out_time = $attendance_data->input('check_out_time');
                        if(!in_array($attendance_data->input('attendance_status'),['Present','Late Coming','Early out','Absent','Half day'])){
                            $att_check_in_status = 0;
                            $att_check_out_status = 0;
                            $att_check_in_time = '00:00:00';
                            $att_check_out_time = '00:00:00';
                            if($attendance_data->input('attendance_status') == "Annual Leave"){
                                $employement_status_check = $this->check_if_employee_permanent($attendance_date ,$update_attendance->att_employee_id,$custom_message = 'msg');
                                if(!$employement_status_check['status']){
                                    return response()->json([
                                        'msg' => $employement_status_check['msg'],
                                        'status' => 'FALSE'
                                    ]);
                                }
                            }
                            
                            $reamining_leaves;
                            $no_of_days = 1;
        
                            $leave_name = LeaveManagement::where('leave_name',$attendance_data->input('attendance_status'))->get(['leave_name','leave_manage_id','leave_visibilty'])->toArray();
                        
                            if(!empty($leave_name)){
        
                                $leave_alloted = Leave::where('leaves_employee_id',$update_attendance->att_employee_id)->where('leave_type_id',$leave_name[0]['leave_manage_id'])->get(['leave_allotement'])->toArray();
                                    
        
                                if($leave_name[0]['leave_visibilty'] == 1){
                                    
                                    if(empty($leave_alloted)){
                                        $leave_alloted[0]['leave_allotement'] = 0;
                                    }
                    
                                
                                    $leave_request_applied = LeaveRequest::where('leave_applied_by',(string) $update_attendance->att_employee_id)
                                        ->where('leave_applied_type', $leave_name[0]['leave_manage_id'])
                                        ->where('leave_approved_by_hr',1)
                                        ->count();
                                        
                                
                                    $get_availed_leaves = Attendance::where('att_employee_id',$update_attendance->att_employee_id)->where('att_check_overall_status', $attendance_data->input('attendance_status'))->count() + LeaveDeduction::deducted_count($update_attendance->att_employee_id, $leave_name[0]['leave_manage_id']);
                            
                                    $total_leaves = $get_availed_leaves + $leave_request_applied;
                                
                                    $reamining_leaves = $leave_alloted[0]['leave_allotement'] - $total_leaves;
                                
                                    if(!empty($leave_alloted) && $reamining_leaves < $no_of_days){
                                        if($reamining_leaves < 0){
                                            $reamining_leaves = 0;
                                        }
                                        return response()->json([
                                            'status' => 'FALSE',
                                            'msg' => $get_employee_decoded[0]['employee_name']." doesn't have remaining " . $attendance_data->input('attendance_status'). '(s) to avail. Balance is ' . $reamining_leaves,
            
                                        ]);
                    
                                    }
                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => 'You can\'t apply '.$attendance_data->input('attendance_status').' at the moment.'
                                    ]);
                                }
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => 'You can\'t apply this leave at the moment.',
                                ]);
                            }
                        }
        
                        $update_attendance->att_check_in_time =   $att_check_in_time;
                        $update_attendance->att_check_out_time = $att_check_out_time;
                        $update_attendance->att_check_overall_status = $attendance_data->input('attendance_status');
                        $update_attendance->att_check_in_status = $att_check_in_status;
                        $update_attendance->att_check_out_status = $att_check_out_status;
                        $update_attendance->att_check_in_source = 3;
                        $update_attendance->att_check_out_source = 3;
                        $process = $update_attendance->save();
                        if($process == 1){
                
                            $this->send_notification('Attendance Update','HR updated your attendance as '.$attendance_data->input('attendance_status').' for '. $update_attendance->att_for_date . '-'. $update_attendance->att_for_month .'-'. $update_attendance->att_for_year .'. Please click here to view.','my-attendance',$update_attendance->att_employee_id,NULL,NULL,'no');

                            $this->send_push_notification_mobile_app($update_attendance['att_employee_id'], 'Attendance Update','HR updated your attendance as '.$attendance_data->input('attendance_status').' for '. $update_attendance->att_for_date . '-'. $update_attendance->att_for_month .'-'. $update_attendance->att_for_year .'.');

                
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Attendance updated successfully. Please wait",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Something went wrong. Please try again."
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Attendnace record not found.",
                        
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You don't have access to update attendance.",
                    
                    ]);
                } 
            }
        }catch(\Exception $e){

            $this->create_error_log('Update Employee Marked Attendance', $e);
            
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

            ActivityTracker::track_activity('Update Employee Marked Attendance');
		
        }
		
       
    }

    protected function update_marked_attendance_absent(Request $attendance_data){
       
        try{
           
            $login_user = $this->get_login_user_detail();

            $request_approve_permission = $this->hr_employees_activity_blocker($login_user[0]['employee_id'],$attendance_data->input('att_employee_id'));
                        
            if($request_approve_permission != null){
                return response()->json([
                    'msg' => $request_approve_permission['msg'],
                    'status' => 'FALSE'
                ]);
            }
          
            $payroll_exists = $this->check_if_payroll_exist($attendance_data->input('att_for_year').'-'.$attendance_data->input('att_for_month').'-'.$attendance_data->input('att_for_date') , $attendance_data->input('att_employee_id'),$custom_message = 'msg');

            if($payroll_exists['status'] == TRUE){
                return response()->json([
                    'msg' => $payroll_exists['msg'],
                    'status' => 'FALSE'
                ]);
            }

            $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($attendance_data->input('att_for_year').'-'.$attendance_data->input('att_for_month').'-'.$attendance_data->input('att_for_date'));

            if($check_attendance_sheet_availability['status'] == FALSE){
                return response()->json([
                    'msg' => $check_attendance_sheet_availability['msg'],
                    'status' => 'FALSE'
                ]);
            }
            $attendance_access = AttendanceAccess::where('access_employee_id',session('user_session'))->get('attendance_access')->toArray();
            if(!empty($attendance_access) && $attendance_access[0]['attendance_access'] == 1){
                $get_employee_decoded = Employees::where('employee_id',$attendance_data->input('att_employee_id'))->get(['employee_name','employee_doj'])->toArray();
                $att_applying_date  = strtotime($attendance_data->input('att_for_year').'-'.$attendance_data->input('att_for_month').'-'.$attendance_data->input('att_for_date'));
                $doj  = strtotime($get_employee_decoded[0]['employee_doj']);
                if($att_applying_date >=  $doj){

                    $att_for_date = $attendance_data->input('att_for_date');

                    if($att_for_date <= 9){
                        $att_for_date = '0'.$attendance_data->input('att_for_date');
                    }
                    
                    if($attendance_data->input('att_for_year').'-'.$attendance_data->input('att_for_month').'-'. $att_for_date <=  date('Y-m-d')){

                        $att_check_in_status = 1;
                        $att_check_out_status = 1;

                        $att_date  = $attendance_data->input('att_for_year').'-'.$attendance_data->input('att_for_month').'-'.$attendance_data->input('att_for_date');
                        
                        $hcm_setting = HrmSetting::get(['check_in_flexibility_time','check_out_flexibility_time'])->toArray();
                        $check_in_grace_time =  $hcm_setting[0]['check_in_flexibility_time'] + 1;
                        $check_out_grace_time =  $hcm_setting[0]['check_out_flexibility_time'] + 1;

                        $employee_shift_start = $this->get_employee_today_shift($attendance_data->input('att_employee_id'),"in",$att_date);
                        $employees_shift_start_time =  $employee_shift_start;
                        
                        $employee_shift_end = $this->get_employee_today_shift($attendance_data->input('att_employee_id'),"out",$att_date);
                        $employees_shift_end_time =  $employee_shift_end;
                        
                        if(!in_array($attendance_data->input('attendance_status'),['Present','Late Coming','Early out','Absent','Half day'])){

                            if($attendance_data->input('attendance_status') == "Annual Leave"){
                                $employement_status_check = $this->check_if_employee_permanent($attendance_data->input('att_for_year').'-'.$attendance_data->input('att_for_month').'-'.$attendance_data->input('att_for_date') , $attendance_data->input('att_employee_id'),$custom_message = 'msg');
                                if(!$employement_status_check['status']){
                                    return response()->json([
                                        'msg' => $employement_status_check['msg'],
                                        'status' => 'FALSE'
                                    ]);
                                }
                            }

                            $att_check_in_status = 0;
                            $att_check_out_status = 0;
                            $reamining_leaves;
                            $no_of_days = 1;

                            $leave_name = LeaveManagement::where('leave_name',$attendance_data->input('attendance_status'))->get(['leave_name','leave_manage_id','leave_visibilty'])->toArray();
                        
                            if(!empty($leave_name)){
                                $employees_shift_start_time = '00:00:00';
                                $employees_shift_end_time = '00:00:00';
                                $leave_alloted = Leave::where('leaves_employee_id',$attendance_data->input('att_employee_id'))->where('leave_type_id',$leave_name[0]['leave_manage_id'])->get(['leave_allotement'])->toArray();
                                
                                if($leave_name[0]['leave_visibilty'] == 1){
                                    
                                    if(empty($leave_alloted)){
                                        $leave_alloted[0]['leave_allotement'] = 0;
                                    }
                    
                                    $leave_request_applied = LeaveRequest::where('leave_applied_by',(string) $attendance_data->input('att_employee_id'))
                                        ->where('leave_applied_type',$leave_name[0]['leave_manage_id'])
                                        ->where('leave_approved_by_hr',1)
                                        ->count();
                                        
                                
                                    $get_availed_leaves = Attendance::where('att_employee_id',$attendance_data->input('att_employee_id'))->where('att_check_overall_status', $leave_name[0]['leave_name'])->count() + LeaveDeduction::deducted_count($attendance_data->input('att_employee_id'), $leave_name[0]['leave_manage_id']);
                            
                                    $total_leaves = $get_availed_leaves + $leave_request_applied;
                                
                                    $reamining_leaves = $leave_alloted[0]['leave_allotement'] - $total_leaves;
                                
                                    if(!empty($leave_alloted) && $reamining_leaves < $no_of_days){
                                        if($reamining_leaves < 0){
                                            $reamining_leaves = 0;
                                        }
                                        return response()->json([
                                            'status' => 'FALSE',
                                            'msg' => $get_employee_decoded[0]['employee_name']." doesn't have remaining " . $leave_name[0]['leave_name']. '(s) to avail. Balance is ' . $reamining_leaves,
            
                                        ]);
                    
                                    }
                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => 'You can\'t apply '.$attendance_data->input('attendance_status').' at the moment.'
                                    ]);
                                }
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => 'You can\'t apply this leave at the moment.',
                                ]);
                            }
                        }

                        if($attendance_data->attendance_status == "Late Coming"){
                        
                            $add_time = strtotime('2022-11-07'. $employee_shift_start.'+'.$check_in_grace_time.'minutes');
                        
                            $employees_shift_start_time = date('H:i:s', $add_time);
                        
                        }elseif($attendance_data->attendance_status == "Half day"){
                            
                            $shift_time =   CustomHelper::calculate_shift_timings_shift_management($employee_shift_start,$employee_shift_end);
                            
                            $employees_shift_end_time = $this->employee_half_day_shift_time($employee_shift_start,$shift_time);

                        }elseif($attendance_data->attendance_status == "Early out"){
                            
                            $subtract_time = strtotime('2022-11-07'. $employee_shift_end.'-'.$check_out_grace_time.'minutes');
                        
                            $employees_shift_end_time = date('H:i:s', $subtract_time);
                        
                        }
                        elseif($attendance_data->attendance_status == "Absent"){
                            
                            $employees_shift_start_time = '00:00:00';
                        
                            $employees_shift_end_time = '00:00:00';
                        
                        }

                        $check_if_attendance_exists = Attendance::where('att_employee_id', $attendance_data->input('att_employee_id'))->where('att_for_date', $att_for_date)->where('att_for_month', $attendance_data->input('att_for_month'))->where('att_for_year', $attendance_data->input('att_for_year'))->get(['attendance_id'])->toArray();
                        
                        if(!empty($check_if_attendance_exists)){
                            $update_attendance = Attendance::find($check_if_attendance_exists[0]['attendance_id']);
                            $update_attendance->att_check_in_time = $employees_shift_start_time;
                            $update_attendance->att_check_out_time =  $employees_shift_end_time;
                            $update_attendance->att_check_overall_status = $attendance_data->input('attendance_status');
                            $update_attendance->att_shift_start_time = $employee_shift_start;
                            $update_attendance->att_shift_end_time = $employee_shift_end;
                            $update_attendance->att_check_in_source = 3;
                            $update_attendance->att_check_out_source = 3;
                            $update_attendance->att_check_in_status = $att_check_in_status;
                            $update_attendance->att_check_out_status = $att_check_out_status;
                            $process = $update_attendance->save();
                        }else{
                            $add_attendance = new Attendance();
                            $add_attendance->att_check_in_time = $employees_shift_start_time;
                            $add_attendance->att_check_out_time =  $employees_shift_end_time;
                            $add_attendance->att_check_overall_status =$attendance_data->input('attendance_status');
                            $add_attendance->att_employee_id = $attendance_data->input('att_employee_id');
                            $add_attendance->att_for_date = $att_for_date;
                            $add_attendance->att_shift_start_time = $employee_shift_start;
                            $add_attendance->att_shift_end_time = $employee_shift_end;
                            $add_attendance->att_for_month = $attendance_data->input('att_for_month');
                            $add_attendance->att_for_year = $attendance_data->input('att_for_year');
                            $add_attendance->att_check_in_source = 3;
                            $add_attendance->att_check_out_source = 3;
                            $add_attendance->att_check_in_status = $att_check_in_status;
                            $add_attendance->att_check_out_status = $att_check_out_status; 
                            $process = $add_attendance->save();
                        }
                        
                        if($process == 1){
                
                            $this->send_notification('Attendance Update','HR updated your attendance as '.$attendance_data->input('attendance_status').' for '. $attendance_data->input('att_for_date') . '-'. $attendance_data->input('att_for_month') .'-'. $attendance_data->input('att_for_year') .'. Please click here to view.','my-attendance',$attendance_data->input('att_employee_id'),NULL,NULL,'no');

                            $this->send_push_notification_mobile_app($attendance_data->input('att_employee_id'), 'Attendance Update','HR updated your attendance as '.$attendance_data->input('attendance_status').' for '. $attendance_data->input('att_for_date') . '-'. $attendance_data->input('att_for_month') .'-'. $attendance_data->input('att_for_year') .'.');
                
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Attendance updated successfully. Please wait",
                            ]);

                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Something went wrong. Please try again."
                            ]);
                        }
                    }else{
                                
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' =>  "You can't update attendance of future date",
                        ]);
                    }
                }else{
                                
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>  "You can't update attendance of ".$get_employee_decoded[0]['employee_name']." before their joining date",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "You don't have access to update attendance.",
                
                ]);
            } 
        
        }catch(\Exception $e){

            $this->create_error_log('Update Absentee Attendance', $e);
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

            ActivityTracker::track_activity('Update Absentee Attendance');
		
        }		
    }

    protected function get_department_managers(Request $request){

        try{

            $get_all_dept_employees = Employees::where('employee_department', $request->input('department'))->where('employee_isactive', '1')->get(['employee_senior_manager', 'employee_junior_manager'])->toArray();
            if(!empty($get_all_dept_employees)){
                
                $ra_employees = array();
                
                foreach($get_all_dept_employees as $employee){
                    if(!in_array($employee['employee_senior_manager'], $ra_employees)){
                        array_push($ra_employees, $employee['employee_senior_manager']);
                    }
                    if(!in_array($employee['employee_junior_manager'], $ra_employees)){
                        array_push($ra_employees, $employee['employee_junior_manager']);
                    } 
                }
                

                $get_dept_ra = Employees::whereIn('employee_id', $ra_employees)->get(['employee_id', 'employee_name'])->toArray();
                
                return response()->json([
                    'data' => $get_dept_ra
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Get Departments Manager', $e);
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

            ActivityTracker::track_activity('Get Departments Manager');
    
        }

	}

    protected function get_employee_daily_attendance(Request $request){

        try{
            
            $title = Self::TITLE_GLOBAL3;
      
            $login_user = $this->get_login_user_detail();

            $where_clause = '1=1';

            if(!empty($request->department_id) && $request->department_id != 'All'){
                $where_clause .= ' AND employee_department = '.\DB::getPdo()->quote((string) $request->department_id);
            }

            if(!empty($request->business_unit) && $request->business_unit != 'All'){
                $where_clause .= " AND business_unit = ".\DB::getPdo()->quote((string) $request->business_unit)."";
            }

            if(!empty($request->employee_group) && $request->employee_group != 'All'){

                $where_clause .= " and employee_group= ".\DB::getPdo()->quote((string) $request->employee_group)."";

            }
            
            if(empty($request->is_active)){
                $where_clause .= ' AND employee_isactive = '."1".'';
            }else{
                if(!empty($request->is_active) && $request->is_active == 1){
                    $where_clause .= ' AND employee_isactive = '."1".''; 
                }
                elseif(!empty($request->is_active) && $request->is_active == 2){
                    $where_clause .= ' AND employee_isactive = '."0".''; 
                }
            }

            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
            array_push($manage_leaves,'RPL');
            if(!empty($request->input('d'))){
                
                $date = date('d', strtotime($request->input('d')));
                $month = date('m', strtotime($request->input('d')));
                $year = date('Y', strtotime($request->input('d')));

                $full_date = $year . '-' . $month . '-' . $date;
        
                $t_employee_leaves = Employees::join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where(['att_for_date'=>$date,'att_for_month'=>$month,'att_for_year'=>$year])->whereRaw($where_clause)->where(function($query) use($manage_leaves){
                    $query->whereIn('att_check_overall_status' ,$manage_leaves)
                    ->orWhere('att_check_overall_status','RPL');
                })->count();
               
                $attendance = DB::select("SELECT STRAIGHT_JOIN e.employee_code, -- employees first: the optimizer otherwise scans designations per row
                
                e.employee_name,
                e.employee_new_code,
                e.business_unit,
                e.employee_offdays,
                dp.department_name,
                ds.designation_name,
                a.att_check_in_status,
                a.attendance_id,
                CASE WHEN a.att_comp_date IS NULL THEN '$full_date' ELSE a.att_comp_date END as attendance_date,
                DAYNAME(CASE WHEN a.att_comp_date IS NULL THEN '$full_date' ELSE a.att_comp_date END) AS day_name,
                CASE WHEN a.att_check_in_time IS NULL THEN '-' ELSE a.att_check_in_time END as att_check_in_time,
                CASE WHEN a.att_check_out_time IS NULL OR a.att_check_out_status = 0
                THEN '-' ELSE a.att_check_out_time
                END as att_check_out_time,
                a.att_check_out_status,
                a.att_created_at,
                a.att_updated_at,
                CASE
                WHEN a.att_check_overall_status IS NULL AND e.employee_offdays LIKE
                DAYNAME(CASE WHEN a.att_comp_date IS NULL THEN '$full_date' ELSE a.att_comp_date END) THEN 'Offdays'
                WHEN a.att_check_overall_status IS NULL THEN 'Absent'
                ELSE a.att_check_overall_status

                END as att_check_overall_status

                FROM tbl_employees e
                INNER JOIN tbl_departments dp ON e.employee_department = dp.department_id
                INNER JOIN tbl_designations ds ON e.employee_designation = ds.designation_id
                LEFT JOIN (SELECT * FROM tbl_attendance WHERE att_for_year ".CustomHelper::sql_attendance_eq($year)." AND att_for_month ".CustomHelper::sql_attendance_eq($month)." AND att_for_date ".CustomHelper::sql_attendance_eq($date).") a
                ON e.employee_id = a.att_employee_id
                WHERE $where_clause ORDER BY e.employee_code");
                $attendance = json_decode(json_encode($attendance), TRUE);

            }else{

                $date = date('d');
                $month = date('m');
                $year = date('Y');

                $full_date = $year . '-' . $month . '-' . $date;

                $t_employee_leaves = Employees::join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where(['att_for_date'=>date('d'),'att_for_month'=>date('m'),'att_for_year'=>date('Y')])->whereRaw($where_clause)->whereIn('att_check_overall_status' , $manage_leaves)->where(function($query) use($manage_leaves){
                    $query->whereIn('att_check_overall_status' ,$manage_leaves)
                    ->orWhere('att_check_overall_status','RPL');
                })->count();

                $attendance = DB::select("SELECT STRAIGHT_JOIN e.employee_code, -- employees first: the optimizer otherwise scans designations per row
                
                e.employee_name,
                e.employee_new_code,
                e.business_unit,
                e.employee_offdays,
                dp.department_name,
                ds.designation_name,
                a.att_check_in_status,
                a.attendance_id,
                CASE WHEN a.att_comp_date IS NULL THEN '$full_date' ELSE a.att_comp_date END as attendance_date,
                DAYNAME(CASE WHEN a.att_comp_date IS NULL THEN '$full_date' ELSE a.att_comp_date END) AS day_name,
                CASE WHEN a.att_check_in_time IS NULL THEN '-' ELSE a.att_check_in_time END as att_check_in_time,
                CASE WHEN a.att_check_out_time IS NULL OR a.att_check_out_status = 0
                THEN '-' ELSE a.att_check_out_time
                END as att_check_out_time,
                a.att_check_out_status,
                a.att_created_at,
                a.att_updated_at,
                CASE
                WHEN a.att_check_overall_status IS NULL AND e.employee_offdays LIKE
                DAYNAME(CASE WHEN a.att_comp_date IS NULL THEN '$full_date' ELSE a.att_comp_date END) THEN 'Offdays'
                WHEN a.att_check_overall_status IS NULL THEN 'Absent'
                ELSE a.att_check_overall_status

                END as att_check_overall_status

                FROM tbl_employees e
                INNER JOIN tbl_departments dp ON e.employee_department = dp.department_id
                INNER JOIN tbl_designations ds ON e.employee_designation = ds.designation_id
                LEFT JOIN (SELECT * FROM tbl_attendance WHERE att_for_year ".CustomHelper::sql_attendance_eq($year)." AND att_for_month ".CustomHelper::sql_attendance_eq($month)." AND att_for_date ".CustomHelper::sql_attendance_eq($date).") a
                ON e.employee_id = a.att_employee_id
                WHERE $where_clause ORDER BY e.employee_code");
                $attendance = json_decode(json_encode($attendance), TRUE);

            }
            
            $leave_name = implode(',',$manage_leaves);

            $departments = Department::get(['department_id','department_name'])->toArray();

            $employee_groups = EmployeeGroup::get(['employee_group_id','group_name'])->toArray();
           
            // card totals over the whole day, then the card / search filter and one page of rows for the table
            $status_counts = [
                'present' => 0, 'late_early' => 0, 'half_day' => 0, 'absent' => 0, 'off_days' => 0,
            ];
            foreach($attendance as $row){
                $row_status = $row['att_check_overall_status'];
                if($row_status == 'Present'){ $status_counts['present']++; }
                elseif(in_array($row_status, ['Late Coming','Early out'])){ $status_counts['late_early']++; }
                elseif($row_status == 'Half day'){ $status_counts['half_day']++; }
                elseif($row_status == 'Absent'){ $status_counts['absent']++; }
                elseif($row_status == 'Offdays' || substr($row_status, 0, 7) == 'Holiday'){ $status_counts['off_days']++; }
            }

            $status_filter = array_filter(array_map('trim', explode(',', (string) $request->input('status'))));
            if(!empty($status_filter)){
                $status_filter = array_map('strtolower', $status_filter);
                $attendance = array_values(array_filter($attendance, function($row) use($status_filter){
                    $row_status = strtolower($row['att_check_overall_status']);
                    if($row_status == 'present' && in_array('on time', $status_filter)){ return true; }
                    if($row_status == 'offdays' && in_array('off day', $status_filter)){ return true; }
                    if(substr($row_status, 0, 7) == 'holiday' && in_array('holiday', $status_filter)){ return true; }
                    return in_array($row_status, $status_filter);
                }));
            }

            $search = trim((string) $request->input('search'));
            if($search !== ''){
                $attendance = array_values(array_filter($attendance, function($row) use($search){
                    return stripos($row['employee_name'], $search) !== false || stripos((string) $row['employee_code'], $search) !== false
                        || stripos((string) $row['department_name'], $search) !== false || stripos((string) $row['designation_name'], $search) !== false;
                }));
            }

            $per_page = CustomHelper::per_page();
            $current_page = LengthAwarePaginator::resolveCurrentPage();
            $pagination = new LengthAwarePaginator(array_slice($attendance, ($current_page - 1) * $per_page, $per_page), count($attendance), $per_page, $current_page, ['path' => $request->url(), 'query' => $request->query()]);
            $attendance = $pagination->items();

            $send_data = compact('title','login_user','attendance','t_employee_leaves','manage_leaves','leave_name','departments','employee_groups','status_counts','pagination');

          
            return view('attendance.daily-attendance-report2',$send_data);
           
        }catch(\Exception $e){
            $this->create_error_log('Daily Attendance Report All Employee ', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('Daily Attendance Report All Employee');
        }   
    }

    protected function get_employee_daily_attendance2(Request $request){

        try{
            
            $title = Self::TITLE_GLOBAL3;
      
            $login_user = $this->get_login_user_detail();

            // $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            $where_clause = '1=1';
            // if(!empty($request->employee_location)){
            //     $where_clause .= " AND employee_location = ".\DB::getPdo()->quote((string) $request->employee_location)."";
            // }

            if(!empty($request->department_id) && $request->department_id != 'All'){
                $where_clause .= ' AND employee_department = '.\DB::getPdo()->quote((string) $request->department_id);
            }

            if(!empty($request->business_unit) && $request->business_unit != 'All'){
                $where_clause .= " AND business_unit = ".\DB::getPdo()->quote((string) $request->business_unit)."";
            }
            
            if(empty($request->is_active)){
                $where_clause .= ' AND employee_isactive = '."1".'';
            }else{
                if(!empty($request->is_active) && $request->is_active == 1){
                    $where_clause .= ' AND employee_isactive = '."1".''; 
                }
                elseif(!empty($request->is_active) && $request->is_active == 2){
                    $where_clause .= ' AND employee_isactive = '."0".''; 
                }
            }
            

            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
            array_push($manage_leaves,'RPL');
            if(!empty($request->input('d'))){
                
                $date = date('d', strtotime($request->input('d')));
                $month = date('m', strtotime($request->input('d')));
                $year = date('Y', strtotime($request->input('d')));

        
                $t_employee_leaves = Employees::join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where(['att_for_date'=>$date,'att_for_month'=>$month,'att_for_year'=>$year])->whereRaw($where_clause)->where(function($query) use($manage_leaves){
                    $query->whereIn('att_check_overall_status' ,$manage_leaves)
                    ->orWhere('att_check_overall_status','RPL');
                })->count();
               
                $attendance =  $this->daily_attendance_all_employee($date,$month,$year,$where_clause);
                
            }else{
                
                $date = date('d');
                $month = date('m');
                $year = date('Y');
                

                $t_employee_leaves = Employees::join('tbl_attendance','tbl_employees.employee_id','tbl_attendance.att_employee_id')->where(['att_for_date'=>date('d'),'att_for_month'=>date('m'),'att_for_year'=>date('Y')])->whereRaw($where_clause)->whereIn('att_check_overall_status' , $manage_leaves)->where(function($query) use($manage_leaves){
                    $query->whereIn('att_check_overall_status' ,$manage_leaves)
                    ->orWhere('att_check_overall_status','RPL');
                })->count();
                $attendance =  $this->daily_attendance_all_employee($date,$month,$year,$where_clause);

            }
            
            $leave_name = implode(',',$manage_leaves);

            $departments = Department::get()->toArray();
           
            $send_data = compact('title','login_user','attendance','t_employee_leaves','manage_leaves','leave_name','departments');

          
            return view('attendance.daily-attendance-report',$send_data);
           
        }catch(\Exception $e){

            $this->create_error_log('Daily Attendance Report All Employee ', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Daily Attendance Report All Employee');
          
        
        }   
    }

    protected function download_daily_attendance_all_employee(Request $request){
        try{
            
            if(!empty($request->input('d'))){
                $date = date('d', strtotime($request->input('d')));
                $month = date('m', strtotime($request->input('d')));
                $year = date('Y', strtotime($request->input('d')));
            }else{
                $date = date('d');
                $month = date('m');
                $year = date('Y');
            }

            $all_employees = Employees::
            leftJoin('tbl_attendance', function($join) use ($date , $month , $year) {
                $join->on('tbl_employees.employee_id', '=', 'tbl_attendance.att_employee_id');
                $join->where('tbl_attendance.att_for_date', $date);
                $join->where('tbl_attendance.att_for_month', $month);
                $join->where('tbl_attendance.att_for_year', $year);
            })
            ->leftjoin('tbl_departments', 'tbl_employees.employee_department', '=', 'tbl_departments.department_id')
            ->leftjoin('tbl_designations', 'tbl_employees.employee_designation', '=', 'tbl_designations.designation_id')
            ->select([
                'employee_code as Employee Code',
                'employee_name as Employee Name',
                'employee_new_code as Employee New Code',
                'employee_offdays as Employee Offdays',
                'department_name as Department Name',
                'designation_name as Designation Name',
                'business_unit as Business Unit',
                DB::raw("CASE WHEN tbl_attendance.att_employee_id IS NULL THEN 'absent' ELSE att_check_overall_status END as 'Attendance Status' "),
                DB::raw("CASE WHEN tbl_attendance.att_employee_id IS NULL THEN '-' ELSE att_check_in_time END as 'Checkin Time' "),
                DB::raw("CASE WHEN tbl_attendance.att_employee_id IS NULL THEN '-' ELSE att_check_out_time END as 'Checkout Time' "),
                DB::raw("CASE WHEN tbl_attendance.att_employee_id IS NULL THEN '-' ELSE CONCAT(tbl_attendance.att_for_year, '-', tbl_attendance.att_for_month, '-', tbl_attendance.att_for_date) END as 'Attendance Date' "),
                DB::raw("CASE WHEN tbl_employees.employee_new_code IS NULL THEN '-' ELSE employee_new_code END as 'Employee New Code' "),

            ])
            ->orderBy('Employee Code')
            ->get()
            ->toArray();

            $header_style = (new Style())->setFontBold();

            return (new FastExcel($all_employees))->headerStyle($header_style)->download('Download-Daily-attendance-report.xlsx');

        }catch(\Exception $e){

            $this->create_error_log('download Attendance Report All Employee', $e);
           
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('download Attendance Report All Employee');
          
        }   
    }


    protected function daily_attendance_all_employee($date = NULL , $month = NULL , $year = NULL, $where_clause = "1=1"){
		
        $all_employees = Employees::
        join('tbl_departments','tbl_employees.employee_department','=','tbl_departments.department_id')
        ->join('tbl_designations','tbl_employees.employee_designation','=','tbl_designations.designation_id')
        ->whereRaw($where_clause)
        ->get(['employee_id as Employee ID','employee_code as Employee Code','employee_name as Employee Name','employee_offdays as Employee Offdays','department_name as Department Name','designation_name as Designation Name','business_unit as Business Unit'])->toArray();
		
       
        if(!empty($all_employees)){

            foreach($all_employees as $employee){

                if(!empty($date) && !empty($month) && !empty($year)){
					
					$all_attendances = Attendance::where('att_employee_id',$employee['Employee ID'])->where('att_for_date',$date)->where('att_for_month',$month)->where('att_for_year',$year)->get()->toArray();
                    
                }else{

                    $all_attendances = Attendance::where('att_employee_id',$employee['Employee ID'])->where('att_for_date',date('d'))->where('att_for_month',date('m'))->where('att_for_year',date('Y'))->get()->toArray();

                }

                $atts = array('attendance' => $all_attendances);

                $result[] = array_merge( $employee , $atts );

            }
         
           return $result;

        }else{

           return array();

        }
    }

    protected function attendance_report(Request $request_data){
      
        try{
            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

            if($request_data->month != '' && is_numeric($request_data->month) && $request_data->month >= 01 && $request_data->month <= 12){
                $month = $request_data->month;
            }else{
                $month = date('m');
            }

            if($request_data->year != '' && is_numeric($request_data->year) && $request_data->year  >= 1900 && $request_data->year <= 2050){
                $year = $request_data->year;
                $month_days = cal_days_in_month(CAL_MONTH_GREGORIAN_LONG, $month,$request_data->year);
            }else{
                $year = date('Y');
                $month_days = date('t');
            }

            if($request_data->employee_id != '' && is_numeric($request_data->employee_id)){
                $employee_id = $request_data->employee_id;
            }else{
                $employee_id = session('user_session');
            }

            $dates = $this->get_all_date_from_month($month,$year,$format=Null);
            array_unshift($dates, $dates[0]);
            unset($dates[0]);
            
            $month_count = 1;
            
            $attendances = Attendance::where(['att_employee_id'=>$employee_id,'att_for_month'=>$month,'att_for_year'=>$year])->get()->toArray();

            $employee = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_id',$employee_id)->get(['employee_id','employee_code','employee_name','department_name','designation_name','employee_jobtitle','employee_offdays','employee_shift_flexibilty','employee_senior_manager'])->toArray();

            $employee_senior_manager = Employees::where('employee_id',$employee[0]['employee_senior_manager'])->get('employee_name')->toArray();
           
            $from_date = !empty($request_data->from_date) ? $request_data->from_date : $year.'-'.$month.'-01';
            $to_date = !empty($request_data->to_date) ? $request_data->to_date : date('Y-m-t', strtotime($year.'-'.$month.'-01'));

            $send_data = compact('employee','month','month_days','year','attendances','employee_senior_manager','manage_leaves','dates','month_count','from_date','to_date');

            return view('attendance.attendance-report',$send_data);
		
        }catch(\Exception $e){

            $this->create_error_log('Attendance Report Specific Employee ', $e);
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('Attendance Report Specific Employee');
		  
        }
      
       
    }

    protected function my_attendance_report(Request $request_data){
        try{

            $login_user = $this->get_login_user_detail();
            $month = '';
            $year = '';

            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

            $flag = true;

            if( $request_data->input('from_date') == '' && $request_data->input('to_date') == ''){
                $flag = false;
                $month_count = 1;
            }
            if($request_data->input('from_date') && $request_data->input('to_date')){
                $validator = Validator::make($request_data->all(), [
                    'from_date' => 'required|date_format:Y-m-d|before:to_date',
                    'to_date' => 'required|date_format:Y-m-d|after:from_date',
                ]);

                $validator->after(function ($validator) use ($request_data) {
                    if ($this->if_to_date_greater_than_today($request_data->input('to_date'))) {
                        $validator->errors()->add('to_date', 'The to date must not be greater than today\'s date.');
                    }
                });
            
                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return Redirect::back()->with('error', $error);
                       
                    }
                }else {

                    if($request_data->input('from_date') < $login_user[0]['employee_doj']){

                        return Redirect::back()->with('error', 'You can\'t apply filter before your joining date');
                       
                    }
                    $start_date = new DateTime($request_data->input('from_date'));
                    $end_date = new DateTime($request_data->input('to_date'));

                    $interval =  $start_date->diff(  $end_date);

                    $daysDifference = $interval->days;
                   
                    if($daysDifference < 40){
                        $from_date =  $request_data->input('from_date');
                        $to_date =  $request_data->input('to_date');
                        $month_count = 0;
                        $period = new DatePeriod(
                            new DateTime($request_data->input('from_date')),
                            new DateInterval('P1D'),
                            new DateTime($request_data->input('to_date'))
                        );
                        $month_days = 0;
                        foreach ($period as $key => $value) {
                            $dates[] = $value->format('d-m-Y');
                            $month_days++;
                            
                        }
                        if (!in_array($to_date, $dates)) {
                            $dates[] = date('d-m-Y', strtotime($to_date));
                        }
                        if($month_days == 0){
                            $month_days = date('t', strtotime($request_data->input('from_date')));
                        }

                        $attendances = Attendance::where('att_employee_id',session('user_session'))
                        ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) >= '".$from_date."'")
                        ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) <= '".$to_date."'")
                        ->get()->toArray();

                   }else{

                    return Redirect::back()->with('error', 'Date Range Exceeds 40 Days.Please try again!');

                   }

                }

            }else{

                if(empty($request->from_date) && empty($request->to_date)){
                    if(date('d') >= '26'){
                        $from_date = date('Y-m-') . '25';
                        $to_date = date('Y-m-d');
                    }else{
                        $today = new DateTime();
                        $today->modify('first day of previous month');
                        $today->modify('+25 days');
                        $from_date = $today->format('Y-m-d');
                        $to_date = date('Y-m-d');
                    }
                }

                $month_count = 0;
                $period = new DatePeriod(
                    new DateTime($from_date),
                    new DateInterval('P1D'),
                    new DateTime($to_date)
                );
                $month_days = 0;
                foreach ($period as $key => $value) {
                    $dates[] = $value->format('d-m-Y');
                    $month_days++;

                }
                if (!in_array($to_date, $dates)) {
                    $dates[] = date('d-m-Y', strtotime($to_date));
                }
                if($month_days == 0){
                    $month_days = date('t', strtotime($from_date));
                }

                $attendances = Attendance::where('att_employee_id',session('user_session'))
                ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) >= '".$from_date."'")
                ->whereRaw("DATE(CONCAT(att_for_year, '-', att_for_month, '-', att_for_date)) <= '".$to_date."'")
                ->get()->toArray();


            }
          
            $employee = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_id',session('user_session'))->get(['employee_id','employee_code','employee_name','department_name','designation_name','employee_jobtitle','employee_offdays','employee_shift_flexibilty','employee_senior_manager'])->toArray();
      
            $employee_senior_manager = Employees::where('employee_id',$employee[0]['employee_senior_manager'])->get('employee_name')->toArray();
            
            $send_data = compact('employee','month','month_days','year','attendances','employee_senior_manager','manage_leaves','dates','month_count','from_date','to_date');
            
            return view('attendance.attendance-report',$send_data);
		
        }catch(\Exception $e){

            $this->create_error_log('My Attendance Report Specific Employee ', $e);
            return view('errors.500');
           
        }finally{

            ActivityTracker::track_activity('My Attendance Report Specific Employee');
         
        }
      
       
    }


    protected function download_attendance_report(Request $request_data){
       
        try{
            $login_user = $this->get_login_user_detail();

            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
            
            if($request_data->month != '' && is_numeric($request_data->month) && $request_data->month >= 01 && $request_data->month <= 12){
                $month = $request_data->month;
            }else{
                $month = date('m');
            }

            if($request_data->year != '' && is_numeric($request_data->year) && $request_data->year  >= 1900 && $request_data->year <= 2050){
                $year = $request_data->year;
                $month_days = cal_days_in_month(CAL_MONTH_GREGORIAN_LONG, $month,$request_data->year);
            }else{
                $year = date('Y');
                $month_days = date('t');
            }

            if($request_data->employee_id != '' && is_numeric($request_data->employee_id)){
                $employee_id = $request_data->employee_id;
            }else{
                $employee_id = session('user_session');
            }
            $from_date = '';
            $to_date = '';
            if($request_data->input('from_date') && $request_data->input('to_date')){
                $validator = Validator::make($request_data->all(), [
                    'from_date' => 'required|date_format:Y-m-d|before:to_date',
                    'to_date' => 'required|date_format:Y-m-d|after:from_date',
                ]);

                $validator->after(function ($validator) use ($request_data) {
                    if ($this->if_to_date_greater_than_today($request_data->input('to_date'))) {
                        $validator->errors()->add('to_date', 'The to date must not be greater than today\'s date.');
                    }
                });
            
    
                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return Redirect::back()->with('error', $error);
                       
                    }
                }else {

                    if($request_data->input('from_date') < $login_user[0]['employee_doj']){

                        return Redirect::back()->with('error', 'You can\'t apply filter before your joining date');
                       
                    }

                    $start_date = new DateTime($request_data->input('from_date'));
                    $end_date = new DateTime($request_data->input('to_date'));

                    $interval =  $start_date->diff(  $end_date);

                    $daysDifference = $interval->days;
                   
                    if($daysDifference < 40){
                        $from_date =  $request_data->input('from_date');
                        $to_date =  $request_data->input('to_date');
                        $month_count = 0;
                        $period = new DatePeriod(
                            new DateTime($request_data->input('from_date')),
                            new DateInterval('P1D'),
                            new DateTime($request_data->input('to_date'))
                        );
                        $month_days = 0;
                        foreach ($period as $key => $value) {
                            $dates[] = $value->format('d-m-Y');
                            $month_days++;
                            
                        
                        }
                        if (!in_array($to_date, $dates)) {
                            $dates[] = date('d-m-Y', strtotime($to_date));
                        }
                        if($month_days == 0){
                            $month_days = date('t', strtotime($request_data->input('from_date')));
                        }
                     
                        $attendances = Attendance::where('att_employee_id',$employee_id)->whereRaw("STR_TO_DATE(CONCAT(att_for_year,'-',att_for_month,'-',att_for_date),'%Y-%m-%d') >= '".$from_date ."' and STR_TO_DATE(CONCAT(att_for_year,'-',att_for_month,'-',att_for_date),'%Y-%m-%d') <= '".$to_date."'  ")->get()->toArray();

                   }else{

                    return Redirect::back()->with('error', 'Date Range Exceeds 40 Days.Please try again!');
                    
                   }
                    
                }

            }else{
                $dates = $this->get_all_date_from_month($month,$year,$format=Null);
                array_unshift($dates, $dates[0]);
                unset($dates[0]);
                $month_count = 1;
                $attendances = Attendance::where(['att_employee_id'=>$employee_id,'att_for_month'=>$month,'att_for_year'=>$year])->get()->toArray();
  
            
            }

           
            $employee = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_id',$employee_id)->get(['employee_id','employee_code','employee_name','department_name','designation_name','employee_jobtitle','employee_offdays','employee_shift_flexibilty','employee_senior_manager'])->toArray();

            $employee_senior_manager = Employees::where('employee_id',$employee[0]['employee_senior_manager'])->get('employee_name')->toArray();
            
            $setting = HrmSetting::get(['company_logo','company_name','company_address'])->toArray();
         
            $send_data = compact('employee','month','month_days','year','attendances','setting','employee_senior_manager','manage_leaves','dates','month_count','from_date','to_date');

            $pdf = Pdf::loadView('attendance.download-attendance-report-pdf', $send_data);
            return $pdf->setPaper('a3', 'potrait')->stream($employee[0]['employee_code'].'-'.$employee[0]['employee_name'].'.pdf');
		
        }catch(\Exception $e){

            $this->create_error_log('Downlad Attendance Report Specific Employee ', $e);
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Download Attendance Report Specific Employee');
            
          

        }
       
    }
    
    


    protected function attendance_calender(Request $request_data){
       
        try{
            $title = Self::TITLE_GLOBAL;
      
            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();

            if($request_data->month != '' && is_numeric($request_data->month) && $request_data->month >= 01 && $request_data->month <= 12){
                $month = $request_data->month;
            }else{
                $month = date('m');
            }

            if($request_data->year != '' && is_numeric($request_data->year) && $request_data->year  >= 1900 && $request_data->year <= 2050){
                $year = $request_data->year;
                $month_days = cal_days_in_month(CAL_MONTH_GREGORIAN_LONG, $month,$request_data->year);
            }else{
                $year = date('Y');
                $month_days = date('t');
            }

            if($request_data->employee_id != '' && is_numeric($request_data->employee_id)){
                $employee_id = $request_data->employee_id;
            }else{
                $employee_id = session('user_session');
            }
            // someone else's calendar: HR / Admin, or that employee's managers
            if($employee_id != session('user_session') && !$this->can_respond_for([$employee_id], ['hr'])){
                $employee_id = session('user_session');
            }

            $employee = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_id',$employee_id)->get(['employee_id','employee_code','employee_name','department_name','designation_name','employee_jobtitle','employee_offdays','employee_shift_flexibilty'])->toArray();

            $attendances = Attendance::where(['att_employee_id'=>$employee_id,'att_for_month'=>$month,'att_for_year'=>$year])->get()->toArray();

            $all_employees = []; // the employee filter searches through /employee-search

            $manage_leaves = LeaveManagement::get(['leave_manage_id','leave_name'])->toArray();

            $leave_names = LeaveManagement::pluck('leave_name')->toArray();
            
            
            $send_data = compact('title','login_user','employee','month','month_days','year','attendances','all_employees','manage_leaves','leave_names','dynamic_years');
            
            if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) ||  CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null)){
		        return view('attendance.attendance-calender',$send_data);
            }else{
               return redirect()->back();
            }
		
        }catch(\Exception $e){

            $this->create_error_log('Attendance Calender Specific Employee ', $e);
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Attendance Calender Specific Employee');

        }
      
       
    }

    protected function share_employee_attendance_report(Request $request){

        try{
            $login_user = $this->get_login_user_detail();

            $validator = Validator::make($request->all(), [
                'emp_depart' => 'required',
                'attendance_month' => 'required',
                'attendance_year' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                
                $share_att_report = new ShareAttReport();

                if(!empty($request->input('emp_depart')) && empty($request->input('emp_names'))){

                    $department_id = $request->input('emp_depart');
                    
                    $employees_id = Employees::where('employee_isactive',1)->whereIn('employee_department',$department_id)->where('employee_id','!=',session('user_session'))->pluck('employee_id')->toArray();
                
                }else{
                    $employees_id = $request->input('emp_names');
                
                }
            
                $share_att_report->att_report_emp_id =  implode(',',$employees_id);
                $share_att_report->att_report_month =  $request->input('attendance_month');
                $share_att_report->att_report_year = $request->input('attendance_year');
                $share_att_report->att_report_created_by = session('user_session');
                $share_att_report->att_report_created_at = date("Y-m-d H:i:s");
                $process = $share_att_report->save();

                if($process == 1){

                    foreach($employees_id as $employee_id){
                        $this->send_notification('Access to Employee Attendance Sheet', ''.$login_user[0]['employee_name'].' share a attendance sheet with you','share-attendance-report/'.$share_att_report->att_report_id.'',$employee_id,NULL,NULL,'no');
                    }

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Attendance sheet share successfully.',
                    ]);
                
                }else{
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Attendance sheet not share successfully try again!.',
                    ]); 
                } 
            
            }
        }catch(\Exception $e){
            $this->create_error_log('Attendance Employees Share Report Insert Record', $e);
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

            ActivityTracker::track_activity('Attendance Employees Share Report Insert Record');
    
        }
    }   


    protected function access_attendance_sheet_report($report_id){
        try{
  
            $login_user = $this->get_login_user_detail();

            $employees = ShareAttReport::where('att_report_id',$report_id)->get()->toArray();

            $employee_record = explode(',',$employees[0]['att_report_emp_id']);
            
            if(in_array(session('user_session'),$employee_record)){
                $month = $employees[0]['att_report_month'];
                $year = $employees[0]['att_report_year'];
                
                $month_days =   $month_days = cal_days_in_month(CAL_MONTH_GREGORIAN_LONG, $month,$year); 

                $date_from = date($year.'-'.$month.'-01');
                $date_to = date($year.'-'.$month.'-t') ;
                
                $all_attendances =  $this->all_attendance_employee_share_report($date_from,$date_to,$month, $year);
               
                $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
            
                $send_data = compact('login_user','all_attendances','month_days','month','year','manage_leaves');


                return view('attendance.all-employee-attendance-pdf',$send_data);

            }else{
                return view('errors.403');
            }
        }catch(\Exception $e){
            $this->create_error_log('All  Attendance Employees Share Report', $e);
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
            ActivityTracker::track_activity('All  Attendance Employees Share Report');

        }
      
    }

    protected function all_attendance_employee_share_report($date_from,$date_to,$month = NULL , $year = NULL){
       
        try{
            $result = [];
            
            $all_employees = Employees::
            where(function ($query) use($date_from,$date_to) {
                $query->where('employee_isactive',1);
                $query->orWhereRaw('STR_TO_DATE(`employee_deactivate_date`,"%Y-%m-%d") >= "'.$date_from.'"  and STR_TO_DATE(`employee_deactivate_date`,"%Y-%m-%d") <= "'.$date_to.'" ');
            })
            ->get(['employee_id','employee_deactivate_date'])->toArray();
    
            if(!empty($all_employees)){

                foreach($all_employees as $employee){


                    $all_attendances = Attendance::where('att_employee_id',$employee['employee_id'])->where('att_for_month',$month)->where('att_for_year',$year)->get()->toArray();
                    
                    $atts = array('attendance' => $all_attendances);
                    $result[] = array_merge( $employee , $atts );
                    

                }
            
                return $result;

            }else{

                return array();

            }
        }catch(\Exception $e){
            $this->create_error_log('All  Attendance Employees Share Report', $e);
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

            ActivityTracker::track_activity('All  Attendance Employees Share Report');
    
        }
        
    }

    protected function monthly_attendance(Request $request){
        try{
        
            $title = Self::TITLE_GLOBAL4;
      
            $login_user = $this->get_login_user_detail();

            // $dynamic_years = $this->dynamic_year_drop_down();

            // $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            // if(!empty($request->is_active == "2")){
            //     $employement_status = 0;
            // }elseif(!empty($request->is_active == "0")){
            //     $employement_status = 0;
            // }else{
            //     $employement_status = 1;
            // }

            // dd(date('d'));

            if(empty($request->from_date) && empty($request->to_date)){
                if(date('d') >= '21'){
                    $from_date = date('Y-m-') . '21';
                    $to_date = date('Y-m-d');
                }else{
                    $today = new DateTime();
                    $today->modify('first day of previous month');
                    $today->modify('+20 days');
                    $from_date = $today->format('Y-m-d');
                    $to_date = date('Y-m-d');
                }
            }
        
            $where_clause = '1=1';

            if(!empty($request->department_id) && $request->department_id != 'All'){
                $where_clause .= ' AND e.employee_department = '.(int) $request->department_id;
            }

            if (!empty($request->designation_id)) {
                $designation_ids = implode(',', array_map('intval', (array) $request->designation_id));
                $where_clause .= ' AND des.designation_id IN ('. $designation_ids .')';
            }

            if(!empty($request->business_unit) && $request->business_unit != 'All'){
                $where_clause .= " AND e.business_unit = ".DB::getPdo()->quote($request->business_unit);
            }

            if(!empty($request->from_date)){
                $from_date = date('Y-m-d', strtotime($request->from_date));
            }

            if(!empty($request->to_date)){
                $to_date = date('Y-m-d', strtotime($request->to_date));
            }


            // $emp_name = '1=1';
            // if($request->input('emp_name') != '' && is_numeric($request->input('emp_name'))){
            //     $emp_name  .= ' AND employee_id = '.\DB::getPdo()->quote((string) $request->input('emp_name')).'';  
            // }
          
            // if($request->input('m') != '' && is_numeric($request->input('m')) && $request->input('m') >= 01 && $request->input('m') <= 12){
            //     $month = $request->input('m');
               
            // }else{
            //     $month = date('m');
             
            // }
            
            // if($request->input('y') != '' && is_numeric($request->input('y')) && $request->input('y') >= 1900 && $request->input('y') <= 3000){
            //     $year = $request->input('y');
            // }else{
            //     $year = date('Y');
            // }
            // $monthdays  = date('t',strtotime($year.'-'.$month.'-01'));
            // $last_date = $year.'-'.$month.'-'.$monthdays;
           
            // $leave_name = LeaveManagement::get('leave_name')->pluck('leave_name')->toArray();
            // array_push($leave_name,"RPL");
            // $leave_name = "'" . implode("', '", $leave_name) . "'";
            
            // if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'finance', $login_user[0]['employee_id'] ?? null)){

            //     $all_employees = Employees::where('employee_isactive', $employement_status);
            //     if ($where_clause != '1=1') {
            //         $all_employees->whereRaw(substr($where_clause, 5));
            //     }
            //     if ($emp_name != '1=1') {
            //         $all_employees->whereRaw($emp_name);
            //     }
            //     $all_employees_data = $all_employees
            //         ->get(['employee_id', 'employee_code', 'employee_name', 'employee_location'])
            //         ->toArray();

            //     $employee_id =  array_column($all_employees_data,'employee_id');

            //     $all_employees = $all_employees_data;
                
            //     $employee_id = "'" . implode("', '", $employee_id) . "'";
                
            //     $where_clause_two = 'AND employee_id IN('.$employee_id.')'; 
            // }
            // else{
                // if($login_user[0]['employee_is_manager'] == 1){
                    // $all_employees = Employees::where('employee_isactive',$employement_status)->where(function($query){
                    //     $query->where('employee_senior_manager',session('user_session'));
                    //     $query->orWhere('employee_junior_manager',session('user_session'));
                    // })
                    // ->whereRaw($where_clause)->whereRaw($emp_name)
                    // ->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

                    // $employee_id =  array_column($all_employees,'employee_id');
                    
                    // $employee_id = "'" . implode("', '", $employee_id) . "'";
                
                    // $where_clause_two = 'AND employee_id IN('.$employee_id.')'; 
                   
                // }else{
                    // $all_employees = Employees::where('employee_isactive',$employement_status)->where('employee_id',session('user_session'))->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();
                    
                    // $where_clause_two = 'AND employee_id IN('.$all_employees[0]['employee_id'].')'; 
                   
                // }
            // }

            // dd($where_clause);

            $departments = Department::get(['department_id','department_name'])->toArray();
            $designations = Designation::where('department_id', $request->department_id)->get(['designation_id','designation_name'])->toArray();
           
            // $all_attendances = $this->get_monthly_attendance($leave_name,$last_date,$year,$month,$employement_status,$where_clause_two);
            // on screen the report is built for one page of employees; downloads cover everyone
            $pagination = null;
            if($request->download != 'TRUE'){
                $search = trim((string) $request->input('search'));
                $pagination = DB::table('tbl_employees as e')
                    ->leftJoin('tbl_designations as des','e.employee_designation','=','des.designation_id')
                    ->whereRaw($where_clause)->where('e.employee_isactive',1)
                    ->when($search !== '', function($query) use($search){
                        $query->where(function($query) use($search){
                            $query->where('e.employee_name','like','%'.$search.'%')->orWhere('e.employee_code','like','%'.$search.'%');
                        });
                    })
                    ->orderBy('e.employee_code')
                    ->paginate(CustomHelper::per_page(), ['e.employee_id'])->withQueryString();
                $page_employee_ids = $pagination->pluck('employee_id')->map(function($id){ return (int) $id; })->toArray();
                $where_clause .= ' AND e.employee_id IN ('.(empty($page_employee_ids) ? '0' : implode(',', $page_employee_ids)).')';
            }

            $all_attendances = $this->get_monthly_attendance_all_employees($from_date , $to_date , $where_clause);
            $all_attendances = json_decode(json_encode($all_attendances), true);

            $send_data = compact('title','login_user','all_attendances','departments','designations','from_date','to_date','pagination');

            if($request->download == 'TRUE'){

                // $all_attendances = DB::select("
                //     SELECT 
                //     e.employee_code as 'Employee Code', 
                //     e.employee_name as 'Employee Name',
                //     FORMAT(CAST(e.employee_doj AS DATE), 'dd-MM-yyyy') as 'Employee Doj',
                //     dep.department_name as 'Department Name', 
                //     des.designation_name as 'Designation Name',
                //     e.business_unit as 'Business Unit',
                //     CONCAT(rep.employee_code , ' ' , rep.employee_name ,' (', e.employee_offdays,')') as 'Shift',
                    
                //     (
                //         DATEDIFF(DAY, 
                //             CASE 
                //                 WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
                //                 ELSE '$from_date' 
                //             END, '$to_date'
                    
                //         ) + 1
                //         - 
                //         (
                //             SUM(CASE 
                //                 WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day') THEN 1 
                //                 ELSE 0 
                //             END) + 
                //             (
                //                 SELECT COUNT(*) 
                //                 FROM (
                //                     SELECT DATEADD(DAY, number, 
                //                         CASE 
                //                             WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
                //                             ELSE '$from_date' 
                //                         END
                //                     ) AS DateInMonth
                //                     FROM master.dbo.spt_values
                //                     WHERE TYPE = 'P'
                //                         AND number BETWEEN 0 AND DATEDIFF(DAY, 
                //                             CASE 
                //                                 WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
                //                                 ELSE '$from_date' 
                //                             END, 
                //                             '$to_date'
                //                         )
                //                         AND NOT EXISTS (
                //                             SELECT 1 FROM tbl_attendance 
                //                             WHERE 
                //                                 att_comp_date = DATEADD(DAY, number, 
                //                                     CASE 
                //                                         WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
                //                                         ELSE '$from_date' 
                //                                     END
                //                                 ) AND
                //                                 att_employee_id = e.employee_id
                //                         )
                //                 ) AS DateRange
                //                 WHERE e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DateInMonth) + '%'
                //             )
                //         )
                //     ) AS 'Absent',
                    
                //     (SUM(CASE WHEN att_check_overall_status = 'Present' OR att_check_overall_status = 'Late Coming' OR att_check_overall_status = 'Early out'
                //     OR att_check_overall_status = 'Half day' THEN 1 ELSE 0 END)) - (
                //         SELECT COUNT(DateInMonth)
                //         FROM (
                //             SELECT 
                //                 DATEADD(DAY, number, '$from_date') AS DateInMonth
                //             FROM master.dbo.spt_values
                //             WHERE TYPE = 'P'
                //                 AND number BETWEEN 0 AND DATEDIFF(DAY, '$from_date', '$to_date')
                //                 AND EXISTS (
                //                     SELECT 1 
                //                     FROM tbl_attendance 
                //                     WHERE 
                //                         att_for_year = YEAR(DATEADD(DAY, number, '$from_date')) AND
                //                         att_for_month = MONTH(DATEADD(DAY, number, '$from_date')) AND
                //                         att_for_date = DAY(DATEADD(DAY, number, '$from_date')) AND
                //                         (
                //                             att_check_in_status = 1 OR
                //                             att_check_out_status = 1
                //                         )
                //                         AND att_employee_id = e.employee_id
                                        
                //                 )
                //                 AND e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DATEADD(DAY, number, '$from_date')) + '%'
                //         ) AS DateRange
                //     ) AS 'DA',
                    
                //     SUM(CASE WHEN att_check_overall_status = 'leaves' 
                //     OR l.leave_name IS NOT NULL THEN 1 ELSE 0 END) AS 'Total Leaves',
                    
                //     (SELECT COUNT(*) 
                //     FROM (
                //     SELECT DATEADD(DAY, number, 
                //         CASE 
                //             WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
                //             ELSE '$from_date' 
                //         END
                //     ) AS DateInMonth
                //     FROM master.dbo.spt_values
                //     WHERE TYPE = 'P'
                //     AND number BETWEEN 0 AND DATEDIFF(DAY, 
                //         CASE 
                //             WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
                //             ELSE '$from_date' 
                //         END, '$to_date'

                //     )
                //             ) AS DateRange
                //             WHERE 
                //             e.employee_offdays like '%'+DATENAME(WEEKDAY, DateInMonth)+'%'
                //         ) as 'Off Days Count',
                            
                //             (SUM(CASE WHEN att_check_overall_status = 'Present' OR att_check_overall_status = 'Late Coming' OR att_check_overall_status = 'Early out'
                //             OR att_check_overall_status = 'Half day' THEN 1 ELSE 0 END)) - (
                //                 SELECT COUNT(DateInMonth)
                //                 FROM (
                //                     SELECT 
                //                         DATEADD(DAY, number, '$from_date') AS DateInMonth
                //                     FROM master.dbo.spt_values
                //                     WHERE TYPE = 'P'
                //                         AND number BETWEEN 0 AND DATEDIFF(DAY, '$from_date', '$to_date')
                //                         AND EXISTS (
                //                             SELECT 1 
                //                             FROM tbl_attendance 
                //                             WHERE 
                //                                 att_for_year = YEAR(DATEADD(DAY, number, '$from_date')) AND
                //                                 att_for_month = MONTH(DATEADD(DAY, number, '$from_date')) AND
                //                                 att_for_date = DAY(DATEADD(DAY, number, '$from_date')) AND
                //                                 (
                //                                     att_check_in_status = 1 OR
                //                                     att_check_out_status = 1
                //                                 )
                //                                 AND att_employee_id = e.employee_id
                                                
                //                         )
                //                         AND e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DATEADD(DAY, number, '$from_date')) + '%'
                //                 ) AS DateRange
                //             ) AS 'Patrol',
                            
                //             (
                //                 DATEDIFF(DAY, 
                //                     CASE 
                //                         WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
                //                         ELSE '$from_date' 
                //                     END, 
                //                     CASE 
                //                         WHEN GETDATE() < '$to_date' THEN GETDATE()
                //                         ELSE '$to_date'
                //                     END
                //                 ) + 1
                //             ) AS 'Month Days',

                //             (
                //                 DATEDIFF(DAY, 
                //                     CASE 
                //                         WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
                //                         ELSE '$from_date' 
                //                     END, 
                //                     CASE 
                //                         WHEN GETDATE() < '$to_date' THEN GETDATE()
                //                         ELSE '$to_date'
                //                     END
                //                 ) + 1
                //             ) - 
                //             (
                //                 DATEDIFF(DAY, 
                //                     CASE 
                //                         WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
                //                         ELSE '$from_date' 
                //                     END, '$to_date'
                //                 ) + 1
                //                 - 
                //                 (
                //                     SUM(CASE 
                //                         WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day') THEN 1 
                //                         ELSE 0 
                //                     END) + 
                //                     (
                //                         SELECT COUNT(*) 
                //                         FROM (
                //                             SELECT DATEADD(DAY, number, 
                //                                 CASE 
                //                                     WHEN e.employee_doj >= '$from_date' THEN e.employee_doj 
                //                                     ELSE '$from_date' 
                //                                 END
                //                             ) AS DateInMonth
                //                             FROM master.dbo.spt_values
                //                             WHERE TYPE = 'P'
                //                                 AND number BETWEEN 0 AND DATEDIFF(DAY, 
                //                                     CASE 
                //                                         WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
                //                                         ELSE '$from_date' 
                //                                     END, 
                //                                     '$to_date'
                //                                 )
                //                                 AND NOT EXISTS (
                //                                     SELECT 1 FROM tbl_attendance 
                //                                     WHERE 
                //                                         att_comp_date = DATEADD(DAY, number, 
                //                                             CASE 
                //                                                 WHEN e.employee_doj > '$from_date' THEN e.employee_doj 
                //                                                 ELSE '$from_date' 
                //                                             END
                //                                         ) AND
                //                                         att_employee_id = e.employee_id
                //                                 )
                //                         ) AS DateRange
                //                         WHERE e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DateInMonth) + '%'
                //                     )
                //                 )
                //             ) AS 'Salary Days',

                //         (
                //             SELECT COUNT(DateInMonth)
                //             FROM (
                //                 SELECT 
                //                     DATEADD(DAY, number, '$from_date') AS DateInMonth
                //                 FROM master.dbo.spt_values
                //                 WHERE TYPE = 'P'
                //                     AND number BETWEEN 0 AND DATEDIFF(DAY, '$from_date', '$to_date')
                //                     AND EXISTS (
                //                         SELECT 1 
                //                         FROM tbl_attendance 
                //                         WHERE 
                //                             att_for_year = YEAR(DATEADD(DAY, number, '$from_date')) AND
                //                             att_for_month = MONTH(DATEADD(DAY, number, '$from_date')) AND
                //                             att_for_date = DAY(DATEADD(DAY, number, '$from_date')) AND
                //                             (
                //                                 att_check_in_status = 1 OR
                //                                 att_check_out_status = 1
                //                             )
                //                             AND att_employee_id = e.employee_id
                                            
                //                     )
                //                     AND e.employee_offdays LIKE '%' + DATENAME(WEEKDAY, DATEADD(DAY, number, '$from_date')) + '%'
                //             ) AS DateRange
                //         ) AS 'Extra Working'

                //             FROM tbl_employees e
                //             LEFT JOIN tbl_attendance a ON e.employee_id = a.att_employee_id 
                //             AND CONCAT(a.att_for_year,'-',a.att_for_month,'-',a.att_for_date) >= '$from_date' 
                //             AND CONCAT(a.att_for_year,'-',a.att_for_month,'-',a.att_for_date) <= '$to_date' 
                //             LEFT JOIN tbl_leave_management l ON a.att_check_overall_status = l.leave_name
                //             LEFT JOIN tbl_departments dep ON e.employee_department = dep.department_id 
                //             LEFT JOIN tbl_designations des ON e.employee_designation = des.designation_id 
                //             LEFT JOIN tbl_employees rep ON e.employee_senior_manager = rep.employee_id
                //             WHERE $where_clause AND e.employee_isactive = 1
                //             GROUP BY 
                //             e.employee_code, 
                //             e.employee_name, 
                //             e.business_unit, 
                //             dep.department_name, 
                //             des.designation_name, 
                //             e.employee_offdays,
                //             e.employee_id,
                //             rep.employee_code,
                //             rep.employee_name,
                //             e.employee_doj

                //             ORDER BY 
                //                 e.employee_code ASC
                // ");

                $all_attendances = DB::select("
                    SELECT
                        e.employee_id,
                        e.employee_code,
                        e.employee_name,
                        e.employee_new_code,
                        DATE_FORMAT(DATE(e.employee_doj), '%d-%m-%Y') as 'employee_doj',
                        dep.department_name,
                        des.designation_name,
                        e.business_unit,
                        CONCAT(rep.employee_code, ' ', rep.employee_name, ' (', e.employee_offdays, ')') as 'Shift',

                        (
                            DATEDIFF(
                                '$to_date',
                                CASE
                                    WHEN e.employee_doj >= '$from_date' THEN e.employee_doj
                                    ELSE '$from_date'
                                END
                            ) + 1
                            -
                            (
                                SUM(CASE
                                    WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day', 'Casual Leave', 'Sick Leave', 'Annual Leave')
                                    OR att_check_overall_status LIKE '%Holiday%'
                                    THEN 1
                                    ELSE 0
                                END) +
                                (
                                    SELECT COUNT(*)
                                    FROM (
                                        SELECT DATE_ADD(
                                            CASE
                                                WHEN e.employee_doj >= '$from_date' THEN e.employee_doj
                                                ELSE '$from_date'
                                            END,
                                            INTERVAL number DAY
                                        ) AS DateInMonth
                                        FROM (
                                            SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS number
                                            FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
                                                 (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens,
                                                 (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) hundreds
                                        ) AS nums
                                        WHERE number BETWEEN 0 AND DATEDIFF(
                                            '$to_date',
                                            CASE
                                                WHEN e.employee_doj > '$from_date' THEN e.employee_doj
                                                ELSE '$from_date'
                                            END
                                        )
                                        AND NOT EXISTS (
                                            SELECT 1 FROM tbl_attendance
                                            WHERE
                                                att_comp_date = DATE_ADD(
                                                    CASE
                                                        WHEN e.employee_doj > '$from_date' THEN e.employee_doj
                                                        ELSE '$from_date'
                                                    END,
                                                    INTERVAL number DAY
                                                ) AND
                                                att_employee_id = e.employee_id
                                        )
                                    ) AS DateRange
                                    WHERE e.employee_offdays LIKE CONCAT('%', DAYNAME(DateInMonth), '%')
                                )
                            )
                        ) AS 'absent',
                        (
                            SUM(CASE
                                WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day') THEN 1
                                ELSE 0
                            END) - (
                                SELECT COUNT(DateInMonth)
                                FROM (
                                    SELECT
                                        DATE_ADD('$from_date', INTERVAL number DAY) AS DateInMonth
                                    FROM (
                                        SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS number
                                        FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
                                             (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens,
                                             (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) hundreds
                                    ) AS nums
                                    WHERE number BETWEEN 0 AND DATEDIFF('$to_date', '$from_date')
                                    AND EXISTS (
                                        SELECT 1
                                        FROM tbl_attendance
                                        WHERE
                                            att_for_year = YEAR(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                            att_for_month = MONTH(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                            att_for_date = DAY(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                            (
                                                att_check_in_status = 1 OR
                                                att_check_out_status = 1
                                            )
                                            AND att_employee_id = e.employee_id
                                    )
                                    AND e.employee_offdays LIKE CONCAT('%', DAYNAME(DATE_ADD('$from_date', INTERVAL number DAY)), '%')
                                ) AS DateRange
                            )
                        ) - IFNULL(Ex.Extraday, 0) AS 'DA',

                        SUM(CASE WHEN att_check_overall_status = 'leaves'
                        OR l.leave_name IS NOT NULL THEN 1 ELSE 0 END) AS total_leaves,

                        (
                            SELECT COUNT(*)
                            FROM (
                                SELECT DATE_ADD(
                                    CASE
                                        WHEN e.employee_doj > '$from_date' THEN e.employee_doj
                                        ELSE '$from_date'
                                    END,
                                    INTERVAL number DAY
                                ) AS DateInMonth
                                FROM (
                                    SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS number
                                    FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
                                         (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens,
                                         (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) hundreds
                                ) AS nums
                                WHERE number BETWEEN 0 AND DATEDIFF(
                                    '$to_date',
                                    CASE
                                        WHEN e.employee_doj > '$from_date' THEN e.employee_doj
                                        ELSE '$from_date'
                                    END
                                )
                            ) AS DateRange
                            WHERE e.employee_offdays LIKE CONCAT('%', DAYNAME(DateInMonth), '%')
                        ) + IFNULL(Ex.HolidayCount, 0) as 'off_days_count',
                        (
                            SUM(CASE WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day') THEN 1 ELSE 0 END) - (
                                SELECT COUNT(DateInMonth)
                                FROM (
                                    SELECT
                                        DATE_ADD('$from_date', INTERVAL number DAY) AS DateInMonth
                                    FROM (
                                        SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS number
                                        FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
                                             (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens,
                                             (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) hundreds
                                    ) AS nums
                                    WHERE number BETWEEN 0 AND DATEDIFF('$to_date', '$from_date')
                                    AND EXISTS (
                                        SELECT 1
                                        FROM tbl_attendance
                                        WHERE
                                            att_for_year = YEAR(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                            att_for_month = MONTH(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                            att_for_date = DAY(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                            (
                                                att_check_in_status = 1 OR
                                                att_check_out_status = 1
                                            )
                                            AND att_employee_id = e.employee_id
                                    )
                                    AND e.employee_offdays LIKE CONCAT('%', DAYNAME(DATE_ADD('$from_date', INTERVAL number DAY)), '%')
                                ) AS DateRange
                            )
                        ) - IFNULL(Ex.Extraday, 0) AS 'Patrol',
                        (
                            DATEDIFF(
                                CASE
                                    WHEN NOW() < '$to_date' THEN DATE(NOW())
                                    ELSE '$to_date'
                                END,
                                CASE
                                    WHEN e.employee_doj >= '$from_date' THEN e.employee_doj
                                    ELSE '$from_date'
                                END
                            ) + 1
                        ) AS 'month_days',
                        (
                            DATEDIFF(
                                CASE
                                    WHEN NOW() < '$to_date' THEN DATE(NOW())
                                    ELSE '$to_date'
                                END,
                                CASE
                                    WHEN e.employee_doj >= '$from_date' THEN e.employee_doj
                                    ELSE '$from_date'
                                END
                            ) + 1
                        ) -
                        (
                            DATEDIFF(
                                '$to_date',
                                CASE
                                    WHEN e.employee_doj >= '$from_date' THEN e.employee_doj
                                    ELSE '$from_date'
                                END
                            ) + 1
                            -
                            (
                                SUM(CASE
                                    WHEN att_check_overall_status IN ('Present', 'Late Coming', 'Early out', 'Half day' , 'Casual Leave', 'Sick Leave', 'Annual Leave')
                                    OR att_check_overall_status LIKE '%Holiday%'
                                    THEN 1
                                    ELSE 0
                                END) +
                                (
                                    SELECT COUNT(*)
                                    FROM (
                                        SELECT DATE_ADD(
                                            CASE
                                                WHEN e.employee_doj >= '$from_date' THEN e.employee_doj
                                                ELSE '$from_date'
                                            END,
                                            INTERVAL number DAY
                                        ) AS DateInMonth
                                        FROM (
                                            SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS number
                                            FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
                                                 (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens,
                                                 (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) hundreds
                                        ) AS nums
                                        WHERE number BETWEEN 0 AND DATEDIFF(
                                            '$to_date',
                                            CASE
                                                WHEN e.employee_doj > '$from_date' THEN e.employee_doj
                                                ELSE '$from_date'
                                            END
                                        )
                                        AND NOT EXISTS (
                                            SELECT 1 FROM tbl_attendance
                                            WHERE
                                                att_comp_date = DATE_ADD(
                                                    CASE
                                                        WHEN e.employee_doj > '$from_date' THEN e.employee_doj
                                                        ELSE '$from_date'
                                                    END,
                                                    INTERVAL number DAY
                                                ) AND
                                                att_employee_id = e.employee_id
                                        )
                                    ) AS DateRange
                                    WHERE e.employee_offdays LIKE CONCAT('%', DAYNAME(DateInMonth), '%')
                                )
                            )
                        ) AS salary_days,
                        (
                            SELECT COUNT(DateInMonth)
                            FROM (
                                SELECT
                                    DATE_ADD('$from_date', INTERVAL number DAY) AS DateInMonth
                                FROM (
                                    SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS number
                                    FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
                                         (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens,
                                         (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) hundreds
                                ) AS nums
                                WHERE number BETWEEN 0 AND DATEDIFF('$to_date', '$from_date')
                                AND EXISTS (
                                    SELECT 1
                                    FROM tbl_attendance
                                    WHERE
                                        att_for_year = YEAR(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                        att_for_month = MONTH(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                        att_for_date = DAY(DATE_ADD('$from_date', INTERVAL number DAY)) AND
                                        (
                                            att_check_in_status = 1 OR
                                            att_check_out_status = 1
                                        )
                                        AND att_employee_id = e.employee_id
                                )
                                AND e.employee_offdays LIKE CONCAT('%', DAYNAME(DATE_ADD('$from_date', INTERVAL number DAY)), '%')
                            ) AS DateRange
                        ) + IFNULL(Ex.Extraday, 0) AS 'extra_working'

                    FROM tbl_employees e
                    LEFT JOIN tbl_attendance a ON e.employee_id = a.att_employee_id
                        AND CONCAT(a.att_for_year, '-', a.att_for_month, '-', a.att_for_date) BETWEEN '$from_date' AND '$to_date'
                    LEFT JOIN tbl_leave_management l ON a.att_check_overall_status = l.leave_name
                    LEFT JOIN tbl_departments dep ON e.employee_department = dep.department_id
                    LEFT JOIN tbl_designations des ON e.employee_designation = des.designation_id
                    LEFT JOIN tbl_employees rep ON e.employee_senior_manager = rep.employee_id
                    LEFT JOIN (
                        SELECT employee_code,
                        COUNT(CASE WHEN a.att_check_overall_status NOT LIKE '%Holiday%' THEN holiday_date END) AS Extraday,
                        COUNT(holiday_date) AS HolidayCount
                        FROM (
                            SELECT
                                h.holiday_date,
                                a.employee_designation,
                                h.holiday_name,
                                a.employee_code,
                                a.att_check_overall_status,
                                a.att_comp_date
                            FROM tbl_holidays AS h
                            JOIN (
                                SELECT e.employee_designation, employee_code, att_check_overall_status, att_comp_date
                                FROM tbl_attendance t
                                INNER JOIN tbl_employees e ON t.att_employee_id = e.employee_id
                                INNER JOIN tbl_departments d ON e.employee_department = d.department_id
                                INNER JOIN tbl_designations de ON e.employee_designation = de.designation_id
                            ) AS a ON h.holiday_date = a.att_comp_date
                                AND (FIND_IN_SET(CAST(a.employee_designation AS CHAR), h.designation_name) OR h.designation_name = '*')
                        ) AS a

                        where a.holiday_date between '$from_date' and '$to_date'

                        GROUP BY employee_code
                    ) AS Ex ON e.employee_code = Ex.employee_code
                    WHERE $where_clause AND e.employee_isactive = 1
                    GROUP BY
                        e.employee_code,
                        e.employee_name,
                        e.employee_new_code,
                        e.business_unit,
                        dep.department_name,
                        des.designation_name,
                        e.employee_offdays,
                        e.employee_id,
                        rep.employee_code,
                        rep.employee_name,
                        e.employee_doj,
                        Ex.Extraday,
                        Ex.HolidayCount

                    ORDER BY e.employee_code ASC
                ");

                $all_attendance = json_decode(json_encode($all_attendances), true);

                $header_style = (new Style())->setFontBold();

                return (new FastExcel($all_attendance))->headerStyle($header_style)->download('payroll-Attendance.xlsx');
            }else{
                return view('attendance.all-employee-manage-attendance',$send_data);
            }

            
          
        }catch(\Exception $e){
            $this->create_error_log('View All Employees Monthly Attendance', $e);
            $send_data = compact('title');
            return $e->getline().$e->getMessage();
            // return view('errors.500');
        }finally{

            ActivityTracker::track_activity('View All Employees Monthly  Attendance');
          
        }
    }

    protected function download_monthly_attendance(Request $request){
        try{
           
            $title = Self::TITLE_GLOBAL4;
      
            $security_variable = FALSE;

            $validator = Validator::make($request->all(), [
                'confirmation_code' => ['required']
            ]);

            $login_user = $this->get_login_user_detail();

            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];

            if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
                $security_variable = TRUE;
            }
            else{
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/monthly-attendance';</script>";
            }

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if(!empty($request->is_active == "2")){
                $employement_status = 0;
            }elseif(!empty($request->is_active == "0")){
                $employement_status = 0;
            }else{
                $employement_status = 1;
            }
        
            $where_clause = '1=1';
            if(!empty($request->employee_location)){
                $where_clause .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request->employee_location);  
            }
            $emp_name = '1=1';
            if($request->input('emp_name') != '' && is_numeric($request->input('emp_name'))){
                $emp_name  .= ' AND employee_id = '.\DB::getPdo()->quote((string) $request->input('emp_name')).'';  
            }
          
            if($request->input('m') != '' && is_numeric($request->input('m')) && $request->input('m') >= 01 && $request->input('m') <= 12){
                $month = $request->input('m');
               
            }else{
                $month = date('m');
             
            }
            
            if($request->input('y') != '' && is_numeric($request->input('y')) && $request->input('y') >= 1900 && $request->input('y') <= 3000){
                $year = $request->input('y');
            }else{
                $year = date('Y');
            }
            $monthdays  = date('t',strtotime($year.'-'.$month.'-01'));
            $last_date = $year.'-'.$month.'-'.$monthdays;
           
            $leave_name = LeaveManagement::pluck('leave_name')->toArray();
            array_push($leave_name,"RPL");
            $leave_name = "'" . implode("', '", $leave_name) . "'";
            
            if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'finance', $login_user[0]['employee_id'] ?? null)){
            
                $all_employees = Employees::where('employee_isactive',$employement_status)->whereRaw($where_clause)->whereRaw($emp_name)
                ->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

                $employee_id =  array_column($all_employees,'employee_id');
                
                $employee_id = "'" . implode("', '", $employee_id) . "'";
                
                $where_clause_two = 'AND employee_id IN('.$employee_id.')'; 
            }
            else{
                if($login_user[0]['employee_is_manager'] == 1){
                    $all_employees = Employees::where('employee_isactive',$employement_status)->where(function($query){
                        $query->where('employee_senior_manager',session('user_session'));
                        $query->orWhere('employee_junior_manager',session('user_session'));
                    })
                    ->whereRaw($where_clause)->whereRaw($emp_name)
                    ->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

                    $employee_id =  array_column($all_employees,'employee_id');
                    
                    $employee_id = "'" . implode("', '", $employee_id) . "'";
                
                    $where_clause_two = 'AND employee_id IN('.$employee_id.')'; 
                   
                }else{
                    $all_employees = Employees::where('employee_isactive',$employement_status)->where('employee_id',session('user_session'))
                    ->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();
                    
                    $where_clause_two = 'AND employee_id IN('.$all_employees[0]['employee_id'].')'; 
                   
                }
            }
           
            $all_attendances = $this->get_monthly_attendance($leave_name,$last_date,$year,$month,$employement_status,$where_clause_two);
            $all_attendances = json_decode(json_encode($all_attendances), true);

            $send_data = compact('all_attendances','month','year');
           
            if($security_variable == TRUE){
              
                $pdf = Pdf::loadView('attendance.all-employee-manage-attendance-download-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Monthly Attendance'.'.pdf');
            }
          
        }catch(\Exception $e){
            $this->create_error_log('View All Employees Monthly Attendance', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('Download All Employees Monthly  Attendance');
          
            
        }
    }

    protected function if_to_date_greater_than_today($to_date){
        $to_date_time = strtotime($to_date);
        $current_date_time = strtotime(date('Y-m-d'));
        return $to_date_time > $current_date_time;
    }

    protected function attendance_locker_sheet(){
        try{
            $title = 'attendance-locker';
            
            $login_user = $this->get_login_user_detail();

            $get_locked_sheet_records = AttendanceLocker::get()->toArray();

            $send_data = compact('title','login_user','get_locked_sheet_records');

            return view('attendance.attendance-locker', $send_data);
        
        }catch(\Exception $e){
            $this->create_error_log('View Attendance Locked Screen', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View Attendance Locked Screen');
          
        }
    }

    protected function lock_attendance_sheet(Request $request){
        try{

            $validator = Validator::make($request->all(), [

                'from_date' => 'required|date_format:Y-m-d|after:'. date('Y-m-d',strtotime('-2 month')). '',
                // 'from_date' => ['required','date_format:Y-m-d','before:' . date('Y-m-d')],
                'to_date' =>   'required|date_format:Y-m-d|after:from_date',

            ]);
    
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                $get_if_already_exists = AttendanceLocker::where('from_date', $request->input('from_date'))->where('to_date', $request->input('to_date'))->get()->toArray();
                if(empty($get_if_already_exists)){
                    $create_new_attendance = new AttendanceLocker();
                    $create_new_attendance->from_date = $request->input('from_date');
                    $create_new_attendance->to_date = $request->input('to_date');
                    $create_new_attendance->locked_by = session('user_session');
                    $create_new_attendance->locked_date = date('Y-m-d H:i:s');
                    $process = $create_new_attendance->save();
                    if($process){
                        return response()->json([
                            'msg' => 'Attendance sheet locked successfully.',
                            'status' => 'TRUE'
                        ]);
                    }else{
                        return response()->json([
                            'msg' => 'Attendance sheet locking failed. Please try again.',
                            'status' => 'FALSE'
                        ]);
                    }
                }else{
                    return response()->json([
                        'msg' => 'Attendance sheet already locked for this selected period.',
                        'status' => 'FALSE'
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log('Attendance locking method', $e);
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
            ActivityTracker::track_activity('Attendance locking method');
        }
    }

    protected function export_data_into_dat_file(Request $request){
        try{
            $from_date = date('Y-m-d', strtotime($request->input('from_date')));

            $to_date = date('Y-m-d', strtotime($request->input('to_date')));

            $emp_group =  $request->input('emp_group');

            $validator = Validator::make($request->all(), [
                'from_date' => 'required',
                'emp_group' => 'required',
                'to_date' => 'required|date_format:Y-m-d|before_or_equal:now',
                // 'to_date' => [
                //     'required',
                //     'date_format:Y-m-d',
                //     'before_or_equal:now',
                //     function ($attribute, $value, $fail) use ($request) {
                //         $fromDate = $request->input('from_date');
                //         $toDate = $request->input('to_date');
            
                //         if ($toDate != $fromDate && strtotime($toDate) > strtotime($fromDate . ' +10 days')) {
                //             $fail('The to date must be equal to from date or within 10 days after from date.');
                //         }
                //     },
                // ],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE',
                        'validation' => 'TRUE',
                    ]);
                }
            }
        
            $get_attendance_data = Attendance::join('tbl_employees', 'tbl_attendance.att_employee_id', '=', 'tbl_employees.employee_id')
            ->whereBetween('att_comp_date', [$from_date, $to_date])
            ->whereIn('employee_group', (array) $emp_group)
            ->orderBy('att_comp_date')
            ->get(['att_check_in_time','att_check_out_time','att_check_in_status','att_check_out_status','att_comp_date','tbl_employees.employee_code','tbl_employees.employee_group'])
            ->toArray();

            if(!empty($get_attendance_data)) {

                // built in memory and sent as a download (no temporary file in the public folder)
                $records = '';
                foreach($get_attendance_data as $attendance_date){
                    $day = date('Ymd', strtotime($attendance_date['att_comp_date']));
                    if($attendance_date['att_check_in_status'] == '1'){
                        $records .= '31'.$day.date("Hi", strtotime($attendance_date['att_check_in_time'])).'0001000'.$attendance_date['employee_code'] .'0063'. "\n";
                    }
                    if($attendance_date['att_check_out_status'] == '1'){
                        $records .= '31'.$day.date("Hi", strtotime($attendance_date['att_check_out_time'])).'0002000'.$attendance_date['employee_code'] .'0063'. "\n";
                    }
                }

                return response($records, 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="attendancelist.dat"',
                ]);
            } else {
                return response()->json([
                    'msg' => 'No Records Found',
                    'status' => 'FALSE',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Download Attendance Dat file', $e);
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
            ActivityTracker::track_activity('Download Attendance Dat file');
        }    
    }

    protected function upload_attendance_record(Request $request)
    {
        set_time_limit(18000);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimetypes:text/plain',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                return response()->json([
                    'msg' => $error,
                    'status' => 'FALSE',
                    'validation' => 'TRUE',
                ]);
            }
        }
       

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getPathname();

            $dataArray = [];
            $error_logs = [];
            $count_row = 0;

            try {
                $handle = fopen($filename, "r");

                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        $data = trim($line);

                        $first_two_static = substr($data, 0, 2);
                        $date = substr($data, 2, 4) . '-' . substr($data, 6, 2) . '-' . substr($data, 8, 2);
                        $time = substr($data, 10, 2) . ':' . substr($data, 12, 2);
                        $second_two_standard = substr($data, 14, 2);
                        $checkinout = substr($data, 16, 2);
                        $three_standard = substr($data, 18, 3);
                        $employee_Code = substr($data, 21, 7);
                        $two_standard = substr($data, 28, 2);
                        $machineID = substr($data, 30, 2);
                        
                        $check_in_time = '';
                        $check_out_time = '';

                        $check_in = '';
                        $check_out= '';

                        if($checkinout == '01') {
                            $check_in_time = $time;
                            $check_in = $checkinout;
                        }else{
                            $check_out_time = $time;
                            $check_out = $checkinout;
                        }  

                        $dataArray[] = [
                            'firststatic' => $first_two_static,
                            'date' => $date,
                            'in_time' => $check_in_time,
                            'out_time' => $check_out_time,
                            'twostandard' => $second_two_standard,
                            'checkin' => $check_in,
                            'checkout' => $check_out,
                            'threestandard' => $three_standard,
                            'employeeCode' => $employee_Code,
                            'twostandard' => $two_standard,
                            'machineID' => $machineID,
                        ];
                            
                        DB::table('tbl_attendance_object')->insert([
                            'first_standard' => $first_two_static,
                            'attendance_date' => $date,
                            'attendance_time' => $check_in_time,
                            'second_two_static' => $second_two_standard,
                            'check_in_out' => $check_in,
                            'three_standard' => $three_standard,
                            'employee_Code' => $employee_Code,
                            'two_standard' => $two_standard,
                            'machine_id' => $machineID,
                            'attendance_out_time' => $check_out_time,
                            'check_out' => $check_out
                        ]);
                        
                            
                        $count_row++;
                    }

                    fclose($handle);

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Attendance records uploaded successfully',
                        'error_logs' => $error_logs,
                    ]);
                } else {
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Error opening the file',
                    ]);
                }
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Error reading the file: '.$e->getLine().' '.$e->getMessage(),
                ]);
            }
        } else {
            return response()->json([
                'status' => 'FALSE',
                'msg' => 'No file uploaded',
            ]);
        }
    }

    protected function delete_attendance_record(Request $request){
        try{
            $attendance_record_id = $request->input('attendance_id');
            $deletion_remarks = $request->input('deletion_remarks');

            if(!empty($attendance_record_id) && !empty($deletion_remarks)){
                $get_attendance_data = Attendance::where('attendance_id', $attendance_record_id)->get([
                    'att_employee_id',
                    'attendance_id',
                    'att_check_in_time',
                    'att_check_out_time',
                    'att_comp_date'
                ])->toArray();

                if(!empty($get_attendance_data)){

                    $add_new_delete_attendance_record = new DeletedAttendance();
                    $add_new_delete_attendance_record->att_record_id = $get_attendance_data[0]['attendance_id'];
                    $add_new_delete_attendance_record->att_date = $get_attendance_data[0]['att_comp_date'];
                    $add_new_delete_attendance_record->att_emp_id = $get_attendance_data[0]['att_employee_id'];
                    $add_new_delete_attendance_record->att_checkin_time = $get_attendance_data[0]['att_check_in_time'];
                    $add_new_delete_attendance_record->att_checkout_time = $get_attendance_data[0]['att_check_out_time'];
                    $add_new_delete_attendance_record->att_deleted_by = session('user_session');
                    $add_new_delete_attendance_record->att_deleted_reason = $deletion_remarks;
                    $add_new_delete_attendance_record->att_deleted_at = date('Y-m-d H:i:s');
                    $add_new_delete_attendance_record->save();

                    Attendance::where('attendance_id', $attendance_record_id)->delete();

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Attendance record has been successfully deleted.',
                    ]);

                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Attendance record is already deleted.',
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Please provide deletion remarks.',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Delete Attendance Record', $e);
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
            ActivityTracker::track_activity('Delete Attendance Record');
        }
    }

    // protected function download_day_wise_attendance_report(Request $request){
    //     // ini_set('max_execution_time',3000);
    //     set_time_limit(1800);
    //     // dd($request->from_date);
    //     $employees = Employees::whereIn('employee_designation', [400,453,478])->where('employee_isactive', 1)->get(['employee_id'])->toArray();
    //     // dd($employees);
    //     if(!empty($employees)){
    //         $attendance_array = [];
    //         foreach($employees as $employee){
    //             $employee_id = $employee['employee_id'];
    //             $attendance = DB::select("SELECT 
    //             emp.employee_code as 'Employee ID', 
    //             emp.employee_name as  'Name', 
    //             CONCAT(LM.employee_code , ' - ' , LM.employee_name) as 'Line Manager',
    //             emp.business_unit as 'Business Unit',
    //             CONVERT(date, dates.date_between) as 'Date', 
    //             DATENAME(dw, dates.date_between) as 'Day', 
    //             CASE 
    //                 WHEN emp.employee_doj <= dates.date_between THEN
    //                     CASE 
    //                         WHEN att.att_check_overall_status IS NULL AND CHARINDEX(DATENAME(dw, dates.date_between), REPLACE(emp.employee_offdays, ' ', '')) > 0 THEN 'Off Day'
    //                         WHEN att.att_check_overall_status IS NULL THEN 'Absent' ELSE 'Present'
    //                     END
    //                 ELSE '-' 
    //             END AS 'Attendance Status',
    //             CASE 
    //                 WHEN emp.employee_doj <= dates.date_between THEN ISNULL(att.att_check_in_time, '') 
    //                 ELSE '' 
    //             END AS 'Check In', 
    //             CASE 
    //                 WHEN emp.employee_doj <= dates.date_between THEN ISNULL(att.att_check_out_time, '') 
    //                 ELSE '' 
    //             END AS 'Check Out'
    //         FROM (
    //             SELECT DATEADD(DAY, n, '$request->from_date') AS date_between
    //             FROM (
    //                 SELECT ones.n + tens.n * 10 + hundreds.n * 100 AS n
    //                 FROM (VALUES (0),(1),(2),(3),(4),(5),(6),(7),(8),(9)) ones(n),
    //                     (VALUES (0),(1),(2),(3),(4),(5),(6),(7),(8),(9)) tens(n),
    //                     (VALUES (0),(1),(2),(3),(4),(5),(6),(7),(8),(9)) hundreds(n)
    //             ) AS numbers
    //             WHERE DATEADD(DAY, n, '$request->from_date') BETWEEN '$request->from_date' AND '$request->to_date'
    //         ) AS dates
    //         LEFT JOIN tbl_attendance AS att ON CONVERT(date, dates.date_between) = att.att_comp_date AND att.att_employee_id = $employee_id
    //         LEFT JOIN tbl_employees AS emp ON $employee_id = emp.employee_id 
    //         left join tbl_employees as LM ON emp.employee_senior_manager = LM.employee_id
    //         ORDER BY dates.date_between ASC");
    //             $attendance = json_decode(json_encode($attendance), TRUE);
    //             $attendance_array = array_merge($attendance_array, $attendance);
    //         }
    //         // dd($attendance_array);
    //         $header_style = (new Style())->setFontBold();

    //         return (new FastExcel($attendance_array))->headerStyle($header_style)->download('Detailed Attendance Report.xlsx');
    //     }
    // }


}