<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Loan, LoanRequest, Employees, EmployeeInformation, LoanInstallment, RequestApproval};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait,MailTrait};
use App\Helpers\{ActivityTracker, CustomHelper};

class LoanApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait ,NotificationTrait, ApiResponseTrait,MailTrait;

    const MAIN_TITLE2 = "my-loan";

    protected function my_loan(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $title = Self::MAIN_TITLE2;

            $all_loans = LoanRequest::where('request_employee_id', $login_user[0]['employee_id'])->orderBy('loan_request_id', 'DESC')->get()->toArray();

            $loans_request_id = array_column($all_loans,'loan_request_id');

            $responsed_request = RequestApproval::where('request_module_id', 5)->whereIn('request_id', $loans_request_id)->pluck('request_id')->toArray();

            $loan_given_amount = LoanRequest::selectRaw('sum(`loan_request_amount`) as total')->where('request_approved_by_finance', 2)->where('request_employee_id', $login_user[0]['employee_id'])->get()->toArray();
          
            $loan_return_amount = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')->join('tbl_loan_installments', 'tbl_loan_installments.loan_installment_loan_id', '=', 'tbl_loans.loan_id')->where('employee_id', $login_user[0]['employee_id'])->selectRaw('sum(`loan_installment_amount`) as total')->get()->toArray();
            
            $loan_remaining_amount = $loan_given_amount[0]['total'] - $loan_return_amount[0]['total'] ;
            
            $data = compact('title','all_loans' ,'loan_given_amount','loan_return_amount','loan_remaining_amount','responsed_request');

            return $this->api_response_json(TRUE,"View My Loans",$data,200);
            
        }catch(\Exception $e){

            $this->create_error_log_from_api('View My Loans Page API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('View My Loans Page API',  $request->header('Bearer-Token'));
        }

    }

    protected function new_loan_request(Request $request){
       
		try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $deduction_month = date('m')-1;

            if($deduction_month ==  '-1'){

                $deduction_month = 11;

            }elseif($deduction_month == '0'){

                $deduction_month = 12;

            }else{

                $deduction_month = $deduction_month;
            }

            $message = ['before'=>'The deduction start date must be a date before ' .date( 'F-Y',strtotime('+2 month')),'after'=>'The deduction start date must be a date after '.date( 'F-Y',strtotime(date('Y-'.$deduction_month.'')))];

            $validator = Validator::make($request->post(), [
                'loan_amount' => 'required|min:1|max:9999999.99|numeric',
                'deduction_amount' => ($request->post('deduction_type') == 1 ) ? 'required|min:1|max:'.$request->post('loan_amount').'|numeric' : '' ,
                'deduction_percentage' => ($request->post('deduction_type') == 2 ) ? 'required|min:1|max:100|numeric' : '' ,
                'loan_reason' => 'Required|in:Bike Loan,Car Loan,Property Loan,Advance Salary,Other',
                'loan_message' => 'Required', 
                'deduction_type' => 'required|integer|in:1,2', 
                'deduction_start' => 'Required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-'.$deduction_month.'').'',

            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {
                if($request->post('deduction_type') == 1){
                    $deduction_amount  =  $request->post('deduction_amount');
                }else{
                    $deduction_amount  = $request->post('deduction_percentage');
                }
        
                $checking_request = LoanRequest::where('request_employee_id', $login_user[0]['employee_id'])->where('loan_applied_date', 'like', '%'.date('d-m-Y').'%')->get()->toArray();

                if(empty($checking_request)){
                    $new_loan = new LoanRequest();
                    $new_loan->request_employee_id = $login_user[0]['employee_id'];
                    $new_loan->loan_request_amount =  $request->post('loan_amount');
                    $new_loan->loan_deduction_type = $request->post('deduction_type');
                    $new_loan->loan_deduction_start = $request->post('deduction_start');
                    $new_loan->loan_deduction_amount = $deduction_amount;
                    $new_loan->loan_reason = $request->post('loan_reason');
                    $new_loan->loan_reason_message = $request->post('loan_message');
                    $new_loan->request_approved_by_finance = 1;
                    $new_loan->loan_applied_date = date('Y-m-d H:i:s');
                    $process = $new_loan->save();
                    if($process == 1){

                        if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                            $to_mngr1  = $login_user[0]['employee_senior_manager'];
                        }
                        if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                            $to_mngr2 = $login_user[0]['employee_junior_manager'];
                        }

                        $this->send_notification_api('New Loan Request',''.$login_user[0]['employee_name'].' applied for loan. Please click here to view.','all-loan-request?request-id='.  $new_loan->loan_request_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes', $login_user[0]['employee_id']);

                        $this->send_module_email(1,'New Loan Request','<b>'.$login_user[0]['employee_name'].'</b> applied for loan request.',''.url('/').'/all-loan-request');

                        return $this->api_response_json(TRUE,"Loan request has been submitted. Please wait for response.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                    }
                }else{

                    $message = "You have already submitted a loan request for ".date('d-m-Y');

                    return $this->api_response_json(FALSE,$message,"",200);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Apply For Loan Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Access Request for Loan Request Module API',  $request->header('Bearer-Token'));
        }
    }

    protected function update_loan_request(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('request_id'),5,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Loan request id is invalid.","",200);
            }

            $deduction_month = date('m')-1;

            if($deduction_month == '-1'){

                $deduction_month = 11;

            }elseif($deduction_month == '0'){
                $deduction_month = 12;
            }else{
                $deduction_month = $deduction_month;
            }

            $message = ['before'=>'The deduction start date must be a date before ' .date( 'F-Y',strtotime('+2 month')),'after'=>'The deduction start date must be a date after '.date( 'F-Y',strtotime(date('Y-'.$deduction_month.'')))];

            $validator = Validator::make($request->all(), [
                'loan_amount' => 'required|min:1|max:9999999.99|numeric',
                'deduction_amount' => ($request->post('deduction_type') == 1 ) ? 'required|min:1|max:'.$request->post('loan_amount').'|numeric' : '' ,
                'deduction_percentage' => ($request->post('deduction_type') == 2 ) ? 'required|min:1|max:100|numeric' : '' ,
                'loan_reason' => 'Required|in:Bike Loan,Car Loan,Property Loan,Advance Salary,Other',
                'loan_message' => 'Required', 
                'deduction_type' => 'required|integer|in:1,2', 
                'deduction_start' => 'Required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-'.$deduction_month.'').'',
            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $loan_update_request = LoanRequest::find($request->post('request_id'));

                if(!empty($loan_update_request)){

                    $checking_request_responsed = RequestApproval::where('request_module_id',5)->where('request_id',$request->post('request_id'))->get()->toArray();
                
                    if(empty($checking_request_responsed) && $loan_update_request->request_approved_by_finance == 1){   

                        $checking_request = LoanRequest::where('request_employee_id', $login_user[0]['employee_id'])->where('loan_applied_date', 'like', '%'.date('d-m-Y').'%')->where('loan_request_id','!=',$request->post('request_id'))->get()->toArray();

                        if(empty($checking_request)){

                            if($request->post('deduction_type') == 1){
                                $deduction_amount  =  $request->post('deduction_amount');
                            }else{
                                $deduction_amount  = $request->post('deduction_percentage');
                            }
                        
                            $loan_update_request->loan_request_amount =  $request->post('loan_amount');
                            $loan_update_request->loan_deduction_type = $request->post('deduction_type');
                            $loan_update_request->loan_deduction_start = $request->post('deduction_start');
                            $loan_update_request->loan_deduction_amount = $deduction_amount;
                            $loan_update_request->loan_reason = $request->post('loan_reason');
                            $loan_update_request->loan_reason_message = $request->post('loan_message');
                            $loan_update_request->loan_applied_date = date('Y-m-d H:i:s');
                        
                            $process =  $loan_update_request->save();

                            if( $process == 1){
                                if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                    $to_mngr1  = $login_user[0]['employee_senior_manager'];
                                }
                                if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                    $to_mngr2 = $login_user[0]['employee_junior_manager'];
                                }
                
                                $this->send_notification_api('Update Loan Request',''.$login_user[0]['employee_name'].' Updated a loan Request. Please click here to view.','all-loan-request?request-id='.$loan_update_request->loan_request_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes', $login_user[0]['employee_id']);

                                return $this->api_response_json(TRUE,"Loan request has been updated. Please wait for response.","",200);

                            }else{

                                return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                            }
                            
                        }else{
                            
                            $message = "You have already submitted a loan request for ".date('d-m-Y');

                            return $this->api_response_json(FALSE,$message,"",200);
                        }
                    }else{

                        return $this->api_response_json(FALSE,"You can't edit this loan now because your reporting authority has changed its status.","",200);
                    }
                }else{

                    return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
                }     
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Update Loan Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
            
        }finally{

            ActivityTracker::track_activity_from_api('Update Loan Request API',  $request->header('Bearer-Token'));
        }
    }

    protected function delete_loan_request(Request $request){
      
        try{
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('delete_id'),5,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Loan request id is invalid.","",200);
            }

            $loan_delete_request = LoanRequest::find($request->post('delete_id'));

            if(!empty($loan_delete_request)){

                $checking_request_responsed = RequestApproval::where('request_module_id',5)->where('request_id',$request->post('delete_id'))->get()->toArray(); 

                if(empty($checking_request_responsed) && $loan_delete_request->request_approved_by_finance == 1){    
                    
                    $process = LoanRequest::where('loan_request_id', $request->post('delete_id'))->delete();

                    if($process == 1){

                        return $this->api_response_json(TRUE,"Loan request has been deleted.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                    }
                        
                }else{

                    return $this->api_response_json(FALSE,"You can't delete this loan now because your reporting authority has changed its status.","",200);
                }
            }else{

                return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
            }         
        }catch(\Exception $e){

            $this->create_error_log_from_api('Delete Loan Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Delete Loan Request API',  $request->header('Bearer-Token'));
        }
    }

    protected function my_loan_request_detail(Request $request, $loan_request_id){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($loan_request_id, 5, $login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Loan request id is invalid.","",200);
            }

            $loan_detail  = Loan::where('loan_request_id', $loan_request_id)->get(['loan_id','loan_given_amount','loan_remaining_amount','loan_submitted_amount'])->toArray();

            if(!empty($loan_detail)){

                $loan_installment = LoanInstallment::where('loan_installment_loan_id',$loan_detail[0]['loan_id'])->get(['loan_installment_amount','loan_installment_date'])->toArray();

                return $this->api_response_json(TRUE,"My Loan Request Detail",$loan_detail,200,[$loan_installment]);
                
            }else{

                $loan_request_status = LoanRequest::where('loan_request_id', $loan_request_id)->get(['request_approved_by_finance'])->toArray();

                if($loan_request_status[0]['request_approved_by_finance'] == 1){
                    return $this->api_response_json(FALSE,"Loan request is Pending.","",200);
                }else{
                    return $this->api_response_json(FALSE,"Loan request is Disapproved.","",200);
                }

            }

        }catch(\Exception $e){

            $this->create_error_log_from_api('My Loan Request Detail API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
            
        }finally{

            ActivityTracker::track_activity_from_api('My Loan Request Detail API', $request->header('Bearer-Token'));
        }
    }

}