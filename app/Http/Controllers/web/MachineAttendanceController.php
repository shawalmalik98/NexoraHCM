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
                $where_clause .= ' AND logs_status like '.\DB::getPdo()->quote('%'.$request->input('status').'%').'';
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

            // the attendance machine authenticates with the shared key from ATTENDANCE_MACHINE_KEY; without a key the endpoint stays closed
            $machine_key = (string) config('services.attendance_machine.key');
            if($machine_key === '' || !hash_equals($machine_key, (string) $request->header('Machine-Key'))){
                return response([
                    'message' => 'Invalid machine key.',
                    'status' => FALSE,
                    'data' => ''
                ], 403);
            }

            // [{employee_id, attendance_punched_time: 'Y-m-d H:i:s', attendance_type: CI|CO}, ...]
            $machine_attendance_records = $request->input('attendance_array');

            $machine_id = ($request->input('machine_id') == 1) ? 1:2;

            if(!empty($machine_attendance_records) && is_array($machine_attendance_records)){
                $this->records_insertion($machine_attendance_records);
                $this->machine_logs(1 , NULL ,$machine_id);
                return response([
                    'message' => 'Attendance inserted successfully',
                    'status' => TRUE,
                    'data' => ''
                ], 200);
            }else{
                return response([
                    'message' => 'No attendance record in payload.',
                    'status' => FALSE,
                    'data' => ''
                ], 200);
            }
           
           
        }catch(\Exception $e){
            $error = $e->getMessage() .' On line no : '. $e->getLine() .' file name : '.$e->getFile();
            $machine_id = ($request->input('machine_id') == 1) ? 1:2;
            $this->machine_logs(2 , $error ,$machine_id);
            return response([
                'message' => 'Attendance insertion failed.',
                'status' => FALSE,
                'data' => ''
            ], 500);
        }
    }

    protected function records_insertion($machine_records){
        foreach($machine_records as $record){
            if(is_array($record) && !empty($record['employee_id']) && !empty($record['attendance_punched_time']) && !empty($record['attendance_type'])){
                if($record['attendance_type'] == 'CI'){
                    $this->multiple_check_in_machine($record['attendance_punched_time'], $record['employee_id'],4);
                }
                elseif($record['attendance_type'] == 'CO'){
                    $this->multiple_check_out_machine($record['attendance_punched_time'], $record['employee_id'],4);
                }
            }
        }
    }
    
    protected function machine_logs($logs_status, $logs_errors, $machine_id){
        $add_logs = new AttendanceMachineLogs();
        $add_logs->logs_status = $logs_status;
        $add_logs->logs_errors = $logs_errors;
        $add_logs->logs_created_at = date('Y-m-d H:i:s');
        $add_logs->machine_id = $machine_id;
        $add_logs->save();
    }
}
