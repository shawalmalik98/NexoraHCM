<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\{AttendanceEmployee, LogGenerateTrait, LoginEmployee, MachineAttendanceTrait};
use App\Models\AttendanceMachineLogs;
use App\Helpers\ActivityTracker;

class MachineAttendanceController extends Controller
{
    use AttendanceEmployee, LoginEmployee, LogGenerateTrait, MachineAttendanceTrait;
    const MAIN_TITLE = 'machine-logs';
    protected function index(Request $request){
        try{

            $title = Self::MAIN_TITLE;
            $login_user = $this->get_login_user_detail();
            $where_clause = "1=1 ";
            if(!empty($request->input('logs_date'))){
                $where_clause .= ' AND logs_created_at like "%'.date('Y-m-d',strtotime($request->input('logs_date'))).'%"';
            }else{
                if(empty($request->input('status'))){
                    $where_clause .= ' AND logs_created_at like "%'.date('Y-m-d').'%"';
                }
            }
            if(!empty($request->input('status'))){
                $where_clause .= ' AND logs_status like "%'.$request->input('status').'%"';
            }
            $fetch_machine_logs = AttendanceMachineLogs::leftJoin('tbl_attendance_devices','tbl_machine_attendance_logs.machine_id','=','tbl_attendance_devices.attendance_device_id')->whereRaw($where_clause)->get()->toArray();
            $send_data = compact('title','login_user','fetch_machine_logs');

        }catch(\Exception $e){
        
            $this->create_error_log('View Attendance Machine Logs', $e);
            $send_data = compact('title','login_user');

        }finally{

            ActivityTracker::track_activity('View Attendance Machine Logs');
            return view('attendance-machine-logs.machine-logs', $send_data);
            
        }
    }

    protected function pull_attendance_from_machine_into_portal(Request $request){
        try{
            $machine_attendance_records = $request->input('attendance_array');
            
            $machine_id = ($request->input('machine_id') == 1) ? 1:2;
            
            if(!empty($machine_attendance_records)){
                $this->records_insertion($machine_attendance_records);
                $this->machine_logs(1 , NULL ,$machine_id);
            }
           
           
        }catch(\Exception $e){
            $error = $e->getMessage() .' On line no : '. $e->getLine() .' file name : '.$e->getFile();
            $machine_id = ($request->input('machine_id') == 1) ? 1:2;
            $this->machine_logs(2 , $error ,$machine_id);
        }
    }

    protected function records_insertion($machine_records){
        foreach($machine_records as $record){
            if(!empty($record['employee_id']) && !empty($record['attendance_punched_time']) && !empty($record['attendance_type'])){
                if($record['attendance_type'] == 88 || $record['attendance_type'] === 88){
                    $this->multiple_check_in_machine($record['attendance_punched_time'], $record['employee_id'],4);
                }
                elseif($record['attendance_type'] == 1 || $record['attendance_type'] == '1'){
                    $this->multiple_check_out_machine($record['attendance_punched_time'], $record['employee_id'],4);
                }
            }
        }
    }
    
    protected function machine_logs($logs_status, $logs_errors = NULL, $machine_id){
        $add_logs = new AttendanceMachineLogs();
        $add_logs->logs_status = $logs_status;
        $add_logs->logs_errors = $logs_errors;
        $add_logs->logs_created_at = date('Y-m-d H:i:s');
        $add_logs->machine_id = $machine_id;
        $add_logs->save();
    }
}
