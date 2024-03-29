<?php

namespace App\Helpers;
use Request;
use App\Models\Employees;
use App\Models\Permission;
use App\Models\Interview;
use App\Models\Attendance;
use App\Models\Notification;
use App\Models\MeetingUsers;
use App\Models\Meeting;
use App\Models\Resignation;
use App\Models\HrmSetting;
use App\Models\LoginHistory;
use App\Models\Department;
use App\Models\ShiftManagement;
use App\Models\EmployeeSalary;
use App\Models\LeaveManagement;
use App\Models\SystemModule;
use App\Models\InterviewCandidate;
use App\Models\OnBoardingEmployee;
use App\Models\OfficeLocation;
use App\Traits\{LogGenerateTrait,LoginEmployee,AttendanceEmployee};
use App\Helpers\ActivityTracker;
use DatePeriod,DateTime,DateInterval;

use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

use Carbon\Carbon;


use App\Models\ErrorLog;
use Illuminate\Support\Facades\URL;
use Exception;

class CustomHelper
{
    public static function dateformatDFY($date){
        return date('d, F Y',strtotime($date));
    }

    public static function getEmpProfileDiv($employee_id){
        try{

            $employeeData = Employees::join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id')
            ->join('tbl_departments', 'tbl_employees.employee_department', '=', 'tbl_departments.department_id')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('tbl_employees.employee_id', $employee_id)
            ->get(['employee_id','employee_code','employee_name','profile_avatar','designation_name as employee_designation','department_name'])
            ->toArray();
    
            if(!empty($employeeData)){
                $profile = 'null';
                if(!empty($employeeData[0]['profile_avatar'])){
                    if(file_exists('portal_assets/profile_pictures/'.$employeeData[0]['profile_avatar'])){
                        $profile = $employeeData[0]['profile_avatar'];
                    }
                }
    
                $employeeProfileDiv = "<span href='javascript:;' class='text-dark fw-bold mb-1 fs-6 employee-hover' style='cursor:pointer;' data-profile='".  $profile."' data-name='".$employeeData[0]['employee_name']." - (".$employeeData[0]['employee_code'].")' data-department='".$employeeData[0]['department_name']."' data-designation='".$employeeData[0]['employee_designation']."'>".$employeeData[0]['employee_name']."</span>";
            }else{
                $employeeProfileDiv = "<span href='javascript:;' class='text-dark fw-bold mb-1 fs-6 employee-hover' style='cursor:pointer;' data-profile='N/A' data-name='N/A' data-department='N/A' data-designation='N/A'>N/A</span>";
            }
          
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get Employee Profile Div', $e);
    
        }
        finally{
        
            return $employeeProfileDiv;
        }
       
    }

    public static function getInterviewCandidateDiv($candidate_id){
        try{

            $candidateData = InterviewCandidate::where('candidate_id',$candidate_id)->get(['candidate_name','candidate_contact_no'])->toArray();
            
            if(!empty($candidateData)){
              
                $candidateDetail = "<span href='javascript:;' class='text-dark fw-bold mb-1 fs-6 candidate-hover' style='cursor:pointer;'  data-name='".$candidateData[0]['candidate_name']."' data-contact='".$candidateData[0]['candidate_contact_no']."'>".$candidateData[0]['candidate_name']."</span>";
            }else{
                $candidateDetail = "<span href='javascript:;' class='text-dark fw-bold mb-1 fs-6 candidate-hover' style='cursor:pointer;'  data-name='N/A' data-contact='N/A' >N/A</span>";
            }
          
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get Employee Profile Div', $e);
    
        }
        finally{
        
             return  $candidateDetail;
        }
       
    }


    public static function access_permission(){
        $get_permission_details = Permission::join('tbl_system_modules','tbl_employee_permission.module_url','=','tbl_system_modules.module_url')->where('employee_id', session('user_session'))->get(['permission_status','module_accessibility','tbl_employee_permission.module_url'])->toArray();
        if(!empty($get_permission_details)){
            return $get_permission_details;
        }
    }


    public static function access_permission_from_api($employee_id){
        $get_permission_details = Permission::join('tbl_system_modules','tbl_employee_permission.module_url','=','tbl_system_modules.module_url')->where('employee_id',$employee_id)->get(['permission_status','module_accessibility'])->toArray();
        if(!empty($get_permission_details)){
            return $get_permission_details;
        }
    }



