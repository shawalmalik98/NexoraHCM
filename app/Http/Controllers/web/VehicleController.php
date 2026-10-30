<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Vehicle, VehicleAllotment, Employees, VehicleImages, VehicleMaintenance};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = 'manage-vehicles';

    protected function index(){
        try{
            $title = Self::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

            $get_all_vehicles = Vehicle::leftJoinSub(function ($query) {
                $query->from('tbl_vehicles_allotment')
                    ->whereNull('allotment_cancellation_date');
            }, 'tbl_vehicles_allotment', function ($join) {
                $join->on('tbl_vehicles.vehicle_id', '=', 'tbl_vehicles_allotment.allotment_vehicle_id');
            })
            ->orderBy('vehicle_id', 'DESC')
            ->get()
            ->toArray();
            
           
            $send_data = compact('title', 'login_user',   'get_all_vehicles');

            return view('vehicle.all-vehicles')->with($send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View All Vehicles', $e);
            $send_data = compact('title');

            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View All Vehicles');

        }
    }

    protected function download_vehicles(Request $request){
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
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/vehicles';</script>";
            }

            $get_all_vehicles = Vehicle::leftJoinSub(function ($query) {
                $query->from('tbl_vehicles_allotment')
                    ->whereNull('allotment_cancellation_date');
            }, 'tbl_vehicles_allotment', function ($join) {
                $join->on('tbl_vehicles.vehicle_id', '=', 'tbl_vehicles_allotment.allotment_vehicle_id');
              
            })
            ->leftJoin('tbl_employees','tbl_vehicles_allotment.allotment_employee_id','tbl_employees.employee_id')  ->orderBy('vehicle_id', 'DESC')
            ->get(['employee_id','employee_name','employee_code','vehicle_id','vehicle_company_name','vehicle_name','vehicle_model','vehicle_number','allotment_employee_id'])
            ->toArray();

           
            $send_data = compact('login_user','get_all_vehicles');
            if($security_variable == TRUE){

                $pdf = Pdf::loadView('vehicle.download-all-vehicles-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Office Vehicle Report'.'.pdf');
            }
        }
        catch(\Exception $e){
            $this->create_error_log('download All Vehicles pdf', $e);
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('download All Vehicles pdf');

        }
    }

    protected function add_vehicle_form(){
        try{
            $title = self::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

            $send_data = compact('title','login_user');

            return view('vehicle.add-vehicle')->with($send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View Add Vehicle Page', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Add Vehicle Page');
  
        }
    }

    protected function create_vehicle_function(Request $vehicleData){

        try{
            
            $validator = Validator::make($vehicleData->all(), [
                'vehicle_type' => 'required',
                'vehicle_company_name' => 'required',
                'vehicle_name' => 'required',
                'vehicle_number' => ['required','unique:tbl_vehicles,vehicle_number'],
                'vehicle_model' => 'required|numeric',
                'vehicle_color' => 'required',
                'vehicle_engine_number' => 'required',
                'vehicle_chassis_number' => 'required',
                // 'vehicle_status' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $add_vehicle = new Vehicle();
                $add_vehicle->vehicle_type = $vehicleData->input('vehicle_type');
                $add_vehicle->vehicle_company_name = $vehicleData->input('vehicle_company_name');
                $add_vehicle->vehicle_name = $vehicleData->input('vehicle_name');
                $add_vehicle->vehicle_number = $vehicleData->input('vehicle_number');
                $add_vehicle->vehicle_model = $vehicleData->input('vehicle_model');
                $add_vehicle->vehicle_color = $vehicleData->input('vehicle_color');
                $add_vehicle->vehicle_engine_number = $vehicleData->input('vehicle_engine_number');
                $add_vehicle->vehicle_chassis_number = $vehicleData->input('vehicle_chassis_number');
                $add_vehicle->vehicle_amount = $vehicleData->input('vehicle_amount');
                // $add_vehicle->vehicle_status = $vehicleData->input('vehicle_status');
                $add_vehicle->vehicle_status = '1';
                $add_vehicle->vehicle_ownership = $vehicleData->input('vehicle_ownership');
                // $add_vehicle->vehicle_installment = $vehicleData->input('vehicle_installment');
                // $add_vehicle->vehicle_installment_date = $vehicleData->input('vehicle_installment_date');
                $add_vehicle->vehicle_tax_paid_till = $vehicleData->input('tax_paid_till');
                $process = $add_vehicle->save();
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Vehicle added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Vehicle addition failed. Please try again after some time.",
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log('Create Vehicle', $e);
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
            ActivityTracker::track_activity('Create Vehicle');
        }

    }

    protected function store_vehicle_images(Request $request){
        try {
            $document = $request->file('file');
            $document_name = $document->getClientOriginalName();
            $document->storeAs('portal_documents/vehicles/', $document_name);
            $document_upload = new VehicleImages();
            $document_upload->vehicle_img_vehicle_id = $request->input('vehicle_id');
            $document_upload->vehicle_img_name = $document_name;
            $document_upload->vehicle_img_upload_date = date('h:i:s a d F, Y');
            $process = $document_upload->save();
        }catch (\Exception $e) {
            $this->create_error_log('Store Vehicle Images', $e);
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
        }finally {
            ActivityTracker::track_activity('Store Vehicle Images');
            return $document_name;
        }
    }

    protected function update_vehicle_function(Request $vehicleData){

        try {

            $validator = Validator::make($vehicleData->all(), [
                'vehicle_type' => 'required',
                'vehicle_company_name' => 'required',
                'vehicle_name' => 'required',
                'vehicle_number' => ['required','unique:tbl_vehicles,vehicle_number,' .$vehicleData->input('vehicle_id'). ' ,vehicle_id'],
                'vehicle_model' => 'required|numeric',
                'vehicle_color' => 'required',
                'vehicle_engine_number' => 'required',
                'vehicle_chassis_number' => 'required',
                // 'vehicle_status' => 'required',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $update_vehicle = Vehicle::find($vehicleData->input('vehicle_id'));
                $update_vehicle->vehicle_type = $vehicleData->input('vehicle_type');
                $update_vehicle->vehicle_company_name = $vehicleData->input('vehicle_company_name');
                $update_vehicle->vehicle_name = $vehicleData->input('vehicle_name');
                $update_vehicle->vehicle_number = $vehicleData->input('vehicle_number');
                $update_vehicle->vehicle_model = $vehicleData->input('vehicle_model');
                $update_vehicle->vehicle_color = $vehicleData->input('vehicle_color');
                $update_vehicle->vehicle_engine_number = $vehicleData->input('vehicle_engine_number');
                $update_vehicle->vehicle_chassis_number = $vehicleData->input('vehicle_chassis_number');
                $update_vehicle->vehicle_amount = $vehicleData->input('vehicle_amount');
                // $update_vehicle->vehicle_status = $vehicleData->input('vehicle_status');
                $update_vehicle->vehicle_ownership = $vehicleData->input('vehicle_ownership');
                // $update_vehicle->vehicle_installment = $vehicleData->input('vehicle_installment');
                // $update_vehicle->vehicle_installment_date = $vehicleData->input('vehicle_installment_date');
                $update_vehicle->vehicle_tax_paid_till = $vehicleData->input('vehicle_tax_paid_till');
                $process = $update_vehicle->save();
                if ($process == 1) {
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Vehicle updated successfully.',
                    ]);
                } else {
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>
                            'Vehicle update failed. Please try again after some time.',
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->create_error_log('Update Vehicle Detail', $e);
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
        } finally {
            ActivityTracker::track_activity('Update Vehicle Detail');
        }
    }

    protected function vehicle_detail($vehicle_id){
        try{

            $title = self::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

            $get_maintenance_data = VehicleMaintenance::where('vehicle_id',$vehicle_id)->orderBy('maintenance_id', 'DESC')->get()->toArray();

            $get_vehicle_data = Vehicle::where('vehicle_id', $vehicle_id)->first()->toArray();

            $all_vehicles = VehicleAllotment::join('tbl_employees','tbl_vehicles_allotment.allotment_employee_id','=','tbl_employees.employee_id')
            ->select('tbl_vehicles_allotment.*','tbl_employees.employee_id','tbl_employees.employee_code','tbl_employees.employee_name','tbl_employees.employee_isactive')
            ->where('tbl_vehicles_allotment.allotment_vehicle_id','=',$vehicle_id)
            ->get()
            ->toArray();

            if(!empty($all_vehicles)){

                $all_employees = Employees::where('employee_isactive', 1)->orWhere('employee_id',$all_vehicles[0]['allotment_employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

            }else{

                $all_employees = Employees::where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();

            }

            $vehicle_images = VehicleImages::where('vehicle_img_vehicle_id','=',$vehicle_id)->get();

            $send_data = compact( 'title','login_user','get_maintenance_data','all_employees','get_vehicle_data','all_vehicles','vehicle_images');
            
            return view('vehicle.vehicle-detail')->with($send_data);
        }
        catch(\Exception $e){
            $this->create_error_log('View Vehicle Detail Page', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View Vehicle Detail Page');
           
        }
    }

    protected function create_maintenance(Request $maintenance_data){

        try{

            $add_maintenance = new VehicleMaintenance();
            $add_maintenance->vehicle_id = $maintenance_data->input('vehicle_id');
            $add_maintenance->maintenance_date = $maintenance_data->input('maintenance_date');
            $maintenance_sub = $maintenance_data->input('maintenance_sub');
            $maintenance_sub = explode(",", $maintenance_sub);
            $str = "";
            $maintenance_item = array();
            for ($i=1; $i <= $maintenance_data->input('maintenance_length')  ; $i++) {
                if(in_array($i , $maintenance_sub)){continue; $str ='yes';}

                $temp = (object) [
                    "item" => $maintenance_data->input('maintenance_item'.$i),
                    "value" => $maintenance_data->input('maintenance_value'.$i)
                ];
                array_push($maintenance_item, $temp);
            }
            $maintenance_item = json_encode($maintenance_item);

            $add_maintenance->maintenance_item = $maintenance_item;

            $result = $add_maintenance->save();

            if($result == 1){
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'Maintenance added successfully.',
                ]);
            } else {
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Maintenance added failed. Please try again after some time.',
                ]);
            }
        }
        catch(\Exception $e){
            $this->create_error_log('create_maintenance', $e);
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
            ActivityTracker::track_activity('create_maintenance');
        }
    }

    protected function update_maintenance(Request $maintenance_data){

        try{
            $maintenance_sub = $maintenance_data->input('maintenance_sub');
            $maintenance_sub = explode(",", $maintenance_sub);
            $str = "";
            $maintenance_item = array();
            for ($i=1; $i <= $maintenance_data->input('maintenance_length')  ; $i++) {
                if(in_array($i , $maintenance_sub)){continue; $str ='yes';}

                $temp = (object) [
                    "item" => $maintenance_data->input('maintenance_item'.$i),
                    "value" => $maintenance_data->input('maintenance_value'.$i)
                ];
                array_push($maintenance_item, $temp);
            }
            $maintenance_item=json_encode($maintenance_item);

            $result = VehicleMaintenance::where('maintenance_id',  $maintenance_data->input('maintenance_id'))
            ->update(['maintenance_date' => $maintenance_data->input('maintenance_date') , 'maintenance_item' => $maintenance_item]);

            if($result == 1){
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'Maintenance updated successfully.',
                ]);
            } else {
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Error in updating maintenance data.',
                ]);
            }
       }
        catch(\Exception $e){
            $this->create_error_log('Update_maintenance', $e);
            $send_data = compact('title');
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
            ActivityTracker::track_activity('update_maintenance');
        }
    }

    protected function delete_maintenance($maintenance_id){

        try{
         VehicleMaintenance::where('maintenance_id',$maintenance_id)->delete();
        }
        catch(\Exception $e){
            $this->create_error_log("Delete mentenance", $e);
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
            ActivityTracker::track_activity('Delete maintenance');

            return redirect('vehicles');
        }
    }
    protected function create_allotment(Request $allotment_data){

        try{

            $validator = Validator::make($allotment_data->all(), [
                'allotment_vehicle_date' => 'required',
                 'allotment_cancellation_date' => 'after:allotment_vehicle_date',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $check_vehicle_allot = VehicleAllotment::where('allotment_employee_id',$allotment_data->input('allotment_employee_id'))->where('allotment_vehicle_id',$allotment_data->input('allotment_vehicle_id'))->whereNull('allotment_cancellation_date')->get()->toArray();

                if(empty($check_vehicle_allot)){
                    $check_vehicle_status = false;
                    $check_vehicle_allots = VehicleAllotment::where('allotment_vehicle_id',$allotment_data->input('allotment_vehicle_id'))->get('allotment_cancellation_date')->toArray();

                    foreach($check_vehicle_allots as $check_vehicle_allot ){
                        if($check_vehicle_allot['allotment_cancellation_date'] == null){
                            $check_vehicle_status = true;
                        }
                    }
                    if($check_vehicle_status){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'This vehicle is already allotted to another employee.',

                        ]);

                    }else {
                        $add_allotment = new VehicleAllotment();
                        $add_allotment->allotment_vehicle_id = $allotment_data->input('allotment_vehicle_id');
                        $add_allotment->allotment_employee_id = $allotment_data->input('allotment_employee_id');
                        $add_allotment->allotment_vehicle_date =  $allotment_data->input('allotment_vehicle_date');
                        $add_allotment->allotment_cancellation_date = $allotment_data->input('allotment_cancellation_date');
                        $result = $add_allotment->save();

                        if($result == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => 'Allotment added successfully.',
                            ]);
                        } else {
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => 'Error in updating allotment data.',
                            ]);
                        }
                    }

                }else {
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'you have already added this vehicle.',
                    ]);
                }
            }

        }
        catch(\Exception $e){
            $this->create_error_log('create_allotment', $e);
            $send_data = compact('title');
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
            ActivityTracker::track_activity('create_Allotment');
        }
    }

    protected function update_allotment(Request $allotment_data){

        try{

            $validator = Validator::make($allotment_data->all(), [
                'allotment_vehicle_date' => 'required',
                'allotment_cancellation_date' => 'required|after:allotment_vehicle_date',

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $check_vehicle_allot = VehicleAllotment::where('allotment_employee_id',$allotment_data->input('allotment_employee_id'))->where('allotment_vehicle_id',$allotment_data->input('allotment_vehicle_id'))->orderBy('allotment_id','DESC')->get()->toArray();

                if(empty($check_vehicle_allot) || (!empty($check_vehicle_allot) && $check_vehicle_allot[0]['allotment_cancellation_date'] == null ) ){
                    $update_allotment = VehicleAllotment::find($allotment_data->input('allotment_id'));
                    $update_allotment->allotment_vehicle_id = $allotment_data->input('allotment_vehicle_id');
                    $update_allotment->allotment_employee_id = $allotment_data->input('allotment_employee_id');
                    $update_allotment->allotment_vehicle_date =  $allotment_data->input('allotment_vehicle_date');
                    $update_allotment->allotment_cancellation_date = $allotment_data->input('allotment_cancellation_date');
                    $result = $update_allotment->save();

                    if($result == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Allotment updated successfully.',
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' =>'Error in updating allotment data.',
                        ]);
                    }
                }else {
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' =>'you have already update this allotment.',
                    ]);
                }
            }

        }
        catch(\Exception $e){
            $this->create_error_log('Update_Allotment',$e);
            $send_data = compact('title');
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
            ActivityTracker::track_activity('update_Allotment');
        }
    }

    protected function delete_allotment($allotment_id){

        try{
            VehicleAllotment::where('allotment_id',$allotment_id)->delete();
        }
        catch(\Exception $e){
            $this->create_error_log("Delete Allotment", $e);
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
            ActivityTracker::track_activity('Delete Allotment');

            return redirect('vehicles');
        }

    }

    protected function download_vehicle_document(Request $request){
        try{
            if(!empty($request->path)){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
                return response()->download(storage_path('app/portal_documents/'.$request->path));
            }else{
                return redirect('/');
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
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
            ActivityTracker::track_activity($request->title);
        }
    }
}
