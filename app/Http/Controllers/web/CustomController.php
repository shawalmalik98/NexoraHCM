<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Employees, Permission, RequestApproval, Attendance, EmailRecipient, LeaveManagement, AttendanceAccess, HrmSetting, RoomScreen};
use App\Traits\LogGenerateTrait;
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\DB;

class CustomController extends Controller
{
    use LogGenerateTrait;

    // protected function fetch_all_employees_permission(){

    //     $get_all_employees = Employees::get(['employee_id','employee_department','employee_is_manager'])->toArray();

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

    //             }elseif($employees['employee_department'] !== 2 && $employees['employee_department'] !== 1 && $employees['employee_is_manager'] == 1){

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

    public function employee_location_insert(Request $request)
    {
        // $employee_locations = DB::select('SELECT * FROM tbl_employees_location');

        // if(empty($employee_locations)){

            $employee_code = [

                1013080,
                1013081,
                1013082,
                1013083,
                1013084,
                1013085,
                1013086,
                1013087,
                1013088,
                1013089,
                1013090,
                1013091,
                1013092,
                1013093,
                1013094,
                1013095,
                1013096,
                1013097,
                1013098,
                1013099,
                1013100,
                1013101,
                1013102,
                1013103,
                1013104,
                1013105,
                1013106,
                1013107,
                1013108,
                1013109,
                1013110,
                1013111,
                1013112,
                1013202,
                1013113,
                1013114,
                1013115,
                1013116,
                1013119,
                1013120,
                1013121,
                1013122,
                1013123,
                1013124,
                1013125,
                1013126,
                1013127,
                1013128,
                1013129,
                1013130,
                1013131,
                1013132,
                1013133,
                1013134,
                1013135,
                1013136,
                1013137,
                1013138,
                1013139,
                1013140,
                1013141,
                1013142,
                1013143,
                1013144,
                1013145,
                1013146,
                1013147,
                1013148,
                1013149,
                1013150,
                1013151,
                1013152,
                1013153,
                1013154,
                1013155,
                1013156,
                1013157,
                1013158,
                1013159,
                1013161,
                1013162,
                1013163,
                1013164,
                1013165,
                1013166,
                1013167,
                1013168,
                1013169,
                1013170,
                1013171,
                1013179,
                1013172,
                1013173,
                1013174,
                1013175,
                1013176,
                1013177,
                1013178,
                1013160,
                1013180,
                1013185,
                1013186,
                1013187,
                1013188,
                1013189,
                1013190,
                1013191,
                1013192,
                1013193,
                1013194,
                1013195,
                1013196,
                1013197,
                1013198,
                1013199,
                1013200,
                1013201,

            ];

            $employee_location = [

                'Qamar Traders Faisalabad',
                'Haji Shoukat & Sons',
                'Ali Raza Traders',
                'Zahid Traders',
                'Haji Shoukat & Sons',
                'Haji Shoukat & Sons',
                'Haji Shoukat & Sons',
                'F.G Traders Samunderi',
                'Imran Bushra Marketing (IBM)',
                'Rumaisa Agencies',
                'Aftab Ibrahim Traders (RP)',
                'Mool Chan & Sons',
                'B.S Traders',
                'Qadeer Traders',
                'Abdullah Enterprises',
                'G.M. Sons Traders',
                'M.A Traders',
                'Faisal Afridi Sweets',
                'Faisal Afridi Sweets',
                'M.A Traders',
                'M.Z Traders',
                'M.A Traders',
                'Bariyah Enterprises',
                'Lucky Two (Karachi)',
                'Masood Traders',
                'Naveed Traders',
                'Muzammil Traders',
                'S.F Traders',
                'Haji Munawar and Sons',
                'Babar Traders',
                'Al - Haram Traders',
                'S.A Traders',
                'Al-Amin Traders',
                'Al-Amin Traders',
                'Bajwa Traders',
                'Bajwa Traders',
                'Bajwa Traders',
                'Abdul Ghani',
                'Imtiaz Traders Gujranwala',
                'Imtiaz Traders Gujranwala',
                'Imtiaz Traders Gujranwala',
                'Imtiaz Traders Gujranwala',
                'Imtiaz Traders Gujranwala',
                'Imtiaz Traders Gujranwala',
                'Imtiaz Traders Gujranwala',
                'Babu Jee Traders',
                'Qureshi Traders',
                'Qureshi Traders',
                'M.R Enterprises',
                'Noman Agencies',
                'GK Enterprises',
                'AJU ENTERPRISE',
                'Akmal Shah Traders',
                'Imtiaz Dha Lahore',
                'Sana Traders',
                'Bilal Traders (K.B)',
                'Nadeem Traders',
                'Nadeem Traders',
                'Haris Traders',
                'Carrefour WTC ISB',
                'Metro I10 ISB',
                'G.M. Sons Traders',
                'G.M. Sons Traders',
                'Chaudary Sajid Traders',
                'Husnain Traders',
                'M.Z Traders',
                'AZEEM TRADERS',
                'BHORVI TRADERS',
                'REHAN TRADERS',
                'Yahya Traders (GWR)',
                'AL SADEED TRADERS',
                'HELM Trading(SMC-private) Limited',
                'CHIRAGH TRADERS',
                'ALM Marketing - IT - (LHR)',
                'Chaudary Traders',
                'ZAFAR ENTERPRISES',
                'HONEY TRADERS',
                'Akbar Elahi Traders',
                'ZAFAR ENTERPRISES',
                'AHMED TRADERS',
                'MALIK TRADERS',
                'Jhangir Enterprises',
                'King Chain General Order Suppler',
                'TAHA ENTERPRISES',
                'PANJTANI TRADERS',
                'Al-Faisal Agencies (RP)',
                'Afzal & Company II',
                'ABDULLAH TRADER',
                'ALI AOUN TRADERS',
                'ALI AOUN TRADERS',
                'S.M TRADERS',
                'MADNI TRADERS',
                'ASIF TRADERS',
                'HELM Trading(SMC-private) Limited',
                'FEROZ TRADERS',
                'AYAN TRADERS (SGD)',
                'AKHTAR BROTHERS',
                'QADEER ENTERPRISES',
                'RAJPUT TRADERS',
                'Jhangir Enterprises',
                'U.A Traders',
                'I Marketing - Faisalabad',
                'I Marketing - Faisalabad',
                'Maan Traders',
                'Sirajia Mareting',
                'Sirajia Traders Sargodha',
                'Al FatimeTraders',
                'Zaman Traders',
                'Hunni Ent Nazimabad',
                'Zahid Enterprises KORANGI',
                'Moinbad Road Malir',
                'Dynamic',
                'Dynamic Dist',
                'Haroon Traders',
                'Mb Enterprises Multan',
                'Ali Interprises',
                'Noor Traders',

            ];

        // }

        if(!empty($employee_location)){
            foreach($employee_location as $key => $location){
                $get_employee_data = DB::select("SELECT employee_id FROM tbl_employees WHERE employee_code = '".$employee_code[$key]."'");
                $get_employee = json_decode(json_encode($get_employee_data), true);
                $check_if_location_exists = DB::select("SELECT * FROM tbl_office_locations WHERE location_title = '".$location."'");
                $get_location = json_decode(json_encode($check_if_location_exists), true);
                if(!empty($get_location) || !empty($get_employee)){
                    DB::table('tbl_employees_location')->insert([
                        'emp_loc_employee_id' => $get_employee[0]['employee_id'],
                        'emp_loc_loc_id' => $get_location[0]['location_id'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    Employees::where('employee_id', $get_employee[0]['employee_id'])
                    ->where('employee_geofencing_enability', '=', 0)->update(['employee_geofencing_enability'=> 1]);
                }
            }
        }

        return response()->json([
            'status' => 'TRUE',
            'msg' => 'Job executed successfully.'
        ]);

    }

}