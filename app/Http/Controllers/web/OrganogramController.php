<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employees;
use App\Traits\{LoginEmployee};

class OrganogramController extends Controller
{
    use LoginEmployee;

    protected function index(){

        $all_employees = array();

        $login_user = $this->get_login_user_detail();

        $get_ceo_of_company = Employees::join('tbl_departments','tbl_employees.employee_department','=','tbl_departments.department_id')
        ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_department', 1)->where('employee_designation', 98)->where('employee_isactive', 1)->get(['employee_id','employee_name','department_name','designation_name','employee_ra as parent_id'])->toArray();
        
        if($get_ceo_of_company){
            array_push($all_employees , $get_ceo_of_company[0]);

            $get_all_senior_managers_ids = Employees::
            whereNotIn('employee_ra', array_column($get_ceo_of_company , 'employee_id'))->groupBy('employee_ra')->pluck('employee_ra')->toArray();

            $get_all_senior_managers = Employees::join('tbl_departments','tbl_employees.employee_department','=','tbl_departments.department_id')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->whereIn('employee_id', $get_all_senior_managers_ids)->where('employee_isactive', 1)->where('employee_can_ra', 1)->get(['employee_id','employee_name','department_name','designation_name','employee_ra','employee_ra2'])->toArray();

            if(!empty($get_all_senior_managers)){
                foreach($get_all_senior_managers as $senior_managers){
                    if(in_array($senior_managers['employee_ra'], array_filter($get_all_senior_managers_ids) )){
                        $senior_managers['parent_id'] = $senior_managers['employee_ra'];
                        unset($senior_managers['employee_ra'], $senior_managers['employee_ra2']);
                        array_push($all_employees , $senior_managers);
                    }elseif(in_array($senior_managers['employee_ra2'], array_filter($get_all_senior_managers_ids) )){
                        $senior_managers['parent_id'] = $senior_managers['employee_ra2'];
                        unset($senior_managers['employee_ra'], $senior_managers['employee_ra2']);
                        array_push($all_employees , $senior_managers);
                    }else{
                        $senior_managers['parent_id'] = $senior_managers['employee_ra'];
                        unset($senior_managers['employee_ra'], $senior_managers['employee_ra2']);
                        array_push($all_employees , $senior_managers);
                    }
                }
            }

            $get_junior_managers_ids = Employees::
            whereNotIn('employee_ra2',$get_all_senior_managers_ids)->whereNotIn('employee_ra2',array_column($get_ceo_of_company , 'employee_id'))->groupBy('employee_ra2')->pluck('employee_ra2')->toArray();

            $get_all_junior_managers = Employees::join('tbl_departments','tbl_employees.employee_department','=','tbl_departments.department_id')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->whereIn('employee_id', $get_junior_managers_ids)->where('employee_isactive', 1)->where('employee_can_ra', 1)->get(['employee_id','employee_name','department_name','designation_name','employee_ra','employee_ra2'])->toArray();

            if(!empty($get_all_junior_managers)){
                foreach($get_all_junior_managers as $junior_managers){
                    if(in_array($junior_managers['employee_ra'], array_filter($get_all_senior_managers_ids) )){
                        $junior_managers['parent_id'] = $junior_managers['employee_ra'];
                        unset($junior_managers['employee_ra'], $junior_managers['employee_ra2']);
                        array_push($all_employees , $junior_managers);
                    }elseif(in_array($junior_managers['employee_ra2'], array_filter($get_all_senior_managers_ids) )){
                        $junior_managers['parent_id'] = $junior_managers['employee_ra2'];
                        unset($junior_managers['employee_ra'], $junior_managers['employee_ra2']);
                        array_push($all_employees , $junior_managers);
                    }else{
                        $junior_managers['parent_id'] = $junior_managers['employee_ra'];
                        unset($junior_managers['employee_ra'], $junior_managers['employee_ra2']);
                        array_push($all_employees , $junior_managers);
                    }
                }
            }

            $get_all_non_manager_employees = Employees::join('tbl_departments','tbl_employees.employee_department','=','tbl_departments.department_id')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->whereNotIn('employee_id', array_column($all_employees , 'employee_id'))->where('employee_isactive', 1)->where('employee_can_ra', 0)->get(['employee_id','employee_name','department_name','designation_name','employee_ra','employee_ra2'])->toArray();
            
            if(!empty($get_all_non_manager_employees)){
                foreach($get_all_non_manager_employees as $employee){
                    if(in_array($employee['employee_ra'], array_filter($get_junior_managers_ids) )){
                        $employee['parent_id'] = $employee['employee_ra'];
                        unset($employee['employee_ra'], $employee['employee_ra2']);
                        array_push($all_employees , $employee);
                    }elseif(in_array($employee['employee_ra2'], array_filter($get_junior_managers_ids) )){
                        $employee['parent_id'] = $employee['employee_ra2'];
                        unset($employee['employee_ra'], $employee['employee_ra2']);
                        array_push($all_employees , $employee);
                    }else{
                        if(in_array($employee['employee_ra'], array_filter($get_all_senior_managers_ids) )){
                            $employee['parent_id'] = $employee['employee_ra'];
                            unset($employee['employee_ra'], $employee['employee_ra2']);
                            array_push($all_employees , $employee);
                        }elseif(in_array($employee['employee_ra2'], array_filter($get_all_senior_managers_ids) )){
                            $employee['parent_id'] = $employee['employee_ra2'];
                            unset($employee['employee_ra'], $employee['employee_ra2']);
                            array_push($all_employees , $employee);
                        }else{
                            $employee['parent_id'] = $employee['employee_ra'];
                            unset($employee['employee_ra'], $employee['employee_ra2']);
                            array_push($all_employees , $employee);
                        }
                    }
                }
            }

            $filtered_employees = $all_employees;
            $title = '';
            $send_data = compact('filtered_employees','title','login_user');
            return view('organogram.all-employees', $send_data);
        }
    }
}