    public static function attendance_summary_employee(){

        try{
           
            $employee_attendance =  Attendance::where(['att_employee_id'=>session('user_session'),'att_for_month'=>date('m'),'att_for_year'=>date('Y')])->pluck('att_for_date')->toArray();

            $t_employee_present = Attendance::where(['att_employee_id'=>session('user_session'),'att_for_month'=>date('m'),'att_for_year'=>date('Y')])->whereIn('att_check_overall_status' ,array('Present','Late Coming','Half Day','Early Out'))->count();

            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
            array_push($manage_leaves, "RPL");
          
            $t_employee_leaves = Attendance::where(['att_employee_id'=>session('user_session'),'att_for_month'=>date('m'),'att_for_year'=>date('Y')])->whereIn('att_check_overall_status' , $manage_leaves)->count();
          
            $employee_offDays = Employees::where('employee_id',session('user_session'))->get(['employee_offdays'])->toArray();

            $month = date('d');
            $offDays = 0;
            $absent = 0;
            $employee_offDays[0]['employee_offdays'] = (!empty($employee_offDays[0]['employee_offdays'])) ? explode(",",$employee_offDays[0]['employee_offdays']):[];
            for ($month_count = 1; $month_count <=$month; ++$month_count){
                $date =  $month_count;
                if(!in_array($date,$employee_attendance)){
                    $nameOfDay = date("l", strtotime(date($date.'-m-Y')));

                    if(in_array($nameOfDay,$employee_offDays[0]['employee_offdays']) ){
                        $offDays++;
                    }else{
                        $absent++;
                    }
                }
            }
           
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Attendance Summary Employee', $e);

        }
        finally{
            
            return ['present' => $t_employee_present ,'leave' => $t_employee_leaves,'offdays' => $offDays,'absent'=>$absent];
        }

       
    }

    public static function attendance_summary_employee_from_api($employee_id){
        
        $employee_attendance =  Attendance::where(['att_employee_id'=>$employee_id,'att_for_month'=>date('m'),'att_for_year'=>date('Y')])->pluck('att_for_date')->toArray();

        $t_employee_present = Attendance::where(['att_employee_id'=>$employee_id,'att_for_month'=>date('m'),'att_for_year'=>date('Y')])->whereIn('att_check_overall_status' ,array('Present','Late Coming','Half Day','Early Out'))->count();

        $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
        array_push($manage_leaves, "RPL");
        $t_employee_leaves = Attendance::where(['att_employee_id'=>$employee_id,'att_for_month'=>date('m'),'att_for_year'=>date('Y')])->whereIn('att_check_overall_status' ,$manage_leaves)->count();

        $employee_offDays = Employees::where('employee_id',$employee_id)->get(['employee_offdays'])->toArray();

        $employee_offDays[0]['employee_offdays'] = (!empty($employee_offDays[0]['employee_offdays'])) ? explode(",",$employee_offDays[0]['employee_offdays']):[];

        $month = date('d');
        $offDays = 0;
        $absent = 0;
        for ($month_count = 1; $month_count <=$month; ++$month_count){
            $date =  $month_count;
            if( !in_array($date,$employee_attendance)){
                $nameOfDay = date("l", strtotime(date($date.'-m-Y')));

                if(in_array($nameOfDay,$employee_offDays[0]['employee_offdays']) ){
                    $offDays++;
                }else{
                    $absent++;
                }
            }
        }
    
        return ['present' => $t_employee_present ,'leave' => $t_employee_leaves,'offdays' => $offDays,'absent'=>$absent];
    

       
    }

    public static function get_my_todays_attendance_status(){
        try{
            date_default_timezone_set("Asia/Karachi");
            $day = date("d");
            $month = date("m");
            $year = date("Y");
            $login_user = session('user_session');

            $employee_shift_start = SELF::get_employee_current_shift_start($login_user,date('Y-m-d'));

            if(date('H:i') > $employee_shift_start){
                $get_attendance = Attendance::where('att_employee_id',$login_user)->where('att_for_date',$day)->where('att_for_month',$month)->where('att_for_year',$year)->get(['attendance_id'])->toArray();
                if(!empty($get_attendance)){
                    return '1';
                }else{
                    return '0';
                }
            }else{
                return '1';
            }

        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get My Today Attendance Status ', $e);

        }
        finally{
        
        }
    }

    public static function calculate_working_hour($att_checkout_status,$start_time,$end_time,$att_created_date,$att_updated_date,$shift_start_time = array()){
        try{

            if($att_checkout_status == '1'){
                if(empty($att_updated_date)){
                    $att_updated_date = '2023-01-01 09:00:00';
                    $att_updated_date = date_format(date_create($att_updated_date),"Y-m-d");
                }else{
                    $att_updated_date = date_format(date_create($att_updated_date),"Y-m-d");
                }
                $att_created_date = date_format(date_create($att_created_date),"Y-m-d");
    
                if(strtotime($att_updated_date) > strtotime($att_created_date) ){
                 
                    $datetime_1 = '2022-12-10 '. $start_time;
                    $datetime_2 = '2022-12-11 ' . $end_time;
                    $start_datetime = new DateTime($datetime_1);
                    $diff = $start_datetime->diff(new DateTime($datetime_2));
                    $days = $diff->format('%d');
    
                    if($days > 0){
                        
                        $hours =  $days * 24;
                        $hours += $diff->format('%H');
                        $minutes = $diff->format('%I');
                        $seconds = $diff->format('%S');
                        return $hours. ":" . $minutes . ":" . $seconds;
                    }else{
                        
                        return $diff->format('%H:%I:%S');
                    }
                    
    
                }else{
                    if(strtotime($start_time) < strtotime($end_time) ){
    
                        $datetime_1 = '2022-12-10 '. $start_time;
                        $datetime_2 = '2022-12-10 ' . $end_time;
                        $start_datetime = new DateTime($datetime_1);
                        $diff = $start_datetime->diff(new DateTime($datetime_2));
                        return $diff->format('%H:%I:%S');
                    }elseif($start_time == '00:00:00' && $end_time = '00:00:00'){
                        return "24:00:00";
                    }
                    else{
                        $datetime_1 = '2022-12-10 '. $start_time;
                        $datetime_2 = '2022-12-11 ' . $end_time;
                        $start_datetime = new DateTime($datetime_1);
                        $diff = $start_datetime->diff(new DateTime($datetime_2));
                        $days = $diff->format('%d');
                        if($days > 0){
                        
                            $hours =  $days * 24;
                            $hours += $diff->format('%H');
                            $minutes = $diff->format('%I');
                            $seconds = $diff->format('%S');
                            return $hours. ":" . $minutes . ":" . $seconds;
                        }else{
                            
                            return $diff->format('%H:%I:%S');
                        }
    
                    }
                    return '00:00:00';
    
                }
            }
            
            return '00:00:00';
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Calculate Working Hour', $e);

        }
        finally{
        
        }

    }

