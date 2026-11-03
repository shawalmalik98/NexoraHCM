<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator,Crypt};
use Barryvdh\DomPDF\Facade\Pdf;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\{Employees, Attendance, AttendanceRule, EmployeeSalary, Payroll, PayrollDeduction, PayrollEarning, Department, Loan, LoanInstallment, AdvanceSalary, TaxSlab, HrmSetting, EmployeeExpense, OverTime, EmployeeDevice, LeaveManagement, SalaryDivision, PayrollAttempts,EmploymentStatus, ProvidentFund};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait, PayrollCalculation};
use App\Models\{LeaveDeduction, Resignation};
use App\Helpers\{ActivityTracker,CustomHelper};
use Illuminate\Support\Facades\Redirect;

use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{   
    use LoginEmployee, LogGenerateTrait , NotificationTrait, ApiResponseTrait, PayrollCalculation;

    const MAIN_TITLE = "Payroll";
    const ADD_TITLE = "Add Payroll";
    const INDIVIDUAL_PDF_TITLE = "Payslip";
    const SUB_TITLE = "My Payslip";
    const verification_code = '123789';
    const DEDUCTION_TITLE = 'deduction policy';

    const MONTH_COUNT = 0;
    // the payroll date limit 1-28 otherwise code not working correct


    protected $not_editable_title = ['gross salary','basic salary','absent','half day','late comming / early out','early out','late comming','loan deduction','advance salary deduction','income tax deduction','over-time','employee expense reimbursement','eobi contribution','provident fund'];

    protected function index(Request $request){

        try{

            $title = Self::MAIN_TITLE;
        
            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();

            if(!empty($request->input('m')) && !empty($request->input('y'))){

                $month = (int) $request->input('m');
                $year = (int) $request->input('y');

            }else{

                $month = (int) date('m');
                $year = (int) date('Y');

                // nothing generated for this month yet: show the latest generated payroll instead of an empty table
                if(!Payroll::where('pc_month', $month)->where('pc_year', $year)->exists()){
                    $latest = Payroll::orderByDesc('pc_year')->orderByDesc('pc_month')->first(['pc_month','pc_year']);
                    if(!empty($latest)){
                        $month = (int) $latest->pc_month;
                        $year = (int) $latest->pc_year;
                    }
                }
            }

            $all_department = Department::get(['department_id','department_name'])->toArray();

            $fetch_employee_payroll = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,pc_employee_gross_salary, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , GREATEST(0, ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`,`disburse`,pc_year,pc_month,pc_created_at FROM `tbl_payroll_calculation` 
                left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 1 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_payroll_calculation.pc_id 
                left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 1 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_payroll_calculation.pc_id 
                where pc_month = '".$month."'
                and pc_year = '".$year."'
                group by pc_id ,pc_deduction_pc_id,pc_earning_pc_id ");

            $fetch_employee_payroll = json_decode(json_encode($fetch_employee_payroll), true);

            // what each total is made of, shown under the totals: absent / late / half day, tax, EOBI, PF, loan, advance ... and overtime, expense ...
            $payroll_ids = array_column($fetch_employee_payroll, 'pc_id');
            $deduction_breakdown = [];
            $earning_breakdown = [];
            if(!empty($payroll_ids)){
                foreach(Payroll::whereIn('pc_id', $payroll_ids)->get(['pc_id','pc_absent_deduction','pc_late_early_deduction','pc_half_day_deduction']) as $row){
                    foreach(['Absent' => $row->pc_absent_deduction, 'Late' => $row->pc_late_early_deduction, 'Half Day' => $row->pc_half_day_deduction] as $label => $amount){
                        if($amount > 0){
                            $deduction_breakdown[$row->pc_id][CustomHelper::payroll_line_label($label)] = $amount;
                        }
                    }
                }
                foreach(PayrollDeduction::whereIn('pc_deduction_pc_id', $payroll_ids)->where('pc_deduction_type', 1)->groupBy('pc_deduction_pc_id','pc_deduction_title')->selectRaw('pc_deduction_pc_id, pc_deduction_title, SUM(pc_deduction_amount) as amount')->get() as $row){
                    $label = CustomHelper::payroll_line_label($row->pc_deduction_title);
                    $deduction_breakdown[$row->pc_deduction_pc_id][$label] = ($deduction_breakdown[$row->pc_deduction_pc_id][$label] ?? 0) + $row->amount;
                }
                foreach(PayrollEarning::whereIn('pc_earning_pc_id', $payroll_ids)->where('pc_earning_type', 1)->groupBy('pc_earning_pc_id','pc_earning_title')->selectRaw('pc_earning_pc_id, pc_earning_title, SUM(pc_earning_amount) as amount')->get() as $row){
                    $label = CustomHelper::payroll_line_label($row->pc_earning_title);
                    $earning_breakdown[$row->pc_earning_pc_id][$label] = ($earning_breakdown[$row->pc_earning_pc_id][$label] ?? 0) + $row->amount;
                }
            }

            // $fetch_employee_payroll = Payroll::selectRaw('( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary) as pc_employee_gross_salary  , ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , GREATEST(0, ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`, `pc_month`, `pc_year`, `pc_date_from`, `pc_date_to`, `pc_employee_designation`, `pc_employee_department`, `pc_employee_job_title`, `pc_employee_bank_name`, `pc_employee_bank_account`, `pc_employee_bank_iban`, `pc_total_working_days`, `pc_present`, `pc_absent`, `pc_off_day`, `pc_leave`, `pc_holiday`, `pc_late_early`, `pc_half_day`, `pc_absent_deduction`, `pc_late_early_deduction`, `pc_half_day_deduction`, `pc_batch`, `pc_created_at`, `pc_updated_at` , disburse')
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
            
            $tax_system_setting = HrmSetting::get(['tax_deduction_apply','club_rule_apply'])->toArray();

            $send_data = compact('title','login_user','fetch_employee_payroll','all_department','month','year','fetch_employee_disburse','tax_system_setting','dynamic_years','deduction_breakdown','earning_breakdown');
            
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
            
            // $fetch_employee_payroll = DB::select("SELECT sum(IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,pc_employee_gross_salary, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , GREATEST(0, ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`,`disburse`,pc_year,pc_month,pc_created_at FROM `tbl_payroll_calculation` 
            //     left join (SELECT pc_earning_pc_id, sum(pc_earning_amount) as sum_pc_earning_amount from tbl_payroll_calculation_earning where pc_earning_type = 1 GROUP by pc_earning_pc_id ) as payroll_earning on payroll_earning.pc_earning_pc_id = tbl_payroll_calculation.pc_id 
            //     left join (SELECT pc_deduction_pc_id, sum(pc_deduction_amount) as sum_pc_deduction_amount from tbl_payroll_calculation_deduction where pc_deduction_type = 1 GROUP by pc_deduction_pc_id ) as payroll_deduction on payroll_deduction.pc_deduction_pc_id = tbl_payroll_calculation.pc_id 
            //     where pc_employee_id = ".session('user_session')." AND disburse = '2' group by pc_id ,pc_deduction_pc_id,pc_earning_pc_id ");

                $fetch_employee_payroll = Payroll::
                select(
                    DB::raw('CASE WHEN payroll_earning.sum_pc_earning_amount > 0 THEN payroll_earning.sum_pc_earning_amount ELSE 0 END as pc_employee_extra_salary'),
                    'pc_employee_gross_salary',
                    DB::raw('(CASE WHEN payroll_deduction.sum_pc_deduction_amount > 0 THEN payroll_deduction.sum_pc_deduction_amount ELSE 0 END + pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction) as pc_employee_deduction'),
                    DB::raw('GREATEST(0, (CASE WHEN payroll_earning.sum_pc_earning_amount > 0 THEN payroll_earning.sum_pc_earning_amount ELSE 0 END + pc_employee_gross_salary) - (CASE WHEN payroll_deduction.sum_pc_deduction_amount > 0 THEN payroll_deduction.sum_pc_deduction_amount ELSE 0 END + pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_net_salary'),
                    'pc_id',
                    'pc_employee_id',
                    'disburse',
                    'pc_year',
                    'pc_month',
                    'pc_created_at'
                )
                ->leftJoin(DB::raw('(SELECT pc_earning_pc_id, SUM(pc_earning_amount) as sum_pc_earning_amount FROM tbl_payroll_calculation_earning WHERE pc_earning_type = 1 GROUP BY pc_earning_pc_id) as payroll_earning'), 'payroll_earning.pc_earning_pc_id', '=', 'tbl_payroll_calculation.pc_id')
                ->leftJoin(DB::raw('(SELECT pc_deduction_pc_id, SUM(pc_deduction_amount) as sum_pc_deduction_amount FROM tbl_payroll_calculation_deduction WHERE pc_deduction_type = 1 GROUP BY pc_deduction_pc_id) as payroll_deduction'), 'payroll_deduction.pc_deduction_pc_id', '=', 'tbl_payroll_calculation.pc_id')
                ->where('pc_employee_id', '=', session('user_session'))
                ->where('disburse', '=', '2')
                ->groupBy(
                    'pc_id',
                    'pc_employee_id',
                    'disburse',
                    'pc_year',
                    'pc_month',
                    'pc_created_at',
                    'payroll_earning.sum_pc_earning_amount',
                    'payroll_deduction.sum_pc_deduction_amount',
                    'pc_absent_deduction',
                    'pc_late_early_deduction',
                    'pc_half_day_deduction',
                    'pc_employee_gross_salary'
                )
                ->get()->toArray();


                // dd($fetch_employee_payroll);

            $fetch_employee_payroll = json_decode(json_encode($fetch_employee_payroll), true);
            
            $current_calculate_option = Payroll::where('disburse',2)->where('pc_employee_id',$login_user[0]['employee_id'])->where('pc_month',date('m'))->where('pc_year',date('Y'))->get()->toArray();
            $pervious_calculate_option = Payroll::where('disburse',2)->where('pc_employee_id',$login_user[0]['employee_id'])->where('pc_month',date('m',strtotime('-1 month')))->where('pc_year',date('Y',strtotime('-1 month')))->get()->toArray();
            
            $send_data = compact('title','login_user','fetch_employee_payroll','current_calculate_option','pervious_calculate_option');
            
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
            ->leftjoin('tbl_office_locations','tbl_office_locations.location_id','=','tbl_employees.employee_location')
            ->where('pc_id','=',$payroll_id)->get()->toArray();

            // deleted / unknown payslip (old link)
            if(empty($payroll)){
                return response()->view('errors.404', ['title' => $title], 404);
            }

            $extra_earning = PayrollEarning::where('pc_earning_pc_id','=',$payroll_id)->
            where('pc_earning_type',1)->get()->toArray();

            $extra_deduction = PayrollDeduction::where('pc_deduction_pc_id','=',$payroll_id)->where('pc_deduction_type',1)->get()->toArray();

            $extra_earning = $this->merge_payslip_lines($extra_earning, 'pc_earning_title', 'pc_earning_amount');
            $extra_deduction = $this->merge_payslip_lines($extra_deduction, 'pc_deduction_title', 'pc_deduction_amount');

            $max_loop = max([count($extra_earning),count($extra_deduction)]);

            $extra_earning_amount = array_sum(array_column($extra_earning, 'pc_earning_amount'));

            $extra_deduction_amount = array_sum(array_column($extra_deduction, 'pc_deduction_amount'));

            $total_earning = $extra_earning_amount + $payroll[0]['pc_employee_gross_salary'];
            $total_deduction = $extra_deduction_amount + $payroll[0]['pc_late_early_deduction'] + $payroll[0]['pc_absent_deduction'] + $payroll[0]['pc_half_day_deduction'];

            // net never goes below zero (deductions more than the earnings: nothing is paid)
            $payroll[0]['pc_employee_net_salary'] = max(0, $payroll[0]['pc_employee_net_salary'] + $extra_earning_amount - $extra_deduction_amount);

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

            // leaves taken by this payroll for absent days / extra late comings / half days (leave_name null = deducted from salary):
            // total = number of days, leave_days = leave taken (1 per absent day, 0.5 per late / half day)
            $leave_deductions = LeaveDeduction::leftJoin('tbl_leave_management','tbl_leave_management.leave_manage_id','=','tbl_leave_deductions.ld_leave_type_id')
            ->where('ld_payroll_id',$payroll_id)
            ->selectRaw('leave_name, ld_reason, COUNT(*) as total, SUM(ld_days) as leave_days')
            ->groupBy('leave_name','ld_reason')
            ->get()->toArray();

            // loan installment the salary could not cover this month (taken in the next payroll)
            $loan_carried = (float) LoanInstallment::where('loan_installment_payroll_id', $payroll_id)->sum('loan_installment_carried');

            $slip = $payroll[0];

            // earnings: gross split into the salary divisions (Basic / House Rent / Utilities ...) + overtime, expense claims ...
            $earning_lines = [];
            foreach($salary_division as $division){
                $earning_lines[] = ['title' => $division['salary_division_title'], 'amount' => round($slip['pc_employee_gross_salary'] * $division['salary_division_percentage'], 2)];
            }
            foreach($extra_earning as $earning){
                $earning_lines[] = ['title' => CustomHelper::payroll_line_label($earning['pc_earning_title']), 'amount' => (float) $earning['pc_earning_amount']];
            }

            // deductions: income tax, EOBI, provident fund first, then loan / advance ..., then absent / late / half days
            $first_deductions = ['income tax deduction' => 1, 'eobi contribution' => 2, 'provident fund' => 3];
            usort($extra_deduction, function($a, $b) use($first_deductions){
                return ($first_deductions[strtolower(trim($a['pc_deduction_title']))] ?? 9) <=> ($first_deductions[strtolower(trim($b['pc_deduction_title']))] ?? 9);
            });
            $deduction_lines = [];
            foreach($extra_deduction as $deduction){
                $deduction_lines[] = ['title' => CustomHelper::payroll_line_label($deduction['pc_deduction_title']), 'amount' => (float) $deduction['pc_deduction_amount']];
            }
            foreach(['pc_absent_deduction' => 'Absent Days', 'pc_late_early_deduction' => 'Late Coming', 'pc_half_day_deduction' => 'Half Days'] as $column => $label){
                if((float) $slip[$column] > 0){
                    $deduction_lines[] = ['title' => $label, 'amount' => (float) $slip[$column]];
                }
            }

            // income tax details: yearly taxable income, slab, yearly tax, tax deducted earlier in this fiscal year, this month
            $tax_details = null;
            if($slip['pc_tax_taxable_income'] !== null){
                $fiscal_date = HrmSetting::value('fiscal_date');
                $fiscal_month_day = !empty($fiscal_date) ? date('m-d', strtotime($fiscal_date)) : '07-01';
                $fiscal_start = date('Y', strtotime($slip['pc_date_to'])).'-'.$fiscal_month_day;
                if($fiscal_start > $slip['pc_date_to']){
                    $fiscal_start = date('Y-m-d', strtotime($fiscal_start.' -1 year'));
                }
                $tax_this_month = (float) PayrollDeduction::where('pc_deduction_pc_id', $payroll_id)->where('pc_deduction_title', 'Income Tax Deduction')->sum('pc_deduction_amount');
                $tax_deducted_before = (float) PayrollDeduction::join('tbl_payroll_calculation', 'tbl_payroll_calculation.pc_id', '=', 'tbl_payroll_calculation_deduction.pc_deduction_pc_id')
                    ->where('pc_employee_id', $slip['pc_employee_id'])->where('pc_deduction_title', 'Income Tax Deduction')
                    ->where('pc_date_to', '>=', $fiscal_start)->where('pc_date_to', '<', $slip['pc_date_to'])
                    ->sum('pc_deduction_amount');
                $tax_details = [
                    'taxable_income' => (float) $slip['pc_tax_taxable_income'],
                    'slab_percentage' => (float) $slip['pc_tax_slab_percentage'],
                    'tax_credit' => 0,
                    'tax_chargeable' => (float) $slip['pc_tax_yearly'],
                    'tax_deducted' => $tax_deducted_before,
                    'tax_payable' => $tax_this_month,
                ];
            }

            // provident fund up to this payslip (contributions, profit, withdrawals)
            $pf_rows = ProvidentFund::where('pf_employee_id', $slip['pc_employee_id'])->where('pf_date', '<=', $slip['pc_date_to'])
                ->selectRaw('pf_type, SUM(pf_member_amount) as member, SUM(pf_company_amount) as company')->groupBy('pf_type')->get()->keyBy('pf_type');
            $pf_summary = null;
            if($pf_rows->isNotEmpty()){
                $pf_sum = fn($type, $column) => (float) ($pf_rows[$type]->$column ?? 0);
                $pf_summary = [
                    'member_contribution' => $pf_sum('contribution', 'member'),
                    'company_contribution' => $pf_sum('contribution', 'company'),
                    'member_profit' => $pf_sum('profit', 'member'),
                    'company_profit' => $pf_sum('profit', 'company'),
                    'permanent_withdrawals' => $pf_sum('permanent_withdrawal', 'member') + $pf_sum('permanent_withdrawal', 'company'),
                    'temporary_withdrawals' => $pf_sum('temporary_withdrawal', 'member') + $pf_sum('temporary_withdrawal', 'company'),
                    'withdrawal_recovery' => $pf_sum('withdrawal_recovery', 'member') + $pf_sum('withdrawal_recovery', 'company'),
                ];
                $pf_summary['available'] = $pf_summary['member_contribution'] + $pf_summary['company_contribution'] + $pf_summary['member_profit'] + $pf_summary['company_profit']
                    - $pf_summary['permanent_withdrawals'] - $pf_summary['temporary_withdrawals'] + $pf_summary['withdrawal_recovery'];
            }

            // service period: joining date to the end of this pay period
            $service_period = '-';
            if(!empty($slip['employee_doj'])){
                $service = date_diff(date_create($slip['employee_doj']), date_create($slip['pc_date_to']));
                $service_period = $service->y.' Year(s), '.$service->m.' Month(s), '.$service->d.' Day(s)';
            }

            $send_data = compact('login_user','payroll','extra_earning','extra_deduction','max_loop','download_button_show','payroll_create_by','payroll_disburse_by','setting','total_deduction','total_earning','salary_division','finance_name','leave_deductions','loan_carried','earning_lines','deduction_lines','tax_details','pf_summary','service_period');

            if(!empty($payroll) && ( ($payroll[0]['pc_employee_id'] == session('user_session') && $payroll[0]['disburse'] == 2) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'finance', $login_user[0]['employee_id'] ?? null) )){

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
    // one payslip line per name: rows shown under the same name (e.g. installments of two loans) are added together
    protected function merge_payslip_lines(array $rows, $title_key, $amount_key){
        $merged = [];
        foreach($rows as $row){
            $label = CustomHelper::payroll_line_label($row[$title_key]);
            if(isset($merged[$label])){
                $merged[$label][$amount_key] += $row[$amount_key];
            }else{
                $merged[$label] = $row;
            }
        }
        return array_values($merged);
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
                    ->leftjoin('tbl_office_locations','tbl_office_locations.location_id','=','tbl_employees.employee_location')
                    ->where('pc_id','=',$payroll_id)->get()->toArray();

                    $extra_earning = PayrollEarning::where('pc_earning_pc_id','=',$payroll_id)->where('pc_earning_type',1)->get()->toArray();

                    $extra_deduction = PayrollDeduction::where('pc_deduction_pc_id','=',$payroll_id)->where('pc_deduction_type',1)->get()->toArray();

                    $extra_earning = $this->merge_payslip_lines($extra_earning, 'pc_earning_title', 'pc_earning_amount');
                    $extra_deduction = $this->merge_payslip_lines($extra_deduction, 'pc_deduction_title', 'pc_deduction_amount');

                    $max_loop = max([count($extra_earning),count($extra_deduction)]);

                    $extra_earning_amount = array_sum(array_column($extra_earning, 'pc_earning_amount'));

                    $extra_deduction_amount = array_sum(array_column($extra_deduction, 'pc_deduction_amount'));

                    $total_earning = $extra_earning_amount + $payroll[0]['pc_employee_gross_salary'];
                    $total_deduction = $extra_deduction_amount + $payroll[0]['pc_late_early_deduction'] + $payroll[0]['pc_absent_deduction'] + $payroll[0]['pc_half_day_deduction'];

                    // net never goes below zero (deductions more than the earnings: nothing is paid)
                    $payroll[0]['pc_employee_net_salary'] = max(0, $payroll[0]['pc_employee_net_salary'] + $extra_earning_amount - $extra_deduction_amount);

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

                    if(!empty($payroll) && $payroll[0]['pc_employee_id'] == $login_user[0]['employee_id'] && $payroll[0]['disburse'] == 2){

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
                $fetch_employee_payroll = DB::select("SELECT ( IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 )) as pc_employee_extra_salary  ,pc_employee_gross_salary, ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)) as pc_employee_deduction , GREATEST(0, ((IF(payroll_earning.sum_pc_earning_amount > 0 ,payroll_earning.sum_pc_earning_amount,0 ) + pc_employee_gross_salary)  - ( IF(payroll_deduction.sum_pc_deduction_amount >0 ,payroll_deduction.sum_pc_deduction_amount,0  ) + (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction)))) as pc_employee_net_salary,`pc_id`, `pc_employee_id`, `pc_month`, `pc_year`, `pc_date_from`, `pc_date_to`, `pc_employee_designation`, `pc_employee_department`, `pc_employee_job_title`, `pc_employee_bank_name`, `pc_employee_bank_account`, `pc_employee_bank_iban`, `pc_total_working_days`, `pc_present`, `pc_absent`, `pc_off_day`, `pc_leave`, `pc_holiday`, `pc_late_early`, `pc_half_day`, `pc_absent_deduction`, `pc_late_early_deduction`, `pc_half_day_deduction`, `pc_batch`, `pc_created_at`, `pc_updated_at` , disburse, employee_name, employee_code FROM `tbl_payroll_calculation` 
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

            if( CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'finance', $login_user[0]['employee_id'] ?? null) ){

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

            // calculate / generate only up to the current month (no future month payroll)
            if(date('Y-m', strtotime($request->input('payroll_month'))) > date('Y-m')){
                return response()->json([
                    'status' => 'FALSE',
                    'title' => "Can't Calculate A Future Month",
                    'text' => "Choose ".date('F Y')." or an earlier month.",
                    'icon' => "warning"
                ]);
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
            
            $fetch_system_setting = HrmSetting::get(['payroll_start_date','tax_deduction_apply','club_rule_apply','eobi_apply','eobi_minimum_wage','eobi_employee_percentage','pf_apply','pf_employee_percentage','pf_company_percentage'])->toArray();

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

            $club_rule_is_applied = $this->payroll_club_rule_applied($request, $fetch_system_setting);

            if($date_to != 1){

                $date_to = $date_to.'-'.$payroll_month.'-'.$payroll_year;
                $date_to = date('Y-m-d',strtotime($date_to));

                $date_from = date('Y-m-d',strtotime($date_to.' -1 months' ));
                $date_to = date('Y-m-d',strtotime($date_to.' -1 days' ));

                $period_count = $this->days_count_between_two_dates($date_from,$date_to);
                $period_end = $date_to;

                if(!empty($date_to_update) && strtotime($date_to_update) < strtotime($date_to) ){

                    $date_to = date('Y-m-d',strtotime($date_to_update));

                }

            }else{

                $date_to = $date_to.'-'.$payroll_month.'-'.$payroll_year;
                $date_to = date('Y-m-t',strtotime($date_to));
                $date_from = date('Y-m-01',strtotime($date_to));

                $period_count = $this->days_count_between_two_dates($date_from,$date_to);
                $period_end = $date_to;

                if(!empty($date_to_update) && strtotime($date_to_update) < strtotime($date_to) ){

                    $date_to = date('Y-m-d',strtotime($date_to_update));

                }

            }


            // the period locks on its last day (e.g. 24th); payroll can be generated from the next day (25th)
            if( time() < strtotime($date_to.'+1 day') &&  empty($request->input('calculatePayrollAmountForm')) ){

                return response()->json([

                'status' => 'FALSE',
                'title' => "Can't Generate Payroll Yet",
                'text' => "This payroll period (".date('d-M-Y',strtotime($date_from))." to ".date('d-M-Y',strtotime($date_to)).") is not complete. You can generate it from ".date('d-M-Y',strtotime($date_to.'+1 day')).". Use Calculate to see an estimate now.",
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

            $fetch_payroll = Payroll::where('pc_year',$payroll_year)->where('pc_month',$payroll_month)->pluck('pc_employee_id')->toArray();

            $is_estimate = !empty($request->input('calculatePayrollAmountForm'));
            $generated_payroll = 0;
            $skipped_without_salary = [];

            // Leavers: the payroll of the calendar month of the employee's last day (and any later one) is held; the last
            // salary paid is the month before. E.g. last day 5/10/15 Nov -> November held, October is the last payroll;
            // last day 28 Oct -> October held, September is the last. HR settles the rest by hand (final settlement).
            // Last day = deactivation date, or the to-date of an approved resignation while the employee is still active.
            $resignation_last_days = Resignation::where('resignation_status', 2)->groupBy('resignation_employee')->selectRaw('resignation_employee, MAX(resignation_to_date) as last_day')->pluck('last_day', 'resignation_employee')->toArray();
            $held_for_settlement = [];

            // employees HR holds in this run (e.g. 2 of a department of 10); their payroll is made later, one by one
            $hold_ids = array_filter(array_map('intval', (array) $request->input('hold_employee_id', [])));
            $held_by_hr = [];

            foreach( $get_employees as $employee ){

                if( strtotime($employee['employee_doj']) >  strtotime($date_to)){
                    continue;
                }

                if(in_array((int) $employee['employee_id'], $hold_ids)){
                    $held_by_hr[] = $employee['employee_code'].' - '.$employee['employee_name'];
                    continue;
                }

                $last_day = ($employee['employee_isactive'] != 1 && !empty($employee['employee_deactivate_date'])) ? $employee['employee_deactivate_date'] : ($resignation_last_days[$employee['employee_id']] ?? null);
                if(!empty($last_day) && date('Y-m', strtotime($last_day)) <= sprintf('%04d-%02d', $payroll_year, $payroll_month)){
                    $held_for_settlement[] = $employee['employee_code'].' - '.$employee['employee_name'].' (last day '.date('d M Y', strtotime($last_day)).')';
                    continue;
                }

                // payroll of this month already generated for the employee
                if(!$is_estimate && in_array($employee['employee_id'],$fetch_payroll)){
                    continue;
                }

                // get attendance record of system of employee
                $attendance = $this->get_employee_attendance($date_from,$date_to,$employee['employee_id']);

                $atts = array('attendance' => $attendance);
                $employee_with_att = array_merge( $employee , $atts );

                $attendance_count = $this->get_employee_complete_attendance_status($date_from,$date_to,$employee_with_att,$club_rule_is_applied);

                $salary_of_employee = $this->calculate_salary_of_employee($date_from,$date_to,$attendance_count,$employee['employee_id'],$period_count);

                // no monthly salary defined for the employee
                if($salary_of_employee === null){
                    $skipped_without_salary[] = $employee['employee_code'].' - '.$employee['employee_name'];
                    continue;
                }

                if($is_estimate){

                    $total_payroll += $salary_of_employee['net_salary'];
                    continue;

                }

                if($attendance_count['present_count'] == 0 && $attendance_count['halfday_count'] == 0 && $attendance_count['late_count'] == 0 && $attendance_count['leave_count'] == 0){
                    $salary_of_employee['deduction_of_absent'] = $salary_of_employee['deduction_of_absent'] + $salary_of_employee['net_salary'] ;
                    $salary_of_employee['net_salary'] = '0';
                    // nothing is paid, so no leave is taken either
                    $salary_of_employee['late_halfday_deductions'] = [];
                }

                // one employee's payroll, leave deductions, loan / advance / expense / overtime / tax rows are saved together
                DB::transaction(function() use($employee,$salary_of_employee,$payroll_month,$payroll_year,$date_from,$date_to,$batch_num,$tax_is_applied,$request,$final_fiscal_year_in_slab,$fetch_system_setting){

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

                    $add_payroll->pc_employee_gross_salary = $salary_of_employee['each_chunk_gross_salary'];

                    $add_payroll->pc_created_at = date('Y-m-d') ;
                    $add_payroll->pc_created_by = session('user_session');

                    $add_payroll->pc_batch = $batch_num;
                    $add_payroll->save();

                    $this->record_late_halfday_deductions($employee['employee_id'],$add_payroll->pc_id,$salary_of_employee['late_halfday_deductions']);

                    $this->advance_salary_deduction_from_salary($payroll_month,$payroll_year,$employee['employee_id'],$add_payroll->pc_id);
                    $this->employee_expense_reimburse($date_to,$employee['employee_id'],$add_payroll->pc_id);
                    $this->overtime_reimbursement($date_to,$employee['employee_id'],$add_payroll->pc_id);

                    if($tax_is_applied == 1 && $request->input('tax_deduction') == "yes"){

                        // tax on the monthly gross salary, not on what this month pays (absent days / joining do not change it)
                        // + the company's provident fund contribution above the tax free limit (Sixth Schedule rule 3(a))
                        $tax_salary = $salary_of_employee['tax_salary'] ?? $salary_of_employee['each_chunk_gross_salary'];
                        $tax_salary += $this->provident_fund_taxable_excess($tax_salary, $fetch_system_setting[0] ?? []);
                        $this->tax_deduction_from_salary($date_from,$date_to,$employee['employee_id'],$add_payroll->pc_id,$tax_salary,$final_fiscal_year_in_slab);

                    }

                    // EOBI / provident fund, only when something is payable this month
                    if($salary_of_employee['net_salary'] > 0){
                        foreach($this->statutory_deductions($salary_of_employee['each_chunk_gross_salary'],$fetch_system_setting[0] ?? []) as $deduction_title => $deduction_amount){
                            $add_payroll_deduction = new PayrollDeduction();
                            $add_payroll_deduction->pc_deduction_pc_id = $add_payroll->pc_id;
                            $add_payroll_deduction->pc_deduction_title = $deduction_title;
                            $add_payroll_deduction->pc_deduction_amount = $deduction_amount;
                            $add_payroll_deduction->save();
                        }

                        // provident fund: employee share (deducted above) + the company's equal share go into the employee's fund
                        $provident_fund = $this->provident_fund_shares($salary_of_employee['each_chunk_gross_salary'],$fetch_system_setting[0] ?? []);
                        if($provident_fund['member'] > 0 || $provident_fund['company'] > 0){
                            $add_provident_fund = new ProvidentFund();
                            $add_provident_fund->pf_employee_id = $employee['employee_id'];
                            $add_provident_fund->pf_payroll_id = $add_payroll->pc_id;
                            $add_provident_fund->pf_type = 'contribution';
                            $add_provident_fund->pf_member_amount = $provident_fund['member'];
                            $add_provident_fund->pf_company_amount = $provident_fund['company'];
                            $add_provident_fund->pf_date = $date_to;
                            $add_provident_fund->pf_created_by = session('user_session');
                            $add_provident_fund->pf_created_at = date('Y-m-d H:i:s');
                            $add_provident_fund->save();
                        }
                    }

                    // loan installments last: only from the pay that is left, the rest is carried to next month
                    $this->laon_deduction_from_salary($payroll_month,$payroll_year,$employee['employee_id'],$add_payroll->pc_id,$salary_of_employee['net_salary']);
                });

                $generated_payroll++;
                $total_payroll += $salary_of_employee['net_salary'];

            }

            $skipped_text = '';
            if(!empty($skipped_without_salary)){
                $skipped_text = "<br><span class='text-danger'>Skipped (no salary defined): ".e(implode(', ',array_slice($skipped_without_salary,0,20))).(count($skipped_without_salary) > 20 ? ' and '.(count($skipped_without_salary) - 20).' more' : '')."</span>";
            }
            if(!empty($held_by_hr)){
                $skipped_text .= "<br><span class='text-warning'>Held by you: ".e(implode(', ', $held_by_hr))."</span>";
            }
            if(!empty($held_for_settlement)){
                $skipped_text .= "<br><span class='text-warning'>Held for final settlement (leaving): ".e(implode(', ',array_slice($held_for_settlement,0,20))).(count($held_for_settlement) > 20 ? ' and '.(count($held_for_settlement) - 20).' more' : '')."</span>";
            }

            if($is_estimate){

                $title = "Payroll Estimate (not saved).<br> Total Payroll = ".round($total_payroll,2).' '.$currency[0]['system_currency']  .$skipped_text;
                if($date_to < $period_end){
                    $title .= "<br><span class='text-muted fs-6'>Period ".date('d-M-Y',strtotime($date_from))." to ".date('d-M-Y',strtotime($period_end))." is still running: counted up to today. It can be generated from ".date('d-M-Y',strtotime($period_end.'+1 day')).".</span>";
                }
                $icon = 'info';
            
            }else{

                if($generated_payroll>0){
                    $title = "Payroll Has Been Generated Successfully.<br> Total Payroll = ".round($total_payroll,2).' '.$currency[0]['system_currency'] .$skipped_text;
                    $icon = 'success';
                    $link = 'payroll?m='.$payroll_month.'&y='.$payroll_year;
                }else{
                    $title = "There's no employee or ungenerated payroll employee".$skipped_text;
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

            if(!config('app.debug')){
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
            $where_clause = 'employee_department in ('.implode(",",array_map('intval', (array) $department)).')';
        }

        if(!empty($employee_id)){
            $where_clause = 'employee_id = '.(int) $employee_id;

        }
        
        $fetch_employee = Employees::
        join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
        ->join('tbl_personal_informations','tbl_personal_informations.employeeid','tbl_employees.employee_id')
        ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
        ->where(function ($query) use($date_from,$date_to) {
            $query->where('employee_isactive',1);
            // deactivated employees whose last day is on or after the start of the period: they worked (part of) it.
            // Someone who leaves after this period is paid this period in full, even if deactivated before it is generated.
            $query->orWhereRaw('STR_TO_DATE(`employee_deactivate_date`,"%Y-%m-%d") >= ?', [$date_from]);
        })
        ->whereRaw($where_clause)
        ->where('employee_earning_type',1) //monthly earning time
        ->get(['employee_id','employee_code','employee_name','designation_name','department_name','employee_jobtitle','employee_offdays','bankname','ibannumber','accountno','employee_doj','employee_deactivate_date','employee_isactive'])->toArray();

        return $fetch_employee;
    }









    // what was not deducted in the last payroll of a loan (salary not enough), less cash installments paid after it
    protected function loan_carried_forward($loan_id){

        $last_payroll_installment = LoanInstallment::where('loan_installment_loan_id', $loan_id)->whereNotNull('loan_installment_payroll_id')->orderByDesc('loan_installment_id')->first();

        if(empty($last_payroll_installment) || $last_payroll_installment->loan_installment_carried <= 0){
            return 0;
        }

        $paid_after = LoanInstallment::where('loan_installment_loan_id', $loan_id)->whereNull('loan_installment_payroll_id')->where('loan_installment_id', '>', $last_payroll_installment->loan_installment_id)->sum('loan_installment_amount');

        return max(0, $last_payroll_installment->loan_installment_carried - $paid_after);
    }

    // $net_salary: this month's pay before loans. A loan takes only what is left after the other deductions;
    // the part it could not take is carried and added to the next month's installment.
    protected function laon_deduction_from_salary($month,$year,$employee_id,$payroll_id,$net_salary = null){


        $loan_amount = 0;

        $available = null;
        if($net_salary !== null){
            $available = max(0, (float) $net_salary + PayrollEarning::where('pc_earning_pc_id', $payroll_id)->sum('pc_earning_amount') - PayrollDeduction::where('pc_deduction_pc_id', $payroll_id)->sum('pc_deduction_amount'));
        }

        $get_loan_records = Loan::where('employee_code',$employee_id)->where('loan_status','!=','4')->whereRaw('STR_TO_DATE(CONCAT(loan_deduction_start,"-01"),"%Y-%m-%d") <= "'.$year.'-'.$month.'-01"')->orderBy('loan_id')->get()->toArray();

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

            // this month's installment plus what earlier months could not take, never more than the loan left
            $loan_due = round(min((float) $get_loan_record['loan_remaining_amount'], $loan_amount + $this->loan_carried_forward($get_loan_record['loan_id'])), 2);

            if($loan_due <= 0){
                continue;
            }

            // only what the salary can still cover
            $loan_amount = ($available === null) ? $loan_due : round(min($loan_due, $available), 2);
            $loan_carried = round($loan_due - $loan_amount, 2);
            if($available !== null){
                $available -= $loan_amount;
            }

            // insert and update the record (also when nothing could be taken, so the shortfall is carried)

            $add_installment = new LoanInstallment();
            $add_installment->loan_installment_loan_id = $get_loan_record['loan_id'];
            $add_installment->loan_installment_amount = $loan_amount;
            $add_installment->loan_installment_due = $loan_due;
            $add_installment->loan_installment_carried = $loan_carried;
            $add_installment->loan_installment_date = date('Y-m-d H:i:s');
            $add_installment->loan_installment_payroll_id = $payroll_id;
            $add_installment->save();

            if($loan_amount>0){

                $loan_remaining_amount = round($get_loan_record['loan_remaining_amount']-$loan_amount, 2);

                if($loan_remaining_amount <= 0){

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
                    
                    // slabs follow each other (next starts at highest + 1): a yearly income with paise between two slabs belongs to the upper one
                    if($tax_slab['ts_lowest_yearly_income'] - 1 < $gross_salary_of_year && $tax_slab['ts_highest_yearly_income'] >= $gross_salary_of_year ){
        
                        // rate applies to the income exceeding the previous slab (slab 1,200,001 - ... -> income above 1,200,000)
                        $total_tax_of_year = ($gross_salary_of_year - max(0, $tax_slab['ts_lowest_yearly_income'] - 1)) * ($tax_slab['ts_deduction_percentage']/100) + $tax_slab['ts_deduction_amount'] ;

                        // kept for the payslip's income tax details
                        Payroll::where('pc_id',$payroll_id)->update(['pc_tax_taxable_income' => round($gross_salary_of_year,2), 'pc_tax_slab_percentage' => $tax_slab['ts_deduction_percentage'], 'pc_tax_yearly' => round(max(0,$total_tax_of_year),2)]);

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
        
                    // slabs follow each other (next starts at highest + 1): a yearly income with paise between two slabs belongs to the upper one
                    if($tax_slab['ts_lowest_yearly_income'] - 1 < $gross_salary_of_year && $tax_slab['ts_highest_yearly_income'] >= $gross_salary_of_year ){
        
                        // rate applies to the income exceeding the previous slab (slab 1,200,001 - ... -> income above 1,200,000)
                        $total_tax_of_year = ($gross_salary_of_year - max(0, $tax_slab['ts_lowest_yearly_income'] - 1)) * ($tax_slab['ts_deduction_percentage']/100) + $tax_slab['ts_deduction_amount'] ;

                        // kept for the payslip's income tax details
                        Payroll::where('pc_id',$payroll_id)->update(['pc_tax_taxable_income' => round($gross_salary_of_year,2), 'pc_tax_slab_percentage' => $tax_slab['ts_deduction_percentage'], 'pc_tax_yearly' => round(max(0,$total_tax_of_year),2)]);
                        
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
              
            if(implode("",$request->input('verification_code')) == config('app.payroll_verification_code', Self::verification_code)){
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

                foreach( $get_employee as $employee ){

                    // get attendance record of system of employee
                    $attendance = $this->get_employee_attendance($date_from,$date_to,$employee['employee_id']);

                    $atts = array('attendance' => $attendance);
                    $employee_with_att = array_merge( $employee , $atts );

                    $attendance_count = $this->get_employee_complete_attendance_status($date_from,$date_to,$employee_with_att,$club_rule_is_applied);

                    $salary_of_employee = $this->calculate_salary_of_employee($date_from,$date_to,$attendance_count,$employee['employee_id'],$period_count);

                    $total_payroll += $salary_of_employee['net_salary'] ?? 0;
                    
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
                    'title' => "Your payroll can't be calculated: no monthly salary is set for you.",
                    'icon' => "warning",
                    'reload' => "TRUE",
                ]);
            }

        }catch(\Exception $e){

            $this->create_error_log("Calculate My Payroll", $e);

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

        $any_deleted = 0;

        foreach($delete_payroll_ids as $delete_payroll_id){

            $delete_payroll = Payroll::where('pc_id',$delete_payroll_id)->where('disburse',1)->delete();

            if($delete_payroll == 1){

                $any_deleted = 1;

                // leaves taken for late comings / half days go back to the employee's balance
                LeaveDeduction::where('ld_payroll_id',$delete_payroll_id)->delete();

                // this payroll's provident fund contribution leaves the fund
                ProvidentFund::where('pf_payroll_id',$delete_payroll_id)->delete();

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
                $update_status = EmployeeExpense::where('emp_expense_payslip_id',$delete_payroll_id)->update($update_values);
                $update_overtime = OverTime::where('ovt_payroll_id',$delete_payroll_id)->update(['ovt_payroll_id' => Null]);


            }
        }

        return $any_deleted;

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
            if(!config('app.debug')){
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


