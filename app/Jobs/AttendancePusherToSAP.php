<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\URL;
use Exception;

class AttendancePusherToSAP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // DB::table('tbl_attendance_job')->insert([
        //     'function_name' => 'Sap Attendance',
        //     'execute_time' => date('Y-m-d H:i:s')
        // ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd('123');
        try{
            DB::table('tbl_attendance_job')->insert([
                'function_name' => 'Sap Attendance',
                'execute_time' => date('Y-m-d H:i:s')
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