    public static function calculate_shift_timings_shift_management($start_time,$end_time,$checkin_status = Null,$checkout_status = Null){
        try{
            if($checkin_status == 1 || $checkin_status == Null || $checkout_status == 1 || $checkout_status == Null ){
                if(strtotime($start_time) < strtotime($end_time) ){
                    $datetime_1 = '2022-12-10' . $start_time;
                    $datetime_2 = '2022-12-10' . $end_time;
                    $start_datetime = new DateTime($datetime_1);
                    $diff = $start_datetime->diff(new DateTime($datetime_2));

                    return $diff->format('%H:%I:%S');


                }elseif($start_time == '00:00:00' && $end_time = '00:00:00'){
                    return "24:00:00";
                
                }else{

                    $datetime_1 = '2022-12-10 ' . $start_time;
                    $datetime_2 = '2022-12-11 ' . $end_time;
                    $start_datetime = new DateTime($datetime_1);
                    $diff = $start_datetime->diff(new DateTime($datetime_2));

                    return $diff->format('%H:%I:%S');

                }
            }

            return '00:00:00';
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Calculate Shift Timing', $e);

        }
        finally{
        
        }

    }

    // public static function calculate_working_hour($att_for_year,$att_for_month,$att_for_date,$att_check_in_time,$att_check_out_time,$employee_shift_start,$employee_shift_end,$month_count,$att_check_overall_status,$request_m,$request_y,$total_working_hours,$total_working_hours_actual,$employee_offdays,$helper_absent,$attendance_total_count,$loop_count,$check_out_status,$att_updated_date){

       
    //     $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

    //     $absent = true;

    //     $in_day_time2 = $att_for_year.'-'.$att_for_month.'-'.$att_for_date;

    //     $in_day_time = $att_for_year.'-'.$att_for_month.'-'.$att_for_date.' '.$att_check_in_time;
    //     $out_day_time = $att_for_year.'-'.$att_for_month.'-'.$att_for_date.' '.$att_check_out_time;

    //     $in_day_time_actual = $att_for_year.'-'.$att_for_month.'-'.$att_for_date.' '.$employee_shift_start;
    //     $out_day_time_actual = $att_for_year.'-'.$att_for_month.'-'.$att_for_date.' '.$employee_shift_end;

    //     $f_month = date('m');
    //     $f_year = date('Y');

    //     if (!empty($request_m)){
    //         $f_month = $request_m;
    //     }

    //     if (!empty($request_y)){
    //         $f_year = $request_y;
    //     }

    //     $date_filter = strtotime(date("Y-m-d", strtotime($f_year . "-" . $f_month . "-" . $month_count )));
    //     $nameOfDay = date('l' , $date_filter);

    //     if(  strtotime($att_check_in_time) > strtotime($att_check_out_time) ){

    //         $out_day_time = date('Y-m-d H:i:s',strtotime($out_day_time.' +1 days'));

    //     }

    //     if( (strtotime($employee_shift_start) > strtotime($employee_shift_end))){

    //         $out_day_time_actual = date('Y-m-d H:i:s',strtotime($out_day_time_actual.' +1 days'));

    //     }




    //     $in_day_time = strtotime($in_day_time);
    //     $out_day_time = strtotime($out_day_time);

    //     $in_day_time_actual = strtotime($in_day_time_actual);
    //     $out_day_time_actual = strtotime($out_day_time_actual);

    //     $total_working_hour_of_this_day = "00:00:00";

    //     if ( $att_for_date == $month_count){
    //         $absent = false;

    //         // $day ++;
    //         // echo $day;
    //         // echo "Present";
    //         if($check_out_status != '0'){
            
    //             $total_working_hour_of_this_day = $out_day_time - $in_day_time;
    //             $total_working_hours += $total_working_hour_of_this_day;
    //             // $total_working_hour_of_this_day = date('H:i',$total_working_hour_of_this_day);

