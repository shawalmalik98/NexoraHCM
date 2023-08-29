<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{LeaveManagement};
use App\Traits\{LoginEmployee, LogGenerateTrait, ApiResponseTrait};
use App\Helpers\ActivityTracker;

class LeavesApiController extends Controller
{
    use LoginEmployee, LogGenerateTrait, ApiResponseTrait;

    const TITLE_GLOBAL = "manage-leaves";
    const TITLE_GLOBAL1 = "leave-management";

    protected function get_leave_names(Request $request){
       
        try{
            $title = Self::TITLE_GLOBAL1;

            $leave_names = LeaveManagement::where('leave_visibilty',1)->get(['leave_manage_id','leave_name'])->toArray();
           
            $data = compact('title','leave_names');

            return $this->api_response_json(TRUE,"All Leave Names",$data,200);
        }
        catch(\Exception $e){

            $this->create_error_log_from_api('Get All Leave Name API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('Get All Leave Name API',  $request->header('Bearer-Token'));

        }
    }

}
