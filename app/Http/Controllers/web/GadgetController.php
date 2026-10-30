<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{EmployeeGadget, GadgetAllotment, Employees};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;

class GadgetController extends Controller
{
    
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "gadgets-inventory";
    const SUB_TITLE = "gadgets-allotment";

    protected function index(Request $gadgetData){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();
                
            $where_clause = "1=1 ";
            if(!empty($gadgetData->gadget_type)){

                $where_clause = 'gadget_type = "'.$gadgetData->gadget_type.'"';

            }

            $all_gadgets = EmployeeGadget::whereRaw($where_clause)->orderBy('gadget_id','DESC')->get()->toArray();
           
            $send_data = compact('title','login_user','all_gadgets');

            return view('gadget.gadget', $send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('List Inventory Gadgets', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('List Inventory Gadgets');
          
        }
       
    }

    protected function add_new_gadget_form(){
        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();
                
            $send_data = compact('title','login_user');
            
            return view('gadget.assign-gadget', $send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('Assign Gadgets Inventory Form', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('Assign gadgets Inventory Form');
           
        }
       
	}

    protected function assign_post_request(Request $gadgetData){
       
		try{

            $validator = Validator::make($gadgetData->all(), [
                'gadget_type' => 'required',
                'gadget_brand' => 'required',
                'gadget_model_no' => 'required',
                'gadget_serial_no' => 'required',
                'gadget_configuration' => 'required',
                'gadget_mac_wifi' => 'required',
                'gadget_mac_lan' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $assign_gadget = new EmployeeGadget();
                $assign_gadget->gadget_type = $gadgetData->input('gadget_type');
                $assign_gadget->gadget_brand = $gadgetData->input('gadget_brand');
                $assign_gadget->gadget_model_no = $gadgetData->input('gadget_model_no');
                $assign_gadget->gadget_serial_no = $gadgetData->input('gadget_serial_no');
                $assign_gadget->gadget_configuration = $gadgetData->input('gadget_configuration');
                $assign_gadget->gadget_mouse = $gadgetData->input('gadget_mouse');
                $assign_gadget->gadget_keyboard = $gadgetData->input('gadget_keyboard');
                $assign_gadget->gadget_lcd = $gadgetData->input('gadget_lcd');
                $assign_gadget->gadget_headphone = $gadgetData->input('gadget_headphone');
                $assign_gadget->gadget_mac_wifi = $gadgetData->input('gadget_mac_wifi');
                $assign_gadget->gadget_mac_lan = $gadgetData->input('gadget_mac_lan');
                $assign_gadget->gadget_create_date = date('Y-m-d');
                $process = $assign_gadget->save();

                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Gadget successfully addedd in inventory",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Gadget addition in inventory failed.",
                    ]);
                }
            }    
        }
        catch(\Exception $e){
            $this->create_error_log('Add Gadget Inventory ', $e);
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
        }
        finally{
            ActivityTracker::track_activity('Add Gadget Inventory');
        }
	}

    protected function view_gadget($gadget_id){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();
    
            $all_employees = Employees::join('tbl_departments','tbl_departments.department_id', '=', 'tbl_employees.employee_department')->where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name','employee_department','department_name'])->toArray();
        
            $gadget_detail = EmployeeGadget::where('gadget_id', $gadget_id)->get()->toArray();
    
            $gadget_allotment = GadgetAllotment::where('gadget_id', $gadget_id)->orderBy('allotment_id','DESC')->get()->toArray();
           
            $send_data = compact('title','login_user','gadget_detail','all_employees','gadget_allotment');
    
            return view('gadget.view-gadget-detail',$send_data);
        }catch(\Exception $e){
            $this->create_error_log('View Inventory Gadgets Detail', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Inventory Gadgets Detail');
          
        }
        
       
        
    }