    //             $total_working_hour_of_this_day = floor($total_working_hour_of_this_day / 60);
    //             $total_working_hour_of_this_day_hours = floor($total_working_hour_of_this_day / 60);
    //             $total_working_hour_of_this_day_minutes = ($total_working_hour_of_this_day % 60);
    //             $total_working_hour_of_this_day_seconds= ($total_working_hour_of_this_day_minutes * 60);
    //             // $total_working_hour_of_this_day = $total_working_hour_of_this_day_hours.':'.((strlen($total_working_hour_of_this_day_minutes)==1) ? '0'.$total_working_hour_of_this_day_minutes : $total_working_hour_of_this_day_minutes);
    //             $total_working_hour_of_this_day = $total_working_hour_of_this_day_hours.':'.sprintf('%02d', $total_working_hour_of_this_day_minutes).':'.sprintf('%02d', $total_working_hour_of_this_day_seconds);

    //         }
    //         $total_working_hour_of_this_day_actual = $out_day_time_actual - $in_day_time_actual;
    //         $total_working_hours_actual += $total_working_hour_of_this_day_actual;

    //         if(in_array($att_check_overall_status, $manage_leaves) || substr($att_check_overall_status , 0, 7) == 'Holiday' || in_array($nameOfDay,$employee_offdays)){
    //             // echo "Leave";
    //             $total_working_hour_of_this_day_actual = $out_day_time_actual - $in_day_time_actual;
    //             $total_working_hours_actual -= $total_working_hour_of_this_day_actual;

    //         }
    //     }else{
    //         $loop_count++;
    //         // echo $loop_count;
    //     }

    //         if($absent && $helper_absent==false && $loop_count==$attendance_total_count){
    //             // $day ++;
    //             $helper_absent = true;
    //             // echo "Main Absent";

    //             $today = strtotime(date("Y-m-d"));

    //             if(in_array($nameOfDay,$employee_offdays) ){
    //                 // $total_working_hour_of_this_day_actual = $out_day_time_actual - $in_day_time_actual;
    //                 // $total_working_hours_actual += $total_working_hour_of_this_day_actual;
    //                 // echo "off day";
    //             }
    //             elseif($date_filter < $today ){
    //                 // echo "Absent";
    //                 // echo "else";
    //                 $total_working_hour_of_this_day_actual = $out_day_time_actual - $in_day_time_actual;
    //                 $total_working_hours_actual += $total_working_hour_of_this_day_actual;



    //             }else{
    //                 // echo "else";
    //                 // $total_working_hour_of_this_day_actual = $out_day_time_actual - $in_day_time_actual;
    //                 // $total_working_hours_actual += $total_working_hour_of_this_day_actual;
    //             }
    //         }
    //     // }

    //     $total_working_hours_actual_floor = floor($total_working_hours_actual / 60);
    //     $hours_actual = floor($total_working_hours_actual_floor / 60);
    //     $minutes_actual = ($total_working_hours_actual_floor % 60);
    //     $seconds_actual = $minutes_actual * 60;
    //     $total_working_hours_floor = floor($total_working_hours / 60);
    //     $hours = floor($total_working_hours_floor / 60);
    //     $minutes = ($total_working_hours_floor % 60);
    //     $seconds = $minutes * 60;
    //     // echo "working";
    //     // echo $day;
    //     return  [
    //         'hours_actual'=>$hours_actual.":".$minutes_actual.":".$seconds_actual,
    //         'hours_worked'=>$hours.":".$minutes.":". $seconds,
    //         'total_working_hour_of_this_day'=>$total_working_hour_of_this_day,
    //         'total_working_hours'=>$total_working_hours,
    //         'total_working_hours_actual'=>$total_working_hours_actual,
    //         'helper_absent'=>$helper_absent,
    //         'loop_count'=>$loop_count

    //     ];
    // }

    public static function calculate_employeement_duration_period($employee_doj, $end) {
        try{
            $human = '';
            $start_date = \Carbon\Carbon::parse($employee_doj);
            $end_date = \Carbon\Carbon::parse($end);
            $years = $end_date->diffInYears($start_date);
            if ($years > 0) {
                $human = $human.$years.' years ';
                $start_date = $start_date->addYears($years);
            }
            $months = $end_date->diffInMonths($start_date);
            if ($months > 0) {
                $human = $human.$months.' months ';
                $start_date = $start_date->addMonths($months);
            }
            $days = $end_date->diffInDays($start_date);
            if ($days > 0) {
                $human = $human.$days.' days ';
                $start_date = $start_date->addDays($days);
            }
      

        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Calculate Employeement Duration Period', $e);

        }
        finally{
            return $human;
        }
    }

    public static function get_my_todays_meeting_status(){
        try{
            $my_meetings_as_invited = MeetingUsers::join('tbl_meetings','tbl_meeting_users.users_meeting_id','=','tbl_meetings.meeting_id')
            ->join('tbl_employees','tbl_meetings.host_id','=','tbl_employees.employee_id')
            ->where('user_id', session('user_session'))->where('meeting_time', 'LIKE', '%'.date("Y-m-d").'%')
            ->get(['meet_user_id'])
            ->toArray();

            if(!empty($my_meetings_as_invited)){
                return '1';
            }else{
                return '0';
            }

        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get My Today Meeting Status', $e);

        }
        finally{

        }
    }

