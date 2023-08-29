@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div class="app-container col-12 d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Add New Employee</h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
            </div>
        </div>
    </div>
    
    <div class="app-content flex-column-fluid">
        <div class="app-container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Personal Details</span>
                        <span class="text-muted mt-1 fw-semibold fs-7"></span>
                    </h3>
                </div> 
                
                <div class="card-body py-3 ">
                    <form  id="add_employee_form" class="form" autocomplete="off">
                        @csrf
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Name</span>
                                </label>
                                
                                <input type="text" class="form-control " placeholder="Enter Employee Name" name="employee_name" required />
                            </div>
                            
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Contact No</span>
                                </label>
                                
                                <input type="text" class="form-control contactInput" name="employee_contact_no"
                                placeholder="Enter Contact No" required="required"  onkeyup="checkValidationExists(this,'contact no',1)"/>
                                <p class="status"></p>
                            </div>
                            
                        </div>
                    
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Personal Email<span>
                                </label>
                                
                                <input type="email" class="form-control emailInput" name="employee_personal_email"
                                placeholder="Enter Employee Email"  required="required" onkeyup="checkValidationExists(this,'email',1)"/>
                                <p class="status"></p>
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Official Email<span>
                                </label>
                                
                                <input type="email" class="form-control emailInput" name="employee_official_email"
                                placeholder="Enter Employee Official Email" required="required" onkeyup="checkValidationExists(this,'email',1)"/>
                                <p class="status"></p>
                            </div>
 
                        </div>

                        <div class="row g-9 mb-8">

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee NIC No</span>
                                </label>
                                
                                <input type="text" class="form-control cnicInput" name="employee_cnic" placeholder="Enter NIC No" required="required"  onkeyup="checkValidationExists(this,'cnic no',1)" />
                                <p class="status"></p>
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Gender<span>
                                </label>
                                
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Employee Gender" name="employee_gender"
                                required="required">
                                    <option value="">Select Employee Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>    
                                </select>
                            </div>
                            
                        </div>
                        <div class="row  g-9 mb-8">

                            <div class="col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Residential Address</span></label>
                                
                                <textarea class="form-control max_length_validator_address"  placeholder="Residential Address" maxlength="500" row="4" name="permanentaddress" ></textarea>
                            </div>

                        </div>

                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Company Details</span>
                            </h3>
                        </div>  
                    
                        <div class="row g-9 mb-8">

                            <div class="col-md-4 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Employee Company</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Employee Company" name="employee_company" required="required">
                                    <option value="">Select Employee Company</option>
                                    @if(!empty($all_companies))
                                        @foreach ($all_companies as $company)
                                            <option value="{{ $company['company_id'] }}">{{ $company['company_name'] }}</option>
                                        @endforeach
                                    @endif                                        
                                </select>
                            </div>
                            
                            <div class="col-md-4 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Employee Department</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Employee Department" name="employee_department"
                                required="required" id="department_select">
                                    <option value="">Select Employee Department</option>
                                    @if(!empty($all_departments))
                                        @foreach ($all_departments as $all_department)
                                            <option value="{{ $all_department['department_id'] }}"
                                                data-depart_id="{{ $all_department['department_id'] }}">
                                                {{ $all_department['department_name'] }}</option>
                                        @endforeach
                                    @endif                                        
                                </select>
                            </div>
                            
                            
                            <div class="col-md-4 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Employee Designation</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Employee Designation" name="employee_designation"
                                id="designss" required>
                                                                                           
                                </select>
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Grade</span>                                                        
                                </label>
                                
                                <input type="number" class="form-control " placeholder="Enter Employee Grade"
                                required name="employee_grade" min="1" max="25" />
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Group</span>                                                        
                                </label>
                                
                                <select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select Employee Group" name="employee_group" required="required">
                                    <option value="">Select Employee Group</option>
                                    @if(!empty($employee_group))
                                        @foreach ($employee_group as $employee_group)
                                            <option value="{{ $employee_group['employee_group_id'] }}"
                                                data-depart_id="{{ $employee_group['employee_group_id'] }}">
                                                {{ $employee_group['group_name'] }}</option>
                                        @endforeach
                                    @endif                                        
                                </select>
                            </div>
                            
                        </div>
                    
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Job Title</span>                                                        
                                </label>
                                
                                <input type="text" class="form-control " placeholder="Enter Job Title"
                                required name="employee_jobtitle"/>
                            </div>

                            <div class="col-md-6 fv-row" id="manager_right_div">
                                <label class="required fs-6 fw-semibold mb-2">Mark as Manager / Team Lead</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" name="make_ra" required="required" id="manager_rights">
                                    <option value="">Select Manager Rights</option>
                                    <option value="1">Yes, Employee's can report</option>
                                    <option value="0">No, Employee's can't report</option>
                                </select>
                            </div>

                            <div class="col-md-3 fv-row d-none" id="attendance_access_div">
                                <label class="required fs-6 fw-semibold mb-2">Attendance Rights</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" name="attendance_access" id="attendance_access" required>
                                    <option value="">Select Attendance Rights</option>
                                    <option value="1">Yes, Manager can edit attendance of team</option>
                                    <option value="0">No, Manager can't edit attendance of team</option>
                                </select>
                            </div>
                            
                            
                            {{-- <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Mark as Manager / Team Lead</span>                                                        
                                </label>
                                
                                <div class="d-flex align-items-center">
                                    <label class="form-check form-check-custom me-10">
                                        <input class="form-check-input h-20px w-20px" type="radio" id="make_manager" name="make_ra" required="" value="1" />
                                        <span class="form-check-label fw-semibold">Yes, Employee's can report to him</span>
                                    </label>
                                    
                                    <label class="form-check form-check-custom">
                                        <input class="form-check-input h-20px w-20px" type="radio" id="make_employee" name="make_ra" value="0"/>
                                        <span class="form-check-label fw-semibold">No, Employee's can't report to him</span>
                                    </label>
                                </div>
                              
                            </div> --}}
                            
                        </div>
                    
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Employee's Senior Manager</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Senior Manager" name="employee_reporting_person"
                                 required="required">
                                <option value="">Select Senior Manager </option>
                                @if(!empty($reporting_managers))
                                    @foreach ($reporting_managers as $reporting_manager)
                                        <option value="{{ $reporting_manager['employee_id'] }}">
                                            {{ $reporting_manager['employee_name'] }}
                                            ({{ $reporting_manager['department_name'] }} -
                                            {{ $reporting_manager['designation_name'] }})
                                        </option>
                                    @endforeach
                                @endif                                            
                                </select>
                            </div>
                            
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Employee's Junior Manager</label>
                                <select class="form-select" data-control="select2" data-hide-search="false"  name="employee_reporting_person2">
                                    <option value="">No Junior Manager </option>
                                    @if(!empty($reporting_managers))
                                        @foreach ($reporting_managers as $reporting_manager)
                                            <option value="{{ $reporting_manager['employee_id'] }}">
                                                {{ $reporting_manager['employee_name'] }}
                                                ({{ $reporting_manager['department_name'] }} -
                                                {{ $reporting_manager['designation_name'] }})
                                            </option>
                                        @endforeach
                                    @endif                                                   
                                </select>
                            </div>
                            
                        </div>

                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Joining Date</span></label>
                                
                                <input type="date" class="form-control " placeholder="mm/dd/yyyy" name="employee_doj" required="required" />
                            </div>
                            
                            
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Employment Status</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Employment Status" name="employee_status" required="required" id="employement-status">
                                    <option value="">Select Employment Status</option>
                                    @if(!empty($employment_status))
                                        @foreach ($employment_status as $employment_stats)
                                            <option value="{{$employment_stats['employement_status_id']}}">{{$employment_stats['employement_status_name']}}</option>
                                        @endforeach
                                    
                                    @endif  
                                </select>
                            </div>
                        </div>
                    
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Salary</span></label>
                                
                                <input type="number" class="form-control " name="employee_salary"
                                placeholder="Enter Employee Salary" required="required" min="0" max="99999999"/>
                            </div>
                            
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2 required">Employee Earning Type</label>
                                <select class="form-select " data-control="select2" data-hide-search="false"   name="earning_type" required>
                                    <option value="">Select Employee Earning Type</option>
                                    <option value="1">Monthly</option>
                                    <option value="2">Daily Wages</option>
                                </select>
                            </div>
                            
                        </div>
                    
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2 required">Employee Job Type</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select a Job Type" name="employee_jobtype" required="required">
                                    <option value="">Select Job Type</option>
                                    <option value="Office">Office Base</option>
                                    <option value="Home">Home Base</option>
                                </select>
                            </div>
                            
                            
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2 required">Employee Activity Status</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select a Activity Status" name="employee_isactive"
                                required="required" id="activity_status">
                                    <option value="">Select Activity Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">De-Active</option>
                                </select>
                            </div>                            
                            
                        </div>
                        <div class="row  g-9 mb-8" >
                           
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Password</span> <span class="text-danger">(Max 10 characters)</span></label>
                                <input type="text" class="form-control showpassword" name="employee_password"
                                placeholder="Enter Employee Password" required="required" value="{{$randomPassword}}"/>
                            </div>

                            <div class="col-md-6 fv-row ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Location</label>
                                <select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select location" name="employee_location" required >
                                    <option value="">Select Bank Name</option>
                                    @if(!empty($all_locations))
                                        @foreach ($all_locations as $location)
                                            <option value="{{ $location['location_id'] }}">{{ $location['location_title'] }}</option>
                                        @endforeach
                                    @endif                                                             
                                </select>
                            </div>
                           
                        </div>
                        <div class="row  g-9 mb-8" id="append_row_date">
                            <div class="col-md-6" >
                                <div class="form-group" >
                                    <label class="fs-6 fw-semibold mb-2">Employee Deactivation Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="employee_deactivate_date" id="deactivate_date">
                                </div>
                            </div>
                            <div class="col-md-6 fv-row"  >
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Deactivation Type
                                </label>
                                <div class="col-md-12 fv-row" id="main-div-history-status">
                                    <select class="form-select " data-control="select2" data-hide-search="false"  name="employee_deactive_type"
                                    id="deactivate_type">
                                        <option value="">Select Employee Deactivation Type</option>
                                        <option value="1">Resigned</option>
                                        <option value="2">Terminated</option>    
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Emergency Contact Information</span>
                            </h3>
                        </div> 

                        <div class="row g-9 mb-8">

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                 <span>Name</span>
                                </label>
                                <input type="text" class="form-control" maxlength="100" placeholder="Enter Name" name="emergency_name"/>
                            </div>
                            
                            
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Contact No</span>
                                </label>
                                <input type="text" class="form-control" maxlength="50"  name="emergency_contact_no"
                                placeholder="Enter Contact No" />
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    Relation
                                </label>
                                <select class="form-select " data-control="select2" data-hide-search="true"  name="employee_relation">
                                    <option value="">Select a Relation</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Brother">Brother</option>
                                    <option value="Sister">Sister</option>
                                    <option value="Guardian">Guardian</option>
                                                                                         
                                </select>
                            </div>

                        </div>
                        
                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Office Shift Timings</span>
                            </h3>
                        </div> 
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Shift Start Time</span></label>
                                
                                <input type="time" class="form-control " placeholder="--:-- --" name="shift_start_timings" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" required="required" id="shift_start_timings" oninput="calculateShiftDuration()"/>
                            </div>
                            
                            
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Shift End Time</span></label>
                                
                                <input type="time" class="form-control " placeholder="--:-- --" name="shift_end_timings" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" required="required" id="shift_end_timings" oninput="calculateShiftDuration()"/>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Total Shift Hours</span></label>
                                
                                <input type="text" class="form-control" id="duration"  readonly/>
                            </div>
                            
                        </div>
                    
                        <div class="row g-9 mb-8">
                            <div class="col-md-6 fv-row">
                                <div class="d-flex align-items-center">
                                    <label class="form-check form-check-custom me-10 form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" id="checkboxPrimary1"
                                        name="employee_shift_flexibilty" value="1" />
                                        <span class="form-check-label fw-semibold">Flexible Shift Timings</span>&nbsp; <i class="far fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="If employee has given facility of flexible shift timings, employee can check-in at any time and has to complete their shift total hours. For e.g 8 hours or 9 hours or it can be any."></i>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Leave Entitlement & Off Days</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                            <div class="card-toolbar">
                                
                            </div>
                        </div>
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Employee Off Day(s)</label>
                                <select class="form-select " data-control="select2" data-close-on-select="false" data-hide-search="false" data-placeholder="Select Off Day(s)" name="employee_offday[]" multiple>
                                    <option value="">Select Off Day(s)</option>
                                    <option value="Sunday">Sunday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Monday">Monday</option>
                                </select>
                            </div>
                            
                            
                        </div>
                    
                        <div class="row g-9 mb-8">
                            @if(!empty($manage_leaves))
                                @foreach ($manage_leaves as $manage_leave)
                                    <div class="col-md-3 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">{{$manage_leave['leave_name']}}</span></label>
                                        <input type="hidden" class="form-control " value="{{$manage_leave['leave_manage_id']}}"  name="leave_manage_id[]"  required="required" />
                                        <input type="number" class="form-control " min="0" max="50" name="leave_allotment[]" value="0" placeholder="Enter {{$manage_leave['leave_name']}}" required="required" />
                                    </div>
                                
                                @endforeach
                            @endif
                           
                        </div>
                    
                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Bank Account Detail (Optional)</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                        </div> 
                    
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Bank Name</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select a Bank Name" name="employee_bank_name">
                                    <option value="">Select Bank Name</option>
                                    @if(!empty($all_banks))
                                        @foreach ($all_banks as $all_bank)
                                            <option value="{{ $all_bank['bank_name'] }}">{{ $all_bank['bank_name'] }}</option>
                                        @endforeach
                                    @endif                                                             
                                </select>
                            </div>
                            
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span >Account Title</span></label>
                                
                                <input type="text" class="form-control " name="employee_account_title" placeholder="Enter Account Title"/>
                            </div>
                            
                        </div>
                    
                        <div class="row g-9 mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Account No</span></label>
                                
                                <input type="text" class="form-control " name="employee_account_number" placeholder="Enter Account No" />
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span >Account IBAN</span></label>
                                
                                <input type="text" class="form-control " name="employee_iban" placeholder="Enter Account IBAN"/>
                            </div>
                            
                        </div>

                        <div class="row g-9 mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Bank Code</span>
                                </label>
                                
                                <input type="text" class="form-control " name="employee_bank_code" placeholder="Enter Bank Code" />
                            </div>
                        </div>   

                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Module Rights</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                        </div>
                      
                        @if(!empty($get_modules))
                            <div class="row mb-8">
                                <div class="col-md-12">
                                    
                                    <table class="table table-row-bordered">
                                        <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th class="ps-4">Basic Modules</th>
                                                <th>Access Right</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                            @foreach ($get_modules as $key=>$module)
                                                @if($module['module_type'] == '0')
                                                   
                                                    <input type="hidden" name="module_url_{{$key}}" value="{{$module['module_url']}}">
                                                    @if($module['module_accessibility'] == 1)
                                                        <tr>
                                                            <td class="ps-4">{{$module['module_title']}}</td>
                                                            <td>
                                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                    <input class="form-check-input " type="checkbox"  checked disabled>
                                                                    <input type="hidden" name="permission_status_{{$key}}" value="1">
                                                                    <input type="hidden" name="module_url_{{$key}}" value="{{$module['module_url']}}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        
                                        </tbody>
                                    </table>

                                    <table class="table table-row-bordered mb-2">
                                        <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th class="ps-4">Other Modules</th>
                                                <th>Access Right</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          
                                            @foreach ($get_modules as $key=>$module)
                                                @if($module['module_type'] == '1')
                                                    @php $class = '' ;$disabled = '' ; @endphp
                                                    @if(in_array($module['module_url'],['/attendance','/attendance-requests','/employees-leaves','/wfh-requests','/all-salary-request','/resignations','/my-requisition','/all-loan-request','/shift-management','/employee-expense','/requisition']))
                                                        @php $class = 'checked'; @endphp
                                                    @endif

                                                    @if(in_array($module['module_url'],['/my-expense','/permissions','/my-shift-time']))
                                                        @php $disabled = 'disabled'; @endphp
                                                    @endif
                                                    <input class="form-check-input" type="hidden"  value="0" name="permission_status_{{$key}}" >
                                                    <input type="hidden" name="module_url_{{$key}}" value="{{$module['module_url']}}">
                                                    @if($module['module_accessibility'] == 1)
                                                   
                                                        <tr>
                                                            <td class="ps-4">{{$module['module_title']}}</td>
                                                            <td>
                                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                    <input class="form-check-input {{$class}}" type="checkbox"  value="1" name="permission_status_{{$key}}" @if(!empty($disabled)) checked disabled @endif>

                                                                     @if(!empty($disabled))
                                                                        <input type="hidden" name="permission_status_{{$key}}" value="1">
                                                                    @endif
                                                                    <input type="hidden" name="module_url_{{$key}}"  value="{{$module['module_url']}}">
                                                                </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        
                                            <input type="hidden" name="total_count" value="{{count($get_modules)}}">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif  
                        @if(!empty($email_config))
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" name="send_email" type="checkbox" id="send_email" />
                                <label class="form-check-label" for="send_email">
                                    Send email to employee of registration
                                </label>
                            </div>
                        @endif
                    
                        <div class="text-center">
                            <button type="submit"class="btn btn-lg btn-success" id="submit_btn">
                                <span class="indicator-label">Add Employee</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                               </span>
                            </button>
                        </div>
                    </form>
                    {{--end::Table container--}}
                </div>
                {{--begin::Body--}}
            </div>
            {{--end::Tables Widget 11--}}
        </div>
        {{--end::Content container--}}

    </div>
    {{--end::Content--}}
