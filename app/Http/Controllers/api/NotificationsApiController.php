<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{Notification, NotificationStatus, Employees};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait};
use App\Helpers\ActivityTracker;

class NotificationsApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait, ApiResponseTrait;
    const MAIN_TITLE = "";

    protected function view_all(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $title = Self::MAIN_TITLE;

            $notifications = $this->get_all_notifications(100, $login_user[0]['employee_id']);

            $data = compact('title','notifications');

            return $this->api_response_json(TRUE,"View All Notifications",$data,200);

        } catch(\Exception $e){
            
            $this->create_error_log_from_api('View All Notifications API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Access All Notifications Module API',  $request->header('Bearer-Token'));

        }

    }

}