    public static function extra_hours_working($employee_shift_time,$employee_working_hour){
        try{
            $time_parts = explode(":", $employee_working_hour);
            $total_hours = intval($time_parts[0]);

            if(strtotime($employee_working_hour) > strtotime($employee_shift_time) || $total_hours > 24 ){

                $time_parts = explode(":", $employee_working_hour);
                $total_hours = intval($time_parts[0]);

                if($total_hours > 24 ){

                    $extra_working_minutes = intval($time_parts[1]);
                    $extra_working_seconds = intval($time_parts[2]);

                    $working_total_seconds = ($total_hours * 3600) + ($extra_working_minutes * 60) + $extra_working_seconds;

                    $shift_time_parts = explode(":", $employee_shift_time);
                    $shift_working_hours = intval($shift_time_parts[0]);
                    $shift_working_minutes = intval($shift_time_parts[1]);
                    $shift_working_seconds = intval($shift_time_parts[2]);

                    $shift_total_seconds = ($shift_working_hours * 3600) + ($shift_working_minutes * 60) + $shift_working_seconds;

                    $duration_seconds = $working_total_seconds - $shift_total_seconds;

                    $hours = floor($duration_seconds / 3600);
                    $minutes = floor(($duration_seconds % 3600) / 60);
                    $seconds = $duration_seconds % 60;

                    return $hours . ":" . $minutes . ":" . $seconds;

                }else{
                    $datetime_1 = '2022-12-10 ' . $employee_working_hour;
                    $datetime_2 = '2022-12-10 ' . $employee_shift_time;
                    $start_datetime = new DateTime($datetime_1);
                    $diff = $start_datetime->diff(new DateTime($datetime_2));
                    return $diff->format('%H:%I:%S');
                }

            }
            return '';
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Extra Working Hours', $e);

        }
        finally{

        }

    }


    public static function late_checkIn_hour($employee_shift_time_start,$employee_check_in){

        if($employee_check_in > $employee_shift_time_start){
            $datetime_1 = '2022-12-10 ' . $employee_shift_time_start;
            $datetime_2 = '2022-12-10 ' . $employee_check_in;
            $start_datetime = new DateTime($datetime_1);
            $diff = $start_datetime->diff(new DateTime($datetime_2));
            return $diff->format('%H:%I:%S');

        }

        return '';

    }

    public static function early_checkOut_hour($check_out_status,$employee_shift_time,$employee_working_hour){
        try{
            if($check_out_status == '1'){
                if($employee_shift_time  > $employee_working_hour ){

                    $datetime_1 = '2022-12-10 ' . $employee_working_hour;
                    $datetime_2 = '2022-12-10 ' . $employee_shift_time;
                    $start_datetime = new DateTime($datetime_1);
                    $diff = $start_datetime->diff(new DateTime($datetime_2));
                    return $diff->format('%H:%I:%S');
        
                }
            }
            return '';
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Early Checkout Hours', $e);

        }
        finally{

        }
       
    }

    public static function hcm_default_settings($pdflogo=Null){

        try{
            $setting = HrmSetting::get(['company_favicon','company_logo','company_name','company_address'])->toArray();

            if(!empty($setting)){

            
                $company_name =  $setting['0']['company_name'];

                $company_fav_icon = $setting['0']['company_favicon'];

                $company_logo = $setting['0']['company_logo'];

                $company_address = $setting['0']['company_address'];

                if(!empty($company_name)){

                    $company_name = $company_name;
                }

                if(!empty($company_address)){

                    $company_address = $company_address;
                }

                if($pdflogo == Null){
                    if(!empty($company_fav_icon)){

                        if(file_exists('portal_assets/setting/images/'.$company_fav_icon)){
                            $company_fav_icon =  url('portal_assets/setting/images/'.$company_fav_icon);
                        }else{
                            $company_fav_icon = url('portal_assets/setting/images/default-company-logo.png');
                        }
    
                    }
                    if(!empty($company_logo)){
    
                        if(file_exists('portal_assets/setting/images/'.$company_logo)){
                            $company_logo =  url('portal_assets/setting/images/'.$company_logo);
                        }
                        else{
                            $company_logo = url('portal_assets/setting/images/default-company-logo.png');
                        }
                    }
                }else{
                    if(!empty($company_fav_icon)){

                        if(file_exists('portal_assets/setting/images/'.$company_fav_icon)){
                            $company_fav_icon =  public_path('portal_assets/setting/images/'.$company_fav_icon);
                        }else{
                            $company_fav_icon = public_path('portal_assets/setting/images/default-company-logo.png');
                        }
    
                    }
                    if(!empty($company_logo)){
    
                        if(file_exists('portal_assets/setting/images/'.$company_logo)){
                            $company_logo =  public_path('portal_assets/setting/images/'.$company_logo);
                        }
                        else{
                            $company_logo = public_path('portal_assets/setting/images/default-company-logo.png');
                        }
                    }
                }
              
            }

        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Hcm Default Setting', $e);

        }
        finally{
            return ['company_name'=>$company_name,'company_logo'=>$company_logo,'company_favicon'=>$company_fav_icon,'company_address'=>$company_address];
        }

    }

