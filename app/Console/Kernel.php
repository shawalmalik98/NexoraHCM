<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\AttendanceGetToSAP;
use App\Jobs\EmployeesGetToSAP;
use App\Jobs\AttendanceGetPreviousDayFromSAP;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        \App\Console\Commands\JobCron::class,
        \App\Console\Commands\EmployeesJob::class,

    ];

    protected function schedule(Schedule $schedule)
    {

        $schedule->command('employees:job')->dailyAt('11:05');

        $schedule->call(function () {

            $response = Http::get('http://172.19.0.101:8080/sap-attendance/post-attend');
            $data = $response->body();
            DB::table('tbl_attendance_job')->insert([
                'function_name' => $data,
                'execute_time' => now()->format('Y-m-d H:i:s')
            ]);

        })->everyFiveMinutes()->between('08:00', '11:00');

        $schedule->call(function () {
            Log::info('Dispatching AttendanceGetToSAP');
            DB::table('tbl_attendance_job')->insert([
                'function_name' => 'Attendance fetched from SAP',
                'execute_time' => date('Y-m-d H:i:s')
            ]);
            Http::timeout(6000)->get('http://172.19.0.101:8080/get-today-sap-attendance');
        })->hourly()->between('10:00', '23:00');

        $schedule->call(function () {
            Log::info('Dispatching AttendanceGetPreviousDayFromSAP');
            DB::table('tbl_attendance_job')->insert([
                'function_name' => 'Attendance fetched (yesterday date) from SAP',
                'execute_time' => date('Y-m-d H:i:s')
            ]);
            $previous_date = date('Ymd', strtotime('-1 day', strtotime(date('Y-m-d'))));
            Http::timeout(6000)->get('http://172.19.0.101:8080/get-sap-attendance/'.$previous_date.'/'.$previous_date);
        })->dailyAt('10:20');

        $schedule->call(function () {
            Log::info('Dispatching AttendanceGetPreviousDayFromSAP');
            DB::table('tbl_attendance_job')->insert([
                'function_name' => 'Attendance fetched (yesterday date) from SAP',
                'execute_time' => date('Y-m-d H:i:s')
            ]);
            $previous_date = date('Ymd', strtotime('-1 day', strtotime(date('Y-m-d'))));
            Http::timeout(6000)->get('http://172.19.0.101:8080/get-sap-attendance/'.$previous_date.'/'.$previous_date);
        })->dailyAt('11:20');

        $schedule->call(function () {
            Log::info('Dispatching AttendanceGetPreviousDayFromSAP');
            DB::table('tbl_attendance_job')->insert([
                'function_name' => 'Attendance fetched (yesterday date) from SAP',
                'execute_time' => date('Y-m-d H:i:s')
            ]);
            $previous_date = date('Ymd', strtotime('-1 day', strtotime(date('Y-m-d'))));
            Http::timeout(6000)->get('http://172.19.0.101:8080/get-sap-attendance/'.$previous_date.'/'.$previous_date);
        })->dailyAt('12:20');

        $schedule->call(function () {
            Log::info('Dispatching AttendanceGetPreviousDayFromSAP');
            DB::table('tbl_attendance_job')->insert([
                'function_name' => 'Attendance fetched (yesterday date) from SAP',
                'execute_time' => date('Y-m-d H:i:s')
            ]);
            $previous_date = date('Ymd', strtotime('-1 day', strtotime(date('Y-m-d'))));
            Http::timeout(6000)->get('http://172.19.0.101:8080/get-sap-attendance/'.$previous_date.'/'.$previous_date);
        })->dailyAt('13:20');

        $schedule->call(function () {

            $response = Http::get('http://172.19.0.101:8080/sap-attendance/post-attend');
            $data = $response->body();
            DB::table('tbl_attendance_job')->insert([
                'function_name' => $data,
                'execute_time' => now()->format('Y-m-d H:i:s')
            ]);

        })->hourly()->between('11:00', '13:00');

        $schedule->call(function () {

            $response = Http::get('http://172.19.0.101:8080/sap-attendance/post-attend');
            $data = $response->body();
            DB::table('tbl_attendance_job')->insert([
                'function_name' => $data,
                'execute_time' => now()->format('Y-m-d H:i:s')
            ]);

        })->everyFiveMinutes()->between('17:00', '21:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
