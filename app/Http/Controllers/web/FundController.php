<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Crypt, Validator};
use App\Models\{Fund, EmployeeFund};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;

class FundController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = 'manage-funds';
    const TITLE_GLOBAL2 = 'employee_funds';

    protected function index(){

        try{
            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $funds = Fund::get()->toArray();
            
            $send_data = compact('title' , 'login_user', 'funds');

            return view('funds.fund')->with($send_data);
        
        }catch(\Exception $e){

            $this->create_error_log('View All Funds', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Funds');
         
        }
    }

    protected function add_fund(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'fund_name' => ['required','unique:tbl_funds,fund_name'],
                
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $new_fund = new Fund();
                $new_fund->fund_name = $request->input('fund_name');
                $new_fund->fund_created_by = session('user_session');
                $new_fund->fund_created_at = date('d-m-Y H:i:s');
                $process = $new_fund->save();   
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Fund added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Fund addition failed. Please try again after some time.",
                    ]);
                }
            }   
             
        }catch(\Exception $e){
            $this->create_error_log("Add Fund", $e);
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
            ActivityTracker::track_activity('Add Fund'); 
        }

    }

    protected function update_fund(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'fund_name' =>  ['required','unique:tbl_funds,fund_name, '.$request->input('fund_id'). ',fund_id'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $update_fund = Fund::find($request->input('fund_id'));

                if(!empty($update_fund)){

                    $update_fund->fund_name = $request->input('fund_name');
                    $update_fund->fund_created_by = session('user_session');
                    $process = $update_fund->save();   
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Fund updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Fund updation failed. Please try again after some time.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Fund not found on this id.',
                    ]);
                }      
            }   
             
        }catch(\Exception $e){
            $this->create_error_log("Update Fund", $e);
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
            ActivityTracker::track_activity('Update Fund'); 
        }

    }

    protected function delete_fund(Request $request){
      
        try{
            
            $delete_fund = Fund::find($request->fund_id);

            if(!empty($delete_fund)){
            
                Fund::where('fund_id', $request->fund_id)->delete();
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'Fund has been deleted',
                ]);
    
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Fund not found on this id.',
                ]);
            }         
        }catch(\Exception $e){
            $this->create_error_log('Delete Fund', $e);
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
            
        }

        finally{
            ActivityTracker::track_activity('Delete Fund');
            
        }
    }

    protected function employee_fund(){
        try{
            $title = Self::TITLE_GLOBAL2;
            $login_user = $this->get_login_user_detail();
        
            $employee_funds = Fund::get()->toArray();

            $send_data = compact('title','login_user','employee_funds');
            return view('funds.employee-fund', $send_data);
        }catch(\Exception $e){
            $this->create_error_log('Employee Funds Fetch', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('Employee Funds');
           
        }
    }
        protected function get_employee_fund_data(Request $request){
            try{
              
                $get_fund_source = $request->input('fund_id');
                
                $validator = Validator::make($request->all(), [
                    'fund_id' => 'required',
                
                ]);
    
                if($validator->fails()) {
                    foreach($validator->errors()->all() as $error){
                        return Redirect::back()->with('error', $error);
                    }
                }
                if(!empty($get_fund_source)){
                   
                    $get_fund_source_data = EmployeeFund::selectRaw('CONVERT(CHAR(10), [ef_created_at], 112) as ef_created_at, fund_id, ef_id, employee_code, employee_name, fund_name, ef_amount')
                    ->join('tbl_funds', 'tbl_employees_funds.ef_fund_id', '=', 'tbl_funds.fund_id')
                    ->join('tbl_employees', 'tbl_employees_funds.ef_employee_id','=','tbl_employees.employee_id')
                    ->where('ef_amount', '>', 0) 
                    ->where('fund_id', $get_fund_source)
                    ->get()->toArray();

                    if(!empty($get_fund_source_data)){
                        return response()->json([
                            'status' => 'TRUE',
                            'data' => $get_fund_source_data,
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'title' => "No record found for this fund.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Fund is required.",
                    ]);
                }
    
            }catch(\Exception $e){
                $this->create_error_log('Employee Funds Fetch', $e);
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
                ActivityTracker::track_activity('Employee Funds Fetch');
            }
        }


}
