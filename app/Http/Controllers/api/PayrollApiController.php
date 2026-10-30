<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Payroll, AttendanceRule, HrmSetting, Employees, Attendance, EmployeeSalary, PayrollEarning, PayrollDeduction, LeaveManagement};
use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait, PayrollCalculation};
use App\Helpers\{ActivityTracker, CustomHelper};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\{Validator,Crypt};

use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\DB;

class PayrollApiController extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait, PayrollCalculation;

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

            foreach( $get_employee as $employee ){

                // get attendance record of system of employee
                $attendance = $this->get_employee_attendance($date_from,$date_to,$employee['employee_id']);

                $atts = array('attendance' => $attendance);
                $employee_with_att = array_merge( $employee , $atts );

                $attendance_count = $this->get_employee_complete_attendance_status($date_from,$date_to,$employee_with_att,$club_rule_is_applied);

                $salary_of_employee = $this->calculate_salary_of_employee($date_from,$date_to,$attendance_count,$employee['employee_id'],$period_count);

                $total_payroll += $salary_of_employee['net_salary'] ?? 0;
                
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

            ActivityTracker::track_activity('Account Identification My Payroll');

        }

    }



}

