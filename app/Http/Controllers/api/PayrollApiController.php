<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Payroll, AttendanceRule, HrmSetting, Employees, Attendance, EmployeeSalary, PayrollEarning, PayrollDeduction, LeaveManagement};
use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait};
use App\Helpers\{ActivityTracker, CustomHelper};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\{Validator,Crypt};

use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\DB;

class PayrollApiController extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait;

    const SUB_TITLE = "My Payslip";
    const MONTH_COUNT = 0;

    protected function my_payroll(Request $request){
        try{
            $title = Self::SUB_TITLE;
        
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            
            $fetch_employee_payroll = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,pc_employee_gross_salary, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`,`disburse`,pc_year,pc_month,pc_created_at FROM `tbl_payroll_calculation` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_payroll_calculation.pc_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_payroll_calculation.pc_id 
                where pc_employee_id = '".$login_user[0]['employee_id']."'
                and disburse = '2'
                group by pc_id ,pc_deduction_pc_id,pc_earning_pc_id ");

            $fetch_employee_payroll = json_decode(json_encode($fetch_employee_payroll), true);
            
            $current_calculate_option = Payroll::where('disburse',2)->where('pc_employee_id',$login_user[0]['employee_id'])->where('pc_month',date('m'))->where('pc_year',date('Y'))->get(['pc_id'])->toArray();

            if(empty($current_calculate_option)){
                $current_calculate_option = true;
            }else{
                $current_calculate_option = false;
            }

            $pervious_calculate_option = Payroll::where('disburse',2)->where('pc_employee_id',$login_user[0]['employee_id'])->where('pc_month',date('m',strtotime('-1 month')))->where('pc_year',date('Y',strtotime('-1 month')))->get(['pc_id'])->toArray();

            if(empty($pervious_calculate_option)){
                $pervious_calculate_option = true;
            }else{
                $pervious_calculate_option = false;
            }

            $data = compact('title','fetch_employee_payroll','current_calculate_option','pervious_calculate_option');
            
            return $this->api_response_json(TRUE,"View My Payslip",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View My Payslip API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('View My Payslip API',  $request->header('Bearer-Token'));
            
        }
    }

    protected function calculate_my_payroll(Request $request){
       
        try{

            $currency = CustomHelper::hcm_system_currency();

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $request->request->add(['employee_id' =>$login_user[0]['employee_id']]);

            if($request->input('month') == 1){
                $payroll_month = date('m');
                $payroll_year = date('Y');
            }else{
                $payroll_month = date('m',strtotime('-1 month'));
                $payroll_year = date('Y',strtotime('-1 month'));
            }
            
            $calculate_option = Payroll::where('disburse',2)->where('pc_employee_id',$request->input('employee_id'))->where('pc_month',$payroll_month)->where('pc_year',$payroll_year )->get()->toArray();
            if(!empty($calculate_option)){

                $message = 'Dear employee salary slip of '.date("F", mktime(0, 0, 0, $payroll_month, 10)).' , '.$payroll_year.' has been created kindly check from payslips tab.';

                return $this->api_response_json(FALSE,$message,"",200);

            }


            $date_to_update = date('Y-m-d');

            $total_payroll = 0;
            
            $fetch_system_setting = HrmSetting::get(['payroll_start_date','tax_deduction_apply','club_rule_apply'])->toArray();

            $date_to = (!empty($fetch_system_setting) && !empty($fetch_system_setting[0]['payroll_start_date']) ) ? $fetch_system_setting[0]['payroll_start_date'] : 1;

            $club_rule_is_applied  = (!empty($fetch_system_setting) && !empty($fetch_system_setting[0]['club_rule_apply']) ) ? $fetch_system_setting[0]['club_rule_apply'] : 0;

            if($date_to != 1){

                $date_to = $date_to.'-'.$payroll_month.'-'.$payroll_year;
                $date_to = date('Y-m-d',strtotime($date_to));

                $date_from = date('Y-m-d',strtotime($date_to.' -1 months' ));
                $date_to = date('Y-m-d',strtotime($date_to.' -1 days' ));

                $period_count = $this->days_count_between_two_dates($date_from,$date_to);

                if(!empty($date_to_update) && strtotime($date_to_update) < strtotime($date_to) ){

                    $date_to = date('Y-m-d',strtotime($date_to_update));

                }

            }else{

                $date_to = $date_to.'-'.$payroll_month.'-'.$payroll_year;
                $date_to = date('Y-m-t',strtotime($date_to));
                $date_from = date('Y-m-01',strtotime($date_to));

                $period_count = $this->days_count_between_two_dates($date_from,$date_to);

                if(!empty($date_to_update) && strtotime($date_to_update) < strtotime($date_to) ){

                    $date_to = date('Y-m-d',strtotime($date_to_update));

                }

            }

            // it will return all active and deactive (who's deactive in payroll period)
            $get_employee = $this->get_payroll_employee($date_from,$date_to,$request->input('department'),$request->input('employee_id'));

            $fetch_late_rule = AttendanceRule::where('attendance_deduction_rule_type',1)->get(['attendance_deduction_rule_day_count','attendance_deduction_rule_deduction_count'])->toArray(); //type 1 for late
            $fetch_half_rule = AttendanceRule::where('attendance_deduction_rule_type',2)->get(['attendance_deduction_rule_day_count','attendance_deduction_rule_deduction_count'])->toArray(); //type 2 for half


            $late_rule = array_combine(array_column($fetch_late_rule, 'attendance_deduction_rule_day_count'),array_column($fetch_late_rule, 'attendance_deduction_rule_deduction_count'));
            $half_rule = array_combine(array_column($fetch_half_rule, 'attendance_deduction_rule_day_count'),array_column($fetch_half_rule, 'attendance_deduction_rule_deduction_count'));

            foreach( $get_employee as $employee ){
                
                // get attendance record of system of employee
                $attendance = $this->get_employee_attendance($date_from,$date_to,$get_employee[0]['employee_id']);

                $atts = array('attendance' => $attendance);
                $employee_with_att = array_merge( $employee , $atts );

                $attendance_count = $this->get_employee_complete_attendance_status($date_from,$date_to,$employee_with_att,$club_rule_is_applied);

                $salary_of_employee = $this->calculate_salary_of_employee($date_from,$date_to,$attendance_count,$late_rule,$half_rule,$get_employee[0]['employee_id'],$period_count);  

                $total_payroll += $salary_of_employee['net_salary'];
                
            }

            $message = "Monthly Expected Earning(s) ".round($total_payroll,2).' '.$currency[0]['system_currency'];
            $data = "Your expected earning(s) from ".date('d-M-Y',strtotime($date_from))." till ".date('d-M-Y',strtotime($date_to));
            
            return $this->api_response_json(TRUE,$message,$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('Calculate My Payroll', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Calculate My Payroll',  $request->header('Bearer-Token'));
            
        }
    }

    // protected function individual_payroll_pdf(Request $request ,$payroll_id){
    //     try{

    //         // $title = Self::INDIVIDUAL_PDF_TITLE;
    //         $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

    //         $payroll = Payroll::join('tbl_employees','tbl_employees.employee_id','=','tbl_payroll_calculation.pc_employee_id')
    //         ->leftjoin('tbl_personal_informations','tbl_personal_informations.employeeid','=','tbl_employees.employee_id')
    //         ->where('pc_id','=',$payroll_id)->get()->toArray();

    //         $extra_earning = PayrollEarning::where('pc_earning_pc_id','=',$payroll_id)->get()->toArray();

    //         $extra_deduction = PayrollDeduction::where('pc_deduction_pc_id','=',$payroll_id)->get()->toArray();

    //         $max_loop = max([count($extra_earning),count($extra_deduction)]);

    //         $extra_earning_amount = array_sum(array_column($extra_earning, 'pc_earning_amount'));

    //         $extra_deduction_amount = array_sum(array_column($extra_deduction, 'pc_deduction_amount'));

    //         $payroll[0]['pc_employee_net_salary'] = $payroll[0]['pc_employee_net_salary'] + $extra_earning_amount - $extra_deduction_amount;

    //         $payroll_create_by = Employees::where('employee_id',$payroll[0]['pc_created_by'])->get(['employee_name','employee_code'])->toArray();
            
    //         $payroll_disburse_by = Employees::where('employee_id',$payroll[0]['disburse_by'])->get(['employee_name','employee_code'])->toArray();
            
    //         $download_button_show = false;

    //         $setting = HrmSetting::orderBy('setting_id','DESC')->get(['name_on_payslip','department_on_payslip','signature_on_payslip'])->toArray();

    //         $send_data = compact('login_user','payroll','extra_earning','extra_deduction','max_loop','download_button_show','payroll_create_by','payroll_disburse_by','setting');

    //         $pdfContent = Pdf::loadView('payroll.individual-payroll-pdf', $send_data)->output();

    //         $filename = 'Payslip-'.$payroll[0]['employee_code'].'-'.$payroll[0]['employee_name'].'-'.date("F", mktime(0, 0, 0, $payroll[0]['pc_month'], 10)).'-'.$payroll[0]['pc_year'].'.pdf';
    //         $headers = [
    //             'Content-Type' => 'application/pdf',
    //             'Content-Disposition' => 'inline; filename="'.$filename.'"',
    //         ];
    //         // Return the API response with the generated PDF
    //         return response($pdfContent, 200, $headers);

    //     }catch(\Exception $e){

    //         $this->create_error_log_from_api('Individual Payroll PDF API', $e, $request->header('Bearer-Token'));

    //         return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

    //     }finally{

    //         ActivityTracker::track_activity_from_api('Individual Payroll PDF API',  $request->header('Bearer-Token'));
    //     }
    // }

    protected function get_payroll_employee($date_from,$date_to,$department,$employee_id=''){
        
        $where_clause = '1=1';

        if(!empty($department)){
            $where_clause = 'employee_department in ('.implode(",",$department).')';
        }

        if(!empty($employee_id)){
            $where_clause = 'employee_id = '.$employee_id;

        }
        
        $fetch_employee = Employees::
        join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
        ->join('tbl_personal_informations','tbl_personal_informations.employeeid','tbl_employees.employee_id')
        ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
        ->where(function ($query) use($date_from,$date_to) {
            $query->where('employee_isactive',1);
            $query->orWhereRaw('STR_TO_DATE(`employee_deactivate_date`,"%Y-%m-%d") >= "'.$date_from.'"  and STR_TO_DATE(`employee_deactivate_date`,"%Y-%m-%d") <= "'.$date_to.'" ');
        })
        ->whereRaw($where_clause)
        ->where('employee_earning_type',1) //monthly earning time
        ->get(['employee_id','employee_code','employee_name','designation_name','department_name','employee_jobtitle','employee_offdays','bankname','ibannumber','accountno','employee_doj','employee_deactivate_date'])->toArray();

        return $fetch_employee;
    }

    protected function get_employee_attendance($date_from,$date_to,$employee_id){

        $attendance = Attendance::where('att_employee_id',$employee_id)
        ->whereRaw('STR_TO_DATE(CONCAT(att_for_year,"-",att_for_month,"-",att_for_date),"%Y-%m-%d") >= "'.$date_from.'"  and STR_TO_DATE(CONCAT(att_for_year,"-",att_for_month,"-",att_for_date),"%Y-%m-%d") <= "'.$date_to.'" ')
        ->get()->toArray();

        return $attendance;

    }

    // count attendance status of each employee
    protected function get_employee_complete_attendance_status($date_from,$date_to,$attendances,$club_rule_is_applied){

        $present_count = 0;
        $absent_count =0;
        $halfday_count = 0;
        $late_count = 0;
        $leave_count = 0;

        $date_with_status = [];
        $absent_dates = [];

        $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

        $attendances['employee_offdays'] = (!empty($attendances['employee_offdays'])) ? explode(",",$attendances['employee_offdays']):[];


        $join_date = strtotime($attendances['employee_doj']);
        $deactive_date = strtotime($attendances['employee_deactivate_date']);

        // complete date of given period
        $date_to = new DateTime($date_to);
        $date_to->modify('+1 day');
        $period = new DatePeriod(
            new DateTime($date_from),
            new DateInterval('P1D'),
            $date_to
        );
        $all_dates_of_periods = [];

        foreach ($period as $dt) {

            array_push($all_dates_of_periods, $dt->format('Y-m-d'));
        }

        foreach($all_dates_of_periods as $all_dates_of_period){

            $date = $all_dates_of_period;
            $nameOfDay = date('l', strtotime($date));
            $absent = true;

            if( $join_date >  strtotime($date) || (!empty($deactive_date) && $deactive_date <  strtotime($date) ) ){

                $absent = false; 
                $absent_count++;
                array_push($absent_dates,$date);

                $date_with_status[$date] = "Absent";
            }else{

                foreach($attendances['attendance'] as $key=>$atty){
                    
                    $att_for_date = $atty['att_for_year'].'-'.$atty['att_for_month'].'-'.$atty['att_for_date'];

                    if($att_for_date == $date ){

                        $absent = false; 

                        if ($atty['att_check_overall_status'] == 'Present'){

                            $present_count++;
                            $date_with_status[$date] = "Present";

                        }
                        elseif($atty['att_check_overall_status'] == 'Absent'){

                            $absent_count++;
                            $date_with_status[$date] = "Absent";

                        }elseif($atty['att_check_overall_status'] == 'Half day'){

                            $halfday_count++;
                            $date_with_status[$date] = "Half day";

                        }elseif($atty['att_check_overall_status'] == 'Late Coming'){

                            $late_count++;
                            $date_with_status[$date] = "Late Coming";

                        }elseif(in_array($atty['att_check_overall_status'] , $manage_leaves)){
                     
                            $leave_count++;
                            $date_with_status[$date] = "Leave";

                        }elseif($atty['att_check_overall_status'] == 'Early out'){

                            $late_count++;
                            $date_with_status[$date] = "Late Coming";

                        }elseif(substr($atty['att_check_overall_status'] , 0, 7) == 'Holiday'){

                            $date_with_status[$date] = "Holiday";

                        }
                    }
                }
            
                $date_filter = $all_dates_of_period;
                $nameOfDay = date('l' , strtotime($date_filter));

                if($absent ){

                    if(in_array($nameOfDay,$attendances['employee_offdays']) ){
                        $date_with_status[$date] = "Off Day";

                    }else{

                     $absent_count++;
                     array_push($absent_dates,$date);
                     $date_with_status[$date] = "Absent";
                     
                    }
                }
            }
        }

        if($club_rule_is_applied == 1){

            $club_rule_absent = $this->apply_club_rule($all_dates_of_periods,$attendances,$absent_dates,$date_with_status);
            $date_with_status = $club_rule_absent['date_with_status'] ;
            $club_rule_absent = $club_rule_absent['rule_absent'];

        }else{

            $club_rule_absent = 0;

        }


        return  [

            'present_count' => $present_count,
            'absent_count' => $absent_count+$club_rule_absent,
            'halfday_count' => $halfday_count,
            'late_count' => $late_count,
            'leave_count' => $leave_count,
            'days_in_period' => count($all_dates_of_periods),
            'date_with_status' => $date_with_status

        ];

    }

    protected function apply_club_rule($all_dates_of_periods,$attendances,$absent_dates,$date_with_status){

        $rule_absent = 0;
        $off_days = [];
        $weekends = [];
        $applied_club_rule_dates = [];
        $attendances['employee_offdays'] = (!empty($attendances['employee_offdays'])) ? explode(",",$attendances['employee_offdays']):[];

        foreach($all_dates_of_periods as $all_dates_of_period){

            $date_filter = $all_dates_of_period;
            $nameOfDay = date('l' , strtotime($date_filter));

            if(in_array($nameOfDay,$attendances['employee_offdays']) ){

                array_push($weekends,$all_dates_of_period);
                
            }else{

                if(count($weekends) > 0){

                    array_push($off_days,$weekends);

                    if(count($weekends)==1){

                        $key = array_search($weekends[0], $all_dates_of_periods);
    
                        if( (($key-1) >0 && in_array($all_dates_of_periods[$key-1],$absent_dates)) && (($key-1) >0 && in_array($all_dates_of_periods[$key+1],$absent_dates)) && !in_array($all_dates_of_periods[$key],$absent_dates)  ){

                            $rule_absent++;

                            array_push($applied_club_rule_dates,$all_dates_of_periods[$key]);

                            $date_with_status[$all_dates_of_periods[$key]] = "Absent Club";
    
                        }
                        
                    }else{
    
                        $key = array_search($weekends[0], $all_dates_of_periods);
                        $key1 = array_search($weekends[1], $all_dates_of_periods);
    
                        if( (($key-1) >0 && in_array($all_dates_of_periods[$key-1],$absent_dates)) && (($key1-1) >0 && in_array($all_dates_of_periods[$key1+1],$absent_dates)) && !in_array($all_dates_of_periods[$key],$absent_dates) && !in_array($all_dates_of_periods[$key1],$absent_dates) ){
    
                            $rule_absent++;
                            $rule_absent++;

                            array_push($applied_club_rule_dates,$all_dates_of_periods[$key]);
                            array_push($applied_club_rule_dates,$all_dates_of_periods[$key1]);

                            $date_with_status[$all_dates_of_periods[$key]] = "Absent Club";
                            $date_with_status[$all_dates_of_periods[$key1]] = "Absent Club";
                        }
                    }
                }
                $weekends = [];
            }
        }
        return  [

            'rule_absent'=>$rule_absent,
            'date_with_status'=>$date_with_status,

        ];
    }

    protected function calculate_salary_of_employee($date_from,$date_to,$attendance_count,$late_deduction_count,$half_deduction_count,$employee_id,$period_count){

        if(Self::MONTH_COUNT == 30){

            $period_count = 30;

        }
        
        $date_with_status = $attendance_count['date_with_status'];

        $salary_period = EmployeeSalary::where('salary_employee_id',$employee_id)->where('salary_effected_date','>=',$date_from)->where('salary_effected_date','<=',$date_to)->orderBy('salary_effected_date','ASC')->get()->toArray();

        if(empty($salary_period)){
            
            $salary_period = EmployeeSalary::where('salary_employee_id',$employee_id)->orderBy('salary_effected_date','DESC')->limit(1)->get()->toArray();

        }else{

            $salary_period_last = EmployeeSalary::where('salary_employee_id',$employee_id)->where('salary_effected_date','<',$salary_period[0]['salary_effected_date'] )->orderBy('salary_effected_date','DESC')->limit(1)->get()->toArray();

            $salary_period = array_merge($salary_period_last,$salary_period);
        }

        $salary_effected_date = array_combine(array_column($salary_period,'salary_effected_date'),array_column($salary_period,'salary_amount'));

        $salary_chunk = [];
        $salary_chunk_attendance_status = [];

        $splite_count = 0;

        if(count($salary_effected_date)>1){
            foreach($salary_effected_date as $salary_effected=>$value){

                $splite_date = array_search($salary_effected, array_keys($date_with_status));
                
                $salary_chunk[$splite_count] = array_slice(array_keys($date_with_status),$splite_date,count($date_with_status));

                $salary_chunk_attendance_status[$splite_count] = array_slice(array_values($date_with_status),$splite_date,count($date_with_status));

                $splite_count++;

            }
        }else{

            $salary_chunk[0] = array_keys($date_with_status);
            $salary_chunk_attendance_status[0] = array_values($date_with_status);

        }

        if(count($salary_effected_date)>1){
            for($i=0;$i<count($salary_chunk)-1;$i++){
    
                $salary_chunk[$i] = array_diff($salary_chunk[$i],$salary_chunk[$i+1]);
                $salary_chunk_attendance_status[$i] = array_slice($salary_chunk_attendance_status[$i],0,count($salary_chunk_attendance_status[$i])-count($salary_chunk_attendance_status[$i+1]));

            }
    
        }

        $salary_list = array_values($salary_effected_date);

        $total_each_chunk_absent = 0;
        $total_chunk_salary_net = 0;

        $each_chunk_late = 0;
        $each_chunk_half_day = 0;
        $each_chunk_present = 0;
        $each_chunk_offday = 0;
        $each_chunk_leave = 0;
        $each_chunk_holiday = 0;
        $each_chunk_gross_salary = 0;

        for($i=0;$i<count($salary_chunk_attendance_status);$i++){

            $each_chunk_attendance_status = array_count_values($salary_chunk_attendance_status[$i]);

            $each_chunk_late += $each_chunk_attendance_status['Late Coming'] ?? 0;
            $each_chunk_late += $each_chunk_attendance_status['Early Out'] ?? 0;

            $each_chunk_absent = $each_chunk_attendance_status['Absent'] ?? 0;
            $each_chunk_absent += $each_chunk_attendance_status['Absent Club'] ?? 0;

            $each_chunk_half_day += $each_chunk_attendance_status['Half day'] ?? 0;

            $each_chunk_present += $each_chunk_attendance_status['Present'] ?? 0;

            $each_chunk_offday += $each_chunk_attendance_status['Off Day'] ?? 0;

            $each_chunk_leave += $each_chunk_attendance_status['Leave'] ?? 0;

            $each_chunk_holiday += $each_chunk_attendance_status['Holiday'] ?? 0;

            $each_chunk_salary =  $salary_list;

            $each_chunk_salary_per_day = $each_chunk_salary[$i]/$period_count;

            $each_chunk_salary_complete = $each_chunk_salary_per_day * count($salary_chunk_attendance_status[$i]) ;

            // $each_chunk_salary_complete = $each_chunk_salary_per_day * $period_count ;

            $each_chunk_gross_salary += $each_chunk_salary_complete;

            $each_chunk_absent_total =  $each_chunk_salary_per_day * $each_chunk_absent;

            $each_chunk_salary_net = $each_chunk_salary_complete  - $each_chunk_absent_total ;

            $total_each_chunk_absent += $each_chunk_absent_total;

            $total_chunk_salary_net += $each_chunk_salary_net;
            
            $latest_salary_per_day = $each_chunk_salary_per_day;

        }

        $deduction_of_late_count  = array_key_exists($each_chunk_late,$late_deduction_count)==1 ? $late_deduction_count[$each_chunk_late] : $each_chunk_late ;

        $deduction_of_halfday_count  = array_key_exists($each_chunk_half_day,$half_deduction_count)==1 ? $half_deduction_count[$each_chunk_half_day] : $each_chunk_half_day;

        $final_half_day_deduction = $latest_salary_per_day*$deduction_of_halfday_count;
        $final_late_day_deduction = $latest_salary_per_day*$deduction_of_late_count;

        $total_chunk_salary_net_after_late_half = $total_chunk_salary_net - $final_half_day_deduction - $final_late_day_deduction;

        return  [

            'net_salary'=>$total_chunk_salary_net_after_late_half,

            'deduction_of_late'=>$final_late_day_deduction,
            'deduction_of_halfday'=>$final_half_day_deduction,
            'deduction_of_absent'=>$total_each_chunk_absent,

            'total_days'=> count($date_with_status),
            'each_chunk_late' => $each_chunk_late,
            'each_chunk_half_day' => $each_chunk_half_day,
            'each_chunk_present' => $each_chunk_present,
            'each_chunk_offday' => $each_chunk_offday,
            'each_chunk_absent' => $each_chunk_absent,
            'each_chunk_leave' => $each_chunk_leave,
            'each_chunk_holiday' => $each_chunk_holiday,
            'each_chunk_gross_salary' => $each_chunk_gross_salary,
            
        ];

    }

    protected function days_count_between_two_dates($date_from,$date_to){
        
        $date_to = new DateTime($date_to);
        $date_to->modify('+1 day');
        $period = new DatePeriod(
            new DateTime($date_from),
            new DateInterval('P1D'),
            $date_to
        );

        $days = 0;
        foreach ($period as $dt) {

            $days++;

        }

        return $days;

    }

    protected function account_identification(Request $request){
       
        try{
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                'user_password' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else{
                $user_password = Employees::where('employee_id', $login_user[0]['employee_id'])->where('employee_isactive',1)->get(['employee_password'])->toArray();
                
                if(!empty($user_password)){
                    if(Crypt::decryptString($user_password[0]['employee_password']) == $request->post('user_password') ){
                    
                        return $this->api_response_json(TRUE,"Account identification successfully completed.","",200);
                
                    }else{
                        return $this->api_response_json(FALSE,"Account identification failed.","",200);
                      
                    }
                }else{
                    
                    return $this->api_response_json(FALSE,"Account identification failed.","",200);
                }
            }
            


        }catch(\Exception $e){

            $this->create_error_log("Account Identification My Payroll", $e);

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

            ActivityTracker::track_activity('Account Identification My Payroll');

        }

    }



}

