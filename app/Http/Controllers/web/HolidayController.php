<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\{Holiday, Employees, Attendance, Department, Designation};
use App\Traits\{LoginEmployee, LogGenerateTrait, AttendanceEmployee};
use App\Helpers\ActivityTracker;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    use LoginEmployee, LogGenerateTrait, AttendanceEmployee;

    const TITLE_GLOBAL = 'holidays';

    protected function index(Request $request){
        try{
            $title = SELF::TITLE_GLOBAL;
                
            $login_user = $this->get_login_user_detail();
            
            $dynamic_years = $this->dynamic_year_drop_down();

            $month = '';

            if(!empty($request->input('y'))){
                $year = $request->input('y');
            }else{
                $year = date('Y');
            }

            $where_clause = '1=1 ';
            if(empty($request->input('m'))){
                $where_clause .= " AND holiday_date LIKE '%" . $year . "%'";
            }else{
                $where_clause .= ' AND date_format(holiday_date,"%Y-%m")  = "'.$year.'-'.$request->input('m').'"';
                $month = $request->input('m');
            }
            
            $get_all_holidays = Holiday::whereRaw($where_clause)->get()->toArray();
            
            $departments = Department::get(['department_id','department_name'])->toArray();

            $designations = Designation::get(['designation_id','designation_name'])->toArray();
            
            $send_data = compact('title', 'login_user', 'get_all_holidays','departments','designations','year','month','dynamic_years');

            return view('holiday.holiday')->with($send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View All Holidays', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
		    ActivityTracker::track_activity('View All Holidays');
           
        }
    }

    protected function holidays_download(Request $request){
        try{

            set_time_limit(3000);

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
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/shift-management';</script>";
            }
          
            $dynamic_years = $this->dynamic_year_drop_down();

            $month = '';

            if(!empty($request->input('y'))){
                $year = $request->input('y');
            }else{
                $year = date('Y');
            }

            $where_clause = "1=1 ";
            if(empty($request->input('m'))){
                $where_clause .= " AND holiday_date LIKE '%" . $year . "%' ";

            }else{
                // $where_clause .= ' AND date_format(holiday_date,"%Y-%m")  = "'.$year.'-'.$request->input('m').'"';
                $where_clause .= " AND FORMAT(holiday_date, 'yyyy-MM') = '" . $year . '-' . $request->input('m') . "'";
                $month = $request->input('m');
            }
            
            $get_all_holidays = Holiday::join('tbl_employees','tbl_employees.employee_id','=','tbl_holidays.holiday_created_by')->whereRaw($where_clause)->get()->toArray();
            
            $departments = Department::get(['department_id','department_name'])->toArray();

            $designations = Designation::get(['designation_id','designation_name'])->toArray();
            
            $send_data = compact('login_user', 'get_all_holidays','departments','designations','year','month','dynamic_years');

            if($security_variable == TRUE){
               
                $pdf = Pdf::loadView('holiday.download-holiday-report',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Holidays'.'.pdf');
            }
        }
        catch(\Exception $e){
            $this->create_error_log('Download All Holidays Pdf', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
		    ActivityTracker::track_activity('Download All Holidays Pdf');
          
        }
    }

    protected function add_holiday_request(Request $request){
       
        try{

            set_time_limit(3000);

            $validator = Validator::make($request->all(), [

                'days_assign' => 'required',
                'from_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:to_date' : '' ,
                'to_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d' : '' ,
                'holiday_date' => ($request->input('days_assign') == 'single' ) ? 'required' : '' ,
                'holiday_reason' => 'required|string|max:30',
                'department_name' => 'required',
                'designation_name' => 'required',
            ]);
                
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                DB::beginTransaction();

                $check_any_employee_payroll = false;

                $unset_department_count = 0;
                $unset_designation_count = 0;

                $request_department_name = $request->input('department_name');
                $request_designation_name = $request->input('designation_name');

                foreach($request_department_name as $key => $departments){

                    $check_if_employees_exist_on_depart = Employees::where('employee_department', $departments)->where('employee_isactive', 1)->get(['employee_id'])->toArray();

                    if(empty($check_if_employees_exist_on_depart)){
                        if(count($request_department_name) == 1){

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'Employee(s) not exist on this department.',
                            ]);
                        }else{

                            $unset_department_count ++;

                            unset($request_department_name[$key]);
                        }
                       
                    }
                }
                $request_department_name;

                if(count($request_department_name)==$request->input('count')){
                    $department_name = "*";
                    
                }else{
                    $department_name = implode(',', $request_department_name);
                    
                }

                foreach($request_designation_name as $key => $designations){

                    $check_if_employees_exist_on_desig = Employees::where('employee_designation', $designations)->where('employee_isactive', 1)->get(['employee_id'])->toArray();

                    if(empty($check_if_employees_exist_on_desig)){
                        if(count($request_designation_name) == 1){

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'Employee(s) not exist on this designation.',
                            ]);
                        }else{

                            $unset_designation_count ++;

                            unset($request_designation_name[$key]);
                        }
                       
                    }
                }
                $request_designation_name;

                if(count($request_designation_name)==$request->input('count_designation')){
                    $designation_name = "*";
                  
                }else{
                    $designation_name = implode(',', $request_designation_name);
                  
                }

                $day = date("d", strtotime($request->input('holiday_date')));

                $month = date("m", strtotime($request->input('holiday_date')));
                
                $year = date("Y", strtotime($request->input('holiday_date')));

                if(!empty($request->input('from_date')) && !empty($request->input('to_date'))){
                    $no_of_days = $this->count_days($request->input('from_date'),$request->input('to_date'));
                    $start_date =  $request->input('from_date');
                }else{
                    $no_of_days = 1;
                    $start_date = $request->input('holiday_date');
                }

                $checking_request_dates_department_designation = array();

                for ($i = 0; $i < $no_of_days; $i++) {
                    $dates = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($start_date)));
                    $checking_request_department_designation = array();
                
                    $valid_department_designations = Designation::join('tbl_departments', 'tbl_designations.department_id', '=', 'tbl_departments.department_id')
                        ->whereIn('tbl_designations.designation_id', $request_designation_name)
                        ->whereIn('tbl_departments.department_id', $request_department_name)
                        ->get(['tbl_departments.department_id', 'tbl_designations.designation_id'])
                        ->toArray();
                
                    foreach ($valid_department_designations as $dept_desg) {

                        $department_id = $dept_desg['department_id'];
                        $designation_id = $dept_desg['designation_id'];
                    
                        if (count($request_designation_name) == $request->input('count_designation') || $designation_name == '*') {
                            $designation_id = "*";
                        }elseif (count($request_department_name) == $request->input('count') || $department_name == '*') {
                            $department_id = "*";
                        }
                        else{
                    
                            $checking_request_dates = Holiday::where('holiday_date', $dates)
                                ->where(function ($query) use ($department_id, $designation_id) {
                                    $query->where(function ($q) use ($department_id) {
                                        $q->where('department_name', 'like', "%" . $department_id . "%")
                                            ->orWhere('department_name', 'like', '%*%');
                                    })
                                    ->where(function ($q) use ($designation_id) {
                                        $q->where('designation_name', 'like', "%" . $designation_id . "%")
                                            ->orWhere('designation_name', 'like', '%*%');
                                    });
                                })
                                ->get(['holiday_date', 'department_name', 'designation_name'])
                                ->toArray();
                    
                            if (empty($checking_request_dates)) {
                                $checking_request_department_designation[] = [
                                    'department_id' => $department_id,
                                    'designation_id' => $designation_id
                                ];
                            } else {
                                // Fetch department and designation names for existing holidays
                                
                                $check_department_name = $department_id != '*' 
                                    ? Department::where('department_id', $department_id)->get(['department_id', 'department_name'])->toArray()
                                    : [];
                                $check_designation_name = $designation_id != '*'
                                    ? Designation::where('designation_id', $designation_id)->get(['designation_id', 'designation_name'])->toArray()
                                    : [];
                    
                                if ((count($request_department_name) + $unset_department_count) != $request->input('count') ||
                                    (count($request_designation_name) + $unset_designation_count) != $request->input('count_designation')) {
                                    if ($department_id != '*' && $designation_id != '*') {
                                        if (!empty($checking_request_dates) && $no_of_days == 1) {
                                            return response()->json([
                                                'status' => 'FALSE',
                                                'msg' => 'Holiday already exists for ' . 
                                                    ($check_department_name[0]['department_name'] ?? 'Unknown Department') . ' department and ' . 
                                                    ($check_designation_name[0]['designation_name'] ?? 'Unknown Designation') . ' designation on this date.',
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                        
                    // Combine checked departments and designations for the given date
                    $checking_request_dates_department_designation[$dates] = $checking_request_department_designation;
                        
                }
                
                foreach ($checking_request_dates_department_designation as $date_key => $request_department_designation) {

                    if(!empty($request_department_designation)){

                        $all_employees = array();

                        $unset_department_payroll_count = 0;
                        $unset_designation_payroll_count = 0;

                        foreach ($request_department_designation as $dept_desg) {
                            $department = $dept_desg['department_id'];
                            $designation = $dept_desg['designation_id'];
                
                            $all_departments_designations_employees = Employees::where('employee_department', $department)
                                ->where('employee_designation', $designation)
                                ->where('employee_isactive', 1)
                                ->where('employee_doj', '<=', $date_key)
                                ->pluck('employee_id')
                                ->toArray();
                
                            if (!empty($all_departments_designations_employees)) {
                                $check_department_designation_employee_payroll = array();
                                foreach ($all_departments_designations_employees as $check_all_employees) {
                                    $payroll_exists = $this->check_if_payroll_exist($date_key, $check_all_employees, $custom_message = 'True');
                                    if ($payroll_exists['status'] == FALSE) {
                                        array_push($check_department_designation_employee_payroll, $check_all_employees);
                                    } else {
                                        $check_any_employee_payroll = true;
                                    }
                                }
                                if (!empty($check_department_designation_employee_payroll)) {
                                    array_push($all_employees, $check_department_designation_employee_payroll);
                                } else {
                                    unset($request_department_designation[$unset_department_payroll_count][$unset_designation_payroll_count]);
                                }
                            }
                            $unset_department_payroll_count++;
                            $unset_designation_payroll_count++;
                        }
                      
                        if(empty($all_employees)){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Holiday can't be marked. Payroll of some employees has already been generated from selected departments.",
                            ]);     
                        }

                        $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($date_key);

                        if($check_attendance_sheet_availability['status'] == FALSE){
                            return response()->json([
                                'msg' => $check_attendance_sheet_availability['msg'],
                                'status' => 'FALSE'
                            ]);
                        }

                        $new_holiday = new Holiday();
                        $new_holiday->holiday_date = $date_key;
                        $new_holiday->holiday_name = $request->input('holiday_reason');
                        // $new_holiday->department_name = implode(',', $request_department);
                        // $new_holiday->designation_name = implode(',', $request_designation);
                        $new_holiday->department_name = implode(',', array_unique(array_column($request_department_designation, 'department_id')));
                        $new_holiday->designation_name = implode(',', array_unique(array_column($request_department_designation, 'designation_id')));
                        $new_holiday->holiday_created_at = date('Y-m-d H:i:s');
                        $new_holiday->holiday_created_by = session('user_session');

                        if ($request->input('overtime_applicable') == 1) {
                            $new_holiday->is_overtime_applicable = 1;
                        
                            if ($request->input('overtime_payment_type') == 1) {
                                $new_holiday->overtime_payment_type = 1;
                            } else {
                                $new_holiday->overtime_payment_type = 0;
                            }
                        
                            $new_holiday->holiday_overtime_amount = $request->input('holiday_overtime_amount');
                        } else {
                            $new_holiday->is_overtime_applicable = 0;
                            $new_holiday->overtime_payment_type = null;
                            $new_holiday->holiday_overtime_amount = null;
                        }

                        $process = $new_holiday->save();

                        $day = date("d", strtotime($date_key));

                        $month = date("m", strtotime($date_key));
                        
                        $year = date("Y", strtotime($date_key));

                        if(!empty($all_employees)){
                            foreach($all_employees as $employee){
                                foreach($employee as $emp){

                                    if($request->input('all_holiday') == "on"){
                                        Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->delete();
                                    }else{
                                        Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->where('att_check_overall_status','Absent')->delete();
                                    }
                                    
                                    $get_attendance = Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->get(['attendance_id'])->toArray();
                                    $employee_shift_start = $this->get_employee_today_shift($emp,"in",$date_key);
                                    $employee_shift_end = $this->get_employee_today_shift($emp,"out",$date_key);
                                    if(empty($get_attendance)){
                                        $update_attendance = new Attendance();
                                        $update_attendance->att_employee_id = $emp;
                                        $update_attendance->att_check_in_time = '00:00:00';
                                        $update_attendance->att_check_out_time = '00:00:00';
                                        $update_attendance->att_check_in_status = 0;
                                        $update_attendance->att_check_out_status = 0;
                                        $update_attendance->att_check_out_source = 1;
                                        $update_attendance->att_shift_start_time = $employee_shift_start;
                                        $update_attendance->att_shift_end_time = $employee_shift_end;
                                        $update_attendance->att_check_overall_status = 'Holiday : '.$request->input('holiday_reason').'';
                                        $update_attendance->att_for_date = $day;
                                        $update_attendance->att_for_month = $month;
                                        $update_attendance->att_for_year = $year;
                                        $update_attendance->att_comp_date = $year . '-' . $month . '-' . $day;
                                        $update_attendance->save();
                                    }
                                }
                            }     
                        }

                    }else{

                        if($department_name != '*' && $designation_name == '*'){

                            if (!is_array($department_name)) {
                                $department_name = explode(',', $department_name);
                            }

                            $department_conditions = [];
                            foreach ($department_name as $department) {
                                $department_conditions[] = "(',' + department_name + ',' LIKE '%,$department,%')";
                            }

                            $department_condition_sql = implode(' OR ', $department_conditions);

                            $Check_if_hoildays_exists = Holiday::where('holiday_date', $date_key)
                            ->whereRaw("($department_condition_sql)")
                            ->where('designation_name', '=', '*')
                            ->get()
                            ->toArray();

                            if(empty($Check_if_hoildays_exists)){
                            
                                $other_holidays = Holiday::where('holiday_date', $date_key)->where('designation_name', '!=', '*')->exists();
                               
                                if (!empty($other_holidays)) {
                                    Holiday::where('holiday_date', $date_key)->delete();
                                }

                                $all_employees = array();

                                $all_departments_employees = Employees::whereIn('employee_department', $department_name)->where('employee_isactive', 1)->where('employee_doj','<=',$date_key)->pluck('employee_id')->toArray();

                                if(!empty($all_departments_employees)){

                                    $check_department_employee_payroll = array();

                                    foreach($all_departments_employees as $check_all_employees){
                                        
                                        $payroll_exists = $this->check_if_payroll_exist($date_key,$check_all_employees,$custom_message = 'True');
                                        
                                        if($payroll_exists['status'] == FALSE ){
                                            array_push($check_department_employee_payroll,$check_all_employees);
                                        }else{
                                            $check_any_employee_payroll = true;
                                        }
                                    }

                                    if(!empty($check_department_employee_payroll)){

                                        array_push($all_employees,$check_department_employee_payroll);
                                    }
                                }

                                if(empty($all_employees)){
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => "Holiday can't be marked. Payroll of some employees has already been generated from selected departments.",
                                    ]);
                                }

                                $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($date_key);

                                if($check_attendance_sheet_availability['status'] == FALSE){
                                    return response()->json([
                                        'msg' => $check_attendance_sheet_availability['msg'],
                                        'status' => 'FALSE'
                                    ]);
                                }
                                
                                $new_holiday = new Holiday();
                                $new_holiday->holiday_date = $date_key;
                                $new_holiday->holiday_name = $request->input('holiday_reason');
                                $new_holiday->department_name = implode(',', $department_name);;
                                $new_holiday->designation_name = '*';
                                $new_holiday->holiday_created_at = date('Y-m-d H:i:s');

                                if ($request->input('overtime_applicable') == 1) {

                                    $new_holiday->is_overtime_applicable = 1;
                                
                                    if ($request->input('overtime_payment_type') == 1) {
                                        $new_holiday->overtime_payment_type = 1;
                                    } else {
                                        $new_holiday->overtime_payment_type = 0;
                                    }
                                
                                    $new_holiday->holiday_overtime_amount = $request->input('holiday_overtime_amount');
                                    
                                }else {
                                    $new_holiday->is_overtime_applicable = 0;
                                    $new_holiday->overtime_payment_type = null;
                                    $new_holiday->holiday_overtime_amount = null;
                                }

                                $process = $new_holiday->save();

                                $day = date("d", strtotime($date_key));

                                $month = date("m", strtotime($date_key));
                                
                                $year = date("Y", strtotime($date_key));
                                
                                if(!empty($all_employees)){
                                    foreach($all_employees as $employee){
                                        foreach($employee as $emp){
                                            
                                            if($request->input('all_holiday') == "on"){
                                                Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->delete();
                                            }else{
                                                Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->where('att_check_overall_status','Absent')->delete();
                                            }
                                            $get_attendance = Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->get(['attendance_id'])->toArray();
                                            $employee_shift_start = $this->get_employee_today_shift($emp,"in",$date_key);
                                            $employee_shift_end = $this->get_employee_today_shift($emp,"out",$date_key);
                                            if(empty($get_attendance)){
                                                $update_attendance = new Attendance();
                                                $update_attendance->att_employee_id = $emp;
                                                $update_attendance->att_check_in_time = '00:00:00';
                                                $update_attendance->att_check_out_time = '00:00:00';
                                                $update_attendance->att_check_in_status = 0;
                                                $update_attendance->att_check_out_status = 0;
                                                $update_attendance->att_check_out_source = 1;
                                                $update_attendance->att_shift_start_time = $employee_shift_start;
                                                $update_attendance->att_shift_end_time = $employee_shift_end;
                                                $update_attendance->att_check_overall_status = 'Holiday : '.$request->input('holiday_reason').'';
                                                $update_attendance->att_for_date = $day;
                                                $update_attendance->att_for_month = $month;
                                                $update_attendance->att_for_year = $year;
                                                $update_attendance->att_comp_date = $year . '-' . $month . '-' . $day;
                                                $update_attendance->save();
                                            }
                                        }
                                    }     
                                }
                            }else{

                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => 'Holiday addition failed. Holiday already exist on this date(s).',
                                ]);
                            }   
                        }
                        
                    }
                }
          
                if(!empty($process) && $process == 1){

                    DB::commit();

                    if($check_any_employee_payroll == false){
                        $msg = 'Holiday(s) marked successfully.';
                    }else{
                        $msg = 'Holiday(s) marked successfully, except on those employee(s) whose payroll for these date(s) are not generated.';
                    }

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => $msg,
                    ]);
                    
                }else{

                    DB::rollBack(); 

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Holiday addition failed. Holiday already exist on this date(s).',
                    ]);
                }
               
            }   
        }catch(\Exception $e){

            DB::rollBack(); 

            $this->create_error_log("Add Holiday", $e);
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
            ActivityTracker::track_activity('Add Holiday'); 
        }

    }

    // protected function add_holiday_request(Request $request){
       
    //     try{

    //         $validator = Validator::make($request->all(), [

    //             'days_assign' => 'required',
    //             'from_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:to_date' : '' ,
    //             'to_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d' : '' ,
    //             'holiday_date' => ($request->input('days_assign') == 'single' ) ? 'required' : '' ,
    //             'holiday_reason' => 'required|string|max:30',
    //             'department_name' => 'required',
    //             'overtime_applicable' => 'nullable|in:1',
    //             'overtime_payment_type' => ($request->input('overtime_applicable') == 1 ) ? 'required|in:0,1' : '' ,
    //             'holiday_overtime_amount' => [
    //                 function ($attribute, $value, $fail) use ($request) {
    //                     if($request->input('overtime_applicable') == 1) {
    //                         if ($request->has('overtime_payment_type') && empty($value)) {
    //                             $message = ($request->input('overtime_payment_type') == 0) ? 'Number of days field is required.' : 'Holiday overtime amount field is required.';
    //                             $fail($message);
    //                         } elseif ($request->input('overtime_payment_type') == 0 && ($value < 1 || $value > 100)) {
    //                             $fail('The number of days must be between 1 and 100.');
    //                         } elseif ($request->input('overtime_payment_type') == 1 && ($value < 1 || $value > 999999)) {
    //                             $fail('The holiday overtime amount must be between 1 and 999999.');
    //                         }
    //                     }
    //                 },
    //             ],
                

    //         ]);
                
    //         if($validator->fails()) {
    //             foreach($validator->errors()->all() as $error){
    //                 return response()->json([
    //                     'msg' => $error,
    //                     'status' => 'FALSE'
    //                 ]);
    //             }
    //         }else {

    //             $check_any_employee_payroll = false;

    //             $unset_department_count = 0;

    //             $request_department_name = $request->input('department_name');

    //             foreach($request->input('department_name') as $key => $departments){

    //                 $check_if_employees_exist_on_depart = Employees::where('employee_department', $departments)->where('employee_isactive', 1)->get(['employee_id'])->toArray();

    //                 if(empty($check_if_employees_exist_on_depart)){
    //                     if(count($request->input('department_name')) == 1){

    //                         return response()->json([
    //                             'status' => 'FALSE',
    //                             'msg' => 'Employee(s) not exist on this department.',
    //                         ]);
    //                     }else{

    //                         $unset_department_count ++;

    //                         unset($request_department_name[$key]);
    //                     }
                       
    //                 }
    //             }
    //             $request_department_name;

    //             if(count($request_department_name)==$request->input('count')){
    //                 $department_name = "*";
                    
    //             }else{
    //                 $department_name = implode(',', $request_department_name);
                    
    //             }
            
    //             $day = date("d", strtotime($request->input('holiday_date')));

    //             $month = date("m", strtotime($request->input('holiday_date')));
                
    //             $year = date("Y", strtotime($request->input('holiday_date')));

    //             if(!empty($request->input('from_date')) && !empty($request->input('to_date'))){
    //                 $no_of_days = $this->count_days($request->input('from_date'),$request->input('to_date'));
    //                 $start_date =  $request->input('from_date');
    //             }else{
    //                 $no_of_days = 1;
    //                 $start_date = $request->input('holiday_date');
    //             }

    //             $checking_request_dates_department = array();

    //             for($i = 0; $i<$no_of_days; $i++){

    //                 $dates = date('Y-m-d', strtotime('+'.$i.' day',  strtotime($start_date)));

    //                 $checking_request_department = array();
                    
    //                     foreach($request_department_name as $department_id){
                           
    //                         if(count($request_department_name)==$request->input('count') || $department_name == '*'){
    //                             $department_id = ["*"];
                               
    //                         }else{

    //                             $checking_request_dates = Holiday::where('holiday_date',$dates)->where(function($query) use ($department_id) { $query->where('department_name', 'like', "%".$department_id."%"); $query->orwhere('department_name', 'like', '%*%'); })->get(['holiday_date', 'department_name'])->toArray();

    //                             if(empty($checking_request_dates)){

    //                                 array_push($checking_request_department, $department_id);

    //                             }
                                 
    //                             $check_department_name = Department::where('department_id', $department_id)->get(['department_id', 'department_name'])->toArray();

    //                             if((count($request_department_name) + $unset_department_count) != $request->input('count')){

    //                                 if($department_name != '*'){
    //                                     if(!empty($checking_request_dates) && $no_of_days == 1 ){
                    
    //                                         return response()->json([
    //                                             'status' => 'FALSE',
    //                                             'msg' =>  'Holiday already exists for '.$check_department_name[0]['department_name']. ' department on this date.',
    //                                         ]);
                                            
    //                                     }
    //                                 } 
    //                             }  
    //                         }
                        
    //                     }
                       
    //                 $checking_request_dates_department[$dates]= $checking_request_department;
    //             }

    //             foreach($checking_request_dates_department  as $date_key => $request_department){

    //                 if(!empty($request_department)){

    //                     $all_employees = array();

    //                     $unset_department_payroll_count = 0;

    //                     foreach($request_department as $department){
                          
    //                         $all_departments_employees = Employees::where('employee_department', $department)->where('employee_isactive', 1)->where('employee_doj','<=',$date_key)->pluck('employee_id')->toArray();

    //                         if(!empty($all_departments_employees)){

    //                             $check_department_employee_payroll = array();

    //                             foreach($all_departments_employees as $check_all_employees){
                                    
    //                                 $payroll_exists = $this->check_if_payroll_exist($date_key,$check_all_employees,$custom_message = 'True');
                                    
    //                                 if($payroll_exists['status'] == FALSE ){
    //                                     array_push($check_department_employee_payroll,$check_all_employees);
    //                                 }else{
    //                                     $check_any_employee_payroll = true;
    //                                 }
    //                             }
    //                             if(!empty($check_department_employee_payroll)){
    //                                 array_push($all_employees,$check_department_employee_payroll);
    //                             }else{
    //                                 unset($request_department[$unset_department_payroll_count]);
    //                             }
                                
    //                         }
    //                         $unset_department_payroll_count ++;
    //                     }
                      
    //                     if(empty($all_employees)){
    //                         return response()->json([
    //                             'status' => 'FALSE',
    //                             'msg' => "Holiday can't be marked. Payroll of some employees has already been generated from selected departments.",
    //                         ]);     
    //                     }

    //                     $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($date_key);

    //                     if($check_attendance_sheet_availability['status'] == FALSE){
    //                         return response()->json([
    //                             'msg' => $check_attendance_sheet_availability['msg'],
    //                             'status' => 'FALSE'
    //                         ]);
    //                     }

    //                     $new_holiday = new Holiday();
    //                     $new_holiday->holiday_date = $date_key;
    //                     $new_holiday->holiday_name = $request->input('holiday_reason');
    //                     $new_holiday->department_name = implode(',', $request_department);
    //                     $new_holiday->holiday_created_at = date('Y-m-d H:i:s');
    //                     $new_holiday->holiday_created_by = session('user_session');

    //                     if ($request->input('overtime_applicable') == 1) {
    //                         $new_holiday->is_overtime_applicable = 1;
                        
    //                         if ($request->input('overtime_payment_type') == 1) {
    //                             $new_holiday->overtime_payment_type = 1;
    //                         } else {
    //                             $new_holiday->overtime_payment_type = 0;
    //                         }
                        
    //                         $new_holiday->holiday_overtime_amount = $request->input('holiday_overtime_amount');
    //                     } else {
    //                         $new_holiday->is_overtime_applicable = 0;
    //                         $new_holiday->overtime_payment_type = null;
    //                         $new_holiday->holiday_overtime_amount = null;
    //                     }

    //                     $process = $new_holiday->save();

    //                     $day = date("d", strtotime($date_key));

    //                     $month = date("m", strtotime($date_key));
                        
    //                     $year = date("Y", strtotime($date_key));

    //                     if(!empty($all_employees)){
    //                         foreach($all_employees as $employee){
    //                             foreach($employee as $emp){

    //                                 if($request->input('all_holiday') == "on"){
    //                                     Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->delete();
    //                                 }else{
    //                                     Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->where('att_check_overall_status','Absent')->delete();
    //                                 }
                                    

    //                                 $get_attendance = Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->get(['attendance_id'])->toArray();
    //                                 $employee_shift_start = $this->get_employee_today_shift($emp,"in",$date_key);
    //                                 $employee_shift_end = $this->get_employee_today_shift($emp,"out",$date_key);
    //                                 if(empty($get_attendance)){
    //                                     $update_attendance = new Attendance();
    //                                     $update_attendance->att_employee_id = $emp;
    //                                     $update_attendance->att_check_in_time = '00:00:00';
    //                                     $update_attendance->att_check_out_time = '00:00:00';
    //                                     $update_attendance->att_check_in_status = 0;
    //                                     $update_attendance->att_check_out_status = 0;
    //                                     $update_attendance->att_check_out_source = 1;
    //                                     $update_attendance->att_shift_start_time = $employee_shift_start;
    //                                     $update_attendance->att_shift_end_time = $employee_shift_end;
    //                                     $update_attendance->att_check_overall_status = 'Holiday : '.$request->input('holiday_reason').'';
    //                                     $update_attendance->att_for_date = $day;
    //                                     $update_attendance->att_for_month = $month;
    //                                     $update_attendance->att_for_year = $year;
    //                                     $update_attendance->att_comp_date = $year . '-' . $month . '-' . $day;
    //                                     $update_attendance->save();
    //                                 }
    //                             }
    //                         }     
    //                     }

    //                 }else{
    //                     if($department_name == '*'){

    //                         Holiday::where('holiday_date', $date_key)->delete();

    //                         $all_employees = array();

    //                         $all_departments_employees = Employees::where('employee_isactive', 1)->where('employee_doj','<=',$date_key)->pluck('employee_id')->toArray();

    //                         if(!empty($all_departments_employees)){

    //                             $check_department_employee_payroll = array();

    //                             foreach($all_departments_employees as $check_all_employees){
                                    
    //                                 $payroll_exists = $this->check_if_payroll_exist($date_key,$check_all_employees,$custom_message = 'True');
                                    
    //                                 if($payroll_exists['status'] == FALSE ){
    //                                     array_push($check_department_employee_payroll,$check_all_employees);
    //                                 }else{
    //                                     $check_any_employee_payroll = true;
    //                                 }
    //                             }

    //                             if(!empty($check_department_employee_payroll)){

    //                              array_push($all_employees,$check_department_employee_payroll);
    //                             }
    //                         }

    //                         if(empty($all_employees)){
    //                             return response()->json([
    //                                 'status' => 'FALSE',
    //                                 'msg' => "Holiday can't be marked. Payroll of some employees has already been generated from selected departments.",
    //                             ]);
    //                         }

    //                         $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($date_key);

    //                         if($check_attendance_sheet_availability['status'] == FALSE){
    //                             return response()->json([
    //                                 'msg' => $check_attendance_sheet_availability['msg'],
    //                                 'status' => 'FALSE'
    //                             ]);
    //                         }
                            
    //                         $new_holiday = new Holiday();
    //                         $new_holiday->holiday_date = $date_key;
    //                         $new_holiday->holiday_name = $request->input('holiday_reason');
    //                         $new_holiday->department_name = '*';
    //                         $new_holiday->holiday_created_at = date('Y-m-d H:i:s');

    //                         if ($request->input('overtime_applicable') == 1) {

    //                             $new_holiday->is_overtime_applicable = 1;
                            
    //                             if ($request->input('overtime_payment_type') == 1) {
    //                                 $new_holiday->overtime_payment_type = 1;
    //                             } else {
    //                                 $new_holiday->overtime_payment_type = 0;
    //                             }
                            
    //                             $new_holiday->holiday_overtime_amount = $request->input('holiday_overtime_amount');
                                
    //                         }else {
    //                             $new_holiday->is_overtime_applicable = 0;
    //                             $new_holiday->overtime_payment_type = null;
    //                             $new_holiday->holiday_overtime_amount = null;
    //                         }

    //                         $process = $new_holiday->save();

    //                         $day = date("d", strtotime($date_key));

    //                         $month = date("m", strtotime($date_key));
                            
    //                         $year = date("Y", strtotime($date_key));
                            
    //                         if(!empty($all_employees)){
    //                             foreach($all_employees as $employee){
    //                                 foreach($employee as $emp){
                                        
    //                                     if($request->input('all_holiday') == "on"){
    //                                         Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->delete();
    //                                     }else{
    //                                         Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->where('att_check_overall_status','Absent')->delete();
    //                                     }
    //                                     $get_attendance = Attendance::where('att_employee_id', $emp)->where('att_for_date', $day)->where('att_for_month', $month)->where('att_for_year', $year)->get(['attendance_id'])->toArray();
    //                                     $employee_shift_start = $this->get_employee_today_shift($emp,"in",$date_key);
    //                                     $employee_shift_end = $this->get_employee_today_shift($emp,"out",$date_key);
    //                                     if(empty($get_attendance)){
    //                                         $update_attendance = new Attendance();
    //                                         $update_attendance->att_employee_id = $emp;
    //                                         $update_attendance->att_check_in_time = '00:00:00';
    //                                         $update_attendance->att_check_out_time = '00:00:00';
    //                                         $update_attendance->att_check_in_status = 0;
    //                                         $update_attendance->att_check_out_status = 0;
    //                                         $update_attendance->att_check_out_source = 1;
    //                                         $update_attendance->att_shift_start_time = $employee_shift_start;
    //                                         $update_attendance->att_shift_end_time = $employee_shift_end;
    //                                         $update_attendance->att_check_overall_status = 'Holiday : '.$request->input('holiday_reason').'';
    //                                         $update_attendance->att_for_date = $day;
    //                                         $update_attendance->att_for_month = $month;
    //                                         $update_attendance->att_for_year = $year;
    //                                         $update_attendance->att_comp_date = $year . '-' . $month . '-' . $day;
    //                                         $update_attendance->save();
    //                                     }
    //                                 }
    //                             }     
    //                         }

    //                     }
                        
    //                 }
    //             }
          
    //             if(!empty($process) && $process == 1){

    //                 if($check_any_employee_payroll == false){
    //                     $msg = 'Holiday(s) marked successfully.';
    //                 }else{
    //                     $msg = 'Holiday(s) marked successfully, except on those employee(s) whose payroll for these date(s) are not generated.';
    //                 }

    //                 return response()->json([
    //                     'status' => 'TRUE',
    //                     'msg' => $msg,
    //                 ]);
                    
    //             }else{
    //                 return response()->json([
    //                     'status' => 'FALSE',
    //                     'msg' => 'Holiday addition failed. Holiday already exist on this date(s).',
    //                 ]);
    //             }
               
    //         }   
    //     }catch(\Exception $e){
    //         $this->create_error_log("Add Holiday", $e);
    //         if(!config('app.debug')){
    //             return response()->json([
    //                 'status' => 'FALSE',
    //                 'msg' => "Something went wrong. Please try again.",
                   
    //             ]);
    //         }else{
    //             return response()->json([
    //                 'status' => 'FALSE',
    //                 'msg' => "Something went wrong. Please try again.",
    //                 'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
    //             ]);
               
    //         }
    //     }finally{
    //         ActivityTracker::track_activity('Add Holiday'); 
    //     }

    // }

    protected function count_days($from_date, $to_date){

        $from_date = strtotime($from_date);
        $to_date = strtotime($to_date);
        
        $timeDiff = abs($to_date - $from_date);
        
        $numberDays = $timeDiff/86400;
        
        $no_of_days = intval($numberDays + 1);
        
        return $no_of_days;
    }

}
