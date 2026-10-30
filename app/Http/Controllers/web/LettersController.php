<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\{Letter, Employees, LetterTemplate, LetterTemplateEmployee, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Barryvdh\DomPDF\Facade\Pdf;

class LettersController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait;
    const MAIN_TITLE = "letters";
    const MAIN_TITLE2 = "employee-letters";
    const SUB_TITLE = "letter-templates";
    const DOWNLOAD_TITLE = "Download Templates";
    const LETTER_TEMPLATE = 'my-comapny-letters';

    protected function index(Request $request){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1 AND employee_isactive = "'.$status.'"';
            $where_clause_to = '1=1';
            if(!empty($request->employee_location) || !empty($request->emp_status) || !empty($request->emp_name)){
                $limit_query = '';
                if(!empty($request->employee_location)){
                    $where_clause .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request->employee_location).'';
                    $where_clause_to   .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request->employee_location).''; 
                }
                if(!empty($request->emp_name)){
                    $where_clause .= ' AND tbl_letters.letter_employee_id = '.\DB::getPdo()->quote((string) $request->emp_name).'';
                }

            }
            
            $employees = Employees::where('employee_isactive', '=', $status)->whereRaw($where_clause_to)->get(['employee_id','employee_code','employee_name'])->toArray();

            $letters = Letter::where('uploader', '=', session('user_session'))->join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_letters.letter_employee_id')
            ->whereRaw($where_clause)
            ->get(['uploader','letter_id','letter_employee_id','letter_title','document_name','upload_date'])
            ->toArray();

            $send_data = compact('title','login_user','letters','employees','employee_locations');
            
            return view('company-letters.all-letters', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View All Letters', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View All Letters');
         
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
                $condition = 'min:'.count($request->input('title'));
            }
            
            
            $validator = Validator::make($request->all(), [
                'title.*'    => 'required',
                'employee' => 'required',
                'document_name' => ['required','array',$condition],
                'document_name.*' =>  ['required','max:10000',($filename == 'txt') ? 
                File::types(['jpg','jpeg','png','docx','pdf','txt'])
                :
                File::types(['jpg','jpeg','png','docx','pdf'])],

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
              
                $i=0;
                
                foreach($request->file('document_name') as $file){

                    $document = $file;
                  
                    $document_name = $document->getClientOriginalName();

                    $j=1;

                    while(is_file(storage_path('app/portal_documents/letter/'.$document_name))){
                    
                        $document_name = $j.$document_name;
                        $j++;
                    }
                  
                    $path = $file->storeAs('portal_documents/letter/', $document_name);

                    $new_letter = new Letter();
                    $new_letter->letter_title = $request->input('title')[$i];
                    $new_letter->letter_employee_id = $request->input('employee');
                    $new_letter->uploader =  session('user_session');
                    $new_letter->upload_date = date('Y-m-d H:i:s');
                    $new_letter->document_name = $document_name;
                    $i++;


                    $process = $new_letter->save();
                }
                
                if($process == 1){


                    $this->send_notification('New Company Letter Added', 'Dear Employee, HR added a new company letter to your account. Please click here to view.','my-comapny-letters',$request->input('employee'),NULL,NULL,'no');
                            

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Letter added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Letter addition failed. Please try again after some time.",
                    ]);
                }
            }   
        }catch(\Exception $e){
            $this->create_error_log("Add Letter", $e);
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
            ActivityTracker::track_activity('Add Letter');
        }

    }

    protected function update_letter(Request $request){

        try{

            $filename = '';

            if($request->hasFile('document_name')){
                $filename = $request->file('document_name')->getClientOriginalExtension();
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'document_name' => ['max:10000',($filename == 'txt') ? 
                File::types(['jpg','jpeg','png','docx','pdf','txt'])
                :
                File::types(['jpg','jpeg','png','docx','pdf'])],

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $update_letter = Letter::find($request->input('letter_id'));
                $update_letter->letter_title = $request->input('title');
                $update_letter->uploader = session('user_session');
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


                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Letter updated successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Letter updation failed. Please try again after some time.",
                    ]);
                }
            }   
        }catch(\Exception $e){
            $this->create_error_log("Update Letter", $e);
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
            ActivityTracker::track_activity('Update Letter');
        }

    }

    protected function personal_letter(){

        try{
            $title = Self::LETTER_TEMPLATE;

            $login_user = $this->get_login_user_detail();

            $letters = Letter::where('letter_employee_id', session('user_session'))->get()->toArray();

            $send_data = compact('title','login_user','letters');

            return view('company-letters.my-letter', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View All Letters', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('View All Letters');
         
        }
    }

    protected function download_employee_letter(Request $request){
        try{
            if(!empty($request->path)){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
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

    protected function download_personal_letter(Request $request){
        try{
            if(!empty($request->path)){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
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

    protected function letter_template(Request $request){
        try{
            $title = Self::SUB_TITLE;

            $login_user = $this->get_login_user_detail();

            $all_letter_template = LetterTemplate::get()->toArray();
            $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->get(['employee_id','employee_code','employee_name','department_name','designation_name'])->toArray();
            
            $send_data = compact('title','login_user','all_letter_template','employees');

            return view('company-letters.letter-template', $send_data);
        }catch(\Exception $e){
            $this->create_error_log('All Letter Template', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('All Letter Template');
          
        }

    }  
    
    protected function add_new_letter_template(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'letter_name' => 'required',
                'template' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();

            $variable_array_validate = explode(" ",strip_tags(str_replace('&nbsp;',' ',$request->input('template'))));

            foreach($variable_array_validate as $validate){
                
               $start = substr($validate, 0, 1);
               $end = substr($validate,-1);
                
               if($start == "#" || $end == '#'){
                    if($start == "#" && $end == '#'){
                        $regex = preg_match('[@!#$%^&*()<>?/|}{~:-]', $validate);
                        if($regex){
                            return response()->json([
                                'msg' => "'".$validate."' remove special characters from string" ,
                                'status' => 'FALSE'
                            ]);
                        }
                    }else{
                        return response()->json([
                            'msg' => "'".$validate."' is in invalid format" ,
                            'status' => 'FALSE'
                        ]);
                    }
               }
            }

            $variable_array = explode("#",strip_tags($request->input('template')));

            $store_variable_array = [];
            for($i=1;$i<count($variable_array);$i+=2){
                array_push($store_variable_array,str_replace(" ","_",$variable_array[$i]));
            }
            if(count($store_variable_array) != count(array_unique($store_variable_array))){

                return response()->json([
                    'msg' => "Can't use the same name variable" ,
                    'status' => 'FALSE'
                ]);
            }

            $add_letter_template = new LetterTemplate();
            $add_letter_template->letter_template_name = $request->input('letter_name');
            $add_letter_template->letter_template_create_by = $login_user[0]['employee_id'];
            $add_letter_template->letter_template_html = $request->input('template');
            $add_letter_template->letter_template_variables = json_encode($store_variable_array);
            $add_letter_template->letter_template_created_at = date('Y-m-d H:i:s');
            $process = $add_letter_template->save();

            if($process == 1){

                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Letter template add successfully.",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Letter template add failed. Please try again after some time.",
                ]);
            }

        }catch(\Exception $e){
            $this->create_error_log("Add Letter Template", $e);
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
            ActivityTracker::track_activity('Add Letter Template');
        }

    }

    protected function employee_letter_template(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'letter_id' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $letter_template = LetterTemplate::where('letter_template_id',$request->input('letter_id'))->get(['letter_template_variables'])->toArray();
            $variable_array = json_decode($letter_template[0]['letter_template_variables'], true);
            
            $validate_array = [];
            foreach($variable_array as $validate_value){
                $validate_array[$validate_value] = 'required';
            }
            $validator = Validator::make($request->all(),
                $validate_array
            );

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();

            $json_variable = [];
            foreach($variable_array as $variable){

                $json_variable[$variable] = $request->input($variable);

            }

            $variables = json_encode($json_variable);

            $add_new_letter = new LetterTemplateEmployee();
            $add_new_letter->temp_emp_create_by = $login_user[0]['employee_id'];
            $add_new_letter->temp_emp_letter_id = $request->input('letter_id');
            $add_new_letter->temp_emp_employee_id = $request->input('employee_id');
            $add_new_letter->temp_emp_submit = 1;
            $add_new_letter->temp_emp_variables = $variables;
            $add_new_letter->temp_emp_created_at = date('Y-m-d H:i:s');
            $process = $add_new_letter->save();

            if($process==1){
                return response()->json([
                    'status' => 'TRUE',
                    'msg' =>  "Employee letter add successfully",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' =>  "Something went wrong. Please try again",
                ]);
            }
            
        }catch(\Exception $e){
            $this->create_error_log("Add Employee Letter Template", $e);
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
            ActivityTracker::track_activity('Add Employee Letter Template');
        }

    }

    protected function employee_letter(){

        try{
            
            $title = Self::MAIN_TITLE2;

            $login_user = $this->get_login_user_detail();

            $all_employee_letter = LetterTemplateEmployee::join('tbl_letter_template','tbl_letter_template.letter_template_id','tbl_letter_template_employee.temp_emp_letter_id')->get()->toArray();

            $send_data = compact('title','login_user','all_employee_letter');

            return view('company-letters.employee-letter-template', $send_data);
            
        }catch(\Exception $e){
            $this->create_error_log('All Employee Letter', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{
            ActivityTracker::track_activity('All Employee Letter');
           
        }
    }

    protected function employee_letter_pdf(Request $request){

        try{
            $all_employee_letter = LetterTemplateEmployee::join('tbl_letter_template','tbl_letter_template.letter_template_id','tbl_letter_template_employee.temp_emp_letter_id')->where('temp_emp_id',$request->letter_id)->get()->toArray();
        
            if(!empty($all_employee_letter)){
        
                $all_variable = json_decode($all_employee_letter[0]['temp_emp_variables'],true);
                $body_html = $all_employee_letter[0]['letter_template_html'];

                foreach($all_variable as $key => $variable){
                    $key = '#'.$key.'#';
                    $body_html = str_replace($key,$variable,$body_html);    
                }
                $filename = $all_employee_letter[0]['letter_template_name'].'pdf';
                $send_data = compact('body_html');
                $pdf = Pdf::loadView('company-letters.employee-letter-pdf', $send_data);
            }else{
                $filename = "letter.pdf";
                $send_data = compact('title');
                $pdf = Pdf::loadView('company-letters.employee-letter-pdf', $send_data);
            }
        }catch(\Exception $e){
            $this->create_error_log('View Letter Employee PDF', $e);
            $filename = "letter.pdf";
            $send_data = compact('title');
            $pdf = Pdf::loadView('company-letters.employee-letter-pdf', $send_data);
        }finally{
            ActivityTracker::track_activity('View Letter Employee PDF');
            return $pdf->setPaper('a4', 'potrait')->stream($filename);
        }
    }

    protected function insert_template_in_document(Request $request){

        try{
            
            $validator = Validator::make($request->all(), [
                'letter_id' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $login_user = $this->get_login_user_detail();
            $all_employee_letter = LetterTemplateEmployee::join('tbl_letter_template','tbl_letter_template.letter_template_id','tbl_letter_template_employee.temp_emp_letter_id')->where('temp_emp_id',$request->input('letter_id'))->get()->toArray();
            
            if(!empty($all_employee_letter) && empty($all_employee_letter[0]['temp_emp_employee_id'])){
                return response()->json([
                    'msg' => 'Their is no employee set for submit letter',
                    'status' => 'FALSE'
                ]);
            }
            if(!empty($all_employee_letter) && $all_employee_letter[0]['temp_emp_submit'] == 2){
                return response()->json([
                    'msg' => 'Letter already submitted',
                    'status' => 'FALSE'
                ]);
            }

            $all_variable = json_decode($all_employee_letter[0]['temp_emp_variables'],true);
            $body_html = $all_employee_letter[0]['letter_template_html'];

            foreach($all_variable as $key => $variable){

                $key = '#'.$key.'#';
                $body_html = str_replace($key,$variable,$body_html);

            }

            $file_name = $login_user[0]['employee_id'].rand(1000,9999). $all_employee_letter[0]['letter_template_name'].'.pdf';

            $send_data = compact('body_html');
            $pdf = Pdf::loadView('company-letters.employee-letter-pdf', $send_data);
            $pdf->render();
            $output = $pdf->output();
            file_put_contents(storage_path('app/portal_documents/letter/'.$file_name), $output);

            $new_letter = new Letter();
            $new_letter->letter_title = $all_employee_letter[0]['letter_template_name'];
            $new_letter->letter_employee_id = $all_employee_letter[0]['temp_emp_employee_id'];
            $new_letter->uploader =  $login_user[0]['employee_id'];
            $new_letter->upload_date = date('Y-m-d H:i:s');
            $new_letter->document_name = $file_name;
            $process = $new_letter->save();
            
            $update = LetterTemplateEmployee::where('temp_emp_id',$request->input('letter_id'))->update(['temp_emp_submit'=>2]);

            if($process == 1){

                $this->send_notification('New Company Letter Added', 'Dear User, HR added a new company letter to your account. Please click here to view.','my-comapny-letters',$all_employee_letter[0]['temp_emp_employee_id'],NULL,NULL,'no');

                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Letter add successfully.",
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Letter updation failed. Please try again after some time.",
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log("Add Letter", $e);
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
            ActivityTracker::track_activity('Add Letter');
        }
    }

    protected function letter_pdf(Request $request){
        
        try{
            $title = Self::DOWNLOAD_TITLE;
            $letter_template = LetterTemplate::get()->where('letter_template_id',$request->letter_id)->toArray();
            if(!empty($letter_template)){
                $body_html = $letter_template[0]['letter_template_html'];
                $filename = $letter_template[0]['letter_template_name'].'pdf';
                $send_data = compact('title','body_html');
                $pdf = Pdf::loadView('company-letters.employee-letter-pdf', $send_data);
            }else{
                $filename = "letter.pdf";
                $send_data = compact('title');
                $pdf = Pdf::loadView('company-letters.employee-letter-pdf', $send_data);
            }
        }catch(\Exception $e){
            $this->create_error_log('View Letter Template PDF', $e);
            $filename = "letter.pdf";
            $send_data = compact('title');
            $pdf = Pdf::loadView('company-letters.employee-letter-pdf', $send_data);
        }finally{
            ActivityTracker::track_activity('View Letter Template PDF');
            return $pdf->setPaper('a4', 'potrait')->stream($filename);
        }
    }
}
