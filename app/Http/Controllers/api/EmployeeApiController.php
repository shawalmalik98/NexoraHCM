<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;  
use App\Models\{Employees, EmployeeDevice, EmployeeDocument, MobileAccess, OfficeLocation, EmployeeLocation, HrmSetting, EmploymentStatus, EmployeeGroup, Department};
use App\Traits\{LoginEmployee, LogGenerateTrait, ApiResponseTrait, NotificationTrait};
use App\Helpers\ActivityTracker;

class EmployeeApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait, ApiResponseTrait, NotificationTrait;

    const MAIN_TITLE = "employee";
    const DOCUMENT = 'employee-personal-document';
    const LOCATION = "office-location";


    protected function get_employee_detail(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_id' , $login_user[0]['employee_id'])
            ->get(['employee_id','employee_code','employee_name','employee_personal_email','employee_department','department_name','designation_name','employee_is_manager','employee_geofencing_enability','employee_offdays','employee_password'])->toArray();
            $employees[0]['password'] = Crypt::decryptString($employees[0]['employee_password']);
            unset($employees[0]['employee_password']);

            return $this->api_response_json(TRUE,"Employee Detail",$employees,200); 
        
        }catch(\Exception $e){

            $this->create_error_log_from_api('View Employee Detail API', $e, $request->header('Bearer-Token'), $request->header('apk-version'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }
        finally{

            ActivityTracker::track_activity_from_api('View Employee Detail API',  $request->header('Bearer-Token'), $request->header('apk-version'));
           
        }

    }

    protected function change_password_function(Request $request){
		
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                // 'current_password' => 'required|max:10',
                'new_password' => 'required|max:10',
                'confirm_new_password' => 'required|max:10',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else{
                
                $employee_password = Employees::where('employee_id', $login_user[0]['employee_id'])->get(['employee_password'])->toArray();

                // $current_password = $request->post('current_password');
                $new_password = $request->post('new_password');
                $confirm_password = $request->post('confirm_new_password');
                
                // if(Crypt::decryptString($employee_password[0]['employee_password']) == $current_password){
                
                    if($new_password == $confirm_password){
                        
                        $update_employee_password = Employees::find($login_user[0]['employee_id']);
                        $update_employee_password->employee_password = Crypt::encryptString($confirm_password);
                        $update_employee_password->save();
                    
                        return $this->api_response_json(TRUE,"Password changed successfully. Please wait.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Confirm password not match. Please try again.","",200);
                    }
                // }else{

                //     return $this->api_response_json(FALSE,"Wrong current password entered. Please try again.","",200);
                // }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Change Account Password API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Change Account Password API',  $request->header('Bearer-Token'), $request->header('apk-version'));

        }
	
	}

    protected function employee_personal_documents(Request $request){
        
        try{
            $title = Self::DOCUMENT;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            
            $documents = EmployeeDocument::where('employee_id',$login_user[0]['employee_id'])->orderBy('document_id','DESC')->get(['document_name','document_title','document_upload_date'])->toArray();

            $i = 0;
            foreach($documents as $document){
                $documents[$i]['document_link'] = url('api/v1/download-personal-document?path=employee_docs/'.$login_user[0]['employee_id'].'/'.$document['document_name'].'&title=Download Personal Document');
                $i++;
            }
           
            $data = compact('title','documents');

            return $this->api_response_json(TRUE,"My Document",$data,200);            

        }catch(\Exception $e){

            $this->create_error_log_from_api('Access Employee Personal Documents Module API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Access Employee Personal Documents Module API',  $request->header('Bearer-Token'));

        }

    }

    protected function download_personal_document(Request $request){
        try{
            if(!empty($request->get('path'))){
                $check_if_file_exists = !empty(\App\Helpers\CustomHelper::portal_document($request->get('path')));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(\App\Helpers\CustomHelper::portal_document($request->get('path')));
                }else{
                    return $this->api_response_json(FALSE,"File not found","",200);
                }
            }else{
                return $this->api_response_json(FALSE,"File path is required","",200);
            }
        }catch(\Exception $e){
            $this->create_error_log_from_api($request->get('title'), $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }finally{
            ActivityTracker::track_activity_from_api($request->get('title'),  $request->header('Bearer-Token'));
        }
    }

    protected function store_employee_personal_documents(Request $request){
        
        try{
            $filename = '';
            if(!empty($request->file('file'))){
                foreach($request->file('file') as $file){
                    $filename = $file->getClientOriginalExtension();
                }
            }
            
            $validator = Validator::make($request->all(), [
                'title'    => 'required',
                'file' =>  ['required','max:10000',($filename == 'txt') ? 
                File::types(['jpg','jpeg','png','docx','pdf','txt'])
                :
                File::types(['jpg','jpeg','png','docx','pdf'])],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

                if($request->hasFile('file')){

                    $document = $request->file('file');
                    $document_name = $document->getClientOriginalName();
    
                    $j=1;
                    while(is_file(storage_path('app/portal_documents/employee_docs/'.$login_user[0]['employee_id'].'/'.$document_name))){
                        $document_name = $j.$document_name;
                        $j++;
                    }

                    $document->storeAs('portal_documents/employee_docs/'.$login_user[0]['employee_id'].'/', $document_name);
                
                    $document_upload = new EmployeeDocument();
                    $document_upload->employee_id = $login_user[0]['employee_id'];
                    $document_upload->document_title = $request->input('title');
                    $document_upload->document_name = $document_name;
                    $document_upload->document_upload_date = date('h:i:s a d F, Y');
                    $document_upload->document_uploaded_by = $login_user[0]['employee_id'];
                    $process = $document_upload->save();
                    if($process == 1){
                        $this->send_notification_api('New Document Upload','Dear HR, '.$login_user[0]['employee_name'].', uploaded a new document Please click here to view.','employees/employee-documents/'.$login_user[0]['employee_id'].'?document-id='.$document_upload->document_id,null,null,null,'yes',$login_user[0]['employee_id']);

                        return $this->api_response_json(TRUE,"Documents uploaded successfully","",200);
                    }else{
                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                    }
                    
                }else{
                    return $this->api_response_json(FALSE,"Documents are required","",200);
                }
            }   
        }catch(\Exception $e){
            $this->create_error_log_from_api("Store Employee personal Documents API", $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }finally{
            ActivityTracker::track_activity('Store Employee personal Documents API');
        }
		
    }


    protected function employee_office_location(Request $request){

        try{
            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $employee_office_location = EmployeeLocation::join('tbl_office_locations','tbl_employees_location.emp_loc_loc_id','tbl_office_locations.location_id')
            ->join('tbl_employees','tbl_employees_location.emp_loc_employee_id','tbl_employees.employee_id')
            ->where('employee_isactive',1)
            ->where('employee_id',$login_user[0]['employee_id'])
            ->get(['location_title','location_latitude','location_longitude','location_attendance_radius'])
            ->toArray();

            return $this->api_response_json(TRUE,"Employee Office Location(s)",$employee_office_location,200);
        }
        catch(\Exception $e){

            $this->create_error_log_from_api('Get Employee Office Location', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
        finally{

            ActivityTracker::track_activity_from_api('Get Employee Office Location', $request->header('Bearer-Token'), $request->header('apk-version'));
        }
    }

    protected function employee_mobile_access(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
           
            $all_mobile_access = MobileAccess::join('tbl_employees', 'tbl_mobile_access.mobile_employee_id', '=', 'tbl_employees.employee_id')
            ->where('employee_isactive',1)
            ->where('employee_id',$login_user[0]['employee_id'])
            ->get(['access_button_id as Button Name ID','access_buttons as Button Access'])->toArray();

            return $this->api_response_json(TRUE,"Employee Mobile Button(s) Access",$all_mobile_access,200);
            

        }catch(\Exception $e){

            $this->create_error_log_from_api('Get Employee Mobile Button Access', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Get Employee Button Access', $request->header('Bearer-Token'), $request->header('apk-version')); 

        }

    }

}