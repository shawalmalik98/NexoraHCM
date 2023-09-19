<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Department, Designation, Employees};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;

class DepartmentController extends Controller
{

    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "manage-departments";

    protected function department(){
        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $departments = Department::all()->toArray();
            $departments_hod =  Department::where('department_hod',$login_user[0]['employee_id'])->get('department_hod')->toArray();
          
            $send_data = compact('title','login_user', 'departments','departments_hod');

            return view('department.department')->with($send_data);

        }catch(\Exception $e){ 

            $this->create_error_log('Access department module', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Access department module');
          
        }
    }


     protected function add_department(Request $depart_data){

		try{

            $validator = Validator::make($depart_data->all(), [
                'department_name' => ['required','unique:tbl_departments,department_name'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                $add_new_department = new Department();
                $add_new_department->department_name = $depart_data->input('department_name');
                $add_new_department->department_create_time = date('d-m-Y H:i:s');
                $add_new_department->department_update_time = '00-00-00 00:00:00';
                $process = $add_new_department->save();
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Department added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Department addition failed. Please try again after some time.",
                    ]);
                }
            }
        }
        catch(\Exception $e){

            $this->create_error_log('Add new Department', $e);
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

            ActivityTracker::track_activity('Add new department');   

        }
    }
    

    protected function update_department(Request $depart_data){
		
		try{

            $validator = Validator::make($depart_data->all(), [
                'department_name' => ['required','unique:tbl_departments,department_name, ' .$depart_data->input('department_id'). ' ,department_id'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $update_department = Department::find($depart_data->input('department_id'));

                if(!empty($update_department)){

                    $update_department->department_name = $depart_data->input('department_name');
                    $update_department->department_update_time = date('d-m-Y H:i:s');
                    $update_department->department_hod =  $depart_data->input('employee_name');
                    $process = $update_department->save();
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Department updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Department updation failed. Please try again after some time.",
                        ]);
                    }
                
                }
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'This department has been deleted.',
                ]);
            }
        }catch(\Exception $e){

            $this->create_error_log("Update department", $e);
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

            ActivityTracker::track_activity('Update department');

        }
    }

    protected function delete_department($department_id){
		try{
            if(!empty($department_id) && is_numeric($department_id)){
                $check_if_department_exists = Department::where('department_id', $department_id)->get()->toArray();
                if(!empty($check_if_department_exists)){
                    if(!in_array($department_id,array('1','2','3'))){
                        $check_if_employees_exist_on_depart = Employees::where('employee_department', $department_id)->limit(1)->get(['employee_id'])->toArray();
                        if(empty($check_if_employees_exist_on_depart)){
                            Department::where('department_id', $department_id)->delete();
                            Designation::where('department_id', $department_id)->delete();
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Department deleted successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "This department can't be delete. Multiple employees registered on this department.",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This department id is reserved due to backend logics. Can't delete it.",
                        ]);
                    } 
                    
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Department not found on this id",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Department id is required",
                ]);
            }  
        }catch(\Exception $e){

            $this->create_error_log("Delete department", $e);
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

		    ActivityTracker::track_activity('Delete department');
        
        }
    }

    protected function department_managers(Request $request){

        try{

            $get_all_dept_employees = Employees::where('employee_department', $request->input('department'))->where('employee_is_manager', 1)->where('employee_isactive', '1')->get(['employee_id','employee_code','employee_name'])->toArray();
            
            return response()->json([
                'data' => $get_all_dept_employees
            ]);
            
        }catch(\Exception $e){
            $this->create_error_log('Get Departments Manager', $e);
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

            ActivityTracker::track_activity('Get Departments Manager');
    
        }

    }
    
}