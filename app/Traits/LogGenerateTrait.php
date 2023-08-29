<?php

namespace App\Traits;

use Illuminate\Support\Facades\URL;
use App\Models\{ErrorLog, EmployeeToken};

trait LogGenerateTrait {

    public static function create_error_log($module_name , $exception){

        $generate_error_log = new ErrorLog();
        $generate_error_log->error_log_module = $module_name;
        $generate_error_log->error_log_action_url = URL::current();
        $generate_error_log->error_log_current_url = URL::previous();
        $generate_error_log->error_log_message = $exception->getMessage() .' On line no : '. $exception->getLine() .' file name : '.$exception->getFile();
        $generate_error_log->error_log_generated_employee = (session('user_session')) ?? 1;
        $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
        $generate_error_log->save();

    }

    public function create_error_log_from_api($module_name , $exception, $header_token){

        $employee_id = EmployeeToken::where('employee_token',$header_token)->get(['employee_token_emp_id'])->toArray();
        $generate_error_log = new ErrorLog();
        $generate_error_log->error_log_module = $module_name;
        $generate_error_log->error_log_action_url = URL::current();
        $generate_error_log->error_log_current_url = URL::previous();
        $generate_error_log->error_log_message = $exception->getMessage() .' On line no : '. $exception->getLine() .' file name : '.$exception->getFile();
        $generate_error_log->error_log_generated_employee = $employee_id[0]['employee_token_emp_id'];
        $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
        $generate_error_log->save();

    }

    public function create_error_log_from_mobile_app($module_name ,$current_url,$previous_url,$get_message,$get_line_number,$get_file,$header_token){

        $employee_id = EmployeeToken::where('employee_token',$header_token)->get(['employee_token_emp_id'])->toArray();
        $generate_error_log = new ErrorLog();
        $generate_error_log->error_log_module = $module_name;
        $generate_error_log->error_log_action_url = $current_url;
        $generate_error_log->error_log_current_url = $previous_url;
        $generate_error_log->error_log_source = 2 ;
        $generate_error_log->error_log_message = $get_message .' On line no : '. $get_line_number .' file name : '.$get_file;
        $generate_error_log->error_log_generated_employee = $employee_id[0]['employee_token_emp_id'];
        $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
        $generate_error_log->save();

    }

}