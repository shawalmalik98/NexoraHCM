<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ErrorLog};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;

class ErrorLogController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "system-errors";
    
    protected function index(Request $request){
        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $error_logs = ErrorLog::orderBy('error_log_id','DESC')->get()->toArray();

            $send_data = compact('title','login_user','error_logs');

            return view('miscellanous.system-error-logs', $send_data );
        }catch(\Exception $e){

            $this->create_error_log('View All System Errors', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All System Errors');
         
            
        }
    }
}
