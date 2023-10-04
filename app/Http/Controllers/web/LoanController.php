<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\{Loan, LoanRequest, Employees, EmployeeInformation, LoanInstallment, RequestApproval, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, MailTrait};
use App\Helpers\{ActivityTracker, CustomHelper};

class LoanController extends Controller
{
    use LoginEmployee, LogGenerateTrait, NotificationTrait, MailTrait;

    const MAIN_TITLE = "manage-loan";
    const MAIN_TITLE2 = "my-loan";
    const TITLE_GLOBAL = "all-loan-request";
   
    protected function index(Request $request){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $currency = CustomHelper::hcm_system_currency();

            $limit_query = '500';
                
            if(!empty($request->loan_req)){
                $limit_query = '*';
            }

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1';
            $where_clause_to = '1=1';
            if(!empty($request->emp_status) || !empty($request->employee_location) || !empty($request->emp_name) || !empty($request->loan_issuance_date || !empty($request->loan_status))){
               
                if(!empty($request->employee_location)){
                    $where_clause .= " AND employee_location = '".$request_data->employee_location."' ";
                    $where_clause_to   .= " AND employee_location = '".$request_data->employee_location."' "; 
                }
                if(!empty($request->emp_name)){
                    $where_clause .= " AND tbl_loans.employee_code = '".$request->emp_name."'";
                }
                if(!empty($request->loan_issuance_date)){
                    $where_clause .= " AND loan_issuance_date like '%".CustomHelper::dateformatDFY($request->loan_issuance_date)."%' ";
                }
                if(!empty($request->emp_status)){
                    $where_clause .= " AND employee_isactive = '".$status."'";
                    $where_clause_to   .= " AND employee_isactive = '".$status."'"; 
                }
                if(!empty($request->loan_status)){
                    $where_clause .= " AND loan_status = '".$request->loan_status."'";
                }

            }

            $all_loans = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->whereRaw($where_clause)
            ->orderBy('loan_id','DESC')
            ->limit($limit_query)
            ->get(['loan_id','loan_given_amount','loan_submitted_amount','loan_remaining_amount','loan_issuance_date','employee_name','employee_id','tbl_employees.employee_code','profile_avatar','loan_deduction_type','loan_deduction_amount','loan_deduction_start','loan_status','department_name','designation_name'])
            ->toArray();


            $total_loan = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->whereRaw($where_clause)->sum(DB::raw('CAST(loan_given_amount AS DECIMAL(18, 2))'));

            $loan_return = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->whereRaw($where_clause)->sum(DB::raw('CAST(loan_submitted_amount AS DECIMAL(18, 2))'));

            $loan_reamining = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->whereRaw($where_clause)->sum(DB::raw('CAST(loan_remaining_amount AS DECIMAL(18, 2))'));

            $all_employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','department_name','designation_name'])->toArray();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            $send_data = compact('title','login_user','all_loans','total_loan','loan_return','loan_reamining','all_employees', 'employee_locations','currency');
            
            return view('loan.all-loans', $send_data);     

        }catch(\Exception $e){

            $this->create_error_log('View All Loans Page', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Loans Page');
              
            
        }

    }

