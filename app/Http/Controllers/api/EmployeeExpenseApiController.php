<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

use App\Models\{EmployeeExpense, EmployeeExpenseAmount, RequestApproval, Employees};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait, AttendanceEmployee};
use App\Helpers\ActivityTracker;

class EmployeeExpenseApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait ,NotificationTrait, ApiResponseTrait;
    const SUB_TITLE = 'my-expense';

    protected $expense_type_list = [ 1 => 'Travel and accommodation', 2 => 'Corporate entertainment', 3 => 'Stationery and consumable goods', 4 => 'Subscriptions', 5 => 'Telephony and home office use' ];

    protected function my_expense(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
          
            $title = Self::SUB_TITLE;
      
            $where_clause = '1=1 ';
            if($request->get('status')){

                $where_clause .= ' and emp_expense_approved_by_hr = '.\DB::getPdo()->quote((string) $request->get('status'));
            }
            if($request->get('paid')){

                $where_clause .= ' and emp_expense_reimbursement = '.\DB::getPdo()->quote((string) $request->get('paid'));
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
            ->whereRaw($where_clause)
            ->groupBy('emp_expense_exp_id')
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

            $data = compact('title','fetch_expense','fetch_expense_total','fetch_expense_total_approved','fetch_expense_total_paid','expense_type_list','responsed_request');

            return $this->api_response_json(TRUE,"My Expense",$data,200);


        }catch(\Exception $e){

            $this->create_error_log_from_api('Access Request My Expense Module API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('View Access My Expense Module API',  $request->header('Bearer-Token'));    
        }
    }

    protected function add_my_expense(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $filename = '';

            if($request->hasFile('expense_attachment')){
                $filename = $request->file('expense_attachment')->getClientOriginalExtension();
            }
            
            $validator = Validator::make($request->post(), [
            
                'expense_date' => 'required|date_format:Y-m-d|before_or_equal:now',
                'expense_reason' => 'required',
                'expense_type' => 'required|in:'.implode(",",array_keys($this->expense_type_list)),
                'expense_amount' => 'required|min:1|max:9999999.99|numeric',
                'expense_attachment' => ['max:10000', ($filename == 'txt' || $filename == 'csv' ) ? 
                File::types(['jpg','png','xlsx','csv','docx','pdf','txt'])
                :
                File::types(['jpg','png','xlsx','docx','pdf'])],
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }

            $new_expense = new EmployeeExpense();
            $new_expense->emp_expense_employee = $login_user[0]['employee_id'];
            $new_expense->emp_expense_date = $request->post('expense_date');
            $new_expense->emp_expense_type = $request->post('expense_type');
            $new_expense->emp_expense_description = $request->post('expense_reason');
            $new_expense->emp_expense_approved_by_hr = 1;
            $new_expense->emp_expense_reimbursement = 1;

            $new_expense->emp_expense_created_at = date('Y-m-d H:i:s');

            if($request->hasFile('expense_attachment')){

                $fileNameToStore = $this->expense_file_name($request->file('expense_attachment'));

                $request->file('expense_attachment')->storeAs('portal_documents/employee_expenses/',$fileNameToStore);

                $new_expense->emp_expense_attachment = $fileNameToStore;

            }
            $new_expense->save();

            $add_amount_expense = new EmployeeExpenseAmount();
            $add_amount_expense->emp_expense_exp_id = $new_expense->emp_expense_id;
            $add_amount_expense->emp_expense_amt_emp_id = $login_user[0]['employee_id'];
            $add_amount_expense->emp_expense_amount	 = $request->post('expense_amount');
            $add_amount_expense->emp_expense_create_at = date('Y-m-d H:i:s');
            $process = $add_amount_expense->save();

            if($process == 1){

                if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                    $to_mngr1  = $login_user[0]['employee_senior_manager'];
                }
                if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                    $to_mngr2 = $login_user[0]['employee_junior_manager'];
                }

                $this->send_notification_api('New Reimbursement Request', ''.$login_user[0]['employee_name'].' placed a reimbursement request. Please click here to view.','employee-expense?expense-id='. $new_expense->emp_expense_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                return $this->api_response_json(TRUE,"Reimbursement Added Successfully.","",200);

            }else{

                return $this->api_response_json(FALSE,"Reimbursement Addition Failed. Please try again after some time.","",200);
            }


        }catch(\Exception $e){

            $this->create_error_log_from_api('Add My Reimbursement Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Access Request Add My Reimbursement Module API',  $request->header('Bearer-Token'));    
        }
        
    }

    protected function update_my_expense(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('expense_id'),6,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Reimbursement request id is invalid.","",200);
            }

            $filename = '';

            if($request->hasFile('expense_attachment')){
                $filename = $request->file('expense_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->post(), [
                'expense_id' => 'required',
                'expense_date' => 'required|date_format:Y-m-d|before_or_equal:now',
                'expense_reason' => 'required',
                'expense_type' => 'required|in:'.implode(",",array_keys($this->expense_type_list)),
                'expense_amount' => 'required|min:1|max:9999999.99|numeric',
                'expense_attachment' => ['max:10000', ($filename == 'txt' || $filename == 'csv' ) ? 
                File::types(['jpg','png','xlsx','csv','docx','pdf','txt'])
                :
                File::types(['jpg','png','xlsx','docx','pdf'])],
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }

            $fetch_update_expense = EmployeeExpense::where('emp_expense_id',$request->post('expense_id'))->where('emp_expense_employee',$login_user[0]['employee_id'])->get()->toArray();

            if(!empty($fetch_update_expense)){

                $responsed_request_manager = RequestApproval::where('request_module_id',7)->where('request_id',$request->post('expense_id'))->get()->toArray();

                if($fetch_update_expense[0]['emp_expense_approved_by_hr'] == 1 && empty($responsed_request_manager)){
                    

                    $update_expense = EmployeeExpense::find($request->post('expense_id'));
                    $update_expense->emp_expense_date = $request->post('expense_date');
                    $update_expense->emp_expense_type = $request->post('expense_type');
                    $update_expense->emp_expense_description = $request->post('expense_reason');

                    $update_expense->emp_expense_created_at = date('Y-m-d H:i:s');

                    if($request->hasFile('expense_attachment')){

                        $fileNameToStore = $this->expense_file_name($request->file('expense_attachment'));

                        $request->file('expense_attachment')->storeAs('portal_documents/employee_expenses/',$fileNameToStore);

                        $update_expense->emp_expense_attachment = $fileNameToStore;

                        if(!empty($fetch_update_expense[0]['emp_expense_attachment'])){

                            if (file_exists(storage_path('app/portal_documents/employee_expenses/'.$fetch_update_expense[0]['emp_expense_attachment']))) {
            
                                unlink(storage_path('app/portal_documents/employee_expenses/'.$fetch_update_expense[0]['emp_expense_attachment']));
                            }
                        }
            
                    }
                    $update_expense->save();

                    $delete_expense_amount = EmployeeExpenseAmount::where('emp_expense_exp_id',$request->post('expense_id'))->delete();

                    $add_amount_expense = new EmployeeExpenseAmount();
                    $add_amount_expense->emp_expense_exp_id = $request->post('expense_id');
                    $add_amount_expense->emp_expense_amt_emp_id = $login_user[0]['employee_id'];
                    $add_amount_expense->emp_expense_amount	 = $request->post('expense_amount');
                    $add_amount_expense->emp_expense_create_at = date('Y-m-d H:i:s');
                    $process = $add_amount_expense->save();

                    if($process == 1){

                        return $this->api_response_json(TRUE,"Reimbursement Updated Successfully.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Reimbursement Updated Failed. Please try again after some time.","",200);
                    }

                }else{

                    return $this->api_response_json(FALSE,"You can't update this request now because your reporting authority has changed its status.","",200);
                }

            }else{

                return $this->api_response_json(FALSE,"Maybe reimbursement has been deleted.","",200);
            }

        }catch(\Exception $e){

            $this->create_error_log_from_api('Update My Reimbursement Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Access Request Update My Reimbursement Module API',  $request->header('Bearer-Token'));
        }

    }

    protected function delete_my_expense(Request $request){

        try{
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('expense_id'),6,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Reimbursement request id is invalid.","",200);
            }

            $validator = Validator::make($request->post(), [
            
                'expense_id' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }

            $fetch_delete_expense = EmployeeExpense::where('emp_expense_id',$request->post('expense_id'))->where('emp_expense_employee',$login_user[0]['employee_id'])->get()->toArray();
            if(!empty($fetch_delete_expense) ){

                $responsed_request_manager = RequestApproval::where('request_module_id',7)->where('request_id',$request->post('expense_id'))->get()->toArray();
                if($fetch_delete_expense[0]['emp_expense_approved_by_hr'] == 1 && empty($responsed_request_manager) ){


                    if(!empty($fetch_delete_expense[0]['emp_expense_attachment'])){

                        if (file_exists(storage_path('app/portal_documents/employee_expenses/'.$fetch_delete_expense[0]['emp_expense_attachment']))) {
        
                            unlink(storage_path('app/portal_documents/employee_expenses/'.$fetch_delete_expense[0]['emp_expense_attachment']));
                        }
                    }
                    
                    $delete_expense = EmployeeExpense::where('emp_expense_id',$request->post('expense_id'))->where('emp_expense_employee',$login_user[0]['employee_id'])->delete();
                    $delete_expense_amount = EmployeeExpenseAmount::where('emp_expense_exp_id',$request->post('expense_id'))->delete();
                    
                    if($delete_expense_amount == 1){

                        return $this->api_response_json(TRUE,"Reimbursement Delete Successfully.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                    }

                }else{

                    return $this->api_response_json(FALSE,"You can't delete this request now because your reporting authority has changed its status.","",200);
                }
                
            }else{

                return $this->api_response_json(FALSE,"Maybe reimbursement already deleted.","",200);
            }

        }catch(\Exception $e){

            $this->create_error_log_from_api('Delete My Reimbursement Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Delete My Reimbursement Request API',  $request->header('Bearer-Token'));    
        }
    }

    protected function expense_amount_log(Request $request){
    
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('expense_id'),6,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Reimbursement request id is invalid.","",200);
            }

            $get_expense_amount_log = EmployeeExpenseAmount::where('emp_expense_exp_id',$request->post('expense_id'))
            ->join('tbl_employees','tbl_employees.employee_id' , 'tbl_employee_expense_amount.emp_expense_amt_emp_id')
            ->get(['emp_expense_amount','employee_name','emp_expense_create_at','employee_code'])
            ->toArray();

            return $this->api_response_json(TRUE,"Get amount log.",$get_expense_amount_log,200);
            
        }catch(\Exception $e){

            $this->create_error_log_from_api('Amount Log Reimbursement API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Amount Log Reimbursement API',  $request->header('Bearer-Token'));
        }

    }

    // stored name: time + random part + the original name, so two employees' "bill.pdf" never overwrite each other
    protected function expense_file_name($file){

        return date('YmdHis').'_'.\Illuminate\Support\Str::random(6).'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
    }

    protected function download_employee_expense_file(Request $request){
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            // only the attachment of one of the employee's own claims: by expense_id, or (older app) by its path
            $own_expense = EmployeeExpense::where('emp_expense_employee', $login_user[0]['employee_id'])->whereNotNull('emp_expense_attachment')->where('emp_expense_attachment', '!=', '');
            if(!empty($request->get('expense_id'))){
                $attachment = $own_expense->where('emp_expense_id', $request->get('expense_id'))->value('emp_expense_attachment');
            }elseif(!empty($request->get('path'))){
                $path = trim(str_replace('\\', '/', (string) $request->get('path')), '/');
                $attachment = $own_expense->get(['emp_expense_attachment'])->pluck('emp_expense_attachment')->first(fn($name) => in_array($path, [$name, 'employee_expenses/'.$name]));
            }else{
                return $this->api_response_json(FALSE,"Path Not Found.","",200);
            }

            $file = !empty($attachment) ? \App\Helpers\CustomHelper::portal_document('employee_expenses/'.$attachment) : null;

            if(empty($file)){
                return $this->api_response_json(FALSE,"File Not Found.","",200);
            }

            return response()->download($file, $attachment);

        }catch(\Exception $e){

            $this->create_error_log_from_api('download-expense-file Api', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('download-expense-file Api',  $request->header('Bearer-Token'));
        }
    }

}   

