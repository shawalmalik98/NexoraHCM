<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Models\{Department, Designation, Employees};

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

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'period' => 'required',
        ]);
        Excel::import(new IncentiveImport($request->input('period')), $request->file('file'));
        return back()->with('success', 'Incentive data imported successfully.');
    }
}
