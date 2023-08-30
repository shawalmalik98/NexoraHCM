<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Employees, Permission, RequestApproval, Attendance, EmailRecipient, LeaveManagement, AttendanceAccess, HrmSetting, RoomScreen};
use App\Traits\LogGenerateTrait;
use App\Helpers\ActivityTracker;

class CustomController extends Controller
{
    use LogGenerateTrait;

    // protected function fetch_all_employees_permission(){

    //     $get_all_employees = Employees::get(['employee_id','employee_department','employee_can_ra'])->toArray();

    //     $permissionRecords = [
    //         '/attendance-locker',
    //         '/employee-group',
    //         '/leave-policy',
    //         '/deduction-policy'
    //     ];

    //     if(!empty($get_all_employees)){
    //         $process = '';
    //         foreach($get_all_employees as $employees){
    //          $employee_permission = Permission::where('employee_id',$employees['employee_id'])->pluck('module_url')->toArray();
    //             if($employees['employee_department'] == 2 || $employees['employee_department'] == 1 ){

    //                 foreach($permissionRecords as $permissionRecord){
    //                     if(!in_array($permissionRecord,$employee_permission)){
    //                         $insert_employee_permissions = new Permission();
    //                         $insert_employee_permissions->employee_id = $employees['employee_id'];
    //                         $insert_employee_permissions->module_url = $permissionRecord;
    //                         $insert_employee_permissions->permission_status = 1;
    //                         $process = $insert_employee_permissions->save();
    //                     }
    //                 }

    //             }elseif($employees['employee_department'] !== 2 && $employees['employee_department'] !== 1 && $employees['employee_can_ra'] == 1){

    //                 $mypermissions = [];

    //                     foreach($permissionRecords as $permissionRecord){
    //                         if(!in_array($permissionRecord,$employee_permission)){
    //                             if(in_array( $permissionRecord,$mypermissions )){
    //                                 $insert_employee_permissions = new Permission();
    //                                 $insert_employee_permissions->employee_id = $employees['employee_id'];
    //                                 $insert_employee_permissions->module_url = $permissionRecord;
    //                                 $insert_employee_permissions->permission_status = 1;
    //                                 $process = $insert_employee_permissions->save();
    //                             }else{
    //                                 $insert_employee_permissions = new Permission();
    //                                 $insert_employee_permissions->employee_id = $employees['employee_id'];
    //                                 $insert_employee_permissions->module_url = $permissionRecord;
    //                                 $insert_employee_permissions->permission_status = 0;
    //                                 $process = $insert_employee_permissions->save();
    //                             }
    //                         }
    //                     }
    //             }else{

    //                 $mypermissions = [];

    //                 foreach($permissionRecords as $permissionRecord){
    //                     if(!in_array($permissionRecord,$employee_permission)){
    //                         if(in_array($permissionRecord,$mypermissions)){
    //                             $insert_employee_permissions = new Permission();
    //                             $insert_employee_permissions->employee_id = $employees['employee_id'];
    //                             $insert_employee_permissions->module_url = $permissionRecord;
    //                             $insert_employee_permissions->permission_status = 1;
    //                             $process = $insert_employee_permissions->save();
    //                         }else{
    //                             $insert_employee_permissions = new Permission();
    //                             $insert_employee_permissions->employee_id = $employees['employee_id'];
    //                             $insert_employee_permissions->module_url = $permissionRecord;
    //                             $insert_employee_permissions->permission_status = 0;
    //                             $process = $insert_employee_permissions->save();
    //                         }
    //                     }
    //                 }

    //             }

    //         }
    //         if($process){

    //             return response()->json([
    //                 'status'=> 'TRUE',
    //                 'msg' => 'Function executed succesfully'
    //             ]);
    //         }else{
    //             return response()->json([
    //                 'status'=> 'FALSE',
    //                 'msg' => 'This job is already exceuted'
    //             ]);
    //         }
    //     }
    // }