    public static function employee_welcome_status(){
        try{
            $login_user = session('user_session');

            $get_employee = LoginHistory::where('login_history_employee_id',$login_user)->where('login_history_source', '!=', '3')->get()->count();

            if($get_employee == 1){

                return '1';

            }else{

                return '0';
            }
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Employee Welcome Status', $e);

        }
        finally{
           
        }
    }

    public static function get_department_name($department_id){
        try{
            if(!empty($department_id)){

                if($department_id == "*"){

                    return "All Departments";
                }else{

                    $department =  Department::where('department_id', $department_id)->get(['department_name'])->toArray();

                    return (!empty($department)) ? $department[0]['department_name'] : 'N/A';

                }
            }
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get Department Name', $e);

        }
        finally{
        
        }

    }

    public static function get_location_name($location_id){
        try{
            if(!empty($location_id)){

                if($location_id == "*"){

                    return "All Locations";
                }else{

                    $location =  OfficeLocation::where('location_id', $location_id)->get(['location_title'])->toArray();

                    return (!empty($location)) ? $location[0]['location_title'] : 'N/A';

                }
            }
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get Location Name', $e);

        }
        finally{
        
        }

    }


    public static function calculate_total_overtime($overtime_working_hours){
        try{
            $seconds = $overtime_working_hours;
            $H = floor($seconds / 3600);
            $i = ($seconds / 60) % 60;
            $s = $seconds % 60;
            $time =  sprintf("%02d:%02d:%02d", $H, $i,  $s);

            return[
                'overtime'=> $time,

            ];
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Calculate Total Overtime', $e);

        }
        finally{
        
        }
    }

    public static function calculate_total_overtime_monthly($all_attendaces){
        try{
            $overtime = 0;
            $time = 0;
            if (!empty($all_attendaces)){

                foreach ($all_attendaces as $key=>$atty){

                    $employee_shift_times =  Self::calculate_shift_timings_shift_management($atty['att_shift_start_time'],$atty['att_shift_end_time'],$atty['att_check_in_status'],$atty['att_check_out_status']);
                    $employee_working_hours = Self::calculate_working_hour($atty['att_check_out_status'],$atty['att_check_in_time'],$atty['att_check_out_time'],$atty['att_created_at'],$atty['att_updated_at']);

                    if( $employee_working_hours > $employee_shift_times){
                        $extra_hours = Self::extra_hours_working($employee_shift_times,$employee_working_hours);
                        $components = explode(":",$extra_hours);
                        $seconds = $components[0]*60*60 + $components[1]*60;
                        $overtime +=$seconds;
                    }
                }

                $seconds = $overtime;
                $H = floor($seconds / 3600);
                
                $i = ($seconds / 60) % 60;
                $s = $seconds % 60;
                $time =  sprintf("%02d:%02d",$H, $i);
              
            }
           
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Calculate Total Overtime Monthly', $e);

        }
        finally{
            return  $time;
        }

    }

    public static function calculate_total_overtime_salary($employee_id,$date,$shift_time,$extra_hours){
        try{
         
            $total_salray = 0;
            $month = date("m", strtotime($date));
            $year = date("Y", strtotime($date));
            $salary_amount = EmployeeSalary::where('salary_employee_id',$employee_id)->where('salary_effected_date','<=',$date)->orderBy('salary_effected_date','ASC')->get('salary_amount')->toArray();
        
            if(empty($salary_amount)){
                $salary_amount = EmployeeSalary::where('salary_employee_id',$employee_id)->whereNull('salary_effected_date')->orderBy('salary_effected_date','ASC')->get('salary_amount')->toArray();
            }
            $no_of_days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
            //Shift Time
            $shifttime = explode(':',$shift_time);
            $hours = $shifttime[0];
            $minutes = $shifttime[1];
            foreach($salary_amount as $salary){
                $per_day_salary =  $salary['salary_amount']/$no_of_days;
                $hours_to_miutes = ($hours * 60)+$minutes;
                $per_minutes_slaray_shift = $per_day_salary/$hours_to_miutes;
            }

            // Extra hours
            $extrahours = explode(':',$extra_hours);
            $ex_hours = $extrahours[0];
            $ex_minutes = $extrahours[1];
            $extra_hours_to_miutes = ($ex_hours * 60)+$ex_minutes;
            $total_salray = $per_minutes_slaray_shift * $extra_hours_to_miutes;


        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Calculate Total Overtime Salary', $e);

        }
        finally{
            return $total_salray;

        }
    }

