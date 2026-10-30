<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Crypt, Validator};
use Illuminate\Validation\Rules\File;
use App\Models\{ExpenseSheet};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseSheetController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "expense";

    protected function index(Request $request){
        try{
            $title = Self::MAIN_TITLE;
        
            $login_user = $this->get_login_user_detail();
                
            $where_clause = '1=1 ';

            if (!empty($request->expense_period)) {
                $expense_period = date("Y-m", strtotime($request->expense_period));
                $where_clause .= " AND DATE_FORMAT(expense_date, '%Y-%m-%d %H:%i:%s') LIKE '%" . $expense_period . "%'";
            } else {
                $where_clause .= " AND DATE_FORMAT(expense_date, '%Y-%m-%d %H:%i:%s') LIKE '%" . date("Y-m") . "%'";
            }
            
            $expenses = ExpenseSheet::whereRaw($where_clause)->get()->toArray();
        
            $expenses2 = ExpenseSheet::whereRaw($where_clause)->get();
            
            $total_expense = $expenses2->sum('expense_amount');

            $send_data = compact('title','login_user','expenses','total_expense');

            return view('cash.expense', $send_data);
        }catch(\Exception $e){
            $this->create_error_log('View All Expenses', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View All Expenses');
          
        }
    }

    protected function download_expense(Request $request){
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
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/daily-expense';</script>";
            }
                
            $where_clause = '1=1 ';
            if (!empty($request->expense_period)) {
                $expense_period = date("Y-m", strtotime($request->expense_period));
                $where_clause .= " AND DATE_FORMAT(expense_date, '%Y-%m-%d %H:%i:%s') LIKE '%" . $expense_period . "%'";
            } else {
                $where_clause .= " AND DATE_FORMAT(expense_date, '%Y-%m-%d %H:%i:%s') LIKE '%" . date("Y-m") . "%'";
            }
            
            $expenses = ExpenseSheet::join('tbl_employees','tbl_expense_sheet.expense_created_by','tbl_employees.employee_id')->whereRaw($where_clause)->get(['employee_id','employee_name','employee_code','expense_id','expense_date','expense_reason','expense_source','expense_attachment','expense_amount', 'expense_created_by', 'expense_created_at'])->toArray();
            $expenses2 = ExpenseSheet::whereRaw($where_clause)->get();
            
            $total_expense = $expenses2->sum('expense_amount');

            $send_data = compact('login_user','expenses','total_expense');
           
            if($security_variable == TRUE){
               
                $pdf = Pdf::loadView('cash.download-expense-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Office Expense Report'.'.pdf');
            }
        }catch(\Exception $e){
            $this->create_error_log('download All Expenses pdf', $e);
            return view('errors.500');
            
        }finally{
            ActivityTracker::track_activity('download All Expenses pdf');
            
        }
    }

    protected function add_expense(Request $request){

        try{

            $filename = '';

            if($request->hasFile('expense_attachment')){
                $filename = $request->file('expense_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->all(), [
            
                'expense_date' => 'required|date_format:Y-m-d',
                'expense_reason' => 'required',
                'expense_source' => 'required',
                'expense_amount' => 'required|min:1|max:99999999.99|numeric',
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
            }else {

                $add_expense_sheet_locked = ExpenseSheet::whereYear('expense_date', date('Y', strtotime($request->input('expense_date'))))
                ->whereMonth('expense_date', date('m', strtotime($request->input('expense_date'))))
                ->where('expense_locked', 1)->get()->toArray();

                if(empty($add_expense_sheet_locked)){

                    $all_employee_expenses = ExpenseSheet::orderBy('expense_id','DESC')->limit(1)->get(['expense_id'])->toArray();
                    if(empty($all_employee_expenses)){
                        $employee_expenses = '1';
                    }else{
                        $employee_expenses = ++$all_employee_expenses[0]['expense_id'];
                    }

                    $new_expense = new ExpenseSheet();
                    $new_expense->expense_date = $request->input('expense_date');
                    $new_expense->expense_reason = $request->input('expense_reason');
                    $new_expense->expense_amount = $request->input('expense_amount');
                    $new_expense->expense_source = $request->input('expense_source');
                    $new_expense->expense_created_by = session('user_session');
                    $new_expense->expense_created_at = date('Y-m-d H:i:s');

                    if($request->hasFile('expense_attachment')){

                        $fileNameToStore = $employee_expenses.$request->file('expense_attachment')->getClientOriginalName();
                        
                        $request->file('expense_attachment')->storeAs('portal_documents/expenses/',$fileNameToStore);
            
                        $new_expense->expense_attachment = $fileNameToStore;
            
                    }

                    $process = $new_expense->save();

                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Expense added successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Expense addition failed. Please try again after some time.",
                        ]);
                    }
                }else{

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You can't add expense for this month because expense sheet is locked.",
                    ]);
                }    
            }
        }catch(\Exception $e){

            $this->create_error_log("Add Expense", $e);
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

            ActivityTracker::track_activity('Add Expense'); 

        }

    }

    protected function update_expense(Request $request){
        
        try{

            $filename = '';

            if($request->hasFile('expense_attachment')){
                $filename = $request->file('expense_attachment')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->all(), [
            
                'expense_date' => 'required|date_format:Y-m-d',
                'expense_reason' => 'required',
                'expense_source' => 'required',
                'expense_amount' => 'required|min:1|max:99999999.99|numeric',
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
            }else {
 
                $update_expense_sheet_locked = ExpenseSheet::where('expense_locked', 1)->where('expense_id',$request->input('expense_id'))->get('expense_id')->toArray();
    
                if(empty($update_expense_sheet_locked)){
            
                    $update_expense = ExpenseSheet::find($request->input('expense_id'));
                    $update_expense->expense_date = $request->input('expense_date');
                    $update_expense->expense_reason = $request->input('expense_reason');
                    $update_expense->expense_amount = $request->input('expense_amount');
                    $update_expense->expense_source = $request->input('expense_source');
                    $update_expense->expense_created_at = date('Y-m-d H:i:s');

                    if($request->hasFile('expense_attachment')){

                        if(!empty($update_expense->expense_attachment)){          
                            if (file_exists(storage_path('app/portal_documents/expenses/'.$update_expense->expense_attachment))) {
                                
                                unlink(storage_path('app/portal_documents/expenses/'.$update_expense->expense_attachment));
                            }
                        }   

                        $fileNameToStore = $request->input('expense_id').$request->file('expense_attachment')->getClientOriginalName();
                        
                        $request->file('expense_attachment')->storeAs('portal_documents/expenses/',$fileNameToStore);

                        $update_expense->expense_attachment = $fileNameToStore;

                    }

                    $process = $update_expense->save();        
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Expense updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Expense update failed. Please try again after some time.",
                        ]);
                    }
                }else{

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You can't update expense for this month because expense sheet is locked.",
                    ]);
                }        
            }
        }catch(\Exception $e){
            $this->create_error_log("Update Expense", $e);
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
            ActivityTracker::track_activity('Update Expense'); 
        }

    }

    protected function download_expense_file(Request $request){
       
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
                return redirect('/');
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
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
            ActivityTracker::track_activity($request->title);
        }
    }

    protected function lock_expense(Request $request){
      
        try{

            $validator = Validator::make($request->all(), [
            
                'expense_date' => 'required|date_format:Y-m|before:'. date('Y-m',strtotime('+1 month')).'',
                
            ]);
                
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $expenses_lock_sheet = ExpenseSheet::whereYear('expense_date', date('Y', strtotime($request->input('expense_date'))))
                    ->whereMonth('expense_date', date('m', strtotime($request->input('expense_date'))))
                    ->where('expense_locked',1)
                    ->get('expense_locked')->toArray();
                
                if(!empty($expenses_lock_sheet)){

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Expense sheet already locked',
                    ]);
                   
                }else{

                    $expenses_lock_sheet = ExpenseSheet::whereYear('expense_date', date('Y', strtotime($request->input('expense_date'))))
                    ->whereMonth('expense_date', date('m', strtotime($request->input('expense_date'))))
                    ->update([
                        'expense_locked' => 1,
                        'expense_locked_created_by' => session('user_session')
                    ]);

                    if($expenses_lock_sheet > 0){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Expense sheet locked successfully for the month of '.date('F, Y',strtotime($request->input('expense_date'))).'.',
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "No expense to lock for this period.",
                        ]);
                    }

                   

                }

            }
        }catch(\Exception $e){

            $this->create_error_log("Expense Month Locked", $e);
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

            ActivityTracker::track_activity('Expense Month Locked'); 

        }

    }
}
