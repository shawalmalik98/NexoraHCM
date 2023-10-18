@extends('layout.main')
@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    @if (empty(Request::get('d')))
                        <h1>Daily Attendance Report ({{ date('d') .  '-' . date('M') . '-' . date('Y') }})</h1>
                    @else
                        <h1>Daily Attendance Report ({{  date('d-M-Y', strtotime(Request::get('d'))) }})</h1>
                    @endif
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#attendance_dat_download">
                         <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>										
                        Attendance Export (.DAT)
                    </a>
                </div>
                {{-- <div class="m-0">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#attendance_information_upload">
                         <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>										
                        Attendance Import
                    </a>
                </div> --}}
                
                @if(!empty($attendance))
                    @if(!empty(Request::get('d')))
                        <div class="m-0">
                            <a href="{{ url('daily-attendance-report/download-daily-attendance?d='.Request::get('d')) }}" class="btn btn-sm btn-success" method="GET">											
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Download Attendance Report ({{  date('d-M-Y', strtotime(Request::get('d'))) }})
                            </a>  
                        </div>
                    @else
                        <div class="m-0">
                            <a href="{{ url('daily-attendance-report/download-daily-attendance') }}" class="btn btn-sm btn-success" method="GET">											
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Download Attendance Report ({{ date('d') .  '-' . date('M') . '-' . date('Y') }})
                            </a>  
                        </div>
                    @endif    
                @endif 
            
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            <div class="row" id="card-container">
                    
                <div class="col-md-2 col-6 filter-record mb-4" data-value="On Time"  style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="present">0</div>
                            <div class="fw-semibold text-gray-400">On Time</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6  filter-record mb-4" data-value='Late Coming,Early out'   style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="lateComing">0</div>
                            <div class="fw-semibold text-gray-400">Late/Early</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6  filter-record mb-4" data-value="Half Day"  style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="HalfDay">0</div>
                            <div class="fw-semibold text-gray-400">Half Day</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6  filter-record mb-4" data-value="Absent"  style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="absent">0</div>
                            <div class="fw-semibold text-gray-400">Absent </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6 filter-record mb-4" data-value="Off Day,Holiday"  style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="offdays">0</div>
                            <div class="fw-semibold text-gray-400">OffDay/Holiday</div>
                        </div>
                    </div>
                </div>    

                <div class="col-md-2 col-6 filter-record mb-4" data-value="{{$leave_name}}"  style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="Leaves">{{$t_employee_leaves??''}}</div>
                            <div class="fw-semibold text-gray-400 ">Leaves</div>
                        </div>
                    </div>
                </div>

            </div>

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
                    <div class="card-toolbar" >
                        @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('employee_location')) ? $filter : '' }} {{ !empty(Request::get('is_active')) ? $filter : '' }} {{ !empty(Request::get('d')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                       
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('daily-attendance-report') }}" method="GET">
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
                                                <select class="form-select " data-kt-select2="true" data-placeholder="Select Name" name="is_active">
                                                <option value="1" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '1') selected @endif>Active Employees</option>
                                                <option value="2" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '2') selected @endif>De-active Employees</option>
                                                <option value="3" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '3') selected @endif>All Employees</option>
                                            </select>
                                            </div>
                                            
                                        </div>

                                        <div class="mb-10">
                                            
                                            <label class="form-label fw-semibold">Select Date:</label>
                                            
                                            <div class="position-relative d-flex align-items-center">
                                                <input type="date" class="form-control" placeholder="Select a date" name="d"  @if(!empty(Request::get('d'))) value="{{ Request::get('d') }}" @else value="{{ date('Y-m-d') }}"  @endif  max="{{ date('Y-m-d') }}" required/>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('employee_location')) || !empty(Request::get('d')) || !empty(Request::get('is_active')))
                                                <a href="{{ url('daily-attendance-report') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">Reset Filter</a>
                                            @endif
                                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                            <button type="submit" class="btn btn-sm btn-primary" >Apply</button>
                                        </div>
                                        
                                    </div>
                                 </form>
                            </div>
                       
                    </div>
                </div>
                    <div class="card-body py-3">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive popup-visible ">
                                <table class="table table-bordered align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="tableDailyAttendance">
                                                                    
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-8">Employee Code & Name</th>
                                            <th class="text-center">Depart &amp; Desig</th>
                                            <th class="text-center">Business Unit </th>
                                            <th class="text-center">Check In Time </th>
                                            <th class="text-center">Check Out Time</th>
                                            <th class="text-center">Hours Worked</th>
                                            <th class="text-center">Status</th>
                                            
                                        
                                        </tr>
                                    </thead>
                                  
                                    <tbody>
                                        @php 
                                            $present = 0;
                                            $halfday = 0;
                                            $late_coming = 0;
                                            $early_out = 0;
                                            $offdays = 0;
                                            $absent_count = 0;
                                            $holiday_count = 0;
                                            if(!empty(Request::get('d'))){
                                                $d = date('d', strtotime(Request::get('d')));
                                                $m = date('m', strtotime(Request::get('d')));
                                                $y = date('Y', strtotime(Request::get('d')));
                                                $current_date = $d . '-' .  $m . '-' . $y;
 
                                            }else{
                                                $current_date = date('d') . '-' . date('m') . '-' . date('Y');
                                            }
     
                                            $name_of_day = date("l", strtotime($current_date ));	

                                           
                                        @endphp
                                        @if (!empty($attendance))
                                        
                                            @foreach ($attendance as $attendances)
                                                @php $attendances['Employee Offdays'] = (!empty($attendances['Employee Offdays'])) ? explode(",",$attendances['Employee Offdays']):[]; @endphp
                                                @php $absent = true;@endphp

                                                <tr>
                                                    <td class="ps-8">{{ $attendances['Employee Code'] }} - {{ $attendances['Employee Name'] }} </td>
                                                    <td class="text-center">
                                                        @if($attendances['Department Name'] != '')
                                                            <p class="text-dark fw-bold mb-1 fs-6"> {{ $attendances['Department Name'] }}</p>
                                                            <p class="text-muted fw-semibold text-muted fs-7">{{$attendances['Designation Name'] }}</p>
                                                        @else
                                                            Not Available
                                                        @endif
                                                    
                                                    </td>
                                                    <td class="text-center"> 
                                                        {{ $attendances['Business Unit'] ?? '-' }}
                                                    </td>

                                                    <td  class="text-center"> 
                                                    
                                                        @foreach ($attendances['attendance'] as $daily_atty)
 
                                                            @php $absent = false; @endphp
                                                        
                                                            @if ($daily_atty['att_check_overall_status'] == 'Present')
                                                                {{ $daily_atty['att_check_in_time'] }}
                                                            @elseif($daily_atty['att_check_overall_status'] == 'Half day')
                                                                {{ $daily_atty['att_check_in_time'] }}
                                                            @elseif($daily_atty['att_check_overall_status'] == 'Late Coming')
                                                                {{ $daily_atty['att_check_in_time'] }}
                                                            @elseif(in_array($daily_atty['att_check_overall_status'],$manage_leaves) || $daily_atty['att_check_overall_status'] == 'RPL')
                                                                    -      
                                                            @elseif($daily_atty['att_check_overall_status'] == 'Absent')
                                                                    -
                                                            @elseif($daily_atty['att_check_overall_status'] == 'Early out')
                                                             {{ $daily_atty['att_check_in_time'] }}
                                                            @elseif(substr($daily_atty['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                    -
                                                            @endif
                                                        
                                                        @endforeach

                                                        @php
                                                    
                                                        $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                                        $today = strtotime(date("Y-m-d"));
                                                        @endphp
                                                        
                                                        @if($absent )
                                                            @if(in_array($name_of_day,$attendances['Employee Offdays']) )
                                                            00:00:00
                                                            @elseif($date_filter< $today)
                                                                -
                                                            @else
                                                                - 
                                                            @endif
                                                        @endif
                                                    
                                                    </td>

                                                    <td  class="text-center"> 
                                                    
                                                        @foreach ($attendances['attendance'] as $today_atty)
                                                
                                                            @php $absent = false; @endphp

                                                            @if ($today_atty['att_check_out_status'] == 0)
                                                                -
                                                            @elseif ($today_atty['att_check_overall_status'] == 'Present')
                                                                {{ $today_atty['att_check_out_time']}}
                                                            @elseif($today_atty['att_check_overall_status'] == 'Half day')
                                                                {{ $today_atty['att_check_out_time'] }}
                                                            @elseif($today_atty['att_check_overall_status'] == 'Late Coming')
                                                                {{ $today_atty['att_check_out_time'] }}
                                                            @elseif(in_array($today_atty['att_check_overall_status'],$manage_leaves) || $today_atty['att_check_overall_status'] == 'RPL')
                                                                    - 
                                                            @elseif($today_atty['att_check_overall_status'] == 'Absent')
                                                                    -
                                                            @elseif($today_atty['att_check_overall_status'] == 'Early out')
                                                                {{ $today_atty['att_check_out_time'] }}
                                                            @elseif(substr($today_atty['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                -
                                                            @endif
                                                            
                                                        @endforeach
                                                    
                                                        @php
                                                            $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                                            $today = strtotime(date("Y-m-d"));
                                                        @endphp
                                                        
                                                        @if($absent )
                                                            @if(in_array($name_of_day,$attendances['Employee Offdays']) )
                                                                00:00:00
                                                            @elseif($date_filter< $today)
                                                                - 
                                                            @else
                                                                - 
                                                            @endif
                                                        @endif
                                                    
                                                    </td> 

                                                    <td class="text-center">
                                                    
                                                        @foreach ($attendances['attendance'] as $atty_hours)
                                            
                                                            @php $absent = false; @endphp
                                                            @if ($atty_hours['att_check_overall_status'] == 'Present')
                                                                {{ CustomHelper::calculate_working_hour($atty_hours['att_check_out_status'],$atty_hours['att_check_in_time'],$atty_hours['att_check_out_time'],$atty_hours['att_created_at'],$atty_hours['att_updated_at'])}}
                                                            @elseif($atty_hours['att_check_overall_status'] == 'Half day')
                                                                {{ CustomHelper::calculate_working_hour($atty_hours['att_check_out_status'],$atty_hours['att_check_in_time'],$atty_hours['att_check_out_time'],$atty_hours['att_created_at'],$atty_hours['att_updated_at'])}}
                                                            @elseif($atty_hours['att_check_overall_status'] == 'Late Coming')
                                                                {{ CustomHelper::calculate_working_hour($atty_hours['att_check_out_status'],$atty_hours['att_check_in_time'],$atty_hours['att_check_out_time'],$atty_hours['att_created_at'],$atty_hours['att_updated_at'])}}
                                                            @elseif(in_array($atty_hours['att_check_overall_status'],$manage_leaves) || $atty_hours['att_check_overall_status'] == 'RPL')
                                                                -
                                                            @elseif($atty_hours['att_check_overall_status'] == 'Absent')
                                                                 -
                                                            @elseif($atty_hours['att_check_overall_status'] == 'Early out')
                                                            {{ CustomHelper::calculate_working_hour($atty_hours['att_check_out_status'],$atty_hours['att_check_in_time'],$atty_hours['att_check_out_time'],$atty_hours['att_created_at'],$atty_hours['att_updated_at'])}}
                                                            @elseif(substr($atty_hours['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                -
                                                            @endif
                                                        
                                                        @endforeach

                                                        @php
                                                    
                                                        $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                                        $today = strtotime(date("Y-m-d"));
                                                        @endphp
                                                        
                                                        @if($absent )
                                                            @if(in_array($name_of_day,$attendances['Employee Offdays']) )
                                                                00:00:00
                                                            @elseif($date_filter< $today)
                                                                -
                                                            @else
                                                                -
                                                            @endif
                                                        @endif

                                                    </td>

                                                    <td class="text-center"> 
                                                        @foreach ($attendances['attendance'] as $atty )
                                                            
                                                            @if ($atty['att_for_date'] !== '' )
                                                                @php $absent = false;@endphp
                                                                @if ($atty['att_check_overall_status'] == 'Present')
                                                                    @php $present++; @endphp
                                                                    <p class="text-success"><i class="fa-solid fa-calendar-check text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $atty['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $atty['att_check_out_time'] }}"></i> -- On Time</p>
                                                                @elseif($atty['att_check_overall_status'] == 'Half day')
                                                                    @php $halfday++; @endphp
                                                                    <p class="text-danger"><i class="fa-solid fa-calendar-check text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $atty['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $atty['att_check_out_time'] }}"></i> -- Half Day</p>
                                                                @elseif($atty['att_check_overall_status'] == 'Late Coming')
                                                                    @php $late_coming++; @endphp
                                                                    <p class="text-muted"><i class="fa-solid fa-calendar-check text-muted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $atty['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $atty['att_check_out_time'] }}"></i> -- Late Coming</p>
                                                                @elseif($atty['att_check_overall_status'] == 'RPL')
                                                                    <p style="color: maroon"><i  style="color: maroon" class="fa-solid fa-calendar-check" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $atty['att_check_overall_status'] }}"></i> -- RPL</p>
                                                                @elseif(in_array($atty['att_check_overall_status'],$manage_leaves))
                                                                    <p class="text-info"><i class="fa-solid fa-user-large-slash" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{$atty['att_check_overall_status']}}"></i> -- {{$atty['att_check_overall_status']}}</p>
                                                                @elseif($atty['att_check_overall_status'] == 'Absent')
                                                                    <p class="text-danger"><i class="fa-solid fa-xmark text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Absent"></i> -- Absent</p>
                                                                @elseif($atty['att_check_overall_status'] == 'Early out')
                                                                    @php $early_out++; @endphp
                                                                    <p class="text-info"><i class="fa-solid fa-clock text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $atty['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $atty['att_check_out_time'] }}"></i> -- Early out</p>
                                                                @elseif(substr($atty['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                    @php $holiday_count++; @endphp
                                                                    <p class="text-warning"><i class="fa-solid fa-umbrella-beach text-warning" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $atty['att_check_overall_status'] }}"></i> -- Holiday</p>
                                                                @endif
                                                            
                                                            

                                                            @endif
                                                        
                                                        @endforeach

                                                        @php
                                                            $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                                            $today = strtotime(date("Y-m-d"));
                                                        @endphp
                                                
                                                        @if($absent )
                                                            @if(in_array($name_of_day,$attendances['Employee Offdays']) )
                                                                @php $offdays++; @endphp
                                                                <p class="text-warning">
                                                                <i class="fa-solid fa-sun text-warning marked-attendance" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Off Day"></i> -- Off Day</p>

                                                            @elseif($date_filter < $today)
                                                                @php $absent_count++; @endphp
                                                                <p class="text-danger">
                                                                <i class="fa-solid fa-xmark text-danger marked-attendance" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Absent"></i> -- Absent</p>
                                                        
                                                            @elseif($date_filter == $today)
                                                                @php $absent_count++; @endphp
                                                                <p class="text-danger">
                                                                <i class="fa-solid fa-xmark text-danger marked-attendance" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Absent"></i> -- Absent</p>
                                                            @endif

                                                        
                                                        @endif
                                                    
                                                    
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

        <form class="form" id="downloadbutton" action="{{ url('daily-attendance-report/attendance-report-download') }}" method="POST">
            @csrf
            <div class="modal fade" id="download_modal_pdf"uploaddex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-650px">
                    <div class="modal-content rounded">
                    
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Download Daily Attendance Report</h4>
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

                                <input name="is_active" type="hidden" value="{{Request::get('is_active')}}">

                                <input name="employee_location" type="hidden" value="{{Request::get('employee_location')}}">

                                <input name="d" type="hidden" value="{{Request::get('d')}}">
                        
                                <div class="col-md-12 mt-3 fv-row">
                                    <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) daily attendance report,
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

            <form class="form" id="attendance_update">
                @csrf	
                <div class="modal fade" id="attendance_information_upload" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        <div class="modal-content rounded">
                        
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Update attendance Records</h4>
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
                                    <input type="hidden" name="Force_Approved">
                                    <div class="col-md-12 fv-row">
                                        <label class="fs-6 fw-semibold mb-2 required">File Attachment</label>
                                        <input type="file" class="form-control" accept=".dat" name="file">
                                        <p class="text-danger mt-1">Please <b>Download Format File</b> and add attendance(s) record. All the fields exist in file are required.</p>
                                        <p class="text-danger mt-0">Only DAT file is supported for attendance(s) upload process.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="update_btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <a href="/portal_assets/location_excel_document/LocationRecord.zip" id="download_btn" class="btn btn-success">Download Format</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form class="form" id="attendance_upload">
                @csrf	
                <div class="modal fade" id="attendance_records_upload" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        <div class="modal-content rounded">
                        
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Confirmation Alert !</h4>
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
                                    <input type="hidden" name="Force_Approved">
                                    <div id="attendance_record_msg">
                                    </div>
                    
                                </div>    
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button onclick="attendancenuploadclose()" type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button onclick="attendanceupload()" type="button"  class="btn btn-primary force_upload">
                                    <span class="indicator-label">Force Upload</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form class="form" id="attendance_record_download"  action="{{ url('daily-attendance-report/export-attendance-data') }}" method="POST">
                @csrf	
                <div class="modal fade" id="attendance_dat_download" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        <div class="modal-content rounded">
                        
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Download Attendance Records</h4>
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
                                <div class="row g-9 mb-8">
                                    <div class="col-md-4 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">From Date</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="date" class="form-control date-control"
                                                placeholder="Select a date" name="from_date" id="from_date"
                                                 max="{{ date('Y-m-d')}}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">To Date</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="date" class="form-control date-control"
                                                placeholder="Select a date" name="to_date" id="to_date" disabled/>
                                        </div>
                                    </div>

                                    <div class="col-md-4 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span>Total Days</span>
                                        </label>


                                        <input type="text" class="form-control" placeholder="Total Days"
                                            id="days" readonly />
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="attendance_download_btn" class="btn btn-primary">
                                    <span class="indicator-label">Download</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    
    </div>
</div>

@endsection



@push('js-link')
<script src="{{url('assets/js/attendance/daily-attendance-report.js')}}"></script>
<script>
var present_attendance = {{ $present }};
var absent_count_attendance = {{ $absent_count }};
var late_coming_attendance = {{ $late_coming }};
var early_out_attendance = {{ $early_out }};
var halfday_attendance = {{ $halfday }};
var total_offDays_holiday_attendance = {{ $offdays }} +  {{ $holiday_count }};
</script>

@endpush