<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

use App\Models\{Letter, Employees};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait};
use App\Helpers\ActivityTracker;

class LettersApiController extends Controller
{
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait;
    const MAIN_TITLE = "letters";

    protected function index(Request $request){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $where_clause = '1=1';
            
            if(strlen($request->get('emp_status'))==0){
                $where_clause .= ' AND letter_employee.employee_isactive = "1"';
            }
            if(strlen($request->get('emp_status'))>0 || !empty($request->get('emp_name'))){
                $limit_query = '';

                if(!empty($request->get('emp_name'))){
                    $where_clause .= ' AND tbl_letters.letter_employee_id = "'.$request->get('emp_name').'"';
                }
                if(strlen($request->get('emp_status'))>0){
                    $where_clause .= ' AND letter_employee.employee_isactive = "'.$request->get('emp_status').'"';
                }

            }

            $employees = Employees::where('employee_isactive', '=',$request->get('emp_status') ?? 1)->get(['employee_id','employee_code','employee_name'])->toArray();

            $letters = Letter::selectRaw('letter_id , letter_employee_id , letter_title , document_name , uploader , upload_date , letter_employee.employee_name as letter_employee_name , uploader_tbl.employee_name as uploader_name , CONCAT("download-employee-letter?path=letter/",document_name,"&title=employee letter") as file_path')
            ->where('uploader', '=', $login_user[0]['employee_id'])
            ->leftjoin('tbl_employees as letter_employee', 'letter_employee.employee_id', '=', 'tbl_letters.letter_employee_id')
            ->leftjoin('tbl_employees as uploader_tbl', 'uploader_tbl.employee_id', '=', 'tbl_letters.uploader')
            ->whereRaw($where_clause)
            ->get()
            ->toArray();

            $data = compact('title','letters','employees');

            return $this->api_response_json(TRUE,"View All Letters",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('View All Letters API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('View All Letters API',  $request->header('Bearer-Token'));

        }
    }


    protected function add_letter(Request $request){

        try{

            $filename = '';
            $condition = 'min:0';
            
            if(!empty($request->file('document_name'))){

                foreach($request->file('document_name') as $file){
                    $filename = $file->getClientOriginalExtension();
                }
                if(!empty($request->post('title'))){
                    $condition = 'min:'.count($request->post('title'));
                }
                
            }
            
            
            $validator = Validator::make($request->all(), [
                'title.*'    => 'required',
                'employee_id' => 'required',
                'document_name' => ['required','array',$condition],
                'document_name.*' =>  ['required','max:10000',($filename == 'txt') ? 
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

                $i=0;
                
                foreach($request->file('document_name') as $file){
                    $new_letter = new Letter();
                    $new_letter->letter_title = $request->post('title')[$i];
                    $new_letter->letter_employee_id = $request->post('employee_id');
                    $new_letter->uploader =  $login_user[0]['employee_id'];
                    $new_letter->upload_date = date('Y-m-d H:i:s');
                    $i++;

                    $file_name =  $file->getClientOriginalName();

                    $path = $file->storeAs('portal_documents/letter/',$file_name);

                    $new_letter->document_name = $file_name;

                    $process = $new_letter->save();
                }
                
                if($process == 1){


                    $this->send_notification_api('New Company Letter Added', 'Dear User, HR added a new company letter to your account. Please click here to view.','my-comapny-letters',$request->post('employee_id'),NULL,NULL,'no',$login_user[0]['employee_id']);
                            
                    return $this->api_response_json(TRUE,"Letter Added Successfully.","",200);

                }else{
                    
                    return $this->api_response_json(FALSE,"Letter Addition Failed. Please try again after some time.","",200);

                }
            }   
        }catch(\Exception $e){

            $this->create_error_log_from_api('Add Letter API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{
      
            ActivityTracker::track_activity_from_api('Add Letter API',  $request->header('Bearer-Token'));

        }

    }



    protected function update_letter(Request $request){

        try{

            $filename = '';

            if($request->hasFile('document_name')){
                $filename = $request->file('document_name')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->all(), [
                'letter_id' => 'required',
                'title' => 'required',
                'document_name' => ['max:10000',($filename == 'txt') ? 
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

                $update_letter = Letter::find($request->post('letter_id'));
                $update_letter->letter_title = $request->post('title');
                $update_letter->uploader = $login_user[0]['employee_id'];
                $update_letter->upload_date = date('Y-m-d H:i:s');

                if($request->hasFile('document_name')){

                    if (file_exists(storage_path('app/portal_documents/letter/'.$update_letter->document_name))) {
                    
                        unlink(storage_path('app/portal_documents/letter/'.$update_letter->document_name));
                    }

                    $file_name = $request->file('document_name')->getClientOriginalName();

                    $path = $request->file('document_name')->storeAS('portal_documents/letter/',$file_name);

                    $update_letter->document_name = $file_name;

                }

                $process = $update_letter->save();

                if($process == 1){

                    return $this->api_response_json(TRUE,"Letter Updated Successfully.","",200);

                }else{

                    return $this->api_response_json(FALSE,"Letter Updation Failed. Please try again after some time.","",200);

                }
            }   
        }catch(\Exception $e){

            $this->create_error_log_from_api('Update Letter API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        
        }finally{
            
            ActivityTracker::track_activity_from_api('Update Letter API',  $request->header('Bearer-Token'));

        }

    }

    protected function personal_letter(Request $request){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $letters = Letter::selectRaw('letter_id , letter_employee_id , letter_title , document_name , uploader , upload_date , CONCAT("download-personal-letter?path=letter/",document_name,"&title=employee letter") as file_path')
            ->where('letter_employee_id', '=', $login_user[0]['employee_id'])
            ->get()
            ->toArray();

            $data = compact('title','letters');

            return $this->api_response_json(TRUE,"My Letters",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('My Letters API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('My Letters API',  $request->header('Bearer-Token'));
        }
    }

    protected function download_employee_letter(Request $request){
        try{
            if(!empty($request->get('path'))){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->get('path')));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->get('path')));
                }else{
                    return $this->api_response_json(FALSE,"Letter Not Found","",200);
                }
            }else{
                return $this->api_response_json(FALSE,"path is required","",200);
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('Employee Letter Download API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Employee Letter Download API',  $request->header('Bearer-Token'));
        }
    }

    protected function download_personal_letter(Request $request){
        try{
            if(!empty($request->get('path'))){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->get('path')));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->get('path')));
                }else{
                    return $this->api_response_json(FALSE,"Letter Not Found","",200);
                }
            }else{
                return $this->api_response_json(FALSE,"path is required","",200);
            }
        }catch(\Exception $e){

            $this->create_error_log_from_api('My Letter Download API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('My Letter Download API',  $request->header('Bearer-Token'));

        }
    }

}
