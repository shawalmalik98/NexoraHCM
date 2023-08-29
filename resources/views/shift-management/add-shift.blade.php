@extends('layout.main')

@section('main-section')
<style>
    .has-time{
        border: 1px solid greenyellow;
    }
</style>
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Manage Shift Timings</h1>
            </div>            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <h4 class="modal-title pl-4">Select Employees</h4>
                        </div>
                    </div>
                </div>

                

                <div class="card-body py-3">
                    <form  id="shiftEmployeeForm" class="form" autocomplete="off">

                        @if(!empty($login_user))
                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                            <div class="row mt-5" >
                                <div class="col-md-9 fv-row" id="selectDepartmentCalculate" >
                                    <label class="fs-6 fw-semibold mb-2">Department</label>
                                    <select class="form-select departments_all" id="departments_all" multiple="multiple" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Department" name="department[]" data-close-on-select="false" >
                                        @if(!empty($all_department))
                                            @foreach ($all_department as $department)
                                                <option value="{{ $department['department_id'] }}" >{{ $department['department_name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3 mb-8 mt-3 fv-row">
                                    
                                    <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                        <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                        <input class="form-check-input h-20px w-20px departments_all" type="checkbox" id="department_all_calculate" name="select_all" value="Select All" /> 
                                
                                    </label>
                                </div> 
                            </div>
                            @endif
                        @endif
                        <div class="row mt-5">
                            <div class="col-md-9 fv-row" id="selectEmployeeCalculate">
                                <label class="fs-6 fw-semibold mb-2">Employees</label>
                                <div class="col-md-12 fv-row">
                                    <select class="form-select  " data-control="select2" multiple="multiple" data-hide-search="true" data-close-on-select="false" data-placeholder="Select Employee" name="employee_id[]" id="employees">
                                        @if(!empty($employees))
                                            @foreach($employees as $employee)
                                            <option value="{{$employee['employee_id']}}">{{$employee['employee_code']}} - {{$employee['employee_name']}}</option>';
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                            </div>
                            <div class="col-md-3 mb-8 mt-3 fv-row">
                                        
                                <label class="fs-6 fw-semibold mb-2">All Employees</label>
                                    <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" id="employee_all_calculate" name="select_all_employee" value="Select All"  disabled/> 
                            
                                </label>
                            </div> 
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-5 mb-xl-8" id="tableForm" >

                <div class="card-header border-0 pt-5">
                    
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilter">
                        </div>
                    </div>
                </div> 
            
                <div class="card-body py-3">
                    <form id="responseEmployeeShiftForm" >
                        <table class="table table-bordered  table-row-bordered" id="system_datatable"> 
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <td class="ps-4"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="date" class="form-control" id="fillDate" min="{{ date('Y-m-d') }}" onInput="fillAllDate(this)"></td>
                                    <td><input type="time" class="form-control" id="fillStartTime" onInput="fillAllStartTime(this)" ></td>
                                    <td><input type="time" class="form-control" id="fillEndTime" onInput="fillAllEndTime(this)" ></td>
                                    <td></td>
                                </tr> 
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4" ></th>
                                    <th>Employee Name</th>
                                    <th>Effected From </th>
                                    <th>Current Start Time</th>
                                    <th>Current End Time</th>
                                    <th>New Effected From </th>
                                    <th>New Start Time </th>
                                    <th>New End Time </th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody id="responseEmployeeShift">
                            </tbody>
                        </table>
                        <div class="modal-footer justify-content-center d-none" id="responseEmployeeShiftSubmitDiv" >
                            <button type="submit" id="responseEmployeeShiftFormButton" class="btn btn-primary">
                                <span class="indicator-label">Update Shift Timings</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            
            </div>
        
        </div>
    
    </div>
</div>


@endsection



@push('js-link')

<script src="{{url('assets/js/shift-management/add-shift.js')}}"></script>

@endpush