    public static function get_employee_current_shift_start($employee_id,$date){
        try{
            $date = date('Y-m-d',strtotime($date));

            // $fetch_employee_today_shift = ShiftManagement::
            // where('employee_id',$employee_id)
            // ->whereIn('shift_start_from_date',function($query) use($date) {
            //         $query->fromRaw('tbl_shift_management as sub_table')
            //         ->selectRaw('MAX(STR_TO_DATE(shift_start_from_date,"%Y-%m-%d"))')
            //         ->whereRaw('STR_TO_DATE(shift_start_from_date,"%Y-%m-%d") <= "'.$date.'"')
            //         ->whereRaw('sub_table.employee_id = tbl_shift_management.employee_id');
            // })
            // ->get('shift_start_time')->toArray();

            $fetch_employee_today_shift = ShiftManagement::
                where('employee_id', $employee_id)
                ->whereIn('shift_start_from_date', function($query) use($date) {
                    $query->fromRaw('tbl_shift_management as sub_table')
                        ->selectRaw('MAX(CONVERT(datetime, shift_start_from_date, 120))')
                        ->whereRaw('CONVERT(datetime, shift_start_from_date, 120) <= ?', [$date])
                        ->whereRaw('sub_table.employee_id = tbl_shift_management.employee_id');
                })
                ->get('shift_start_time')
                ->toArray();

            if(!empty($fetch_employee_today_shift)){
                return $fetch_employee_today_shift[0]['shift_start_time'];
            }else{
                return "09:00:00";
            }
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get Employee Current Shift', $e);

        }
        finally{
           
        }

    }

    public static function current_formatter($amount){
        try{
            if ($amount >= 1000 && $amount <= 99999) {
                $formattedNumber = number_format($amount / 1000, 1) . ' K';
            } elseif($amount >= 100000) {
                $formattedNumber = number_format($amount / 100000, 1) . ' M';
            }else{
                $formattedNumber = $amount;
            }
       
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Current Formatter', $e);

        }
        finally{
            return $formattedNumber;
        }

    }

    public static function total_alerts_notification_records(){
        try{
           
            $interviews = Interview::where('interview_date', date('Y-m-d'))->count();

            $meetings = Meeting::where('meeting_time','like' ,'%'.date('Y-m-d').'%')->count();

            $birthdays = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=','tbl_personal_informations.employeeid')->where('employee_isactive', 1)->whereRaw('MONTH(dob) = '.date('m'))->whereRaw('LEFT(dob, 2) = '.date('d'))->count();

            $employee_work_ceremony = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_isactive', 1)
            ->whereRaw('DATEDIFF(YEAR, employee_doj, GETDATE()) >= 1')
            ->whereRaw('MONTH(employee_doj) = MONTH(GETDATE())')->whereRaw('DAY(employee_doj) = DAY(GETDATE())')
            ->count();

            $probation_completion = Employees::where('employee_isactive', 1)->where('employee_status', 2)
            ->whereRaw('DATEADD(MONTH, 3, employee_doj) = GETDATE()')
            ->count();

            $notice_period_ending = Resignation::where('resignation_status', '=', 2)->where('resignation_to_date', date('Y-m-d'))->count();

            $onboard_employees = OnBoardingEmployee::where('onboard_status',0)->where('doj', date('Y-m-d'))->count();

            $notification_alerts = array(
                'interviews' => $interviews,
                'meetings' => $meetings,
                'birthdays' => $birthdays,
                'employee_work_Ceremony' => $employee_work_ceremony,
                'probation_completion' => $probation_completion,
                'notice_period_ending' => $notice_period_ending,
                'onboard_employees' => $onboard_employees
            );
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Total Alerts Notification Records', $e);

        }
        finally{
            // return $notification_alerts;
        }
    }

    public static function employee_shift_time_calculation_absent($employee_id,$month,$year,$from_date,$to_date){
       
		try{
            $total_absent = [];
            $employee_records = Employees::where('employee_isactive',1)->where('employee_id',$employee_id)->get(['employee_offdays','employee_id','employee_doj'])->toArray();
            
            if(!empty($month) && !empty($year)){
                $month_dates = LoginEmployee::get_all_date_from_month($month,$year,$format=1);
            }else{
                $period = new DatePeriod(
                    new DateTime($from_date),
                    new DateInterval('P1D'),
                    new DateTime($to_date)
                );
                
                foreach ($period as $key => $value) {
                
                    $month_dates[] = $value->format('Y-m-d');
                   
                }
                if (!in_array($to_date, $month_dates)) {
                    $month_dates[] = $to_date;
                }
              
            }
         
            $employee_shift_start = [];
            $employee_shift_end = [];
            foreach($employee_records as $employee_record){
                if(!empty($month) && !empty($year)){
                    $employee_attendance = Attendance::selectRaw('att_for_year + CHAR(45) + att_for_month + CHAR(45) + att_for_date as date')
                    ->where('att_employee_id',$employee_record['employee_id'])
                    ->where(['att_for_month'=>$month])->where(['att_for_year'=>$year])->get()->toArray();
                       
                   
                }else{
                    $employee_attendance = Attendance::selectRaw('att_for_year + CHAR(45) + att_for_month + CHAR(45) + att_for_date as date')->where('att_employee_id',$employee_record['employee_id'])->whereRaw("DATEFROMPARTS(CONCAT(att_for_year + CHAR(45) + att_for_month + CHAR(45) + att_for_date)) >= '".$from_date ."' and DATEFROMPARTS(CONCAT(att_for_year + CHAR(45) + att_for_month + CHAR(45) + att_for_date)) <= '".$to_date."'  ")->get()->toArray();

                  
                }
              
                $employee_attendance = array_column($employee_attendance,'date');
                $employee_record['employee_offdays'] = (!empty($employee_record['employee_offdays'])) ? explode(",",$employee_record['employee_offdays']):[];
                
                foreach($month_dates as $date){
                    $date = date("Y-m-d", strtotime($date));
                
                    if($date){
                        if(!in_array($date,$employee_attendance)){
                            $nameOfDay = date("l", strtotime(date($date)));
                    
                            if(in_array($nameOfDay,$employee_record['employee_offdays'])){
                                continue;
                            }else if($date < $employee_record['employee_doj'] ){
                                continue;
                            }
                            else{
                                // array_push($employee_attendance,$date);
                                $employee_shift_start[] = AttendanceEmployee::get_employee_today_shift($employee_record['employee_id'],"in",$date);

                                $employee_shift_out[] = AttendanceEmployee::get_employee_today_shift($employee_record['employee_id'],"out",$date);
                                
                            }
                            
                        }
                    }
                    
                }
            }
            $array = [];
            $total_working_hours_actual = 0;
            foreach ($employee_shift_start as $key => $value) {
                $shift_start =  $value;
                $shift_end =  $employee_shift_out[$key];
                $calculate_shift_timings_overall = CustomHelper::calculate_shift_timings_shift_management($shift_start,$shift_end);
                $shift_components = explode(":", $calculate_shift_timings_overall);
                $shift_seconds = $shift_components[0]*60*60 + $shift_components[1]*60;
                $total_working_hours_actual +=$shift_seconds;
                
            }
		
        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Employee Shift Time Calculation Absent', $e);

        }
        finally{
            return $total_working_hours_actual;
        }
        
	}