    protected function download_loan_details(Request $request){

        try{

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
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/loans';</script>";
            }

            $limit_query = '500';
                
            if(!empty($request->loan_req)){
                $limit_query = '*';
            }

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1';
            $where_clause_to = '1=1';
            if(!empty($request->emp_status) || !empty($request->employee_location) || !empty($request->emp_name) || !empty($request->loan_issuance_date || !empty($request->loan_status))){
               
                if(!empty($request->employee_location)){
                    $where_clause .= " AND employee_location = '".$request_data->employee_location."' ";
                    $where_clause_to   .= " AND employee_location = '".$request_data->employee_location."' "; 
                }
                if(!empty($request->emp_name)){
                    $where_clause .= " AND tbl_loans.employee_code = '".$request->emp_name."'";
                }
                if(!empty($request->loan_issuance_date)){
                    $where_clause .= " AND loan_issuance_date like '%".CustomHelper::dateformatDFY($request->loan_issuance_date)."%' ";
                }
                if(!empty($request->emp_status)){
                    $where_clause .= " AND employee_isactive = '".$status."'";
                    $where_clause_to   .= " AND employee_isactive = '".$status."'"; 
                }
                if(!empty($request->loan_status)){
                    $where_clause .= " AND loan_status = '".$request->loan_status."'";
                }

            }

            $all_loans = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->whereRaw($where_clause)
            ->orderBy('loan_id','DESC')
            ->limit($limit_query)
            ->get(['loan_id','loan_given_amount','loan_submitted_amount','loan_remaining_amount','loan_issuance_date','employee_name','employee_id','tbl_employees.employee_code','profile_avatar','loan_deduction_type','loan_deduction_amount','loan_deduction_start','loan_status','department_name','designation_name'])
            ->toArray();

            $total_loan = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->whereRaw($where_clause)->sum(DB::raw('CAST(loan_given_amount AS DECIMAL(18, 2))'));

            $loan_return = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->whereRaw($where_clause)->sum(DB::raw('CAST(loan_submitted_amount AS DECIMAL(18, 2))'));

            $loan_reamining = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->whereRaw($where_clause)->sum(DB::raw('CAST(loan_remaining_amount AS DECIMAL(18, 2))'));

            $all_employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','department_name','designation_name'])->toArray();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            $employee_loans = Loan::distinct('employee_code')->count();

            $loan_status_paid = Loan::where('loan_status', '=', '2')->count();

            $loan_status_unpaid = Loan::where('loan_status', '=', '1')->count();
       
            $send_data = compact('login_user','all_loans','total_loan','loan_return','loan_reamining','employee_loans','all_employees','employee_locations','loan_status_paid','loan_status_unpaid');
            
            if($security_variable == TRUE){
               
                $pdf = Pdf::loadView('loan.download-all-loan-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Loan details'.'.pdf');
            }
        }catch(\Exception $e){

            $this->create_error_log('Download Loans details pdf', $e);
            return view('errors.500');
        
        }finally{

            ActivityTracker::track_activity('Download Loans details pdf');            
        }

    }

    protected function grant_new_loan(Request $loan_data){
		try{

            $currency = CustomHelper::hcm_system_currency();
            $validator = Validator::make($loan_data->all(), [
                'deduction_amount' => ($loan_data->input('deduction_type') == 1 ) ? 'required|min:1|max:'.$loan_data->input('loan_amount').'|numeric' : '' ,
                'deduction_percentage' => ($loan_data->input('deduction_type') == 2 ) ? 'required|min:1|max:100|numeric' : '' ,
                'loan_other_reason' => ($loan_data->input('loan_type') == 'Other' ) ? 'required' : '' ,
                'loan_approval_date' => 'required|date_format:Y-m-d|before:tomorrow', 
                'deduction_type' => 'required|numeric', 
                'employee_code'  => 'required', 
                'loan_amount' => 'required|numeric', 
                'loan_type' => 'required',
                'deduction_start' => 'required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-m',strtotime('-1 month')).'',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $login_user = $this->get_login_user_detail();

                if($loan_data->input('deduction_type') == 1){
                    $deduction_amount  =  $loan_data->input('deduction_amount');
                }else{
                    $deduction_amount  = $loan_data->input('deduction_percentage');
                }

                $loan_getter = Employees::where('employee_id',$loan_data->input('employee_code'))->get()->toArray();

                $new_loan = new Loan();
                $new_loan->employee_code = $loan_data->input('employee_code');
                $new_loan->loan_given_amount = $loan_data->input('loan_amount');
                $new_loan->loan_submitted_amount = '0';
                $new_loan->loan_remaining_amount = $loan_data->input('loan_amount');
                $new_loan->loan_type = $loan_data->input('loan_type');
                $new_loan->loan_other_reason = $loan_data->input('loan_other_reason');
                $new_loan->loan_deduction_type = $loan_data->input('deduction_type');
                $new_loan->loan_deduction_start = $loan_data->input('deduction_start');
                $new_loan->loan_deduction_amount = $deduction_amount;
                $new_loan->loan_issuance_date = date('d, F Y');
                $new_loan->loan_status = '1';
                $new_loan->last_updated = date('Y-m-d H:i:s');
                $process = $new_loan->save();
                if($process == 1){

                    $new_loan_request = new LoanRequest();
                    $new_loan_request->request_employee_id = $loan_data->input('employee_code');
                    $new_loan_request->loan_request_amount =  $loan_data->input('loan_amount');
                    $new_loan_request->loan_deduction_type = $loan_data->input('deduction_type');
                    $new_loan_request->loan_deduction_start = $loan_data->input('deduction_start');
                    $new_loan_request->loan_deduction_amount = $deduction_amount;
                    $new_loan_request->loan_reason = $loan_data->input('loan_type');
                    $new_loan_request->loan_reason_message = 'This loan request is system generated.';
                    $new_loan_request->request_approved_by_finance = 2;
                    $new_loan_request->request_approved_date_finance = 'Loan approved by ' . $login_user[0]['employee_name'] . ', at ' . date('d F, Y H:i:s');
                    $new_loan_request->request_aprrover_message = 'Loan approved by ' . $login_user[0]['employee_name'] . ', at ' . date('d F, Y H:i:s');
                    $new_loan_request->loan_applied_date = date('Y-m-d H:i:s');
                    $process = $new_loan_request->save();

                    if( $process == 1){
                        $loan_data_request_id_update =  Loan::find($new_loan->loan_id);
                        $loan_data_request_id_update->loan_request_id = $new_loan_request->loan_request_id;
                        $loan_data_request_id_update->save();

                        $this->send_notification('Loan Granted To You', 'Dear '.$loan_getter[0]['employee_name'].', Company granted you a loan on your request of amount '.$loan_data->input('loan_amount').' '.$currency[0]['system_currency']. '.','my-loan?request-id='.$new_loan_request->loan_request_id,$loan_data->input('employee_code'),NULL,NULL,'no');
                        
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Loan granted successfully.",
                        ]);

                    }

                    
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Loan addition failed. Please try again after some time.",
                    ]);
                }
            }    
        }catch(\Exception $e){
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
            ActivityTracker::track_activity('Grant New Loan To Employee');
        }
    }

    protected function loan_detail($loan_id){

        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $loan_detail = Loan::where('loan_id', $loan_id)->get()->toArray();
            
            $image = EmployeeInformation::where('employeeid',$loan_detail[0]['employee_code'])->get()->toArray();

            $all_employees = Employees::all()->toArray();

            $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_id',$loan_detail[0]['employee_code'])->get()->toArray();

            $employee_total_loan = Loan::where('employee_code', $loan_detail[0]['employee_code'])->sum('loan_given_amount');

            $employee_submitted_loan = Loan::where('employee_code', $loan_detail[0]['employee_code'])->sum('loan_submitted_amount');

            $employee_remaining_loan = Loan::where('employee_code', $loan_detail[0]['employee_code'])->sum('loan_remaining_amount');

            $installment_details = LoanInstallment::where('loan_installment_loan_id', $loan_id)->get()->toArray();

            $send_data = compact('title','login_user','employees', 'all_employees', 'loan_detail','installment_details', 'employee_total_loan', 'employee_submitted_loan', 'employee_remaining_loan', 'image');

            return view('loan.loan-detail', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('View Loan Detail Page', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('View Loan Detail Page');
           
        }
    }

    protected function update_loan_details(Request $loan_data){

		try{

            $loans= Loan::where('loan_id', $loan_data->input('loan_id'))->get(['loan_given_amount', 'employee_code'])->toArray();

            $validator = Validator::make($loan_data->all(), [
                'deduction_amount' => ($loan_data->input('deduction_type') == 1 ) ? 'required|min:1|max:'.$loan_data->input('loan_amount').'|numeric' : '' ,
                'deduction_percentage' => ($loan_data->input('deduction_type') == 2 ) ? 'required|min:1|max:100|numeric' : '' ,
                'loan_other_reason' => ($loan_data->input('loan_type') == 'Other' ) ? 'required' : '' ,
                'deduction_type' => 'required|numeric', 
                'loan_amount' => 'required|min:'.$loans[0]['loan_given_amount'].'|max:'.$loans[0]['loan_given_amount'].'|numeric',
                'employee_code' => ["required" , "max:255", 'in: '.$loans[0]['employee_code'].' '],  
                'deduction_start' => 'required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-m',strtotime('-1 month')).'',
                'loan_type' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                if($loan_data->input('deduction_type') == 1){
                    $deduction_amount  =  $loan_data->input('deduction_amount');
                }else{
                    $deduction_amount  = $loan_data->input('deduction_percentage');
                }

                $update_loan = Loan::find($loan_data->input('loan_id'));

                if( $update_loan->loan_submitted_amount == '0'){

                    $update_loan->loan_given_amount = $loan_data->input('loan_amount');
                    $update_loan->loan_type = $loan_data->input('loan_type');
                    $update_loan->loan_other_reason = $loan_data->input('loan_other_reason');
                    $update_loan->loan_deduction_type =$loan_data->input('deduction_type');
                    $update_loan->loan_deduction_start =$loan_data->input('deduction_start');
                    $update_loan->loan_deduction_amount = $deduction_amount;
                    $update_loan->loan_status = '1';
                    $update_loan->last_updated = date('Y-m-d H:i:s');
                    $process = $update_loan->save();
                    if($process == 1){

                        $update_record = [
                            'loan_deduction_type' => $loan_data->input('deduction_type'),
                            'loan_deduction_start' => $loan_data->input('deduction_start'), 
                            'loan_deduction_amount' => $deduction_amount,
                            'loan_reason' => $loan_data->input('loan_type')
                        ];

                        $update_loan_request = LoanRequest::where('request_employee_id',$loan_data->input('employee_code'))
                        ->where('loan_request_amount',$loan_data->input('loan_amount'))
                        ->where('loan_applied_date',$loan_data->input('loan_issuance_date'))
                        ->update($update_record);

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Loan updated successfully",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Loan addition failed. Please try again after some time.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t edit this loan now because your employee has paid its installment.',
                        "autoreload" => "TRUE",
                    ]);
                }    

            }   
        }catch(\Exception $e){
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
            ActivityTracker::track_activity('Update Loan Details');
        }
    }

    protected function pay_installment(Request $installment_data){
		try{
            $currency = CustomHelper::hcm_system_currency();

            $fetch_installment_data= Loan::where('loan_id', $installment_data->input('loan_id'))->get('loan_remaining_amount')->toArray();

            $validator = Validator::make($installment_data->all(), [
               
                'installment_amount' => 'required|numeric', 
                'loan_remaining_amount' => 'required|min:'.$fetch_installment_data[0]['loan_remaining_amount'].'|max:'. $fetch_installment_data[0]['loan_remaining_amount'].'|numeric',
                'loan_installement_source' =>'required',
                'loan_installment_source_remarks' =>'required'
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
               
                $update_installment = Loan::find($installment_data->input('loan_id'));

                $loan_getter = employees::where('employee_id',$update_installment->employee_code)->get();

                $submitted_amount = $update_installment->loan_submitted_amount;
                $paying_amount = $installment_data->input('installment_amount');

                $update_installment->loan_submitted_amount = $submitted_amount + $paying_amount;

                $remaining_amount = $update_installment->loan_remaining_amount;

                $update_installment->loan_remaining_amount = $remaining_amount - $paying_amount;

                if($remaining_amount - $paying_amount == '0'){

                    $update_installment->loan_status = '2';

                }else{
                    $update_installment->loan_status = '1';
                }

                $process = $update_installment->save();

                if($process == 1){
                    
                    $add_installment = new LoanInstallment();
                    $add_installment->loan_installment_amount = $paying_amount;
                    $add_installment->loan_installment_loan_id = $installment_data->input('loan_id');
                    $add_installment->loan_installment_source = $installment_data->input('loan_installement_source');
                    $add_installment->loan_installment_remarks =  $installment_data->input('loan_installment_source_remarks');
                    $add_installment->loan_installment_created_by = session('user_session');
                    $add_installment->loan_installment_date = date('Y-m-d H:i:s');
                    $add_installment->save();

                    $this->send_notification('Loan Return', 'Dear '.$loan_getter[0]['employee_name']. ','.$paying_amount.' '.$currency[0]['system_currency']. ' has been refunded on behalf of your loan. loan Remaining amount is '.$update_installment->loan_remaining_amount.' '.$currency[0]['system_currency'] ,'#',$loan_getter[0]['employee_id'],NULL,NULL,'no');

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Installment added successfully",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Installment addition failed. Please try again after some time.",
                    ]);
                }
            }   
        }catch(\Exception $e){
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
            ActivityTracker::track_activity('Update Loan Installment');
        }

    }

    protected function delete_loan(Request $request_data){
      
        try{
            
            $loan_delete_request = Loan::find($request_data->delete_id);
            if($loan_delete_request->loan_submitted_amount == '0'){
        
                $process = Loan::where('loan_id', $request_data->delete_id)->delete();
                if($process == 1){
                    LoanRequest::where('request_employee_id', $loan_delete_request->employee_code)->where('loan_request_amount', $loan_delete_request->loan_given_amount)->where('loan_applied_date', $loan_delete_request->loan_issuance_date)->delete();
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Loan has been deleted',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Something went wrong. Please try again.',
                    ]);
                }
                     
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'You can\'t delete this loan now because your employee has paid its loan amount.',
                    "autoreload" => "TRUE",
                ]);
            }


        }catch(\Exception $e){
            $this->create_error_log('Delete Loan', $e);
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
            ActivityTracker::track_activity('Delete Loan');
        }
    }

    protected function all_loan_request(Request $request_data){

        try{
            $title = Self::TITLE_GLOBAL;
      
            $login_user = $this->get_login_user_detail();

            $currency = CustomHelper::hcm_system_currency();

            $all_requests = array();
            $employees_data = array();
            $responsed_request = array();

            if($request_data->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();
            
            if($login_user[0]['employee_department'] == 3 || $login_user[0]['employee_department'] == 1){
             
                $where_clause = "1=1 AND employee_isactive = '".$status."'";
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->request_created_date) || !empty($request_data->req_status)){
                    $limit_query = '';
                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND request_employee_id = "'.$request_data->emp_name.'"';
                    }
                    if(!empty($request_data->request_created_date)){
                        $created_date = date("Y-m-d", strtotime($request_data->request_created_date));
                        $where_clause .= ' AND loan_applied_date LIKE "%'.$created_date.'%"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND request_approved_by_finance = "'.$request_data->req_status.'"';
                    }
                }

                $employees_data = Employees::where('employee_isactive','=',$status)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();

                $all_requests = LoanRequest::select('*')
                ->join('tbl_employees', 'tbl_loan_request.request_employee_id', '=', 'tbl_employees.employee_id')
                ->whereRaw($where_clause)
                ->orderBy('loan_request_id', 'DESC')
                ->get()->toArray(); 

                $responsed_request = RequestApproval::where('request_module_id', 5)->pluck('request_id')->toArray();
    
            }
    
            elseif($login_user[0]['employee_department'] != 3 && $login_user[0]['employee_department'] !== 1 && $login_user[0]['employee_is_manager'] == 1){
              
                $login_employee_id = $login_user[0]['employee_id'];
                
                $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id,  $status);
            
                $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
                $where_clause_to = '1=1';
                if(!empty($request_data->employee_location) || !empty($request_data->emp_status) || !empty($request_data->emp_name) || !empty($request_data->req_status)){
                    
                    if(!empty($request_data->employee_location)){
                        $where_clause .= ' AND employee_location = "'.$request_data->employee_location.'"';
                        $where_clause_to   .= ' AND employee_location = "'.$request_data->employee_location.'"'; 
                    }
                    if(!empty($request_data->emp_name)){
                        $where_clause .= ' AND request_employee_id = "'.$request_data->emp_name.'"';
                    }
                    if(!empty($request_data->req_status)){
                        $where_clause .= ' AND request_approved_by_finance = "'.$request_data->req_status.'"';
                    }
                }

                $employees_data = Employees::where('employee_isactive','=',$status)->whereIn('employee_id', $total_team_member)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name','employee_location'])->toArray();
               
                $all_requests = LoanRequest::join('tbl_employees', 'tbl_loan_request.request_employee_id', '=', 'tbl_employees.employee_id')->whereIn('request_employee_id',$total_team_member)->whereRaw($where_clause)->orderBy('loan_request_id','DESC')->get()->toArray();

                $responsed_request = RequestApproval::where('request_module_id', 5)->where('request_approver_id',$login_user[0]['employee_id'])->pluck('request_id')->toArray();

                if(empty($all_requests)){
                 
                    $all_requests = array();
                  
                }
    
            }
          
           $send_data = compact('title','login_user','all_requests','employees_data','employee_locations','responsed_request','currency');
            
           return view('loan.all-loan-request', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View All Loan Request', $e);
            $send_data = compact('title');
            return view('errors.500');
        }

        finally{
            ActivityTracker::track_activity('Access All Loan Request Module');

        }
    

    }

    protected function my_loan(Request $request){

        try{
            $title = Self::MAIN_TITLE2;

            $login_user = $this->get_login_user_detail();

            $all_loans = LoanRequest::where('request_employee_id' , session('user_session'))->orderBy('loan_request_id', 'DESC')->get()->toArray();

            $loans_request_id = array_column($all_loans,'loan_request_id');

            $responsed_request = RequestApproval::where('request_module_id', 5)->whereIn('request_id', $loans_request_id)->pluck('request_id')->toArray();

            $loan_given_amount = LoanRequest::selectRaw('sum(loan_request_amount) as total')->where('request_approved_by_finance', 2)->where('request_employee_id',session('user_session'))->get()->toArray();
          
            $loan_return_amount = Loan::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_loans.employee_code')->join('tbl_loan_installments', 'tbl_loan_installments.loan_installment_loan_id', '=', 'tbl_loans.loan_id')->where('employee_id',session('user_session'))->selectRaw('sum(loan_installment_amount) as total')->get()->toArray();
            
            $loan_remaining_amount = $loan_given_amount[0]['total'] - $loan_return_amount[0]['total'] ;
            
            $send_data = compact('title','login_user','all_loans' ,'loan_given_amount','loan_return_amount','loan_remaining_amount','responsed_request');
            
            return view('loan.my-loans', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('View My Loans Page', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View My Loans Page');
 
        }

    }

    protected function new_loan_request(Request $request_data){

		try{

            $deduction_month = date('m')-1;

            if($deduction_month ==  '-1'){

                $deduction_month = 11;

            }elseif($deduction_month == '0'){

                $deduction_month = 12;

            }else{

                $deduction_month = $deduction_month;
            }

            $message = ['before'=>'The deduction start date must be a date before ' .date( 'F-Y',strtotime('+2 month')),'after'=>'The deduction start date must be a date after '.date( 'F-Y',strtotime(date('Y-'.$deduction_month.'')))];

            $validator = Validator::make($request_data->all(), [
                'loan_amount' => 'required|min:1|max:9999999.99|numeric',
                'deduction_amount' => ($request_data->input('deduction_type') == 1 ) ? 'required|min:1|max:'.$request_data->input('loan_amount').'|numeric' : '' ,
                'deduction_percentage' => ($request_data->input('deduction_type') == 2 ) ? 'required|min:1|max:100|numeric' : '' ,
                'loan_reason' => 'Required',
                'loan_message' => 'Required', 
                'deduction_type' => 'Required', 
                'deduction_start' => 'Required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-'.$deduction_month.'').'',

            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                if($request_data->input('deduction_type') == 1){
                    $deduction_amount  =  $request_data->input('deduction_amount');
                }else{
                    $deduction_amount  = $request_data->input('deduction_percentage');
                }
        
                $login_user = $this->get_login_user_detail();
                $checking_request = LoanRequest::where('request_employee_id', session('user_session'))->where('loan_applied_date', 'like', '%'.date('d-m-Y').'%')->get()->toArray();
                if(empty($checking_request)){
                    $new_loan = new LoanRequest();
                    $new_loan->request_employee_id = $request_data->session()->get('user_session');
                    $new_loan->loan_request_amount =  $request_data->input('loan_amount');
                    $new_loan->loan_deduction_type = $request_data->input('deduction_type');
                    $new_loan->loan_deduction_start = $request_data->input('deduction_start');
                    $new_loan->loan_deduction_amount = $deduction_amount;
                    $new_loan->loan_reason = $request_data->input('loan_reason');
                    $new_loan->loan_reason_message = $request_data->input('loan_message');
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

                        $this->send_notification('New Loan Request',''.$login_user[0]['employee_name'].' applied for loan. Please click here to view.','all-loan-request?request-id='.  $new_loan->loan_request_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');
                        
                        $this->send_module_email(1,'New Loan Request','<b>'.$login_user[0]['employee_name'].'</b> applied for loan request.',''.url('/').'/all-loan-request?request-id='.  $new_loan->loan_request_id);
                        $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Loan Request','You\'ve successfully applied loan request and has been forwarded to your reporting authorities.');

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Loan request has been submitted. Please wait for response',
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Something went wrong. Please try again.',
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You have already submitted a request for '.date('d-m-Y').'.',
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log('Apply For Loan Request', $e);
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
            ActivityTracker::track_activity('Access Request for Loan Request Module');
        }
    }

    protected function loan_request_approval(Request $request){

        try{

            if($request['status'] == "Approve"){

                $process = '';

                $checking_loan_amout = LoanRequest::where('loan_request_id', '=', $request->input('ids'))->get(['loan_request_amount','loan_deduction_type'])->toArray();

                if(empty($checking_loan_amout)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>"Loan request dosen't exits.",
                        
                    ]);
                }
                
                $validator = Validator::make($request->all(), [
                    'loan_amount' => 'required|min:1|max:'.$checking_loan_amout[0]['loan_request_amount'].'|numeric',
                    'deduction_amount' => ($request->input('deduction_type') == 1 ) ? 'required|min:1|max:'.($request->input('loan_amount') ?? 0).'|numeric' : '' ,
                    'deduction_percentage' => ($request->input('deduction_type') == 2 ) ? 'required|min:1|max:100|numeric' : '' ,
                    'deduction_type' => 'required|in:'.$checking_loan_amout[0]['loan_deduction_type'].' ', 
                    'approvermsg' => 'required',
                ]);
    
                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return response()->json([
                            'msg' => $error,
                            'status' => 'FALSE'
                        ]);
                    }
                }else {

                    $login_user = $this->get_login_user_detail();
                    $get_loan = LoanRequest::find($request->input('ids'));
                
                    if($request->input('deduction_type') == 1){
                        $deduction_amount  =  $request->input('deduction_amount');
                    }else{
                        $deduction_amount  = $request->input('deduction_percentage');
                    }
        
                    if(!empty($get_loan)){
                        
                        if($get_loan->request_approved_by_finance == 1){
                            $get_loan->request_aprrover_message = $request->input('approvermsg');
                            $get_loan->loan_deduction_type = $request->input('deduction_type');
                            $get_loan->loan_request_amount = $request->input('loan_amount');
                            $get_loan->loan_deduction_amount = $deduction_amount;
                            $get_loan->request_approved_by_finance = 2;
                            $get_loan->request_approved_date_finance = 'Approved By : '.$login_user[0]['employee_name'].', at : ' .date('h:i A d M, Y');
                            $process =  $get_loan->save();
                        
                            if($process == 1){
                    
                                $this->send_notification('Loan Request Approval', 'Finance approved your loan request Please click here to view.','my-loan?request-id='. $get_loan->loan_request_id .'',$get_loan->request_employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($get_loan['request_employee_id'], 'Loan Request','Your loan request has been approved by Finance.');

                                $this->create_loan_record($get_loan->request_employee_id ,$get_loan->loan_request_id, $get_loan->loan_request_amount , $get_loan->loan_reason, $get_loan->loan_deduction_type, $get_loan->loan_deduction_amount, $get_loan->loan_deduction_start);
            
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not aprroved successfully try again",
                                    
                                ]);
                            }
                        }   
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Request not found on this id.',
                        ]);
                    }
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' =>"Request aprroved successfully",
                            
                        ]);
                    }
                    else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' =>"Already responded to this request",
                            
                        ]);
                    }
                }   
            }    

            if($request['status'] == "DisApprove"){

                $validator = Validator::make($request->all(), [
                    'disapprovermsg' => 'required',
                ]);

                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return response()->json([
                            'msg' => $error,
                            'status' => 'FALSE'
                        ]);
                    }
                }else {

                    $process = '';
                    $login_user = $this->get_login_user_detail();
                    $get_loan = LoanRequest::find($request->input('ids'));
        
                    if(!empty($get_loan)){
                        
                        if($get_loan->request_approved_by_finance == 1){
                            $get_loan->request_aprrover_message = $request->input('disapprovermsg');
                            $get_loan->request_approved_by_finance = 3;
                            $get_loan->request_approved_date_finance = 'Dis-Approved By : '.$login_user[0]['employee_name'].', at : ' .date('h:i A d M, Y');
                            $process =  $get_loan->save();
                            if($process == 1){
            
                                $this->send_notification('Loan Request Disapproval',  'Finance disapproved your loan request. Please click here to view.','my-loan?request-id='. $get_loan->loan_request_id .'',$get_loan->request_employee_id,NULL,NULL,'no');

                                $this->send_push_notification_mobile_app($get_loan['request_employee_id'], 'Loan Request','Your loan request has been disapproved by Finance.');
        
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' =>"Request not disapproved successfully try again",
                                    
                                ]);
                            }
                        }
                        if($process == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' =>"Request Disaprroved successfully",
                                
                            ]);   
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>"Already responded to this request",
                                
                            ]);
                        }  
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Loan request dosen't exits.",
                        ]);
                    } 
                }   
            }    

            if($request['status'] == "LeadApprove"){
                $process = '';
                $login_user = $this->get_login_user_detail();
                $approval_maker = session('user_session');
                $update_request = LoanRequest::find($request->input('ids'));
                $status = 1;

                $checking_request = RequestApproval::where('request_module_id', 5)->where('request_approver_id',session('user_session'))->where('request_id',$request->input('ids'))->get()->toArray();
                
                if(empty($checking_request)){

                    $update_request = LoanRequest::find($request->input('ids'));

                    if(!empty($update_request) && $update_request->request_approved_by_finance == 1 && in_array($update_request->request_employee_id,$this->get_employee_hierarchy_unique($approval_maker,  $status))){

                        $loan_request = new RequestApproval();
                        $loan_request->request_module_id = 5;
                        $loan_request->request_id = $request->input('ids');
                        $loan_request->request_approver_id =  $approval_maker;
                        $loan_request->request_approver_status = 2;
                        $loan_request->request_approver_msg = $request['approvermsg'];
                        $loan_request->request_approval_time = date('h:i A d M, Y');
                        $process = $loan_request->save();
                        
                        if($process == 1){

                            $employee_name = Employees::where('employee_id', $update_request['request_employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

                            $notification_msg  = 'Dear employee, '. $login_user[0]['employee_name'].' marked your Loan request(s) as Approve. Please click here to view.';

                            $this->send_notification('Loan Request Approval',$notification_msg,'my-loan?request-id='. $update_request->loan_request_id .'', $update_request['request_employee_id'],NULL,NULL,'no');

                            $this->send_push_notification_mobile_app($update_request['request_employee_id'], 'Loan Request','Your loan request been approved by your line manager.');

                            $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Approved their Loan request(s). Please click here to view.';

                            $this->send_notification('Loan Request Approval ',$notification_msg_hr, 'all-loan-request?request-id='.$update_request->loan_request_id .'',NULL,NULL,NULL,'yes');

                            return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Request approved successfully",
                            
                            ]);

                        }else{
                
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>"Request not approved successfully ",
                                
                            ]);
                        }   
        
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' =>"Already responded to this request.",
                            
                        ]);
                    }
                        
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Already responded to this request",
                        
                    ]);
                }     
            }

            if($request['status'] == "LeadDisApprove"){
                $process = '';
                $login_user = $this->get_login_user_detail();
                $approval_maker = session('user_session');
                $update_request = LoanRequest::find($request->input('ids'));
                $status = 1;

                $checking_request = RequestApproval::where('request_module_id', 5)->where('request_approver_id',session('user_session'))->where('request_id',$request->input('ids'))->get()->toArray();

                if(empty($checking_request)){

                    $update_request = LoanRequest::find($request->input('ids'));

                    if(!empty($update_request) && $update_request->request_approved_by_finance == 1 && in_array($update_request->request_employee_id,$this->get_employee_hierarchy_unique($approval_maker,  $status))){

                        $loan_request = new RequestApproval();
                        $loan_request->request_module_id = 5;
                        $loan_request->request_id = $request->input('ids');
                        $loan_request->request_approver_id =  $approval_maker;
                        $loan_request->request_approver_status = 3;
                        $loan_request->request_approver_msg = $request['disapprovermsg'];
                        $loan_request->request_approval_time = date('h:i A d M, Y');
                        $process = $loan_request->save();
                        
                        if($process == 1){

                            $employee_name = Employees::where('employee_id', $update_request['request_employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

                            $notification_msg  = 'Dear employee, '. $login_user[0]['employee_name'].' marked your Loan request(s) as Disapproved. Please click here to view.';

                            $this->send_notification('Loan Request Disapproval',$notification_msg, 'my-loan?request-id='. $update_request->loan_request_id .'', $update_request['request_employee_id'],NULL,NULL,'no');
                            
                            $this->send_push_notification_mobile_app($update_request['request_employee_id'], 'Loan Request','Your loan request has been disapproved by your line manager.');

                            $notification_msg_hr = ''.$employee_name[0]['employee_name'].''."'s". ' Manager Disapproved their Loan request(s). Please click here to view.';

                            $this->send_notification('Loan Request Disapproval ',$notification_msg_hr, 'all-loan-request?request-id='.$update_request->loan_request_id .'',NULL,NULL,NULL,'yes');

                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Request disapproved successfully",
                                
                            ]);

                        }else{
                
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' =>"Request not disapproved successfully ",
                                
                            ]);
                        }   
        
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' =>"Already responded to this request.",
                            
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Already responded to this request",
                        
                    ]);
                }  
            }

        }catch(\Exception $e){
            $this->create_error_log('Approved And Disapproved Loan Request by Manager', $e);
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
            ActivityTracker::track_activity('Approved And Disapproved Loan Request by Manager');
            
        }
       
    }

    protected function create_loan_record($employee_code ,$loan_request_id, $loan_amount , $loan_type, $loan_deduction_type, $loan_deduction_amount, $loan_deduction_start){
        $new_loan = new Loan();
        $new_loan->employee_code = $employee_code;
        $new_loan->loan_request_id = $loan_request_id;
        $new_loan->loan_given_amount = $loan_amount;
        $new_loan->loan_submitted_amount = '0';
        $new_loan->loan_remaining_amount = $loan_amount;
        $new_loan->loan_type = $loan_type;
        $new_loan->loan_other_reason = NULL;
        $new_loan->loan_issuance_date = date('Y-m-d H:i:s');
        $new_loan->loan_status = '1';
        $new_loan->last_updated = date('Y-m-d H:i:s');
        $new_loan->loan_deduction_type = $loan_deduction_type;
        $new_loan->loan_deduction_amount = $loan_deduction_amount;
        $new_loan->loan_deduction_start = $loan_deduction_start;
        $new_loan->save();
    }

    protected function update_loan_request(Request $request_data){

        try{

            if($this->check_request_validation($request_data->request_id,5) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Loan request id is invalid',
                ]);
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

            $validator = Validator::make($request_data->all(), [
                'loan_amount' => 'required|min:1|max:9999999.99|numeric',
                'deduction_amount' => ($request_data->input('deduction_type') == 1 ) ? 'required|min:1|max:'.$request_data->input('loan_amount').'|numeric' : '' ,
                'deduction_percentage' => ($request_data->input('deduction_type') == 2 ) ? 'required|min:1|max:100|numeric' : '' ,
                'loan_reason' => 'Required',
                'loan_message' => 'Required', 
                'deduction_type' => 'Required', 
                'deduction_start' => 'Required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-'.$deduction_month.'').'',
            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $login_user = $this->get_login_user_detail();

                $loan_update_request = LoanRequest::find($request_data->request_id);

                if(!empty($loan_update_request)){

                    $checking_request_responsed = RequestApproval::where('request_module_id',5)->where('request_id',$request_data->request_id)->get()->toArray();
                
                    if(empty($checking_request_responsed) && $loan_update_request->request_approved_by_finance == 1){   

                        $checking_request = LoanRequest::where('request_employee_id', session('user_session'))->where('loan_applied_date', 'like', '%'.date('d-m-Y').'%')->where('loan_request_id','!=',$request_data->request_id)->get()->toArray();

                        if(empty($checking_request)){

                            if($request_data->input('deduction_type') == 1){
                                $deduction_amount  =  $request_data->input('deduction_amount');
                            }else{
                                $deduction_amount  = $request_data->input('deduction_percentage');
                            }
                        
                            $loan_update_request->loan_request_amount =  $request_data->input('loan_amount');
                            $loan_update_request->loan_deduction_type = $request_data->input('deduction_type');
                            $loan_update_request->loan_deduction_start = $request_data->input('deduction_start');
                            $loan_update_request->loan_deduction_amount = $deduction_amount;
                            $loan_update_request->loan_reason = $request_data->input('loan_reason');
                            $loan_update_request->loan_reason_message = $request_data->input('loan_message');
                            $loan_update_request->loan_applied_date = date('Y-m-d H:i:s');
                        
                            $process =  $loan_update_request->save();

                            if( $process == 1){
                                if(!empty($login_user[0]['employee_senior_manager']) || $login_user[0]['employee_senior_manager'] != ''){
                                    $to_mngr1  = $login_user[0]['employee_senior_manager'];
                                }
                                if(!empty($login_user[0]['employee_junior_manager']) || $login_user[0]['employee_junior_manager'] != ''){
                                    $to_mngr2 = $login_user[0]['employee_junior_manager'];
                                }
                
                                $this->send_notification('Update Loan Request',''.$login_user[0]['employee_name'].' Updated a loan Request. Please click here to view.','all-loan-request?request-id='.$loan_update_request->loan_request_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes');
                                $this->send_push_notification_mobile_app($login_user[0]['employee_id'], 'Update Loan Request','your loan request has been updated.');

                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => 'Loan request has been updated. Please wait for response',
                                ]);
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => 'Something went wrong. Please try again.',
                                ]);
                            }
                            
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'You have already submitted a request for'.date('d-m-Y').'.',
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'You can\'t edit this loan now because your reporting authority has changed its status.',
                            "autoreload" => "TRUE",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Request not found on this id.',
                    ]);
                }     
            }
        }catch(\Exception $e){
            $this->create_error_log('Update Loan Request', $e);
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
            ActivityTracker::track_activity('Update Loan Request');
        }
    }

    protected function delete_loan_request(Request $request_data){
      
        try{
            
            if($this->check_request_validation($request_data->delete_id,5) == TRUE){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Loan request id is invalid',
                ]);
            }

            $loan_delete_request = LoanRequest::find($request_data->delete_id);

            if(!empty($loan_delete_request)){

                $checking_request_responsed = RequestApproval::where('request_module_id',5)->where('request_id',$request_data->delete_id)->get()->toArray(); 

                if(empty($checking_request_responsed) && $loan_delete_request->request_approved_by_finance == 1){    
                    
                    $process = LoanRequest::where('loan_request_id', $request_data->delete_id)->delete();
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Loan request has been deleted',
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Something went wrong. Please try again.',
                        ]);
                    }
                        
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'You can\'t delete this loan now because your reporting authority has changed its status.',
                        "autoreload" => "TRUE",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Request not found on this id.',
                ]);
            }         
        }catch(\Exception $e){
            $this->create_error_log('Delete Loan Request', $e);
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
            ActivityTracker::track_activity('Delete Loan Request');
        }
    }

    protected function my_loan_request_detail(Request $request_data){

        try{

            if($this->check_request_validation($request_data['request_id'],5) == TRUE){
               
                return response()->json([
                    'error_status' => 'FALSE',
                    'msg' => 'Loan request id is invalid',
                ]);
            }
           
           $loan_detail  = Loan::where('loan_request_id',$request_data['request_id'])->get(['loan_id','loan_given_amount','loan_remaining_amount','loan_submitted_amount'])->toArray();
        
           if(!empty($loan_detail)){

            $loan_installment = LoanInstallment::where('loan_installment_loan_id',$loan_detail[0]['loan_id'])->get()->toArray();
            
            return response()->json([
                'status' => 'TRUE',
                'data'  => $loan_detail,
                'loan_installment' =>  $loan_installment,
            ]);
        }else{
            return response()->json([
                'status' => 'FALSE',
            ]);
        }

         
        }catch(\Exception $e){
            $this->create_error_log('My Loan Request Detail', $e);
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
            ActivityTracker::track_activity('My Loan Request Detail');
        }
    }

    protected function get_loan_installement_source($loan_installement_id){
        try{

            $loan_installement_source = Employees::join('tbl_loan_installments','tbl_loan_installments.loan_installment_created_by','=', 'tbl_employees.employee_id')->where('loan_installment_id',$loan_installement_id)->get(['loan_installment_source','loan_installment_remarks','employee_name','loan_installment_date'])->toArray();

            if(!empty($loan_installement_source)){
                return response()->json([
                    'status' => "TRUE", 
                    'data' =>  $loan_installement_source,
                ]);
            }else{
                return response()->json([
                    'status' => "FALSE", 
                    
                ]);
            }

        }catch(\Exception $e){
            $this->create_error_log('Loan Installement Source Modal Access', $e);
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
            ActivityTracker::track_activity('Loan Installement Source Modal Access');
        }
      
    }


}
