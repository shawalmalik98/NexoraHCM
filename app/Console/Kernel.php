<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Jobs\AttendanceGetToSAP;
use App\Jobs\EmployeesGetToSAP;

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
    ];

    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {
            EmployeesGetToSAP::dispatch();
        })->dailyAt('11:00');

        $schedule->call(function () {
            $response = Http::get('http://172.19.0.101:8080/sap-attendance/post-attend');
            $data = $response->body();
            DB::table('tbl_attendance_job')->insert([
                'function_name' => $data,
                'execute_time' => now()->format('Y-m-d H:i:s')
            ]);

        })->everyFiveMinutes()->between('08:00', '11:00');

        // $schedule->call(function () {
        //     AttendanceGetToSAP::dispatch();
        // })->everyFiveMinutes()->between('08:00', '11:00');

        $schedule->call(function () {
            $response = Http::get('http://172.19.0.101:8080/sap-attendance/post-attend');
            $data = $response->body();
            DB::table('tbl_attendance_job')->insert([
                'function_name' => $data,
                'execute_time' => now()->format('Y-m-d H:i:s')
            ]);

        })->hourly()->between('11:00', '18:00');

        // $schedule->call(function () {
        //     AttendanceGetToSAP::dispatch();
        // })->hourly()->between('11:00', '18:00');

        $schedule->call(function () {
            $response = Http::get('http://172.19.0.101:8080/sap-attendance/post-attend');
            $data = $response->body();
            DB::table('tbl_attendance_job')->insert([
                'function_name' => $data,
                'execute_time' => now()->format('Y-m-d H:i:s')
            ]);

        })->everyFiveMinutes()->between('18:00', '20:00');

        // $schedule->call(function () {
        //     AttendanceGetToSAP::dispatch();
        // })->everyFiveMinutes()->between('18:00', '20:00');
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
