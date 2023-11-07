<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{AppFlowModules, Department, AppFlowDetail};
use App\Traits\{LoginEmployee, LogGenerateTrait};

class AppFlowController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "appflow";
    const DETAiL_TITLE = "appflow-detail";

    public function appflow_modules(){
        $title = Self::MAIN_TITLE;
        $login_user = $this->get_login_user_detail();
        $appflow_modules = AppFlowModules::get()->toArray();
        $send_data = compact('appflow_modules','login_user','title');
        return view('appflow.appflow_modules', $send_data);
    }

    public function appflow_detail($id){
        $title = Self::DETAiL_TITLE;
        $login_user = $this->get_login_user_detail();
        $appflow_module = AppFlowModules::where('appflow_module_id', $id)->get()->toArray();
        $appflow_detail = AppFlowDetail::where('appflow_module_id', $id)->get()->toArray();
        $appflow_detail_count = AppFlowDetail::where('appflow_module_id', $id)->count();
        $departments = Department::get()->toArray();
        $send_data = compact('appflow_module','login_user','title','departments','appflow_detail','appflow_detail_count');
        // dd($appflow_detail);
        return view('appflow.appflow_detail', $send_data);
    }

    public function update_appflow_detail(Request $request){
        // dd($request->input('module_id'));
        AppFlowDetail::where('appflow_module_id',$request->input('module_id'))->delete();
        if(!empty($request->input('sequence_number'))){
            foreach($request->input('sequence_number') as $key => $seq){
                if(!empty($seq) && !empty($request->input('departments')[$key])){
                    $add_new_appflow_details = new AppFlowDetail();
                    $add_new_appflow_details->appflow_module_id = $request->input('module_id');
                    $add_new_appflow_details->appflow_seq_no = $seq;
                    $add_new_appflow_details->appflow_department = $request->input('departments')[$key];
                    $add_new_appflow_details->save();
                }
            }
            return response()->json([
                'status' => 'TRUE',
                'msg' => "AppFlow Detail Updated Successfully.",
            ]);
        }else{
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Fields are required.",
            ]);
        }
    }
}