    protected function update_gadget_details(Request $gadgetData){
      
        try{

            $validator = Validator::make($gadgetData->all(), [
                'gadget_type' => 'required',
                'gadget_brand' => 'required',
                'gadget_model_no' => 'required',
                'gadget_serial_no' => 'required',
                'gadget_configuration' => 'required',
                'gadget_mac_wifi' => 'required',
                'gadget_mac_lan' => 'required',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $update_gadget = EmployeeGadget::find($gadgetData->input('gadget_id'));

                if(!empty($update_gadget)){

                    $update_gadget->gadget_type = $gadgetData->input('gadget_type');
                    $update_gadget->gadget_brand = $gadgetData->input('gadget_brand');
                    $update_gadget->gadget_model_no = $gadgetData->input('gadget_model_no');
                    $update_gadget->gadget_serial_no = $gadgetData->input('gadget_serial_no');
                    $update_gadget->gadget_configuration = $gadgetData->input('gadget_configuration');
                    $update_gadget->gadget_mouse = $gadgetData->input('gadget_mouse');
                    $update_gadget->gadget_keyboard = $gadgetData->input('gadget_keyboard');
                    $update_gadget->gadget_lcd = $gadgetData->input('gadget_lcd');
                    $update_gadget->gadget_headphone = $gadgetData->input('gadget_headphone');
                    $update_gadget->gadget_mac_wifi = $gadgetData->input('gadget_mac_wifi');
                    $update_gadget->gadget_mac_lan = $gadgetData->input('gadget_mac_lan');
                    $update_gadget->gadget_create_date = date('Y-m-d');
                    $process = $update_gadget->save();

                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Gadget updated successfully",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Gadget updation failed.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Gadget id not found.",
                    ]);
                }
                
            }    
            
        }
        catch(\Exception $e){
            $this->create_error_log('Update Gadget Details', $e);
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
        }
        finally{
            ActivityTracker::track_activity('Update Gadget Details');
        }
    }


    protected function assign_gadget_employee(Request $gadgetData){
    
        try{
         
            if($gadgetData->employee_code == null){
        
                $update_gadget = EmployeeGadget::find($gadgetData->gadget_id);
              
                $update_gadget->gadget_status = 2;
                $update_gadget->save();

                
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => "Gadget return to this employee successfully",
                ]);

            }
            
            $allotment =  new GadgetAllotment;
        
            $where_clause = '1=1' ;
            
            $where_clause .= ' AND gadget_status = 1 and tbl_gadget_allotment.allotment_id in (select max(allotment_id) from tbl_gadget_allotment group by gadget_id  )';
    
            $gadget_allotments = GadgetAllotment::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_gadget_allotment.allotment_employee_id')
            ->join('tbl_employees_gadgets', 'tbl_employees_gadgets.gadget_id', '=', 'tbl_gadget_allotment.gadget_id')
            ->whereRaw($where_clause)
            ->orderBy('allotment_id','DESC')
            ->get('employee_id')
            ->toArray();
           
            if(!empty($gadget_allotments) && !empty($gadget_allotments[0]['employee_id'] == $gadgetData->employee_code)){

                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Already assign gadget",
                ]);
            
            }else{
                $allotment->gadget_id = $gadgetData->gadget_id;
                $allotment->allotment_employee_id = $gadgetData->employee_code;
                // $allotment->allotment_status = $gadgetData->gadget_status;
                $allotment->allotment_created_date = date('Y-m-d');
        
                $process = $allotment->save();
                
                if($process == 1){

                    $update_gadget = EmployeeGadget::find($gadgetData->gadget_id);
                
                    $status = 1;
                    
                    if($gadgetData->status == 2){
                        $status = 2;
                    }

                    $update_gadget->gadget_status =  $status;
                    $update_gadget->save();

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Gadget assign to employee successfully",
                    ]);
                }
            }
            
        }
        catch(\Exception $e){
            $this->create_error_log('Assign Gadgets', $e);
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
            
        }
        finally{

            ActivityTracker::track_activity('Assign gadgets to employee page');
          
        }
       
    }

    protected function gadget_allotment(){
        try{
            $title = Self::SUB_TITLE;

            $login_user = $this->get_login_user_detail();

            $where_clause = '1=1' ;
            
            $where_clause .= ' AND gadget_status = 1 and tbl_gadget_allotment.allotment_id in (select max(allotment_id) from tbl_gadget_allotment group by gadget_id  )';
    
            $gadget_allotments = GadgetAllotment::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_gadget_allotment.allotment_employee_id')
            ->join('tbl_employees_gadgets', 'tbl_employees_gadgets.gadget_id', '=', 'tbl_gadget_allotment.gadget_id')
            ->whereRaw($where_clause)
            ->orderBy('allotment_id','DESC')
            ->get(['employee_id','gadget_type','gadget_brand','gadget_model_no','gadget_serial_no','gadget_mac_wifi','gadget_mac_lan'])
            ->toArray();
           
            $send_data = compact('title','login_user','gadget_allotments');

            return view('gadget.gadget-allotment', $send_data);
           
        }
        catch(\Exception $e){
            $this->create_error_log('Gadgets List Allotment', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('Gadgets List Allotment');
           
        }
    }



    protected function delete_gadget($gadget_id){
        try{
            EmployeeGadget::where('gadget_id',$gadget_id)->delete();
        }
        catch(\Exception $e){
            $this->create_error_log("Delete Gadget", $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Something went wrong. Please try again.",
            ]);
        }
        finally{
            ActivityTracker::track_activity('Delete Gadget');
            return redirect('gadgets-inventory');
        }
    }

  
}
