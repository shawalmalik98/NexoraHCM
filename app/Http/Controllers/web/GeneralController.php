<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Models\{Department, Designation, Employees};
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    use LoginEmployee, LogGenerateTrait;
    const MAIN_TITLE = "incentive";
    public function index(){
        $title = Self::MAIN_TITLE;
        $login_user = $this->get_login_user_detail();
        $get_incentive = DB::select('SELECT * FROM tbl_incentive');
        $incentive = json_decode(json_encode($get_incentive), TRUE);
        $send_data = compact('title','login_user','incentive');
        return view('incentive.incentive')->with($send_data);
    }

    public function saveIncentive(Request $request){
        $designations = $request->designation_name;
        $brands = $request->brand_name;
        $targetTypes = $request->target_type;
        $incentives = $request->incentive;
        $incentive_period = $request->incentive_period;
        $target_value = $request->target_value;

        if (count($designations) == count($brands) && count($brands) == count($targetTypes) && count($targetTypes) == count($incentives)) {
            for ($i = 0; $i < count($designations); $i++) {
                DB::table('tbl_incentive')->insert([
                    'incentive_period' => $incentive_period,
                    'employee_type' => $designations[$i],
                    'brand' => $brands[$i],
                    'productive_criteria' => $targetTypes[$i],
                    'incentive' => $incentives[$i],
                    'target_value' => $target_value[$i],
                ]);
            }
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Data mismatch']);
        }
    }

}