    public static function employee_welcome_status_api($login_user_id){
       
        $get_employee = LoginHistory::where('login_history_employee_id',$login_user_id)->where('login_history_source', '!=', 3)->where('login_history_source', '!=', 1)->get()->count();

        if($get_employee == 1){

            return '1';

        }else{

            return '0';
        }
       
    }

    public static function hcm_system_currency(){
        try{
            $setting = HrmSetting::get('system_currency')->toArray();


        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get Hcm System Currency ', $e);

        }
        finally{
            return $setting;
        }

    }

    public static function hcm_sap_attendance(){
      
        try{

            $login_user = session('user_session');

            $sap_permission = Permission::where('employee_id', $login_user)->where('permission_status', '=', 1)->where('module_url', '=', '/sap-attendance')->get()->toArray();           

            if(!empty($sap_permission)){
                return $sap_permission;
            }

        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get sap permission ', $e);
        }
        
    }

    public static function hcm_attendance_adjustment(){
      
        try{

            $login_user = session('user_session');

            $att_adjustment_permission = Permission::where('employee_id', $login_user)->where('permission_status', '=', 1)->where('module_url', '=', '/attendance-adjustment')->get()->toArray();           

            if(!empty($att_adjustment_permission)){
                return $att_adjustment_permission;
            }

        }catch(\Exception $e){
            LogGenerateTrait::create_error_log('Get attendance adjustment permission ', $e);
        }
        
    }


    public static function refreshSapAttendance($start_date, $end_date, $employee_Code)
    {
        try {

            $start_date = date("Ymd", strtotime($start_date));
            $end_date = date("Ymd", strtotime($end_date));

            if ($employee_Code[0] !== '0') {
                $employee_Code = '0' . $employee_Code;
            }

            // $endpoint = env("SAP_API") . "/get-employee-sap-attendance/$employee_Code/$start_date/$end_date";

            $endpoint = Http::get("http://172.19.0.101:8080/get-employee-sap-attendance/$employee_Code/$start_date/$end_date");

            // dd($endpoint);

            // $thirtyMinutesAgo = Carbon::now()->subMinutes(30);

            // $recordExists = DB::table('sap_attendance_report')
            //     ->whereBetween('sap_attendance_report.attendance_date', [$start_date, $end_date])
            //     ->where('sap_attendance_report.emp_code', '=', $employee_Code)
            //     ->where('sap_attendance_report.updated_at', '>=', $thirtyMinutesAgo)
            //     ->exists();

            $recordExists = false; // hardcoded
            if (!$recordExists ) {    // Send HTTP GET request
                
                // $response = Http::get($endpoint);
                $response = $endpoint;
                Log::Info($response);
                // Check if the request was successful
                if ($response->successful()) {
                    // Get the response content
                    $responseData = $response->body();
                } 
                // else {

                //     // Handle the case where the request failed
                //     echo "Failed to fetch data from the SAP API.";
                // }
            }
        } catch (Exception $e) {
            $generate_error_log = new ErrorLog();
            $generate_error_log->error_log_module = 'refresh Sap Attendance';
            $generate_error_log->error_log_action_url = URL::current();
            $generate_error_log->error_log_current_url = URL::previous();
            $generate_error_log->error_log_message = $e->getMessage();
            $generate_error_log->error_log_generated_employee = (session('user_session_id')) ?? 1;
            $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
            $generate_error_log->save();
        }
    }



}
