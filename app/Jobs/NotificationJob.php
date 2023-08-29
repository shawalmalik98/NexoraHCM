<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\{NotificationTrait};
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Contract\Database;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\URL;
use Exception;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use NotificationTrait;

    public $title;
    public $msg;
    public $url;
    public $to_employee;
    public $to_manager1;
    public $to_manager2;
    public $to_hr;
    public $user_logined;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title , $msg , $url , $to_employee = null , $to_manager1 = null , $to_manager2 = null , $to_hr, $user_logined,$time)
    {   
        $this->title = $title;
        $this->msg = $msg;
        $this->url = $url;
        $this->to_employee = $to_employee;
        $this->to_manager1 = $to_manager1;
        $this->to_manager2 = $to_manager2;
        $this->to_hr = $to_hr;
        $this->user_logined = $user_logined;
        $this->time = $time;
      
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        try{
            $factory = (new Factory)->withServiceAccount(__DIR__.'/fir-laravel-7b8ce-firebase-adminsdk-b7yqy-76ccd9dc80.json')->withDatabaseUri('https://fir-laravel-7b8ce-default-rtdb.firebaseio.com/');
            $database = $factory->createDatabase();
            
            $database->getReference('tbl_notifications')->push([
                "notification_title"        => $this->title,
                "notification_message"      => $this->msg,
                "notification_url"          => $this->url,
                "notification_to_employee"  => $this->to_employee,
                "notification_to_manager1"  =>$this->to_manager1,
                "notification_to_manager2"  =>$this->to_manager2,
                "notification_from"         =>$this->user_logined,
                "notification_to_hr"        =>$this->to_hr,
                "notification_time"         =>$this->time,
            ]);
        }catch(Exception $e){
            $generate_error_log = new ErrorLog();
            $generate_error_log->error_log_module = 'Insert notification in firebase';
            $generate_error_log->error_log_action_url = URL::current();
            $generate_error_log->error_log_current_url = URL::previous();
            $generate_error_log->error_log_message = $e->getMessage();
            $generate_error_log->error_log_generated_employee =  $this->user_logined;
            $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
            $generate_error_log->save();
        }

    }
}
