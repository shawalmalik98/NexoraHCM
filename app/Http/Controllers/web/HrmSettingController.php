<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{HrmSetting, MacAddress, EmailConfiguration,Employees,LeaveManagement, SalaryDivision, EmailEndPoint, EmailRecipient};
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Validator;
use App\Traits\{LoginEmployee, NotificationTrait, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class HrmSettingController extends Controller
{
    use LoginEmployee, NotificationTrait, LogGenerateTrait;

    const MAIN_TITLE = "system-settings";

    protected function index(){
       
        try{

            $title = Self::MAIN_TITLE;
        
            $login_user = $this->get_login_user_detail();

            $setting = HrmSetting::orderBy('setting_id','DESC')->get()->toArray();

            $mac_address = MacAddress::get()->toArray();

            $get_leave_managements = LeaveManagement::get()->toArray();
            
            $email_config = EmailConfiguration::get()->toArray();

            $salary_division = SalaryDivision::get()->toArray();

            $email_endpoints = EmailEndPoint::get()->toArray();

            $operation_manager = Employees::where('employee_isactive',1)->get(['employee_id','employee_name'])->toArray();

            $email_recipient = EmailRecipient::join('tbl_email_endpoints','tbl_email_recipients.endpoint_id','=','tbl_email_endpoints.endpoints_id')->get(['endpoint_desc','receiever_id','recipients_created_by','recipient_id','recipients_created_at','recipient_type','endpoint_id'])->toArray();
            $hr_employees = Employees::where('employee_isactive',1)->where('employee_department',2)->get(['employee_id','employee_name'])->toArray();
          
            $finance_employees = Employees::where('employee_isactive',1)->get(['employee_id','employee_name'])->toArray();

            if(!empty($setting) || !empty($mac_address) ){
                $send_data = compact('title','login_user','setting','mac_address','email_config','get_leave_managements','salary_division','email_endpoints','operation_manager','email_recipient','hr_employees','finance_employees');
            }else{
                $send_data = compact('title','login_user');
            }

            return view('miscellanous.hrm-settings', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View Hrm Settings', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Hrm Settings');
         
        }
    }

    protected function create(Request $request){

        try{
        
            $validator = Validator::make($request->all(), [
                'company_logo' => ['max:10000', File::types(['png', 'jpg','jpeg','PNG','JPG','JPEG'])],
                'company_favicon' => ['max:10000', File::types(['png', 'jpg','jpeg','PNG','JPG','JPEG'])],
                'signature_on_payslip' => ['max:10000', File::types(['png', 'jpg','jpeg','PNG','JPG','JPEG'])],
                'company_name' =>  'required',
                'company_email' =>  'required|email',
                'company_address' => 'required',
                'company_address' => 'required',
                'check_in_grace_time'=>'required|min:0|numeric',
                'check_out_grace_time'=>'required|min:0|numeric',
                'mac_address' => 'required',
                'daily_wages_late_deduction' => 'min:0|max:100|integer|nullable',
                'daily_wages_half_deduction' => 'min:0|max:100|integer|nullable',
                'email' => 'required_with:password,mailer,host,email_port,email_encryption,from_address,from_name',
                'password' => 'required_with:email,mailer,host,email_port,email_encryption,from_address,from_name',
                'mailer' => 'required_with:email,password,host,email_port,email_encryption,from_address,from_name',
                'host' => 'required_with:email,password,mailer,email_port,email_encryption,from_address,from_name',
                'email_port' => 'required_with:email,password,mailer,host,email_encryption,from_address,from_name',
                'email_encryption' => 'required_with:email,password,mailer,host,email_port,from_address,from_name',
                'from_address' => 'required_with:email,password,mailer,host,email_port,email_encryption,from_name',
                'from_name' => 'required_with:email,password,mailer,host,email_port,email_encryption,from_address',
                'leave_yearly_allotment.*' => 'required|numeric|min:0|max:100',
                'leave_manage_id.*'     => 'required|numeric',
                'salary_division_title.*'     => 'required',
                'salary_division_percentage.*' => 'required|numeric|min:0|max:100',
                'onboarding_email_name' => 'string|max:50|nullable',
                'system_currency'   => 'required|string',
                
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{

                $login_user = $this->get_login_user_detail();

                $salary_divisions = array_sum($request->input('salary_division_percentage'));

                if(array_sum($request->input('salary_division_percentage')) > 100) {
                    return response()->json([
                        'msg' => 'Salary division percentage should not be greater than 100',
                        'status' => 'FALSE'
                    ]);
                }

                $salary_title = array_count_values(array_map('strtolower',$request->input('salary_division_title')));
               
                if(max(array_values($salary_title)) > 1){
                    return response()->json([
                        'msg' => 'Title must be unique',
                        'status' => 'FALSE'
                    ]);
                }

                $address = array_count_values($request->input('mac_address'));
               
                if(max(array_values($address)) > 1){
                    return response()->json([
                        'msg' => 'Ip address must be unique',
                        'status' => 'FALSE'
                    ]);
                }

                if($request->input('mailer') == '' && ($request->input('email_permission') == '1' || $request->input('push_notification_permission') == '1')){
                    return response()->json([
                        'msg' => 'Without email configuration not checked the email or fcm alert check.',
                        'status' => 'FALSE'
                    ]);
                }

                if(!is_dir('setting/images/')){
                    mkdir('setting/images/', 0777,true);
                }
    
                $add_hrm_setting = HrmSetting::find($request->input('setting_id'));
                
                $old_email_permission = $add_hrm_setting->company_email_permission;
                $old_push_notification_permission = $add_hrm_setting->push_notification_permission;
                if($request->hasFile('company_logo')){
                    $logo = $request->file('company_logo');
                    $company_logo = $logo->getClientOriginalName();
                    $logo->move('portal_assets/setting/images/',$company_logo);
    
                    $add_hrm_setting->company_logo = $company_logo;
                }
                
                if($request->hasFile('company_favicon')){
                    $favicon = $request->file('company_favicon');
                    $company_favicon = $favicon->getClientOriginalName();
                    $favicon->move('portal_assets/setting/images/',$company_favicon);
    
                    $add_hrm_setting->company_favicon = $company_favicon;
                }

                if($request->hasFile('signature_on_payslip')){
                    $signature = $request->file('signature_on_payslip');
                    $signature_on_payslip = $signature->getClientOriginalName();
                    $signature->move('portal_assets/setting/images/',$signature_on_payslip);
    
                    $add_hrm_setting->signature_on_payslip = $signature_on_payslip;
                }
    
                if(empty($add_hrm_setting->payroll_start_date)){
    
                    $validator = Validator::make($request->all(), [
                        'payroll_start_date' =>  'required|min:1|max:28|numeric',
                    ]);
                    
                  
                    if($validator->fails()) {
                        foreach($validator->errors()->all() as $error){
                            return response()->json([
                                'msg' => $error,
                                'status' => 'FALSE'
                            ]);
                        }
                    }
                    $payroll_start_date = $request->input('payroll_start_date');
                    
                }else{
                   $payroll_start_date =  $add_hrm_setting->payroll_start_date;
                }
                
    
                $add_hrm_setting->company_name   = $request->input('company_name');
                $add_hrm_setting->company_email   = $request->input('company_email');
                $add_hrm_setting->company_address  = $request->input('company_address');
                $add_hrm_setting->company_email_permission = $request->input('email_permission');
                $add_hrm_setting->push_notification_permission = $request->input('push_notification_permission');
                $add_hrm_setting->check_in_flexibility_time = $request->input('check_in_grace_time');
                $add_hrm_setting->check_out_flexibility_time = $request->input('check_out_grace_time');
                $add_hrm_setting->payroll_start_date =  $payroll_start_date;
                $add_hrm_setting->fiscal_date =  $request->input('fiscal_date');
                $add_hrm_setting->club_rule_apply  = $this->checkbox_is_active($request->input('club_rule'));
                $add_hrm_setting->tax_deduction_apply  = $this->checkbox_is_active($request->input('tax_deduction'));
                $add_hrm_setting->web_attendance =  $request->input('web_attendance');
                $add_hrm_setting->mobile_attendance =  $request->input('mobile_attendance');
                $add_hrm_setting->name_on_payslip =  $request->input('name_on_payslip');
                $add_hrm_setting->daily_wages_late_deduction = (!empty($request->input('daily_wages_late_deduction'))) ? $request->input('daily_wages_late_deduction')/100 : '';
                $add_hrm_setting->daily_wages_half_deduction = (!empty($request->input('daily_wages_half_deduction'))) ? $request->input('daily_wages_half_deduction')/100 : '';
                $add_hrm_setting->annual_leave_display = $this->checkbox_is_active($request->input('annual_leave_allow'));
                $add_hrm_setting->total_leave_all_leaves_visibility = $this->checkbox_is_active($request->input('all_leave_visibilty_allow'));
                $add_hrm_setting->onboarding_email_name = $request->input('onboarding_email_name');
                $add_hrm_setting->system_currency   =  $request->input('system_currency');
                $process = $add_hrm_setting->save();
                
                if($process == 1){
                    MacAddress::truncate();
                    
                    foreach($request->input('mac_address') as $mac_address){
                        $mac = new MacAddress();
                        $mac->mac_address = $mac_address;
                        $mac->mac_address_added_date =  date('d-m-Y H:i:s');
                        $mac->save();
                    }

                    foreach($request->leave_manage_id as $key=>$manage_leave){
                    
                        $leave_names = LeaveManagement::where('leave_manage_id',$manage_leave)->get('leave_name')->toArray();
                        
                        if(!empty($leave_names)){
                            
                            $leave = LeaveManagement::updateOrCreate([
                                'leave_manage_id' =>  $manage_leave
                            ], [
                                'leave_yearly_allotment' => $request->leave_yearly_allotment[$key],
                              
                            ]);
                        }
                       
                    }
                    
                    SalaryDivision::truncate();

                    $i = 0;
                    foreach($request->input('salary_division_title') as $salary_title){
                        $salary_division = new SalaryDivision();
                        $salary_division->salary_division_title = $salary_title;
                        $salary_division->salary_division_percentage = $request->input('salary_division_percentage')[$i];
                        $salary_division->save();
                        $i++;
                    }    

                    EmailConfiguration::truncate();

                    if(!empty($request->input('email'))){
                        
                        if($old_email_permission == $request->input('email_permission')){
                            HrmSetting::where('company_email_permission',0)->update(['company_email_permission'=>1]);
                       
                        }
                        if($old_push_notification_permission == $request->input('push_notification_permission')){
                            HrmSetting::where('push_notification_permission',0)->update(['push_notification_permission'=> 1]);
                        }
                       
                        $email_config = new EmailConfiguration();
                        $email_config->email_config_mailer = $request->input('mailer');
                        $email_config->email_config_host = $request->input('host');
                        $email_config->email_config_email = $request->input('email');
                        $email_config->email_config_pass = $request->input('password');
                        $email_config->email_config_port = $request->input('email_port');
                        $email_config->email_config_encryption = $request->input('email_encryption');
                        $email_config->email_config_from_address = $request->input('from_address');
                        $email_config->email_config_from_name = $request->input('from_name');
                        $email_config->email_config_created_at = date('Y-m-d H:i:s');
                        $email_config->email_config_updated_at = date('Y-m-d H:i:s');
                        $email_config->save();
                    }

                    $executive_employees = Employees::where('employee_isactive',1)->where('employee_department',1)->get('employee_id')->toArray();
                    foreach($executive_employees as  $executive_employee){

                        $this->send_notification('System Setting Updated',''.$login_user[0]["employee_name"].' update system settings. Please click here to view.','system-settings',$executive_employee['employee_id'],NULL,NULL,'no');
                    }
                   
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "System settings updated successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "System settings not updated successfully.",
                    ]);
                }
            }
       
        }catch(\Exception $e){
            $this->create_error_log('Add Hrm Settings', $e);

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
        finally{
            ActivityTracker::track_activity('Add Hrm Settings');
        } 
    }

    protected function delete_salary_division(Request $request){

		try{

            SalaryDivision::where('salary_division_id',$request->salary_division_id)->delete();
            
        }catch(\Exception $e){
            $this->create_error_log("Delete Salary Division", $e);

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
        }finally{
		    ActivityTracker::track_activity('Delete Salary Division');
            return redirect('system-settings');
        }
    }

    protected function delete_mac_address(Request $request){

		try{

            MacAddress::where('address_id',$request->address_id)->delete();
            
        }catch(\Exception $e){
            $this->create_error_log("Delete Mac Address", $e);

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
        }finally{
		    ActivityTracker::track_activity('Delete Mac Address');
            return redirect('system-settings');
        }
    }

    protected function delete_email_access_module_employee(Request $request){
      
		try{
          
            if(!empty($request->recipient_type == 'to')){
                $endpoint = EmailRecipient::where('endpoint_id',$request->endpoint_id)->where('recipient_type','to')->count();
                if($endpoint > 1){
                    EmailRecipient::where('recipient_id',$request->recipient_id)->delete();
                }else{
                    $get_recipent_id = EmailRecipient::where('endpoint_id',$request->endpoint_id)->pluck('recipient_id')->toArray();     
                    EmailRecipient::where('endpoint_id',$request->endpoint_id)->delete();
                    return response()->json([
                        'status' => 'TRUE',
                        'recipents_id' => $get_recipent_id,
                    ]);
                }

            }else{
                EmailRecipient::where('recipient_id',$request->recipient_id)->delete();
                 
            } 
        }catch(\Exception $e){
            $this->create_error_log("Email Recipient Delete", $e);

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
        }finally{
		    ActivityTracker::track_activity('Email Recipient Delete');
           
        }
    }

    protected function send_test_mail(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'receiving_mail' =>  'required|email',
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            Mail::raw('Hi, welcome user! this is testing mail', function ($message) use ($request) {
                $message->to($request->input('receiving_mail'))->subject('Testing Mail');
            });

            return response()->json([
                'status' => 'TRUE',
                'msg' => "Testing mail sent successfully",
            ]);

        }catch(\Exception $e){
            $this->create_error_log('Sending Test Mail', $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => $e->getMessage(),
            ]);
        }
        finally{
            ActivityTracker::track_activity('Sending Test Mail');
        }

    }
    
    protected function add_email(Request $request){
       
        try{
           
           
            $validator = Validator::make($request->all(), [
                'email_module' =>  'required',
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            $check_exist_endpoint = EmailRecipient::whereIn('endpoint_id',$request->email_module)->where('recipient_type','to')->pluck('endpoint_id')->toArray();

            if(!in_array(implode(',',$request->email_module),$check_exist_endpoint) && empty($request->input('email_reciever'))){

                $not_matching_values = array_merge(
                    array_diff($request->email_module, $check_exist_endpoint),
                 
                );
                $endpoint_name = EmailEndPoint::whereIn('endpoints_id',$not_matching_values)->pluck('endpoint_desc')->toArray();

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "You can't add cc reciepient until to reciepient added. ". implode(',',$endpoint_name),
                ]);
            }

            foreach($request->input('email_module') as $email_module){
                if(!empty($request->input('email_reciever'))){
                    foreach($request->input('email_reciever') as $employee){

                        if(!empty($request->input('cc_recipients'))){
                            $to_recipient_employee = [$employee];

                            (!empty($request->input('cc_recipients'))) ? array_push($to_recipient_employee,$request->input('cc_recipients')) : '';
                
                            if(in_array($employee, $request->input('cc_recipients'))){
                
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "You have assigned same employee for both to and cc reciepents. Please select different.",
                                ]);
                            }
                        }
                    
                        $check_exist = EmailRecipient::where('endpoint_id',$email_module)->where('receiever_id',$employee)->where('recipient_type','to')->get('recipient_id')->toArray();

                        $check_exist_cc = EmailRecipient::where('endpoint_id',$email_module)->where('receiever_id',$employee)->where('recipient_type','cc')->get('endpoint_id')->toArray();

                        if(empty($check_exist) && empty($check_exist_cc)){
                            $add_rcipient = new EmailRecipient();
                            $add_rcipient->endpoint_id = $email_module;
                            $add_rcipient->receiever_id = $employee;
                            $add_rcipient->recipient_type = 'to';
                            $add_rcipient->recipients_created_by = session('user_session');
                            $add_rcipient->recipients_created_at = date('Y-m-d H:i:s');
                            $add_rcipient->save();
                        }
                        
                    }
                }
                if(!empty($request->input('cc_recipients'))){

                    foreach($request->input('cc_recipients') as $employee){
                        $check_exist = EmailRecipient::where('endpoint_id',$email_module)->where('receiever_id',$employee)->where('recipient_type','cc')->get('recipient_id')->toArray();

                        $check_exist_to = EmailRecipient::where('endpoint_id',$email_module)->where('receiever_id',$employee)->where('recipient_type','to')->get('endpoint_id')->toArray();

                        if(empty($check_exist) && empty($check_exist_to)){
                            $add_rcipient = new EmailRecipient();
                            $add_rcipient->endpoint_id = $email_module;
                            $add_rcipient->receiever_id = $employee;
                            $add_rcipient->recipient_type = 'cc';
                            $add_rcipient->recipients_created_by = session('user_session');
                            $add_rcipient->recipients_created_at = date('Y-m-d H:i:s');
                            $add_rcipient->save();
                        }
                    }
                }
            }
            return response()->json([
                'status' => 'TRUE',
                'msg' => "Email recipient(s) added successfully.",
            ]);
        }catch(\Exception $e){
            $this->create_error_log('Endpoint recipient(s) addition', $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => $e->getMessage(),
            ]);
        }
        finally{
            ActivityTracker::track_activity('Endpoint recipient(s) addition');
        }
    }

    public static function checkbox_is_active($value){
        if($value == '1'){
            return 1;
        }
        return 0;
    }

    protected function download_system_setting(Request $request){
        
        try{

            $title = Self::MAIN_TITLE;
        
            $security_variable = FALSE;

            $validator = Validator::make($request->all(), [
                'confirmation_code' => ['required']
            ]);
    
            $login_user = $this->get_login_user_detail();
    
            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];
    
            if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
                $security_variable = TRUE;
            }
            else{
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/system-settings';</script>";
            }
    
            $setting = HrmSetting::orderBy('setting_id','DESC')->get()->toArray();

            $mac_address = MacAddress::get()->toArray();

            $get_leave_managements = LeaveManagement::get()->toArray();
            
            $email_config = EmailConfiguration::get()->toArray();

            $salary_division = SalaryDivision::get()->toArray();

            $email_endpoints = EmailEndPoint::get()->toArray();

            $operation_manager = Employees::where('employee_isactive',1)->get(['employee_id','employee_name'])->toArray();

            $email_recipient = EmailRecipient::join('tbl_email_endpoints','tbl_email_recipients.endpoint_id','=','tbl_email_endpoints.endpoints_id')->join('tbl_employees','tbl_email_recipients.receiever_id','=','tbl_employees.employee_id')->get(['endpoint_desc','receiever_id','recipients_created_by','recipient_id','recipients_created_at','recipient_type','endpoint_id','employee_name'])->toArray();

            $hr_employees = Employees::where('employee_isactive',1)->where('employee_department',2)->get(['employee_id','employee_name'])->toArray();
          
            $finance_employees = Employees::where('employee_isactive',1)->get(['employee_id','employee_name'])->toArray();

            if(!empty($setting) || !empty($mac_address) ){
                $send_data = compact('title','login_user','setting','mac_address','email_config','get_leave_managements','salary_division','email_endpoints','operation_manager','email_recipient','hr_employees','finance_employees');
            }else{
                $send_data = compact('title','login_user');
            }

            if($security_variable == TRUE){
                  
                $pdf = Pdf::loadView('miscellanous.downlaod-system-setting',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('Hcm System Setting'.'.pdf');
            }

        }catch(\Exception $e){
            $this->create_error_log('Download System Settings', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('Download System Settings');
            
        
        }
       
    }
    
}

