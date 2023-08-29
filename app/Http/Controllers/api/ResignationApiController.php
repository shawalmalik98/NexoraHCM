<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{Resignation, Department, Designation, Employees, RequestApproval};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait,MailTrait};
use App\Helpers\ActivityTracker;

class ResignationApiController extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait,MailTrait;

    const Resignation = "resignation";
    const AllResignation = "all-resignation";

    protected  function employee_resignation(Request $request){

        try{

            $title = Self::Resignation;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $resignations = Resignation::
            join('tbl_employees','tbl_employees.employee_id','tbl_resignation_letters.resignation_employee')
            ->where('resignation_employee', $login_user[0]['employee_id'])->orderBy('resignation_id','DESC')->get(['resignation_id','employee_name','resignation_description','resignation_from_date','resignation_to_date','resignation_status'])->toArray();

            $resignation_request_id = array_column($resignations,'resignation_id');

            $resignation_status = Resignation::where('resignation_employee', $login_user[0]['employee_id'])->orderBy('resignation_id','DESC')->limit(1)->get(['resignation_status'])->toArray();

            $department_name = Department::where('department_id',$login_user[0]['employee_department'])->get('department_name')->toArray();

            $designation_name = Designation::where('designation_id',$login_user[0]['employee_designation'])->get('designation_name')->toArray();
        
            $data = compact('title', 'resignations','resignation_status','department_name','designation_name');
        
            return $this->api_response_json(TRUE,"My Resignation",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('My Resignation API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('My Resignation API',  $request->header('Bearer-Token'));

        }
        
    }

    protected function add_employee_resignation(Request $request){
     
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                'reason' => 'required',
                'from_date' => 'required|date_format:Y-m-d|after_or_equal:today|before:to_date',
                'to_date' => 'required|date_format:Y-m-d|after:from_date',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $resignation_exist = Resignation::where('resignation_employee', $login_user[0]['employee_id'])->where('resignation_status', '!=', 3)->get()->toArray();

                if(!empty($resignation_exist)){

                    return $this->api_response_json(FALSE,"Resignation Already Placed.","",200);
                } 

                $resignation = new Resignation();
                $resignation->resignation_employee = $login_user[0]['employee_id'];
                $resignation->resignation_description = $request->post('reason');
                $resignation->resignation_from_date = $request->post('from_date');
                $resignation->resignation_to_date = $request->post('to_date');
                $resignation->resignation_status = 1;
                
                $process = $resignation->save();

                if($process == 1){

                    if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                        $to_mngr1  = $login_user[0]['employee_ra'];
                    }
                    if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                        $to_mngr2 = $login_user[0]['employee_ra2'];
                    }

                    $this->send_notification_api('Employee Resignation', ''.$login_user[0]['employee_name'].' placed a Resignation. Please click here to view.','resignations?resignation-id='. $resignation->resignation_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                    $this->send_module_email(4,'Employee Resignation', '<b>'.$login_user[0]['employee_name']. '</b> placed a resignation.',''.url('/').'/resignations');

                    return $this->api_response_json(TRUE,"Your Resignation has been submitted. Please wait for response.","",200);

                }else{

                    return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);

                }
            }    
        }catch(\Exception $e){

            $this->create_error_log_from_api('Apply Resignation API', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Apply Resignation API',  $request->header('Bearer-Token'));
        }
    }

}
