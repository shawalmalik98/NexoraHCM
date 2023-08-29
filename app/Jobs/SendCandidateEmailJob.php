<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailCandidate;
use App\Models\ErrorLog;
use Exception;
use Illuminate\Support\Facades\URL;

class SendCandidateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $email;
    public $subject;
    public $body;
    public $user_logined;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$subject,$body,$user_logined)
    {
        //
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
        $this->user_logined = $user_logined;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        try{
            Mail::to($this->email)->send(new SendMailCandidate($this->subject,$this->body));
        }catch(Exception $e){
            $generate_error_log = new ErrorLog();
            $generate_error_log->error_log_module = 'Send email of '.$this->subject;
            $generate_error_log->error_log_action_url = URL::current();
            $generate_error_log->error_log_current_url = URL::previous();
            $generate_error_log->error_log_message = $e->getMessage();
            $generate_error_log->error_log_generated_employee =  $this->user_logined;
            $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
            $generate_error_log->save();
        }
    }
}
