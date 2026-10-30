<?php

namespace App\Http\Controllers\web;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\{Attendance, Employees, OverTime, OfficeLocation};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait};
use App\Helpers\ActivityTracker;

class OvertimeController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait;
    const MAIN_TITLE = 'overtime-management';

    protected function index(Request $request){

        try{
            
            $title = Self::MAIN_TITLE;
      
            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();
            
            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            $where_clause = '1=1';

            if(empty($request->is_active)){
                $where_clause   .= " AND employee_isactive = '1'";
            }else{
                if(!empty($request->is_active) && $request->is_active == 1){
                    $where_clause   .= ' AND employee_isactive = "1"'; 
                }
                elseif(!empty($request->is_active) && $request->is_active == 2){
                    $where_clause   .= ' AND employee_isactive = "0"'; 
                }
            }

            if(!empty($request->employee_location)){
                $where_clause   .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request->employee_location).''; 
            }
            
            $emp_id = '1 = 1';
            if($request->input('emp_name') != '' && is_numeric($request->input('emp_name'))){
                $emp_id .= " and employee_id = ".\DB::getPdo()->quote((string) $request->input('emp_name'));
            }
            
            if($request->input('m') != '' && is_numeric($request->input('m')) && $request->input('m') >= 01 && $request->input('m') <= 12){
                $month = $request->input('m');
                
            }else{
                $month = date('m');
               
            }
            
            if($request->input('y') != '' && is_numeric($request->input('y')) && $request->input('y') >= 1900 && $request->input('y') <= 3000){
                $year = $request->input('y');
            }else{
                $year = date('Y');
            }
            
            // on screen one page of employees (ascending, as the sheet shows them); the PDF download covers everyone
            $pagination = null;
            $page_employee_ids = NULL;
            if($request->input('download') != 'TRUE'){
                $search = trim((string) $request->input('search'));
                $pagination = Employees::whereRaw($emp_id)->whereRaw($where_clause)
                    ->when($search !== '', function($query) use($search){
                        $query->where(function($query) use($search){
                            $query->where('employee_name','like','%'.$search.'%')->orWhere('employee_code','like','%'.$search.'%');
                        });
                    })
                    ->orderBy('employee_id')
                    ->paginate(CustomHelper::per_page(), ['employee_id'])->withQueryString();
                $page_employee_ids = $pagination->pluck('employee_id')->toArray();
            }

            $all_attendaces =  $this->monthly_attendance_all_employee($emp_id,$month,$year,$where_clause,$page_employee_ids);

            $all_employees = Employees::whereRaw($where_clause)->get(['employee_id','employee_code','employee_name'])->toArray();

            $send_data = compact('title','login_user','all_attendaces','all_employees','month','year','employee_locations','dynamic_years','pagination');

        
            if($request->input('download') == 'TRUE'){
                $pdf = Pdf::loadView('overtime-management.overtime-sheet-download-pdf', $send_data);
                return $pdf->setPaper('a3', 'potrait')->stream('Overtime Report'.'.pdf');
            }else{
                return view('overtime-management.overtime-sheet', $send_data);

            }
           
        }catch(\Exception $e){

            $this->create_error_log('View OverTime Page Access ', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View OverTime Page Access');
           

        }
        
    }

    protected function download_overtime_report(Request $request){

        try{
            
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
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/overtime-management';</script>";
            }

            $dynamic_years = $this->dynamic_year_drop_down();
            
            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            $where_clause = '1=1';

            if(empty($request->is_active)){
                $where_clause   .= ' AND employee_isactive = "1"';
            }else{
                if(!empty($request->is_active) && $request->is_active == 1){
                    $where_clause   .= ' AND employee_isactive = "1"'; 
                }
                elseif(!empty($request->is_active) && $request->is_active == 2){
                    $where_clause   .= ' AND employee_isactive = "0"'; 
                }
            }

            if(!empty($request->employee_location)){
                $where_clause   .= ' AND employee_location = '.\DB::getPdo()->quote((string) $request->employee_location).''; 
            }
            
            $emp_id = '1 = 1';
            if($request->input('emp_name') != '' && is_numeric($request->input('emp_name'))){
                $emp_id .= " and employee_id = ".\DB::getPdo()->quote((string) $request->input('emp_name'));
            }
            
            if($request->input('m') != '' && is_numeric($request->input('m')) && $request->input('m') >= 01 && $request->input('m') <= 12){
                $month = $request->input('m');
                
            }else{
                $month = date('m');
               
            }
            
            if($request->input('y') != '' && is_numeric($request->input('y')) && $request->input('y') >= 1900 && $request->input('y') <= 3000){
                $year = $request->input('y');
            }else{
                $year = date('Y');
            }
            
            $all_attendaces =  $this->monthly_attendance_all_employee($emp_id,$month,$year,$where_clause);
           
            $all_employees = Employees::whereRaw($where_clause)->get(['employee_id','employee_code','employee_name'])->toArray();

            $send_data = compact('login_user','all_attendaces','all_employees','month','year','employee_locations','dynamic_years');

              
            if($security_variable == TRUE){
               
                $pdf = Pdf::loadView('overtime-management.overtime-sheet-download-pdf', $send_data);
                return $pdf->setPaper('a3', 'potrait')->stream('Overtime Report'.'.pdf');
            }

        }catch(\Exception $e){

            $this->create_error_log('Download OverTime management pdf', $e);
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('Download OverTime management pdf');
           

        }
        
    }

    protected function overtime_detail(Request $request){
        
        try{
        
            $title = Self::MAIN_TITLE;
      
            $login_user = $this->get_login_user_detail();
            
            $emp_id = '1 = 1';
            $where_clause = '1=1';
            $employee_id = '';
            if($request->id != '' && is_numeric($request->id)){
                $emp_id .= " and employee_id = ".\DB::getPdo()->quote((string) $request->id);
                $employee_id = $request->id;
            }
            
            if($request->m != '' && is_numeric($request->m) && $request->m >= 01 && $request->m <= 12){
                $month = $request->m;
                
            }else{
                $month = date('m');
               
            }
            
            if($request->y != '' && is_numeric($request->y) && $request->y >= 1900 && $request->y <= 3000){
                $year = $request->y;
            }else{
                $year = date('Y');
            }
    
            $dates = $this->get_all_date_from_month($month,$year,$format=1);
        
            $checking_exits_record =  OverTime::where('ovt_emp_id',$request->id)->whereIn('over_time_date',$dates)->pluck('over_time_date')->toArray();
        
            $all_attendaces =  $this->monthly_attendance_all_employee($emp_id,$month,$year,$where_clause);
           
            $overtime_records =  OverTime::where('ovt_emp_id',$request->id)->whereIn('over_time_date',$dates)->get()->toArray();

            $employee_name = Employees::where('employee_id',$employee_id)->pluck('employee_name')->toArray();
            
            $ovt_date = array_column($overtime_records,'over_time_date');
            $amount_payable = array_column($overtime_records,'amount_payable');
            $overtime_record = array_combine( $ovt_date,  $overtime_records);

            $send_data = compact('title','login_user','all_attendaces','month','year','checking_exits_record','overtime_record','employee_id','employee_name');
            
            
            if($request->input('download') == 'TRUE'){
                $pdf = Pdf::loadView('overtime-management.overtime-detail-download-pdf', $send_data);
                return $pdf->setPaper('a3', 'potrait')->stream('Overtime Detail Report'.'.pdf');
            }else{

                return view('overtime-management.overtime-detail', $send_data); 
            }


        }catch(\Exception $e){

            $this->create_error_log('Detail OverTime Page Access ', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Detail OverTime Page Access'); 

               
        }
    }

    protected function overtime_detail_download(Request $request){
        try{
      
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
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '".url('/overtime-management/overtime-detail/'.$request->input('id').'?is_active='.$request->input('is_active').'&y='.$request->input('y').'&m='.$request->input('m'))."';</script>";
            }
            
            $emp_id = '1 = 1';
            $where_clause = '1=1';
            $employee_id = '';
            if($request->id != '' && is_numeric($request->id)){
                $emp_id .= " and employee_id = ".\DB::getPdo()->quote((string) $request->id);
                $employee_id = $request->id;
            }
            
            if($request->m != '' && is_numeric($request->m) && $request->m >= 01 && $request->m <= 12){
                $month = $request->m;
                
            }else{
                $month = date('m');
               
            }
            
            if($request->y != '' && is_numeric($request->y) && $request->y >= 1900 && $request->y <= 3000){
                $year = $request->y;
            }else{
                $year = date('Y');
            }
    
           $dates = $this->get_all_date_from_month($month,$year,$format=1);
        
            $checking_exits_record =  OverTime::where('ovt_emp_id',$request->id)->whereIn('over_time_date',$dates)->pluck('over_time_date')->toArray();
            
            $all_attendaces =  $this->monthly_attendance_all_employee($emp_id,$month,$year,$where_clause);
            
            $employee_name = Employees::where('employee_id',$employee_id)->pluck('employee_name')->toArray();
            
            $overtime_records =  OverTime::where('ovt_emp_id',$request->id)->whereIn('over_time_date',$dates)->get()->toArray();

            $ovt_date = array_column($overtime_records,'over_time_date');
            $amount_payable = array_column($overtime_records,'amount_payable');
            $overtime_record = array_combine( $ovt_date,  $overtime_records);
        
            $send_data = compact('login_user','all_attendaces','month','year','checking_exits_record','overtime_record','employee_id','employee_name');
            
            
            if($security_variable == TRUE){
               
                $pdf = Pdf::loadView('overtime-management.overtime-detail-download-pdf', $send_data);
                return $pdf->setPaper('a3', 'potrait')->stream('Overtime Detail Report'.'.pdf');
            }
               
        }catch(\Exception $e){

            $this->create_error_log('Download Detail OverTime Pdf', $e);
             return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Download Detail OverTime Pdf'); 

        }
    }

    protected function overtime_record_insert(Request $request_data){
        
        try{
          
            $validator = Validator::make($request_data->all(), [
                'employee_id' => 'required|numeric',
                'extra_hour' => 'required',
                'date' => 'required|date_format:Y-m-d',
                'salary_amount' => 'required|min:1|max:99999999.99|numeric',
            ]);
           
            if($validator->fails()) {
                 foreach($validator->errors()->all() as $error){
                     return response()->json([
                         'msg' => $error,
                         'status' => 'FALSE'
                     ]);
                 }
            }
          
            $checking_exits_record =  OverTime::where('ovt_emp_id',$request_data['employee_id'])->where('over_time_date', $request_data['date'])->get()->toArray();
           
            if(!empty($checking_exits_record) &&  empty($checking_exits_record['ovt_payroll_id'])){
               
                if($checking_exits_record[0]['over_time_reibursement_status'] == 0){
                    $process = OverTime::where('ovt_emp_id',$request_data['employee_id'])->where('over_time_date',$request_data['date'])->update(['over_time_reibursement_status'=>1]);
    
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Overtime approve successfully",
                            
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Overtime approve not successfully.",
                            
                        ]);
                    }
                }
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Already responsed to this overtime",
                    
                ]);
                
            }else{
               
                if(empty($checking_exits_record) &&  empty($checking_exits_record['ovt_payroll_id'])){
                    $add_overtime = new Overtime();
                    $add_overtime->ovt_emp_id =  $request_data['employee_id'];
                    $add_overtime->extra_hours = $request_data['extra_hour'];
                    $add_overtime->over_time_date = $request_data['date'];
                    $add_overtime->over_time_salary = $request_data['salary_amount'];
                    $add_overtime->over_time_created_by =	session('user_session');
                    $add_overtime->over_time_reibursement_status = 1;
                    $add_overtime->over_time_created_at	=  date('h:i:s a Y F, d');
                    $process = $add_overtime->save();
    
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Overtime approve successfully",
                            
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Overtime approve not successfully.",
                            
                        ]);
                    }
                }
                
            }
        
        }catch(\Exception $e){

            $this->create_error_log('Singal Record OverTime Insert', $e);
            if(!config('app.debug')){
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

            ActivityTracker::track_activity('Singal Record OverTime Insert');
               
        }


    }

    protected function multiple_overtime_record_insert_or_remove(Request $request_data){
     
        try{
            $validator = Validator::make($request_data->all(), [
                'employee_id' => ['required'],
                'extra_hours' => ['required'],
                'over_time_date' => ['required'],
                'salary_amount' => ['required','min:1','max:99999999.99'],
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }
         
            else{
                $process = '';

                if($request_data->title == "Multiple-Remove"){
    
                    foreach($request_data->employee_id as $key => $overtime_record){
                        if(!empty($request_data->input('checkbox_'.$key))){
                            $checking_exits_record =  OverTime::where('ovt_emp_id',$request_data['employee_id'])->whereIn('over_time_date',array($request_data->over_time_date[$key]))->get()->toArray();
                           
                            if(!empty($checking_exits_record) && empty($checking_exits_record[$key]['ovt_payroll_id']) ){
                                if($checking_exits_record[0]['over_time_reibursement_status'] == 1){
                                    $process =  OverTime::where('ovt_emp_id',$request_data['employee_id'])->whereIn('over_time_date',array($request_data->over_time_date[$key]))->update(['over_time_reibursement_status'=> 0]);
                               
                                }
                              
                            }
                        }
                    }
        
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Overtime reimburesement cancelled successfully",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "There are no overtime to cancel their reimbursement.",
                        ]);
                    }
                }else{
                    foreach($request_data->employee_id as $key => $overtime_record){
                        if(!empty($request_data->input('checkbox_'.$key))){
                            $checking_exits_record =  OverTime::where('ovt_emp_id',$request_data['employee_id'])->whereIn('over_time_date',array($request_data->over_time_date[$key]))->get()->toArray();
                            
                            if(empty($checking_exits_record) &&  empty($checking_exits_record[$key]['ovt_payroll_id']) ){
                              
                                $add_overtime = new Overtime();
                                $add_overtime->ovt_emp_id = $overtime_record;
                                $add_overtime->extra_hours = $request_data->extra_hours[$key];
                                $add_overtime->over_time_date =  $request_data->over_time_date[$key];
                                $add_overtime->over_time_salary =  $request_data->salary_amount[$key];
                                $add_overtime->over_time_created_by =	session('user_session');
                                $add_overtime->over_time_reibursement_status = 1;
                                $add_overtime->over_time_created_at	=  date('h:i:s a Y F, d');
                                $process = $add_overtime->save();
                                
                            }else{
                               
                                if($checking_exits_record[0]['over_time_reibursement_status'] == 0){
    
                                    $process = OverTime::where('ovt_emp_id',$overtime_record)->where('over_time_date',$request_data->over_time_date[$key])->update(['over_time_reibursement_status'=> 1]);
                                   
                                }
                            }
                        }
                       
    
                    }
                  
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Overtime insert successfully",
                            
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Already responsed this overtime.",
                            
                        ]);
                    }
                }
            }
          
           
        }catch(\Exception $e){

            $this->create_error_log('Multiple Record OverTime Insert or Remove', $e);
            if(!config('app.debug')){
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

            ActivityTracker::track_activity('Multiple Record OverTime Insert or Remove');
            
        }
      
    }

    protected function delete_overtime_record_insert(Request $request_data){
        try{
           
            $validator = Validator::make($request_data->all(), [
                'employee_id' => 'required|numeric',
                'ovt_date' => 'required|date_format:Y-m-d',
            ]);
           
            if($validator->fails()) {
                 foreach($validator->errors()->all() as $error){
                     return response()->json([
                         'msg' => $error,
                         'status' => 'FALSE'
                     ]);
                 }
            }else{
                $process = '';
                $checking_exits_record = OverTime::where('ovt_emp_id',$request_data->employee_id)->where('over_time_date',$request_data->ovt_date)->get(['ovt_emp_id','ovt_payroll_id','over_time_reibursement_status'])->toArray();
                
                if(!empty($checking_exits_record) && empty($checking_exits_record[0]['ovt_payroll_id'])){
                  
                    if($checking_exits_record[0]['over_time_reibursement_status'] != 0){
                        $process = OverTime::where('ovt_emp_id',$request_data->employee_id)->where('over_time_date',$request_data->ovt_date)->update(['over_time_reibursement_status'=> 0]);
                    }
                  
                    if($process == 1){
                        
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Overtime reimburesement record delete successfully.",
                            
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You can't responsed this.",
                            
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You can't responed this overtime.",
                        
                    ]);
                }
            }
           
        }catch(\Exception $e){

            $this->create_error_log('Singal Delete Record OverTime Insert', $e);
            if(!config('app.debug')){
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

            ActivityTracker::track_activity('Singal Delete Record OverTime Insert');
            
        }
       
    }

    protected function edit_overtime_record(Request $request_data){
        try{
            $validator = Validator::make($request_data->all(), [
                'amount_payable' => 'required|min:1|max:99999999.99|numeric',
                'employee_id' => 'required|numeric',
                'extra_hours' => 'required',
                'ovt_date' => 'required|date_format:Y-m-d',
                'salary_amount' => 'required|min:1|max:99999999.99|numeric',
            ]);
           
            if($validator->fails()) {
                 foreach($validator->errors()->all() as $error){
                     return response()->json([
                         'msg' => $error,
                         'status' => 'FALSE'
                     ]);
                 }
            }else{
                $checking_exits_record =  OverTime::where('ovt_emp_id',$request_data->employee_id)->where('over_time_date', $request_data->ovt_date)->get()->toArray();

                if(empty($checking_exits_record) && empty($checking_exits_record[0]['ovt_payroll_id'])){
                    $add_overtime = new Overtime();
                    $add_overtime->ovt_emp_id =  $request_data->employee_id;
                    $add_overtime->extra_hours = $request_data->extra_hours;
                    $add_overtime->over_time_date = $request_data->ovt_date;
                    $add_overtime->amount_payable = $request_data->amount_payable;
                    $add_overtime->over_time_salary = $request_data->salary_amount;
                    $add_overtime->over_time_created_by =	session('user_session');
                    $add_overtime->over_time_reibursement_status = 0;
                    $add_overtime->over_time_created_at	=  date('h:i:s a Y F, d');
                    $process = $add_overtime->save();
        
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Overtime reimburesement amount payable updated successfully",
                            
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Overtime reimburesement not amount payable updated successfully",
                            
                        ]);
                    }
                }else{
                  
                    if(!empty($checking_exits_record) && empty($checking_exits_record[0]['ovt_payroll_id'])){
                        
                        if($checking_exits_record[0]['over_time_reibursement_status'] != 1){
                            $process = OverTime::where('ovt_emp_id',$request_data->employee_id)->where('over_time_date',$request_data->ovt_date)->update(['amount_payable'=>$request_data->amount_payable]);
                            
                            if($process == 1){
                                
                                return response()->json([
                                    'status' => 'TRUE',
                                    'msg' => "Overtime reimburesement amount payable updated successfully.",
                                    
                                ]);
                            }else{
                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => "Overtime reimburesement not amount payable updated successfully.",
                                    
                                ]);
                            }
                        }
                       
                    }
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You can't responed this overtime.",
                        
                    ]);
                    
                }
            }
          
           
        }catch(\Exception $e){

            $this->create_error_log('Amount Payable  Record Update OverTime', $e);
            if(!config('app.debug')){
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

            ActivityTracker::track_activity('Amount Payable  Record Update OverTime');
            
        }
    }

    protected function monthly_attendance_all_employee($employee_id = NULL ,$month = NULL , $year = NULL, $location = NULL, $only_employee_ids = NULL){


        $all_employees = Employees::whereRaw($employee_id)
        ->whereRaw($location)
        ->when($only_employee_ids !== NULL, function($query) use($only_employee_ids){
            $query->whereIn('employee_id', $only_employee_ids);
        })
        ->orderBy('employee_id','DESC')
        ->get(['employee_id','employee_code','employee_name','employee_isactive'])->toArray();
		
        if(!empty($all_employees)){

            if(empty($month) || empty($year)){
                $month = date('m');
                $year = date('Y');
            }

            // one query per 1000 employees instead of one per employee
            $attendance_by_employee = [];
            foreach(array_chunk(array_column($all_employees, 'employee_id'), 1000) as $employee_ids_chunk){
                $month_attendance = Attendance::whereIn('att_employee_id',$employee_ids_chunk)->where('att_for_month',$month)->where('att_for_year',$year)->orderBy('attendance_id')->get()->toArray();
                foreach($month_attendance as $attendance_row){
                    $attendance_by_employee[$attendance_row['att_employee_id']][] = $attendance_row;
                }
            }

            foreach($all_employees as $employee){

                $all_attendances = $attendance_by_employee[$employee['employee_id']] ?? [];

                $atts = array('attendance' => $all_attendances);
                $result[] = array_merge( $employee , $atts );

            }
         
           return $result;

        }else{

           return 'No Employee Found';

        }
    }
}
