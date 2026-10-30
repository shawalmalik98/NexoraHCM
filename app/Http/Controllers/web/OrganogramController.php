<?php

namespace App\Http\Controllers\web;

use App\Helpers\CustomHelper;
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
        ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_isactive', 1)->where(function($query){ $query->whereNull('employee_senior_manager')->orWhere('employee_senior_manager', 0)->orWhereColumn('employee_senior_manager', 'tbl_employees.employee_id'); })->get(['employee_id','employee_name','department_name','designation_name','employee_senior_manager as parent_id'])->toArray();
        
        if(!empty($get_ceo_of_company)){
            array_push($all_employees , $get_ceo_of_company[0]);

            $get_all_senior_managers_ids = Employees::
            whereNotIn('employee_senior_manager', array_column($get_ceo_of_company , 'employee_id'))->groupBy('employee_senior_manager')->pluck('employee_senior_manager')->toArray();
            // lookups instead of searching the id list for every employee
            $senior_manager_set = array_flip(array_filter($get_all_senior_managers_ids));

            $get_all_senior_managers = Employees::join('tbl_departments','tbl_employees.employee_department','=','tbl_departments.department_id')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->whereIn('employee_id', $get_all_senior_managers_ids)->where('employee_isactive', 1)->where('employee_is_manager', 1)->get(['employee_id','employee_name','department_name','designation_name','employee_senior_manager','employee_junior_manager'])->toArray();

            if(!empty($get_all_senior_managers)){
                foreach($get_all_senior_managers as $senior_managers){
                    if(isset($senior_manager_set[$senior_managers['employee_senior_manager'] ?? ''])){
                        $senior_managers['parent_id'] = $senior_managers['employee_senior_manager'];
                        unset($senior_managers['employee_senior_manager'], $senior_managers['employee_junior_manager']);
                        array_push($all_employees , $senior_managers);
                    }elseif(isset($senior_manager_set[$senior_managers['employee_junior_manager'] ?? ''])){
                        $senior_managers['parent_id'] = $senior_managers['employee_junior_manager'];
                        unset($senior_managers['employee_senior_manager'], $senior_managers['employee_junior_manager']);
                        array_push($all_employees , $senior_managers);
                    }else{
                        $senior_managers['parent_id'] = $senior_managers['employee_senior_manager'];
                        unset($senior_managers['employee_senior_manager'], $senior_managers['employee_junior_manager']);
                        array_push($all_employees , $senior_managers);
                    }
                }
            }

            $get_junior_managers_ids = Employees::
            whereNotIn('employee_junior_manager',$get_all_senior_managers_ids)->whereNotIn('employee_junior_manager',array_column($get_ceo_of_company , 'employee_id'))->groupBy('employee_junior_manager')->pluck('employee_junior_manager')->toArray();
            $junior_manager_set = array_flip(array_filter($get_junior_managers_ids));

            $get_all_junior_managers = Employees::join('tbl_departments','tbl_employees.employee_department','=','tbl_departments.department_id')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->whereIn('employee_id', $get_junior_managers_ids)->where('employee_isactive', 1)->where('employee_is_manager', 1)->get(['employee_id','employee_name','department_name','designation_name','employee_senior_manager','employee_junior_manager'])->toArray();

            if(!empty($get_all_junior_managers)){
                foreach($get_all_junior_managers as $junior_managers){
                    if(isset($senior_manager_set[$junior_managers['employee_senior_manager'] ?? ''])){
                        $junior_managers['parent_id'] = $junior_managers['employee_senior_manager'];
                        unset($junior_managers['employee_senior_manager'], $junior_managers['employee_junior_manager']);
                        array_push($all_employees , $junior_managers);
                    }elseif(isset($senior_manager_set[$junior_managers['employee_junior_manager'] ?? ''])){
                        $junior_managers['parent_id'] = $junior_managers['employee_junior_manager'];
                        unset($junior_managers['employee_senior_manager'], $junior_managers['employee_junior_manager']);
                        array_push($all_employees , $junior_managers);
                    }else{
                        $junior_managers['parent_id'] = $junior_managers['employee_senior_manager'];
                        unset($junior_managers['employee_senior_manager'], $junior_managers['employee_junior_manager']);
                        array_push($all_employees , $junior_managers);
                    }
                }
            }

            $get_all_non_manager_employees = Employees::join('tbl_departments','tbl_employees.employee_department','=','tbl_departments.department_id')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->whereNotIn('employee_id', array_column($all_employees , 'employee_id'))->where('employee_isactive', 1)->where('employee_is_manager', 0)->get(['employee_id','employee_name','department_name','designation_name','employee_senior_manager','employee_junior_manager'])->toArray();
            
            if(!empty($get_all_non_manager_employees)){
                foreach($get_all_non_manager_employees as $employee){
                    if(isset($junior_manager_set[$employee['employee_senior_manager'] ?? ''])){
                        $employee['parent_id'] = $employee['employee_senior_manager'];
                        unset($employee['employee_senior_manager'], $employee['employee_junior_manager']);
                        array_push($all_employees , $employee);
                    }elseif(isset($junior_manager_set[$employee['employee_junior_manager'] ?? ''])){
                        $employee['parent_id'] = $employee['employee_junior_manager'];
                        unset($employee['employee_senior_manager'], $employee['employee_junior_manager']);
                        array_push($all_employees , $employee);
                    }else{
                        if(isset($senior_manager_set[$employee['employee_senior_manager'] ?? ''])){
                            $employee['parent_id'] = $employee['employee_senior_manager'];
                            unset($employee['employee_senior_manager'], $employee['employee_junior_manager']);
                            array_push($all_employees , $employee);
                        }elseif(isset($senior_manager_set[$employee['employee_junior_manager'] ?? ''])){
                            $employee['parent_id'] = $employee['employee_junior_manager'];
                            unset($employee['employee_senior_manager'], $employee['employee_junior_manager']);
                            array_push($all_employees , $employee);
                        }else{
                            $employee['parent_id'] = $employee['employee_senior_manager'];
                            unset($employee['employee_senior_manager'], $employee['employee_junior_manager']);
                            array_push($all_employees , $employee);
                        }
                    }
                }
            }

            $filtered_employees = $all_employees;

            // a chart of thousands of boxes freezes the browser: large companies see the reporting lines
            // (everyone with people under them) and each box shows how many members it leads directly
            if(count($all_employees) > 1500){
                $parent_ids = [];
                foreach($all_employees as $employee){
                    if($employee['parent_id'] != $employee['employee_id']){
                        $parent_ids[$employee['parent_id']] = true;
                    }
                }
                $hidden_team = [];
                $filtered_employees = [];
                foreach($all_employees as $employee){
                    if(isset($parent_ids[$employee['employee_id']]) || $employee['parent_id'] == $employee['employee_id']){
                        $filtered_employees[] = $employee;
                    }else{
                        $hidden_team[$employee['parent_id']] = ($hidden_team[$employee['parent_id']] ?? 0) + 1;
                    }
                }
                foreach($filtered_employees as &$employee){
                    $employee['hidden_team'] = $hidden_team[$employee['employee_id']] ?? 0;
                }
                unset($employee);
            }
            $title = '';
            $send_data = compact('filtered_employees','title','login_user');
            return view('organogram.all-employees', $send_data);
        }
    }
}