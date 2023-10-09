@extends('layout.main')
@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    @if (empty(Request::get('d')))
                        <h1>Daily Attendance Report ({{ date('d') .  '-' . date('F') . '-' . date('Y') }})</h1>
                    @else
                        <h1>Daily Attendance Report ({{  date('d-F-Y', strtotime(Request::get('d'))) }})</h1>
                    @endif
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                <div class="m-0">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#attendance_information_upload">
                         <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>										
                        Attendance Import
                    </a>
                </div>
                @if(!empty($attendance))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">											
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Daily Attendance Report
                        </a>  
                    </div>
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
                            <div class="text-gray-900 fw-bold fs-2 " id="HalfDay">0</div>
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
                            <div class="text-gray-900 fw-bold fs-2 " id="offdays">0</div>
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
                                                <option value="1" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '1') selected @endif>Active
                                                    Employees</option>
                                                <option value="2" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '2') selected @endif>De-active
                                                    Employees</option>
                                                <option value="3" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '3') selected @endif>All
                                                        Employees</option>
                                            </select>
                                            </div>
                                            
                                        </div>

                                        <div class="mb-10">
                                            
                                            <label class="form-label fw-semibold">Select Date:</label>
                                            
                                            <div class="position-relative d-flex align-items-center">
                                            
                                                    
                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                                        <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                                        <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                {{--end::Svg Icon--}}
                                                {{--end::Icon--}}
                                                {{--begin::Datepicker--}}
                                                <input type="date" class="form-control  ps-12 " placeholder="Select a date" name="d"  @if(!empty(Request::get('d'))) value="{{ Request::get('d') }}" @else value="{{ date('Y-m-d') }}"  @endif  max="{{ date('Y-m-d') }}" required/>
                                                {{--end::Datepicker--}}
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
                                            <th  class="ps-8">Employee Code & Name</th>
                                            <th class="text-center">Depart &amp; Desig</th>
                                            <th class="text-center">Check In Time </th>
                                            <th class="text-center">Check Out Time</th>
                                            <th class="text-center">Hours Worked</th>
                                            <th  class="text-center">Status</th>
                                            
                                        
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
                                                @php $attendances['employee_offdays'] = (!empty($attendances['employee_offdays'])) ? explode(",",$attendances['employee_offdays']):[]; @endphp
                                                @php $absent = true;@endphp

                                                <tr>
                                                    <td class="ps-8">{{ $attendances['employee_code'] }} - {{ $attendances['employee_name'] }} </td>
                                                    <td class="text-center">
                                                    @if($attendances['department_name'] != '')
                                                        <p class="text-dark fw-bold mb-1 fs-6"> {{ $attendances['department_name'] }}</p>
                                                        <p class="text-muted fw-semibold text-muted fs-7">{{$attendances['designation_name'] }}</p>
                                                    @else
                                                        Not Available
                                                    @endif
                                                    
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
                                                            @if(in_array($name_of_day,$attendances['employee_offdays']) )
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
                                                            @if(in_array($name_of_day,$attendances['employee_offdays']) )
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
                                                            @if(in_array($name_of_day,$attendances['employee_offdays']) )
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
                                                            @if(in_array($name_of_day,$attendances['employee_offdays']) )
                                                                @php $offdays++; @endphp
                                                                <p class="text-warning">
                                                                <i class="fa-solid fa-sun text-warning marked-attendance"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="top" data-bs-title="Off Day"></i> -- Off Day</p>

                                                            @elseif($date_filter < $today)
                                                                @php $absent_count++; @endphp
                                                                <p class="text-danger">
                                                                    <i class="fa-solid fa-xmark text-danger marked-attendance"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Absent"></i> -- Absent</p>
                                                        
                                                            @elseif($date_filter == $today)
                                                                @php $absent_count++; @endphp
                                                                <p class="text-danger">
                                                                    <i class="fa-solid fa-xmark text-danger marked-attendance"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Absent"></i> -- Absent</p>
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