<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Models\{Department, Designation, Employees, Company};
use App\Helpers\ActivityTracker;

class CompanyController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "manage-companies";

    protected function index(){
        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $companies = Company::get()->toArray();
          
            $send_data = compact('title','login_user','companies');

            return view('company.company')->with($send_data);

        }catch(\Exception $e){ 

            $this->create_error_log('Access company module', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Access company module');
          
        }
    }

    protected function add_company(Request $company_data){

		try{

            $validator = Validator::make($company_data->all(), [
                'company_name' => ['required','unique:tbl_company,company_name'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                $add_new_company = new Company();
                $add_new_company->company_name = $company_data->input('company_name');
                $add_new_company->created_by = session('user_session');
                $add_new_company->created_at = date('Y-m-d H:i:s');
                $process = $add_new_company->save();
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Company added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Company addition failed. Please try again after some time.",
                    ]);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log('Add new Company', $e);
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

            ActivityTracker::track_activity('Add new company');   

        }
    }

    protected function update_company(Request $company_data){
		
		try{

            $validator = Validator::make($company_data->all(), [
                'company_name' => ['required','unique:tbl_company,company_name, ' .$company_data->input('company_id'). ' ,company_id'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $update_company = Company::find($company_data->input('company_id'));

                if(!empty($update_company)){
                    $update_company->company_name = $company_data->input('company_name');
                    $process = $update_company->save();
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Company updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Company updation failed. Please try again after some time.",
                        ]);
                    }
                
                }
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'This company has been deleted.',
                ]);
            }
        }catch(\Exception $e){

            $this->create_error_log("Update company", $e);
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

            ActivityTracker::track_activity('Update company');

        }
    }

    protected function delete_company($company_id){
		try{
            if(!empty($company_id) && is_numeric($company_id)){
                $check_if_company_exists = Department::where('department_id', $company_id)->get()->toArray();
                if(!empty($check_if_company_exists)){
                    $check_if_employees_exist_on_company = Employees::where('employee_company', $company_id)->limit(1)->get(['employee_id'])->toArray();
                    if(empty($check_if_employees_exist_on_company)){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Company deleted successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This company can't be delete. Multiple employees registered on this company.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Company not found on this id",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Company id is required",
                ]);
            }  
        }catch(\Exception $e){

            $this->create_error_log("Delete company", $e);
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

		    ActivityTracker::track_activity('Delete company');
        
        }
    }
}
