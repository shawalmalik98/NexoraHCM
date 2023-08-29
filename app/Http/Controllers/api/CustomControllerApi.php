<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{RequestApproval};
use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait};
use App\Helpers\{ActivityTracker};

class CustomControllerApi extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait;

    protected function request_manager_status(Request $request){
       
        try{

            $request_status = RequestApproval::join('tbl_employees', 'tbl_request_approval.request_approver_id', '=', 'tbl_employees.employee_id')->where('request_id',$request['request_id'])->where('request_module_id',$request['request_module_id'])->get(['employee_code','employee_name','request_approval_time','request_approver_msg','request_approver_status'])->toArray();
        
            if(!empty($request_status)){
            
                return $this->api_response_json(TRUE,"Manager Status Response",$request_status,200);

            }else{
                return $this->api_response_json(FALSE,"Manager Status Response","",200);
            }

        }catch(\Exception $e){
             
            $this->create_error_log_from_api('View All My Attendance Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }finally{
             ActivityTracker::track_activity_from_api('View Request Response by (Manager)',$request->header('Bearer-Token'));
        }
    }
}
