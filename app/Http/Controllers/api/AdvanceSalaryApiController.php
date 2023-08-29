<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\{AdvanceSalary, Employees, EmployeeSalary, RequestApproval};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, ApiResponseTrait, AttendanceEmployee};
use App\Helpers\ActivityTracker;

class AdvanceSalaryApiController extends Controller
{
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait, AttendanceEmployee;

    const TITLE_GLOBAL2   = "my-salary-request";

    protected function my_salary_request(Request $request){

        try{
            $title = Self::TITLE_GLOBAL2;

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            
            $my_requests = AdvanceSalary::where('employee_id' , $login_user[0]['employee_id'])->orderBy('advance_salary_request_id', 'DESC')->get()->toArray();
          
            $advsalary_request_id = array_column($my_requests,'advance_salary_request_id');

            $responsed_request = RequestApproval::where('request_module_id', 4)->whereIn('request_id', $advsalary_request_id)->pluck('request_id')->toArray();
            
            $employee_salary = EmployeeSalary::where('salary_employee_id' , $login_user[0]['employee_id'])->where('salary_effected_date', '<=' , date('Y-m-d'))->orderBy('salary_id','DESC')->limit(1)->get('salary_amount')->toArray();

            $data = compact('title','my_requests' ,'employee_salary','responsed_request');

            return $this->api_response_json(TRUE,"My Advance Salary Request",$data,200);
        
        }catch(\Exception $e){

            $this->create_error_log_from_api('Access Request for Advance Salary Module API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }
        finally{

            ActivityTracker::track_activity_from_api('View Access Request for Advance Salary Module API',  $request->header('Bearer-Token'));

        }
    }

    protected function requestfor_advance_salary(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
                      
            $deduction_month =  date('m')-1;

            if($deduction_month == '-1'){

                $deduction_month = 11;

            }elseif($deduction_month == '0'){

                $deduction_month = 12;

            }

            $employee_salary = EmployeeSalary::where('salary_employee_id' , $login_user[0]['employee_id'])->where('salary_effected_date', '<=' , date('Y-m-d'))->orderBy('salary_id','DESC')->limit(1)->get('salary_amount')->toArray();

            $message = ['before'=>'The payback date must be a date before ' .date( 'F-Y',strtotime('+2 month')),'after'=>'The payback date must be a date after '.date( 'F-Y',strtotime(date('Y-'.$deduction_month.'')))];

            $validator = Validator::make($request->post(), [
                'advance_salary' => 'required|min:1|max:'.$employee_salary[0]['salary_amount'].'|numeric',
                'payback_date' => 'required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-'.$deduction_month.'').'',
                'message' => 'required', 

            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $checking_request = AdvanceSalary::where('employee_id', $login_user[0]['employee_id'])->where('effective_payroll_period', $request->post('payback_date'))->where('request_approved_by_finance', 1)->get(['advance_salary_request_id'])->toArray();
                
                $payroll_exists = $this->check_if_payroll_exist($request->post('payback_date'),$login_user[0]['employee_id'],$custom_message = NULL);

                if($payroll_exists['status'] == TRUE){

                    return $this->api_response_json(FALSE,$payroll_exists['msg'],"",200);
                }

                $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($request->post('payback_date'));

                if($check_attendance_sheet_availability['status'] == FALSE){
                    return $this->api_response_json(FALSE,$check_attendance_sheet_availability['msg'],"",200);
                }
                
                if(empty($checking_request)){
                    $salary_request = new AdvanceSalary();
                    $salary_request->employee_id = $login_user[0]['employee_id'];
                    $salary_request->effective_payroll_period = $request->post('payback_date');
                    $salary_request->advance_salary_amount = $request->post('advance_salary');
                    $salary_request->request_message = $request->post('message');
                    $salary_request->request_approved_by_finance = 1;
                    $salary_request->request_created_at = date('d-m-Y H:i:s');
                    $process = $salary_request->save();
                    if($process == 1){

                        if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                            $to_mngr1  = $login_user[0]['employee_ra'];
                        }
                        if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                            $to_mngr2 = $login_user[0]['employee_ra2'];
                        }
        
                        $this->send_notification_api('New Advance Salary Request',''.$login_user[0]['employee_name'].' submitted a Salary request. Please click here to view.','all-salary-request?request-id='. $salary_request->advance_salary_request_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes',$login_user[0]['employee_id']);

                        return $this->api_response_json(TRUE,"Advance salary request has been submitted. Please wait for response.","",200);

                    }else{

                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);

                    }
                }else{

                    $message =  "You have already submitted an advance salary request for the month of ".date('F-Y',strtotime($request->post('payback_date')));

                    return $this->api_response_json(FALSE,$message,"",200);
                }
            }    
        }catch(\Exception $e){

            $this->create_error_log_from_api('Apply Advance Salary Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Access Request for Advance Salary Module API',  $request->header('Bearer-Token'));
        }

    }

    protected function update_advance_salary_request(Request $request){
       
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('request_id'),4,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Advance salary request id is invalid.","",200);
            }

            $deduction_month =  date('m')-1;

            if($deduction_month == '-1'){

                $deduction_month = 11;

            }elseif($deduction_month == '0'){

                $deduction_month = 12;

            }

            $employee_salary = EmployeeSalary::where('salary_employee_id' , $login_user[0]['employee_id'])->where('salary_effected_date', '<=' , date('Y-m-d'))->orderBy('salary_id','DESC')->limit(1)->get('salary_amount')->toArray();

            $message = ['before'=>'The payback date must be a date before ' .date( 'F-Y',strtotime('+2 month')),'after'=>'The payback date must be a date after '.date( 'F-Y',strtotime(date('Y-'.$deduction_month.'')))];
            
            $validator = Validator::make($request->post(), [
                'advance_salary' => 'required|min:1|max:'.$employee_salary[0]['salary_amount'].'|numeric',
                'payback_date' => 'required|date_format:Y-m|before:'. date('Y-m',strtotime('+2 month')).'|after:'. date('Y-'.$deduction_month.'').'',
                'message' => 'required', 

            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {

                $advancesalary_update_request = AdvanceSalary::find($request->post('request_id'));

                if(!empty($advancesalary_update_request)){

                    $checking_request_responsed = RequestApproval::where('request_module_id',4)->where('request_id',$request->post('request_id'))->get('request_approval_id')->toArray();
                
                    if(empty($checking_request_responsed) && $advancesalary_update_request->request_approved_by_finance == 1){

                        $checking_request = AdvanceSalary::where('employee_id', $login_user[0]['employee_id'])->where('effective_payroll_period',$request->post('payback_date'))->where('advance_salary_request_id','!=',$request->post('request_id'))->get(['advance_salary_request_id'])->toArray();
    
                        if(empty($checking_request)){
                        
                            $advancesalary_update_request->effective_payroll_period = $request->post('payback_date');
                            $advancesalary_update_request->advance_salary_amount = $request->post('advance_salary');
                            $advancesalary_update_request->request_message = $request->post('message');
                            $advancesalary_update_request->request_created_at = date('d-m-Y H:i:s');
                        
                            $process =  $advancesalary_update_request->save();
    
                            if( $process == 1){
                                if(!empty($login_user[0]['employee_ra']) || $login_user[0]['employee_ra'] != ''){
                                    $to_mngr1  = $login_user[0]['employee_ra'];
                                }
                                if(!empty($login_user[0]['employee_ra2']) || $login_user[0]['employee_ra2'] != ''){
                                    $to_mngr2 = $login_user[0]['employee_ra2'];
                                }
                
                                $this->send_notification_api('Update Advance Salary Request',''.$login_user[0]['employee_name'].' updated an advance salary request. Please click here to view.','all-salary-request?request-id='.$advancesalary_update_request->advance_salary_request_id .'',NULL,$to_mngr1= $to_mngr1 ?? NULL,$to_mngr2=$to_mngr2 ?? NULL,'yes', $login_user[0]['employee_id']);

                                return $this->api_response_json(TRUE,"Advance Salary request has been updated. Please wait for response.","",200);

                            }else{

                                return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);
                            }
                            
                        }else{

                            $message =  "You have already submitted an advance salary request for the month of ".date('F-Y',strtotime($request->post('payback_date')));

                            return $this->api_response_json(FALSE,$message,"",200);

                        }
                    }else{

                        return $this->api_response_json(FALSE,"You can't edit this request now because your reporting authority has changed its status.","",200);
                    }
                }else{

                    return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
                }
            }   
        }catch(\Exception $e){

            $this->create_error_log_from_api('Update Advance Salary Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }

        finally{

            ActivityTracker::track_activity_from_api('Update Advance Salary Request API',  $request->header('Bearer-Token'));            
        }
        
    }

    protected function delete_advance_salary_request(Request $request){
      
        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            if($this->check_request_validation($request->post('advance_salary_request_id'),4,$login_user[0]['employee_id']) == TRUE){

                return $this->api_response_json(FALSE,"Advance salary request id is invalid.","",200);
            }

            $get_salary_request = AdvanceSalary::find($request->post('advance_salary_request_id'));
            if(!empty($get_salary_request)){

                $checking_request_responsed = RequestApproval::where('request_module_id',4)->where('request_id',$request->post('advance_salary_request_id'))->get('request_approval_id')->toArray();
                
                if(empty($checking_request_responsed) && $get_salary_request->request_approved_by_finance == 1){

                    $process = AdvanceSalary::where('advance_salary_request_id', $request->post('advance_salary_request_id'))->delete();
                    if( $process == 1){

                        return $this->api_response_json(TRUE,"Advance Salary request has been deleted.","",200);
                
                    }else{

                        return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
                    }
                         
                }else{

                    return $this->api_response_json(FALSE,"You can't delete this request now because your reporting authority has changed its status.","",200);
                }
            }else{

                return $this->api_response_json(FALSE,"Request not found on this ID.","",200);
            }

        }catch(\Exception $e){

            $this->create_error_log_from_api('Delete Advance Salary Request API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);
        }
        finally{

            ActivityTracker::track_activity_from_api('Delete Advance Salary Request API',  $request->header('Bearer-Token'));
        }
    }

}