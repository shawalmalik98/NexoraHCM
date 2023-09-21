@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
    {{--begin::Toolbar--}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        {{--begin::Toolbar container--}}
        <div class="app-container col-12 d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Edit Employee</h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary">Go Back</a>
            </div>
        </div>
    </div>
    <div class="app-content flex-column-fluid">
        {{--begin::Content container--}}
        <div class="app-container">
            {{--begin::Tables Widget 11--}}
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Employee Details</span>
                        <span class="text-muted mt-1 fw-semibold fs-7"></span>
                    </h3>
                    <div class="card-toolbar">
                        
                    </div>
                </div> 

                <div class="card-body py-3">
                    <form  id="edit_employee_form" class="form" autocomplete="off">
                        @csrf
                        {{-- <div class="row g-9 mb-8">
                            <div class="col-md-12 ">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Employee Code</span>
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Employee Code" name="employee_code" required  value="{{ $employee_detail[0]['employee_code'] }}">
                            </div>
                        </div> --}}
                        {{-- <input type="hidden" class="form-control" placeholder="Enter Employee Code" name="employee_code" required  value="{{ $employee_detail[0]['employee_code'] }}"> --}}
                        <div class="row g-9 mb-8">

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee ID</span>
                                </label>
                                
                                <input type="text" class="form-control" placeholder="Enter Employee ID" name="employee_code" required value="{{ $employee_detail[0]['employee_code'] }}" readonly style="background-color:lightgray"/>
                            </div>
                            
                            <div class="col-md-6 ">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2"> Employee Name</label>
                                <input type="text" class="form-control " placeholder="Enter Employee Name" name="employee_name" required  value="{{ $employee_detail[0]['employee_name'] }}">
                                <input type="hidden" class="form-control" name="employee_id" placeholder="Enter Employee Name" required="required" value="{{ $employee_detail[0]['employee_id'] }}">
                            </div>
                            
                        </div>

                        <div class="row g-9 mb-8">

                            <div class="col-md-4">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Contact No </label>
                                <input type="text" class="form-control contactInput " name="employee_contact_no" placeholder="Enter Contact No" required="required" value="{{$employee_detail[0]['employee_contactno'] }}" onkeyup="checkValidationExists(this,'contact no',2,'{{$employee_id}}')"/>
                                <span class="status"></span>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2"> Employee Personal Email</label>
                                <input type="email" class="form-control emailInput" name="employee_personal_email"
                                placeholder="Enter Employee Email" required="required"  value="{{$employee_detail[0]['employee_personal_email'] }}" onkeyup="checkValidationExists(this,'email',2)"/>
                                <span class="status"></span>
                            </div>
                            
                            <div class="col-md-4 fv-row">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Official Email</label>
                                <input type="email" class="form-control emailInput" name="employee_official_email"
                                placeholder="Enter Employee Official Email" required="required" value="{{$employee_detail[0]['employee_work_email'] }}" onkeyup="checkValidationExists(this,'email',2)"/>
                                <span class="status"></span>
                            </div>
 
                        </div>

                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-4">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee NIC No </label>
                                <input type="text" class="form-control cnicInput" name="employee_cnic" placeholder="Enter NIC No" required="required" value="{{$employee_detail[0]['employee_cnic'] }}"  onkeyup="checkValidationExists(this,'cnic no')"/>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="">NIC Issue Date</span>
                                </label>
                                
                                <input type="date" class="form-control" name="employee_cnic_issue_date" value="{{ $employee_detail[0]['cnic_issue_date'] }}"/>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="">NIC Expiry Date</span>
                                </label>
                                
                                <input type="date" class="form-control" name="employee_cnic_expiry_date" value="{{ $employee_detail[0]['cnic_expiry_date'] }}"/>
                            </div>
                            
                        </div>

                        <div class="row g-9 mb-8">
                            <div class="col-md-4 ">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Gender</label>
                                <select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select Employee Gender" name="employee_gender" required="required">
                                    <option value="">Select Employee Gender</option>
                                    <option value="Male" @if (!empty($employee_detail[0]['employee_gender']) && $employee_detail[0]['employee_gender'] == 'Male') selected @endif>Male</option>
                                    <option value="Female" @if (!empty($employee_detail[0]['employee_gender']) && $employee_detail[0]['employee_gender'] == 'Female') selected @endif>Female</option>    
                                </select>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="">Nationality<span>
                                </label>
                                
                                <input type="text" class="form-control" name="employee_nationality" placeholder="Enter Nationality" value="{{ $employee_bank[0]['citizenship'] }}"/>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="">Marital Status<span>
                                </label>
                                
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Employee Gender" name="employee_marital_status">
                                    <option value="">Select Marital Status</option>
                                    <option value="Single" @if($employee_bank[0]['maritalstatus'] == 'Single') selected @endif>Single</option>
                                    <option value="Married" @if($employee_bank[0]['maritalstatus'] == 'Married') selected @endif>Married</option>
                                    <option value="Widowed" @if($employee_bank[0]['maritalstatus'] == 'Widowed') selected @endif>Widowed</option>
                                    <option value="Separated" @if($employee_bank[0]['maritalstatus'] == 'Separated') selected @endif>Separated</option>
                                    <option value="Divorced" @if($employee_bank[0]['maritalstatus'] == 'Divorced') selected @endif>Divorced</option>    
                                </select>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="">Religion<span>
                                </label>
                                
                                <select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select Religion" name="employee_religion">
                                    <option value="">Select Religion</option>
                                    <option value="Atheism/Agnosticism">Atheism/Agnosticism</option>
                                        <option value="Bahai" @if($employee_bank[0]['religion'] == 'Bahai') selected @endif >Bahai</option>
                                        <option value="Buddhism" @if($employee_bank[0]['religion'] == 'Buddhism') selected @endif >Buddhism</option>
                                        <option value="Christianity" @if($employee_bank[0]['religion'] == 'Christianity') selected @endif>Christianity</option>
                                        <option value="Confucianism" @if($employee_bank[0]['religion'] == 'Confucianism') selected @endif>Confucianism</option>
                                        <option value="Druze" @if($employee_bank[0]['religion'] == 'Druze') selected @endif >Druze</option>
                                        <option value="Gnosticism" @if($employee_bank[0]['religion'] == 'Gnosticism') selected @endif>Gnosticism</option>
                                        <option value="Hinduism" @if($employee_bank[0]['religion'] == 'Hinduism') selected @endif>Hinduism</option>
                                        <option value="Islam" @if($employee_bank[0]['religion'] == 'Islam') selected @endif >Islam</option>
                                        <option value="Jainism" @if($employee_bank[0]['religion'] == 'Jainism') selected @endif>Jainism</option>
                                        <option value="Rastafarianism" @if($employee_bank[0]['religion'] == 'Rastafarianism') selected @endif>Rastafarianism</option>
                                        <option value="Shinto" @if($employee_bank[0]['religion'] == 'Shinto') selected @endif >Shinto</option>
                                        <option value="Sikhism" @if($employee_bank[0]['religion'] == 'Sikhism') selected @endif>Sikhism</option>
                                        <option value="Zoroastrianism" @if($employee_bank[0]['religion'] == 'Zoroastrianism') selected @endif>Zoroastrianism</option>
                                        <option value="Traditional African Religions" @if($employee_bank[0]['religion'] == 'Traditional African Religions') selected @endif>Traditional AfricanReligions</option>
                                        <option value="African Diaspora Religions" @if($employee_bank[0]['religion'] == 'African Diaspora Religions') selected @endif>African DiasporaReligions</option>
                                        <option value="Indigenous American Religions" @if($employee_bank[0]['religion'] == 'Indigenous American Religions') selected @endif>Indigenous AmericanReligions</option>
                                </select>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="">Place of Birth<span>
                                </label>
                                
                                <input type="text" class="form-control" name="employee_place_of_birth" placeholder="Enter Place of Birth" value="{{ $employee_bank[0]['pob'] }}"/>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Date of Birth<span>
                                </label>
                                
                                <input type="date" class="form-control" name="employee_dob" required="required" value="{{ $employee_bank[0]['dob'] }}"/>
                            </div>
                        </div>

                        {{-- <div class="row g-9 mb-8">

                            <div class="col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Residential Address</label> 
                                <textarea class="form-control max_length_validator_address"  placeholder="Residential Address" maxlength="500" row="4" name="permanentaddress" value="{{ $employee_bank[0]['permanentaddress'] }}">{{ $employee_bank[0]['permanentaddress'] }}</textarea>

                            </div>

                        </div> --}}

                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Company Details</span>
                            </h3>
                        </div> 
                        
                        <div class="row g-9 mb-8">
                            <div class="col-md-4 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Company</label>
                                <select class="form-select" data-control="select2" data-hide-search="false" name="employee_company" required="required">
                                    @if (!empty($all_companies))
                                        @foreach ($all_companies as $company)
                                            <option value="{{ $company['company_id'] }}" @if($employee_detail[0]['employee_company'] == $company['company_id']) selected @endif>{{ $company['company_name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-md-4 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Department</label>
                                <select class="form-select" data-control="select2" data-hide-search="false" name="employee_department" required="required" id="department_select">
                                    @if (!empty($all_departments))
                                        @foreach ($all_departments as $all_department)
                                            <option value="{{ $all_department['department_id'] }}" data-depart_id="{{ $all_department['department_id'] }}" @if ($employee_detail[0]['employee_department'] == $all_department['department_id']) selected @endif>{{ $all_department['department_name'] }}</option>
                                        @endforeach
                                    @endif                               
                                </select>
                            </div>
                            
                            
                            <div class="col-md-4 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Designation</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Designation" name="employee_designation"
                                id="designss" pla required>
                                @if (!empty($employee_designation_detail))
                                    @foreach ($employee_designation_detail as $designation)
                                    <option value="{{$designation['designation_id'] }}"
                                        @if ($employee_detail[0]['employee_designation'] == $designation['designation_id'] ) selected @endif>
                                        {{ $designation['designation_name'] }}
                                    </option>
                                    @endforeach
                                @endif                                                      
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-9 mb-8">

                            <div class="col-md-4 fv-row">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Grade </label>
                                
                                <input type="number" class="form-control " placeholder="Enter Employee Grade"
                                required name="employee_grade" min="1" max="25" value="{{ $employee_detail[0]['employee_grade'] }}" />
                            </div>

                            <div class="col-md-4">
                                <label class="required fs-6 fw-semibold mb-2">Employee Group</label>
                                <select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select Employee Group" name="employee_group" required="required">
                                    <option value="">Select Employee Group</option>
                                    @if(!empty($employee_groups))
                                        @foreach ($employee_groups as $employee_group)
                                            <option value="{{ $employee_group['employee_group_id'] }}"
                                                @if ($employee_detail[0]['employee_group'] == $employee_group['employee_group_id']) selected @endif>
                                                {{ $employee_group['group_name'] }}</option>
                                        @endforeach
                                    @endif                                        
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Job Title</label>
                                
                                <input type="text" class="form-control" placeholder="Enter Job Title"
                                required name="employee_jobtitle"  value="{{ $employee_detail[0]['employee_jobtitle'] }}"/>
                            </div>
                            
                        </div>

                        <div class="row g-9 mb-8">
                            
                            <div class="{{ $employee_detail[0]['employee_is_manager'] == 1 ? "col-md-3" : "col-md-6" }} fv-row" id="manager_right_div">
                                <label class="required fs-6 fw-semibold mb-2">Mark as Manager / Team Lead</label>
                                <select class="form-select" data-control="select2" data-hide-search="false" name="make_ra" required="required" id="manager_rights">
                                    <option value="">Select Manager Rights</option>
                                    <option value="1" @if($employee_detail[0]['employee_is_manager'] == 1) selected @endif>Yes, Employee's can report</option>
                                    <option value="0" @if($employee_detail[0]['employee_is_manager'] == 0) selected @endif>No, Employee's can't report</option>
                                </select>
                            </div>

                            <div class="{{ $employee_detail[0]['employee_is_manager'] == 1 ? "col-md-3" : "col-md-3" }} fv-row {{ $employee_detail[0]['employee_is_manager'] == 1 ? "" : "d-none" }}" id="attendance_access_div">
                                <label class="required fs-6 fw-semibold mb-2">Attendance Rights</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" name="attendance_access" id="attendance_access">
                                    <option value="">Select Attendance Rights</option>
                                    <option value="1" @if(!empty($get_attendance_access) && $get_attendance_access[0]['attendance_access'] == 1) selected @endif>Yes, Manager can edit attendance of team</option>
                                    <option value="0" @if(!empty($get_attendance_access) && $get_attendance_access[0]['attendance_access'] == 0) selected @endif>No, Manager can't edit attendance of team</option>
                                </select>
                            </div>
                            
                            
                            {{-- <div class="col-md-6 ">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Mark As Manager / Team Lead  </label>
                                <div class="d-flex align-items-center">
                                    <label class="form-check form-check-custom me-10 form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="radio" id="customRadio5" name="make_ra" required="" value="1"  @if ($employee_detail[0]['employee_is_manager'] == 1) checked @endif />
                                        <span class="form-check-label fw-semibold">Yes, Employee's can report to him</span>
                                    </label>
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="radio" id="customRadio6" name="make_ra" value="0"  @if ($employee_detail[0]['employee_is_manager'] == 0) checked @endif />
                                        <span class="form-check-label fw-semibold">No, Employee's can't report to him</span>
                                    </label>
                                </div>
                              
                            </div> --}}
                            
                        </div>


                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee's Senior Manager</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" name="employee_reporting_person"
                                 required="required">
                                <option value="">Select Senior Manager </option>
                                @if (!empty($reporting_managers))
                                    @foreach ($reporting_managers as $reporting_manager)
                                        @if(!in_array($reporting_manager['employee_id'],$total_team_member))
                                            <option value="{{ $reporting_manager['employee_id'] }}"
                                                @if ($employee_detail[0]['employee_senior_manager'] == $reporting_manager['employee_id']) selected @endif>
                                                {{ $reporting_manager['employee_name'] }}
                                                ({{ $reporting_manager['department_name'] }} -
                                                {{ $reporting_manager['designation_name'] }})
                                            </option>
                                        @endif
                                    @endforeach
                                @endif                                     
                                </select>
                            </div>
                            
                            
                            <div class="col-md-6 ">
                                <label class="fs-6 fw-semibold mb-2">Employee's Junior Manager</label>
                                <select class="form-select " data-control="select2" data-hide-search="false"  name="employee_reporting_person2">
                                    <option value="">Select Junior Manager </option>
                                    @if (!empty($reporting_managers))
                                        @foreach ($reporting_managers as $reporting_manager)
                                            @if(!in_array($reporting_manager['employee_id'],$total_team_member))
                                                <option value="{{ $reporting_manager['employee_id'] }}"
                                                    @if($employee_detail[0]['employee_junior_manager'] == $reporting_manager['employee_id']) selected @endif>
                                                    {{ $reporting_manager['employee_name'] }}
                                                    ({{ $reporting_manager['department_name'] }} -
                                                    {{ $reporting_manager['designation_name'] }})
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif                                                 
                                </select>
                            </div>
                            
                        </div>
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 ">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Joining Date                                                  </label>
                                <input type="date" class="form-control " placeholder="mm/dd/yyyy" name="employee_doj"
                                required="required" id="employee_doj"  value="{{ $employee_detail[0]['employee_doj'] }}" @if($attendance_count > 0) readonly @endif/>
                                @if($attendance_count > 0)
                                    <span class="text-danger"> you can't update the date of joining b/c attendance record exits</span>
                                @endif
                            </div>
                            
                            @if($employment_period_count > 1)
                                @php $classes = 'col-md-5'; @endphp
                                @else
                                @php $classes = 'col-md-6'; @endphp
                            @endif
                            <div class={{$classes}}>
                                <label class="required fs-6 fw-semibold mb-2">Employment Status</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" name="employee_status"
                                required="required" id="employement-status">
                                    @if(!empty($employment_status))
                                        @foreach ($employment_status as $employment_stats)
                                            <option value="{{$employment_stats['employement_status_id']}}" @if ($employee_detail[0]['employee_status'] == $employment_stats['employement_status_id']) selected @endif>{{$employment_stats['employement_status_name']}}</option>
                                        @endforeach
                                
                                    @endif  
                                   
                                </select>
                            </div>
                            @if($employment_period_count > 1)
                                <div class="col-lg-1 col-md-1 col-3 " style="margin-top: 60px;">
                                    <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#view-employee-status-history">	
                                
                                        <i class="fas fa-eye"  data-bs-toggle="tooltip" title="View Employee Status History"></i>
                                                                    
                                    </a>
                                
                                </div>
                            @endif
                            <div class="col-md-6 d-none" id="append_row_date_emp_status">
                                <div class="form-group">
                                    <label class="fs-6 fw-semibold mb-2">Employment Status Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control " name="employment_status_date" value="{{!empty($employment_period[0]['effective_date']) ? $employment_period[0]['effective_date'] :'' }}" id="employment_status_date" required="required" min={{date(!empty($secondLastEffectiveDate) ? $secondLastEffectiveDate[0]['effective_date']: $employment_period[0]['effective_date'])}}>
                                </div>
                            </div>
                            
                        </div>

                        

                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Job Type</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" name="employee_jobtype"
                                required="required">
                                    <option value="">Select Job Type</option>
                                    <option value="Office"
                                         @if ($employee_detail[0]['employee_jobtype'] == 'Office') selected @endif>Office base
                                    </option>
                                    <option value="Home"
                                        @if ($employee_detail[0]['employee_jobtype'] == 'Home') selected @endif>Home base
                                    </option>
                                </select>
                            </div>
                            
                            
                            <div class="col-md-6 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Activity Status</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" name="employee_isactive"
                                required="required" id="activity_status">
                                <option value="">Select Activity Status</option>
                                <option value="1" @if ($employee_detail[0]['employee_isactive'] == 1) selected @endif>Active</option>
                                <option value="0" @if ($employee_detail[0]['employee_isactive'] == 0) selected @endif>De-Active</option>                                                                                                                                         
                                </select>
                            </div>

                        </div>

                        <div class="row g-9 mb-8">

                            <div class="col-md-6 ">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Password</span> <span class="text-danger">(Max 10 characters)</span></label>
                                <input type="text" class="form-control showpassword"  name="employee_password" placeholder="Enter Employee Password" required="required" value="{{ Crypt::decryptString($employee_detail[0]['employee_password']) }}" />
                            </div>
                            
                            <div class="col-md-6 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Location</label>
                                <select class="form-select" data-control="select2" data-hide-search="true" name="employee_location[]" multiple>
                                    <option value="">Select Employee Location</option>
                                    @if (!empty($all_locations))
                                        @foreach ($all_locations as $location)
                                            <option value="{{ $location['location_id'] }}" @if(in_array($location['location_id'] , array_column($employee_locations , 'emp_loc_loc_id'))) selected @endif>{{ $location['location_title'] }}</option>
                                        @endforeach
                                    @endif                                                          
                                </select>
                            </div>
                            
                        </div>
                        <div class="row  g-9 mb-8" id="append_row_date">
                            <div class="col-md-6" >
                                <div class="form-group">
                                    <label class="fs-6 fw-semibold mb-2">Employee Deactivation Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control " name="employee_deactivate_date" id="deactivate_date" required="required" value="{{ $employee_detail[0]['employee_deactivate_date'] }}" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Deactivation Type<span>
                                </label>
                                
                                <select class="form-select " data-control="select2" data-hide-search="false"  name="employee_deactive_type"
                                id="deactivate_type">
                                    <option value="">Select Employee Deactivation Type</option>
                                    <option value="1" @if($employee_detail[0]['employee_deactivation_type'] == 1) selected @endif>Resigned</option>
                                    <option value="2" @if($employee_detail[0]['employee_deactivation_type'] == 2) selected @endif>Terminated</option>    
                                </select>
                            </div>
                        </div>
                        {{-- <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Emergency Contact Information (Optional)</span>
                            </h3>
                        </div>

                        <div class="row g-9 mb-8">

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Name</span>
                                </label>
                                
                                <input type="text" class="form-control " maxlength="100" placeholder="Enter Name" name="emergency_name" value="{{ $employee_bank[0]['emergency_name'] }}"/>
                            </div>
                            
                            
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Contact No</span>
                                </label>
                                
                                <input type="text" class="form-control " maxlength="50" name="emergency_contact_no"
                                placeholder="Enter Contact No" value="{{ $employee_bank[0]['emergency_contact_no'] }}" />
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    Relation
                                </label>
                                <select class="form-select " data-control="select2" data-hide-search="true"  name="employee_relation">
                                    @foreach($relations as $relation)
                                        <option value="{{$relation}}" @if($relation == $employee_bank[0]['employee_relation'] )  selected @endif>{{$relation}}</option>
                                    @endforeach                                                       
                                </select>
                            </div>


                        </div> --}}

                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Salary Details</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                            <div class="card-toolbar">
                                
                            </div>
                        </div> 

                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-5">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Salary </label>
                                <input type="number" class="form-control gross_salary" name="employee_salary"
                                placeholder="Enter Employee Salary" required="required"  value="{{ $employee_salary[0]['salary_amount'] ?? ''}}" readonly/>
                            </div>
                            <div class="col-md-5 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Basic Salary</span></label>
                                <input type="number" class="form-control " name="employee_basic_salary"
                                placeholder="Enter Employee Salary" required="required" min="0" max="99999999" readonly value="0" id="basic_salary"/>
                            </div>
                            <div class="col-md-2" style="margin-top: 60px;">
                                <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#view-employee-salary-detail">	
                               
                                    <i class="fas fa-eye"  data-bs-toggle="tooltip" title="View Employee Salary"></i>
                                                                
                                </a>
                                <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#edit-employee-salary">
                                    <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Edit Employee Salary">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                            {{-- <div class="col-lg-1 col-md-1 col-2 " style="margin-top: 60px;">
                                
                            </div> --}}
                            
                            {{-- <div class="col-md-6 ">
                                <label class="fs-6 fw-semibold mb-2 required">Employee Earning Type</label>
                                <select class="form-select " data-control="select2" data-hide-search="false"   name="earning_type">
                                    <option value="">Select Employee Earning Type</option>
                                    <option value="1" @if ($employee_detail[0]['employee_earning_type'] == 1) selected @endif>Monthly</option>
                                    <option value="2" @if ($employee_detail[0]['employee_earning_type'] == 2) selected @endif>Daily Wages</option>                                                                                                                                         
                                   
                                </select>
                            </div> --}}

                        </div>
                        
                        <div class="card-header border-0 px-0">
                            {{-- --}}
                                <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Office Shift Timing</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                            {{-- --}}
                            <div class="card-toolbar">
                                
                            </div>
                        </div> 
                      
                        
                        <div class="row g-9 mb-8">
                            <div class="col-md-4">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Shift Start Timing </label>
                                <input type="time" class="form-control " placeholder="--:-- --" name="shift_start_timings" id="shift_start_timings" oninput="calculateShiftDuration()"
                                pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$"
                                required="required" value="@if(!empty($shift_start_timings)){{substr($shift_start_timings,0,-3)}}@endif"/>
                            </div>
                            <div class="col-md-4 ">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Shift End Timing </label>
                                <input type="time" class="form-control " placeholder="--:-- --" name="shift_end_timings" id="shift_end_timings" oninput="calculateShiftDuration()"
                                pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$"
                               required="required" value="@if(!empty($shift_end_timings)){{substr($shift_end_timings,0,-3)}}@endif"/>
                            </div>
                            <div class="col-md-3 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Total Shift Hours</label>

                                <input type="text" class="form-control" id="duration" value="{{CustomHelper::calculate_shift_timings_shift_management($shift_start_timings,$shift_end_timings)}}" readonly />
                            </div>
                            <div class="col-md-1" style="margin-top: 60px;">
                                <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#view-employee-shift-detail">	
                                    <i class="fas fa-eye"  data-bs-toggle="tooltip" title="View Employee Shift Detail"></i>               
                                </a>
                            </div>
                        </div>
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 ">
                                <div class="d-flex align-items-center">
                                    <label class="form-check form-check-custom me-10 form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="employee_geofencing_enability" value="1" @if ($employee_detail[0]['employee_geofencing_enability'] == true) checked="" @endif/>
                                        <span class="form-check-label fw-semibold">Geo Fencing Enability</span>&nbsp; <i class="far fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="If this option is checked it means employee has to bee in radius of mapped location, otherwise he can mark attendance from anywhere."></i>
                                    </label>
                                    <label class="form-check form-check-custom me-10 form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="employee_shift_flexibilty" value="1" @if ($employee_detail[0]['employee_shift_flexibilty'] == '1') checked="" @endif />
                                        <span class="form-check-label fw-semibold">Flexible Shift Timings</span>&nbsp; <i class="far fa-question-circle" data-toggle="tooltip" data-placement="right" data-title="If employee has given facility of flexible shift timings, employee can check-in at any time and has to complete their shift total hours. For e.g 8 hours or 9 hours or it can be any."></i>
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
                            
                            <div class="col-md-6 ">
                                <label class="fs-6 fw-semibold mb-2 required">Employee Off Day(s)</label>
                                @php $employee_detail[0]['employee_offdays'] = (!empty($employee_detail[0]['employee_offdays'])) ? explode(",",$employee_detail[0]['employee_offdays']):[] @endphp
                                <select class="form-select " data-control="select2" data-close-on-select="false" data-hide-search="false" data-placeholder="Select Off Day(s)" name="employee_offday[]" multiple>
                                    <option value="">Select Off Day(s)</option>
                                    <option value="Sunday" @if(in_array("Sunday",$employee_detail[0]['employee_offdays'] )) selected @endif >Sunday</option>
                                    <option value="Saturday" @if(in_array("Saturday",$employee_detail[0]['employee_offdays'] )) selected @endif >Saturday</option>
                                    <option value="Friday" @if(in_array("Friday",$employee_detail[0]['employee_offdays'] )) selected @endif >Friday</option>
                                    <option value="Thursday" @if(in_array("Thursday",$employee_detail[0]['employee_offdays'] )) selected @endif >Thursday</option>
                                    <option value="Wednesday" @if(in_array("Wednesday",$employee_detail[0]['employee_offdays'] )) selected @endif >Wednesday</option>
                                    <option value="Tuesday" @if(in_array("Tuesday",$employee_detail[0]['employee_offdays'] )) selected @endif >Tuesday</option>
                                    <option value="Monday" @if(in_array("Monday",$employee_detail[0]['employee_offdays'] )) selected @endif >Monday</option>
                                </select>
                                
                            </div>
                            
                        </div>

                        <div class="row g-9 mb-8">
                            @if(!empty($leaves_entitled))
                                @foreach ($leaves_entitled as $manage_leave)
                                    <div class="col-md-3 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">{{$manage_leave['leave_name']}}</span></label>
                                        <input type="hidden" value="{{$manage_leave['leave_ent_created_at']}}" name="leave_ent_created_at[]" />
                                        <input type="hidden" class="form-control " value="{{$manage_leave['leave_manage_id']}}"  name="leave_manage_id[]"  required="required" />
                                        <input type="number" class="form-control " min="0" max="50" name="leave_allotment[]" value="{{$manage_leave['leave_allotement']}}" placeholder="Enter {{$manage_leave['leave_name']}}" required="required" />
                                    </div>
                                
                                @endforeach
                            @endif
                            
                        </div>

                        <div class="card-header border-0 px-0">
                                <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Bank Account Detail (Optional)</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                            <div class="card-toolbar">
                                
                            </div>
                        </div> 

                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 ">
                                <label class="fs-6 fw-semibold mb-2">Bank Name</label>
                                    <input type="hidden" class="form-control" name="information_id" value="{{$employee_bank[0]['information_id']}}"/>
                                <select class="form-select " data-control="select2" data-hide-search="true" name="bank_name">
                                    <option value="">Select Bank Name</option>
                                    @if (!empty($all_banks))
                                        @foreach ($all_banks as $all_bank)
                                            <option value="{{ $all_bank['bank_name'] }}"
                                                @if ($employee_bank[0]['bankname'] == $all_bank['bank_name']) selected @endif>
                                                {{ $all_bank['bank_name'] }}</option>
                                        @endforeach
                                    @endif                                                          
                                </select>
                            </div>
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span >Account Title</span>
                                </label>
                                
                                <input type="text" class="form-control " name="account_title" placeholder="Enter Account Title" value="{{ $employee_bank[0]['accounttitle'] }}"/>
                            </div>
                            
                        </div>


                        <div class="row g-9 mb-8">
                        
                            
                            <div class="col-md-6 ">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span >Account No </span>                                                        
                                </label>
                                
                                <input type="text" class="form-control " name="account_number" placeholder="Enter Account No" value="{{ $employee_bank[0]['accountno'] }}" />
                            </div>

                            <div class="col-md-6 ">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span >Account IBAN </span>                                                       
                                </label>
                                
                                <input type="text" class="form-control " name="account_iban" placeholder="Enter Account IBAN" value="{{ $employee_bank[0]['ibannumber'] }}"/>
                            </div>
                            
                        </div>   

                        <div class="row g-9 mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Bank Code</span>
                                </label>
                                
                                <input type="text" class="form-control " name="bank_code" placeholder="Enter Bank Code" value="{{ $employee_bank[0]['bankcode'] }}"/>
                            </div>
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
                                            
                                            @foreach($get_modules as $key =>$module)
                                                @if($module['module_type'] == '0')
                                                    <input type="hidden" name="module_url_{{$key}}" value="{{$module['module_url']}}">
                                                    @if($module['module_accessibility'] == 1)
                                                    
                                                        <tr>
                                                            <td class="ps-4">{{$module['module_title']}}</td>
                                                            <td>
                                                                <div class="form-check form-check-sm form-check-custom">
                                                                    <input class="form-check-input" type="checkbox"  checked disabled>
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
                                            
                                            @foreach($get_modules as $key =>$module)
                                                @if($module['module_type'] == '1')
                                                    @php $checked = '';$disabled = ''; $class = ''; @endphp
                                                    @if(in_array($module['module_url'],$get_permissions))
                                                        @php $checked = 'checked'; @endphp
                                                    @endif
                                                    
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
                                                                    <input class="form-check-input {{$class}}"  type="checkbox"  value="1" {{$checked}}  name="permission_status_{{$key}}"  @if(!empty($disabled)) checked disabled @endif>
                                                                    @if(!empty($disabled))
                                                                        <input type="hidden" name="permission_status_{{$key}}" value="1">
                                                                    @endif
                                                                    <input type="hidden" name="module_url_{{$key}}"  value="{{$module['module_url']}}">
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
                        <div class="text-center">
                            <button type="submit"class="btn btn-lg btn-success" id="submit_btn">
                                <span class="indicator-label">Update Employee</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
   
    {{-- View Salary Modal Start --}}
    <div class="modal fade" id="view-employee-salary-detail" tabindex="-1" aria-hidden="true">
       
        <div class="modal-dialog modal-dialog-top mw-650px">
          
            <div class="modal-content rounded">
                
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Employee Salary Details</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
              
                <div class="modal-body" style="overflow-y:scroll;overflow-x:hidden;max-height: 500px;">
                    <table class="table table-row-bordered me-5">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-7">S.No</th>
                                <th>Employee Name</th>
                                <th >Salary</th>
                                <th >Salary Type</th>
                                <th >Effective From</th>
                                <th >Modified Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($employee_salary_detail))
                                @foreach ($employee_salary_detail as $key => $salary)
                                    <tr>
                                        <td class="ps-8">{{ $key + 1 }}</td>
                                        <td>{{$salary['employee_name']}}</td>
                                        <td>{{number_format($salary['salary_amount'])}}</td>
                                        <td>{{($salary['salary_earning_type'] == 1) ? 'Monthly' : 'Daily Wages'}}</td>
                                        <td>{{date('d-M-Y',strtotime($salary['salary_effected_date'])) ?? 'N\A'}}</td>
                                        <td>{{date('d-M-Y',strtotime($salary['salary_created_date'])) ?? 'N\A'}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
              
            </div>
         
        </div>
       
    </div>
    {{----View Salaray Modal End--}}

    <div class="modal fade" id="view-employee-status-history" tabindex="-1" aria-hidden="true">
    
        <div class="modal-dialog modal-dialog-top mw-650px">
        
            <div class="modal-content rounded">
                
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Employment Status History</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            
                <div class="modal-body" style="overflow-y:scroll;overflow-x:hidden;max-height: 500px;">
                    <table class="table table-row-bordered me-5">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-7">Employee Name</th>
                                <th >Status</th>    
                                <th >Effected Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($employement_status_history))
                                @foreach ($employement_status_history as $employement_status_history)
                                    <tr>
                                        <td class="ps-8">{{$employement_status_history['employee_name']}}</td>
                                        <td>{{$employement_status_history['employement_status_name']}}</td>
                                        <td>{{date('d-M-Y',strtotime($employement_status_history['effective_date'])) ?? 'N\A'}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            
            </div>
        
        </div>
    
    </div>
    {{-------- Employement Status history Modal---------}}
    {{-- View Shift Modal Start --}}
    <div class="modal fade" id="view-employee-shift-detail" tabindex="-1" aria-hidden="true">
       
        <div class="modal-dialog modal-dialog-top mw-650px">
          
            <div class="modal-content rounded">
                
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Employee Shift Detail</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
              
                <div class="modal-body" style="overflow-y:scroll;overflow-x:hidden;max-height: 500px;">
                    <table class="table table-row-bordered me-5">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-7">Effected Date</th>
                                <th >Shift Start Time</th>
                                <th >Shift End Time</th>
                                <th >Shift Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($fetch_shift_timing))
                                @foreach ($fetch_shift_timing as $shift_timing)
                                    <tr>
                                        <td class="ps-8">{{date('d-M-Y',strtotime($shift_timing['shift_start_from_date']))}}</td>
                                        <td>{{date('h:i A',strtotime($shift_timing['shift_start_time']))}}</td>
                                        <td>{{date('h:i A',strtotime($shift_timing['shift_end_time']))}}</td>
                                        <td>{{CustomHelper::calculate_shift_timings_shift_management($shift_timing['shift_start_time'],$shift_timing['shift_end_time'])}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
              
            </div>
         
        </div>
       
    </div>
    {{----View Shift Modal End--}}

    
        {{--begin::Modal -Edit Salary--}}
        <div class="modal fade" id="edit-employee-salary" tabindex="-1" aria-hidden="true">
            
            <div class="modal-dialog modal-dialog-top mw-650px">
                
                <div class="modal-content rounded">
                    
                    <div class="modal-content rounded">
                        
                        <div class="modal-header">
                          <h4 class="modal-title pl-4">Edit Employee Salary</h4>
                          <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                              <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                          </button>
                        </div>
                        
                    </div>
                    
                    
                    <div class="modal-body scroll-y pt-0 pb-15">
                        
                        <form id="edit_employee_salary" class="form">
                            @csrf
                            <input type="hidden" class="form-control" name="employee_id" required="required" value="{{ $employee_detail[0]['employee_id'] }}">
                            
                            <div class="col-md-12 mb-8 mt-3 ">
                                <label class="required fs-6 fw-semibold mb-2">Effective From</label>
                                <div class="position-relative d-flex align-items-center">
                                    <input type="date" class="form-control" placeholder="Select a date" name="effected_date"  required/>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-column mb-8">
                                <label class="fs-6 fw-semibold mb-2 required">Salary Amount</label>
                                <input type="number" class="form-control" min="0" name="salary_amount" placeholder="Enter Employee Salary" value="{{$employee_salary[0]['salary_amount'] ?? ''}}" required />
                            </div>
                            
                            <div class="text-center">
                               
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit_btn_salary" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </form>
                        
                    </div>
                    
                </div>
                
            </div>
            
        </div>
          
        {{--end::Modal - Edit Salary--}}


        <div class="modal fade" id="check-employee-name" data-backdrop="static" data-keyboard="False" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-scrollable modal-dialog-top mw-950px">
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Employees Reporting</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary close_btn" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div class="modal-body scroll-y pt-0 pb-15">
                        <form id="update_employee_can_ra" class="form">
                            @csrf  
                            <input type="hidden" name="old_reporting_emp_id" id="old_reporting"/>
                            <input type="hidden" name="emp_deactivate_date" id="emp_deactivate_date"/>
                            <input type="hidden" name="emp_deactivate_type" id="emp_deactivate_type"/>
                            <input type="hidden" name="emp_can_ra" id="emp_can_ra"/>
                            <div class="card card-flush pt-3 mb-5 mb-lg-10" data-kt-subscriptions-form="pricing">
                                <div class="card-body pt-0">
                                    <div class="row pt-3 mb-4">
                                    <p>Employee(s) are under reporting of this employee. Kindly update their reporting authorities.</p>
                                        <div id="employee-manager">

                                        </div>
                                    </div>
                                    <div id="kt_create_new_payment_method">
                                      
                                        <table class="table table-row-bordered">
                                            <thead>
                                                <tr class="fw-bold text-muted bg-light">
                                                    <th class="ps-7">Employee Name</th>
                                                    <th >Department</th>
                                                    <th >Designation</th>
                                                    <th >Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="employee-data">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                {{-- <button type="reset" class="btn btn-light me-3 close_btn" data-bs-dismiss="modal">Close</button> --}}
                                <button type="submit" id="employees_reporting_submit_btn" class="btn btn-success">
                                    <span class="indicator-label">Update</span>
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
<script src="{{url('assets/js/employees/edit-employee.js')}}"></script>
<script>
    var gross_salary_ratio = {{ $no_of_employees[0]['gross_salary_ratio'] }};
    var employee_isactive = '{{ $employee_detail[0]['employee_isactive'] }}';
    var employement_status = @json($employee_detail[0]['employee_status']);
    var efffective_date = @json($employment_period[0]['effective_date']);
    var doj = @json($employee_detail[0]['employee_doj']);
    var url = '{{url()->previous()}}';
</script>

    
@endpush