@extends('layout.main')

@section('main-section')


    <div class="d-flex flex-column flex-column-fluid">
        <form id="hrm_setting" class="form " autocomplete="off" >
            @csrf 
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

                <div class="app-container col-12 d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">System Settings</h1>
                    </div>
                    <div class="d-flex justify-content-end mb-8">
                        <div class="me-3 ">
                            <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Download System Setting
                            </a>
                        </div>
                        @if(!empty($setting))
                            <button type="submit" id="submit_btn" class="btn btn-sm btn-success">
                                <input type="hidden"  name="form_status" value="update" />
                                <span class="indicator-label">Update Changes</span>    
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="app-content flex-column-fluid">
                <div class="app-container">
                    <div class="row">
                        <input type="hidden" name="setting_id" value="{{$setting[0]['setting_id'] ?? ''}}" />
                        <div class="col-md-3 card mb-5">
                            <div class="card-body p-0">
                                <div class="d-flex flex-column ">
                                    <div class="card card-flush">
                                        <div class="card-header" style="justify-content: center !important">
                                            <div class="card-title">
                                                <h2 >Company Logo</h2>
                                            </div>
                                        </div>
                                        <div class="card-body text-center pt-0">
                                            
                                            <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                                                <div class="image-input-wrapper w-150px h-150px main-div " style="background-image: url('{{URL('portal_assets/setting/images/'.$setting[0]['company_logo']) }}')">
                                                </div>
                                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change logo">
                                                    <i class="bi bi-pencil-fill fs-7"></i>
                                                    <input type="file" name="company_logo" accept=".png, .jpg, .jpeg"  id="file"/>
                                                </label>
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel logo">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove logo">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                            </div>
                                            <div class="text-muted fs-7">This logo will diaplay as a company's primary logo in system. Only *.png, *.jpg and *.jpeg image files are accepted.</div>
                                        </div>
                                    </div>
        
                                    <div class="card card-flush">
                                        <div class="card-header" style="justify-content: center !important">
                                           
                                            <div class="card-title" >
                                                <h2>Company Favicon</h2>
                                            </div>
                                        </div>
                                        <div class="card-body text-center pt-0">
                                            <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                                                <div class="image-input-wrapper w-150px h-150px " style="background-image: url('{{URL('portal_assets/setting/images/'.$setting[0]['company_favicon']) }}')">
                                                </div>
                                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow " data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change favicon">
                                                    <i class="bi bi-pencil-fill fs-7"></i>
                                                    <input type="file" name="company_favicon" accept=".png, .jpg, .jpeg" />
                                            
                                                </label>
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel favicon">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove favicon">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                            </div>
                                            <div class="text-muted fs-7">This image will display as your favicon. Only *.png, *.jpg and *.jpeg image files are accepted.</div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="card card-flush">
                                <div class="card-header card-header-stretch">
                                    <div class="card-title d-flex">
                                    </div>
                                    <div class="card-toolbar m-0">
                                        <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bold"
                                            role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a id="general-settings-tabs" class="tab-nav nav-link  text-active-gray-800 active" data-bs-toggle="tab" role="tab" href="#general-settings-tab">General</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a id="attendance-settings-tabs" class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#attendance-settings-tab">Attendance</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a id="payroll-settings-tabs"  class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#payroll-settings-tab">Payroll</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a id="email-config-tabs"  class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#email-config-tab">Email Config</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a id="leave-settings-tabs"  class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#leave-settings-tab">Leaves</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a id="salary-settings-tabs"  class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#salary-settings-tab">Salary</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a id="email-settings-tabs"  class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#email-settings-tab">Email Recipients</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body pt-5">
                                    <div class="tab-content" id="custom-tabs-four-tabContent2">
                                        <div class="tab-panel card-body p-0 tab-pane fade show active" id="general-settings-tab"
                                            role="tabpanel" aria-labelledby="general-settings-tabs">
                                            <div class="card card-flush py-4">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <h2>General Settings</h2>
                                                    </div>
                                                </div>
                                                <div class="card-body pt-0">
                                                    <div class="mb-4">
                                                        <label class="required form-label">Company Name</label>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="This company name will display all over system. For e.g on sidebar etc."></i>
                                                        <input type="text" class="form-control mb-2" name="company_name"  value="{{$setting[0]['company_name'] ?? ''}}" placeholder="Enter Company Name" required />
                                                        
                                                    </div>
                
                                                    <div class="mb-4">
                                                        <label class="required form-label">Company Primary Email</label>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="This company email will display all over system."></i>
                                                        <input type="text" class="form-control mb-2" name="company_email"  value="{{$setting[0]['company_email'] ?? ''}}" placeholder="Enter Company Email" required />
                                                        
                                                    </div>
        
                                                    <div class="mb-4">
                                                        <label class="required form-label">Company Address</label>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="This company name will display all over system."></i>
                                                        <input type="text" class="form-control mb-2" name="company_address"  value="{{$setting[0]['company_address'] ?? ''}}" placeholder="Enter Company Address" required />
                                                        
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="form-label email_configurationl_label">Representative HR Employee</label>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="This representative HR employee's name will be used in system generated emails."></i>
                                                        <select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select HR" name="onboarding_email_name" data-close-on-select="true">
                                                            <option value="">Select HR</option>
                                                            @if(!empty($hr_employees))
                                                                @foreach($hr_employees as $hr)
                                                                    <option value="{{$hr['employee_name']}}"
                                                                    @if(!empty($setting) && $setting[0]['onboarding_email_name'] == $hr['employee_name'])
                                                                        selected
                                                                    @endif
                                                                     >{{$hr['employee_name']}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="form-label email_configurationl_label">Representative Finance Employee</label>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="This representative Finance employee's name will be used in system generated pay slips and reports."></i>
                                                        <select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select Finance" name="name_on_payslip" data-close-on-select="true">
                                                            <option value="">Select Finance</option>
                                                            @if(!empty($finance_employees))
                                                                @foreach($finance_employees as $finance)
                                                                    <option value="{{$finance['employee_id']}}"
                                                                    @if(!empty($setting) && $setting[0]['name_on_payslip'] == $finance['employee_id'])
                                                                        selected
                                                                    @endif
                                                                     >{{$finance['employee_name']}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="form-label required">System Currency</label>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="This currency format will show"></i>
                                                        <select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select System Currency" name="system_currency" data-close-on-select="true" >
                                                            <option value="">Select System Currency</option>
                                                            <option value="PKR" @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'PKR') selected @endif>Pakistani Rupee</option>  
                                                            <option value="AUD" @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'AUD') selected @endif>Australian Dollar</option>  
                                                            <option value="CAD" @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'CAD') selected @endif>Canadian Dollar</option>  
                                                            <option value="USD" @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'USD') selected @endif>United States Dollar</option>  
                                                            <option value="GBP" @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'GBP') selected @endif>UNITED KINGDOM</option>  
                                                             
                                                            {{-- @if(!empty($finance_employees)) 
                                                                @foreach($finance_employees as $finance)
                                                                    <option value="{{$finance['employee_id']}}"
                                                                    @if(!empty($setting) && $setting[0]['name_on_payslip'] == $finance['employee_id'])
                                                                        selected
                                                                    @endif
                                                                     >{{$finance['employee_name']}}</option>
                                                                @endforeach
                                                            @endif --}}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="tab-panel card-body p-0 tab-pane fade show" id="attendance-settings-tab"
                                            role="tabpanel" aria-labelledby="attendance-settings-tabs">
                                            <div class="card card-flush py-4">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <h2>Attendance Devices</h2> &nbsp; 
                                                        <span class="text-gray-400 mt-1 fw-semibold fs-8">(Attendance can be performed only through registered IP Addresses)</span>
                                                    </div>
                                                    <div class="card-toolbar" >
                                                        <button class="btn btn-primary btn-sm" type="button" id="add_new_ip_address"><i class="fa fa-plus"></i>Add New IP Address</button>
                                                    </div>
                                                </div>
                                                @php $count = 0 @endphp
                                                @if(!empty($mac_address))
                                                    
                                                    <div class="card-body" >
                                                        <div id="ip_div">
                                                            @foreach($mac_address as $mac)
                                                                @php $index = ++ $count; @endphp
                                                                <div class="row mb-4 mac_row{{$index}}">
                                                                    <label class="form-label">IP Address {{ $index }}</label>
                                                                    <div class="col-sm-11">
                                                                        <input type="text" class="form-control mb-2" name="mac_address[]" value="{{$mac['mac_address']}}" placeholder="Enter IP Address {{ $index }}" required/>
                                                                    </div>
                                                                    @if($index > 1)
                                                                        <div class="col-sm-1">
                                                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" id="mac_removed" onclick="delete_mac_address('{{$mac['address_id'] }}', '{{$index}}')">
                                                                                <span class="svg-icon svg-icon-3">
                                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                                        <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                                        <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                                    </svg>
                                                                                </span>
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    
                                                        <div class="mb-4">
                                                            <label class="form-check form-switch form-check-custom form-check-solid">
                                                                <input class="form-check-input" name="web_attendance" type="checkbox" value="1" @if(!empty($setting[0]['web_attendance']) && $setting[0]['web_attendance'] == 1) checked="checked"  @endif />
                                                                <span class="form-check-label fw-semibold text-muted">Web Attendance Permission</span>
                                                            </label>
                                                        </div>

                                                        <div class="mb-4">
                                                            <label class="form-check form-switch form-check-custom form-check-solid">
                                                                <input class="form-check-input" name="mobile_attendance" type="checkbox" value="1" @if(!empty($setting[0]['mobile_attendance']) && $setting[0]['mobile_attendance'] == 1) checked="checked"  @endif />
                                                                <span class="form-check-label fw-semibold text-muted">Mobile Attendance Permission</span>
                                                            </label>
                                                        </div>

                                                        <div class="mb-4">
                                                            <label class="required form-label">Check In Grace Time </label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Check in grace time for late marking. Grace time should be in minutes greater and equals to 0 minutes and less and equals to 60 minutes."></i>
                                                            <input type="number" name="check_in_grace_time" class="form-control mb-2" value="{{$setting[0]['check_in_flexibility_time'] ?? ''}}" placeholder="Value should be in minutes" min="0" max="60" required/>
                                                        </div>
                    
                                                        <div class="mb-4">
                                                            <label class="required form-label">Check Out Grace Time</label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Check out grace time for early checkout or half day. Grace time should be in minutes greater and equals to 0 minutes and less and equals to 60 minutes."></i>
                                                            <input type="number" name="check_out_grace_time" class="form-control mb-2"  value="{{$setting[0]['check_out_flexibility_time'] ?? ''}}" placeholder="Value should be in minutes" min="0" max="60" required/>
                                                        </div>
 
                                                    </div>
                                                   
                                                @endif
                                            </div>
                                        </div>

                                        <div class="tab-panel card-body p-0 tab-pane fade show" id="payroll-settings-tab"
                                            role="tabpanel" aria-labelledby="payroll-settings-tabs">
                                            <div class="card card-flush py-4">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <h2>Payroll Settings</h2>
                                                    </div>
                                                </div>
                                                <div class="card-body pt-0">
                                                    <div class="mb-4">
                                                        <label class="required form-label">Payroll Start Period</label>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Payroll Period Date ."></i>
                                                        @if(empty($setting) || empty($setting[0]['payroll_start_date']))
                                                            <input type="number" class="form-control mb-2" value="{{$setting[0]['payroll_start_date']}}"  name="payroll_start_date" required />
                                                        @else
                                                            <input type="number" class="form-control mb-2" value="{{$setting[0]['payroll_start_date']}}"  disabled min="1" max="28" required />
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="row">

                                                        <div class="col-md-6 mb-4">
                                                            <label class="required form-label">Tax Fiscal Start Date</label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Tax Fiscal Start Date ."></i>
                                                            <input type="date" class="form-control mb-2" name="fiscal_date" id="fiscal_start_date" value="{{$setting[0]['fiscal_date']}}"   required />
                                                          
                                                            
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-6">
                                                            @php   $date = date('Y-m-d', strtotime('+1 year -1 day', strtotime($setting[0]['fiscal_date'])) );
                                                           @endphp
                                                            <label class="required form-label">Tax Fiscal End Date</label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Tax Fiscal End Date ."></i>
                                                            <input type="date" class="form-control mb-2" id="fiscal_end_date"  value="{{$date}}"   readonly />
                                                          
                                                            
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-4">
                                                            <label class="required form-label">Late Deduction Rate</label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Late Deduction Rate (Daily Wages)"></i>
                                                            <div class="input-group" >    
                                                                <input type="number" class="form-control" name="daily_wages_late_deduction" value="{{ (!empty($setting[0]['daily_wages_late_deduction'])) ? $setting[0]['daily_wages_late_deduction']*100 : ''}}" value="" min="0" max="100" />
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-6">
                                                            <label class="required form-label">Half Day Deduction Rate</label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Half Day Deduction Rate (Daily Wages)"></i>
                                                            <div class="input-group" > 
                                                                <input type="number" class="form-control" name="daily_wages_half_deduction" value="{{!empty($setting[0]['daily_wages_half_deduction']) ? $setting[0]['daily_wages_half_deduction']*100  : ''}}" value="" min="0" max="100" />
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                
                                                    <div class="mb-4">
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input" name="club_rule" type="checkbox" value="1" @if(!empty($setting[0]['club_rule_apply']) && $setting[0]['club_rule_apply'] == 1) checked="checked"  @endif/>
                                                            <span class="form-check-label fw-semibold text-muted">Club Rule Implentation on Payroll</span>
                                                        </label>
                                                    </div>
                                                    
                                                    <div class="mb-4">
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input" name="tax_deduction" type="checkbox" value="1"  @if(!empty($setting[0]['tax_deduction_apply']) && $setting[0]['tax_deduction_apply'] == 1) checked="checked"  @endif />
                                                            <span class="form-check-label fw-semibold text-muted">Tax Deduction on Payroll</span>
                                                        </label>
                                                    </div>

                                                    <div class="card card-flush">
                                                        <div class="card-header" style="justify-content: center !important">
                                                            <div class="card-title">
                                                                <h2 >Signature</h2>
                                                            </div>
                                                        </div>
                                                        <div class="card-body text-center pt-0">
                                                            
                                                            <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                                                                <div class="image-input-wrapper w-150px h-150px main-div " style="background-image: url('{{URL('portal_assets/setting/images/'.$setting[0]['signature_on_payslip']) }}')">
                                                                </div>
                                                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Signature">
                                                                    <i class="bi bi-pencil-fill fs-7"></i>
                                                                    <input type="file" name="signature_on_payslip" accept=".png, .jpg, .jpeg"  id="file"/>
                                                                </label>
                                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Signature">
                                                                    <i class="bi bi-x fs-2"></i>
                                                                </span>
                                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove Signature">
                                                                    <i class="bi bi-x fs-2"></i>
                                                                </span>
                                                            </div>
                                                            <div class="text-muted fs-7">This signature will diaplay on payslip. Only *.png, *.jpg and *.jpeg image files are accepted.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="tab-panel card-body p-0 tab-pane fade show" id="email-config-tab"
                                            role="tabpanel" aria-labelledby="email-config-tabs">
                                            <div class="card card-flush py-4">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <h2>Email Configuration</h2>
                                                    </div>
                                                </div>
                                                <div class="card-body pt-0">

                                                    <div class="row" >
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">Email</label>
                                                            <input type="email" class="form-control mb-2 email_configurationl_input" value="@if(!empty($email_config)){{$email_config[0]['email_config_email']}}@endif"  name="email" placeholder="Enter Email"  />
                                                        </div>
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">Password</label>
                                                            <input type="text" class="form-control mb-2 email_configurationl_input" value="@if(!empty($email_config)){{$email_config[0]['email_config_pass']}}@endif"  name="password" placeholder="Enter Password"  />
                                                        </div>
                                                    </div>

                                                    <div class="row" >
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">Mailer</label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="smtp"></i>
                                                            <input type="text" class="form-control mb-2 email_configurationl_input" value="@if(!empty($email_config)){{$email_config[0]['email_config_mailer']}}@endif"  name="mailer" placeholder="Enter Email"  />
                                                        </div>
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">Host</label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="DNS record (MX)"></i>
                                                            <input type="text" class="form-control mb-2 email_configurationl_input" value="@if(!empty($email_config)){{$email_config[0]['email_config_host']}}@endif"  name="host" placeholder="Enter Host" />
                                                        </div>
                                                    </div>

                                                    <div class="row" >
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">Port</label>
                                                            <input type="text" class="form-control mb-2 email_configurationl_input" value="@if(!empty($email_config)){{$email_config[0]['email_config_port']}}@endif" name="email_port" placeholder="Enter Port"  />
                                                        </div>
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">Encryption</label>
                                                            <input type="text" class="form-control mb-2 email_configurationl_input" value="@if(!empty($email_config)){{$email_config[0]['email_config_encryption']}}@endif"  name="email_encryption" placeholder="Enter Encryption " />
                                                        </div>
                                                    </div>

                                                    <div class="row" >
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">From Address</label>
                                                            <input type="text" class="form-control mb-2 email_configurationl_input" value="@if(!empty($email_config)){{$email_config[0]['email_config_from_address']}}@endif"  name="from_address" placeholder="Enter From Address" />
                                                        </div>
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">From Name</label>
                                                            <input type="text" class="form-control mb-2 email_configurationl_input" value="@if(!empty($email_config)){{$email_config[0]['email_config_from_name']}}@endif"  name="from_name" placeholder="Enter From Name"/>
                                                        </div>
                                                    </div>

                                                    
                                                    <div class="mb-4">
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input" name="email_permission" type="checkbox" value="1"  @if(!empty($setting[0]['company_email_permission']) && $setting[0]['company_email_permission'] == 1) checked="checked"  @endif @if(empty($email_config)) disabled @endif />
                                                            <span class="form-check-label fw-semibold text-muted">Use this email for email alerts and notifications</span>
                                                        </label>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input" name="push_notification_permission" type="checkbox" value="1"  @if(!empty($setting[0]['push_notification_permission']) && $setting[0]['push_notification_permission'] == 1) checked="checked"  @endif @if(empty($email_config)) disabled @endif />
                                                            <span class="form-check-label fw-semibold text-muted">Fcm Enability</span>
                                                        </label>
                                                    </div>

                                                    <br>
                                                    @if(!empty($setting[0]['company_email_permission']))
                                                        <div class="row" >
                                                            <div class="col-md-12 mb-4">
                                                                <label class="required form-label">Email</label>
                                                                <input type="email" class="form-control mb-2" placeholder="Enter Receiving Email" name="receiving_mail" />
                                                            </div>
                                                        </div>
                                                        <div class="card-toolbar" >
                                                            <button type="button"  class="btn btn-success btn-sm" onclick="sendTestEmail(this)">
                                                                <span class="indicator-label">Send Test Mail</span>    
                                                                <span class="indicator-progress">Please wait... 
                                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                            </button>
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-panel card-body p-0 tab-pane fade show" id="leave-settings-tab"
                                            role="tabpanel" aria-labelledby="leave-settings-tabs">
                                            <div class="card card-flush py-4">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <h2>Leaves Settings</h2>
                                                    </div>
                                                </div>
                                                <div class="card-body pt-0">
                                                    @if(!empty($get_leave_managements))
                                                        <div class="row">
                                                            @foreach ($get_leave_managements as $manage_leave)
                                                                <div class="col-md-6 mb-4">
                                                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                    <span class="required">{{$manage_leave['leave_name']}}</span></label>
                                                                    <input type="hidden" class="form-control " value="{{$manage_leave['leave_manage_id']}}"  name="leave_manage_id[]"  required="required" />
                                                                    <input type="number" class="form-control " min="0" max="50" name="leave_yearly_allotment[]" value="{{$manage_leave['leave_yearly_allotment'] ?? 0}}"  required="required" />
                                                                </div>
                                                                
                                                        
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    

                                                    {{-- <div class="row" >
                                                        <div class="col-md-6 mb-4">
                                                            <label class="required form-label">Total Sick Leave(s)</label>
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="smtp"></i>
                                                            <input type="text" class="form-control mb-2" value="{{$setting[0]['total_sick_allocation']}}" name="total_sick_leave" placeholder="Total Sick Leave(s)"  />
                                                        </div>
                                                    </div> --}}

                                                    <div class="mb-4">
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input" name="annual_leave_allow" type="checkbox" value="1" @if(!empty($setting[0]['annual_leave_display']) && $setting[0]['annual_leave_display'] == 1) checked="checked"  @endif  />
                                                            <span class="form-check-label fw-semibold text-muted">Show <b>Annual Leave(s)</b> to probation employees.</span>
                                                        </label>
                                                    </div> 

                                                    <div class="mb-4">
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input" name="all_leave_visibilty_allow" type="checkbox" value="1" @if(!empty($setting[0]['total_leave_all_leaves_visibility']) && $setting[0]['total_leave_all_leaves_visibility'] == 1) checked="checked"  @endif  />
                                                            <span class="form-check-label fw-semibold text-muted">Show total Leaves / Show at the moment count of leaves.</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-panel card-body p-0 tab-pane fade show" id="salary-settings-tab"
                                            role="tabpanel" aria-labelledby="salary-settings-tabs">
                                            <div class="card card-flush py-4">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <h2>Salary Division</h2> &nbsp;
                                                        <span class="text-danger mt-1 fw-semibold fs-8 d-none" id="division_message">(Salary Division Percentage should not be greater than 100)</span>
                                                    </div>
                                                    <div class="card-toolbar" >
                                                        <button class="btn btn-primary btn-sm" type="button" onclick="addfileRow()"><i class="fa fa-plus"></i>Add Salary Division</button>
                                                    </div>
                                                </div>

                                                @php $count = 0 @endphp
                                                @if(!empty($salary_division))

                                                    <div class="card-body">

                                                        <div class="row mt-3 division_row"> 
                                                            <div class="col-md-12 mb-4">
                                                                <label class="fs-6 fw-semibold mb-2 required">Gross Salary Percentage</label>
                                                                <input type="text" class="form-control"  name="gross_salary_ratio" value="{{ number_format($setting[0]['gross_salary_ratio'],3) }}" placeholder="Gross Salary Percentage" required="">
                                                            </div>
                                                        </div>

                                                        <div id="salary_div">
                                                            
                                                            @foreach($salary_division as $division)
                                                                @php $increment = ++ $count; @endphp
                                                                <div class="row mt-3 division_row{{$increment}}"> 
                                                                
                                                                    <div class="col-md-5 mb-4">
                                                                        <label class="fs-6 fw-semibold mb-2 required">Salary Division Title </label>
                                                                        <input type="text" class="form-control"  name="salary_division_title[]" value="{{$division['salary_division_title']}}" placeholder="Salary Division Title" required="">
                                                                    </div>

                                                                    <div class="col-md-5 mb-4">
                                                                        <label class="fs-6 fw-semibold mb-2 required">Salary Division Percentage </label>
                                                                        <div class="input-group">
                                                                            <input type="number" min="0" max="100" class="form-control salarypercentage" step="0.01" name="salary_division_percentage[]" value="{{$division['salary_division_percentage']}}" placeholder="Salary Division Percentage" required="" onkeyup="maxpercentage()">
                                                                            <span class="input-group-text">%</span>
                                                                        </div>  
                                                                    </div>
                                                                    @if($increment > 1)
                                                                        <div class="col-md-2 mb-4" >
                                                                            <label class="fs-6 fw-semibold mb-15"></label>
                                                                            <button class="btn btn-icon btn-sm" type="button" onclick="delete_salary_division('{{$division['salary_division_id']}}','{{$increment}}')">
                                                                            <span class="svg-icon svg-icon-3">
                                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                                </svg>
                                                                            </span>    
                                                                            </button>
                                                                        </div>
                                                                    @endif   
                                                                </div>
                                                            @endforeach 
                                                        </div>
                                                    </div>  
                                                @else
                                                    <div class="card-body">   
                                                        <div id="salary_div">
                                                            <div class="row mt-3">
                                                                <input type="hidden" name="salary_length" value="1">

                                                                <div class="col-md-5 mb-4">
                                                                    <label class="fs-6 fw-semibold mb-2 required">Salary Division Title </label>
                                                                    <input type="text" class="form-control"  name="salary_division_title[]" placeholder="Salary Division" required="">
                                                                </div>

                                                                <div class="col-md-5 mb-4">
                                                                    <label class="fs-6 fw-semibold mb-2 required">Salary Division Percentage </label>
                                                                        <div class="input-group">
                                                                            <input type="number" min="0" max="100" class="form-control salarypercentage" step="0.01" name="salary_division_percentage[]" placeholder="Salary Division Percentage" required="" onkeyup="maxpercentage()">
                                                                            <span class="input-group-text">%</span>
                                                                        </div>                                                         
                                                                </div>
                                                            </div>   
                                                        </div>      
                                                    </div>    
                                                @endif
                                            </div>
                                        </div>

                                        <div class="tab-panel card-body p-0 tab-pane fade" id="email-settings-tab"
                                            role="tabpanel" aria-labelledby="email-settings-tabs">
                                            <div class="card card-flush py-4">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <h2>Email Recipients</h2>
                                                    </div>
                                                </div>
                                                <div class="card-body pt-0">
                                                    <div class="col-md-12 mb-4">
                                                        <label class="required form-label email_configurationl_label">Email Endpoints</label>
                                                        <select class="form-select" data-control="select2" multiple="multiple" data-hide-search="true" data-placeholder="Select Email Endpoints" name="email_module[]" data-close-on-select="true">
                                                            @if(!empty($email_endpoints))
                                                                @foreach($email_endpoints as $endpoint)
                                                                    <option value="{{$endpoint['endpoints_id']}}" >{{$endpoint['endpoint_desc']}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">To Recipients</label>
                                                            <select class="form-select" data-control="select2" multiple="multiple" data-hide-search="false" data-placeholder="Select Employee" name="email_reciever[]" data-close-on-select="true">
                                                                @if(!empty($operation_manager))
                                                                    @foreach($operation_manager as $manager)
                                                                        <option value="{{$manager['employee_id']}}" >{{$manager['employee_name']}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
    
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label email_configurationl_label">Cc Recipients</label>
                                                            <select class="form-select" data-control="select2" multiple="multiple" data-hide-search="false" data-placeholder="Select Employee" name="cc_recipients[]" data-close-on-select="true">
                                                                @if(!empty($operation_manager))
                                                                    @foreach($operation_manager as $manager)
                                                                        <option value="{{$manager['employee_id']}}" >{{$manager['employee_name']}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="card-toolbar justify-content-center" >
                                                        <button type="button"  class="btn btn-success btn-sm" onclick="addEmail(this)">
                                                            <span class="indicator-label">Add Recipients</span>    
                                                            <span class="indicator-progress">Please wait... 
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card card-flush py-4">
                                                <div class="card-header">
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
                                                <div class="card-body pt-0">
                                                    <table class="table table-bordered align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable">
                                                        <thead>
                                                            <tr class="ps-4 fw-bold text-muted bg-light">
                                                                <th>Employee</th>
                                                                <th>Recipients Type</th>
                                                                <th>Endpoint</th>
                                                                <th class="text-center" >Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(!empty($email_recipient))
                                                                @php $count_record = 0 @endphp
                                                                @foreach($email_recipient as $key=>$recipient)
                                                                    {{-- @php $increment = ++ $count_record; @endphp --}}
                                                                    <tr class="email_reciept_row" data-recipient-id="{{ $recipient['recipient_id'] }}">
                                                                        <td>{!! CustomHelper::getEmpProfileDiv($recipient['receiever_id']) !!} </td>
                                                                        <td> {{$recipient['recipient_type'] }} </td>    
                                                                        <td>{{$recipient['endpoint_desc'] }}</td>     
                                                                        <td class="text-center" >
                                                                            <a href="javascript:void(0);" onclick="delete_module_email_access('{{$recipient['endpoint_id']}}','{{$recipient['recipient_id']}}','{{$recipient['recipient_type'] }}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="tooltip" title="Remove Module">
                                                                                <span class="svg-icon svg-icon-3">
                                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                                        <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                                        <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                                    </svg>
                                                                                </span>
                                                                            </a>
                                                                        </td>
                                                                    
                                                                   
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
                            </div>

                        </div>
                    
                    </div>
                </div>
            </div>
        </form>
    </div>



    <div class="modal fade" id="modal-danger" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content bg-rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Confirmation Alert</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1 bg-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="delete_ip_address" class="form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="address_id" />
                        <p>Are you sure to delete this IP Address ?</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="delete_button" class="btn btn-primary">
                            <span class="indicator-label">Delete</span>
                            <span class="indicator-progress">Please wait 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>    
            </div>
        </div>
    </div>

    <div class="modal fade" id="salary_division" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content bg-rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Confirmation Alert</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1 bg-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="delete_salary_division" class="form ">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="salary_division_id" />
                        <p>Are you sure to delete this Salary Division ?</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="delete_submit_btn" class="btn btn-primary">
                            <span class="indicator-label">Delete</span>
                            <span class="indicator-progress">Please wait 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>    
            </div>
        </div>
    </div>


    <div class="modal fade" id="email-module-access-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content bg-rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Confirmation Alert</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1 bg-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="email_module_access_delete" class="form ">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="endpoint_id" />
                        <input type="hidden" class="form-control" name="recipient_id" />
                        <input type="hidden" class="form-control" name="recipient_type" />
                        <p>Are you sure to delete this employee email module access ?</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="delete_module_submit_btn" class="btn btn-primary">
                            <span class="indicator-label">Delete</span>
                            <span class="indicator-progress">Please wait 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>    
            </div>
        </div>
    </div>

    <form class="form" id="downloadbutton" action="{{url('/system-settings/download-system-setting')}}" method="POST">
        @csrf
        <div class="modal fade" id="download_modal_pdf" uploaddex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">

                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Download System Setting</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-8">

                            <div class="col-md-12 mt-3 fv-row">
                                <p onselectstart="javascript:return false;" >This action required a security check. If you want to download system setting  report,
                                Please type <b>TODP-ID</b> (case sensitive) to confirm.</p>
                                <input type="text" class="form-control " name="confirmation_code" placeholder="Enter Confirmation Code" required="required" autocomplete="off" onpaste="return false" >
                            </div>

                            <div class="col-md-12 mt-3 fv-row text-center">
                                <span class="d-none text-danger" id="download_message">
                                    Please wait, PDF file generation is in process. It may take a while.
                                </span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="download_btn" class="btn btn-primary">
                            <span class="indicator-label">Download</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('js-link')

    <script>
        const payroll_date = "{{ $setting[0]['payroll_start_date'] }}";
        var length_count = 2 + {{ $count-2}};
    </script>    
    <script src="{{url('assets/js/miscellanous/hrm-settings.js')}}"></script>

@endpush