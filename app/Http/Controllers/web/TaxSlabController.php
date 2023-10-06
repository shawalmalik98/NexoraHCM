<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\{TaxSlab, HrmSetting, Payroll, PayrollEarning, PayrollDeduction};
use App\Traits\{LoginEmployee,LogGenerateTrait};
use App\Helpers\ActivityTracker;

class TaxSlabController extends Controller
{

    use LoginEmployee, LogGenerateTrait;

    const MAIN_TITLE = "Tax Slab";
    const TITLE_GLOBAL = "Tax Collection";

    protected function index(Request $request){

        try{
            
            $title = SELF::MAIN_TITLE;
                    
            $login_user = $this->get_login_user_detail();

            $tax_slab_filter = TaxSlab::groupBy('ts_fiscal_start','ts_fiscal_end')->orderBy('ts_fiscal_start','DESC')->get(['ts_fiscal_start','ts_fiscal_end'])->toArray();


            $fetch_tax_slab = [];
            if(!empty($request->input('fiscal'))){

                $fetch_tax_slab = TaxSlab::where('ts_fiscal_start',$request->input('fiscal'))->get()->toArray();


            }
            elseif(!empty($tax_slab_filter)){

                $fetch_tax_slab = TaxSlab::where('ts_fiscal_start',$tax_slab_filter[0]['ts_fiscal_start'])->get()->toArray();

            }
            

            $tax_slab_filter = TaxSlab::groupBy('ts_fiscal_start','ts_fiscal_end')->get(['ts_fiscal_start','ts_fiscal_end'])->toArray();

            $fiscal_date  = HrmSetting::get(['fiscal_date'])->toArray();
            
            $start_year = date('Y');

            $fiscal_dates_array = [];
            if(!empty( $fiscal_date)){
                for($i=-3;$i<1;$i++){

                    $fiscal_start = date($start_year+$i.'-m-d',strtotime($fiscal_date[0]['fiscal_date']));
                    $period = [
                        'start_fiscal'=> $fiscal_start,
                        'end_fiscal'=>date('Y-m-d',strtotime($fiscal_start.'+1 year -1 day')),
                    ];
                    if(!in_array($fiscal_start,array_column($tax_slab_filter,'ts_fiscal_start'))){

                        array_push($fiscal_dates_array,$period);
                    }
                    
                }

                $fiscal_dates_array = array_reverse($fiscal_dates_array);
            }
            

            $send_data = compact('title', 'login_user','fetch_tax_slab','fiscal_dates_array','tax_slab_filter');

            return view('tax-slab.tax-slab',$send_data);
        }catch(\Exception $e){

            $this->create_error_log("view Tax Slab", $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('view Tax Slab'); 
 
            
        }

        

    }


    protected function add_tax_slab(Request $request){



        try{
            
            $fiscal_date  = HrmSetting::get(['fiscal_date'])->toArray();
            
            $start_year = date('Y');

            $fiscal_dates_array_valid = [];
            if(!empty( $fiscal_date)){
                for($i=-3;$i<1;$i++){

                    $fiscal_start = date($start_year+$i.'-m-d',strtotime($fiscal_date[0]['fiscal_date']));
                    
                    array_push($fiscal_dates_array_valid,$fiscal_start);
                    
                    
                }
            }


            $validator = Validator::make($request->all(), [

                'lowest_yearly_income' => ['required','array'],
                'lowest_yearly_income.*' =>  ['required'],

                'highest_yearly_income' => ['required','array'],
                'highest_yearly_income.*' =>  ['required'],

                'deduction_percentage' => ['required','array'],
                'deduction_percentage.*' =>  ['required','numeric','between:0,100'],

                'deduction_amount' => ['required','array'],
                'deduction_amount.*' =>  ['required'],

                'fiscal_start' => 'required|in:'.implode(",",$fiscal_dates_array_valid),
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            for($i=0;$i<count($request->input('lowest_yearly_income'))-1 ; $i++){

                if($request->input('lowest_yearly_income')[0] == 0 && $request->input('lowest_yearly_income')[$i] <  $request->input('highest_yearly_income')[$i] && ($request->input('highest_yearly_income')[$i]+1) == ($request->input('lowest_yearly_income')[$i+1]) && $request->input('lowest_yearly_income')[$i+1] <  $request->input('highest_yearly_income')[$i+1]  ){

                    continue;

                }else{

                    return response()->json([
                        'msg' => 'Yearly income sequence invalid',
                        'status' => 'FALSE'
                    ]);


                }

            }

            
            
            
            for($i=0;$i<count($request->input('lowest_yearly_income')) ; $i++){

            $add_tax_slab = new TaxSlab();

                $add_tax_slab->ts_lowest_yearly_income = $request->input('lowest_yearly_income')[$i];
                $add_tax_slab->ts_highest_yearly_income = $request->input('highest_yearly_income')[$i];
                $add_tax_slab->ts_deduction_percentage = $request->input('deduction_percentage')[$i];
                $add_tax_slab->ts_deduction_amount = $request->input('deduction_amount')[$i];
                $add_tax_slab->ts_fiscal_start = date('Y-m-d',strtotime($request->input('fiscal_start')));
                $add_tax_slab->ts_fiscal_end = date('Y-m-d',strtotime($request->input('fiscal_start').'+1 year -1 days'));

                $add_tax_slab->ts_created_by = session('user_session');
                $add_tax_slab->ts_created_at = date('Y-m-d H:i:s');
                
                $process = $add_tax_slab->save();   
            }

            if($process == 1){
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Tax slab added successfully.",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Tax slab addition failed. Please try again after some time.",
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log("Add Tax Slab", $e);
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
            ActivityTracker::track_activity('Add Tax Slab'); 
        }

    }


    protected function edit_tax_slab(Request $request){


        try{


            $validator = Validator::make($request->all(), [

                'lowest_yearly_income' => ['required','array'],
                'lowest_yearly_income.*' =>  ['required','numeric','between:0,99999999'],

                'highest_yearly_income' => ['required','array'],
                'highest_yearly_income.*' =>  ['required','numeric','between:0,99999999'],

                'deduction_percentage' => ['required','array'],
                'deduction_percentage.*' =>  ['required','numeric','between:0,100'],

                'deduction_amount' => ['required','array'],
                'deduction_amount.*' =>  ['required'],

                'fiscal_start' => 'required'
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            for($i=0;$i<count($request->input('lowest_yearly_income'))-1 ; $i++){

                if($request->input('lowest_yearly_income')[0] == 0 && $request->input('lowest_yearly_income')[$i] <  $request->input('highest_yearly_income')[$i] && ($request->input('highest_yearly_income')[$i]+1) == ($request->input('lowest_yearly_income')[$i+1]) && $request->input('lowest_yearly_income')[$i+1] <  $request->input('highest_yearly_income')[$i+1]  ){

                    continue;

                }else{

                    return response()->json([
                        'msg' => 'Yearly income sequence invalid',
                        'status' => 'FALSE'
                    ]);


                }

            }
            
            $fetch_create_date = TaxSlab::where('ts_fiscal_start',$request->input('fiscal_start'))->get(['ts_created_at','ts_fiscal_start'])->toArray();

            if(!empty($fetch_create_date)){

            
                $create_date = $fetch_create_date[0]['ts_created_at'];

                $delete_tax_fiscal = TaxSlab::where('ts_fiscal_start',$request->input('fiscal_start'))->delete();

                for($i=0;$i<count($request->input('lowest_yearly_income')) ; $i++){

                $add_tax_slab = new TaxSlab();

                    $add_tax_slab->ts_lowest_yearly_income = $request->input('lowest_yearly_income')[$i];
                    $add_tax_slab->ts_highest_yearly_income = $request->input('highest_yearly_income')[$i];
                    $add_tax_slab->ts_deduction_percentage = $request->input('deduction_percentage')[$i];
                    $add_tax_slab->ts_deduction_amount = $request->input('deduction_amount')[$i];
                    $add_tax_slab->ts_fiscal_start = date('Y-m-d',strtotime($request->input('fiscal_start')));
                    $add_tax_slab->ts_fiscal_end = date('Y-m-d',strtotime($request->input('fiscal_start').'+1 year -1 days'));

                    $add_tax_slab->ts_created_by = session('user_session');
                    $add_tax_slab->ts_created_at = $create_date;
                    $add_tax_slab->ts_updated_at = date('Y-m-d H:i:s');
                    
                    $process = $add_tax_slab->save();   
                }

                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Tax slab update successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Tax slab updation failed. Please try again after some time.",
                    ]);
                }
            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Tax slab updation failed. Please try again after some time.",
                ]);

            }
        }catch(\Exception $e){
            $this->create_error_log("Edit Tax Slab", $e);
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
            ActivityTracker::track_activity('Edit Tax Slab'); 
        }

    }   


    protected function tax_collection(Request $request){

        try{
            
            $title = SELF::TITLE_GLOBAL;
                    
            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();

            if(!empty($request->input('m')) && !empty($request->input('y'))){

                $month = $request->input('m');
                $year = $request->input('y');

            }else{

                $month = date('m');
                $year = date('Y');
            }

            $fetch_employee_tax = Payroll::selectRaw('pc_id, pc_created_at, pc_employee_id, pc_year, pc_month, pc_employee_gross_salary, (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction) as pc_employee_deduction, pc_employee_net_salary,disburse,sum(pc_deduction_amount) as total_tax_collection')
            ->join('tbl_payroll_calculation_deduction','tbl_payroll_calculation_deduction.pc_deduction_pc_id','=','tbl_payroll_calculation.pc_id')
            ->where('pc_deduction_title','=','Income Tax Deduction')
            ->where('pc_month', $month)
            ->where('pc_year', $year)
            ->groupBy('pc_deduction_pc_id')
            ->get()->toArray();
            
            $i=0;
            foreach($fetch_employee_tax as $employee_tax){

                
                $earning = PayrollEarning::selectRaw('sum(pc_earning_amount) as total')->where('pc_earning_pc_id',$employee_tax['pc_id'])->get()->toArray();

                $deduction = PayrollDeduction::selectRaw('sum(pc_deduction_amount) as total')->where('pc_deduction_pc_id',$employee_tax['pc_id'])->get()->toArray();

                $fetch_employee_tax[$i]['pc_employee_deduction'] = $fetch_employee_tax[$i]['pc_employee_deduction'] + $deduction[0]['total'];
                $fetch_employee_tax[$i]['pc_employee_gross_salary'] = $fetch_employee_tax[$i]['pc_employee_gross_salary'] + $earning[0]['total'];

                $fetch_employee_tax[$i]['pc_employee_net_salary'] = $fetch_employee_tax[$i]['pc_employee_gross_salary'] -$fetch_employee_tax[$i]['pc_employee_deduction'];

                $i++;


            }
            $total_tax_collection = array_sum(array_column($fetch_employee_tax,'total_tax_collection'));
            $send_data = compact('title', 'login_user','month','year','fetch_employee_tax','total_tax_collection','dynamic_years');


        }catch(\Exception $e){

            $this->create_error_log("view Tax Slab", $e);
            $send_data = compact('title');

        }finally{

            ActivityTracker::track_activity('view Tax Slab'); 
            return view('tax-slab.tax-collection',$send_data);
            
        }

    }

    protected function download_tax_collection_pdf(Request $request){


        try{

            $login_user = $this->get_login_user_detail();

            $month = $request->input('month');
            $year = $request->input('year');

            $fetch_employee_tax = Payroll::selectRaw('pc_id, pc_created_at, pc_employee_id, pc_year, pc_month, pc_employee_gross_salary, (pc_absent_deduction + pc_late_early_deduction + pc_half_day_deduction) as pc_employee_deduction, pc_employee_net_salary,disburse,sum(pc_deduction_amount) as total_tax_collection')
            ->join('tbl_payroll_calculation_deduction','tbl_payroll_calculation_deduction.pc_deduction_pc_id','=','tbl_payroll_calculation.pc_id')
            ->where('pc_deduction_title','=','Income Tax Deduction')
            ->where('pc_month', $month)
            ->where('pc_year', $year)
            ->groupBy('pc_deduction_pc_id')
            ->get()->toArray();

                
            $i=0;
            foreach($fetch_employee_tax as $employee_tax){

                
                $earning = PayrollEarning::selectRaw('sum(pc_earning_amount) as total')->where('pc_earning_pc_id',$employee_tax['pc_id'])->get()->toArray();

                $deduction = PayrollDeduction::selectRaw('sum(pc_deduction_amount) as total')->where('pc_deduction_pc_id',$employee_tax['pc_id'])->get()->toArray();

                $fetch_employee_tax[$i]['pc_employee_deduction'] = $fetch_employee_tax[$i]['pc_employee_deduction'] + $deduction[0]['total'];
                $fetch_employee_tax[$i]['pc_employee_gross_salary'] = $fetch_employee_tax[$i]['pc_employee_gross_salary'] + $earning[0]['total'];

                $fetch_employee_tax[$i]['pc_employee_net_salary'] = $fetch_employee_tax[$i]['pc_employee_gross_salary'] -$fetch_employee_tax[$i]['pc_employee_deduction'];

                $i++;


            } 

            $download_button_show = true;

            if($request->input('download')=="TRUE"){
                $download_button_show = false;
            }

            $send_data = compact('fetch_employee_tax','month','year','download_button_show');

            if( $login_user[0]['employee_department'] == "1" || $login_user[0]['employee_department'] == "2" || $login_user[0]['employee_department'] == "10" ){

                if($request->input('download')=="TRUE"){
                    
                    $pdf = Pdf::loadView('tax-slab.all-tax-collection-pdf', $send_data);
                    return $pdf->download('Tax-Collection-'.date("F", mktime(0, 0, 0, $month, 10)).'-'.$year.'.pdf');
                }
                return view('tax-slab.all-tax-collection-pdf',$send_data);
                
            }else{

                return abort(404);

            }

        }catch(\Exception $e){

            $this->create_error_log('Download Tax PDF', $e);

            $send_data = compact('title');
            return view('errors.500');

        }
        finally{


            ActivityTracker::track_activity('Download Tax PDF');
            
        }

            
    }

}
