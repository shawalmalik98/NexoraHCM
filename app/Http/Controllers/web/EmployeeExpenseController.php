<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait};
use App\Helpers\ActivityTracker;

use App\Models\{EmployeeExpense, EmployeeExpenseAmount, RequestApproval, Employees, OfficeLocation};

class EmployeeExpenseController extends Controller
{
    use LoginEmployee, LogGenerateTrait ,NotificationTrait;
    const MAIN_TITLE = 'employee-expense';
    const SUB_TITLE = 'my-expense';

    protected $expense_type_list = [ 1 => 'Travel and accommodation', 2 => 'Corporate entertainment', 3 => 'Stationery and consumable goods', 4 => 'Subscriptions', 5 => 'Telephony and home office use' ];

    protected function index(Request $request){
        
        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1' ;

            if(!empty($request->emp_name)){
                $where_clause .= " AND employee_id = '".$request->emp_name."'";
            }
            if(!empty($request->employee_location)){
                $where_clause .= " AND employee_location = '".$request->employee_location."'";
                
            }
               
            $employees_data = Employees::where('employee_isactive','=',$status)->whereRaw($where_clause)->pluck('employee_id')->toArray();

            $whereRaw = '1=1 ';
            $where_clause_to = '1=1 ';
           
            if($request->get('expense_type')){
                $whereRaw .= ' and  emp_expense_type = '.$request->get('expense_type');
            }
            if($request->get('status')){

                $whereRaw .= ' and emp_expense_approved_by_hr = '.$request->get('status');
            }
            if($request->get('paid')){

                $whereRaw .= ' and emp_expense_reimbursement = '.$request->get('paid');
            }
            if($request->get('req_status')){

                $whereRaw .= ' and emp_expense_approved_by_hr = '.$request->get('req_status');
            }

            $responsed_request = RequestApproval::where('request_module_id',7)->pluck('request_id')->toArray();
            
            if($login_user[0]['employee_department'] != 1 && $login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 3){
                
                $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'],  $status);

                $where_clause_to .= ' and emp_expense_employee in ('.implode(",",$total_team_member).')';
                
                $responsed_request = RequestApproval::where('request_module_id',7)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();
            }

            $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'],  $status);

            $fetch_expenses = [];
          
            $employee_arrays = array_chunk($total_team_member , 2000);
            
            foreach($employee_arrays as $employee){

                $fetch_expense = EmployeeExpense::
                selectRaw('emp_expense_employee,emp_expense_id,emp_expense_date,emp_expense_description,emp_expense_type,emp_expense_amount,emp_expense_approved_by_hr,emp_expense_reimbursement,emp_expense_created_at,employee_name,employee_name,employee_code,emp_expense_approved_by_hr_time,emp_expense_attachment,emp_expense_approved_by_hr_reason,(select count(emp_expense_exp_id) from tbl_employee_expense_amount where emp_expense_exp_id = emp_expense_id   ) as update_amount, pc_id, pc_month, pc_year, disburse')
                ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
                ->leftjoin('tbl_employees','tbl_employees.employee_id','tbl_employee_expense.emp_expense_approved_by_hr_id')
                ->leftjoin('tbl_payroll_calculation','tbl_payroll_calculation.pc_id','tbl_employee_expense.emp_expense_payslip_id')
                ->whereIn('emp_expense_amt_id',function($query){
                    $query->from('tbl_employee_expense_amount')
                    ->selectRaw('MAX(emp_expense_amt_id)')
                    ->groupBy('emp_expense_exp_id');
                })
                ->whereRaw($where_clause_to)
                ->whereRaw($whereRaw)
                ->whereIn('emp_expense_employee', $employee)
                ->groupBy('emp_expense_exp_id','emp_expense_employee','emp_expense_id','emp_expense_date','emp_expense_description','emp_expense_type','emp_expense_amount','emp_expense_approved_by_hr','emp_expense_reimbursement','emp_expense_created_at','employee_name','employee_code','emp_expense_approved_by_hr_time','emp_expense_attachment','emp_expense_approved_by_hr_reason','pc_id','pc_month','pc_year','disburse')
                ->orderBy('emp_expense_id','DESC')
                ->get()->toArray();

                $fetch_expenses = array_merge($fetch_expenses , $fetch_expense);    
            }    

            $fetch_expenses_total = [];
            $expense_arrays = array_chunk($total_team_member , 2000);
            foreach($expense_arrays as $employee_expense){
                $fetch_expense_total = EmployeeExpense::selectRaw(' sum(emp_expense_amount) as total')
                ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
                ->whereIn('emp_expense_amt_id',function($query){
                    $query->from('tbl_employee_expense_amount')
                    ->selectRaw('MAX(emp_expense_amt_id)')
                    ->groupBy('emp_expense_exp_id');
                })
                ->whereRaw($where_clause_to)
                ->whereIn('emp_expense_employee', $employee_expense)
                ->get()->toArray();

                $fetch_expenses_total = array_merge($fetch_expenses_total , $fetch_expense_total);    
            }        
           
            $fetch_expenses_approved  = [];
            $expense_approved_arrays = array_chunk($total_team_member , 2000);
            foreach($expense_approved_arrays as $employee_approved){
                $fetch_expense_total_approved = EmployeeExpense::selectRaw('sum(emp_expense_amount) as total')
                ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
                ->whereIn('emp_expense_amt_id',function($query){
                    $query->from('tbl_employee_expense_amount')
                    ->selectRaw('MAX(emp_expense_amt_id)')
                    ->groupBy('emp_expense_exp_id');
                })
                ->whereRaw($where_clause_to)
                ->whereIn('emp_expense_employee', $employee_approved)
                ->where('emp_expense_approved_by_hr',2)
                ->get()->toArray();

                $fetch_expenses_approved = array_merge($fetch_expenses_approved , $fetch_expense_total_approved);    
            }        

            $fetch_expenses_paid  = [];
            $expense_paid_arrays = array_chunk($total_team_member , 2000);
            foreach($expense_paid_arrays as $employee_paid){
                $fetch_expense_total_paid = EmployeeExpense::selectRaw('sum(emp_expense_amount) as total')
                ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
                ->whereIn('emp_expense_amt_id',function($query){
                    $query->from('tbl_employee_expense_amount')
                    ->selectRaw('MAX(emp_expense_amt_id)')
                    ->groupBy('emp_expense_exp_id');
                })
                ->whereRaw($where_clause_to)
                ->whereIn('emp_expense_employee', $employee_paid)
                ->where('emp_expense_reimbursement',2)
                ->get()->toArray();
            
                $fetch_expenses_paid = array_merge($fetch_expenses_paid , $fetch_expense_total_paid);    
            }         
            $expense_type_list = $this->expense_type_list;

            if($login_user[0]['employee_department'] != 1 && $login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 3){
                
                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->whereRaw($where_clause)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

            }else{

                $employees_data = Employees::where('employee_isactive','=',$status)->whereRaw($where_clause)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

            }
            
            $send_data = compact('title','login_user','fetch_expenses','fetch_expenses_total','fetch_expenses_approved','fetch_expenses_paid','expense_type_list','responsed_request','employees_data','employee_locations');
            
            return view('employee-expense.all-expense',$send_data);
        }catch(\Exception $e){

            $this->create_error_log('All Employee Expense', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('All Employee Expense');
        
        }
    }

