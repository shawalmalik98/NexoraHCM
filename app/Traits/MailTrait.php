<?php

namespace App\Traits;

use App\Models\{Employees, EmailRecipient};
use App\Jobs\{SendEmailJob, OnBoardingEmail,SendCandidateEmailJob};
use App\Models\HrmSetting;
trait MailTrait {

    protected function sent_email($email,$cc_reciepents,$subject, $body, $url){
        $email_permission = HrmSetting::get('company_email_permission')->toArray();
        if(!empty($email_permission) && $email_permission[0]['company_email_permission'] == 1){
            $user_logined = session('user_session');
            if (!empty(env('MAIL_MAILER')) && !empty(env('MAIL_HOST')) && !empty(env('MAIL_PORT')) && !empty(env('MAIL_FROM_ADDRESS')) && !empty(env('MAIL_FROM_NAME'))) {
                $SendEmailJob = new SendEmailJob($email,$cc_reciepents,$subject, $body, $url, $user_logined);
                dispatch($SendEmailJob);
            }
           
        }
      
    }

    protected function sent_interview_candidate_email($email,$subject, $body){
        $email_permission = HrmSetting::get('company_email_permission')->toArray();
        if (!empty(env('MAIL_MAILER')) && !empty(env('MAIL_HOST')) && !empty(env('MAIL_PORT')) && !empty(env('MAIL_FROM_ADDRESS')) && !empty(env('MAIL_FROM_NAME'))) {
            if(!empty($email_permission) && $email_permission[0]['company_email_permission'] == 1){
                $user_logined = session('user_session');
                $SendEmailJob = new SendCandidateEmailJob($email,$subject, $body,$user_logined);
                dispatch($SendEmailJob);
            }
        }
      
    }

    public function send_module_email($module_id, $subject, $body, $url = NULL){
        
        $to_reciepents = EmailRecipient::join('tbl_employees','tbl_employees.employee_id','=','receiever_id')->where('endpoint_id', $module_id)->where('employee_isactive',1)->where('recipient_type','to')->pluck('employee_work_email')->toArray();

        $cc_reciepents = EmailRecipient::join('tbl_employees','tbl_employees.employee_id','=','receiever_id')->where('endpoint_id', $module_id)->where('employee_isactive',1)->where('recipient_type','cc')->pluck('employee_work_email')->toArray();

        if(!empty($to_reciepents)){
            $this->sent_email($to_reciepents,$cc_reciepents,$subject, $body, $url);
        }
    }
    
    public function send_interviewer_email($employee_id, $subject, $body, $url = NULL){
        
        $emails_to_send = Employees::where('employee_id',$employee_id)->where('employee_isactive',1)->pluck('employee_work_email')->toArray();
        if(!empty($emails_to_send)){
            $this->sent_email($emails_to_send,Null,$subject, $body, $url);
        }
    }

    public function send_candidate_email($candidate_email,$subject,$body){
        
        if(!empty($candidate_email)){
            $this->sent_interview_candidate_email($candidate_email,$subject,$body);
        }
    }

    public function send_meeting_email($employee_id,$subject, $body, $url = NULL,$add_another_user = Null){
        
        $cc_reciepents = Null;
    
        if($add_another_user == Null){
          
            $cc_reciepents = Employees::where('employee_id',session('user_session'))->where('employee_isactive',1)->pluck('employee_work_email')->toArray();
        }
       
        $to_reciepents = Employees::whereIn('employee_id',$employee_id)->where('employee_isactive',1)->pluck('employee_work_email')->toArray();
        
        if(!empty($to_reciepents)){
            $this->sent_email($to_reciepents,$cc_reciepents,$subject, $body, $url);
        }
    }

    protected function sent_email_on_boarding($email, $name, $employee_code, $password){
        //$email = 'shawaltelecart@gmail.com';
        $user_logined = session('user_session');
        $SendEmailJob = new OnBoardingEmail($email, $name, $employee_code, $password, $user_logined);
        dispatch($SendEmailJob);
    }
}