    protected function request_manager_status(Request $request_data){

        try{

            $request_status = RequestApproval::join('tbl_employees', 'tbl_request_approval.request_approver_id', '=', 'tbl_employees.employee_id')->where('request_id',$request_data['request_id'])->where('request_module_id',$request_data['request_module_id'])->get(['employee_code','employee_name','request_approval_time','request_approver_msg','request_approver_status'])->toArray();

            if(!empty($request_status)){
                return response()->json([
                    'status' => 'TRUE',
                    'data'  => $request_status,
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                ]);
            }

        }catch(\Exception $e){
             $this->create_error_log('View Request Response by (Manager)', $e);
             return response()->json([
                 'status' => 'FALSE',
                 'msg' =>"Please try Again",

             ]);
        }finally{
             ActivityTracker::track_activity('View Request Response by (Manager)');
        }
    }

    protected function get_employee_department(Request $request){

        try{
            $add_department_hd = (gettype($request['department']) == 'array' ) ? $request['department'] : array($request['department']);

            $result = array();
            if(!empty($add_department_hd)){

                $employees = Employees::where('employee_isactive',1)->whereIn('employee_department',$add_department_hd)->get(['employee_id','employee_code','employee_name'])->toArray();

                $result[] = '<option value="">Select Employee</option>';
                foreach($employees as $employee){
                    $result[] .= '<option value="'.$employee['employee_id'].'">'.$employee['employee_code'].' - '.$employee['employee_name'].'</option>';
                }
            }else{
                $result[] = '<option value="">First Select Department</option>';
            }

        }
        catch(\Exception $e){
            $this->create_error_log("Fetch Employee Selected Department", $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Something went wrong. Please try again.",
            ]);
        }
        finally{

            ActivityTracker::track_activity('Fetch Employee Selected Department');
            return $result;

        }
    }

    protected function earlyout_of_missing_checkout_attendance(){

        try{

            $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

            $attendance_status = 'Early out';
            $get_attendance_with_missing_checkouts = Attendance::
            where('att_check_out_time','00:00:00')
            ->where('att_check_out_status','0')
            ->where('att_check_overall_status','!=',$attendance_status)
            ->whereNot('att_check_overall_status','LIKE', '%Holiday%')
            ->whereNotIn('att_check_overall_status',$manage_leaves)
            ->whereRaw('STR_TO_DATE(CONCAT(att_for_date,"-",att_for_month,"-",att_for_year),"%d-%m-%Y") < CURDATE() - INTERVAL 2 DAY')
            ->update(['att_check_overall_status' => 'Early out']);

            if($get_attendance_with_missing_checkouts > 0){

                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'Attendance records successfully updated.'
                ]);
            }else{
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'No attendance found with missing checkouts'
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log("CheckOut missing early out", $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Something went wrong. Please try again.",
            ]);
        }
        finally{

            ActivityTracker::track_activity('CheckOut Missing Early out');


        }

    }

    protected function add_attendance_access_data(){

        try{

            $employees = Employees::pluck('employee_id')->toArray();

            if(!empty($employees)){
                $temp_array = array();
                foreach($employees as $employee){
                    $access_object = [
                        'access_employee_id' => $employee,
                        'attendance_access' => 0,
                        'access_create_at' => date('Y-m-d H:i:s'),
                    ];
                    array_push($temp_array, $access_object);
                }
                $get_attendance_accedd_data = AttendanceAccess::get()->toArray();
                if(empty($get_attendance_accedd_data)){
                    $process = AttendanceAccess::insert($temp_array);
                    if($process){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Job executed successfully.'
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'Job execution failed.'
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Job already executed.'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'No employee found.'
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log("Attendance access ", $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Something went wrong. Please try again.",
            ]);
        }
        finally{

            ActivityTracker::track_activity('Attendance Access');


        }

    }

    protected function add_recipient_type(){
        $email_recipent = EmailRecipient::whereNull('recipient_type')->update(['recipient_type'=> 'to']);
        return response()->json([
            'status' => 'TRUE',
            'msg' => 'Job executed successfully.'
        ]);
    }

    protected function add_room_location(){
        RoomScreen::whereNull('room_location_id')->update(['room_location_id'=> 1]);
        return response()->json([
            'status' => 'TRUE',
            'msg' => 'Job executed successfully.'
        ]);
    }

}