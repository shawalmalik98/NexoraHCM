<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Crypt, Validator};
use App\Models\{Fund};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;

class FundController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = 'manage-funds';

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


}
