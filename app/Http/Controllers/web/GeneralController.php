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
        $departments = Department::all()->toArray();
        $departments_hod =  Department::where('department_hod',$login_user[0]['employee_id'])->get('department_hod')->toArray();
        $send_data = compact('title','login_user', 'departments','departments_hod');
        return view('incentive.incentive')->with($send_data);
    }

    public function saveIncentive(Request $request){
        $designations = $request->designation_name;
        $brands = $request->brand_name;
        $targetTypes = $request->target_type;
        $incentives = $request->incentive;

        if (count($designations) == count($brands) && count($brands) == count($targetTypes) && count($targetTypes) == count($incentives)) {
            for ($i = 0; $i < count($designations); $i++) {
                DB::table('tbl_incentive')->insert([
                    'designation' => $designations[$i],
                    'brand' => $brands[$i],
                    'target_type' => $targetTypes[$i],
                    'incentive' => $incentives[$i],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Data mismatch']);
        }
    }

}