    protected function my_expense(Request $request){

        try{
          
            $title = Self::SUB_TITLE;
      
            $login_user = $this->get_login_user_detail();

            $whereRaw = '1=1 ';
            if($request->get('status')){

                $whereRaw .= ' and emp_expense_approved_by_hr = '.$request->get('status');
            }
            if($request->get('paid')){

                $whereRaw .= ' and emp_expense_reimbursement = '.$request->get('paid');
            }

            $fetch_expense = EmployeeExpense::
            selectRaw('emp_expense_id,emp_expense_date,emp_expense_description,emp_expense_type,emp_expense_amount,emp_expense_approved_by_hr,emp_expense_reimbursement,emp_expense_created_at,employee_name,employee_code,emp_expense_approved_by_hr_time,emp_expense_attachment,emp_expense_approved_by_hr_reason, (select count(emp_expense_exp_id) from tbl_employee_expense_amount where emp_expense_exp_id = emp_expense_id   ) as update_amount, pc_id, pc_month, pc_year, disburse')
            ->where('emp_expense_employee',$login_user[0]['employee_id'])
            ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
            ->leftjoin('tbl_employees','tbl_employees.employee_id','tbl_employee_expense.emp_expense_approved_by_hr_id')
            ->leftjoin('tbl_payroll_calculation','tbl_payroll_calculation.pc_id','tbl_employee_expense.emp_expense_payslip_id')
            ->whereIn('emp_expense_amt_id',function($query){
                $query->from('tbl_employee_expense_amount')
                ->selectRaw('MAX(emp_expense_amt_id)')
                ->groupBy('emp_expense_exp_id');
            })
            // ->whereRaw($where_clause)
            ->whereRaw($whereRaw)
            ->groupBy('emp_expense_exp_id','emp_expense_employee','emp_expense_id','emp_expense_date','emp_expense_description','emp_expense_type','emp_expense_amount','emp_expense_approved_by_hr','emp_expense_reimbursement','emp_expense_created_at','employee_name','employee_code','emp_expense_approved_by_hr_time','emp_expense_attachment','emp_expense_approved_by_hr_reason','pc_id','pc_month','pc_year','disburse')
            ->orderBy('emp_expense_id','DESC')
            ->get()->toArray();

            $fetch_expense_total = EmployeeExpense::selectRaw(' sum(emp_expense_amount) as total')
            ->where('emp_expense_employee',$login_user[0]['employee_id'])
            ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
            ->whereIn('emp_expense_amt_id',function($query){
                $query->from('tbl_employee_expense_amount')
                ->selectRaw('MAX(emp_expense_amt_id)')
                ->groupBy('emp_expense_exp_id');
            })
            ->get()->toArray();
            
            $fetch_expense_total_approved = EmployeeExpense::selectRaw('sum(emp_expense_amount) as total')
            ->where('emp_expense_employee',$login_user[0]['employee_id'])
            ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
            ->whereIn('emp_expense_amt_id',function($query){
                $query->from('tbl_employee_expense_amount')
                ->selectRaw('MAX(emp_expense_amt_id)')
                ->groupBy('emp_expense_exp_id');
            })
            ->where('emp_expense_approved_by_hr',2)
            ->get()->toArray();

            $fetch_expense_total_paid = EmployeeExpense::selectRaw('sum(emp_expense_amount) as total')
            ->where('emp_expense_employee',$login_user[0]['employee_id'])
            ->join('tbl_employee_expense_amount','tbl_employee_expense.emp_expense_id','tbl_employee_expense_amount.emp_expense_exp_id')
            ->whereIn('emp_expense_amt_id',function($query){
                $query->from('tbl_employee_expense_amount')
                ->selectRaw('MAX(emp_expense_amt_id)')
                ->groupBy('emp_expense_exp_id');
            })
            ->where('emp_expense_reimbursement',2)
            ->get()->toArray();

            $employee_expense_id = array_column($fetch_expense,'emp_expense_id');
            $responsed_request = RequestApproval::where('request_module_id',7)->whereIn('request_id',$employee_expense_id)->pluck('request_id')->toArray();

            $expense_type_list = $this->expense_type_list;

            $send_data = compact('title','login_user','fetch_expense','fetch_expense_total','fetch_expense_total_approved','fetch_expense_total_paid','expense_type_list','responsed_request');

            return view('employee-expense.my-expense',$send_data);
        }catch(\Exception $e){

            $this->create_error_log('My Expense', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('My Expense');
        
    
        }
    }

    protected function add_my_expense(Request $request){


        try{

            $filename = '';

            if($request->hasFile('expense_attachment')){
                $filename = $request->file('expense_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->all(), [
            
                'expense_date' => 'required|date_format:Y-m-d|before_or_equal:now',
                'expense_reason' => 'required',
                'expense_type' => 'required|in:'.implode(",",array_keys($this->expense_type_list)),
                'expense_amount' => 'required|min:1|max:9999999.99|numeric',
                'expense_attachment' => ['max:10000', ($filename == 'txt' || $filename == 'csv' ) ? 
                File::types(['jpg','png','xlsx','csv','docx','pdf','txt'])
                :
                File::types(['jpg','png','xlsx','csv','docx','pdf'])],
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();

            $all_employee_expenses = EmployeeExpense::orderBy('emp_expense_id','DESC')->limit(1)->get(['emp_expense_id'])->toArray();
            if(empty($all_employee_expenses)){
                $employee_expenses = '1';
            }else{
                $employee_expenses = ++$all_employee_expenses[0]['emp_expense_id'];
            }
            $new_expense = new EmployeeExpense();
            $new_expense->emp_expense_employee = $login_user[0]['employee_id'];
            $new_expense->emp_expense_date = $request->input('expense_date');
            $new_expense->emp_expense_type = $request->input('expense_type');
            $new_expense->emp_expense_description = $request->input('expense_reason');
            $new_expense->emp_expense_approved_by_hr = 1;
            $new_expense->emp_expense_reimbursement = 1;

            $new_expense->emp_expense_created_at = date('Y-m-d H:i:s');

            if($request->hasFile('expense_attachment')){

                $fileNameToStore =  $employee_expenses.$request->file('expense_attachment')->getClientOriginalName();
                
                $request->file('expense_attachment')->storeAs('portal_documents/employee_expenses/',$fileNameToStore);
    
                $new_expense->emp_expense_attachment = $fileNameToStore;
    
            }
            $new_expense->save();

            $add_amount_expense = new EmployeeExpenseAmount();
            $add_amount_expense->emp_expense_exp_id = $new_expense->emp_expense_id;
            $add_amount_expense->emp_expense_amt_emp_id = $login_user[0]['employee_id'];
            $add_amount_expense->emp_expense_amount	 = $request->input('expense_amount');
            $add_amount_expense->emp_expense_create_at = date('Y-m-d H:i:s');
            $process = $add_amount_expense->save();

            if($process == 1){

                if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                    $to_mngr1  = $login_user[0]['employee_senior_manager'];
                }
                if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                    $to_mngr2 = $login_user[0]['employee_junior_manager'];
                }

        
                $this->send_notification('New Reimbursement Request', ''.$login_user[0]['employee_name'].' placed a reimbursement request. Please click here to view.','employee-expense?expense-id='. $new_expense->emp_expense_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');
                $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Reimbursement Request','You\'ve successfully applied reimbursement request and has been forwarded to your reporting authorities.');

                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Reimbursement added successfully.",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Reimbursement addition failed. Please try again after some time.",
                ]);
            }


        }catch(\Exception $e){

            $this->create_error_log('Add My Reimbursement', $e);
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

            ActivityTracker::track_activity('Add My Reimbursement');
    
        }
        
    }

    protected function expense_amount_log(Request $request){
    
        try{

            $get_expense_amount_log = EmployeeExpenseAmount::where('emp_expense_exp_id',$request->input('expense_id'))
            ->join('tbl_employees','tbl_employees.employee_id' , 'tbl_employee_expense_amount.emp_expense_amt_emp_id')
            ->get(['emp_expense_amount','employee_name','emp_expense_create_at','employee_code'])
            ->toArray();

            return response()->json([
                'status' => 'TRUE',
                'msg' => "Get amount log",
                'data' => $get_expense_amount_log,
            ]);
            
        }catch(\Exception $e){

            $this->create_error_log('Amount Log Reimbursement', $e);
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

            ActivityTracker::track_activity('Amount Log Reimbursement');

        }

    }

    protected function delete_my_expense(Request $request){

        try{

            $validator = Validator::make($request->all(), [
            
                'expense_id' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();

            if($this->check_request_validation($request->input('expense_id'),6) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Reimbursement request id is invalid',
                ]);
            }

            $fetch_delete_expense = EmployeeExpense::where('emp_expense_id',$request->input('expense_id'))->where('emp_expense_employee',$login_user[0]['employee_id'])->get()->toArray();
            if(!empty($fetch_delete_expense) ){

                $responsed_request_manager = RequestApproval::where('request_module_id',7)->where('request_id',$request->input('expense_id'))->get()->toArray();
                if($fetch_delete_expense[0]['emp_expense_approved_by_hr'] == 1 && empty($responsed_request_manager) ){


                    if(!empty($fetch_delete_expense[0]['emp_expense_attachment'])){

                        if (file_exists(storage_path('app/portal_documents/employee_expenses/'.$fetch_delete_expense[0]['emp_expense_attachment']))) {
        
                            unlink(storage_path('app/portal_documents/employee_expenses/'.$fetch_delete_expense[0]['emp_expense_attachment']));
                        }
                    }
                    
                    $delete_expense = EmployeeExpense::where('emp_expense_id',$request->input('expense_id'))->where('emp_expense_employee',$login_user[0]['employee_id'])->delete();
                    $delete_expense_amount = EmployeeExpenseAmount::where('emp_expense_exp_id',$request->input('expense_id'))->delete();
                    
                    if($delete_expense_amount == 1){

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Reimbursement delete successfully",
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
                        'msg' => "You can't delete this request now because your reporting authority has changed its status..",
                    ]);

                }
                

            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Maybe reimbursement already deleted.",
                ]);

            }

        }catch(\Exception $e){

            $this->create_error_log('Delete My Reimbursement', $e);
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

            ActivityTracker::track_activity('Delete My Reimbursement');
    
        }
    }

    protected function update_my_expense(Request $request){

        try{

            $filename = '';

            if($request->hasFile('expense_attachment')){
                $filename = $request->file('expense_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->all(), [
                'expense_id' => 'required',
                'expense_date' => 'required|date_format:Y-m-d|before_or_equal:now',
                'expense_reason' => 'required',
                'expense_type' => 'required|in:'.implode(",",array_keys($this->expense_type_list)),
                'expense_amount' => 'required|min:1|max:9999999.99|numeric',
                'expense_attachment' => ['max:10000', ($filename == 'txt' || $filename == 'csv' ) ? 
                File::types(['jpg','png','xlsx','csv','docx','pdf','txt'])
                :
                File::types(['jpg','png','xlsx','csv','docx','pdf'])],
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            if($this->check_request_validation($request->input('expense_id'),6) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Reimbursement request id is invalid',
                ]);
            }

            $login_user = $this->get_login_user_detail();

            $fetch_update_expense = EmployeeExpense::where('emp_expense_id',$request->input('expense_id'))->where('emp_expense_employee',$login_user[0]['employee_id'])->get()->toArray();

            if(!empty($fetch_update_expense)){

        
                $responsed_request_manager = RequestApproval::where('request_module_id',7)->where('request_id',$request->input('expense_id'))->get()->toArray();

                if($fetch_update_expense[0]['emp_expense_approved_by_hr'] == 1 && empty($responsed_request_manager)){
                    

                    $update_expense = EmployeeExpense::find($request->input('expense_id'));
                    $update_expense->emp_expense_date = $request->input('expense_date');
                    $update_expense->emp_expense_type = $request->input('expense_type');
                    $update_expense->emp_expense_description = $request->input('expense_reason');

                    $update_expense->emp_expense_created_at = date('Y-m-d H:i:s');

                    if($request->hasFile('expense_attachment')){

                        $fileNameToStore = $request->input('expense_id').$request->file('expense_attachment')->getClientOriginalName();
                        
                        $request->file('expense_attachment')->storeAs('portal_documents/employee_expenses/',$fileNameToStore);
            
                        $update_expense->emp_expense_attachment = $fileNameToStore;

                        if(!empty($fetch_update_expense[0]['emp_expense_attachment'])){
                           
                            if (file_exists(storage_path('app/portal_documents/employee_expenses/'.$fetch_update_expense[0]['emp_expense_attachment']))) {
                                
                                unlink(storage_path('app/portal_documents/employee_expenses/'.$fetch_update_expense[0]['emp_expense_attachment']));
                               
                            }
                        }
            
                    }
                    $update_expense->save();

                    $delete_expense_amount = EmployeeExpenseAmount::where('emp_expense_exp_id',$request->input('expense_id'))->delete();

                    $add_amount_expense = new EmployeeExpenseAmount();
                    $add_amount_expense->emp_expense_exp_id = $request->input('expense_id');
                    $add_amount_expense->emp_expense_amt_emp_id = $login_user[0]['employee_id'];
                    $add_amount_expense->emp_expense_amount	 = $request->input('expense_amount');
                    $add_amount_expense->emp_expense_create_at = date('Y-m-d H:i:s');
                    $process = $add_amount_expense->save();

                    if($process == 1){

                        $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Update Reimbursement Request','Your reimbursement request has been update.');


                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Reimbursement updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Reimbursement updated failed. Please try again after some time.",
                        ]);
                    }

                }else{

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You can't update this request now because your reporting authority has changed its status..",
                    ]);
                }

            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Maybe reimbursement has been deleted.",
                ]);

            }

        }catch(\Exception $e){

            $this->create_error_log('Update My Reimbursement', $e);
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

            ActivityTracker::track_activity('Update My Reimbursement');

        }

    }

    protected function approved_expense(Request $request){

        try{

            $login_user = $this->get_login_user_detail();

            $validator = Validator::make($request->all(), [
            
                'approved_expense_id' => 'required',
                'expense_amount' => 'required|min:1|max:9999999.99|numeric',
                'expense_message' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $fetch_approved_expense = EmployeeExpense::where('emp_expense_id',$request->input('approved_expense_id'))->get()->toArray();

            if(!empty($fetch_approved_expense )){

                if($fetch_approved_expense[0]['emp_expense_approved_by_hr'] == 1){

                    $approved_expense_amount = EmployeeExpenseAmount::where('emp_expense_exp_id',$request->input('approved_expense_id'))->orderBy('emp_expense_amt_id','DESC')->limit(1)->get(['emp_expense_amount'])->toArray();
                    $original_amount = $approved_expense_amount[0]['emp_expense_amount'];

                    $update_approved_expense = EmployeeExpense::find($request->input('approved_expense_id'));

                    $employee_id_notification = $update_approved_expense->emp_expense_employee;

                    $update_approved_expense->emp_expense_approved_by_hr = 2;
                    $update_approved_expense->emp_expense_approved_by_hr_id = $login_user[0]['employee_id'];
                    $update_approved_expense->emp_expense_approved_by_hr_time = date('Y-m-d H:i:s');
                    $update_approved_expense->emp_expense_approved_by_hr_reason = $request->input('expense_message');
                    $process = $update_approved_expense->save();

                    if($original_amount != $request->input('expense_amount')){

                        $add_amount_expense = new EmployeeExpenseAmount();
                        $add_amount_expense->emp_expense_exp_id = $request->input('approved_expense_id');
                        $add_amount_expense->emp_expense_amt_emp_id = $login_user[0]['employee_id'];
                        $add_amount_expense->emp_expense_amount	 = $request->input('expense_amount');
                        $add_amount_expense->emp_expense_create_at = date('Y-m-d H:i:s');
                        $process = $add_amount_expense->save();

                    }

                    if($process == 1){

                        $this->send_notification('Reimbursement Approved', 'Dear employee, your reimbursement request has been approved by HR. Please click here to view.','my-expense?expense-id='. $request->input('approved_expense_id') .'',$employee_id_notification,NULL,NULL,'no');

                        $this->send_push_notification_mobile_app($update_approved_expense['emp_expense_employee'], 'Reimbursement Request','Your reimbursement request has been approved by HR.');

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Aprroved successfully.",
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
                        'msg' => "Reimbursement status already changed.",
                    ]);

                }

            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Maybe reimbursement has been deleted.",
                ]);

            }
            
            
        }catch(\Exception $e){

            $this->create_error_log('Approved reimbursement By HR', $e);
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

            ActivityTracker::track_activity('Approved reimbursement By HR');

        }

    }

    protected function disapproved_expense(Request $request){
    
        try{
            
            $login_user = $this->get_login_user_detail();

            $validator = Validator::make($request->all(), [
            
                'disapproved_expense_id' => 'required',
                'expense_message' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $fetch_disapproved_expense = EmployeeExpense::where('emp_expense_id',$request->input('disapproved_expense_id'))->get()->toArray();

            if(!empty($fetch_disapproved_expense )){

                if($fetch_disapproved_expense[0]['emp_expense_approved_by_hr'] == 1){

                    $update_approved_expense = EmployeeExpense::find($request->input('disapproved_expense_id'));

                    $employee_id_notification = $update_approved_expense->emp_expense_employee;

                    $update_approved_expense->emp_expense_approved_by_hr = 3;
                    $update_approved_expense->emp_expense_approved_by_hr_id = $login_user[0]['employee_id'];
                    $update_approved_expense->emp_expense_approved_by_hr_time = date('Y-m-d H:i:s');
                    $update_approved_expense->emp_expense_approved_by_hr_reason = $request->input('expense_message');
                    $process = $update_approved_expense->save();


                    if($process == 1){

                        $this->send_notification('Reimbursement Disapproved', ' Dear employee, your reimbursement request has been disapproved by HR. Please click here to view. ','my-expense?expense-id='. $request->input('disapproved_expense_id') .'',$employee_id_notification,NULL,NULL,'no');

                        $this->send_push_notification_mobile_app($update_approved_expense['emp_expense_employee'], 'Reimbursement Request','Your reimbursement request has been disapproved by HR.');

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Disapproved successfully.",
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
                        'msg' => "Reimbursement status already changed.",
                    ]);

                }

            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Maybe reimbursement has been deleted.",
                ]);

            }

        
        }catch(\Exception $e){

            $this->create_error_log('Disapproved Reimbursement By HR', $e);
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

            ActivityTracker::track_activity('Disapproved Reimbursement By HR');

        }

    }

    protected function approved_expense_manager(Request $request){
        try{

            $login_user = $this->get_login_user_detail();

            $validator = Validator::make($request->all(), [
            
                'approved_expense_id' => 'required',
                'expense_amount' => 'required|min:1|max:9999999.99|numeric',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $fetch_approved_expense = EmployeeExpense::where('emp_expense_id',$request->input('approved_expense_id'))->get()->toArray();
            $status = 1;

            if(!empty($fetch_approved_expense )){

                if($fetch_approved_expense[0]['emp_expense_approved_by_hr'] == 1){
                    
                    $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'],  $status);

                    if(in_array($fetch_approved_expense[0]['emp_expense_employee'], $total_team_member)){

                        $responsed_request = RequestApproval::where('request_module_id',7)->where('request_id',$request->input('approved_expense_id'))->where('request_approver_id',$login_user[0]['employee_id'])->get()->toArray();

                        if(empty($responsed_request)){

                            $approved_expense_amount = EmployeeExpenseAmount::where('emp_expense_exp_id',$request->input('approved_expense_id'))->orderBy('emp_expense_amt_id','DESC')->limit(1)->get(['emp_expense_amount'])->toArray();
                            $original_amount = $approved_expense_amount[0]['emp_expense_amount'];

                            if($original_amount != $request->input('expense_amount')){

                                $add_amount_expense = new EmployeeExpenseAmount();
                                $add_amount_expense->emp_expense_exp_id = $request->input('approved_expense_id');
                                $add_amount_expense->emp_expense_amt_emp_id = $login_user[0]['employee_id'];
                                $add_amount_expense->emp_expense_amount	 = $request->input('expense_amount');
                                $add_amount_expense->emp_expense_create_at = date('Y-m-d H:i:s');
                                $add_amount_expense->save();

                            }

                            $expense_approval = new RequestApproval();
                            $expense_approval->request_module_id = 7;
                            $expense_approval->request_id = $request->input('approved_expense_id');
                            $expense_approval->request_approver_id =  $login_user[0]['employee_id'];
                            $expense_approval->request_approver_status = 2;
                            $expense_approval->request_approver_msg = $request->input('expense_message');
                            $expense_approval->request_approval_time = date('h:i A d M, Y');
                            $process = $expense_approval->save();

                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $fetch_approved_expense[0]['emp_expense_employee'])->get(['employee_id','employee_code','employee_name'])->toArray();

                                $employee_id_notification = $fetch_approved_expense[0]['emp_expense_employee'];
                                $this->send_notification('Reimbursement Approved ', ' Dear employee, your reimbursement request has been approved by your manager. Please click here to view. ','my-expense?expense-id='. $request->input('approved_expense_id') .'',$employee_id_notification,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($fetch_approved_expense[0]['emp_expense_employee'], 'Reimbursement Request','Your reimbursement request been approved by your line manager.');
                                
                                $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Approved their reimbursement request. Please click here to view.';

                                $this->send_notification('Reimbursement Approved ',$notification_msg_hr, 'employee-expense?expense-id='.$request->input('approved_expense_id') .'',NULL,NULL,NULL,'yes');
                                
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Aprroved successfully.",
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
                                'msg' => "You have already responsed on this request.",
                            ]);

                        }

                    }else{

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This employee has not in your reporting.",
                        ]);
                        
                    }

                }else{

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Reimbursement status already changed.",
                    ]);

                }

            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Maybe reimbursement has been deleted.",
                ]);

            }

        }catch(\Exception $e){

            $this->create_error_log('Disapproved Reimbursement By Manager', $e);
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

            ActivityTracker::track_activity('Disapproved Reimbursement By Manager');

        }

    }

    protected function disapproved_expense_manager(Request $request){

        try{

            $login_user = $this->get_login_user_detail();
    
            $validator = Validator::make($request->all(), [
            
                'disapproved_expense_id' => 'required',
                'expense_message' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $fetch_disapproved_expense = EmployeeExpense::where('emp_expense_id',$request->input('disapproved_expense_id'))->get()->toArray();
            $status = 1;

            if(!empty($fetch_disapproved_expense )){

                if($fetch_disapproved_expense[0]['emp_expense_approved_by_hr'] == 1){
                    
                    $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'],  $status);

                    if(in_array($fetch_disapproved_expense[0]['emp_expense_employee'], $total_team_member)){

                        $responsed_request = RequestApproval::where('request_module_id',7)->where('request_id',$request->input('disapproved_expense_id'))->where('request_approver_id',$login_user[0]['employee_id'])->get()->toArray();

                        if(empty($responsed_request)){


                            $expense_approval = new RequestApproval();
                            $expense_approval->request_module_id = 7;
                            $expense_approval->request_id = $request->input('disapproved_expense_id');
                            $expense_approval->request_approver_id =  $login_user[0]['employee_id'];
                            $expense_approval->request_approver_status = 3;
                            $expense_approval->request_approver_msg = $request->input('expense_message');
                            $expense_approval->request_approval_time = date('h:i A d M, Y');
                            $process = $expense_approval->save();

                            if($process == 1){

                                $employee_name = Employees::where('employee_id', $fetch_disapproved_expense[0]['emp_expense_employee'])->get(['employee_id','employee_code','employee_name'])->toArray();

                                $employee_id_notification = $fetch_disapproved_expense[0]['emp_expense_employee'];
                                $this->send_notification('Reimbursement Disapproved', ' Dear employee, your reimbursement request has been disapproved by your manager. Please click here to view. ','my-expense?expense-id='. $request->input('disapproved_expense_id') .'',$employee_id_notification,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($fetch_disapproved_expense[0]['emp_expense_employee'], 'Reimbursement Request','Your reimbursement request has been disapproved by your line manager.');

                                $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager disapproved their reimbursement request. Please click here to view.';

                                $this->send_notification('Reimbursement Disapproved ',$notification_msg_hr, 'employee-expense?expense-id='.$request->input('disapproved_expense_id') .'',NULL,NULL,NULL,'yes');
                                
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Disapproved successfully.",
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
                                'msg' => "You have already responsed on this request.",
                            ]);

                        }

                    }else{

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This employee has not in your reporting.",
                        ]);
                        
                    }

                }else{

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Reimbursement status already changed.",
                    ]);

                }

            }else{

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Maybe reimbursement has been deleted.",
                ]);

            }
            
        }catch(\Exception $e){

            $this->create_error_log('Disapproved Reimbursement By HR', $e);
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

            ActivityTracker::track_activity('Disapproved Reimbursement By HR');

        }

    }

    protected function download_employee_expense_file(Request $request){
        try{
            if(!empty($request->path)){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
            }else{
                return redirect('/');
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
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
            ActivityTracker::track_activity($request->title);
        }
    }
}
