<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use App\Models\{PettyCash, ExpenseSheet};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Barryvdh\DomPDF\Facade\Pdf;

class PettyCashController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "cash";

    protected function index(Request $request){
        try{
            $title = Self::MAIN_TITLE;
        
            $login_user = $this->get_login_user_detail();
            
            $where_clause = '1=1 ';
            $where_clause_to = '1=1 ';

            if (!empty($request->expense_period)) {
                $expense_period = date("Y-m", strtotime($request->expense_period));
                $where_clause .= " AND CONVERT(VARCHAR, petty_cash_for_month_and_year, 120) LIKE '%" . $expense_period . "%'";
            } else {
                $where_clause .= " AND CONVERT(VARCHAR, petty_cash_for_month_and_year, 120) LIKE '%" . date("Y-m") . "%'";
            }
            

            if (!empty($request->expense_period)) {
                $expense_period = date("Y-m", strtotime($request->expense_period));
                $where_clause_to .= " AND CONVERT(VARCHAR, expense_date, 120) LIKE '%" . $expense_period . "%'";
            } else {
                $where_clause_to .= " AND CONVERT(VARCHAR, expense_date, 120) LIKE '%" . date("Y-m") . "%'";
            }

            $all_cash_history = PettyCash::whereRaw($where_clause)->get()->toArray();

            $total_cash = PettyCash::whereRaw($where_clause)->get()->sum('petty_cash_amount');
            
            $total_expenses = ExpenseSheet::whereRaw($where_clause_to)->get()->sum('expense_amount');

            $cash_remaining = $total_cash - $total_expenses;

            $send_data = compact('title','login_user','all_cash_history', 'total_cash','total_expenses' ,'cash_remaining');

            return view('cash.cash', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View All Cash', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View All Cash');

        }
    }

    protected function download_pettycash(Request $request){
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
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/petty-cash';</script>";
            }
            
            $where_clause = '1=1 ';
            $where_clause_to = '1=1 ';

            if(!empty($request->expense_period)){
                $expense_period = date("Y-m", strtotime($request->expense_period));
                $where_clause .= 'AND petty_cash_for_month_and_year LIKE  "%'.$expense_period.'%"';    
            }else{
                $where_clause .= 'AND petty_cash_for_month_and_year LIKE  "%'.date("Y-m").'%"'; 
            }
            if(!empty($request->expense_period)){
                $expense_period = date("Y-m", strtotime($request->expense_period));
                $where_clause_to .= 'AND expense_date LIKE  "%'.$expense_period.'%"';    
            }else{
                $where_clause_to .= 'AND expense_date LIKE  "%'.date("Y-m").'%"'; 
            }

            $all_cash_history = PettyCash::join('tbl_employees','tbl_petty_cash.petty_cash_created_by','tbl_employees.employee_id')->whereRaw($where_clause)->get(['employee_id','employee_name','employee_code','petty_cash_id','petty_cash_amount','petty_cash_for_month_and_year','petty_cash_source','petty_cash_source_attachment','petty_cash_created_at','petty_cash_created_by'])->toArray();

            $total_cash = PettyCash::whereRaw($where_clause)->get()->sum('petty_cash_amount');
            
            $total_expenses = ExpenseSheet::whereRaw($where_clause_to)->get()->sum('expense_amount');

            $cash_remaining = $total_cash - $total_expenses;

            $send_data = compact('login_user','all_cash_history', 'total_cash','total_expenses' ,'cash_remaining');

            if($security_variable == TRUE){
               
                $pdf = Pdf::loadView('cash.download-cash-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Office Cash Report'.'.pdf');
            }
        }catch(\Exception $e){
            $this->create_error_log('Download All Cash Pdf', $e);
            return view('errors.500');
            
        }finally{
            ActivityTracker::track_activity('Download All Cash Pdf'); 
            
        }
    }

    protected function add_cash(Request $request){

       
        try{

            $filename = '';

            if($request->hasFile('petty_cash_source_attachment')){
                $filename = $request->file('petty_cash_source_attachment')->getClientOriginalExtension();
            }
        
            $validator = Validator::make($request->all(), [
                'cash_amount' => 'required|min:1|max:99999999.99|numeric',
                'cash_date' => 'required|date_format:Y-m',
                'cash_source' => 'required',
                'petty_cash_source_attachment' => ['max:10000', ($filename == 'txt' || $filename == 'csv' ) ? 
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
            }else {

                $employee_petty_cash = PettyCash::orderBy('petty_cash_id','DESC')->limit(1)->get(['petty_cash_id'])->toArray();
                if(empty($employee_petty_cash)){
                    $petty_cash_id = '1';
                }else{
                    $petty_cash_id = ++$employee_petty_cash[0]['petty_cash_id'];
                }

                $new_cash = new PettyCash();
                $new_cash->petty_cash_amount = $request->input('cash_amount');
                $new_cash->petty_cash_for_month_and_year = $request->input('cash_date');
                $new_cash->petty_cash_source = $request->input('cash_source');
                $new_cash->petty_cash_created_by = session('user_session');
                $new_cash->petty_cash_created_at = date('Y-m-d H:i:s');
                
                if($request->hasFile('petty_cash_source_attachment')){

                    $fileNameToStore = $petty_cash_id.$request->file('petty_cash_source_attachment')->getClientOriginalName();
                    
                    $request->file('petty_cash_source_attachment')->storeAs('portal_documents/cash/',$fileNameToStore);
        
                    $new_cash->petty_cash_source_attachment = $fileNameToStore;
        
                }
                
                $process = $new_cash->save();   
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Cash added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Cash addition failed. Please try again after some time.",
                    ]);
                }
            }  
        }catch(\Exception $e){
            $this->create_error_log("Add Cash", $e);
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
            ActivityTracker::track_activity('Add Cash'); 
        }

    }

    protected function update_cash(Request $request){
       
        try{

            $filename = '';

            if($request->hasFile('petty_cash_source_attachment')){
                $filename = $request->file('petty_cash_source_attachment')->getClientOriginalExtension();
            }
    
            $validator = Validator::make($request->all(), [
                'cash_amount' => 'required|min:1|max:99999999.99|numeric',
                'cash_date' => 'required|date_format:Y-m',
                'cash_source' => 'required',
                'petty_cash_source_attachment' => ['max:10000', ($filename == 'txt' || $filename == 'csv' ) ? 
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
            }else {

            $update_cash = PettyCash::find($request->input('petty_cash_id'));
            $update_cash->petty_cash_amount = $request->input('cash_amount');
            $update_cash->petty_cash_for_month_and_year = $request->input('cash_date');
            $update_cash->petty_cash_source = $request->input('cash_source');
            $update_cash->petty_cash_created_at = date('Y-m-d H:i:s');
            
                if($request->hasFile('petty_cash_source_attachment')){

                    if(!empty($update_cash->petty_cash_source_attachment)){ 

                        if (file_exists(storage_path('app/portal_documents/cash/'.$update_cash->petty_cash_source_attachment))) {
                    
                            unlink(storage_path('app/portal_documents/cash/'.$update_cash->petty_cash_source_attachment));
                        }
                    }    

                    $fileNameToStore = $request->input('petty_cash_id').$request->file('petty_cash_source_attachment')->getClientOriginalName();
                    
                    $request->file('petty_cash_source_attachment')->storeAs('portal_documents/cash/',$fileNameToStore);
        
                    $update_cash->petty_cash_source_attachment = $fileNameToStore;
        
                }
                
                $process = $update_cash->save();   
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Cash updated successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Cash update failed. Please try again after some time.",
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log("Update Cash", $e);
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
            ActivityTracker::track_activity('Update Cash'); 
        }

    }

    protected function download_cash_file(Request $request){
       
        try{
            if(!empty($request->path)){
                
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
               
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
                return response()->download(storage_path('app/portal_documents/'.$request->path));
            }else{
                return redirect(url());
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
