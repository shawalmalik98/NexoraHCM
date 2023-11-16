<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\{Designation, Department, Employees};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;


class DesignationController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = 'manage-designations';
  
    protected function designation(){
        try{
            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $designations = Department::join('tbl_designations', 'tbl_departments.department_id', '=', 'tbl_designations.department_id')->get(['designation_id','designation_name','tbl_designations.department_id','department_name','designation_create_time','designation_update_time']);

            $departments = Department::all()->toArray();

            $send_data = compact('title' , 'login_user' , 'designations', 'departments' );

            return view('designation.designation')->with($send_data);
        }
        catch(\Exception $e){

            $this->create_error_log('View All Designation', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Designation');
         
        }
    }

    protected function add_designation(Request $designation_data){
       
        try {

            $designation_name = $designation_data->input('designation_name');
            $department_ids = $designation_data->input('department_ids');

            if(!empty($department_ids)){

                $all_departments = Department::whereIn('department_id', $department_ids)->get(['department_id', 'department_name'])->toArray();

                $existing_departments = [];
                foreach ($all_departments as $department) {
                    $designationExists = Designation::where('designation_name', $designation_name)
                        ->where('department_id', $department['department_id'])
                        ->exists();
                
                    if ($designationExists) {
                        $existing_departments[] = $department['department_name'];
                        
                    }
                }
                if (!empty($existing_departments)) {
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Designation already exists in this department(s): ' . implode(', ', $existing_departments),
                    ]);
                }
                
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'The department(s) name field is required'
                ]);

            }    

            $validator = Validator::make($designation_data->all(), [
                'designation_name' => [
                    'required',
                    //'regex:/^[\pL\s\&\,(\).]+$/u',
                    Rule::unique('tbl_designations')->where(function ($query) use ($designation_name, $department_ids) {
                        return $query->where('designation_name', $designation_name)->whereIn('department_id', $department_ids);
                    })
                ],
                'department_ids' => 'required',
            ]);
        
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            } else {

                $check_existing_departments = Department::whereIn('department_id', $department_ids)->pluck('department_id')->toArray();

                $missing_departments = array_diff($department_ids, $check_existing_departments);
                
                if (!empty($missing_departments)) {
                    $valid_department_ids = array_diff($department_ids, $missing_departments);
                    
                    if (count($valid_department_ids) == 0) {
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'This department(s) has been deleted.',
                        ]);
                    } else {
                        $department_ids = $valid_department_ids;
                    }
                }
                
                foreach ($department_ids as $department_id) {
                   
                    $add_new_designation = new Designation();
                    $add_new_designation->designation_name = $designation_data->input('designation_name');
                    $add_new_designation->department_id = $department_id;
                    $add_new_designation->designation_create_time = date('Y-m-d H:i:s');
                    $add_new_designation->designation_update_time = '00-00-00 00:00:00';
                    $process = $add_new_designation->save();
                    
                }
                if(!empty($process) && $process == 1) {
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Designation added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg'  => "Designation addition failed. Please try again after some time.",
                    ]);
                }    
            }
        } catch (\Exception $e) {
            $this->create_error_log('Add New Designation', $e);
            if (!env('APP_DEBUG')) {
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                ]);
            } else {
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' => 'Error on line ' . $e->getLine() . ' ' . $e->getMessage(),
                ]);
            }
        } finally {
            ActivityTracker::track_activity('Add New Designation');
        }
        
    }

    protected function update_designation(Request $designation_data){
		
		try{

            $designation_name = $designation_data->input('designation_name');
            $department_id = $designation_data->input('department_id');

            $validator = Validator::make($designation_data->all(), [
                'designation_name' => [ 'required',
                    Rule::unique('tbl_designations')->ignore($designation_data->input('designation_id'), 'designation_id')->where(function ($query) use ($designation_name, $department_id) {
                        return $query->where('designation_name', $designation_name)
                            ->where('department_id', $department_id);
                    }),
                ],
                'department_id' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                $check_if_department_exists = Department::where('department_id', $department_id )->get()->toArray();
                if(!empty($check_if_department_exists)){
                    $update_desgnation = Designation::find($designation_data->input('designation_id'));
                    if(!empty($update_desgnation)){

                        $update_desgnation->designation_name = $designation_data->input('designation_name');
                        $update_desgnation->department_id = $designation_data->input('department_id');
                        $update_desgnation->designation_update_time = date('Y-m-d H:i:s');
                        $process = $update_desgnation->save();
                        if($process == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Designation updated successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Designation updated failed. Please try again after some time.",
                            ]);
                        }

                    }

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This designation has been deleted.',
                    ]);
                    
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This department has been deleted.',
                    ]);
                }          
            }
        }catch(\Exception $e){
            $this->create_error_log("Update Designation", $e);
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

            ActivityTracker::track_activity('Update Designation'); 
        }
    }

    protected function delete_designation($designation_id){

        try{
            if(!empty($designation_id) && is_numeric($designation_id)){
                $check_if_designation_exists = Designation::where('designation_id', $designation_id)->get()->toArray();
                if(!empty($check_if_designation_exists)){
                    $check_if_employees_exist_on_desig = Employees::where('employee_designation', $designation_id)->limit(1)->get(['employee_id'])->toArray();
                    if(empty($check_if_employees_exist_on_desig)){
                        Designation::where('designation_id', $designation_id)->delete();
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Designation deleted successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This designation can't be delete. Multiple employees registered on this designation.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Designation not found on this id",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Designation id is required",
                ]);
            }  
        }catch(\Exception $e){

            $this->create_error_log("Delete Designation", $e);
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

            ActivityTracker::track_activity('Delete Designation');
        }
    }

    protected function get_designations($department_id){
		
		try{

            $designations = Designation::where('department_id', $department_id)->get(['designation_id','designation_name'])->toArray();

            $result[] = '<option value="">Select Designation</option>';
            foreach($designations as $designation){
                $result[] .= '<option value="'.$designation['designation_id'].'">'.$designation['designation_name'].'</option>';
            }
            
        }catch(\Exception $e){

            $this->create_error_log("Fetch Designations For Create Employee Page", $e);
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

            ActivityTracker::track_activity('Fetch Designations For Create Employee Page');
            return $result;
          
        }
    }

}