</div>

    <div class="modal fade"  id="check-candidate-name" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top mw-950px">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">On Board Employees</h4>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15">
                    <form id="update_onboarding_employee_status" class="form">
                        @csrf  
                        <div class="card card-flush pt-3 mb-5 mb-lg-10" data-kt-subscriptions-form="pricing">
                            <div class="card-body pt-0">
                                <div id="kt_create_new_payment_method">
                                    <p class="mb-4 text-center">These are the matching candidates in your on boarding. You can select to remove them from On Board process.</p>
                                    <div class="onboarding-employees"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                        
                            <button type="submit" id="submit_btnn1" class="btn btn-primary" onclick="proceed_btn()">
                                <span class="indicator-label">Cancel</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <button type="submit" id="submit_btnn2" class="btn btn-success">
                                <span class="indicator-label">Process & Remove From On Board</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="employee-registered-successfuly" data-backdrop="static" data-keyboard="false"  tabindex="-1" aria-hidden="true">
       
        <div class="modal-dialog modal-dialog-top mw-650px">
          
            <div class="modal-content rounded">
              
                {{-- <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4 text-success">Employee Register Successfully</h4>
                       
                    </div>
                </div> --}}
                
                <div class="modal-body pt-0 pb-5" >

                    <div class="text-center">
                        <img class="w-150px pt-15 pb-5" src="{{ url('/portal_assets/images/checked.png') }}" alt="" />
                        <h3 class="text-center text-success">Congratulations !</h3>
                        <p class="text-center">Employee profile has been successfully created in our HCM. Below mentioned are the details of recently craeted employee.</p>
                    </div>
             
                    <div id="employee-registered">

                    </div>
                    <div class="modal-footer justify-content-center">
                        
                        <button type="submit" id="copy_btn"  class="btn btn-success" onclick="Copy();">
                            <span class="indicator-label">Copy Details</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
              
            </div>
         
        </div>
       
    </div>

@endsection



@push('js-link')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>

<script src="{{url('assets/js/employees/add-employee.js')}}"></script>
    
@endpush