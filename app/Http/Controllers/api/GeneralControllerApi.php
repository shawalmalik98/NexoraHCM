<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Employees};
use App\Traits\{LoginEmployee, LogGenerateTrait, ApiResponseTrait};
use App\Helpers\ActivityTracker;

class GeneralControllerApi extends Controller
{

    use LoginEmployee, LogGenerateTrait, ApiResponseTrait;
    protected function get_error_log_mobile_application(Request $request){
   
        try{

            $validator = Validator::make($request->post(), [
                'module_name' => 'required',
                'current_url' => 'required|url',
                'previous_url' => 'required|url',
                'message'   => 'required',
                'error_line_number' => 'required',
                'get_file'  => 'required',
            
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else{

                $this->create_error_log_from_mobile_app($request->post('module_name'),$request->post('current_url'),$request->post('previous_url'),$request->post('message'),$request->post('error_line_number') ,$request->post('get_file'),$request->header('Bearer-Token'));

            }

        }catch(\Exception $e){

            $this->create_error_log_from_api('Error Log Mobile API App Burst', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Error Log Mobile API ',  $request->header('Bearer-Token'));

        }
   }

}
