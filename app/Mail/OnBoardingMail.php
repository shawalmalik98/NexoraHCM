<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\HrmSetting;

class OnBoardingMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $employee_code;
    public $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$employee_code,$password)
    {
  
        $this->name = $name;
        $this->employee_code = $employee_code;
        $this->password = $password;

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {   
        $company_name = HrmSetting::get(['company_name'])->toArray();
        if(!empty($company_name)){
            $subject = "Welcome to ".$company_name[0]['company_name'];
        }else{
            $subject = "Welcome to Company";
        }
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {   
        $hr_name = HrmSetting::get(['onboarding_email_name'])->toArray();
        if(!empty($hr_name)){
            $regards = $hr_name[0]['onboarding_email_name'].".";
        }else{
            $regards = "";
        }

        return new Content(
            view: 'email-template.onboard-employee',
            with: [
               
                'name' => $this->name,
                'employee_code' =>  $this->employee_code,
                'password' =>  $this->password,
                'regards' => $regards,

            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
