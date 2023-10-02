<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Attendance, Employees, Leave, OfficeLocation,LeaveManagement,LeaveRequest};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Barryvdh\DomPDF\Facade\Pdf;

class LeavesController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = "manage-leaves";
    const TITLE_GLOBAL1 = "leave-management";

    const LEAVE_NAME = ['Present','Present Leave','Half Day','Half Day Leave','Early Out','Early Out Leave','Absent' ,'Absent Leave', 'Late Coming' ,'Late Coming Leave', 'Leave','Testing Leave'];

    protected function index(Request $request){

        try{

            $title = Self::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();
            
            if(!empty($request->is_active =="2")){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1';
            if(!empty($request->employee_location)){
                $where_clause .= ' AND employee_location = "'.$request->employee_location.'"'; 
            }

             $all_employees = Employees::selectRaw('Sum(CAST(leave_allotement AS INT)) as total_alloted_leave,employee_id,employee_name,employee_code,employee_location,leaves_employee_id')->join('tbl_leave_entitlement', 'tbl_employees.employee_id', '=', 'tbl_leave_entitlement.leaves_employee_id')->join('tbl_leave_management', 'tbl_leave_entitlement.leave_type_id', '=', 'tbl_leave_management.leave_manage_id')->where('employee_isactive',$status)->whereRaw($where_clause)->groupBy('leaves_employee_id','employee_id','employee_name','employee_code','employee_location')->get()->toArray();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            $leaves = LeaveManagement::get()->toArray();
        
            $leave_name = array_column($leaves, 'leave_name');
            
            foreach($all_employees as $employee){


                $get_availed_total_leaves =  Attendance::selectRaw('Count(att_check_overall_status) as total_alloted_leave')->where('att_employee_id', $employee['leaves_employee_id'])->whereIn('att_check_overall_status',$leave_name)->count();
                
                $all_availed_leaves = array('used_get_availed_leaves' => $get_availed_total_leaves);
                
                $result[] = array_merge($employee , $all_availed_leaves);
                
            }
           
            if(!empty($all_employees)){
                $leaves_entitled = $result;
                
            }else{
                $leaves_entitled = array();
                
            }

             
          
            $send_data = compact('title','login_user','leaves_entitled','employee_locations');
            
            return view('leave.leaves-entitled',$send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View All Manage Leave', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('Access All Employees Leaves Entitiled Module');
        }

    }

    protected function download_manage_leaves(Request $request){
        // try{

           
            $security_variable = FALSE;

            $validator = Validator::make($request->all(), [
                'confirmation_code' => ['required']
            ]);
            
            $login_user = $this->get_login_user_detail();

            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];

            if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
                $security_variable = TRUE;
            }
            else{
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/manage-leaves';</script>";
            }

            if(!empty($request->is_active =="2")){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1';
            if(!empty($request->employee_location)){
                $where_clause .= ' AND employee_location = "'.$request->employee_location.'"'; 
            }

            $all_employees = Employees::selectRaw('Sum(CAST(leave_allotement AS INT)) as total_alloted_leave,employee_id,employee_name,employee_code,employee_location,leaves_employee_id')->join('tbl_leave_entitlement', 'tbl_employees.employee_id', '=', 'tbl_leave_entitlement.leaves_employee_id')->join('tbl_leave_management', 'tbl_leave_entitlement.leave_type_id', '=', 'tbl_leave_management.leave_manage_id')->where('employee_isactive',$status)->whereRaw($where_clause)->groupBy('leaves_employee_id','employee_id','employee_name','employee_code','employee_location')->get()->toArray();
         
            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            $leaves = LeaveManagement::get()->toArray();

            $leave_name = array_column($leaves, 'leave_name');

            foreach($all_employees as $employee){


                $get_availed_total_leaves =  Attendance::selectRaw('Count(att_check_overall_status) as total_alloted_leave')->where('att_employee_id', $employee['leaves_employee_id'])->whereIn('att_check_overall_status',$leave_name)->count();

                $all_availed_leaves = array('used_get_availed_leaves' => $get_availed_total_leaves);

                $result[] = array_merge($employee , $all_availed_leaves);

            }
           
            if(!empty($all_employees)){
                $leaves_entitled = $result;
            }else{
                $leaves_entitled = array();
            }
          
            $send_data = compact('login_user','leaves_entitled','employee_locations');

            if($security_variable == TRUE){
               
                $pdf = Pdf::loadView('leave.download-leave-entitlement-report',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Leave Entitlement'.'.pdf');
            }
        // }
        // catch(\Exception $e){
        //     $this->create_error_log('Download All Manage Leave', $e)
        //     return view('errors.500');
        // }finally{

        //     ActivityTracker::track_activity('Download All Employees Leaves Entitiled Report Pdf');

        
        // }
    }

    protected function leave_entitlement_detail($employee_id){
       
        try{

            $title = Self::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

            $leaves_entitled = LeaveManagement::leftJoin('tbl_leave_entitlement', function($join) use($employee_id){
                $join->on('tbl_leave_management.leave_manage_id','=', 'tbl_leave_entitlement.leave_type_id');
                $join->where('leaves_employee_id',$employee_id);
            })->get()->toArray();
            
            $i =0;
            
            foreach($leaves_entitled as $leaves_entitled_employee){
                
                $get_availed_leaves =  Attendance::where('att_employee_id', $employee_id)->where('att_check_overall_status', $leaves_entitled_employee['leave_name'])->count();
                $leaves_entitled[$i]['employee_availed_leaves'] = $get_availed_leaves;
                
                $i++;
            }
        
            $send_data = compact('title','login_user','leaves_entitled','employee_id');

            return view('leave.leaves-entitled-detail',$send_data);

       }
        catch(\Exception $e){
            $this->create_error_log('View Leave Entitlement Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('View Leave Entitlement Detail');

        }
    }


    protected function update_employee_leave_entitlement(Request $request){
      
        try{
            $message = ['required'=>'Leave alloted field is required ','numeric'=>'The leave alloted must be a number','min'=> 'Leave alloted must be at least 0','max'=>'Leave alloted must not be greater than 100','regex' =>'Leave alloted format is invalid'];

            $validator = Validator::make($request->all(), [
                'leave_alloted.*' => 'required|numeric|min:0|max:100|regex:/^[^+\-]+$/',
            ],$message);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
               
                foreach($request->leave_type_id as $key=>$leave_entitlement_detail){
                    
                    $leave_names = LeaveManagement::where('leave_manage_id',$leave_entitlement_detail)->get('leave_name')->toArray();
                    
                    if(!empty($leave_names)){
                        if(!empty($request->leave_ent_created_at[$key])){
                   
                            $date = $request->leave_ent_created_at[$key];
                            $updated_date = date('Y-m-d H:i:s');
                        }else{
                            $date =  date('Y-m-d H:i:s');
                            $updated_date = '';
                        }
                        $leave = Leave::updateOrCreate([
                            'leaves_employee_id' => $request->employee_id,
                            'leave_type_id' =>  $leave_entitlement_detail
                        ], [
                            'leave_allotement' => $request->leave_alloted[$key],
                            'leave_ent_created_at' => $date,
                            'leave_ent_updated_at' => $updated_date
                        ]);
                    }
                   
                }
        
                if($leave->wasRecentlyCreated || $leave->wasChanged()){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Leave entitlement updated successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Leave entitlement not updated successfully.",
                    ]);
                }
            } 
        }catch(\Exception $e){

            $this->create_error_log("Update Leave Entitlement", $e);
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

        }finally{

            ActivityTracker::track_activity('Update Leave Entitlement');

        }
    }


    protected function leave_management(){
        try{

            $title = Self::TITLE_GLOBAL1;

            $login_user = $this->get_login_user_detail();
            
            $leaves = LeaveManagement::get()->toArray();

            $leave_name = array_column($leaves, 'leave_name');

            $leave_id = array_column($leaves, 'leave_manage_id');

            $leave_approval_name = Attendance::whereIn('att_check_overall_status',$leave_name)->pluck('att_check_overall_status')->toArray();
            $leave_approval_name = array_unique($leave_approval_name);

            $leave_manag_request_id = LeaveRequest::whereIn('leave_applied_type',$leave_id)->pluck('leave_applied_type')->toArray();

            $leave_manag_request_id = array_unique($leave_manag_request_id);

            $send_data = compact('title','login_user','leaves','leave_approval_name','leave_manag_request_id');
            
            return view('leave.manage-leaves',$send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View All Leave Management', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('View All Leave Management');

        }

    }

    protected function add_leave(Request $request){
        try{
            
            $validator = Validator::make($request->all(), [
                'leave_name' => ['required','unique:tbl_leave_management,leave_name','regex:/^[\pL\s\&\,(\)]+$/u','min:3','max:30'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{

                $leave_names = SELF::LEAVE_NAME;
                $add_leave_name = ucwords(trim($request->input('leave_name')));
                $leave_name_exits = LeaveManagement::where('leave_name','LIKE',"$request->input('leave_name')%")->get('leave_manage_id')->toArray();
                if(empty($leave_name_exits)){
                    if(!in_array($add_leave_name,$leave_names)){
                        $add_new_leave = new LeaveManagement();
                        $add_new_leave->leave_name = trim($request->input('leave_name'));
                        $add_new_leave->leave_manag_created_by = session('user_session');
                        $add_new_leave->leave_visibilty = 1;
                        $add_new_leave->leave_manag_created_at = date('Y-m-d H:i:s');
                        $process = $add_new_leave->save();
                        if($process == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Leave added successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Leave addition failed. Please try again after some time.",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This name is reserved, you can't add leave of this name.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "This name is reserved, you can't add leave of this name.",
                    ]);
                }
                
                
            }
        }
        catch(\Exception $e){

            $this->create_error_log('Add Leave Management', $e);
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

        }finally{

            ActivityTracker::track_activity('Add Leave Management');   

        }

    }

    protected function update_leave_management(Request $request){
      
		try{
            $validator = Validator::make($request->all(), [
                'leave_name' => ['required','unique:tbl_leave_management,leave_name,' .$request->input('leave_id'). ' ,leave_manage_id', 'regex:/^[\pL\s\&\,(\)]+$/u','min:3','max:30'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                $process = '';
                $update_leave = LeaveManagement::find($request->input('leave_id'));
               
                $leaves = ['Casual Leave','Sick Leave','Annual Leave','Maternity Leave','Paternity Leave'];
                
                $leave_names = SELF::LEAVE_NAME;
                $leave_name = ucwords(trim($request->input('leave_name')));

                if(!in_array($leave_name,$leave_names)){
                    if(!empty($update_leave)){
                        $leave_visibility = $request->input('edit_leave_visibility');
                    
                        $leave_request_count = LeaveRequest::where('leave_applied_type',$update_leave->leave_manage_id)->count();

                        $leave_approval_count = Attendance::where('att_check_overall_status',$update_leave->leave_name)->count();
        
                        if($leave_approval_count > 0 || $leave_request_count > 0 ){
                            $update_leave->leave_manag_created_by = session('user_session');
                            $update_leave->leave_visibilty = isset($leave_visibility)?1:0;
                            $update_leave->leave_manag_created_at = date('Y-m-d H:i:s');
                            $process = $update_leave->save();
                        
                            
                        }else{
                        
                            if(!in_array($update_leave->leave_name, $leaves)){
                                $update_leave->leave_name = trim($request->input('leave_name'));
                            }
                            $update_leave->leave_manag_created_by = session('user_session');
                            $update_leave->leave_visibilty = isset($leave_visibility)?1:0;
                            $update_leave->leave_manag_created_at = date('Y-m-d H:i:s');
                            $process = $update_leave->save();
                        }
                        
                        if($process == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Leave updated successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Leave updation failed. Please try again after some time.",
                            ]);
                        }
                        
                    }
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This leave id not found.',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "This name is reserved, you can't update leave of this name.",
                    ]);
                }
            }
        }catch(\Exception $e){

            $this->create_error_log("Update Leave Management", $e);
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

        }finally{

            ActivityTracker::track_activity('Update Leave Management');

        }
    }

    protected function delete_leave_management($leave_id){
        try{
            if(!empty($leave_id) && is_numeric($leave_id)){
                $check_if_leave_exists = LeaveManagement::where('leave_manage_id', $leave_id)->get()->toArray();
               
                if(!empty($check_if_leave_exists)){

                    $leave_approval_count = Attendance::where('att_check_overall_status',$check_if_leave_exists[0]['leave_name'])->count();

                    $leave_request_count = LeaveRequest::where('leave_applied_type',$check_if_leave_exists[0]['leave_manage_id'])->count();

                    $leaves = ['Casual Leave','Sick Leave','Annual Leave','Maternity Leave','Paternity Leave'];
                   
                    if(!in_array($check_if_leave_exists[0]['leave_name'], $leaves) &&  $leave_approval_count <= 0 &&   $leave_request_count <=0){
                        LeaveManagement::where('leave_manage_id',$leave_id)->delete();
                        Leave::where('leave_type_id',$leave_id)->delete();
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Manage leave deleted successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This leave can't be delete now. Already availed by employee(s).",
                        ]);
                    }
                
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Manage leave not found on this id",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Manage leave id is required",
                ]);
            }  
        }catch(\Exception $e){

            $this->create_error_log("Manage leave Deleted", $e);
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
        }finally{

		    ActivityTracker::track_activity('Delete Leave Management');
        
        }
    }


}
