<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Exception;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\DB;

class AttendanceGetPreviousDayFromSAP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            DB::table('tbl_attendance_job')->insert([
                'function_name' => 'Attendance fetched (yesterday date) from SAP',
                'execute_time' => date('Y-m-d H:i:s')
            ]);
            $previous_date = date('Ymd', strtotime('-1 day', strtotime(date('Y-m-d'))));
            Http::timeout(6000)->get('http://172.19.0.101:8080/get-sap-attendance/'.$previous_date.'/'.$previous_date);
            
        }catch(Exception $e){
            $generate_error_log = new ErrorLog();
            $generate_error_log->error_log_module = 'Get Sap today Attendance';
            $generate_error_log->error_log_action_url = URL::current();
            $generate_error_log->error_log_current_url = URL::previous();
            $generate_error_log->error_log_message = $e->getMessage();
            $generate_error_log->error_log_generated_employee =  1;
            $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
            $generate_error_log->save();
        }
    }
}
