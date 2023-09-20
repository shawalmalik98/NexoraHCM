<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;  
use App\Models\{Employees, EmployeeDocument, OfficeLocation, EmployeeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, ApiResponseTrait, NotificationTrait};
use App\Helpers\ActivityTracker;

class EmployeeApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait, ApiResponseTrait, NotificationTrait;

    const DOCUMENT = 'employee-personal-document';
    const LOCATION = "office-location";

    protected function change_password_function(Request $request){
		
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                'current_password' => 'required',
                'new_password' => 'required',
                'confirm_new_password' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else{
                
                $employee_password = Employees::where('employee_id', $login_user[0]['employee_id'])->get(['employee_password'])->toArray();

                $current_password = $request->post('current_password');
                $new_password = $request->post('new_password');
                $confirm_password = $request->post('confirm_new_password');
                
                if(Crypt::decryptString($employee_password[0]['employee_password']) == $current_password){
                
                    if($new_password == $confirm_password){
                        
                        $update_employee_password = Employees::find($login_user[0]['employee_id']);
                        $update_employee_password->employee_password = Crypt::encryptString($confirm_password);
                        $update_employee_password->save();
                    
                        return $this->api_response_json(TRUE,"Password changed successfully. Please wait.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Confirm password not match. Please try again.","",200);
                    }
                }else{

                    return $this->api_response_json(FALSE,"Wrong current password entered. Please try again.","",200);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Change Account Password API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Change Account Password API',  $request->header('Bearer-Token'));

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
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->get('path')));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->get('path')));
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

            ActivityTracker::track_activity_from_api('Get Employee Office Location', $request->header('Bearer-Token')); 
        }
    }

}