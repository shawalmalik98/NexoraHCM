<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator,Crypt};
use Barryvdh\DomPDF\Facade\Pdf;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\{Employees, Attendance, AttendanceRule, EmployeeSalary, Payroll, PayrollDeduction, PayrollEarning, Department, Loan, LoanInstallment, AdvanceSalary, TaxSlab, HrmSetting, EmployeeExpense, OverTime, EmployeeDevice, DailyWages, LeaveManagement, SalaryDivision, PayrollAttempts,EmploymentStatus};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait};
use App\Helpers\{ActivityTracker,CustomHelper};
use Illuminate\Support\Facades\Redirect;

use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{   
    use LoginEmployee, LogGenerateTrait , NotificationTrait, ApiResponseTrait;

    const MAIN_TITLE = "Payroll";
    const ADD_TITLE = "Add Payroll";
    const INDIVIDUAL_PDF_TITLE = "Payslip";
    const SUB_TITLE = "My Payslip";
    const MAIN_TITLE_DAILY = "daily-wages";
    const verification_code = '123789';
    const DEDUCTION_TITLE = 'deduction policy';

    const MONTH_COUNT = 0;
    // the payroll date limit 1-28 otherwise code not working correct


    protected $not_editable_title = ['gross salary','basic salary','absent','half day','late comming / early out','early out','late comming','loan deduction','advance salary deduction','income tax deduction','over-time','employee expense reimbursement'];

    protected function index(Request $request){

        try{

            $title = Self::MAIN_TITLE;
        
            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();

            if(!empty($request->input('m')) && !empty($request->input('y'))){

                $month = $request->input('m');
                $year = $request->input('y');

            }else{

                $month = date('m');
                $year = date('Y');
            }

            $all_department = Department::get(['department_id','department_name'])->toArray();

            $fetch_employee_payroll = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,pc_employee_gross_salary, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`,`disburse`,pc_year,pc_month,pc_created_at FROM `tbl_payroll_calculation` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 1 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_payroll_calculation.pc_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 1 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_payroll_calculation.pc_id 
                where pc_month = '".$month."'
                and pc_year = '".$year."'
                group by pc_id ,pc_deduction_pc_id,pc_earning_pc_id ");

            $fetch_employee_payroll = json_decode(json_encode($fetch_employee_payroll), true);

            // $fetch_employee_payroll = Payroll::selectRaw('( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary) as pc_employee_gross_salary  , ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`, `pc_month`, `pc_year`, `pc_date_from`, `pc_date_to`, `pc_employee_designation`, `pc_employee_department`, `pc_employee_job_title`, `pc_employee_bank_name`, `pc_employee_bank_account`, `pc_employee_bank_iban`, `pc_total_working_days`, `pc_present`, `pc_absent`, `pc_off_day`, `pc_leave`, `pc_holiday`, `pc_late_early`, `pc_half_day`, `pc_absent_deduction`, `pc_late_early_deduction`, `pc_half_day_deduction`, `pc_batch`, `pc_created_at`, `pc_updated_at` , disburse')
            // ->leftJoin(
            //     PayrollEarning::selectRaw('pc_earning_pc_id,  sum (pc_earning_amount) as sum_pc_earning_amount')
            //     ->groupBy('pc_earning_pc_id')->fromRaw('tbl_payroll_calculation_earning as payroll_earning') , function($join) {
            //     $join->on('payroll_earning.pc_earning_pc_id','=','tbl_payroll_calculation.pc_id');
            // })
            // ->leftJoin(
            //     PayrollDeduction::selectRaw('pc_deduction_pc_id,  sum (pc_deduction_amount) as sum_pc_deduction_amount')
            //     ->groupBy('pc_deduction_pc_id')->fromRaw('tbl_payroll_calculation_deduction as payroll_deduction') , function($join) {
            //     $join->on('payroll_deduction.pc_deduction_pc_id','=','tbl_payroll_calculation.pc_id');
            // })
            // ->where('pc_month', $month)
            // ->where('pc_year', $year)
            // ->groupBy('pc_id')
            // ->groupBy('pc_deduction_pc_id')
            // ->groupBy('pc_earning_pc_id')
            // ->get()
            // ->toArray()
            
            $fetch_employee_disburse = Payroll::join('tbl_employees','tbl_employees.employee_id','=','tbl_payroll_calculation.pc_employee_id')
            ->where('pc_month', $month)
            ->where('pc_year', $year)
            ->where('disburse', 1)
            ->get(['pc_id','employee_name','employee_code'])->toArray();
            
            $tax_system_setting = HrmSetting::get(['tax_deduction_apply'])->toArray();

            $send_data = compact('title','login_user','fetch_employee_payroll','all_department','month','year','fetch_employee_disburse','tax_system_setting','dynamic_years');
            
            return view('payroll.all-employee-payroll', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('View Payroll', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View Payroll');
         

        }
    }

    protected function my_payroll(){
        try{
            
            $title = Self::SUB_TITLE;
        
            $login_user = $this->get_login_user_detail();
            
            $fetch_employee_payroll = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,pc_employee_gross_salary, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`,`disburse`,pc_year,pc_month,pc_created_at FROM `tbl_payroll_calculation` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 1 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_payroll_calculation.pc_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 1 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_payroll_calculation.pc_id 
                where pc_employee_id = ".session('user_session')." AND disburse = '2' group by pc_id ,pc_deduction_pc_id,pc_earning_pc_id ");

            $fetch_employee_wages = DB::select("SELECT sum(( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ))) as dw_employee_extra_daily_wages  ,sum(dw_employee_gross_daily_wages) as dw_employee_gross_daily_wages, sum( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction)) as dw_employee_deduction , sum((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + dw_employee_gross_daily_wages)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction))) as dw_employee_net_daily_wages,`dw_id`, `dw_employee_id`,`dw_disburse`,dw_of_date,dw_created_at, count(dw_id) as wage_count FROM `tbl_daily_wages` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 2 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_daily_wages.dw_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 2 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_daily_wages.dw_id 
                where dw_employee_id = ".session('user_session')." AND dw_disburse = '2' group by dw_employee_id, date_format(dw_of_date,'%m-%Y')");

            $fetch_employee_wages = json_decode(json_encode($fetch_employee_wages), true);

            $fetch_employee_payroll = json_decode(json_encode($fetch_employee_payroll), true);
            
            $current_calculate_option = Payroll::where('disburse',2)->where('pc_employee_id',$login_user[0]['employee_id'])->where('pc_month',date('m'))->where('pc_year',date('Y'))->get()->toArray();
            $pervious_calculate_option = Payroll::where('disburse',2)->where('pc_employee_id',$login_user[0]['employee_id'])->where('pc_month',date('m',strtotime('-1 month')))->where('pc_year',date('Y',strtotime('-1 month')))->get()->toArray();
            
            $send_data = compact('title','login_user','fetch_employee_payroll','current_calculate_option','pervious_calculate_option','fetch_employee_wages');
            
            if(session('my_payroll') == "allow"){

                return view('payroll.my-payroll', $send_data);

            }else{

                return view('include.payslip-password-verification', $send_data);

            }

        }catch(\Exception $e){

            $this->create_error_log('View My Payroll', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            
           if(session('my_payroll') == "allow"){

                ActivityTracker::track_activity('View My Payroll');

            }else{

                ActivityTracker::track_activity('View My Payroll Direct Url');
        
            }

        }

    }

    protected function my_wages(Request $request){

        try{

            $title = Self::SUB_TITLE;
        
            $login_user = $this->get_login_user_detail();

            if(!empty($request->input('m')) && !empty($request->input('y'))){
                $month = $request->input('m');
                $year = $request->input('y');
            }else{
                $month = date('m');
                $year = date('Y');
            }

            $fetch_employee_wages = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,dw_employee_gross_daily_wages, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction)) as pc_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + dw_employee_gross_daily_wages)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction))) as dw_employee_net_daily_wages,`dw_id`, `dw_employee_id`,`dw_disburse`,dw_of_date,dw_created_at FROM `tbl_daily_wages` 
            left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 2 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_daily_wages.dw_id 
            left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 2 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_daily_wages.dw_id 
            where dw_employee_id = ".session('user_session')." AND dw_disburse = '2' AND date_format(dw_of_date,'%m') = ".$month." AND date_format(dw_of_date,'%Y') = ".$year." group by dw_id ,pc_deduction_pc_id,pc_earning_pc_id ");

            $fetch_employee_wages = json_decode(json_encode($fetch_employee_wages), true);

            $send_data = compact('title','login_user','fetch_employee_wages','month','year');

            if(session('my_payroll') == "allow"){

                return view('payroll.my-wages', $send_data);

            }else{

                return view('include.payslip-password-verification', $send_data);

            }

        }catch(\Exception $e){

            $this->create_error_log('View My Wage', $e);
            $send_data = compact('title','login_user');
            return view('errors.500');

        }finally{
            
            if(session('my_payroll') == "allow"){

                ActivityTracker::track_activity('View My Wage');
            
            }else{

                ActivityTracker::track_activity('View My Wage Direct Url');

            }
        }

    }

    protected function edit_individaul_employee_payroll(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'pc_id' => 'required',
                'extra_earning_title' => 'array',
                'extra_earning_title.*' => 'required',
                'extra_deduction_title' => 'array',
                'extra_deduction_title.*' => 'required',
                'extra_earning_amount' => 'array',
                'extra_earning_amount.*' => 'required|min:1|max:999999|numeric',
                'extra_deduction_amount' => 'array',
                'extra_deduction_amount.*' => 'required|min:1|max:999999|numeric',
            
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            DB::beginTransaction();

            if(!empty($request->input('extra_earning_title'))){
                $i =0;
                $request_extra_earning_title = array_map('strtolower', $request->input('extra_earning_title'));
                foreach($request_extra_earning_title as $extra_earning_title ){
                    if(in_array($extra_earning_title,$this->not_editable_title)){
                        $earning_record = PayrollEarning::where('pc_earning_pc_id',$request->input('pc_id'))->where('pc_earning_type',1)->where('pc_earning_title',$extra_earning_title)->get(['pc_earning_title','pc_earning_amount'])->toArray();
                        if(count($earning_record) != array_count_values($request_extra_earning_title)[$extra_earning_title] ){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>  "Can't use ".$extra_earning_title." as title",
                            ]);
                        }
                    }
                }
            }
            if(!empty($request->input('extra_deduction_title'))){
                $i =0;
                $request_extra_deduction_title = array_map('strtolower', $request->input('extra_deduction_title'));
                foreach($request_extra_deduction_title as $extra_deduction_title ){
                    if(in_array($extra_deduction_title,$this->not_editable_title)){
                        $deduction_record = PayrollDeduction::where('pc_deduction_pc_id',$request->input('pc_id'))->where('pc_deduction_type',1)->where('pc_deduction_title',$extra_deduction_title)->get(['pc_deduction_title','pc_deduction_amount'])->toArray();
                        if(count($deduction_record) != array_count_values($request_extra_deduction_title)[$extra_deduction_title] ){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>  "Can't use ".$extra_deduction_title." as title",
                            ]);
                        }
                    }
                }
            }

            $delete_old_earning_record = PayrollEarning::where('pc_earning_pc_id',$request->input('pc_id'))->whereNotIn('pc_earning_title',$this->not_editable_title)->where('pc_earning_type',1)->delete();

            if(!empty($request->input('extra_earning_title'))){

                $i=0;
                foreach($request->input('extra_earning_title') as $extra_earning_title ){
                    if(!in_array(strtolower($extra_earning_title),$this->not_editable_title)){
                        if($request->input('extra_earning_amount')[$i] != '' && is_numeric($request->input('extra_earning_amount')[$i])){
                            $add_payroll_earning = new PayrollEarning();
                            $add_payroll_earning->pc_earning_pc_id = $request->input('pc_id');
                            $add_payroll_earning->pc_earning_title = $extra_earning_title;
                            $add_payroll_earning->pc_earning_amount = $request->input('extra_earning_amount')[$i];
                            $add_payroll_earning->save();
                        }
                    }
                    $i++;
                }

            }

            $delete_old_deduction_record = PayrollDeduction::where('pc_deduction_pc_id',$request->input('pc_id'))->where('pc_deduction_type',1)->whereNotIn('pc_deduction_title',$this->not_editable_title)->delete();

            if(!empty($request->input('extra_deduction_title'))){
                $i=0;
                foreach($request->input('extra_deduction_title') as $extra_deduction_title ){
                    if(!in_array(strtolower($extra_deduction_title),$this->not_editable_title)){
                        if($request->input('extra_deduction_amount')[$i] != '' && is_numeric($request->input('extra_deduction_amount')[$i])){
                            $add_payroll_deduction = new PayrollDeduction();
                            $add_payroll_deduction->pc_deduction_pc_id = $request->input('pc_id');
                            $add_payroll_deduction->pc_deduction_title = $extra_deduction_title;
                            $add_payroll_deduction->pc_deduction_amount = $request->input('extra_deduction_amount')[$i];
                            $add_payroll_deduction->save();
                        }

                    }
                    $i++;
                }
            }
            $update_date = Payroll::where('pc_id',$request->input('pc_id'))->update(['pc_updated_at'=>date('Y-m-d')]);

            DB::commit();

            return response()->json([
                'status' => 'TRUE', 
                'msg' => 'Payroll updated successfully'
            ]);
        }catch(\Exception $e){

            DB::rollback();

            $this->create_error_log('Update Payroll', $e);

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

            ActivityTracker::track_activity('Update Payroll');
        
        }

    }

    
    protected function edit_payroll(Request $request,$payroll_id){
        
        try{
            
            $title = Self::ADD_TITLE;
        
            $login_user = $this->get_login_user_detail();

            $specific_payroll = [];

            $fetch_payroll_details = Payroll::where('pc_id',$payroll_id)
            ->join('tbl_employees','tbl_employees.employee_id','=','tbl_payroll_calculation.pc_employee_id')
            ->where('disburse',1)
            ->get()->toArray();
            
            if(!empty($fetch_payroll_details)){
            $fetch_extra_earning = PayrollEarning::where('pc_earning_pc_id',$fetch_payroll_details[0]['pc_id'])->where('pc_earning_type',1)->get()->toArray();
            
            $fetch_extra_deduction = PayrollDeduction::where('pc_deduction_pc_id',$fetch_payroll_details[0]['pc_id'])->where('pc_deduction_type',1)->get()->toArray();

            $all_employees = Employees::where('employee_isactive',1)->get(['employee_id','employee_code','employee_name'])->toArray();
            $not_editable_title = $this->not_editable_title;
            
            $send_data = compact('title','login_user','all_employees','specific_payroll','fetch_payroll_details','fetch_extra_earning','fetch_extra_deduction','not_editable_title');
            
            return view('payroll.edit-individual-employee-payroll', $send_data);

            }else{

                return redirect(url()->previous());

            }
            
        }catch(\Exception $e){

            $this->create_error_log('Edit Payroll', $e);
            $send_data = compact('title');

        }finally{

            ActivityTracker::track_activity('Edit Payroll');
            
        }

    }

    protected function individual_payroll_pdf(Request $request ,$payroll_id){
       
        try{
            
            $title = Self::INDIVIDUAL_PDF_TITLE;
            $login_user = $this->get_login_user_detail();

            $payroll = Payroll::join('tbl_employees','tbl_employees.employee_id','=','tbl_payroll_calculation.pc_employee_id')
            ->join('tbl_employement_status', 'tbl_employees.employee_status', '=', 'tbl_employement_status.employement_status_id')
            ->leftjoin('tbl_personal_informations','tbl_personal_informations.employeeid','=','tbl_employees.employee_id')
            ->where('pc_id','=',$payroll_id)->get()->toArray();

            $extra_earning = PayrollEarning::where('pc_earning_pc_id','=',$payroll_id)->
            where('pc_earning_type',1)->get()->toArray();

            $extra_deduction = PayrollDeduction::where('pc_deduction_pc_id','=',$payroll_id)->where('pc_deduction_type',1)->get()->toArray();

            $max_loop = max([count($extra_earning),count($extra_deduction)]);

            $extra_earning_amount = array_sum(array_column($extra_earning, 'pc_earning_amount'));

            $extra_deduction_amount = array_sum(array_column($extra_deduction, 'pc_deduction_amount'));

            $total_earning = $extra_earning_amount + $payroll[0]['pc_employee_gross_salary'];
            $total_deduction = $extra_deduction_amount + $payroll[0]['pc_late_early_deduction'] + $payroll[0]['pc_absent_deduction'] + $payroll[0]['pc_half_day_deduction'];

            $payroll[0]['pc_employee_net_salary'] = $payroll[0]['pc_employee_net_salary'] + $extra_earning_amount - $extra_deduction_amount;

            $payroll_create_by = Employees::where('employee_id',$payroll[0]['pc_created_by'])->get(['employee_name','employee_code'])->toArray();
            
            $payroll_disburse_by = Employees::where('employee_id',$payroll[0]['disburse_by'])->get(['employee_name','employee_code'])->toArray();
            
            $download_button_show = true;

            $setting = HrmSetting::orderBy('setting_id','DESC')->get(['name_on_payslip','signature_on_payslip'])->toArray();

            $finance_name = '';
            if(!empty($setting) && !empty($setting[0]['name_on_payslip'])){
                $finance_name = Employees::join('tbl_departments','tbl_departments.department_id','=','employee_department')->join('tbl_designations','tbl_designations.designation_id','=','employee_designation')->where('employee_id',$setting[0]['name_on_payslip'])->get(['employee_name','designation_name','department_name'])->toArray(); 
            }
            
            $salary_division = SalaryDivision::SelectRaw('(salary_division_percentage/100) as salary_division_percentage,salary_division_title')->get()->toArray();

            $sum_of_all_allowance = array_sum(array_column($salary_division,'salary_division_percentage'));
            if((1 -  $sum_of_all_allowance )> 0){
                array_push($salary_division,['salary_division_title' => 'Other' , 'salary_division_percentage' => 1 - $sum_of_all_allowance]);
            }
          
            if($request->input('download')=="TRUE"){
                $download_button_show = false;
            }
            
            $send_data = compact('login_user','payroll','extra_earning','extra_deduction','max_loop','download_button_show','payroll_create_by','payroll_disburse_by','setting','total_deduction','total_earning','salary_division','finance_name');

            if(!empty($payroll) && ( ($payroll[0]['pc_employee_id'] == session('user_session') && $payroll[0]['disburse'] == 2) || $login_user[0]['employee_department'] == "1" || $login_user[0]['employee_department'] == "2" || $login_user[0]['employee_department'] == "3" )){

                if($request->input('download')=="TRUE"){

                    $pdf = Pdf::loadView('payroll.individual-payroll-pdf', $send_data);
                    return $pdf->setPaper('a3', 'potrait')->download('Payslip-'.$payroll[0]['employee_code'].'-'.$payroll[0]['employee_name'].'-'.date("F", mktime(0, 0, 0, $payroll[0]['pc_month'], 10)).'-'.$payroll[0]['pc_year'].'.pdf');
                    
                }

                return view('payroll.individual-payroll-pdf', $send_data);
                
            }else{

                return abort(404);

            }

        }catch(\Exception $e){

            $this->create_error_log('Individual Payroll PDF', $e);

            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Individual Payroll PDF');

           
        }
    }
    protected function payslip_pdf(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
                'device_token' => ['required'],
                'pc_id' => ['required'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => $error,
                    ]);
                }
            }

            $login_user = $this->get_token_user_detail($request->input('token'));

            if(!empty($login_user)){

                $active_device = EmployeeDevice::where('device_token',$request->input('device_token'))->where('device_employee_id',$login_user[0]['employee_id'])->where('device_status',1)->get(['device_employee_id'])->toArray();

                if(!empty($active_device)){

                    $title = Self::INDIVIDUAL_PDF_TITLE;

                    $payroll_id = $request->input('pc_id');
                    $payroll = Payroll::join('tbl_employees','tbl_employees.employee_id','=','tbl_payroll_calculation.pc_employee_id')
                    ->leftjoin('tbl_personal_informations','tbl_personal_informations.employeeid','=','tbl_employees.employee_id')
                    ->where('pc_id','=',$payroll_id)->get()->toArray();

                    $extra_earning = PayrollEarning::where('pc_earning_pc_id','=',$payroll_id)->where('pc_earning_type',1)->get()->toArray();

                    $extra_deduction = PayrollDeduction::where('pc_deduction_pc_id','=',$payroll_id)->where('pc_deduction_type',1)->get()->toArray();

                    $max_loop = max([count($extra_earning),count($extra_deduction)]);

                    $extra_earning_amount = array_sum(array_column($extra_earning, 'pc_earning_amount'));

                    $extra_deduction_amount = array_sum(array_column($extra_deduction, 'pc_deduction_amount'));

                    $payroll[0]['pc_employee_net_salary'] = $payroll[0]['pc_employee_net_salary'] + $extra_earning_amount - $extra_deduction_amount;

                    $total_earning = $extra_earning_amount + $payroll[0]['pc_employee_gross_salary'];
                    $total_deduction = $extra_deduction_amount + $payroll[0]['pc_late_early_deduction'] + $payroll[0]['pc_absent_deduction'] + $payroll[0]['pc_half_day_deduction'];

                    $payroll_create_by = Employees::where('employee_id',$payroll[0]['pc_created_by'])->get(['employee_name','employee_code'])->toArray();
                    
                    $payroll_disburse_by = Employees::where('employee_id',$payroll[0]['disburse_by'])->get(['employee_name','employee_code'])->toArray();

                    $setting = HrmSetting::orderBy('setting_id','DESC')->get(['name_on_payslip','signature_on_payslip'])->toArray();

                    $download_button_show = false;
                    
                    $finance_name = '';
                    if(!empty($setting) && !empty($setting[0]['name_on_payslip'])){
                        $finance_name = Employees::join('tbl_departments','tbl_departments.department_id','=','employee_department')->join('tbl_designations','tbl_designations.designation_id','=','employee_designation')->where('employee_id',$setting[0]['name_on_payslip'])->get(['employee_name','designation_name','department_name'])->toArray(); 
                    }
                    
                    $salary_division = SalaryDivision::SelectRaw('(salary_division_percentage/100) as salary_division_percentage,salary_division_title')->get()->toArray();

                    $sum_of_all_allowance = array_sum(array_column($salary_division,'salary_division_percentage'));
                    if((1 -  $sum_of_all_allowance )> 0){
                        array_push($salary_division,['salary_division_title' => 'Other' , 'salary_division_percentage' => 1 - $sum_of_all_allowance]);
                    }

                    if(!empty($payroll) && ( ($payroll[0]['pc_employee_id'] == $login_user[0]['employee_id'] ))){

                        $send_data = compact('login_user','payroll','extra_earning','extra_deduction','max_loop','download_button_show','payroll_create_by','payroll_disburse_by','setting','total_deduction','total_earning','salary_division','finance_name');
                        $pdf = Pdf::loadView('payroll.individual-payroll-pdf', $send_data);
                        return $pdf->setPaper('a3', 'potrait')->download('Payslip-'.$payroll[0]['employee_code'].'-'.$payroll[0]['employee_name'].'-'.date("F", mktime(0, 0, 0, $payroll[0]['pc_month'], 10)).'-'.$payroll[0]['pc_year'].'.pdf');
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Payslip no. is invalid',
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Device is invalid',
                    ]);
                }

            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Token is invalid',
                ]);
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('View My Payslip API', $e, $request->input('token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('View My Payslip API',  $request->input('token'));
            
        }
    }

    protected function download_payroll_pdf(Request $request){

        try{

            $login_user = $this->get_login_user_detail();

            $month = $request->input('month');
            $year = $request->input('year');
            
            if(!empty($request->input('download_file')) && $request->input('download_file') == 'excel'){
                $fetch_employee_payroll = DB::select("SELECT pc_id as 'Slip No.', employee_name as 'Employee Name', employee_code as 'Employee Code', concat(MONTHNAME(STR_TO_DATE(pc_month, '%m')),'-', pc_year)  as 'Pay Period', pc_employee_gross_salary as 'Gross Salary' , ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as 'Extra Earning' , ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as 'Total Deduction' , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction))) as 'Net Salary', DATE_FORMAT(pc_created_at,'%d-%M-%Y') as 'Generation Date' , pc_employee_department as 'Department', pc_employee_designation as 'Designation' , pc_employee_bank_name as 'Bank Name' ,pc_employee_bank_account as 'Account No.',pc_employee_bank_iban as 'IBAN', `pc_total_working_days` as 'Total Days', `pc_present` as 'Total Presents', `pc_late_early` as 'Total Late/Early Out', `pc_half_day` as 'Total Half Days', `pc_absent` as 'Total Absents', `pc_off_day` as 'Total Off Days', `pc_leave` as 'Total Leaves', `pc_holiday` as 'Total Holidays', date_format(pc_date_from,'%d-%M-%Y') as 'From Date' , date_format(pc_date_to,'%d-%M-%Y') as 'To Date' FROM `tbl_payroll_calculation` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 1 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_payroll_calculation.pc_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 1 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_payroll_calculation.pc_id 
                join tbl_employees on tbl_employees.employee_id = tbl_payroll_calculation.pc_employee_id
                where pc_month = '".$month."'
                and pc_year = '".$year."'
                group by pc_id ,pc_deduction_pc_id,pc_earning_pc_id ");
            }else{
                $fetch_employee_payroll = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,pc_employee_gross_salary, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`, `pc_month`, `pc_year`, `pc_date_from`, `pc_date_to`, `pc_employee_designation`, `pc_employee_department`, `pc_employee_job_title`, `pc_employee_bank_name`, `pc_employee_bank_account`, `pc_employee_bank_iban`, `pc_total_working_days`, `pc_present`, `pc_absent`, `pc_off_day`, `pc_leave`, `pc_holiday`, `pc_late_early`, `pc_half_day`, `pc_absent_deduction`, `pc_late_early_deduction`, `pc_half_day_deduction`, `pc_batch`, `pc_created_at`, `pc_updated_at` , disburse, employee_name, employee_code FROM `tbl_payroll_calculation` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 1 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_payroll_calculation.pc_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 1 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_payroll_calculation.pc_id 
                join tbl_employees on tbl_employees.employee_id = tbl_payroll_calculation.pc_employee_id
                where pc_month = '".$month."'
                and pc_year = '".$year."'
                group by pc_id ,pc_deduction_pc_id,pc_earning_pc_id ");
            }

            $fetch_employee_payroll = json_decode(json_encode($fetch_employee_payroll), true);

            $download_button_show = true;

            if($request->input('download')=="TRUE"){
                $download_button_show = false;
            }

            $send_data = compact('fetch_employee_payroll','month','year','download_button_show');

            if( $login_user[0]['employee_department'] == "1" || $login_user[0]['employee_department'] == "2" || $login_user[0]['employee_department'] == "3" ){

                if($request->input('download')=="TRUE"){
                    
                    if(!empty($request->input('download_file')) && $request->input('download_file') == 'excel'){
                        return (new FastExcel($fetch_employee_payroll))->download('Payroll-'.date("F", mktime(0, 0, 0, $month, 10)).'-'.$year.'.xlsx');
                    }else{
                        $pdf = Pdf::loadView('payroll.all-payroll-pdf', $send_data);
                        return $pdf->setPaper('a3')->download('Payroll-'.date("F", mktime(0, 0, 0, $month, 10)).'-'.$year.'.pdf');
                    }

                }
                return view('payroll.all-payroll-pdf',$send_data);
                
            }else{

                return abort(404);

            }


        }catch(\Exception $e){

            $this->create_error_log('Complete Payroll PDF', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Complete Payroll PDF');

        }
            
    }

    protected function get_employee_payroll(Request $request){  

        $month = date('m',strtotime($request->input('pay_period')));
        $year = date('Y',strtotime($request->input('pay_period')));

        $fetch_payroll_details = Payroll::where('pc_year',$year)->where('pc_month',$month)->where('pc_employee_id',$request->input('employee_id'))->get()->toArray();

        $fetch_extra_earning = PayrollEarning::where('pc_earning_pc_id',$fetch_payroll_details[0]['pc_id'])->get()->toArray();
        
        $fetch_extra_deduction = PayrollDeduction::where('pc_deduction_pc_id',$fetch_payroll_details[0]['pc_id'])->get()->toArray();
        

        return response()->json([

            'status' => 'TRUE', 
            'data' => $fetch_payroll_details,
            'earning' => $fetch_extra_earning,
            'deduction' => $fetch_extra_deduction

        ]);

    }

    protected function generate_payroll(Request $request){

        try{

            $currency = CustomHelper::hcm_system_currency();

            $validator = Validator::make($request->all(), [
                'payroll_month' => ['required'],
                'department' => 'required_without:employee_id',
                'employee_id' => 'required_without:department',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return response()->json([

                        'status' => 'FALSE',
                        'title' => $error,
                        'text' => "",
                        'icon' => "warning"
        
                        ]);
                }
            }

            if($request->input('calculatePayrollAmountForm')){
                
                $payroll_month = date('m',strtotime($request->input('payroll_month')));
                $payroll_year = date('Y',strtotime($request->input('payroll_month')));
                $date_to_update = date('Y-m-d');

            }else{

                $payroll_month = date('m',strtotime($request->input('payroll_month')));
                $payroll_year = date('Y',strtotime($request->input('payroll_month')));
                
                $date_to_update = "";

            }

            $batch_num = time();
            $total_payroll = 0;
            // we will implement the condition not generate payroll when payroll period date not complete  ->done 


            // mark all attendance half day if check out not mark
            
            $fetch_system_setting = HrmSetting::get(['payroll_start_date','tax_deduction_apply','club_rule_apply'])->toArray();

            if((!empty($fetch_system_setting) && empty($fetch_system_setting[0]['payroll_start_date']) )){

                return response()->json([

                    'status' => 'FALSE',
                    'title' => "Payroll date has not been set",
                    'text' => "If you want to generate or calculate payroll, first need to set the payroll date, go to system settings",
                    'icon' => "warning",
                    'link' => "system-settings",
                    'button' => "Add"
    
                    ]);

            }

            $tax_is_applied = (!empty($fetch_system_setting) && !empty($fetch_system_setting[0]['tax_deduction_apply']) ) ? $fetch_system_setting[0]['tax_deduction_apply'] : 0;

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


            // return response if date not complete of payroll
            if( time() < strtotime($date_to.'+2 days') &&  empty($request->input('calculatePayrollAmountForm')) ){

                return response()->json([

                'status' => 'FALSE',
                'title' => "Can't Generate Payroll",
                'text' => "You can generate payroll until ".date('d-M-Y',strtotime($date_to.'+2 days')),
                'icon' => "warning"

                ]);
            }

            if( strtotime($date_from) >  strtotime($date_to)){

                return response()->json([

                    'status' => 'FALSE',
                    'title' => "Can't Estimate Payroll Of Next Month",
                    'text' => "You can estimate payroll after ".date('d-M-Y',strtotime($date_from.'+1 days')),
                    'icon' => "warning"
    
                    ]);

            }

            // it will return all active and deactive (who's deactive in payroll period)
            $get_employees = $this->get_payroll_employee($date_from,$date_to,$request->input('department'),$request->input('employee_id'));

            $fetch_late_rule = AttendanceRule::where('attendance_deduction_rule_type',1)->get(['attendance_deduction_rule_day_count','attendance_deduction_rule_deduction_count'])->toArray(); //type 1 for late
            $fetch_half_rule = AttendanceRule::where('attendance_deduction_rule_type',2)->get(['attendance_deduction_rule_day_count','attendance_deduction_rule_deduction_count'])->toArray(); //type 2 for half

            $fehch_all_tax_slabs = TaxSlab::where(function($query) use($date_from,$date_to) {
                $query->where('ts_fiscal_start','<=',$date_from);
                $query->where('ts_fiscal_end','>=',$date_from);


            })->orWhere(function($query) use($date_from,$date_to) {

                $query->where('ts_fiscal_start','<=',$date_to);
                $query->where('ts_fiscal_end','>=',$date_to);


            })
            ->get()->toArray(); 

            $fiscal_year_in_slab = array_column($fehch_all_tax_slabs,'ts_fiscal_end');

            $fiscal_year_in_slabs = array_unique($fiscal_year_in_slab);
            
            
            $final_fiscal_year_in_slab = [];

            foreach($fiscal_year_in_slabs as $fiscal_year_in_slab  ){

                $final_fiscal_year_in_slab[$fiscal_year_in_slab] = [];

            }
            
            foreach($fehch_all_tax_slabs as $fehch_all_tax_slab){

                array_push($final_fiscal_year_in_slab[$fehch_all_tax_slab['ts_fiscal_end']],$fehch_all_tax_slab);

            }   

            if($tax_is_applied == 1 && $request->input('tax_deduction') == "yes" && empty($final_fiscal_year_in_slab)){

                return response()->json([

                    'status' => 'FALSE',
                    'title' => "Tax slab has not been define",
                    'text' => "If you want to apply the tax deduction, first need to set the tax slab",
                    'icon' => "warning",
                    'link' => "tax-slab",
                    'button' => "Add"
    
                    ]);

            }

            $late_rule = array_combine(array_column($fetch_late_rule, 'attendance_deduction_rule_day_count'),array_column($fetch_late_rule, 'attendance_deduction_rule_deduction_count'));
            $half_rule = array_combine(array_column($fetch_half_rule, 'attendance_deduction_rule_day_count'),array_column($fetch_half_rule, 'attendance_deduction_rule_deduction_count'));

            $fetch_payroll = Payroll::where('pc_year',$payroll_year)->where('pc_month',$payroll_month)->pluck('pc_employee_id')->toArray();

            $generated_payroll = 0;
            foreach( $get_employees as $employee ){

                    
                    if( strtotime($employee['employee_doj']) >  strtotime($date_to)){
                        continue;
                    }
                
                    // get attendance record of system of employee
                    $attendance = $this->get_employee_attendance($date_from,$date_to,$employee['employee_id']);

                    $atts = array('attendance' => $attendance);
                    $employee_with_att = array_merge( $employee , $atts );

                    $attendance_count = $this->get_employee_complete_attendance_status($date_from,$date_to,$employee_with_att,$club_rule_is_applied);
                    
                    $salary_of_employee = $this->calculate_salary_of_employee($date_from,$date_to,$attendance_count,$late_rule,$half_rule,$employee['employee_id'],$period_count);  
                    
                if(!in_array($employee['employee_id'],$fetch_payroll) &&  empty($request->input('calculatePayrollAmountForm'))  ) {
                  
                    if($attendance_count['present_count'] == 0 && $attendance_count['halfday_count'] == 0 && $attendance_count['late_count'] == 0 && $attendance_count['leave_count'] == 0){
                        $salary_of_employee['deduction_of_absent'] = $salary_of_employee['deduction_of_absent'] + $salary_of_employee['net_salary'] ;
                        $salary_of_employee['net_salary'] = '0';
                    }

                    $generated_payroll++;
                    $total_payroll += $salary_of_employee['net_salary'];

                    // store in db
                    $add_payroll = new Payroll();
                    $add_payroll->pc_employee_id = $employee['employee_id'];

                    $add_payroll->pc_month = $payroll_month ;
                    $add_payroll->pc_year = $payroll_year ;
                    $add_payroll->pc_date_from = $date_from;
                    $add_payroll->pc_date_to = $date_to;

                    $add_payroll->pc_employee_designation = $employee['designation_name'];
                    $add_payroll->pc_employee_department = $employee['department_name'];
                    $add_payroll->pc_employee_job_title = $employee['employee_jobtitle'];

                    $add_payroll->pc_employee_net_salary = $salary_of_employee['net_salary'];
                    $add_payroll->pc_employee_bank_name = $employee['bankname'];
                    $add_payroll->pc_employee_bank_account = $employee['accountno'];
                    $add_payroll->pc_employee_bank_iban = $employee['ibannumber'];

                    $add_payroll->pc_total_working_days = $salary_of_employee['total_days'];

                    $add_payroll->pc_present = $salary_of_employee['each_chunk_present'];
                    $add_payroll->pc_absent = $salary_of_employee['each_chunk_absent'];
                    $add_payroll->pc_off_day = $salary_of_employee['each_chunk_offday'];
                    $add_payroll->pc_leave = $salary_of_employee['each_chunk_leave'];
                    $add_payroll->pc_holiday = $salary_of_employee['each_chunk_holiday'];
                    
                    $add_payroll->pc_late_early = $salary_of_employee['each_chunk_late'];
                    $add_payroll->pc_half_day = $salary_of_employee['each_chunk_half_day'];

                    $add_payroll->pc_absent_deduction = $salary_of_employee['deduction_of_absent'];
                    $add_payroll->pc_late_early_deduction = $salary_of_employee['deduction_of_late'];
                    $add_payroll->pc_half_day_deduction = $salary_of_employee['deduction_of_halfday'];

                    $add_payroll->pc_half_day_deduction = $salary_of_employee['deduction_of_halfday'];
                    
                    $add_payroll->pc_employee_gross_salary = $salary_of_employee['each_chunk_gross_salary'];

                    $add_payroll->pc_created_at = date('Y-m-d') ;
                    $add_payroll->pc_created_by = session('user_session');

                    $add_payroll->pc_batch = $batch_num;
                    $add_payroll->save();

                    $loan_deduction = $this->laon_deduction_from_salary($payroll_month,$payroll_year,$employee['employee_id'],$add_payroll->pc_id);
                    $advance_salary = $this->advance_salary_deduction_from_salary($payroll_month,$payroll_year,$employee['employee_id'],$add_payroll->pc_id);
                    $employee_expense = $this->employee_expense_reimburse($date_to,$employee['employee_id'],$add_payroll->pc_id);
                    $overtime_reimbursement = $this->overtime_reimbursement($date_to,$employee['employee_id'],$add_payroll->pc_id);

                    if($tax_is_applied == 1 && $request->input('tax_deduction') == "yes"){

                        $tax_deduction = $this->tax_deduction_from_salary($date_from,$date_to,$employee['employee_id'],$add_payroll->pc_id,$salary_of_employee['each_chunk_gross_salary'],$final_fiscal_year_in_slab);

                    }
                }

                if(!empty($request->input('calculatePayrollAmountForm'))  ){

                    $total_payroll += $salary_of_employee['net_salary'];

                }
                
            }

            if(!empty($request->input('calculatePayrollAmountForm')) ){

                $title = "Payroll Estimate Amount .<br> Total Payroll = ".round($total_payroll,2).' '.$currency[0]['system_currency'] ;
                $icon = 'info';
            
            }else{

                if($generated_payroll>0){
                    $title = "Payroll Has Been Generated Successfully.<br> Total Payroll = ".round($total_payroll,2).' '.$currency[0]['system_currency'];
                    $icon = 'success';
                    $link = 'payroll?m='.$payroll_month.'&y='.$payroll_year;
                }else{
                    $title = "There's no employee or ungenerated payroll employee";
                    $icon = 'info';
                }

            }
            return response()->json([

                'status' => 'TRUE',
                'title' => $title,
                'text' => "Payroll of ".date('d-M-Y',strtotime($date_from))." till ".date('d-M-Y',strtotime($date_to)),
                'icon' => $icon,
                'link' => $link ?? '',

            ]);
        }catch(\Exception $e){

            $this->create_error_log("Generate Bulk Payroll", $e);

            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'title' =>  "Something went wrong. Please try again.",
                    'icon' => "warning"
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'title' =>  "Something went wrong. Please try again.",
                    'icon' => "warning",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            }

        }finally{

            ActivityTracker::track_activity('Generate Bulk Payroll');

        }
    }   


    // function return all active and given period deactive employee
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
        ->get(['employee_id','employee_code','employee_name','designation_name','department_name','employee_jobtitle','employee_offday1','bankname','ibannumber','accountno','employee_doj','employee_deactivate_date'])->toArray();

        return $fetch_employee;
    }


    // function get atteance of complete month of specific employee
    protected function get_employee_attendance($date_from,$date_to,$employee_id){

        $attendance = Attendance::where('att_employee_id',$employee_id)
        ->whereRaw('STR_TO_DATE(CONCAT(att_for_year,"-",att_for_month,"-",att_for_date),"%Y-%m-%d") >= "'.$date_from.'"  and STR_TO_DATE(CONCAT(att_for_year,"-",att_for_month,"-",att_for_date),"%Y-%m-%d") <= "'.$date_to.'" ')
        ->get()->toArray();

        return $attendance;

    }


    // count attendance status of each employee
    protected function get_employee_complete_attendance_status($date_from,$date_to,$attendances,$club_rule_is_applied){
        
        $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();
        
        $present_count = 0;
        $absent_count =0;
        $halfday_count = 0;
        $late_count = 0;
        $leave_count = 0;

        $date_with_status = [];
        $absent_dates = [];

        $attendances['employee_offday1'] = (!empty($attendances['employee_offday1'])) ? explode(",",$attendances['employee_offday1']):[];


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

                        }elseif(in_array($atty['att_check_overall_status'],$manage_leaves)){

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

                    if(in_array($nameOfDay,$attendances['employee_offday1']) ){
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

    protected function calculate_salary_of_employee($date_from,$date_to,$attendance_count,$late_deduction_count,$half_deduction_count,$employee_id,$period_count){

        if(Self::MONTH_COUNT == 30){

            $period_count = 30;

        }
        
        $date_with_status = $attendance_count['date_with_status'];

        $salary_period = EmployeeSalary::where('salary_employee_id',$employee_id)->where('salary_effected_date','>=',$date_from)->where('salary_effected_date','<=',$date_to)->where('salary_earning_type',1)->orderBy('salary_effected_date','ASC')->get()->toArray();

        if(empty($salary_period)){
            
            $salary_period = EmployeeSalary::where('salary_employee_id',$employee_id)->where('salary_earning_type',1)->orderBy('salary_effected_date','DESC')->limit(1)->get()->toArray();

        }else{

            $salary_period_last = EmployeeSalary::where('salary_employee_id',$employee_id)->where('salary_effected_date','<',$salary_period[0]['salary_effected_date'] )->where('salary_earning_type',1)->orderBy('salary_effected_date','DESC')->limit(1)->get()->toArray();

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


    protected function apply_club_rule($all_dates_of_periods,$attendances,$absent_dates,$date_with_status){

        $rule_absent = 0;
        $off_days = [];
        $weekends = [];
        $applied_club_rule_dates = [];
        $attendances['employee_offday1'] = (!empty($attendances['employee_offday1'])) ? explode(",",$attendances['employee_offday1']):[];

        foreach($all_dates_of_periods as $all_dates_of_period){

            $date_filter = $all_dates_of_period;
            $nameOfDay = date('l' , strtotime($date_filter));

            if(in_array($nameOfDay,$attendances['employee_offday1']) ){

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


    protected function laon_deduction_from_salary($month,$year,$employee_id,$payroll_id){


        $loan_amount = 0;
    
        $get_loan_records = Loan::where('employee_code',$employee_id)->where('loan_status','!=','4')->whereRaw('STR_TO_DATE(CONCAT(loan_deduction_start,"-01"),"%Y-%m-%d") <= "'.$year.'-'.$month.'-01"')->get()->toArray();

        foreach($get_loan_records as $get_loan_record){

            // for fixed amount
            if($get_loan_record['loan_deduction_type'] == "1"){
                
                // installment is greater or equal to remaining amount
                if($get_loan_record['loan_remaining_amount'] >= $get_loan_record['loan_deduction_amount'] ){

                    $loan_amount = $get_loan_record['loan_deduction_amount'];
                
                // other wise remaining amount use
                }else{

                    $loan_amount = $get_loan_record['loan_remaining_amount'];

                }   
                
            // percentage wise 
            }else{

                $calculate_installment_amount = ($get_loan_record['loan_given_amount']*$get_loan_record['loan_deduction_amount'])/100;

                // installment is greater or equal to remaining amount
                if($get_loan_record['loan_remaining_amount'] >= $calculate_installment_amount ){

                    $loan_amount =  $calculate_installment_amount;
                
                // other wise remaining amount use
                }else{

                    $loan_amount = $get_loan_record['loan_remaining_amount'];

                } 


            }

            // insert and update the record

            if($loan_amount>0){

                $add_installment = new LoanInstallment();
                $add_installment->loan_installment_loan_id = $get_loan_record['loan_id'];
                $add_installment->loan_installment_amount = $loan_amount;
                $add_installment->loan_installment_date = date('Y-m-d H:i:s');
                $add_installment->loan_installment_payroll_id = $payroll_id;
                $add_installment->save();

                $loan_remaining_amount = $get_loan_record['loan_remaining_amount']-$loan_amount;

                if($loan_remaining_amount == 0){

                    $upadate_loan_row = [
                        'loan_remaining_amount'=>$loan_remaining_amount,
                        'loan_submitted_amount'=>($get_loan_record['loan_submitted_amount']+$loan_amount),
                        'loan_status' => '4'];

                }else{

                    $upadate_loan_row = [
                        'loan_remaining_amount'=>$loan_remaining_amount,
                        'loan_submitted_amount'=>($get_loan_record['loan_submitted_amount']+$loan_amount)];

                }
                

                $upadate_loan_row = Loan::where('loan_id',$get_loan_record['loan_id'])->update($upadate_loan_row);
                
                $add_payroll_deduction = new PayrollDeduction();
                $add_payroll_deduction->pc_deduction_pc_id = $payroll_id;
                $add_payroll_deduction->pc_deduction_title = "Loan Deduction";
                $add_payroll_deduction->pc_deduction_amount = $loan_amount;
                $add_payroll_deduction->save();

            }

        }
        
    }

    protected function advance_salary_deduction_from_salary($month,$year,$employee_id,$payroll_id){

        $advance_salary_record = AdvanceSalary::where('employee_id',$employee_id)->where('request_approved_by_finance','=','2')->whereRaw('effective_payroll_period = "'.$year.'-'.$month.'"')->get()->toArray();
        
        if(!empty($advance_salary_record)){

            // $advance_salary_record
            $add_payroll_deduction = new PayrollDeduction();
            $add_payroll_deduction->pc_deduction_pc_id = $payroll_id;
            $add_payroll_deduction->pc_deduction_title = "Advance Salary Deduction";
            $add_payroll_deduction->pc_deduction_amount = $advance_salary_record[0]['advance_salary_amount'];
            $add_payroll_deduction->save();

        }

    }

    protected function get_employee_payroll_month(Request $request){
        

        $payroll_month = date('m',strtotime($request->input('payroll_month')));
        $payroll_year = date('Y',strtotime($request->input('payroll_month')));

        $fetch_system_setting = HrmSetting::get(['payroll_start_date'])->toArray();

        $date_to = (!empty($fetch_system_setting) && !empty($fetch_system_setting[0]['payroll_start_date']) ) ? $fetch_system_setting[0]['payroll_start_date'] : 1;

        if($date_to != 1){

            $date_to = $date_to.'-'.$payroll_month.'-'.$payroll_year;
            $date_to = date('Y-m-d',strtotime($date_to));

            $date_from = date('Y-m-d',strtotime($date_to.' -1 months' ));
            $date_to = date('Y-m-d',strtotime($date_to.' -1 days' ));

        }else{

            $date_to = $date_to.'-'.$payroll_month.'-'.$payroll_year;
            $date_to = date('Y-m-t',strtotime($date_to));
            $date_from = date('Y-m-1',strtotime($date_to));

        }
        
        $get_employees = $this->get_payroll_employee($date_from,$date_to,'');

        $get_payroll_generated_employee = Payroll::where('pc_year',$payroll_year)->where('pc_month',$payroll_month)->pluck('pc_employee_id')->toArray();

        $result[] = '<option value="">Select Name</option>';
        if(!empty($get_employees)){

            foreach($get_employees as $employee){

                if( strtotime($employee['employee_doj']) >  strtotime($date_to)){
                    continue;
                }
                
                if(!in_array($employee['employee_id'],$get_payroll_generated_employee)){

                    $result[] .= '<option value="'.$employee['employee_id'].'">'.$employee['employee_code']. ' - '. $employee['employee_name'].'</option>';

                }
            }
        }

        return response()->json([

            'status' => "TRUE",
            'get_employees' => $result
        
        ]);
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

    protected function payroll_disbursement(Request $request){


        try{

            $validator = Validator::make($request->all(), [
                'employee_ids' => ['required'],
                'confirmation_code' => ['required']
            ]);

            if($validator->fails() ) {
                foreach($validator->errors()->all() as $error){

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => $error,
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();
                
            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];
            
            if($confirmation_code === $request->input('confirmation_code')){

                if(!empty($request->input('employee_ids'))){

                    $result = 0;

                    if(!empty($request->input('included'))){
                        $get_month_year = Payroll::where('pc_id',$request->input('employee_ids')[0])->get(['pc_month','pc_year'])->toArray();
                        $get_employee_pc_id = Payroll::where('pc_month',$get_month_year[0]['pc_month'])->where('pc_year',$get_month_year[0]['pc_year'])->whereNotIn('pc_id',$request->input('employee_ids'))->pluck('pc_id')->toArray();
                    }else{
                        $get_employee_pc_id = $request->input('employee_ids');
                    }

                    if(empty($request->input('included'))){
                        foreach($get_employee_pc_id as $pc_id ){
                            $payroll_exist = Payroll::where('pc_id',$pc_id)->join('tbl_employees','tbl_employees.employee_id','tbl_payroll_calculation.pc_employee_id')->get(['disburse','employee_code','employee_name'])->toArray();
                            if(!empty($payroll_exist)){
                                if($payroll_exist[0]['disburse'] == 2){
                                    return response()->json([
                                        'status' => 'FALSE', 
                                        'msg' => 'Payroll of '.$payroll_exist[0]['employee_name'].'-'.$payroll_exist[0]['employee_code'].' has been already disbursed'
                                    ]);
                                }
                            }else{
                                return response()->json([
                                    'status' => 'FALSE', 
                                    'msg' => 'Payslip No.'.$pc_id.' has been deleted'
                                ]);
                            }

                        }
                    }
                    foreach($get_employee_pc_id as $pc_id ){

                        $disbursPayroll = Payroll::find($pc_id);

                        $notifiction_employee_id = $disbursPayroll->pc_employee_id;

                        $month = $disbursPayroll->pc_month;
                        $year = $disbursPayroll->pc_year;

                        $disbursPayroll->disburse = 2; 
                        $disbursPayroll->disburse_date = date('Y-m-d H:i:s');
                        $disbursPayroll->disburse_by = session('user_session');
                        $result = $disbursPayroll->save();

                        $notification_message = 'Dear employee, your salary of ('.date("F", mktime(0, 0, 0, $month, 10)).', '.$year.') has been disbursed. Click here to view payslip';

                        $this->send_notification('Salary Disbursement', $notification_message ,'my-payroll?slip-id='.$pc_id,$notifiction_employee_id,NULL,NULL,'no');


                    }


                    if($result == 1){

                        return response()->json([

                            'status' => 'TRUE', 
                            'msg' => 'Payroll disbursement successfully'
                
                        ]);

                    }else{

                        return response()->json([

                            'status' => 'FALSE', 
                            'msg' => 'Payroll disbursement failed'
                
                        ]);

                    }
                    
                }else{

                    return response()->json([

                        'status' => 'FALSE', 
                        'msg' => 'Payroll disbursement failed'
            
                    ]);

                }
            }else{

                return response()->json([

                    'status' => 'FALSE', 
                    'msg' => 'Confirmation is invalid'

                ]);

            }

        }catch(\Exception $e){

            $this->create_error_log("Payroll Disbursement", $e);
            
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

            ActivityTracker::track_activity('Payroll Disbursement');

        }
        
    }



    protected function tax_deduction_from_salary($date_from,$date_to,$employee_id,$payroll_id,$gross_salary,$all_tax_slabs){
        

        if(count($all_tax_slabs)>1){

            $i = 0;

            foreach($all_tax_slabs as $fiscal_end_date => $tax_slabs){

                $i++;

                foreach($tax_slabs as $tax_slab){

                    $gross_salary_of_year = $gross_salary*12;
                    
                    if($tax_slab['ts_lowest_yearly_income'] <= $gross_salary_of_year && $tax_slab['ts_highest_yearly_income'] >= $gross_salary_of_year ){
        
                        $total_tax_of_year = ($gross_salary_of_year - $tax_slab['ts_lowest_yearly_income']) * ($tax_slab['ts_deduction_percentage']/100) + $tax_slab['ts_deduction_amount'] ;

                        if($total_tax_of_year>0){
        
                            $tax_of_this_per_day = $total_tax_of_year/365;

                            if($i==1){

                                $days_count_of_tax = $this->days_count_between_two_dates($date_from,array_key_first($all_tax_slabs));

                            }else{

                                $days_count_of_tax = ($this->days_count_between_two_dates(array_key_first($all_tax_slabs),$date_to))-1;

                            }
                            
                            $tax_of_this_month = $tax_of_this_per_day * $days_count_of_tax;

                            $add_payroll_tax_deduction = new PayrollDeduction();
                            $add_payroll_tax_deduction->pc_deduction_pc_id = $payroll_id;
                            $add_payroll_tax_deduction->pc_deduction_title = "Income Tax Deduction";
                            $add_payroll_tax_deduction->pc_deduction_amount = $tax_of_this_month;
                            $add_payroll_tax_deduction->save();
        
                        }
        
                        break;
                    }
        
                }

            }
        }else{


            foreach($all_tax_slabs as $fiscal_end_date => $tax_slabs){

                foreach($tax_slabs as $tax_slab){

                    $gross_salary_of_year = $gross_salary*12;
        
                    if($tax_slab['ts_lowest_yearly_income'] <= $gross_salary_of_year && $tax_slab['ts_highest_yearly_income'] >= $gross_salary_of_year ){
        
                        $total_tax_of_year = ($gross_salary_of_year - $tax_slab['ts_lowest_yearly_income']) * ($tax_slab['ts_deduction_percentage']/100) + $tax_slab['ts_deduction_amount'] ;
                        
                        if($total_tax_of_year>0){
        
                            $tax_of_this_month = $total_tax_of_year/12;
        
                            $add_payroll_tax_deduction = new PayrollDeduction();
                            $add_payroll_tax_deduction->pc_deduction_pc_id = $payroll_id;
                            $add_payroll_tax_deduction->pc_deduction_title = "Income Tax Deduction";
                            $add_payroll_tax_deduction->pc_deduction_amount = $tax_of_this_month;
                            $add_payroll_tax_deduction->save();
        
                        }
        
                        break;
                    }
        
                }
            }
        }

    }

    protected function account_identification(Request $request){
    
        try{

            $user_password = Employees::where('employee_id',session('user_session'))->where('employee_isactive',1)->get(['employee_password'])->toArray();
              
            if(!empty($user_password)){
                if(Crypt::decryptString($user_password[0]['employee_password']) == $request->input('user_password') ){
                
                    $request->session()->put([
                        'my_payroll'=>'allow'
                    ]);
    
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Account identification successfully completed.",
                    ]);
    
                }else{
    
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Account identification failed",
                    ]);
                }
            }else{
                
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Account identification failed",
                ]);

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

    protected function payroll_identification(Request $request){
    
        try{

            $validator = Validator::make($request->all(), [
                'verification_code.*' => ['required']
            ],[
                'verification_code.*.required' => 'The verification code field is required.'
            ]);

            if($validator->fails() ) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => $error,
                    ]);
                }
            }
              
            if(implode("",$request->input('verification_code')) == Self::verification_code){
                $remaining_attempts = '';
                $get_last_five_attempts = PayrollAttempts::where('employee_id', session('user_session'))->orderBy('attempt_id','DESC')->limit(5)->get()->toArray();
                $attempts = array();
                if(!empty($get_last_five_attempts)){
                    foreach($get_last_five_attempts as $attempt){
                        if($attempt['attempt_status'] == 1){
                            break;
                        }
                        $attempts[] = $attempt;
                    }
                    $remaining_attempts = 5 - count($attempts);
                }

                if($remaining_attempts == 0){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Payroll access has been blocked on your account. Please contact HR.",
                    ]);
                }
                
                $request->session()->put([
                    'payroll_management'=>'allow'
                ]);

                $create_new_log = new PayrollAttempts();
                $create_new_log->employee_id = session('user_session');
                $create_new_log->password_entered = implode("",$request->input('verification_code'));
                $create_new_log->attempt_status = 1;
                $create_new_log->created_at = date('Y-m-d H:i:s');
                $create_new_log->save();

                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Verification completed successfully",
                ]);
            }else{

                $create_new_log = new PayrollAttempts();
                $create_new_log->employee_id = session('user_session');
                $create_new_log->password_entered = implode("",$request->input('verification_code'));
                $create_new_log->attempt_status = 2;
                $create_new_log->created_at = date('Y-m-d H:i:s');
                $create_new_log->save();

                $get_last_five_attempts = PayrollAttempts::where('employee_id', session('user_session'))->orderBy('attempt_id','DESC')->limit(5)->get()->toArray();
                $attempts = array();
                $remaining_attempts = '';
                if(!empty($get_last_five_attempts)){
                    foreach($get_last_five_attempts as $attempt){
                        if($attempt['attempt_status'] == 1){
                            break;
                        }
                        $attempts[] = $attempt;
                    }
                    $remaining_attempts = 5 - count($attempts);
                }

                if($remaining_attempts > 0){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Incorrect verification code. Only " . $remaining_attempts . " attempt(s) left.",
                    ]);
                }elseif($remaining_attempts == 0){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Payroll access has been blocked on your account. Please contact HR.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Incorrect verification code.",
                    ]);
                }
                
            }

        }catch(\Exception $e){
            $this->create_error_log("Verification Manage Payroll", $e);

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
            ActivityTracker::track_activity('Verification Manage Payroll');
        }

    }

    protected function payroll_delete(Request $request){

        try{
            

            $validator = Validator::make($request->all(), [

                'payroll_id' => 'required',
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return response()->json([

                        'status' => 'FALSE',
                        'msg' => $error,
        
                    ]);
                }
            }

            $delete_payroll_ids = explode(",",$request->input('payroll_id'));

            foreach($delete_payroll_ids as $payroll_id){
                $check_payroll_exist = Payroll::where('pc_id',$payroll_id)->get()->toArray();
                if(empty($check_payroll_exist)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Payslip already deleted. Kindly generate again. Slip No. ".$payroll_id,
                    ]);
                }else{

                    if($check_payroll_exist[0]['disburse'] == 2){
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Payroll can't be delete after disbursement. Slip No. ".$payroll_id,
                        ]);
                    }
                }
            }
            
            $delete_payroll = $this->delete_payroll_array($delete_payroll_ids);
              
            if($delete_payroll == 1){
                
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Payroll delete successfully",
                ]);

            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Payroll can't be delete after disbursement.",
                ]);
            }


        }catch(\Exception $e){

            $this->create_error_log("Delete Payroll", $e);

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

            ActivityTracker::track_activity('Delete Payroll');

        }

    }

    protected function payroll_delete_by_department(Request $request){

        try{

            $validator = Validator::make($request->all(), [

                'department' => 'required',
                'month' => 'required',
                'year' => 'required',
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return response()->json([

                        'status' => 'FALSE',
                        'msg' => $error,
        
                    ]);
                }
            }


            $delete_department = $request->input('department');

            $all_employees = Employees::whereIn('employee_department',$delete_department)->pluck('employee_id')->toArray();

            $get_delete_payroll_ids = Payroll::whereIn('pc_employee_id',$all_employees)->where('pc_month',$request->input('month'))->where('pc_year',$request->input('year'))->pluck('pc_id')->toArray();

            $delete_payroll = $this->delete_payroll_array($get_delete_payroll_ids);

            if($delete_payroll == 1){
                    
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Department Payroll delete successfully",
                ]);

            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "No undisburse payroll found of this department(s)",
                ]);
            }

        }catch(\Exception $e){

            $this->create_error_log("Delete Department Payroll", $e);

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

            ActivityTracker::track_activity('Delete Department Payroll');

        }

    }  

    protected function calculate_my_payroll(Request $request){

        try{
            $currency = CustomHelper::hcm_system_currency();

            $request->request->add(['employee_id' => session('user_session')]);

            if($request->input('month') == 1){
                $payroll_month = date('m');
                $payroll_year = date('Y');
            }else{
                $payroll_month = date('m',strtotime('-1 month'));
                $payroll_year = date('Y',strtotime('-1 month'));
            }
            
            $calculate_option = Payroll::where('disburse',2)->where('pc_employee_id',$request->input('employee_id'))->where('pc_month',$payroll_month)->where('pc_year',$payroll_year )->get()->toArray();
            if(!empty($calculate_option)){

                return response()->json([

                    'status' => 'FALSE',
                    'title' => '',
                    'text' => 'Dear employee salary slip of '.date("F", mktime(0, 0, 0, $payroll_month, 10)).' , '.$payroll_year.' has been created kindly check from payslips tab.',
                    'icon' => 'warning',
    
                ]);

            }

            $check_earning_type = Employees::where('employee_id',$request->input('employee_id'))->where('employee_earning_type',1)->get('employee_id')->toArray();

                if(!empty($check_earning_type)){
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

                $title = "Monthly Expected Earning(s) <br> ".round($total_payroll,2).' '.$currency[0]['system_currency'];
                $icon = 'info';
                
                return response()->json([

                    'status' => 'TRUE',
                    'title' => $title,
                    'text' => " Your expected earning(s) from ".date('d-M-Y',strtotime($date_from))." till ".date('d-M-Y',strtotime($date_to)). ". <span class='text-danger'>This amount isn't finalized. It can be above or below from your actual payroll.</span>",
                    'icon' => $icon,

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "You have not calculate your's monthly payroll, because your earning type is daily wage.",
                    'icon' => "warning",
                    'reload' => "TRUE",
                ]);
            }

        }catch(\Exception $e){

            $this->create_error_log("Calculate My Payroll", $e);

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

            ActivityTracker::track_activity('Calculate My Payroll');

        }

    }

    protected function employee_expense_reimburse($date_to,$employee_id,$pc_id){
        
        $fetch_expense_approved = EmployeeExpense::selectRaw('emp_expense_id, emp_expense_amount')
            ->where('emp_expense_employee',$employee_id)
            ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
            ->whereIn('emp_expense_amt_id',function($query){
                $query->from('tbl_employee_expense_amount')
                ->selectRaw('MAX(emp_expense_amt_id)')
                ->groupBy('emp_expense_exp_id');
            })
            ->where('emp_expense_approved_by_hr',2) // hr approved
            ->where('emp_expense_reimbursement',1) //unpaid
            ->where('emp_expense_date','<=',$date_to) 
            ->get()->toArray();

            if(!empty($fetch_expense_approved)){
                $update_status_expense_id = [];
                foreach($fetch_expense_approved as $expense){

                    array_push($update_status_expense_id,$expense['emp_expense_id']);
                    
                    $add_payroll_earning = new PayrollEarning();
                    $add_payroll_earning->pc_earning_pc_id = $pc_id;
                    $add_payroll_earning->pc_earning_title = 'Employee Expense Reimbursement';
                    $add_payroll_earning->pc_earning_amount = $expense['emp_expense_amount'];
                    $add_payroll_earning->save();

                }   
                $update_values = ['emp_expense_reimbursement' => 2, 'emp_expense_payslip_id' => $pc_id ];
                $update_status = EmployeeExpense::whereIn('emp_expense_id',$update_status_expense_id)->update($update_values);
            }
    }

    protected function overtime_reimbursement($date_to,$employee_id,$pc_id){

        $over_times = OverTime::selectRaw('over_time_id, IF(amount_payable != "",amount_payable,over_time_salary ) as amount')
        ->where('ovt_emp_id',$employee_id)
        ->where('over_time_reibursement_status',1)
        ->whereRaw('STR_TO_DATE(over_time_date,"%Y-%m-%d") <= "'.$date_to.'"')
        ->whereNull('ovt_payroll_id')
        ->get()
        ->toArray();
        if(!empty($over_times )){
        
            $over_times_amount = 0;
            $overtime_id = [];
            foreach($over_times as $over_time){
                $over_times_amount +=  $over_time['amount'];
                array_push($overtime_id,$over_time['over_time_id']);
            }

            OverTime::whereIn('over_time_id',$overtime_id)->update(['ovt_payroll_id' => $pc_id]);
            $add_payroll_earning = new PayrollEarning();
            $add_payroll_earning->pc_earning_pc_id = $pc_id;
            $add_payroll_earning->pc_earning_title = 'Over-Time';
            $add_payroll_earning->pc_earning_amount = $over_times_amount;
            $add_payroll_earning->save();
        }
    }   
    
    protected function delete_payroll_array($delete_payroll_ids){

        $delete_payroll = 0;

        foreach($delete_payroll_ids as $delete_payroll_id){

            $delete_payroll = Payroll::where('pc_id',$delete_payroll_id)->where('disburse',1)->delete();

            if($delete_payroll == 1){

                $delete_deduction = PayrollDeduction::where('pc_deduction_pc_id',$delete_payroll_id)->where('pc_deduction_type',1)->delete();
                $delete_earning = PayrollEarning::where('pc_earning_pc_id',$delete_payroll_id)->where('pc_earning_type',1)->delete();

                $get_delete_laon_installment = LoanInstallment::where('loan_installment_payroll_id',$delete_payroll_id)->get()->toArray();

                if(!empty($get_delete_laon_installment)){
                    
                    foreach($get_delete_laon_installment as $laon_installment){

                        $get_loan_details = Loan::find($laon_installment['loan_installment_loan_id']);

                        $get_loan_details->loan_remaining_amount = $get_loan_details->loan_remaining_amount + $laon_installment['loan_installment_amount'];
                        $get_loan_details->loan_submitted_amount = $get_loan_details->loan_submitted_amount - $laon_installment['loan_installment_amount'];

                        $get_loan_details->loan_status = 1;

                        $get_loan_details->save();

                    }

                    $delete_laon_installment = LoanInstallment::where('loan_installment_payroll_id',$delete_payroll_id)->delete();

                }

                $update_values = ['emp_expense_reimbursement' => 1, 'emp_expense_payslip_id' => null ];
                $update_status = EmployeeExpense::where('emp_expense_payslip_id',$delete_payroll_ids)->update($update_values);
                $update_overtime = OverTime::whereIn('ovt_payroll_id',$delete_payroll_ids)->update(['ovt_payroll_id' => Null]);


            }
        }

        return $delete_payroll;

    }

    // daily wages work start

    protected function daily_wages_payroll(Request $request){
        try{

            $title = Self::MAIN_TITLE_DAILY;

            $login_user = $this->get_login_user_detail();
            
            $dynamic_years = $this->dynamic_year_drop_down();

            if(!empty($request->input('m')) && !empty($request->input('y'))){
                $month = $request->input('m');
                $year = $request->input('y');
            }else{
                $month = date('m');
                $year = date('Y');
            }

            $all_department = Department::get(['department_id','department_name'])->toArray();

            // $daily_wages = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as dw_employee_extra_daily_wages  ,dw_employee_gross_daily_wages, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction)) as dw_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + dw_employee_gross_daily_wages)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction))) as dw_employee_net_daily_wages,`dw_id`, `dw_employee_id`,`dw_disburse`,dw_of_date,dw_created_at FROM `tbl_daily_wages` 
            // left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 2 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_daily_wages.dw_id 
            // left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 2 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_daily_wages.dw_id 
            // where date_format(dw_of_date,'%m') = ".$month."
            // and date_format(dw_of_date,'%Y') = ".$year."
            // group by dw_id ,pc_deduction_pc_id,pc_earning_pc_id");

            $daily_wages = DB::select("SELECT sum(( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ))) as dw_employee_extra_daily_wages  ,sum(dw_employee_gross_daily_wages) as dw_employee_gross_daily_wages, sum( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction)) as dw_employee_deduction , sum((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + dw_employee_gross_daily_wages)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction))) as dw_employee_net_daily_wages,`dw_id`, `dw_employee_id`,`dw_disburse`,dw_of_date,dw_created_at,count(dw_id) as wage_count FROM `tbl_daily_wages` 
            left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 2 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_daily_wages.dw_id 
            left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 2 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_daily_wages.dw_id 
            where date_format(dw_of_date,'%m') = ".$month."
            and date_format(dw_of_date,'%Y') = ".$year."
            group by dw_employee_id;");
            
            $daily_wages = json_decode(json_encode($daily_wages), true);
            
            $daily_wages_employee = Employees::
            where('employee_earning_type',2) //daily earning type
            ->where('employee_isactive',1) //active employee
            ->get(['employee_id','employee_code','employee_name'])->toArray();

            $fetch_employee_disburse = DailyWages::join('tbl_employees','tbl_employees.employee_id','=','tbl_daily_wages.dw_employee_id')
            ->whereRaw("date_format(dw_of_date,'%m') = ".$month)
            ->whereRaw("date_format(dw_of_date,'%Y') = ".$year)
            ->where('dw_disburse', 1)
            ->get(['dw_id','employee_name','employee_code','dw_of_date'])->toArray();

            $send_data = compact('title','login_user','daily_wages_employee','daily_wages','month','year','fetch_employee_disburse','all_department','dynamic_years');
            
            return view('payroll.daily-wages-payroll', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('Daily Wages', $e);
            $send_data = compact('title','login_user');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Daily Wages');
          
        }
    }

    protected function individual_generate_daily_wages(Request $request){

    try{
        $currency = CustomHelper::hcm_system_currency();

        $validator = Validator::make($request->all(), [
            'days_assign' => 'required|in:multiple,single,till',
            'wage_date' => ($request->input('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before_or_equal:'.date('Y-m-d',strtotime('-2 days')) : '',
            'from_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:to_date' : '',
            'to_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before_or_equal:'.date('Y-m-d',strtotime('-2 days')) : '',
            'employee_id' => 'required_without:department',
            'department' => 'array'
        ]);
        
        if($validator->fails()) {
            foreach($validator->errors()->all() as $error){
                return response()->json([
                    'status' => 'FALSE',
                    'title' => $error,
                    'text' => "",
                    'icon' => "warning"
                ]);
            }
        }
        $setting = HrmSetting::orderBy('setting_id','DESC')->get(['daily_wages_late_deduction','daily_wages_half_deduction'])->toArray();
        $login_user = $this->get_login_user_detail();

        if($request->input('days_assign') == 'multiple'){
            $to_date = new DateTime($request->input('to_date'));
            $to_date->modify('+1 day');
            $period = new DatePeriod(
                new DateTime($request->input('from_date')),
                new DateInterval('P1D'),
                $to_date
            );
            $dates_array = [];
            foreach ($period as $date) {
                array_push($dates_array,$date->format('Y-m-d'));
            }
        }elseif($request->input('days_assign') == 'till'){

            if(empty($request->input('department'))){
                $employee_doj = Employees::where('employee_id',$request->input('employee_id'))->get(['employee_doj'])->toArray();

                $to_date = new DateTime(date('Y-m-d',strtotime('-2 days')));
                $to_date->modify('+1 day');
                $period = new DatePeriod(
                    new DateTime($employee_doj[0]['employee_doj']),
                    new DateInterval('P1D'),
                    $to_date
                );
                $dates_array = [];
                foreach ($period as $date) {
                    array_push($dates_array,$date->format('Y-m-d'));
                }
            }else{

                $employee_doj = Employees::whereIn('employee_department',$request->input('department'))->where('employee_earning_type',2)->orderBy('employee_doj','asc')->pluck('employee_doj')->toArray();

                $to_date = new DateTime(date('Y-m-d',strtotime('-2 days')));
                $to_date->modify('+1 day');
                $period = new DatePeriod(
                    new DateTime($employee_doj[0]),
                    new DateInterval('P1D'),
                    $to_date
                );
                $dates_array = [];
                foreach ($period as $date) {
                    array_push($dates_array,$date->format('Y-m-d'));
                }

            }

        }else{
            $dates_array = [$request->input('wage_date')];
        }   
        
        if(!empty($request->input('employee_id'))){
            $employee_array = [$request->input('employee_id')];
        }else{
            $employee_array = Employees::whereIn('employee_department',$request->input('department'))->where('employee_earning_type',2)->pluck('employee_id')->toArray();
        }
        $get_employees = Employees::
        join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
        ->join('tbl_personal_informations','tbl_personal_informations.employeeid','tbl_employees.employee_id')
        ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
        ->where('employee_earning_type',2) //monthly earning time
        ->whereIn('employee_id',$employee_array) 
        ->get(['employee_id','employee_code','employee_name','designation_name','department_name','employee_jobtitle','employee_offday1','bankname','ibannumber','accountno','employee_doj','employee_deactivate_date'])->toArray();

        if($request->input('days_assign') == 'single' && !empty($request->input('employee_id'))){
            foreach($get_employees as $employee){

                $off_days_array = [];
                
                if(!empty($employee) && !empty($employee['employee_offday1'])){
                    $off_days_array = explode(",",$employee['employee_offday1']);
                }
                
                foreach($dates_array as $date){

                    $check_wages_excite = DailyWages::where('dw_employee_id',$employee['employee_id'])->where('dw_of_date',$date)->get(['dw_employee_id'])->toArray();

                    if(empty($check_wages_excite)){
                        $att_of_employee = $this->employee_attendance_by_date($employee['employee_id'],$date,$off_days_array);

                        if($att_of_employee != "Absent"){

                            $wage_of_date = $this->wage_of_date($employee['employee_id'],$date);
                        
                            if(!empty($wage_of_date)){

                                if( strtotime($employee['employee_doj']) <=  strtotime($date) ){

                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'title' => "The date of join of employee is greater than ".date('d-M-Y',strtotime($date)),
                                        'text' => "",
                                        'icon' => "warning"
                                    ]);
                                }
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'title' => "Daily wage of employee is not set of ".date('d-M-Y',strtotime($date)),
                                    'text' => "",
                                    'icon' => "warning"
                                ]);
                            }
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'title' => "The employee is absent on ".date('d-M-Y',strtotime($date)),
                                'text' => "",
                                'icon' => "warning"
                            ]);
                        }
                    }else{

                        return response()->json([
                            'status' => 'FALSE',
                            'title' => "Wage already exist on ".date('d-M-Y',strtotime($date)),
                            'text' => "",
                            'icon' => "warning"
                        ]);
                    }
                }
            }
        }

        $any_record = 0;

        foreach($get_employees as $employee){

            $off_days_array = [];
            
            if(!empty($employee) && !empty($employee['employee_offday1'])){
                $off_days_array = explode(",",$employee['employee_offday1']);
            }
            $entry = 0;
            $total_wages = 0;
            foreach($dates_array as $date){

                $check_wages_excite = DailyWages::where('dw_employee_id',$employee['employee_id'])->where('dw_of_date',$date)->get()->toArray();

                if(empty($check_wages_excite)){
                    $att_of_employee = $this->employee_attendance_by_date($employee['employee_id'],$date,$off_days_array);

                    if($att_of_employee != "Absent"){

                        $wage_of_date = $this->wage_of_date($employee['employee_id'],$date);
                    
                        if(!empty($wage_of_date)){

                            if( strtotime($employee['employee_doj']) <=  strtotime($date) ){

                                $employee_gross_daily_wages = $wage_of_date[0]['salary_amount'];
                                $deduction_amount = $wage_of_date[0]['salary_amount'] * $this->rate_by_attendance($att_of_employee,$setting);
                                $employee_net_daily_wages = $wage_of_date[0]['salary_amount']  - $wage_of_date[0]['salary_amount'] * $this->rate_by_attendance($att_of_employee,$setting);

                                $add_daily_wages = new DailyWages();
                                $add_daily_wages->dw_employee_id = $employee['employee_id'];
                                $add_daily_wages->dw_of_date = $date;
                                $add_daily_wages->dw_created_by = $login_user[0]['employee_id'];
                                $add_daily_wages->dw_employee_designation = $employee['designation_name'];
                                $add_daily_wages->dw_employee_department = $employee['department_name'];
                                $add_daily_wages->dw_employee_job_title = $employee['employee_jobtitle'];
                                $add_daily_wages->dw_employee_net_daily_wages = $employee_net_daily_wages;
                                $add_daily_wages->dw_employee_gross_daily_wages = $employee_gross_daily_wages;
                                $add_daily_wages->dw_employee_bank_name = $employee['bankname'];
                                $add_daily_wages->dw_employee_bank_account = $employee['accountno'];
                                $add_daily_wages->dw_employee_bank_iban = $employee['accountno'];
                                $add_daily_wages->dw_present = ($att_of_employee == "Present") ? 1 : 0;
                                $add_daily_wages->dw_absent = ($att_of_employee == "Absent") ? 1 : 0;
                                $add_daily_wages->dw_off_day = ($att_of_employee == "Off Day") ? 1 : 0;
                                $add_daily_wages->dw_leave = ($att_of_employee == "Leave") ? 1 : 0;
                                $add_daily_wages->dw_holiday = ($att_of_employee == "Holiday") ? 1 : 0;
                                $add_daily_wages->dw_late_early = ($att_of_employee == "Early out" || $att_of_employee == "Late Coming" ) ? 1 : 0;
                                $add_daily_wages->dw_half_day = ($att_of_employee == "Half day") ? 1 : 0;
                                $add_daily_wages->dw_absent_deduction = ($att_of_employee == "Absent") ? $deduction_amount : 0;
                                $add_daily_wages->dw_late_early_deduction = ($att_of_employee == "Early out" || $att_of_employee == "Late Coming" ) ? $deduction_amount : 0;
                                $add_daily_wages->dw_half_day_deduction = ($att_of_employee == "Half day") ? $deduction_amount : 0;
                                $add_daily_wages->dw_disburse = 1;
                                $add_daily_wages->dw_created_at = date('Y-m-d H:i:s');
                                $add_daily_wages->save();
                                if($entry == 0){
                                    $id = $add_daily_wages->dw_id;
                                }
                                $add_daily_wages->dw_batch = $id;
                                $add_daily_wages->save();
                                $entry++;
                                $any_record++;
                                $total_wages += $employee_net_daily_wages;
                            }
                        }
                    }
                }
            }
        }
       
        if($any_record > 0){

            return response()->json([
                'status' => 'TRUE',
                'title' => "Daily wages of employee created successfully",
                'text' => "Daily wages amount total wages = ".number_format($total_wages).' '.$currency[0]['system_currency'],
                'icon' => "success",
                'link' => "daily-wages-payroll?m=".date('m',strtotime($dates_array[0]))."&y=".date('Y',strtotime($dates_array[0]))
            ]);

        }else{
            return response()->json([
                'status' => 'FALSE',
                'title' => "There's no employee or ungenerated wages employee.",
                'icon' => "warning"
            ]);
        }
        
        }catch(\Exception $e){
            $this->create_error_log('Daily Wages Payroll', $e);

            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "Something went wrong. Please try again.",
                    'icon' => "warning"
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "Something went wrong. Please try again.",
                    'icon' => "warning",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            }
        }finally{
            ActivityTracker::track_activity('Daily Wages Payroll');
        }
    }

    protected function calculate_daily_wages(Request $request){

        try{
            $currency = CustomHelper::hcm_system_currency();

            $validator = Validator::make($request->all(), [
                'days_assign' => 'required|in:multiple,single,till',
                'wage_date' => ($request->input('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before_or_equal:'.date('Y-m-d',strtotime('-2 days')) : '',
                'from_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:to_date' : '',
                'to_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before_or_equal:'.date('Y-m-d',strtotime('-2 days')) : '',
                'employee_id' => 'required_without:department',
                'department' => 'array'
            ]);
    
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'status' => 'FALSE',
                        'title' => $error,
                        'text' => "",
                        'icon' => "warning"
                    ]);
                }
            }
            $setting = HrmSetting::orderBy('setting_id','DESC')->get(['daily_wages_late_deduction','daily_wages_half_deduction'])->toArray();
            $login_user = $this->get_login_user_detail();
    
            if($request->input('days_assign') == 'multiple'){
                $to_date = new DateTime($request->input('to_date'));
                $to_date->modify('+1 day');
                $period = new DatePeriod(
                    new DateTime($request->input('from_date')),
                    new DateInterval('P1D'),
                    $to_date
                );
                $dates_array = [];
                foreach ($period as $date) {
                    array_push($dates_array,$date->format('Y-m-d'));
                }
            }elseif($request->input('days_assign') == 'till'){
    
                if(empty($request->input('department'))){
                    $employee_doj = Employees::where('employee_id',$request->input('employee_id'))->get(['employee_doj'])->toArray();
    
                    $to_date = new DateTime(date('Y-m-d',strtotime('-2 days')));
                    $to_date->modify('+1 day');
                    $period = new DatePeriod(
                        new DateTime($employee_doj[0]['employee_doj']),
                        new DateInterval('P1D'),
                        $to_date
                    );
                    $dates_array = [];
                    foreach ($period as $date) {
                        array_push($dates_array,$date->format('Y-m-d'));
                    }
                }else{
    
                    $employee_doj = Employees::whereIn('employee_department',$request->input('department'))->where('employee_earning_type',2)->orderBy('employee_doj','asc')->pluck('employee_doj')->toArray();
    
                    $to_date = new DateTime(date('Y-m-d',strtotime('-2 days')));
                    $to_date->modify('+1 day');
                    $period = new DatePeriod(
                        new DateTime($employee_doj[0]),
                        new DateInterval('P1D'),
                        $to_date
                    );
                    $dates_array = [];
                    foreach ($period as $date) {
                        array_push($dates_array,$date->format('Y-m-d'));
                    }
    
                }
    
            }else{
                $dates_array = [$request->input('wage_date')];
            }   
            
            if(!empty($request->input('employee_id'))){
                $employee_array = [$request->input('employee_id')];
            }else{
                $employee_array = Employees::whereIn('employee_department',$request->input('department'))->where('employee_earning_type',2)->pluck('employee_id')->toArray();
            }
            $get_employees = Employees::
            join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_personal_informations','tbl_personal_informations.employeeid','tbl_employees.employee_id')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_earning_type',2) //monthly earning time
            ->whereIn('employee_id',$employee_array) 
            ->get(['employee_id','employee_code','employee_name','designation_name','department_name','employee_jobtitle','employee_offday1','bankname','ibannumber','accountno','employee_doj','employee_deactivate_date'])->toArray();
    
            $any_record = 0;
    
            foreach($get_employees as $employee){
    
                $off_days_array = [];
                
                if(!empty($employee) && !empty($employee['employee_offday1'])){
                    $off_days_array = explode(",",$employee['employee_offday1']);
                }
                $total_wages = 0 ;
                foreach($dates_array as $date){
    
                    $check_wages_excite = DailyWages::where('dw_employee_id',$employee['employee_id'])->where('dw_of_date',$date)->get()->toArray();
    
                    if(empty($check_wages_excite)){
                        $att_of_employee = $this->employee_attendance_by_date($employee['employee_id'],$date,$off_days_array);
    
                        if($att_of_employee != "Absent"){
    
                            $wage_of_date = $this->wage_of_date($employee['employee_id'],$date);
                        
                            if(!empty($wage_of_date)){
    
                                if( strtotime($employee['employee_doj']) <=  strtotime($date) ){
    
                                    $employee_net_daily_wages = $wage_of_date[0]['salary_amount']  - $wage_of_date[0]['salary_amount'] * $this->rate_by_attendance($att_of_employee,$setting);
                                    $total_wages += $employee_net_daily_wages;
                                    $any_record++;
                                }
                            }
                        }
                    }
                }
            }
           
            if($any_record > 0){
    
                return response()->json([
                    'status' => 'TRUE',
                    'title' => "Daily wages estimate amount <br> Total Wages = ".number_format($total_wages).' '.$currency[0]['system_currency'],
                    'text' => "",
                    'icon' => "success"
                ]);
    
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "There's no employee or ungenerated wages employee.",
                    'icon' => "warning"
                ]);
            }
            
            }catch(\Exception $e){
                $this->create_error_log('Calculate Daily Wages Payroll', $e);

                if(!env('APP_DEBUG')){
                    return response()->json([
                        'status' => 'FALSE',
                        'title' => "Something went wrong. Please try again.",
                        'icon' => "warning",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'title' => "Something went wrong. Please try again.",
                        'icon' => "warning",
                        'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                    ]);
                }

            }finally{
                ActivityTracker::track_activity('Calculate Daily Wages Payroll');
            }
        }

    protected function get_employee_daily_wages(Request $request){
        
        $date  = $request->input('wage_date');
        $get_employees = Employees::
        join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
        ->join('tbl_personal_informations','tbl_personal_informations.employeeid','tbl_employees.employee_id')
        ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
        ->where('employee_earning_type',2) //monthly earning time 
        ->get(['employee_id','employee_code','employee_name','designation_name','department_name','employee_jobtitle','employee_offday1','bankname','ibannumber','accountno','employee_doj','employee_deactivate_date'])->toArray();


        if( $date != "multiple"){
            $get_payroll_generated_employee = DailyWages::where('dw_of_date',$date)->pluck('dw_employee_id')->toArray();
        }else{
            $get_payroll_generated_employee = [];
        }


        $result[] = '<option value="">Select Name</option>';
        if(!empty($get_employees)){

            foreach($get_employees as $employee){
                if($date != "multiple"){
                    if( strtotime($employee['employee_doj']) >  strtotime($date) ){
                        continue;
                    }
                }
                if(!in_array($employee['employee_id'],$get_payroll_generated_employee)){
                    $result[] .= '<option value="'.$employee['employee_id'].'">'.$employee['employee_code']. ' - '. $employee['employee_name'].'</option>';
                }
            }
        }
        return response()->json([

            'status' => "TRUE",
            'get_employees' => $result
        
        ]);

    }

    protected function employee_attendance_by_date($employee_id,$date,$off_days_array){
        
        $attendance_of_date = Attendance::where('att_employee_id',$employee_id)->where('att_for_date',date('d',strtotime($date)))->where('att_for_month',date('m',strtotime($date)))->where('att_for_year',date('Y',strtotime($date)))->get(['att_check_overall_status'])->toArray();
        
        $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

        if(!empty($attendance_of_date)){
            if (substr($attendance_of_date[0]['att_check_overall_status'] , 0, 7) == 'Holiday'){
                
                $att_of_employee = 'Holiday';

            }elseif(in_array($attendance_of_date[0]['att_check_overall_status'],$manage_leaves)){

                $att_of_employee =  "Leave";

            }else{
                $att_of_employee = $attendance_of_date[0]['att_check_overall_status'];
            }
        }else{
            
            $days = date('l',strtotime($date));

            if(in_array($days,$off_days_array)){
                $att_of_employee = "Off Day";
            }else{
                $att_of_employee = "Absent";
            }
        }
        return $att_of_employee ;
    }

    protected function wage_of_date($employee,$date){

        $salary_period = EmployeeSalary::where('salary_employee_id',$employee)
        ->where('salary_earning_type',2)
        ->where('salary_effected_date','<=',$date)
        ->orderBy('salary_effected_date','DESC')->limit(1)->get()->toArray();

        return $salary_period;
    }    

    protected function rate_by_attendance($att_of_employee,$setting){

        $late_deduction_rate = $setting[0]['daily_wages_late_deduction'];
        $half_deduction_rate = $setting[0]['daily_wages_half_deduction'];
        $absent_deduction_rate = 1;
        
        if($att_of_employee == "Early out" ||  $att_of_employee == "Late Coming"){
            return $late_deduction_rate;
        }elseif($att_of_employee == "Absent"){
            return $absent_deduction_rate;
        }elseif($att_of_employee == "Half day"){
            return $half_deduction_rate;
        }else{
            return 0;
        }

    }

    protected function delete_daily_wages(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'daily_wage_id' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => $error,
                    ]);
                }
            }
            $daily_wages = DailyWages::where('dw_id',$request->input('daily_wage_id'))->get(['dw_disburse'])->toArray();
            if(!empty($daily_wages)){
                if($daily_wages[0]['dw_disburse'] == 1){


                    $delete_wage = $this->delete_daily_wages_main($request->input('daily_wage_id')); 
                    
                    if($delete_wage == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' =>  "Wage deleted successfully.",
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
                        'msg' => "Wage disbursed, you can't delete this wage.",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Wage already deleted.",
                ]);
            }
        
        }catch(\Exception $e){

            $this->create_error_log("Delete Daily Wage", $e);

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

            ActivityTracker::track_activity('Delete Daily Wage');

        }
    }

    protected function pdf_daily_wages(Request $request){

        try{

            $login_user = $this->get_login_user_detail();

            $all_dw_id = DailyWages::where('dw_id',$request->wage_id)->get(['dw_batch'])->toArray();   

            $daily_wages = DailyWages::join('tbl_employees','tbl_employees.employee_id','=','tbl_daily_wages.dw_employee_id')->where('dw_batch',$all_dw_id[0]['dw_batch'])->get()->toArray(); 
            
            $download_button_show = true;  
            if($request->input('download')=="TRUE"){
                $download_button_show = false;
            }
            if(!empty($daily_wages)){

                $all_dw_id = array_column($daily_wages,'dw_id');

                $extra_earning = PayrollEarning::whereIn('pc_earning_pc_id',$all_dw_id)->where('pc_earning_type',2)->get()->toArray();
        
                $extra_deduction = PayrollDeduction::whereIn('pc_deduction_pc_id',$all_dw_id)->where('pc_deduction_type',2)->get()->toArray();
            
            }
            $max_loop = max([count($extra_earning),count($extra_deduction)]);

            $extra_earning_amount = array_sum(array_column($extra_earning, 'pc_earning_amount'));

            $extra_deduction_amount = array_sum(array_column($extra_deduction, 'pc_deduction_amount'));

            $total_earning = $extra_earning_amount + array_sum(array_column($daily_wages,'dw_employee_gross_daily_wages'));
            $total_deduction = $extra_deduction_amount + array_sum(array_column($daily_wages,'dw_late_early_deduction')) + array_sum(array_column($daily_wages,'dw_absent_deduction')) + array_sum(array_column($daily_wages,'dw_half_day_deduction'));

        
            $setting = HrmSetting::orderBy('setting_id','DESC')->get(['name_on_payslip','signature_on_payslip'])->toArray();

            $finance_name = '';
            if(!empty($setting) && !empty($setting[0]['name_on_payslip'])){
                $finance_name = Employees::join('tbl_departments','tbl_departments.department_id','=','employee_department')->join('tbl_designations','tbl_designations.designation_id','=','employee_designation')->where('employee_id',$setting[0]['name_on_payslip'])->get(['employee_name','designation_name','department_name'])->toArray(); 
            }


            $send_data = compact('daily_wages','download_button_show','total_earning','total_deduction','setting','max_loop','extra_earning','extra_deduction','finance_name');
           
            if(!empty($daily_wages) && ( ($daily_wages[0]['dw_employee_id'] == session('user_session') && $daily_wages[0]['dw_disburse'] == 2) || $login_user[0]['employee_department'] == "1" || $login_user[0]['employee_department'] == "2" || $login_user[0]['employee_department'] == "3" )){

                if($request->input('download')=="TRUE"){

                    $pdf = Pdf::loadView('payroll.pdf-daily-wages', $send_data);
                    return $pdf->setPaper('a3', 'potrait')->download('Payslip-'.$daily_wages[0]['employee_code'].'-'.$daily_wages[0]['employee_name'].'-'.date("d-M-Y", strtotime($daily_wages[0]['dw_of_date'])).'.pdf');
                    
                }

                return view('payroll.pdf-daily-wages', $send_data);
                
            }else{

                return abort(404);

            }
        }catch(\Exception $e){

            $this->create_error_log('Daily Wage PDF', $e);

            $send_data = compact('title');

            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Daily Wage PDF');

           

        }
    }

    protected function edit_daily_wages(Request $request){

        try{
            $title = Self::MAIN_TITLE_DAILY;
        
            $login_user = $this->get_login_user_detail();

            $daily_wages = DailyWages::join('tbl_employees','tbl_employees.employee_id','=','tbl_daily_wages.dw_employee_id')->where('dw_id',$request->wage_id)->get()->toArray(); 

            if(!empty($daily_wages)){

                $fetch_extra_earning = PayrollEarning::where('pc_earning_pc_id',$daily_wages[0]['dw_id'])->where('pc_earning_type',2)->get()->toArray();
            
                $fetch_extra_deduction = PayrollDeduction::where('pc_deduction_pc_id',$daily_wages[0]['dw_id'])->where('pc_deduction_type',2)->get()->toArray();

            }
            

            $send_data = compact('login_user','title','daily_wages','fetch_extra_earning','fetch_extra_deduction');

        }catch(\Exception $e){

            $this->create_error_log('Edit daily dages', $e);
            $send_data = compact('title');

        }finally{

            ActivityTracker::track_activity('Edit daily dages');
            return view('payroll.edit-daily-wages-payroll', $send_data); 
        }

    }

    protected function update_daily_wages(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'dw_id' => 'required',
                'extra_earning_title' => 'array',
                'extra_earning_title.*' => 'required',
                'extra_deduction_title' => 'array',
                'extra_deduction_title.*' => 'required',
                'extra_earning_amount' => 'array',
                'extra_earning_amount.*' => 'required|min:1|max:999999|numeric',
                'extra_deduction_amount' => 'array',
                'extra_deduction_amount.*' => 'required|min:1|max:999999|numeric',
            
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }
            DB::beginTransaction();

            if(!empty($request->input('extra_earning_title'))){
                $i =0;
                $request_extra_earning_title = array_map('strtolower', $request->input('extra_earning_title'));
                foreach($request_extra_earning_title as $extra_earning_title ){
                    if(in_array($extra_earning_title,$this->not_editable_title)){
                        $earning_record = PayrollEarning::where('pc_earning_pc_id',$request->input('pc_id'))->where('pc_earning_type',1)->where('pc_earning_title',$extra_earning_title)->get(['pc_earning_title','pc_earning_amount'])->toArray();
                        if(count($earning_record) != array_count_values($request_extra_earning_title)[$extra_earning_title] ){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>  "Can't use ".$extra_earning_title." as title",
                            ]);
                        }
                    }
                }
            }
            if(!empty($request->input('extra_deduction_title'))){
                $i =0;
                $request_extra_deduction_title = array_map('strtolower', $request->input('extra_deduction_title'));
                foreach($request_extra_deduction_title as $extra_deduction_title ){
                    if(in_array($extra_deduction_title,$this->not_editable_title)){
                        $deduction_record = PayrollDeduction::where('pc_deduction_pc_id',$request->input('pc_id'))->where('pc_deduction_type',1)->where('pc_deduction_title',$extra_deduction_title)->get(['pc_deduction_title','pc_deduction_amount'])->toArray();
                        if(count($deduction_record) != array_count_values($request_extra_deduction_title)[$extra_deduction_title] ){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>  "Can't use ".$extra_deduction_title." as title",
                            ]);
                        }
                    }
                }
            }
            $delete_old_earning_record = PayrollEarning::where('pc_earning_pc_id',$request->input('dw_id'))->where('pc_earning_type',2)->delete();

            if(!empty($request->input('extra_earning_title'))){

                $i=0;
                foreach($request->input('extra_earning_title') as $extra_earning_title ){
                    if(!in_array(strtolower($extra_earning_title),$this->not_editable_title)){
                        if($request->input('extra_earning_amount')[$i] != '' && is_numeric($request->input('extra_earning_amount')[$i])){
                            $add_payroll_earning = new PayrollEarning();
                            $add_payroll_earning->pc_earning_pc_id = $request->input('dw_id');
                            $add_payroll_earning->pc_earning_title = $extra_earning_title;
                            $add_payroll_earning->pc_earning_amount = $request->input('extra_earning_amount')[$i];
                            $add_payroll_earning->pc_earning_type = 2;
                            $add_payroll_earning->save();
                        }
                    }
                    $i++;
                }

            }

        $delete_old_deduction_record = PayrollDeduction::where('pc_deduction_pc_id',$request->input('dw_id'))->where('pc_deduction_type',2)->delete();

        if(!empty($request->input('extra_deduction_title'))){
            $i=0;
            foreach($request->input('extra_deduction_title') as $extra_deduction_title ){
                if(!in_array(strtolower($extra_deduction_title),$this->not_editable_title)){
                    if($request->input('extra_deduction_amount')[$i] != '' && is_numeric($request->input('extra_deduction_amount')[$i])){
                        $add_payroll_deduction = new PayrollDeduction();
                        $add_payroll_deduction->pc_deduction_pc_id = $request->input('dw_id');
                        $add_payroll_deduction->pc_deduction_title = $extra_deduction_title;
                        $add_payroll_deduction->pc_deduction_amount = $request->input('extra_deduction_amount')[$i];
                        $add_payroll_deduction->pc_deduction_type = 2;
                        $add_payroll_deduction->save();
                    }
                }
                $i++;
            }
        }
        DB::commit();
        return response()->json([
            'status' => 'TRUE', 
            'msg' => 'Daily wage updated successfully'
        ]);
        }catch(\Exception $e){

            DB::rollback();

            $this->create_error_log('Update daily wage', $e);

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

            ActivityTracker::track_activity('Update daily wage');
        
        }

    }

    protected function wages_disbursement(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'employee_ids' => ['required'],
                'confirmation_code' => ['required']
            ]);
            if($validator->fails() ) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => $error,
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();
            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];
        
            if($confirmation_code === $request->input('confirmation_code')){
                if(!empty($request->input('employee_ids'))){
                    $result = 0;
                    if(!empty($request->input('included'))){
                        $get_month_year = DailyWages::where('dw_id',$request->input('employee_ids')[0])->get(['dw_of_date'])->toArray();
                        $get_employee_pc_id  = DailyWages::whereRaw("date_format(dw_of_date,'%m') = ".date('m',strtotime($get_month_year[0]['dw_of_date'])))
                        ->whereRaw("date_format(dw_of_date,'%Y') = ".date('Y',strtotime($get_month_year[0]['dw_of_date'])))
                        ->whereNotIn('dw_id',$request->input('employee_ids'))->pluck('dw_id')->toArray();
                        
                    }else{
                        $get_employee_pc_id = $request->input('employee_ids');
                    }
                    if(empty($request->input('included'))){
                        foreach($get_employee_pc_id as $pc_id ){
                            $payroll_exist = DailyWages::where('dw_id',$pc_id)->join('tbl_employees','tbl_employees.employee_id','tbl_daily_wages.dw_employee_id')->get(['dw_disburse','employee_code','employee_name'])->toArray();
                            if(!empty($payroll_exist)){
                                if($payroll_exist[0]['dw_disburse'] == 2){
                                    return response()->json([
                                        'status' => 'FALSE', 
                                        'msg' => 'Payroll of '.$payroll_exist[0]['employee_name'].'-'.$payroll_exist[0]['employee_code'].' has been already disbursed'
                                    ]);
                                }
                            }else{
                                return response()->json([
                                    'status' => 'FALSE', 
                                    'msg' => 'Payslip no.'.$pc_id.' has been deleted'
                                ]);
                            }

                        }
                    }
                    foreach($get_employee_pc_id as $pc_id ){

                        $disbursPayroll = DailyWages::find($pc_id);

                        $notifiction_employee_id = $disbursPayroll->dw_employee_id;
                        $date = $disbursPayroll->dw_of_date;
                        $disbursPayroll->dw_disburse = 2; 
                        $disbursPayroll->dw_disburse_date = date('Y-m-d H:i:s');
                        $disbursPayroll->dw_disburse_by = session('user_session');
                        $result = $disbursPayroll->save();

                        $notification_message = 'Dear employee, your wage of ('.date("d-M-Y", strtotime($date)).') has been disbursed. Click here to view payslip';

                        $this->send_notification('Wage Disbursement', $notification_message ,'my-payroll?slip-id='.$pc_id,$notifiction_employee_id,NULL,NULL,'no');
                    }

                    if($result == 1){
                        return response()->json([
                            'status' => 'TRUE', 
                            'msg' => 'Wage(s) disbursement successfully'
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE', 
                            'msg' => 'Wage(s) disbursement failed'
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE', 
                        'msg' => 'Payroll disbursement failed'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE', 
                    'msg' => 'Confirmation is invalid'
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log("Wage Disbursement", $e);

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
            ActivityTracker::track_activity('Wage Disbursement');
        }
        
    }

    protected function wages_delete_by_department(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'department' => 'required',
                'month' => 'required',
                'year' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => $error,
                    ]);
                }
            }

            $delete_payroll = 0;
            $delete_department = $request->input('department');

            $all_employees = Employees::whereIn('employee_department',$delete_department)->pluck('employee_id')->toArray();

            $get_delete_payroll_ids = DailyWages::whereIn('dw_employee_id',$all_employees)
            ->whereRaw("date_format(dw_of_date,'%m') = ".$request->input('month'))
            ->whereRaw("date_format(dw_of_date,'%Y') = ".$request->input('year'))
            ->pluck('dw_id')->toArray();

            foreach($get_delete_payroll_ids as $dw_id){
                $delete_payroll = $this->delete_daily_wages_main($dw_id);
            }   

            if($delete_payroll == 1){
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Department wage(s) delete successfully",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "No undisburse wage found of this department(s)",
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log("Delete Department Wages", $e);

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
            ActivityTracker::track_activity('Delete Department Wages');
        }
    }

    protected function delete_selected_daily_wages(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'payroll_id' => 'required',
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => $error,
                    ]);
                }
            }

            $delete_payroll = 0;
            $delete_payroll_ids = explode(",",$request->input('payroll_id'));

            foreach($delete_payroll_ids as $payroll_id){
                $check_payroll_exist = DailyWages::where('dw_id',$payroll_id)->get()->toArray();
                if(empty($check_payroll_exist)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Payslip already deleted. Kindly generate again. Slip No. ".$payroll_id,
                    ]);
                }else{

                    if($check_payroll_exist[0]['dw_disburse'] == 2){
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Wage can't be delete after disbursement. Slip No. ".$payroll_id,
                        ]);
                    }
                }
            }

            foreach($delete_payroll_ids as $dw_id){
                $delete_payroll = $this->delete_daily_wages_main($dw_id);
            }   
              
            if($delete_payroll == 1){
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Wage(s) delete successfully",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Wage can't be delete after disbursement.",
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log("Delete Selected Wage", $e);

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
            ActivityTracker::track_activity('Delete Selected Wage');
        }
    }
    protected function delete_daily_wages_main($daily_wage_id){

        $delete_wage =  DailyWages::where('dw_id',$daily_wage_id)->where('dw_disburse',1)->delete();

        if($delete_wage == 1){
            $delete_deduction = PayrollDeduction::where('pc_deduction_pc_id',$daily_wage_id)->where('pc_deduction_type',2)->delete();
            $delete_earning = PayrollEarning::where('pc_earning_pc_id',$daily_wage_id)->where('pc_earning_type',2)->delete();
        }
        return $delete_wage;
    }

    protected function download_daily_wages_pdf(Request $request){

        try{

            $login_user = $this->get_login_user_detail();

            $month = $request->input('month');
            $year = $request->input('year');
            
            if(!empty($request->input('download_file')) && $request->input('download_file') == 'excel'){
                $fetch_employee_payroll = DB::select("SELECT dw_id as 'Slip No.', employee_name as 'Employee Name', employee_code as 'Employee Code', dw_of_date  as 'Pay Period', dw_employee_gross_daily_wages as 'Gross Salary' , ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as 'Extra Earning' , ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction)) as 'Total Deduction' , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + dw_employee_gross_daily_wages)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction))) as 'Net Salary', DATE_FORMAT(dw_created_at,'%d-%M-%Y') as 'Generation Date' , dw_employee_department as 'Department', dw_employee_designation as 'Designation' , dw_employee_bank_name as 'Bank Name' ,dw_employee_bank_account as 'Account No.',dw_employee_bank_iban as 'IBAN',`dw_present` as 'Total Presents', `dw_late_early` as 'Total Late/Early Out', `dw_half_day` as 'Total Half Days', `dw_absent` as 'Total Absents', `dw_off_day` as 'Total Off Days', `dw_leave` as 'Total Leaves', `dw_holiday` as 'Total Holidays', date_format(dw_of_date,'%d-%M-%Y') as 'Wage Date' FROM `tbl_daily_wages` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 2 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_daily_wages.dw_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 2 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_daily_wages.dw_id 
                join tbl_employees on tbl_employees.employee_id = tbl_daily_wages.dw_employee_id
                where date_format(dw_of_date,'%m')  = '".$month."'
                and date_format(dw_of_date,'%Y') = '".$year."'
                group by dw_id ,pc_deduction_pc_id,pc_earning_pc_id ");
            }else{
                $fetch_employee_payroll = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,dw_employee_gross_daily_wages, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction)) as pc_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + dw_employee_gross_daily_wages)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction))) as pc_employee_net_salary,`dw_id`, `dw_employee_id`, `dw_of_date`, `dw_employee_designation`, `dw_employee_department`, `dw_employee_job_title`, `dw_employee_bank_name`, `dw_employee_bank_account`, `dw_employee_bank_iban`, `dw_present`, `dw_absent`, `dw_off_day`, `dw_leave`, `dw_holiday`, `dw_late_early`, `dw_half_day`, `dw_absent_deduction`, `dw_late_early_deduction`, `dw_half_day_deduction`, `dw_batch`, `dw_created_at`, `dw_updated_at` , dw_disburse, employee_name, employee_code FROM `tbl_daily_wages` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 2 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_daily_wages.dw_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 2 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_daily_wages.dw_id 
                join tbl_employees on tbl_employees.employee_id = tbl_daily_wages.dw_employee_id
                where date_format(dw_of_date,'%m') = '".$month."'
                and date_format(dw_of_date,'%Y') = '".$year."'
                group by dw_id ,pc_deduction_pc_id,pc_earning_pc_id ");
            }

            $fetch_employee_payroll = json_decode(json_encode($fetch_employee_payroll), true);

            $download_button_show = true;

            if($request->input('download')=="TRUE"){
                $download_button_show = false;
            }

            $send_data = compact('fetch_employee_payroll','month','year','download_button_show');

            
            if( $login_user[0]['employee_department'] == "1" || $login_user[0]['employee_department'] == "2" || $login_user[0]['employee_department'] == "3" ){

                if($request->input('download')=="TRUE"){
                    
                    if(!empty($request->input('download_file')) && $request->input('download_file') == 'excel'){
                        return (new FastExcel($fetch_employee_payroll))->download('Wages-'.date("F", mktime(0, 0, 0, $month, 10)).'-'.$year.'.xlsx');
                    }else{
                        $pdf = Pdf::loadView('payroll.all-daily-wages-pdf', $send_data);
                        return $pdf->setPaper('a3')->download('Wages-'.date("F", mktime(0, 0, 0, $month, 10)).'-'.$year.'.pdf');
                    }

                }
                return view('payroll.all-daily-wages-pdf',$send_data);
                
            }else{

                return abort(404);

            }
            
        }catch(\Exception $e){

            $this->create_error_log('Complete Wages PDF', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{


            ActivityTracker::track_activity('Complete Wages PDF');

        }
    }

    protected function daily_wages_employee(Request $request){

        try{

            $title = Self::MAIN_TITLE_DAILY;

            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();

            if(!empty($request->input('m')) && !empty($request->input('y'))){
                $month = $request->input('m');
                $year = $request->input('y');
            }else{
                $month = date('m');
                $year = date('Y');
            }

            $all_department = Department::get(['department_id','department_name'])->toArray();

            $daily_wages = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as dw_employee_extra_daily_wages  ,dw_employee_gross_daily_wages, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction)) as dw_employee_deduction , ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + dw_employee_gross_daily_wages)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (dw_absent_deduction + dw_late_early_deduction + dw_half_day_deduction))) as dw_employee_net_daily_wages,`dw_id`, `dw_employee_id`,`dw_disburse`,dw_of_date,dw_created_at FROM `tbl_daily_wages` 
            left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 2 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_daily_wages.dw_id 
            left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 2 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_daily_wages.dw_id 
            where date_format(dw_of_date,'%m') = ".$month."
            and date_format(dw_of_date,'%Y') = ".$year."
            and dw_employee_id = ".(!empty($request->input('employee')) ? $request->input('employee') : $login_user[0]['employee_id'] )."
            group by dw_id ,pc_deduction_pc_id,pc_earning_pc_id");
            
            $daily_wages = json_decode(json_encode($daily_wages), true);
            
            $daily_wages_employee = Employees::
            where('employee_earning_type',2) //daily earning type
            ->where('employee_isactive',1) //active employee
            ->get(['employee_id','employee_code','employee_name'])->toArray();

            $fetch_employee_disburse = DailyWages::join('tbl_employees','tbl_employees.employee_id','=','tbl_daily_wages.dw_employee_id')
            ->whereRaw("date_format(dw_of_date,'%m') = ".$month)
            ->whereRaw("date_format(dw_of_date,'%Y') = ".$year)
            ->where('dw_disburse', 1)
            ->get(['dw_id','employee_name','employee_code','dw_of_date'])->toArray();

            $send_data = compact('title','login_user','daily_wages_employee','daily_wages','month','year','fetch_employee_disburse','all_department','dynamic_years');

            return view('payroll.daily-wages-employee', $send_data);

           
        }catch(\Exception $e){

            $this->create_error_log('Daily wages of employee', $e);
            $send_data = compact('title','login_user');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Daily wages of employee');
         
        }
    } 

    protected function calculate_my_wages(Request $request){
        try{
            $currency = CustomHelper::hcm_system_currency();

            $validator = Validator::make($request->all(), [
                'days_assign' => 'required|in:multiple,single,till',
                'wage_date' => ($request->input('days_assign') == 'single' ) ? 'required|date_format:Y-m-d|before_or_equal:'.date('Y-m-d',strtotime('-2 days')) : '',
                'from_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before:to_date' : '',
                'to_date' => ($request->input('days_assign') == 'multiple' ) ? 'required|date_format:Y-m-d|before_or_equal:'.date('Y-m-d',strtotime('-2 days')) : '',

            ]);
    
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'status' => 'FALSE',
                        'title' => $error,
                        'text' => "",
                        'icon' => "warning"
                    ]);
                }
            }

            $setting = HrmSetting::orderBy('setting_id','DESC')->get(['daily_wages_late_deduction','daily_wages_half_deduction'])->toArray();
            $login_user = $this->get_login_user_detail();
    
            if($request->input('days_assign') == 'multiple'){
                $to_date = new DateTime($request->input('to_date'));
                $to_date->modify('+1 day');
                $period = new DatePeriod(
                    new DateTime($request->input('from_date')),
                    new DateInterval('P1D'),
                    $to_date
                );
                $dates_array = [];
                foreach ($period as $date) {
                    array_push($dates_array,$date->format('Y-m-d'));
                }
            }elseif($request->input('days_assign') == 'till'){
    
                $employee_doj = Employees::where('employee_id',$login_user[0]['employee_id'])->get(['employee_doj'])->toArray();

                $to_date = new DateTime(date('Y-m-d',strtotime('-2 days')));
                $to_date->modify('+1 day');
                $period = new DatePeriod(
                    new DateTime($employee_doj[0]['employee_doj']),
                    new DateInterval('P1D'),
                    $to_date
                );
                $dates_array = [];
                foreach ($period as $date) {
                    array_push($dates_array,$date->format('Y-m-d'));
                }
    
            }else{
                $dates_array = [$request->input('wage_date')];
            }   

            $employee_array = [$login_user[0]['employee_id']];

            $get_employees = Employees::
            join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_personal_informations','tbl_personal_informations.employeeid','tbl_employees.employee_id')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_earning_type',2) //monthly earning time
            ->whereIn('employee_id',$employee_array) 
            ->get(['employee_id','employee_code','employee_name','designation_name','department_name','employee_jobtitle','employee_offday1','bankname','ibannumber','accountno','employee_doj','employee_deactivate_date'])->toArray();
    
            if(!empty($get_employees)){
    
                foreach($get_employees as $employee){
        
                    $off_days_array = [];
                    
                    if(!empty($employee) && !empty($employee['employee_offday1'])){
                        $off_days_array = explode(",",$employee['employee_offday1']);
                    }
                    $total_wages = 0 ;
                    foreach($dates_array as $date){
                
                        $att_of_employee = $this->employee_attendance_by_date($employee['employee_id'],$date,$off_days_array);
    
                        if($att_of_employee != "Absent"){
    
                            $wage_of_date = $this->wage_of_date($employee['employee_id'],$date);
                        
                            if(!empty($wage_of_date)){
    
                                if( strtotime($employee['employee_doj']) <=  strtotime($date) ){
    
                                    $employee_net_daily_wages = $wage_of_date[0]['salary_amount']  - $wage_of_date[0]['salary_amount'] * $this->rate_by_attendance($att_of_employee,$setting);
                                    $total_wages += $employee_net_daily_wages;
                                }
                            }
                        }
                    }
                }
        
                return response()->json([
                    'status' => 'TRUE',
                    'title' => "Daily wages estimate amount <br> Total Wages = ".number_format($total_wages).' '.$currency[0]['system_currency'],
                    'text' => "",
                    'icon' => "success"
                ]);
                
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "You have not calculate your's daily wage, because your earning type is monthly.",
                    'icon' => "warning",
                    'reload' => "TRUE",
                ]);
            }
            
        }catch(\Exception $e){
            $this->create_error_log('Calculate My Daily Wages', $e);

            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "Something went wrong. Please try again.",
                    'icon' => "warning",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "Something went wrong. Please try again.",
                    'icon' => "warning",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            }

        }finally{
            ActivityTracker::track_activity('Calculate My Daily Wages');
        }
        
    }

    protected function deduction_policy(){
        try{
            $title = Self::DEDUCTION_TITLE;
            $login_user = $this->get_login_user_detail();
            $send_data = compact('title','login_user');
            return view('payroll.deduction-policy', $send_data);
        }catch(\Exception $e){
            $this->create_error_log('Deduction Policy Fetch', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('Deduction Policy');
           
        }
    }

    protected function get_deduction_source_data(Request $request){
        try{
          
            $get_deduction_source = $request->input('deduction_source_id');
           
            $validator = Validator::make($request->all(), [
                'deduction_source_id' => ['required','in:1,2'],
            
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return Redirect::back()->with('error', $error);
                }
            }
            if(!empty($get_deduction_source)){
               
                $get_deduction_source_data = AttendanceRule::where('attendance_deduction_rule_type', $get_deduction_source)->get()->toArray();
                if(!empty($get_deduction_source_data)){
                    return response()->json([
                        'status' => 'TRUE',
                        'data' => $get_deduction_source_data,
                        'type' =>$get_deduction_source,
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'title' => "No record found for this deduction source.",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Deduction source is required.",
                ]);
            }

        }catch(\Exception $e){
            $this->create_error_log('Add Form Shift Time', $e);
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
            ActivityTracker::track_activity('Deduction Policy');
        }
    }

    protected function update_deduction_policy(Request $request){
        try{
            $message = ['required'=>'The deduction count field is required','gte'=>'The deduction count must be greater than or equal to 0 integer','lte'=>'The deduction count must be less than or equal to 30 integer'];

            $validator = Validator::make($request->all(), [
                'deduction_days.*' => ['required','gte:0','lte:30'],
                'deduction_type' => ['required','in:1,2'],
            
            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            if(!empty($request->input('deduction_days')) && is_array($request->input('deduction_days'))){
                AttendanceRule::where('attendance_deduction_rule_type', $request->input('deduction_type'))->delete();
                $process = '';
                foreach($request->input('deduction_days') as $days=>$deduct){
                
                    $attendance_rule = new AttendanceRule();
                    $attendance_rule->attendance_deduction_rule_type = $request->input('deduction_type');
                    $attendance_rule->attendance_deduction_rule_day_count = $days ;
                    $attendance_rule->attendance_deduction_rule_deduction_count = $deduct ;
                    $process = $attendance_rule->save();

                }

                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Deduction policy updated successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Deduction policy updated failed. Please try again after some time.",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Deduction days not found.",
                ]);
            }

        }catch(\Exception $e){
            $this->create_error_log('Update Attendance Deduction Rule Policy', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "Something went wrong. Please try again.",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            }
        }finally{
            ActivityTracker::track_activity('Update Attendance Deduction Rule Policy');
        }
    }
}


