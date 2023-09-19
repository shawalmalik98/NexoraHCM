<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CronJob;
use App\Models\Employees;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LateComers;


class JobCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CronJob Command Executed Successfully!';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function double_unpunctuality_absent(){

        $current_date = date('Y-m-d');
        $check_job_is_done = CronJob::where('function_name','double_unpunctuality_absent')
        ->whereRaw('date_format(execute_time, "%Y-%m-%d") = "'.$current_date.'"')->get(['cron_job_id'])->toArray();

        if(empty($check_job_is_done)){

            $add_cron_job = new CronJob();
            $add_cron_job->function_name = "double_unpunctuality_absent";
            $add_cron_job->schedule_time = date('Y-m-d');
            $add_cron_job->execute_time = date('Y-m-d H:i:s');
            $add_cron_job->save();

            $get_all_active_employees = Employees::where('employee_isactive',1)
            ->get(['employee_id','employee_code','employee_name','employee_offdays','employee_department'])->toArray();


            foreach($get_all_active_employees as $employee){

                $fetch_all_holiday = Holiday::where('department_name','*')->orWhere('department_name','LIKE','%'.$employee['employee_department'].'%')->pluck('holiday_date')->toArray();

                $last_coming_dates = [];

                $past_day = -1;

                $employee['employee_offdays'] = (!empty($employee['employee_offdays'])) ? explode(",",$employee['employee_offdays']):[];

                while(count($last_coming_dates) != 2){

                    $new_date = date('Y-m-d',strtotime($past_day.' days'));
                    $check_holiday = in_array($new_date,$fetch_all_holiday);

                    if(!in_array(date('l',strtotime($new_date)),$employee['employee_offdays']) && !$check_holiday ) {

                        array_push($last_coming_dates,$new_date);
                    }
                    

                    $past_day --;
                }

                $absent = 0;
                foreach($last_coming_dates as $attendance_date){

                    $check_absent = Attendance::where('att_for_date',date('d',strtotime($attendance_date)))->where('att_for_month',date('m',strtotime($attendance_date)))->where('att_for_year',date('Y',strtotime($attendance_date)))->where('att_check_out_status','!=','Absent')->where('att_employee_id',$employee['employee_id'])->get('att_employee_id')->toArray();
                    if(empty($check_absent)){
                        $absent++;
                    }

                }

                if($absent==2){

                    $add_late_comers = new LateComers();
                    $add_late_comers->employee_id = $employee['employee_id'];
                    $add_late_comers->latecome_date1 = $last_coming_dates[0];
                    $add_late_comers->latecome_date2 = $last_coming_dates[1];
                    $add_late_comers->attendance_status = 'Absent';
                    $add_late_comers->create_date = date('Y-m-d');
                    $process = $add_late_comers->save();

                }

            }

        }
    }

    public function earlyout_of_missing_checkout_attendance(){

        $current_date = date('Y-m-d');
        $check_job_is_done = CronJob::where('function_name','earlyout_of_missing_checkout_attendance')
        ->whereRaw('date_format(execute_time, "%Y-%m-%d") = "'.$current_date.'"')->get(['cron_job_id'])->toArray();

        if(empty($check_job_is_done)){

            $add_cron_job = new CronJob();
            $add_cron_job->function_name = "earlyout_of_missing_checkout_attendance";
            $add_cron_job->schedule_time = date('Y-m-d');
            $add_cron_job->execute_time = date('Y-m-d H:i:s');
            $add_cron_job->save();

            $attendance_status = 'Early Out';
            $get_attendance_with_missing_checkouts = Attendance::
            where('att_check_out_time','00:00:00')
            ->where('att_check_overall_status','!=',$attendance_status)
            ->whereRaw("CONCAT(att_for_date,'-',att_for_month,'-',att_for_year)  <=  '".date("d-m-Y", strtotime("-2 days"))."'")
            ->get(['attendance_id'])
            ->toArray();

            if(!empty($get_attendance_with_missing_checkouts)){
                foreach($get_attendance_with_missing_checkouts as $missing_checkout_attendance){
                    $update_attendance_record = Attendance::find($missing_checkout_attendance['attendance_id']);
                    $update_attendance_record->att_check_overall_status = $attendance_status;
                    $process = $update_attendance_record->save();
                }
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Attendance records successfully updated.'
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'No records found to update due to error in fetched data'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'No attendance found with missing checkouts'
                ]);
            }
        }    
    }

    public function handle()
    {
       $this->double_unpunctuality_absent();
       $this->earlyout_of_missing_checkout_attendance();
       
    }
}
