<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Employees, EmployeeInformation, LoginHistory, ActivityLog, EmployeeToken, EmployeeDevice, ForgetPassword};
use App\Traits\{LoginEmployee, LogGenerateTrait, ApiResponseTrait, NotificationTrait};
use App\Helpers\{ActivityTracker, CustomHelper};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Mail;

class LoginApiController extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait, NotificationTrait;

    protected function login_process(Request $request){

        try{

            $validator = Validator::make($request->post(), [
                'login_history_longitude' => 'required',
                'login_history_latitude' => 'required',
                'employee_code' => 'required|numeric',
                'employee_password' => 'required',
                'device_token' => 'required',
                'device_name' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){

                    return $this->api_response_json(FALSE,$error,"",200);
                
                }
            }else {

                $filter_user = Employees::join('tbl_designations','tbl_employees.employee_designation','=','tbl_designations.designation_id')->where('employee_code',$request->post('employee_code'))->get(['tbl_employees.*','tbl_designations.designation_name'])->toArray();
                if(!empty($filter_user)){
                    if($filter_user[0]['employee_mobile_access'] == 1){
                        if(Crypt::decryptString($filter_user[0]['employee_password']) == $request->post('employee_password')){
                            if($filter_user[0]['employee_isactive'] == 1){
                                
                                $current_date = strtotime(date('Y-m-d'));
                                $doj = strtotime($filter_user[0]['employee_doj']);
                                if($current_date >=  $doj){       

                                    $welcome_notification = CustomHelper::employee_welcome_status_api($filter_user[0]['employee_id']);
                                    if($welcome_notification == 1){

                                        $this->send_push_notification_mobile_app($filter_user[0]['employee_id'], 'Welcome on MegaHCM Mobile App.','Dear employee, we welcome you on our HCM\'s mobile application. Click here to explore more features in our updated app.');
                                    }
                                
                                    $bearer_token_value = $filter_user[0]['employee_code'].time().rand(1000,9999).bin2hex(random_bytes(2));
                                    
                                    $employee_device_exits = EmployeeDevice::where('device_employee_id','!=',$filter_user[0]['employee_id'])->where('device_status',1)->where('device_token',$request->post('device_token'))->get()->toArray();
                                
                                    if(empty($employee_device_exits)){

                                        $employee_device = EmployeeDevice::where('device_employee_id', $filter_user[0]['employee_id'])->where('device_status',1)->get()->toArray();
                                    
                                        if(empty($employee_device)){

                                            $bearer_token = new EmployeeToken();
                                            $bearer_token->employee_token_emp_id = $filter_user[0]['employee_id'];
                                            $bearer_token->employee_token = $bearer_token_value;
                                            $bearer_token->employee_token_created_at = date('Y-m-d H:i:s');
                                            $bearer_token->save();

                                            $active_device_if_exist = EmployeeDevice::where('device_token',$request->post('device_token'))->where('device_employee_id',$filter_user[0]['employee_id'])->update(['device_status' => 1, 'device_updated_at' => date('Y-m-d H:i:s') ]);
                                        
                                            if($active_device_if_exist != 1){

                                                $add_employee_device = new EmployeeDevice();
                                                $add_employee_device->device_employee_id = $filter_user[0]['employee_id'];
                                                $add_employee_device->device_name = $request->post('device_name');
                                                $add_employee_device->device_token = $request->post('device_token');
                                                $add_employee_device->device_status = 1;
                                                $add_employee_device->device_created_at = date('Y-m-d H:i:s');
                                                $add_employee_device->save();

                                            }
                                            
                                            $employee_device_id = EmployeeDevice::where('device_token',$request->post('device_token'))->where('device_status',1)->get('device_id')->toArray();

                                            $filter_user = $this->filter_array_key($filter_user);
                                            $filter_user[0]['password'] = Crypt::decryptString($filter_user[0]['employee_password']);
                                            unset($filter_user[0]['employee_password']);
                                        
                                            $login_history = new LoginHistory();
                                            $login_history->login_history_employee_id = $filter_user[0]['employee_id'];
                                            $login_history->login_history_session_token = $bearer_token_value;
                                            $login_history->login_history_time = date('Y-m-d H:i:s');
                                            $login_history->login_history_ip = request()->ip();
                                            $login_history->login_history_country = "Pakistan";
                                            $login_history->login_history_city = "Karachi";
                                            $login_history->login_history_latitude = $request->post('login_history_latitude') ?? "40.7044985" ;
                                            $login_history->login_history_longitude = $request->post('login_history_longitude') ?? "-74.0423467";
                                            $login_history->login_history_source = 2;
                                            $login_history->login_employee_device = $employee_device_id[0]['device_id'] ;
                                            $login_history->save();
                                            
                                            $this->deactivate_previous_logins($filter_user[0]['employee_id'], $bearer_token_value);
                                            return $this->api_response_json(TRUE,"Login Successfully",$filter_user,200,['token' => $bearer_token_value ]);

                                        }else{

                                            $employee_device_logged = EmployeeDevice::where('device_token',$request->post('device_token'))->where('device_status',1)->get()->toArray();
                                        
                                            if(!empty($employee_device_logged)){

                                                $bearer_token = new EmployeeToken();
                                                $bearer_token->employee_token_emp_id = $filter_user[0]['employee_id'];
                                                $bearer_token->employee_token = $bearer_token_value;
                                                $bearer_token->employee_token_created_at = date('Y-m-d H:i:s');
                                                $bearer_token->save();

                                                $filter_user = $this->filter_array_key($filter_user);
                                                $filter_user[0]['password'] = Crypt::decryptString($filter_user[0]['employee_password']);

                                                unset($filter_user[0]['employee_password']);

                                                $login_history = new LoginHistory();
                                                $login_history->login_history_employee_id = $filter_user[0]['employee_id'];
                                                $login_history->login_history_session_token = $bearer_token_value;
                                                $login_history->login_history_time = date('Y-m-d H:i:s');
                                                $login_history->login_history_ip = request()->ip();
                                                $login_history->login_history_country = "Pakistan";
                                                $login_history->login_history_city = "Karachi";
                                                $login_history->login_history_latitude = $request->post('login_history_latitude') ?? "40.7044985" ;
                                                $login_history->login_history_longitude = $request->post('login_history_longitude') ?? "-74.0423467";
                                                $login_history->login_history_source = 2;
                                                $login_history->login_employee_device =  $employee_device_logged[0]['device_id'];
                                                $login_history->save();

                                                $this->deactivate_previous_logins($filter_user[0]['employee_id'], $bearer_token_value);
                                                return $this->api_response_json(TRUE,"Login Successfully",$filter_user,200,['token' => $bearer_token_value ]);

                                            }else{
                                                
                                                $message = 'You can login from '.$employee_device[0]['device_name'].' device';
                                                return $this->api_response_json(FALSE,$message,"",200);

                                            }


                                        }
                                    }else{
                                        return $this->api_response_json(FALSE,"You can't login from this device. It's already in use by another user.","",200);
                                    }
        
                                    
                                }else{

                                    return $this->api_response_json(FALSE,"You can't login before your joining date.","",200);

                                }
                            }else{

                                return $this->api_response_json(FALSE,"Your account has been deactivated.","",200);

                            }
                        }else{

                            return $this->api_response_json(FALSE,"Wrong credentials entered. Please try again.","",200);
                        }
                    }else{

                        return $this->api_response_json(FALSE,"Mobile application is not accessible for you.","",200);
                    }    
                }else{

                    return $this->api_response_json(FALSE,"Wrong credentials entered. Please try again.","",200);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log_from_api('Store login employee process', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);
        }
    }

    protected function filter_array_key($filter_user){
        
        unset($filter_user[0]['employee_bank_name'] , $filter_user[0]['employee_iban'] , $filter_user[0]['employee_account_number'] , $filter_user[0]['employee_shift_flexibilty'] , $filter_user[0]['employee_jobtitle'] , $filter_user[0]['employee_status'] , $filter_user[0]['employee_location'] , $filter_user[0]['employee_earning_type'] , $filter_user[0]['employee_cnic'] , $filter_user[0]['employee_deactivation_type']);

        return $filter_user;
    }

    protected function deactivate_previous_logins($employee_id , $employee_login_token){
        EmployeeToken::where('employee_token_emp_id', $employee_id)->where('employee_token','!=',$employee_login_token)->whereNull('employee_token_cancelled_at')->update(['employee_token_cancelled_at' => date('Y-m-d H:i:s')]);
    }

    protected function store_employee_fcm_token(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $validator = Validator::make($request->post(), [
                'employee_fcm_token' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return $this->api_response_json(FALSE,$error,"",200);
                }
            }else {
                $update_fcm_token = Employees::find($login_user[0]['employee_id']);
                $update_fcm_token->employee_fcm_token = $request->post('employee_fcm_token');
                $process = $update_fcm_token->save();
                if($process == 1){

                    return $this->api_response_json(TRUE,"FCM token successfully updated.","",200);

                }else{

                    return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",200);

                }
            }

        }catch(\Exception $e){

            $this->create_error_log_from_api('Store employee FCM token', $e, $request->header('Bearer-Token'));
            return $this->api_response_json(FALSE,"Something went wrong. Please try again.","",500);

        }finally{

            ActivityTracker::track_activity_from_api('Store employee FCM token',  $request->header('Bearer-Token'), $request->header('apk-version'));
        }

    }

    protected function reset_password_api(Request $request){
        try{

            $validator = Validator::make($request->post(), [
                'employee_cnic' => 'required',
                'employee_id' => 'required|numeric',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $employee_id = $request->post('employee_id');
                $cnic = $request->post('employee_cnic');

                $get_employee_detail = Employees::where('employee_code', $employee_id)->where('employee_cnic', $cnic)->where('employee_isactive', '1')->get()->toArray();

                if(!empty($get_employee_detail)){
                    // if($get_employee_detail[0]['employee_work_email'] != ''){
                        // $emailParts = explode('@', $get_employee_detail[0]['employee_work_email']);
                        // if (count($emailParts) === 2) {
                            // $afterAtSymbol = $emailParts[1];
                            // if($afterAtSymbol == 'ismailindustries.com' || $afterAtSymbol == 'ismailindustries.com.pk'){
                                // $data['link'] = 'reset-password-link?xteko=' . Crypt::encryptString($get_employee_detail[0]['employee_id']);
                                // Mail::send('email-template.reset-password',$data,function($message) use ($get_employee_detail) {
                                //     $message->from('iilinnovations@ismailindustries.com', 'IIL Innovations');
                                //     $message->to($get_employee_detail[0]['employee_work_email']);
                                //     $message->subject('Reset Password');
                                // });

                                $update_password = Employees::where('employee_code', $employee_id)->where('employee_cnic', $cnic)->update([
                                    'employee_password' => Crypt::encryptString('123')
                                ]);

                                if($update_password){
                                    $forget_password = new ForgetPassword();
                                    $forget_password->forget_employee_id = $get_employee_detail[0]['employee_id'];
                                    $forget_password->created_at = date('Y-m-d H:i:s');
                                    $forget_password->save();
                                    
                                    return response()->json([
                                        'status' => 'TRUE',
                                        'msg' => "Your password has been reseted to 123.",
                                    ]);
                                }else{
                                    return response()->json([
                                        'status' => 'FALSE',
                                        'msg' => "Somehting went wrong. Please try again.",
                                    ]);
                                }

                                $forget_password = new ForgetPassword();
                                $forget_password->forget_employee_id = $get_employee_detail[0]['employee_id'];
                                $forget_password->created_at = date('Y-m-d H:i:s');
                                $forget_password->save();
                                
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Password reset link has been sent on your email.",
                                ]);
                            // }else{
                            //     return response()->json([
                            //         'status' => 'FALSE',
                            //         'msg' => "Your corporate email isn't mapped in IIL HCM. Please contact HR.",
                            //     ]);
                            // }
                        // }
                    // }else{
                    //     return response()->json([
                    //         'status' => 'FALSE',
                    //         'msg' => "Your corporate email isn't mapped in IIL HCM. Please contact HR.",
                    //     ]);
                    // }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Wrong details entered. No record found.",
                    ]);
                }

            }
        }catch(\Exception $e){
            if(!env('APP_DEBUG')){
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
        }
    }
}
