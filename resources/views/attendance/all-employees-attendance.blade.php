@extends('layout.main')



@push('css-link')
table.dataTable
{
 overflow-x:hidden !important;
 overflow-y:auto !important;
}
.dtfh-floatingparent dtfh-floatingparenthead{
    top:50px !important;
}
@endpush



@section('main-section')


<div class="d-flex flex-column flex-column-fluid">
						 
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                @if (empty($month) && empty($year))
                    Attendance Sheet ({{ date('F') . '-' . date('Y') }})
                @else
                    Attendance Sheet ({{ date("F", mktime(0, 0, 0, $month, 10)) }}-{{ $year }})
                @endif
                </h1>   
            </div>
            <div>
                @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null))

                  
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#att-share-report-modal">								
                        Attendance Report Share
                    </a>
               
                    <button class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modal-default2">Mark Attendance</button>
                @endif
                    <button class=" btn btn-sm btn-info float-sm-right float-sm-right " data-bs-toggle="modal" data-bs-target="#modal_signs">Check Signs </button>
            </div>
           

        </div>
    </div>
       
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">

                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1 me-5">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilter">
                        </div>
                        
                    </div>

                    @php $update_attendance = '' @endphp

                    @if(!empty($attendance_access) && $attendance_access[0]['attendance_access'] == 1)
                       
                        @php  $update_attendance = 'marked-attendance' @endphp
                    @endif
                    
                    @php $filter = 'btn-color-primary'; @endphp
                    
                    {{-- @if (!empty($all_attendances)) --}}
                        <div class="card-toolbar" >
                            
                            <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('employee_location')) ? $filter : '' }} {{ !empty(Request::get('is_active')) ? $filter : '' }} {{ !empty(Request::get('emp_name')) ? $filter : '' }} {{ !empty(Request::get('dept')) ? $filter : '' }} {{ !empty(Request::get('m')) ? $filter : '' }} {{ !empty(Request::get('y')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        
                            <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            Filter</a>
                            
                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                    <form action="{{ url('attendance') }}" method="GET">
                                        <div class="px-7 py-5">
                                            <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                        </div>
                                    
                                        <div class="separator border-gray-200"></div>
                                        <div class="px-7 py-5">

                                            <div class="mb-3">
                                            
                                                <label class="form-label fw-semibold">
                                                    <span>Office Location</span>
                                                </label>
                                                
                                                <div>
                                                    <select class="form-select" data-control="select2" data-hide-search="false" name="employee_location" id="employee_location">
                                                        <option value="">Select Office Location</option>
                                                        @if(!empty($employee_locations))
                                                            @foreach ($employee_locations as $location)
                                                                <option value="{{ $location['location_id'] }}" @if (Request::get('employee_location') == $location['location_id']) selected @endif>{{ $location['location_title'] }}</option>
                                                            @endforeach
                                                        @endif                                                             
                                                    </select>
                                                </div>
                                                
                                            </div>

                                            <div class="mb-3">
                                            
                                                <label class="form-label fw-semibold">Employee Status</label>
                                                
                                                <div>
                                                    <select class="form-select" data-kt-select2="true" data-placeholder="Select option" name="is_active" id="employee_data" required>
                                                        <option value="1" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '1') selected @endif>Active Employees</option>
                                                        <option value="2"@if(!empty(Request::get('is_active')) && Request::get('is_active') == '2') selected @endif>De-active Employees</option>
                                                    </select>
                                                </div>
                                                
                                            </div>

                                            <div class="mb-3">
                                                
                                                <label class="form-label fw-semibold">
                                                    <span>By Name</span>  
                                                </label>
                                                
                                                <div>
                                                    <select class="form-select " data-kt-select2="true" name="emp_name"
                                                    id="company_emp_name" data-employee-select data-scope="team">
                                                        @if(!empty($all_employees))
                                                        <option value="">All Employees</option>
                                                            @include('include.employee-selected-options', ['selected' => Request::get('emp_name')])
                                                        @endif
                                                    </select>
                                                </div>
                                                
                                            </div>
                                            <div class="mb-3 row" >
                                                <div class="col-6">
                                                
                                                    <label class="form-label fw-semibold">Select Month</label>
                                                    
                                                    <div>
                                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Month" name="m" required="required">
                                                            <option value="">Select Month</option>
                                                            <option value="01" @if (!empty($month) && $month == '01') selected  @elseif(empty($month) && date('m') == '01') selected  @endif>January
                                                            </option>
                                                            <option value="02" @if (!empty($month) && $month == '02') selected @elseif(empty($month) && date('m') == '02') selected  @endif>February
                                                            </option>
                                                            <option value="03" @if (!empty($month) && $month == '03') selected @elseif(empty($month) && date('m') == '03') selected  @endif>March
                                                            </option>
                                                            <option value="04" @if (!empty($month) && $month == '04') selected @elseif(empty($month) && date('m') == '04') selected @endif>April
                                                            </option>
                                                            <option value="05" @if (!empty($month) && $month == '05') selected @elseif(empty($month) && date('m') == '05') selected @endif>May
                                                            </option>
                                                            <option value="06" @if (!empty($month) && $month == '06') selected @elseif(empty($month) && date('m') == '06') selected @endif>June
                                                            </option>
                                                            <option value="07" @if (!empty($month) && $month == '07') selected @elseif(empty($month) && date('m') == '07') selected  @endif>July
                                                            </option>
                                                            <option value="08" @if (!empty($month) && $month == '08') selected @elseif(empty($month) && date('m') == '08') selected  @endif>August
                                                            </option>
                                                            <option value="09" @if (!empty($month) && $month == '09') selected @elseif(empty($month) && date('m') == '09') selected  @endif>September
                                                            </option>
                                                            <option value="10" @if (!empty($month) && $month == '10') selected  @elseif(empty($month) && date('m') == '10') selected @endif>October
                                                            </option>
                                                            <option value="11" @if (!empty($month) && $month == '11') selected  @elseif(empty($month) && date('m') == '11') selected @endif>November
                                                            </option>
                                                            <option value="12" @if (!empty($month) && $month == '12') selected @elseif(empty($month) && date('m') == '12') selected @endif>December
                                                            </option>
                                                        </select>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-6">
                                                    
                                                    <label class="form-label fw-semibold">Select Year</label>
                                                    
                                                    <div>
                                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Year"  name="y" required="required">
                                                            <option value="">Select Year</option>
                                                            @if(!empty($dynamic_years))
                                                                @foreach ($dynamic_years as $year_list)
                                                                <option value="{{$year_list}}" @if (!empty($year) && $year == $year_list) selected @elseif(empty($year) && date('Y') == $year_list) selected  @endif>{{$year_list}}
                                                                </option>
                                                                @endforeach
                                                            @endif
                                                           
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null))
                                                <div class="mb-3">
                                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                        <span>Department</span>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="By selecting department you will only get result of employees that are active in selected department."></i>
                                                    </label>
                                                
                                                    <div>
                                                        <select class="form-select " data-kt-select2="true"  name="dept" onchange="changeDepartment(this.value)">
                                                            <option value="">Select Department</option>
                                                            @if(!empty($all_department))
                                                                @foreach ($all_department as $department)
                                                                    <option value="{{ $department['department_id'] }}" @if (!empty(Request::get('dept')) && Request::get('dept') == $department['department_id']) selected @endif>{{ $department['department_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    
                                                </div>
                                            @endif
                                        
                                            @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null))
                                                @php $update_attendance = 'marked-attendance' @endphp
                                                <div class="mb-3">
                                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                        <span>Reporting Authority</span>
                                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="By selecting reporting authority you will only get result of employees that are active in selected department and under the selected reporting authority."></i>
                                                    </label>
                                                
                                                    <div>
                                                        <select class="form-select" data-kt-select2="true" data-placeholder="Select Reporting Authority"  name="ra" id="reportingAuthority">
                                                            <option value="">Select Reporting Authority</option>
                                                        </select>
                                                    </div>
                                                    
                                                </div>
                                            @endif
                                           
                                            <div class="d-flex justify-content-end">
                                                @if (!empty(Request::get('employee_location')) || !empty(Request::get('is_active')) || !empty(Request::get('emp_name')) || !empty(Request::get('m')) || !empty(Request::get('y')))
                                            
                                                    <a href="{{ url('attendance') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                                        >Reset Filter</a>
                                                @endif
                                                <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" >Close</button>
                                                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                            </div>
                                            
                                        </div>
                                    </form>
                                </div>
                        
                        </div>
                    {{-- @endif --}}
                </div> 
                <div class="card-body py-3" id="sidebarminimize">
                    							 
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                        
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered display nowrap" id="tableAllEmployeeAttendance" data-server-paginated="true" aria-describedby="tableEmployee_info">
                                                                                                            
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-7">Employee</th>
										@for ($month_count = 1; $month_count <= $month_days; ++$month_count)
											@php
											
                                            $date = '' . $month_count . '-' . $month . '-' . $year . '';
											@endphp
											<th>{{ $month_count }} </th>
										@endfor
											<th><i class="fa-solid fa-calendar-check text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total On Time"></i></th>
											<th><i class="fa-solid fa-xmark text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total Absent"></i></th>
											<th><i class="fa-solid fa-calendar-check text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total Half Day"></i></th>
											<th><i class="fa-solid fa-calendar-check text-gray" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total Late Coming"></i></th>
											<th><i class="fa-solid fa-user-large-slash text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total Leaves"></i></th>
                                            <th><i class="fa-solid fa-clock text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total Work Hours"></i></th>
                                            <th>Action</th>
                                    </tr>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-7"></th>
										@for ($month_count = 1; $month_count <= $month_days; ++$month_count)
											@php
											
                                            $date = '' . $month_count . '-' . $month . '-' . $year . '';
											@endphp
											<th style="writing-mode: vertical-rl;" >{{ substr(date('D',strtotime($date)),0,3) }} </th>
										@endfor
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
									@if (!empty($all_attendances))
                                       
										@foreach ($all_attendances as $all_attendance)
											<tr>
                                                @php  
                                                    $calculate_absent_shift = 0;
                                                    $calculate_absent_shift += CustomHelper::employee_shift_time_calculation_absent($all_attendance['employee_id'],$month,$year,$from_date=NUll,$to_date=Null);  @endphp
                                                <td class="ps-6">{{ $all_attendance['employee_name'] }}</td>
												

												@php 
												
													$present_count = 0;
													$absent_count = 0;
													$halfday_count = 0;
													$late_count = 0;
													$leave_count = 0;
												@endphp
                                                @php
                                                    $all_attendance['employee_offdays'] = (!empty($all_attendance['employee_offdays'])) ? explode(",",$all_attendance['employee_offdays']):[];

                                                    // totals and per-record work hours computed once, records grouped by day for the calendar cells
                                                    $total_working_hours = 0;
                                                    $total_working_hours_actual = 0;
                                                    $attendance_by_day = [];
                                                    foreach ($all_attendance['attendance'] as $atty) {
                                                        $calculate_shift_timing = CustomHelper::calculate_shift_timings_shift_management($atty['att_shift_start_time'],$atty['att_shift_end_time'],$atty['att_check_in_status'],$atty['att_check_out_status']);
                                                        $atty['calculated_working_hour'] = CustomHelper::calculate_working_hour($atty['att_check_out_status'],$atty['att_check_in_time'],$atty['att_check_out_time'],$atty['att_created_at'],$atty['att_updated_at']);

                                                        $shift_components = explode(":", $calculate_shift_timing);
                                                        $total_working_hours_actual += $shift_components[0]*60*60 + $shift_components[1]*60;

                                                        $components = explode(":", $atty['calculated_working_hour']);
                                                        $total_working_hours += $components[0]*60*60 + $components[1]*60;

                                                        if ($atty['att_for_date'] != '') {
                                                            $attendance_by_day[(int) $atty['att_for_date']][] = $atty;
                                                        }
                                                    }
                                                @endphp
												@for ($month_count = 1; $month_count <= $month_days; ++$month_count)
													@php $absent = true; @endphp
													<td>
														@foreach ($attendance_by_day[$month_count] ?? [] as $atty)
																@php $absent = false; $calculate_working_hour = $atty['calculated_working_hour']; @endphp

																@if ($atty['att_check_overall_status'] == 'Present')
																	@php $present_count++; @endphp
																	<i class="fa-solid fa-calendar-check {{$atty['att_remote_applicable'] == 1?'text-warning':'text-success'}} {{$update_attendance}}"
																		data-bs-toggle="tooltip"
																		data-bs-placement="top"
																		data-bs-title="Check In Time : {{ $atty['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $atty['att_check_out_time'] }} &nbsp; &nbsp; Work Hours : {{ $calculate_working_hour}}"
																		data-attendance_id="{{ $atty['attendance_id'] }}"
																		data-intime="{{ $atty['att_check_in_time'] }}"
																		data-outtime="{{ $atty['att_check_out_time'] }}"
																		data-attstatus="{{ $atty['att_check_overall_status'] }}" data-workhours="{{ $calculate_working_hour }}" data-attdate="{{ $atty['att_for_date'] . '-' . $atty['att_for_month'] . '-' . $atty['att_for_year'] }}" data-checkinsource="{{ $atty['att_check_in_source'] }}" data-checkoutsource="{{ $atty['att_check_out_source'] }}" data-att_request_approval="{{ $atty['att_request_approval'] }}" style="cursor: pointer;"></i>
																@elseif($atty['att_check_overall_status'] == 'Absent')
																	@php $absent_count++; @endphp
																	<i class="fa-solid fa-xmark {{$atty['att_remote_applicable'] == 1?'text-warning':'text-danger'}} {{$update_attendance}}"
																		data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="Absent"
																		data-attendance_id="{{ $atty['attendance_id'] }}"
																		data-intime="{{ $atty['att_check_in_time'] }}"
																		data-outtime="{{ $atty['att_check_out_time'] }}"
																		data-attstatus="{{ $atty['att_check_overall_status'] }}" data-workhours="{{ $calculate_working_hour }}" data-attdate="{{ $atty['att_for_date'] . '-' . $atty['att_for_month'] . '-' . $atty['att_for_year'] }}" data-checkinsource="{{ $atty['att_check_in_source'] }}" data-checkoutsource="{{ $atty['att_check_out_source'] }}" data-att_request_approval="{{ $atty['att_request_approval'] }}" style="cursor: pointer;"></i>
																@elseif($atty['att_check_overall_status'] == 'Half day')
																	@php $halfday_count++; @endphp
																	<i class="fa-solid fa-calendar-check {{$atty['att_remote_applicable'] == 1?'text-warning':'text-danger'}} {{$update_attendance}}"
																		data-bs-toggle="tooltip"
																		data-bs-placement="top"
																		data-bs-title="Check In Time : {{ $atty['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $atty['att_check_out_time'] }}  &nbsp; &nbsp; Work Hours : {{ $calculate_working_hour }}"
																		data-attendance_id="{{ $atty['attendance_id'] }}"
																		data-intime="{{ $atty['att_check_in_time'] }}"
																		data-outtime="{{ $atty['att_check_out_time'] }}"
																		data-attstatus="{{ $atty['att_check_overall_status'] }}" data-workhours="{{ $calculate_working_hour }}" data-attdate="{{ $atty['att_for_date'] . '-' . $atty['att_for_month'] . '-' . $atty['att_for_year'] }}" data-checkinsource="{{ $atty['att_check_in_source'] }}" data-checkoutsource="{{ $atty['att_check_out_source'] }}" data-att_request_approval="{{ $atty['att_request_approval'] }}" style="cursor: pointer;"></i>
																@elseif($atty['att_check_overall_status'] == 'Late Coming')
																	@php $late_count++; @endphp
																	<i class="fa-solid fa-calendar-check {{$atty['att_remote_applicable'] == 1?'text-warning':'text-gray'}} {{$update_attendance}}"
																		data-bs-toggle="tooltip"
																		data-bs-placement="top"
																		data-bs-title="Check In Time : {{ $atty['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $atty['att_check_out_time'] }}  &nbsp; &nbsp; Work Hours : {{ $calculate_working_hour}}"
																		data-attendance_id="{{ $atty['attendance_id'] }}"
																		data-intime="{{ $atty['att_check_in_time'] }}"
																		data-outtime="{{ $atty['att_check_out_time'] }}"
																		data-attstatus="{{ $atty['att_check_overall_status'] }}" data-workhours="{{ $calculate_working_hour }}" data-attdate="{{ $atty['att_for_date'] . '-' . $atty['att_for_month'] . '-' . $atty['att_for_year'] }}" data-checkinsource="{{ $atty['att_check_in_source'] }}" data-checkoutsource="{{ $atty['att_check_out_source'] }}" data-att_request_approval="{{ $atty['att_request_approval'] }}" style="cursor: pointer;"></i>
                                                                @elseif(in_array($atty['att_check_overall_status'],$leaves_name))
                                                                    @php $leave_count++;
                                                                    @endphp
																	<i class="fa-solid fa-user-large-slash text-info {{$update_attendance}}"
																		data-bs-toggle="tooltip"
																		data-bs-placement="top"
																		data-bs-title="{{$atty['att_check_overall_status']}}"
																		data-attendance_id="{{ $atty['attendance_id'] }}"
																		data-intime="{{ $atty['att_check_in_time'] }}"
																		data-outtime="{{ $atty['att_check_out_time'] }}"
																		data-attstatus="{{ $atty['att_check_overall_status'] }}" data-workhours="{{ $calculate_working_hour }}" data-attdate="{{ $atty['att_for_date'] . '-' . $atty['att_for_month'] . '-' . $atty['att_for_year'] }}" data-checkinsource="{{ $atty['att_check_in_source'] }}" data-checkoutsource="{{ $atty['att_check_out_source'] }}" data-att_request_approval="{{ $atty['att_request_approval'] }}" style="cursor: pointer;"></i>
																
																@elseif($atty['att_check_overall_status'] == 'Early out')
																	<i class="fa-solid fa-clock {{$atty['att_remote_applicable'] == 1?'text-warning':'text-info'}} {{$update_attendance}}"
																		data-bs-toggle="tooltip"
																		data-bs-placement="top"
																		data-bs-title="Check In Time : {{ $atty['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $atty['att_check_out_time'] }}  &nbsp; &nbsp; Work Hours : {{ $calculate_working_hour }}"
																		data-attendance_id="{{ $atty['attendance_id'] }}"
																		data-intime="{{ $atty['att_check_in_time'] }}"
																		data-outtime="{{ $atty['att_check_out_time'] }}"
																		data-attstatus="{{ $atty['att_check_overall_status'] }}" data-workhours="{{ $calculate_working_hour }}" data-attdate="{{ $atty['att_for_date'] . '-' . $atty['att_for_month'] . '-' . $atty['att_for_year'] }}" data-checkinsource="{{ $atty['att_check_in_source'] }}" data-checkoutsource="{{ $atty['att_check_out_source'] }}" data-att_request_approval="{{ $atty['att_request_approval'] }}" style="cursor: pointer;"></i>
                                                                @elseif($atty['att_check_overall_status'] == 'RPL')
                                                                        <i class="fa-solid fa-calendar-check {{$update_attendance}}" style="color: maroon"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        data-bs-title="{{$atty['att_check_overall_status']}}"
                                                                        data-attendance_id="{{ $atty['attendance_id'] }}"
                                                                        data-intime="{{ $atty['att_check_in_time'] }}"
                                                                        data-outtime="{{ $atty['att_check_out_time'] }}"
                                                                        data-attstatus="{{ $atty['att_check_overall_status'] }}" data-workhours="{{ $calculate_working_hour }}" data-attdate="{{ $atty['att_for_date'] . '-' . $atty['att_for_month'] . '-' . $atty['att_for_year'] }}" data-checkinsource="{{ $atty['att_check_in_source'] }}" data-checkoutsource="{{ $atty['att_check_out_source'] }}" data-att_request_approval="{{ $atty['att_request_approval'] }}" style="cursor: pointer;"></i>
																@elseif(substr($atty['att_check_overall_status'] , 0, 7) == 'Holiday')

																	<i class="fa-solid fa-umbrella-beach text-warning {{$update_attendance}}"
																		data-bs-toggle="tooltip"
																		data-bs-placement="top"
																		data-bs-title="{{ $atty['att_check_overall_status'] }}"
																		data-attendance_id="{{ $atty['attendance_id'] }}"
																		data-intime="{{ $atty['att_check_in_time'] }}"
																		data-outtime="{{ $atty['att_check_out_time'] }}"
																		data-attstatus="Present" style="cursor: pointer;"></i>
																	
																@endif
																<span class="d-none">
																	@if(substr($atty['att_check_overall_status'] , 0, 7) == 'Holiday' || in_array($atty['att_check_overall_status'],$manage_leaves) ) 
																	{{ $atty['att_check_overall_status'] }}
																	@else
																	CI: {{ $atty['att_check_in_time'] }} | CO: {{ $atty['att_check_out_time'] }} | WH: {{ $calculate_working_hour }} | {{ $atty['att_check_overall_status'] }}
																	@endif
																</span>
														@endforeach
														@php
															
															$date_filter = strtotime(date("Y-m-d", strtotime($year . "-" . $month . "-" . $month_count )));
															$name_of_day = date('l' , $date_filter);
															$today = strtotime(date("Y-m-d"));
														@endphp
														
														@if($absent )
										
                                                            @if(in_array($name_of_day,$all_attendance['employee_offdays']))
																<i class="fa-solid fa-sun text-warning {{$update_attendance}}"
																data-bs-toggle="tooltip"
																data-bs-placement="top" data-bs-title="Off Day"
																data-attendance_id="NA"
																data-intime="00:00:00"
																data-outtime="00:00:00"
																data-employeeId="{{$all_attendance['employee_id']}}"                                                                       
																data-date= "{{$month_count}}"
																data-month = "{{$month}}"
																data-year = "{{$year}}"
																data-attstatus="Off Day" style="cursor: pointer;"></i>
																
																<span class="d-none">
																   Off Day
																</span>

															@elseif($date_filter < $today)
																@php $absent_count++; 
                                                                @endphp
																<i class="fa-solid fa-xmark text-danger {{$update_attendance}}"
																data-bs-toggle="tooltip"
																data-bs-placement="top" data-bs-title="Absent"
																data-attendance_id="NA"
																data-intime="00:00:00"
																data-outtime="00:00:00"
																data-employeeId="{{$all_attendance['employee_id']}}"                                                                       
																data-date= "{{$month_count}}"
																data-month = "{{$month}}"
																data-year = "{{$year}}"
																data-attstatus="Absent" style="cursor: pointer;"></i>
																
																<span class="d-none">
																	Absent
																 </span>
															@endif
														@endif
														   
													</td>
													

													
												@endfor
                                                    @php 
                                                    $total_shift_calculation =  $calculate_absent_shift + $total_working_hours_actual;
                                                    $total_working_hours_actual = CustomHelper::calculate_total_overtime($total_shift_calculation); @endphp
													<td>{{ $present_count }}</td>
													<td>{{ $absent_count }}</td>
													<td>{{ $halfday_count }}</td>
													<td>{{ $late_count }}</td>
													<td>{{ $leave_count }}</td>
                                                    <td 
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-bs-title="Actual Hours : 
                                                    {{$total_working_hours_actual['overtime']}}
                                                    ">
                                                    @php $total_working_hours = CustomHelper::calculate_total_overtime($total_working_hours); @endphp
                                                    {{$total_working_hours['overtime'] }}
                                                   
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('attendance/attendance-report?employee_id='.$all_attendance['employee_id'].'&month='.$month.'&year='.$year) }}" target="blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Download Attendance">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) )
                                                            <a href="{{ url('attendance/attendance-calender?employee_id='.$all_attendance['employee_id'].'&month='.$month.'&year='.$year) }}" target="blank" data-bs-toggle="tooltip" data-bs-placement="top" title="View Calender">
                                                                <span class="svg-icon svg-icon-3">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                        @endif
                                                        
                                                    </td>
                                                   
											</tr>
										@endforeach
									@endif
								</tbody>
								<tfoot>
									<tr>
										<th>Employee</th>
										@for ($month_count = 1; $month_count <= $month_days; ++$month_count)
											@php
											
                                            $date = '' . $month_count . '-' . $month . '-' . $year . '';
											
											@endphp
                                            {{-- {{ date('D', strtotime($date)); }} --}}
											<th>{{ $month_count }}</th>
										@endfor
                                        <th><i class="fa-solid fa-calendar-check text-success"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-title="Total On Time"
                                            ></i></th>
                                        <th><i class="fa-solid fa-xmark text-danger" 
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-title="Total Absent"

                                            ></i></th>
                                        <th><i class="fa-solid fa-calendar-check text-danger"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-title="Total Half Day"
                                            ></i></th>
                                        <th><i class="fa-solid fa-calendar-check text-gray"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-title="Total Late Coming"
                                            ></i></th>
                                        <th><i class="fa-solid fa-user-large-slash text-info"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-title="Total Leaves" 
                                            ></i></th>
                                        <th><i class="fa-solid fa-clock text-success" 
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-title="Total Work Hours" 
                                             ></i></th>
									</tr>
								</tfoot>	
                             
                            
                            </table>
                        </div>
                        @include('include.server-pagination', ['paginator' => $pagination ?? null])
                    </div>  
                </div>
                 
            </div>
            
           
        </div>
       
        
    </div>
    
    {{-- Check Sign Modal Starts Here --}}
        <div class="modal fade" id="modal_signs" tabindex="-1" aria-hidden="true">
            
            <div class="modal-dialog modal-dialog-scrollable">
                
            
                    
                
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Attendance Symbols</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            
                            
                            <div class="modal-body pt-0">
                                <table class="table table-bordered">
                                    <thead>
                                        <th class="text-center">Symbol</th>
                                        <th class="text-center">Description</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-calendar-check text-success"></i></td>
                                            <td class="text-center">On Time</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-calendar-check text-gray"></i></td>
                                            <td class="text-center">Late Coming</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-calendar-check text-danger"></i></td>
                                            <td class="text-center">Half Day</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-clock text-info"></i></td>
                                            <td class="text-center">Early Out</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-xmark text-danger"></i></td>
                                            <td class="text-center">Absent</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-user-large-slash text-info"></i></td>
                                            <td class="text-center">All Leaves</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-sun text-warning"></i></td>
                                            <td class="text-center">Off Day</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-umbrella-beach text-warning"></i></td>
                                            <td class="text-center">Holiday</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i class="fa-solid fa-calendar-check text-warning"></i></td>
                                            <td class="text-center">Remote Work</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><i  style="color: maroon" class="fa-solid fa-calendar-check"></i></td>
                                            <td class="text-center">RPL</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> 
                        </div>

            </div>
            
        </div>
    {{-- Check Sign Modal Ends Here --}}

    {{-- Mark Attendance Modal Starts Here --}}
    @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || (!empty($attendance_access) && $attendance_access[0]['attendance_access'] == 1))
        <form id="update_attendance" class="form">
            @csrf
            <div class="modal fade" id="modal-default2" tabindex="-1" aria-hidden="true">
                
                <div class="modal-dialog modal-dialog-scrollable">
                    
                    <div class="modal-content rounded">
                        
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Mark Attendance</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        
                        <div class="modal-body">
                            
                            <div class="only_date_input col-md-12 mb-8 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Attendance Date</label>
                                <div class="position-relative d-flex align-items-center">
                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                            <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                            <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <input type="date" class="form-control  ps-12" name="attendance_date"  max="{{date('Y-m-d')}}" required/>
                                
                                </div>
                            </div>

                            <div class="d-flex flex-column mb-8 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Select Employee</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Please select employee name attendance mark ."></i>
                                </label>
                                            
                                <div>
                                    <select class="form-select select" data-kt-select2="true" data-placeholder="Select Employee Name" name="employee_code" required data-employee-select data-scope="team">
                                    
                                    <option value="">Select Employee</option>
                                    @include('include.employee-selected-options', ['selected' => null])
                                    </select>
                                </div>
                            
                            </div>

                            <div class="d-flex flex-column mb-8 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Please Mark My Attendance</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Please select the option below which you are willing to mark your attendance."></i>
                                </label>
                                <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Attendance type"  name="attendance_status" required> 
                                    <option value="" >Select Attendance Type</option>
                                    <option value="Present">As Present</option>
                                    <option value="Half day">As Half Day</option>
                                    <option value="Late Coming">Late Coming</option>
                                    
                                </select>
                            </div>
                            
                        </div>
                        
                        <div class="modal-footer justify-content-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="mark_attendance_btn" class="btn btn-primary">
                                <span class="indicator-label">Mark Attendance</span>
                                <span class="indicator-progress">Please wait 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
   
    {{-- Mark Attendance Modal Ends Here --}}

    {{--Edit Modal Starts Here --}}
        <form id="update_attendance2" class="form">
            @csrf
            <div class="modal fade" id="edit_modal" tabindex="-1" aria-hidden="true">
                
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    
                    <div class="modal-content rounded">
                            
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Edit Attendance</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                
                        <div class="modal-body">
                            <div class="row d-none">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Attendance ID<span class="text-danger"> *</span></label>
                                        <input type="text" class="form-control" name="attenadnce_id" placeholder="Enter Attendance Id" required id="attendance_date">
                        
                                        <input type="text" class="form-control" name="att_employee_id" placeholder="Enter Employee Id"  id="att_employee_id">
                        
                                        <input type="text" class="form-control" name="att_for_date" placeholder="Enter Attendance Id"  id="att_for_date">
                        
                                        <input type="text" class="form-control" name="att_for_month" placeholder="Enter Attendance Id"  id="att_for_month">
                        
                                        <input type="text" class="form-control" name="att_for_year" placeholder="Enter Attendance Id" id="att_for_year">
                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-8">
                                    <label class="required fs-6 fw-semibold mb-2">In Time</label>
                                    <input type="time" step="1" class="form-control" name="check_in_time"
                                        placeholder="Enter in Time" required id="intime">
                                </div>
                            
                                <div class="col-md-6 mb-8">
                                    <label class="required fs-6 fw-semibold mb-2">Out Time</label>
                                    <input type="time" step="1" class="form-control" name="check_out_time" placeholder="Enter out Time" required id="outtime">
                                </div>
                            
                                <div class="d-flex flex-column mb-8 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">Mark Attendance Status</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Please select the option below which you are willing to mark employee attendance."></i>
                                    </label>
                                    <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Attendance Status"  name="attendance_status" id="attendance_status" required> 
                                        <option value="" >Select Status</option>
                                        <option value="Present">On Time</option>
                                        <option value="Late Coming">Late Coming</option>
                                        <option value="Half day">Half Day</option>
                                        <option value="Early out">Early out</option>
                                        <option value="Absent">Absent</option>
                                        @if(!empty($manage_leaves))
                                            @foreach ($manage_leaves as $manage_leave)
                                                <option value="{{$manage_leave['leave_name']}}">{{$manage_leave['leave_name']}}</option>
                                            @endforeach
                                        @endif
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-8">
                                    <table class="table-bordered w-100 text-center">
                                        <thead>
                                            <th>Attendance Date</th>
                                            <th>Check In Source</th>
                                            <th>Check Out Source</th>
                                            <th>Leave / Deputation</th>
                                            <th>Hours Worked</th>
                                            <th>Status</th>
                                        </thead>
                                        <tbody>
                                            <td id="attendance_date_show"></td>
                                            <td id="check_in_source_show"></td>
                                            <td id="check_out_source_show"></td>
                                            <td id="attendance_deputation_show"></td>
                                            <td id="hours_worked_show"></td>
                                            <td id="status_show"></td>
                                        </tbody>
                                   </table>
                                </div>
                            </div>
                    
                        </div>
                        
                        <div class="modal-footer justify-content-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="edit_attendance_btn" class="btn btn-primary">
                                <span class="indicator-label">Update Attendance</span>
                                <span class="indicator-progress">Please wait 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>

                    </div>
                </div> 
            </div>
        </form>
    @endif
    @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null))
        <form class="form" id="att_share_report">
            @csrf	
            <div class="modal fade" id="att-share-report-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-650px">
                    <div class="modal-content rounded">
                       
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Share Attendance Report </h4>
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
    
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold required">Select Month</label>
                                                    
                                    <div>
                                        <select class="form-select select7" data-kt-select2="true" data-placeholder="Select Month" name="attendance_month" required="required">
                                            <option value="">Attendance Month</option>
                                            <option value="01" @if (!empty($month) && $month == '01') selected  @elseif(empty($month) && date('m') == '01') selected  @endif>January
                                            </option>
                                            <option value="02" @if (!empty($month) && $month == '02') selected @elseif(empty($month) && date('m') == '02') selected  @endif>February
                                            </option>
                                            <option value="03" @if (!empty($month) && $month == '03') selected @elseif(empty($month) && date('m') == '03') selected  @endif>March
                                            </option>
                                            <option value="04" @if (!empty($month) && $month == '04') selected @elseif(empty($month) && date('m') == '04') selected @endif>April
                                            </option>
                                            <option value="05" @if (!empty($month) && $month == '05') selected @elseif(empty($month) && date('m') == '05') selected @endif>May
                                            </option>
                                            <option value="06" @if (!empty($month) && $month == '06') selected @elseif(empty($month) && date('m') == '06') selected @endif>June
                                            </option>
                                            <option value="07" @if (!empty($month) && $month == '07') selected @elseif(empty($month) && date('m') == '07') selected  @endif>July
                                            </option>
                                            <option value="08" @if (!empty($month) && $month == '08') selected @elseif(empty($month) && date('m') == '08') selected  @endif>August
                                            </option>
                                            <option value="09" @if (!empty($month) && $month == '09') selected @elseif(empty($month) && date('m') == '09') selected  @endif>September
                                            </option>
                                            <option value="10" @if (!empty($month) && $month == '10') selected  @elseif(empty($month) && date('m') == '10') selected @endif>October
                                            </option>
                                            <option value="11" @if (!empty($month) && $month == '11') selected  @elseif(empty($month) && date('m') == '11') selected @endif>November
                                            </option>
                                            <option value="12" @if (!empty($month) && $month == '12') selected @elseif(empty($month) && date('m') == '12') selected @endif>December
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold required">Select Year</label>
                                                    
                                    <div>
                                        <select class="form-select select7" data-kt-select2="true" data-placeholder="Select Year"  name="attendance_year" required="required">
                                            <option value="">Attendance Year</option>
                                            @if(!empty($dynamic_years))
                                                @foreach ($dynamic_years as $year_list)
                                                    <option value="{{$year_list}}" @if (!empty($year) && $year == $year_list) selected @elseif(empty($year) && date('Y') == $year_list) selected  @endif>{{$year_list}}
                                                    </option>
                                                @endforeach
                                            @endif
                                       
                                        </select>
                                    </div>
                                </div>

                                <div class ="row" style="margin-top: 5px;">
                                    <div class="col-md-10 mb-8 mt-3 fv-row" id="selectedepartment">
                                        <label class="fs-6 fw-semibold mb-2 required">Department</label>

                                            <select class="form-select select7 " id="departments_all" data-control="select2" multiple="multiple" data-hide-search="true" data-placeholder="Select Department" name="emp_depart[]" data-close-on-select="false" required>

                                                @php $count = 0 @endphp
                                                @if(!empty($all_department))
                                                    @foreach ($all_department as $department)
                                                    @php $count ++; @endphp
                                                        <option value="{{$department['department_id']}}">
                                                            {{ $department['department_name'] }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                    </div>
                                    <div class="col-md-2 mb-8 mt-3 fv-row">

                                        <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                            <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2">
                                                <input type = "hidden" name="count" value="{{$count}}">
                                                <input class="form-check-input h-20px w-20px" type="checkbox" id="department_all" name="select_all" value="Select All" />

                                            </label>
                                    </div>

                                </div>

                                <div class="row" style="margin-top: 5px;">
                                    <div class="col-md-12 fv-row" id="selectedemployees">
                                        <label class="form-label fw-semibold">Share With Employee</label>
                                                        
                                        <div>
                                            {{-- searched on the server within the picked departments; left empty it means everyone in them --}}
                                            <select class="form-select " id="select_all_employee" data-kt-select2="true"  data-close-on-select="false" multiple="true"   name="emp_names[]" data-employee-select data-scope="active" data-departments-from="#departments_all" data-placeholder="All employees of the selected departments">
                                            </select>
                                        </div>
                                    </div>
                               
                                </div>

                
                            </div>    
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit_btn" class="btn btn-primary">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif

</div>

@endsection



@push('js-link')

<script src="{{url('assets/js/attendance/all-employees-attendance.js')}}?v=3"></script>

<script>
    var length_count = {{ $count ?? 0 }};

</script>
    
@endpush