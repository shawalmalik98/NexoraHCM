<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{LeaveManagement};
use App\Traits\{LoginEmployee, LogGenerateTrait};

class LeavesPolicyControlller extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const MAIN_TITLE = "leave-policy";
    const CHILD_TITLE = "leave-policy";

    protected function index(){
        $title = Self::MAIN_TITLE;
        $login_user = $this->get_login_user_detail();
        $send_data = compact('login_user','title');
        return view('leave.leave-policy', $send_data);
    }

    protected function add_new_leave_policy(){
        $title = Self::CHILD_TITLE;
        $login_user = $this->get_login_user_detail();

        $manage_leaves = LeaveManagement::get()->toArray();

        $send_data = compact('login_user','title','manage_leaves');
        return view('leave.add-leave-policy', $send_data);
    }
}
