<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Traits\{NotificationTrait, LogGenerateTrait};
use App\Models\{Employees};


class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use NotificationTrait, LogGenerateTrait;

    public $fcm_token;
    public $title;
    public $msg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fcm_token, $title, $msg)
    {
        $this->fcm_token = $fcm_token;
        $this->title = $title;
        $this->msg = $msg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            
            $server_api_key = 'AAAAt4nPc8M:APA91bG0ul8qsNkufwrT3252M2bvArWu2Vthta5fCrNz8lK9DZSmGZf1QftyKjPUDip8UbTzmQwnvF_b31EZmTP7FZqtJIhTNSr1ULxdY0oJ-_Skv2ljnyOI9m1V9KZtGaPwfdSNXJyb';

            $data = [
                "registration_ids" => [$this->fcm_token],
                "notification" => [
                    "title" =>  $this->title,
                    "body" =>  $this->msg,  
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $server_api_key,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $data);

        } catch (\Exception $e) {
            $this->create_error_log('Push Notification Mobile App', $e);
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
