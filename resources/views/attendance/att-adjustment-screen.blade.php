@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
       
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                 
                    @if (!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                      Attendance Adjustment ({{date('F Y',strtotime($year . '-' . $month . '-01')) }})
                    @else
                        
                        Attendance Adjustment ({{date('d-M', strtotime($from_date)) .' to ' .date('d-M-Y', strtotime($to_date))}})
                    @endif
                </h1>   
            </div>
            {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @if (!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                        <a href="{{ url('my-attendance-report?from_date='.Request::get('from_date').'&to_date='.Request::get('to_date'))}}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @else
                        <a href="{{ url('my-attendance-report?from_date='.Request::get('from_date').'&to_date='.Request::get('to_date')) }}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @endif
                  
                </div>
            </div> --}}
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container" >
            <form action="{{ url('attendance-adjustment') }}" method="GET">
                <div class="row" id="card-container">
                        
                    <div class="col-md-3 col-6 mb-4"  >
                        <div class="card" >
                            <div class="p-3">
                                <label class="form-label fw-semibold @if(!empty(Request::get('from_date'))) required  @endif" id="label-from-date">Form Date</label>
                                <input class="form-control" type="date" name="from_date" id="from_date" value="{{ $from_date}}" @if(!empty(Request::get('from_date'))) required  @endif max="{{date('Y-m-d')}}" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 mb-4"  >
                        <div class="card" >
                            <div class="p-3">
                                <label class="form-label fw-semibold @if(!empty(Request::get('to_date'))) required  @endif" id="label-to-date">To Date</label>
                                <input class="form-control" type="date" name="to_date" id="to_date" value="{{$to_date}}" @if(!empty(Request::get('to_date'))) required  @endif max="{{date('Y-m-d')}}"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 mb-4"  >
                        <div class="card" >
                            <div class="p-3">
                                        
                                <label class="form-label fw-semibold">
                                    <span>Select Employee</span>
                                </label>
                                <div>
                                    <select class="form-select" data-kt-select2="true" name="employee_id" data-placeholder="Select employees">
                                        <option value="">Select Employee</option>
                                        @if(!empty($all_employees))
                                            @foreach ($all_employees as $employees)
                                                <option value="{{ $employees['employee_id'] }}" @if (Request::get('employee_id') == $employees['employee_id']) selected @endif>{{$employees['employee_code']. ' - ' .$employees['employee_name']}} </option>
                                            @endforeach
                                        @endif                                                             
                                    </select>
                                    
                                </div>
                            </div>
                        </div>    
                    </div>

                    <div class="col-md-3 col-6 mb-4"  >
                        <div class="card" style="width:max-content !important; height: 6.9rem; justify-content: center;" >
                            <div class="p-4">

                                @if (!empty(Request::get('from_date')) || !empty(Request::get('to_date')) || !empty(Request::get('m')) || !empty(Request::get('y')))
                                    <a href="{{ url('attendance-adjustment') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2">Reset Filter</a>
                                @endif
                                <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                <button type="submit" class="btn btn-sm btn-primary" >Apply</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>    
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
                    {{-- <div class="card-toolbar" >
                         @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('m')) ? $filter : '' }} {{ !empty(Request::get('y')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                       
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('my-attendance') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                    </div>

                                    <div class="separator border-gray-200"></div>
                                    <div class="px-7 py-5">
                                    
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold @if(!empty(Request::get('from_date'))) required  @endif" id="label-from-date">Form Date</label>
                                            <input class="form-control" type="date" name="from_date" id="from_date" value="{{ $from_date}}" @if(!empty(Request::get('from_date'))) required  @endif max="{{date('Y-m-d')}}" />
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold @if(!empty(Request::get('to_date'))) required  @endif" id="label-to-date">To Date</label>
                                            <input class="form-control" type="date" name="to_date" id="to_date" value="{{$to_date}}" @if(!empty(Request::get('to_date'))) required  @endif max="{{date('Y-m-d')}}"/>
                                        </div>
                                       
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('from_date')) || !empty(Request::get('to_date')) || !empty(Request::get('m')) || !empty(Request::get('y')))
                                                <a href="{{ url('my-attendance') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2">Reset Filter</a>
                                             @endif
                                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                            <button type="submit" class="btn btn-sm btn-primary" >Apply</button>
                                        </div>
                                        
                                    </div>
                                 </form>
                            </div>
                       
                    </div> --}}
                </div>
                @if(Session::has('error'))
                    <script>
                         swal.fire("Error", "{{Session::get('error') }}", "error");
                    </script>
                @endif
                    <div class="card-body py-3">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive popup-visible ">
                                <table class="table table-bordered align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable">
                                                            
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-8">Date</th>
                                            <th class="text-center">Check In Time </th>
                                            <th class="text-center">Check Out Time</th>
                                            <th class="text-center">Shift Duration</th>
                                            <th class="text-center">Leave/Deputation</th>
                                            <th class="text-center">Att Source</th>
                                            <th class="text-center">Hours Worked</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $total_working_hours = 0;
                                        $total_working_hours_actual = 0;
                                        $in_day_time_actual = $out_day_time_actual = 0 ;
                                        $present = 0;
                                        $halfday = 0;
                                        $late_coming = 0;
                                        $early_out = 0;
                                        $offdays = 0;
                                        $absent_count = 0;
                                        $holiday_count = 0;

                                        $attendance_total_count = count($attendance);
                                        $login_user[0]['employee_offdays'] = (!empty($login_user[0]['employee_offdays'])) ? explode(",",$login_user[0]['employee_offdays']):[]; 
                                        @endphp
                                       
                                        @for ($month_count = $month_count; $month_count <= $month_days; $month_count++)
                                            @php $absent = true;
                                            $loop_count = 0;
                                            $helper_absent = false;

                                            @endphp
                                            <tr>
                                            
                                                @php
                                                    $current_date = $dates[$month_count];
                                                    $name_of_day = date("l", strtotime($current_date ));	
                                                    $name_of_day_short = date("D", strtotime($current_date ));	
                                                @endphp
                                                
                                                <td class="ps-8">
                                                    {{ $current_date }} - {{  $name_of_day_short }}
                                                </td>
                                                
                                                <td  class="text-center"> 
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                        
                                                            
                                                            @if ($attendances['att_for_date'].'-'.$attendances['att_for_month'].'-'.$attendances['att_for_year'] == $dates[$month_count])
                                                                
                                                                @php $absent = false; @endphp
                                                            
                                                                @if ($attendances['att_check_overall_status'] == 'Present')
                                                                    {{ $attendances['att_check_in_time'] }}
                                                                @elseif($attendances['att_check_overall_status'] == 'Half day')
                                                                    {{ $attendances['att_check_in_time'] }}
                                                                @elseif($attendances['att_check_overall_status'] == 'Late Coming')
                                                                    {{ $attendances['att_check_in_time'] }}
                                                                @elseif(in_array($attendances['att_check_overall_status'],$manage_leaves) || $attendances['att_check_overall_status'] == 'RPL')
                                                                        -       
                                                                @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                        -
                                                                @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                                {{ $attendances['att_check_in_time'] }}
                                                                @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                        -
                                                                @endif
                                                            @endif  
                                                        @endforeach
                                                    @endif

                                                    @php
                                                
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                    
                                                    @if($absent )
                                                    
                                                        @if(in_array($name_of_day,$login_user[0]['employee_offdays']) )
                                                            00:00:00
                                                        @elseif($date_filter< $today)
                                                            -
                                                        @else
                                                            -    
                                                        @endif
                                                    @endif
                                                
                                                </td> 

                                                <td  class="text-center"> 
                                                    @if (!empty($attendance))
                                                    
                                                        @foreach ($attendance as $attendances)

                                                            @if ($attendances['att_for_date'].'-'.$attendances['att_for_month'].'-'.$attendances['att_for_year'] == $dates[$month_count])
                                                                @php $absent = false; @endphp
                                                                @if($attendances['att_check_out_status'] == 0)
                                                                    -
                                                                @elseif ($attendances['att_check_overall_status'] == 'Present')
                                                                    {{ $attendances['att_check_out_time'] }}
                                                                @elseif($attendances['att_check_overall_status'] == 'Half day')
                                                                    {{ $attendances['att_check_out_time'] }}
                                                                @elseif($attendances['att_check_overall_status'] == 'Late Coming')
                                                                    {{ $attendances['att_check_out_time'] }}
                                                                @elseif(in_array($attendances['att_check_overall_status'],$manage_leaves) || $attendances['att_check_overall_status'] == 'RPL')
                                                                        -    
                                                                @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                        -
                                                                @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                                    {{ $attendances['att_check_out_time'] }}
                                                                @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                        -
                                                                @endif
                                                            @endif 
                                                                
                                                        @endforeach
                                                        
                                                    @endif

                                                    @php
                                                    
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                    
                                                    @if($absent )
                                                        @if(in_array($name_of_day,$login_user[0]['employee_offdays']) )
                                                            00:00:00

                                                        @elseif($date_filter< $today)
                                                            -
                                                        @else
                                                            -    
                                                        @endif
                                                    @endif
                                                
                                                    
                                                </td> 
                                                    
                                                <td  class="text-center"> 

                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                            
                                                            @if ($attendances['att_for_date'].'-'.$attendances['att_for_month'].'-'.$attendances['att_for_year'] == $dates[$month_count])
                                                                @php $absent = false; @endphp
                                                                @if ($attendances['att_check_overall_status'] == 'Present')
                                                                    {{ CustomHelper::calculate_shift_timings_shift_management($attendances['att_shift_start_time'],$attendances['att_shift_end_time']); }}
                                                                
                                                                @elseif($attendances['att_check_overall_status'] == 'Half day')
                                                                    {{ CustomHelper::calculate_shift_timings_shift_management($attendances['att_shift_start_time'],$attendances['att_shift_end_time']); }}
                                                                
                                                                @elseif($attendances['att_check_overall_status'] == 'Late Coming')
                                                                    {{ CustomHelper::calculate_shift_timings_shift_management($attendances['att_shift_start_time'],$attendances['att_shift_end_time']); }}
                                                                
                                                                @elseif(in_array($attendances['att_check_overall_status'],$manage_leaves) || $attendances['att_check_overall_status'] == 'RPL')
                                                                    -     
                                                                                    
                                                                @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                    -
                                                                    
                                                                @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                                    {{ CustomHelper::calculate_shift_timings_shift_management($attendances['att_shift_start_time'],$attendances['att_shift_end_time']); }}
                                                                
                                                                @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                @php $holiday_count++; @endphp
                                                                        -
                                                                @endif
                                                            @endif 
                                                        @endforeach


                                                        @if($absent )
                                                            @if(in_array($name_of_day,$login_user[0]['employee_offdays']) )
                                                                00:00:00
                                                            @elseif($date_filter < $today)
                                                                -
                                                            @else
                                                                -    
                                                            @endif
                                                        @endif

                                                    @endif
                                                </td> 
                                            
                                                <td  class="text-center"> 
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                
                                                        @if ($attendances['att_for_date'].'-'.$attendances['att_for_month'].'-'.$attendances['att_for_year'] == $dates[$month_count])
                                                                @php $absent = false; @endphp
                                                                @if($attendances['att_request_approval'] == 1)
                                                                    <span class="badge badge-light-danger text-center font-weight-bold">Attendance Request</span>
                                                                    @elseif($attendances['att_request_approval'] == 2)
                                                                    <span class="badge badge-light-danger text-center font-weight-bold">Leave Request</span>
                                                                    @elseif($attendances['att_request_approval'] == 3)
                                                                    <span class="badge badge-light-danger text-center font-weight-bold">RPL Leave Request</span>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </td> 
                                            
                                                <td  class="text-center"> 
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                
                                                       @if ($attendances['att_for_date'].'-'.$attendances['att_for_month'].'-'.$attendances['att_for_year'] == $dates[$month_count])
                                                            @php $absent = false; @endphp
                                                            @if($attendances['att_check_in_source'] == 1)
                                                                <span class="badge badge-light-success text-center font-weight-bold">Web Application</span>
                                                            @elseif($attendances['att_check_in_source'] == 2)
                                                                <span class="badge badge-light-info text-center font-weight-bold">Mobile Application</span>
                                                            @elseif($attendances['att_check_in_source'] == 3)
                                                                <span class="badge badge-light-danger text-center font-weight-bold">Updated Attendance</span>
                                                            @else
                                                                <span class="badge badge-light-success text-center font-weight-bold">Machine Attendance</span>
                                                            @endif
                                                        @endif 
                                                        @endforeach
                                                    @endif

                                                
                                                </td> 
                                            
                                                <td class="text-center">
                                                
                                                    @if (!empty($attendance))
                                                        @php
                                                            $total_working_hours = 0;
                                                            $total_working_hours_actual = 0;
                                                        @endphp
                                                        @foreach ($attendance as $attendances)

                                                        @php
                                                            
                                                            $calculate_shift_timings_overall = CustomHelper::calculate_shift_timings_shift_management($attendances['att_shift_start_time'],$attendances['att_shift_end_time'],$attendances['att_check_in_status'],$attendances['att_check_out_status']);
                                                            $calculate_working_hour_overall = CustomHelper::calculate_working_hour($attendances['att_check_out_status'],$attendances['att_check_in_time'],$attendances['att_check_out_time'],$attendances['att_created_at'],$attendances['att_updated_at']);
                                                            
                                                            $shift_components = explode(":", $calculate_shift_timings_overall);
                                                            $shift_seconds = $shift_components[0]*60*60 + $shift_components[1]*60;
                                                            $total_working_hours_actual +=$shift_seconds;

                                                            $components = explode(":",$calculate_working_hour_overall);
                                                            $seconds = $components[0]*60*60 + $components[1]*60;
                                                            $total_working_hours +=$seconds;

                                                        @endphp
                                                            
                                                           @if ($attendances['att_for_date'].'-'.$attendances['att_for_month'].'-'.$attendances['att_for_year'] == $dates[$month_count])
                                                            @php $absent = false; @endphp
                                                            @if ($attendances['att_check_overall_status'] == 'Present')
                                                                {{ CustomHelper::calculate_working_hour($attendances['att_check_out_status'],$attendances['att_check_in_time'],$attendances['att_check_out_time'],$attendances['att_created_at'],$attendances['att_updated_at'])}}
                                                            @elseif($attendances['att_check_overall_status'] == 'Half day')
                                                            {{ CustomHelper::calculate_working_hour($attendances['att_check_out_status'],$attendances['att_check_in_time'],$attendances['att_check_out_time'],$attendances['att_created_at'],$attendances['att_updated_at'])}}
                                                            @elseif($attendances['att_check_overall_status'] == 'Late Coming')
                                                            {{ CustomHelper::calculate_working_hour($attendances['att_check_out_status'],$attendances['att_check_in_time'],$attendances['att_check_out_time'],$attendances['att_created_at'],$attendances['att_updated_at'])}}
                                                            @elseif(in_array($attendances['att_check_overall_status'],$manage_leaves) || $attendances['att_check_overall_status'] == 'RPL' )
                                                                -
                                                            @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                -
                                                            @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                            {{ CustomHelper::calculate_working_hour($attendances['att_check_out_status'],$attendances['att_check_in_time'],$attendances['att_check_out_time'],$attendances['att_created_at'],$attendances['att_updated_at'])}}
                                                            @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                -
                                                            @endif
                                                        @endif 
                                                        @endforeach
                                                    @endif

                                                    @php
                                                
                                                    $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                    $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                    
                                                    @if($absent )
                                                        @if(in_array($name_of_day,$login_user[0]['employee_offdays']) )
                                                            00:00:00
                                                        @elseif($date_filter < $today)
                                                            -
                                                        @else
                                                            -
                                                        @endif
                                                    @endif

                                                </td>
                                                
                                                <td class="text-center"> 
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                            {{-- {{dd($month_count)}} --}}
                                                           @if ($attendances['att_for_date'].'-'.$attendances['att_for_month'].'-'.$attendances['att_for_year'] == $dates[$month_count])
                                                                @php $absent = false; @endphp
                                                                @if ($attendances['att_check_overall_status'] == 'Present')
                                                                    @php  $present++; @endphp
                                                                    <p class="text-success"><i class="fa-solid fa-calendar-check text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $attendances['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $attendances['att_check_out_time'] }}" ></i> -- On Time</p>
                                                                @elseif($attendances['att_check_overall_status'] == 'Half day')
                                                                    @php  $halfday++; @endphp
                                                                    <p class="text-danger"><i class="fa-solid fa-calendar-check text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $attendances['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $attendances['att_check_out_time'] }}"></i> -- Half Day</p>
                                                                @elseif($attendances['att_check_overall_status'] == 'Late Coming')
                                                                    @php  $late_coming++; @endphp
                                                                    <p class="text-gray"><i class="fa-solid fa-calendar-check text-gray" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $attendances['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $attendances['att_check_out_time'] }}"></i> -- Late Coming</p>
                                                                @elseif(in_array($attendances['att_check_overall_status'],$manage_leaves))
                                                                    <p class="text-info"><i class="fa-solid fa-user-large-slash" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{$attendances['att_check_overall_status']}}"></i>-- {{$attendances['att_check_overall_status']}}</p>
                                                                @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                    <p class="text-danger"><i class="fa-solid fa-xmark text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Absent"></i> -- Absent</p>
                                                                @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                                    @php $early_out++; @endphp
                                                                    <p class="text-info"><i class="fa-solid fa-clock text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $attendances['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $attendances['att_check_out_time'] }}"></i> -- Early out</p>
                                                                @elseif($attendances['att_check_overall_status'] == 'RPL')
                                                                <p style="color: maroon"><i style="color: maroon" class="fa-solid fa-calendar-check" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Check In Time : {{ $attendances['att_check_in_time'] }}  &nbsp; &nbsp; Check Out Time : {{ $attendances['att_check_out_time'] }}"></i> -- RPL </p>
                                                                @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                    <p class="text-warning"><i class="fa-solid fa-umbrella-beach text-warning" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $attendances['att_check_overall_status'] }}"></i> -- Holiday</p>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    @php
                                                       
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                    
                                                    @if($absent )
                                                        
                                                        @if(in_array($name_of_day,$login_user[0]['employee_offdays']) )
                                                            @php $offdays++; @endphp
                                                            <p class="text-warning">
                                                            <i class="fa-solid fa-sun text-warning" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" data-bs-title="Off Day"></i> -- Off Day</p>

                                                        @elseif($date_filter < $today)
                                                            @php $absent_count++; @endphp
                                                            <p class="text-danger">
                                                                <i class="fa-solid fa-xmark text-danger" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" data-bs-title="Absent"></i> -- Absent</p>
                                                        @endif
                                                    @endif
                                                    
                                                </td> 

                                                <td class="text-center">
                                                    {{-- @if (!empty($attendance)) --}}
                                                        <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions 
                                                    
                                                            <span class="svg-icon svg-icon-5 m-0">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    
                                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                            <div class="menu-item px-3">
                                                                {{-- <a href="javascript:void(0)" class="menu-link px-3"  onclick="editAttendanceadjustment('{{$attendances['att_employee_id']}}','{{date('Y-m-d', strtotime($current_date))}}')">
                                                                    Adjustment
                                                                  
                                                                </a> --}}

                                                                <a href="javascript:void(0)" class="menu-link px-3" onclick="editAttendanceadjustment('{{ Request::get('employee_id') ?? $login_user[0]['employee_id'] }}','{{ date('Y-m-d', strtotime($current_date)) }}')">
                                                                    Adjustment
                                                                </a>
                                                                {{-- <form id="add_new_adjustment" class="form ">
                                                                    @csrf

                                                                    <a href="#" class="menu-link px-3" >
                                                                        <input type="hidden" name="employee_id" value="{{ Request::get('employee_id') ?? $login_user[0]['employee_id'] }}" />
                                                                        <input type="hidden" name="attendance_date" value="{{ date('Y-m-d', strtotime($current_date)) }}"/>
                                                                        <select class="form-select" data-control="select2" data-hide-search="true" data-placeholder="Select Attendance type" id="attendance_type" name="attendance_type"> 
                                                                            <option value="" >Select Attendance Type</option>
                                                                            <option value="Official Tour">Official Tour</option>
                                                                            <option value="On Time Check In">Update Check In Time</option>
                                                                            <option value="On Time Check Out">Update Check Out Time</option>
                                                                            <option value="Present">Present</option>
                                                                        </select>
                                                                    </a>
                                                                    
                                                                </form>     --}}
                                                                
                                                            </div>    
                                                        
                                                        </div>
                                                    {{-- @endif     --}}
                                                         
                                                </td>

                                                
                                                
                                            </tr>
                                        
                                        @endfor
                                   
                                    <tr class="bg-light">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                      
                                        <td class="text-center"> 
                                            @if (!empty($month) && !empty($year))
                                                {{-- @php  $calculate_absent_shift = CustomHelper::employee_shift_time_calculation_absent($login_user[0]['employee_id'],$month,$year,$from_date=NUll,$to_date=Null); @endphp --}}
                                            @else
                                                 {{-- @php  $calculate_absent_shift = CustomHelper::employee_shift_time_calculation_absent($login_user[0]['employee_id'],$month,$year,Request::get('from_date'),Request::get('to_date')); @endphp --}}
                                            @endif
                                            @php 
                                             
                                                // $total_shift_calculation =  $calculate_absent_shift + $total_working_hours_actual;
                                                $total_shift_calculation =  $total_working_hours_actual;
                                                $total_working_hours_actual = CustomHelper::calculate_total_overtime($total_shift_calculation); @endphp
                                                {{$total_working_hours_actual['overtime']}}
                                        </td>
                                    
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">
                                            @php $total_working_hours = CustomHelper::calculate_total_overtime($total_working_hours); @endphp
                                            {{$total_working_hours['overtime'] }}

                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                            
                                    </tbody>
                                  
                                
                                </table>
                            
                            </div>
                        
                        </div>
                    
                    </div>
            
                </div>
            </div>
        
        </div>

        <form id="add_new_adjustment" class="form">
            @csrf
            <div class="modal fade" id="attendance_adjustment" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-650px">
                    <div class="modal-content rounded">
                        <div class="modal-content rounded">
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Attendance Adjustment </h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <div class="modal-body scroll-y">
                                    
                                <input type="hidden" name="attendance_id" />
                                <input type="hidden" name="employee_code" />

                                <div class="d-flex flex-column mb-8 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Attendance Date</label>
                                    <div class="position-relative d-flex align-items-center">

                                        <input type="date" class="form-control" name="attendance_date" id="update_dated" required/>
                                    </div>
                                </div>

                                <div class="d-flex flex-column mb-8 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">Update Attendance</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Please select the option below which you are willing to mark your attendance."></i>
                                    </label>
                                    <select class="form-select" data-control="select2" data-hide-search="true" data-placeholder="Select Attendance type" id="" name="attendance_type" required> 
                                        <option value="" >Select Attendance Type</option>
                                        <option value="Official Tour">Official Tour</option>
                                        <option value="On Time Check In">Update Check In Time</option>
                                        <option value="On Time Check Out">Update Check Out Time</option>
                                        <option value="Present">Present</option>
                                    </select>
                                </div>

                                    {{-- <div class="main-check-in-div col-md-12 mb-8 fv-row d-none">
                                        <label class="required fs-6 fw-semibold mb-2">Check In Time</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="time" step="1" class="form-control" name="check_in_time" placeholder="Enter in Time" id="check_in_time">
                                        </div>
                                    </div>

                                    <div class="main-check-out-div col-md-12 mb-8 fv-row d-none">
                                        <label class="required fs-6 fw-semibold mb-2">Check Out Time</label>
                                        <div class="position-relative d-flex align-items-center">
                                        
                                            <input type="time" step="1" class="form-control" name="check_out_time"
                                                    placeholder="Enter out Time"  id="check_out_time"  >
                                        </div>
                                    </div>


                                    <div class="d-flex flex-column mb-8">
                                        <label class="fs-6 fw-semibold mb-2 required">Enter Reason</label>
                                        <textarea class="form-control " rows="3" name="attendance_message" id="kt_docs_maxlength_threshold3" maxlength="500" placeholder="Message" required></textarea>
                                    </div>
                                </div> --}}
                            <div class="modal-footer justify-content-center">
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit_btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    
    </div>
