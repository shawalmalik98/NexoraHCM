<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Crypt, Validator};
use Illuminate\Support\Str;
use App\Models\{Employees, EmployeeInformation, LoginHistory, ActivityLog, MacAddress};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;

class LoginController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "login-history";
    const MAIN_TITLE2 = "login-activity";

    const IP_ADDRESS = ['127.0.0.1','110.39.142.154','113.203.241.197','182.184.117.34','182.176.186.45'];

    protected function login_process_sap(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|min:0',
                'employee_password' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'message' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $ip_address_static = SELF::IP_ADDRESS;
                 
                $get_ip_address = $request->ip();

                // if (in_array($get_ip_address, $ip_address_static)) {
                    
                    //if(!empty($request->input('login_history_longitude')) && !empty($request->input('login_history_latitude'))  ){
                            
                        $filter_user = Employees::where('employee_code',$request->input('employee_id'))->get()->toArray();
                        if(!empty($filter_user)){

                            if (strpos(url()->current(), 'sap-login')) {

                                if(Crypt::decryptString($filter_user[0]['employee_password']) == $request->input('employee_password')){
                                    if($filter_user[0]['employee_isactive'] == 1){
                                        
                                        $current_date  = strtotime(date('Y-m-d'));
                                        $doj  = strtotime($filter_user[0]['employee_doj']);
                                        if($current_date >=  $doj){

                                            $token = Str::random(20);
                                            $token_value = date('hisymd').$token;
                
                                            $request->session()->flush();
                                            $session = $request->session()->put([
                                                'user_session_id' => $filter_user[0]['employee_id'],
                                                'token' => $token_value,
                                                'sap-login' => true, 
                                            ]); 
                                            
                                            $login_history = new LoginHistory();
                                            $login_history->login_history_employee_id = $filter_user[0]['employee_id'];
                                            $login_history->login_history_session_token = session('token');
                                            $login_history->login_history_time = date('Y-m-d H:i:s');
                                            $login_history->login_history_ip = request()->ip();
                                            $login_history->login_history_country = "Pakistan";
                                            $login_history->login_history_city = "Karachi";
                                            $login_history->login_history_latitude = $request->input('login_history_longitude') ?? "40.7044985" ;
                                            $login_history->login_history_longitude = $request->input('login_history_latitude') ?? "-74.0423467";
                                            $login_history->login_history_source = 1;
                                            $login_history->save();
                
                                            return response([
                                                'message' => 'Login successfully',
                                                'status' => 'TRUE',
                                                'sap-login' => true,
                                            ], 200);
                                        }else{
                                            return response([
                                                'message' => "You can't login before your joining date",
                                                'status' => FALSE,
                                                'data' => ''
                                            ], 200);
                                        }
                                    
                                    }else{
                                        return response([
                                            'message' => 'Your account has been deactivated.',
                                            'status' => FALSE,
                                            'data' => ''
                                        ], 200);
                                    }
                                }else{
                                    return response([
                                        'message' => 'Wrong credentials entered. Please try again.',
                                        'status' => FALSE,
                                        'data' => ''
                                    ], 200);
                                }
                        
                            }else{

                                if($filter_user[0]['employee_web_access'] == 1){
                            
                                    if(Crypt::decryptString($filter_user[0]['employee_password']) == $request->input('employee_password')){
                                        if($filter_user[0]['employee_isactive'] == 1){
                                            
                                            $current_date  = strtotime(date('Y-m-d'));
                                            $doj  = strtotime($filter_user[0]['employee_doj']);
                                            if($current_date >=  $doj){
    
                                                $token = Str::random(20);
                                                $token_value = date('hisymd').$token;
                    
                                                $request->session()->flush();
                                                $request->session()->put([
                                                    'user_session' => $filter_user[0]['employee_id'],
                                                    'token' => $token_value,
                                                    'sap-login' => false, 
                                                ]); 

                                                $login_history = new LoginHistory();
                                                $login_history->login_history_employee_id = $filter_user[0]['employee_id'];
                                                $login_history->login_history_session_token = session('token');
                                                $login_history->login_history_time = date('Y-m-d H:i:s');
                                                $login_history->login_history_ip = request()->ip();
                                                $login_history->login_history_country = "Pakistan";
                                                $login_history->login_history_city = "Karachi";
                                                $login_history->login_history_latitude = $request->input('login_history_longitude') ?? "40.7044985" ;
                                                $login_history->login_history_longitude = $request->input('login_history_latitude') ?? "-74.0423467";
                                                $login_history->login_history_source = 1;
                                                $login_history->save();
                    
                                                return response([
                                                    'message' => 'Login successfully',
                                                    'status' => 'TRUE',
                                                    'sap-login' => false,
                                                ], 200);
                                            }else{
                                                return response([
                                                    'message' => "You can't login before your joining date",
                                                    'status' => FALSE,
                                                    'data' => ''
                                                ], 200);
                                            }
                                        
                                        }else{
                                            return response([
                                                'message' => 'Your account has been deactivated.',
                                                'status' => FALSE,
                                                'data' => ''
                                            ], 200);
                                        }
                                    }else{
                                        return response([
                                            'message' => 'Wrong credentials entered. Please try again.',
                                            'status' => FALSE,
                                            'data' => ''
                                        ], 200);
                                    }
                                }else{
                                    return response([
                                        'message' => 'Web application is not accessible for you.',
                                        'status' => FALSE,
                                        'data' => ''
                                    ], 200);
                                }    
                            }
                            
                        }else{
                            return response([
                                'message' => 'Wrong credentials entered. Please try again.',
                                'status' => FALSE,
                                'data' => ''
                            ], 200);
                        }
                    
                    //}
                    // else{

                    //     return response([
                    //         'message' => 'First turn on your web location. Please try again.',
                    //         'status' => FALSE,
                    //         'data' => ''
                    //     ], 200);

                    // }
                // }else{
                //     return response()->json([
                //         'message' => 'You can\'t login from this device.',
                //         'status' => FALSE,
                //         'data' => ''
                //     ], 200);
                // } 
                      
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

    // protected function login_process(Request $request){

    //     try{

    //         $validator = Validator::make($request->all(), [
    //             'employee_id' => 'required|min:0',
    //             'employee_password' => 'required',
    //         ]);

    //         if($validator->fails()) {
    //             foreach($validator->errors()->all() as $error){
    //                 return response()->json([
    //                     'message' => $error,
    //                     'status' => 'FALSE'
    //                 ]);
    //             }
    //         }else {

    //             $ip_address_static = SELF::IP_ADDRESS;
                 
    //             $get_ip_address = $request->ip();

    //             // if (in_array($get_ip_address, $ip_address_static)) {
                    
    //                 //if(!empty($request->input('login_history_longitude')) && !empty($request->input('login_history_latitude'))  ){
                            
    //                     $filter_user = Employees::where('employee_code',$request->input('employee_id'))->get()->toArray();
    //                     if(!empty($filter_user)){

    //                         if($filter_user[0]['employee_web_access'] == 1){
                            
    //                             if(Crypt::decryptString($filter_user[0]['employee_password']) == $request->input('employee_password')){
    //                                 if($filter_user[0]['employee_isactive'] == 1){
                                        
    //                                     $current_date  = strtotime(date('Y-m-d'));
    //                                     $doj  = strtotime($filter_user[0]['employee_doj']);
    //                                     if($current_date >=  $doj){
                                        
    //                                         $token = Str::random(20);
    //                                         $token_value = date('hisymd').$token;
                
    //                                         $request->session()->flush();
    //                                         $request->session()->put([
    //                                             'user_session'=>$filter_user[0]['employee_id'],
    //                                             'token' => $token_value,
    //                                         ]);
                                            
    //                                         $login_history = new LoginHistory();
    //                                         $login_history->login_history_employee_id = $filter_user[0]['employee_id'];
    //                                         $login_history->login_history_session_token = session('token');
    //                                         $login_history->login_history_time = date('Y-m-d H:i:s');
    //                                         $login_history->login_history_ip = request()->ip();
    //                                         $login_history->login_history_country = "Pakistan";
    //                                         $login_history->login_history_city = "Karachi";
    //                                         $login_history->login_history_latitude = $request->input('login_history_longitude') ?? "40.7044985" ;
    //                                         $login_history->login_history_longitude = $request->input('login_history_latitude') ?? "-74.0423467";
    //                                         $login_history->login_history_source = 1;
    //                                         $login_history->save();
                
    //                                         return response([
    //                                             'message' => 'Login successfully',
    //                                             'status' => 'TRUE',
    //                                         ], 200);
    //                                     }else{
    //                                         return response([
    //                                             'message' => "You can't login before your joining date",
    //                                             'status' => FALSE,
    //                                             'data' => ''
    //                                         ], 200);
    //                                     }
                                    
    //                                 }else{
    //                                     return response([
    //                                         'message' => 'Your account has been deactivated.',
    //                                         'status' => FALSE,
    //                                         'data' => ''
    //                                     ], 200);
    //                                 }
    //                             }else{
    //                                 return response([
    //                                     'message' => 'Wrong credentials entered. Please try again.',
    //                                     'status' => FALSE,
    //                                     'data' => ''
    //                                 ], 200);
    //                             }
    //                         }else{
    //                             return response([
    //                                 'message' => 'Web application is not accessible for you.',
    //                                 'status' => FALSE,
    //                                 'data' => ''
    //                             ], 200);
    //                         }    
    //                     }else{
    //                         return response([
    //                             'message' => 'Wrong credentials entered. Please try again.',
    //                             'status' => FALSE,
    //                             'data' => ''
    //                         ], 200);
    //                     }
                    
    //                 //}
    //                 // else{

    //                 //     return response([
    //                 //         'message' => 'First turn on your web location. Please try again.',
    //                 //         'status' => FALSE,
    //                 //         'data' => ''
    //                 //     ], 200);

    //                 // }
    //             // }else{
    //             //     return response()->json([
    //             //         'message' => 'You can\'t login from this device.',
    //             //         'status' => FALSE,
    //             //         'data' => ''
    //             //     ], 200);
    //             // } 
                      
    //         }
    //     }catch(\Exception $e){
    //         if(!env('APP_DEBUG')){
    //             return response()->json([
    //                 'status' => 'FALSE',
    //                 'msg' => "Something went wrong. Please try again.",
                   
    //             ]);
    //         }else{
    //             return response()->json([
    //                 'status' => 'FALSE',
    //                 'msg' => "Something went wrong. Please try again.",
    //                 'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
    //             ]);
               
    //         }
    //     }
    // }

    protected function login_history(Request $request){
        try{
           
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $where_clause = '1=1 ';
            if(!empty($request->history_date) || !empty($request->history_records) ){

               if(!empty($request->history_date) && empty($request->history_records)){
                    $history_date = date("Y-m-d", strtotime($request->history_date));
                    $where_clause .= " AND login_history_time LIKE '%". $history_date ."%'";
               }

               if(!empty($request->history_records)){
                    if($request->history_records == "7"){
                        $lastdays = date('Y-m-d', strtotime('-7 days'));
                    }elseif($request->history_records == "15"){
                        $lastdays = date('Y-m-d', strtotime('-15 days'));
                    }else{
                        $lastdays = date('Y-m-d', strtotime('-30 days'));
                    } 
                    $where_clause = "login_history_time >= '" .$lastdays. "'";
                }

                ($where_clause) ?? '';
 
                $history = LoginHistory::join('tbl_employees','tbl_employees.employee_id', '=', 'tbl_login_history.login_history_employee_id')
                ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->whereRaw($where_clause)
                ->orderBy('tbl_login_history.login_history_id', 'DESC')
                ->get(['employee_code','employee_name','login_history_time','login_history_ip','login_history_country','login_history_city','login_history_latitude','login_history_longitude','login_history_source','login_employee_device','login_history_session_token','department_name','designation_name'])->toArray();

            }else{

                $where_clause .= " AND login_history_time LIKE '%".date("Y-m-d")."%'";

                $history = LoginHistory::join('tbl_employees','tbl_employees.employee_id', '=' , 'tbl_login_history.login_history_employee_id')
                ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->whereRaw($where_clause)
                ->orderBy('tbl_login_history.login_history_id', 'DESC')
                ->get(['employee_code','employee_name','login_history_time','login_history_ip','login_history_country','login_history_city','login_history_latitude','login_history_longitude','login_history_source','login_employee_device','login_history_session_token','department_name','designation_name'])->toArray();
                
            }
            $send_data = compact('title','login_user','history');

            return view('login-history.login-history', $send_data );
        }catch(\Exception $e){

            $this->create_error_log('View All Employees Login History Page', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('View All Employees Login History Page');
          
        }
    }

    protected function login_user_activity($loginID){
        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $history = LoginHistory::join('tbl_employees','tbl_employees.employee_id', '=' , 'tbl_login_history.login_history_employee_id')
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id' )
            ->leftjoin('tbl_employees_devices', 'tbl_employees_devices.device_id', '=', 'tbl_login_history.login_employee_device')
            ->where('login_history_session_token', '=', $loginID )
            ->orderBy('tbl_login_history.login_history_id', 'DESC')
            ->get(['employee_id','employee_code','employee_name','department_name','designation_name','device_name','login_history_source','login_history_latitude','login_history_longitude','login_history_employee_id'])
            ->toArray();
            
            $image = EmployeeInformation::where('employeeid', $history[0]['login_history_employee_id'])->get()->toArray();

            $activity = ActivityLog::where('activity_login_history_id', $loginID)->orderBy('tbl_activity_log.activity_id','DESC')->get();

            $send_data = compact('title','login_user','activity','history','image');

            return view('login-history.session-activity', $send_data);
            
        }catch(\Exception $e){

            $this->create_error_log('View Login Employee Activity Page', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('View Login Employee Activity Page');
           
        }

    }

    protected function proxy_user(Request $request){
        try{

            $login_user = $this->get_login_user_detail();

            if((!empty($login_user[0]['employee_department']) && $login_user[0]['employee_department'] == 1) || session('proxy_user')=="TRUE" ){
                
                // if(!empty($request->input('login_history_longitude')) && !empty($request->input('login_history_latitude'))  ){

                    $request->session()->flush();
                   

                    $token = Str::random(20);
                    $token_value = date('hisymd').$token;

                    $request->session()->put(array(
                    'user_session'=>$request->proxy_by_name,
                    'proxy_user'=>"TRUE",
                    'token' => $token_value,
                    ));


                    $login_history = new LoginHistory();
                    $login_history->login_history_employee_id = $request->proxy_by_name;
                    $login_history->login_history_session_token = session('token');
                    $login_history->login_history_time = date('Y-m-d H:i:s');
                    $login_history->login_history_ip = request()->ip();
                    $login_history->login_history_country = "Pakistan";
                    $login_history->login_history_city = "Karachi";
                    $login_history->login_history_latitude = $request->input('login_history_longitude') ?? "40.7044985" ;
                    $login_history->login_history_longitude = $request->input('login_history_latitude') ?? "-74.0423467";
                    $login_history->login_history_source = 3;
                    $login_history->save();

                    return response([
                        'msg' => 'Proxy login successfully please wait',
                        'status' => "TRUE",
                    ], 200);
                // }else{

                //     return response([
                //         'msg' => 'First turn on your web location. Please try again.',
                //         'status' => "FALSE",
                //     ], 200);
                // }                

            }else{
                
                return response([
                    'msg' => 'You don\'t have the access',
                    'status' => "FALSE",
                ], 200);

            }
            
        }catch(\Exception $e){
            $this->create_error_log('Proxy Login', $e);
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