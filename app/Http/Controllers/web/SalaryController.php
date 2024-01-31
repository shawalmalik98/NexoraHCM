<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Employees, Salary, Earning, Deduction};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;

class SalaryController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const TITLE_GLOBAL = 'manage-salary';

    protected function index(){
        
        try{

            $title = SELF::TITLE_GLOBAL;
            
            $login_user = $this->get_login_user_detail();

            $employees_salary = Salary::join('tbl_employees','tbl_salary.salary_employee_id','=','tbl_employees.employee_id')
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->join('tbl_earning','tbl_salary.salary_id','=','tbl_earning.earning_salary_id')
            ->join('tbl_deduction','tbl_salary.salary_id','=','tbl_deduction.deduction_salary_id')
            ->get(['employee_id','employee_code','employee_name','department_name','designation_name','salary_id','basic_salary','house_rent','utilities','gross_adjustment','created_at','earning_title','earning_amount','deduction_title','deduction_amount'])
            ->toArray();
           
            $send_data = compact('title','login_user','employees_salary');
            
            return view('salary-screen.salary')->with($send_data);
        }
        catch(\Exception $e){

            $this->create_error_log('View All Employees Salary', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Employees Salary');
    
        }
    }
}