</div>

@endsection



@push('js-link')
<script src="{{url('assets/js/attendance/my-attendance.js')}}"></script>
<script>
var present_attendance = {{ $present }};
var absent_count_attendance = {{ $absent_count }};
var late_coming_attendance = {{ $late_coming }};
var early_out_attendance = {{ $early_out }};
var halfday_attendance = {{ $halfday }};
var total_offDays_holiday_attendance = {{ $offdays }} +  {{ $holiday_count }};


function editAttendanceadjustment(att_employee_id ,att_for_date) {
    // $('input[name=attendance_id]').val(attendance_id);
    $('input[name=employee_code]').val(att_employee_id);
    $("#update_dated").val(att_for_date);
    $('#attendance_adjustment').modal('show');
}



    $('#add_new_adjustment').submit(function (e) {
        $('#submit_btn').prop('disabled', true);
        $("#submit_btn").attr('data-kt-indicator', 'on');
        $('#submit_btn').css('cursor', 'pointer');

        e.preventDefault();
        $.ajax({
            url: base_url + '/attendance-adjustment-update',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {

                if (data.status == 'TRUE') {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg,
                        timer: 3000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    $('#submit_btn').prop('disabled', false);
                    $('#submit_btn').css('cursor', 'pointer');
                    $("#submit_btn").removeAttr('data-kt-indicator');
                }
            },
            error: function (jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#submit_btn').prop('disabled', false);
                $('#submit_btn').css('cursor', 'pointer');
                $("#submit_btn").removeAttr('data-kt-indicator');
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
                } else {
                    if (errorStatus == 419) {
                        location.reload();
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: 'Try Again. Error Code ' + errorStatus,
                            timer: 3000,
                        })
                    }
                }
            }
        });
    });

// $(document).ready(function() {
    // $('#add_new_adjustment select[name="attendance_type"]').on('change', function() {

    //     // Submit the form
    //     var selectedValue = $(this).val();
    //     // Set the value of the hidden input
    //     $('#attendance_type').val(selectedValue);

    //     // Disable the select element again
    
    //     // Perform AJAX request
    //     $.ajax({
    //         url: base_url + '/attendance-adjustment-update',
    //         type: 'POST',
    //         headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: $('#add_new_adjustment').serialize(),
    //         success: function(data) {
    //             if (data.status == 'TRUE') {
    //                 // Show success message
    //                 Toast.fire({
    //                     icon: 'success',
    //                     title: data.msg,
    //                     timer: 3000,
    //                 });
    //                 setTimeout(() => {
    //                     location.reload();
    //                 }, 3000);
    //             } else {
    //                 // Show error message
    //                 Toast.fire({
    //                     icon: 'warning',
    //                     title: data.msg,
    //                     timer: 5000,
    //                 });
    //             }
    //         },
    //         error: function(jqXHR, textStatus, errorThrown) {
    //             // Show error message
    //             Toast.fire({
    //                 icon: 'error',
    //                 title: 'Error occurred. Please try again.',
    //                 timer: 3000,
    //             });
    //         }
    //     });
    // });
// });




   
</script>

    
@endpush