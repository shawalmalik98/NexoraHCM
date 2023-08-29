@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
       
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                 
                    @if (!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                        My Attendance ({{date('F Y',strtotime($year . '-' . $month . '-01')) }})
                    @else
                        
                        My Attendance ({{date('d-F', strtotime(Request::get('from_date'))) .' to ' .date('d-F-Y', strtotime(Request::get('to_date')))}})
                    @endif
                </h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @if (!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                        <a href="{{ url('my-attendance-report?m='.$month.'&y='.$year.'&from_date='.Request::get('from_date').'&to_date='.Request::get('to_date'))}}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @else
                        <a href="{{ url('my-attendance-report?m='.$month.'&y='.$year.'&from_date='.Request::get('from_date').'&to_date='.Request::get('to_date')) }}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @endif
                  
                </div>
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container" >
            <div class="row" id="card-container">
                    
                <div class="col-md-2 col-6 filter-record mb-4" data-value="On Time"  style="cursor: pointer" >
                    <div class="card" >
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="present">0</div>
                            <div class="fw-semibold text-gray-400 ">Present</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6  filter-record mb-4" data-value='Late Coming,Early out'  style="cursor: pointer">
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
                            <div class="fw-semibold text-gray-400 ">On Leaves</div>
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
                                        
                                        <div class="mb-3" id="main-div-select-month">
                                            
                                            <label class="form-label fw-semibold @if (!empty($month) && is_numeric($month)) required  @endif" id="label-month">Select Month</label>
                                            
                                            <select class="form-select " data-kt-select2="true" data-placeholder="Select Month"name="m"
                                                 id="select-month" @if (!empty($month) && is_numeric($month)) required  @endif>
                                                <option value="">Select Month</option>
                                                <option value="01" @if (!empty($month) && $month == '01') selected  @endif>January
                                                </option>
                                                <option value="02" @if (!empty($month) && $month == '02') selected  @endif>February
                                                </option>
                                                <option value="03" @if (!empty($month) && $month == '03') selected @endif>March
                                                </option>
                                                <option value="04" @if (!empty($month) && $month == '04') selected  @endif>April
                                                </option>
                                                <option value="05" @if (!empty($month) && $month == '05') selected  @endif>May
                                                </option>
                                                <option value="06" @if (!empty($month) && $month == '06') selected  @endif>June
                                                </option>
                                                <option value="07" @if (!empty($month) && $month == '07') selected  @endif>July
                                                </option>
                                                <option value="08" @if (!empty($month) && $month == '08') selected  @endif>August
                                                </option>
                                                <option value="09" @if (!empty($month) && $month == '09') selected  @endif>September
                                                </option>
                                                <option value="10" @if (!empty($month) && $month == '10') selected  @endif>October
                                                </option>
                                                <option value="11" @if (!empty($month) && $month == '11') selected  @endif>November
                                                </option>
                                                <option value="12" @if (!empty($month) && $month == '12') selected @endif>December
                                                </option>
                                            </select>
                                        
                                            
                                        </div>

                                        <div class="mb-3" id="main-div-select-year">
                                            
                                            <label class="form-label fw-semibold @if (!empty($year) && is_numeric($year)) required  @endif" id="label-year">Select Year</label>
                                            
                                                <select class="form-select " data-kt-select2="true" data-placeholder="Select Year" name="y"  id="select-year" @if (!empty($year) && is_numeric($year)) required  @endif>
                                                    <option value="">Select Year</option>
                                                    @if(!empty($dynamic_years))
                                                        @foreach ($dynamic_years as $year_list)
                                                            <option value="{{$year_list}}" @if (!empty($year) && $year == $year_list) selected   @endif>{{$year_list}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                     
                                            
                                        </div>

                                        <div class="mb-3">
                                            
                                            <label class="form-label fw-semibold @if(!empty(Request::get('from_date'))) required  @endif" id="label-from-date">Form Date</label>
                                            <input class="form-control" type="date" name="from_date" id="from_date" value="{{Request::get('from_date')}}" @if(!empty(Request::get('from_date'))) required  @endif max="{{date('Y-m-d')}}" />
                                        </div>

                                        <div class="mb-3">
                                            
                                            <label class="form-label fw-semibold @if(!empty(Request::get('to_date'))) required  @endif" id="label-to-date">To Date</label>
                                            <input class="form-control" type="date" name="to_date" id="to_date" value="{{Request::get('to_date')}}" @if(!empty(Request::get('to_date'))) required  @endif max="{{date('Y-m-d')}}"/>
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
                       
                    </div>
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
                                        $login_user[0]['employee_offday1'] = (!empty($login_user[0]['employee_offday1'])) ? explode(",",$login_user[0]['employee_offday1']):[]; 
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
                                                                        00:00:00       
                                                                @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                        00:00:00
                                                                @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                                {{ $attendances['att_check_in_time'] }}
                                                                @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                        00:00:00
                                                                @endif
                                                            @endif  
                                                        @endforeach
                                                    @endif

                                                    @php
                                                    
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                        @endphp
                                                        
                                                        @if($absent )
                                                        
                                                            @if(in_array($name_of_day,$login_user[0]['employee_offday1']) )
                                                            00:00:00

                                                            @elseif($date_filter< $today)
                                                                00:00:00
                                                            @endif
                                                        @endif
                                                
                                                    
                                                </td> 

                                                <td  class="text-center"> 
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                
                                                       @if ($attendances['att_for_date'].'-'.$attendances['att_for_month'].'-'.$attendances['att_for_year'] == $dates[$month_count])
                                                            @php $absent = false; @endphp
                                                            @if ($attendances['att_check_overall_status'] == 'Present')
                                                                {{ $attendances['att_check_out_time'] }}
                                                            @elseif($attendances['att_check_overall_status'] == 'Half day')
                                                                {{ $attendances['att_check_out_time'] }}
                                                            @elseif($attendances['att_check_overall_status'] == 'Late Coming')
                                                                {{ $attendances['att_check_out_time'] }}
                                                            @elseif(in_array($attendances['att_check_overall_status'],$manage_leaves) || $attendances['att_check_overall_status'] == 'RPL')
                                                                00:00:00    
                                                            @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                    00:00:00
                                                            @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                                {{ $attendances['att_check_out_time'] }}
                                                            @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                    00:00:00
                                                            @endif
                                                        @endif 
                                                        @endforeach
                                                    @endif

                                                    @php
                                                    
                                                        
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                    
                                                    @if($absent )
                                                        @if(in_array($name_of_day,$login_user[0]['employee_offday1']) )
                                                            00:00:00

                                                        @elseif($date_filter< $today)
                                                            00:00:00
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
                                                                    00:00:00     
                                                                                    
                                                                @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                    00:00:00
                                                                    
                                                                @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                                    {{ CustomHelper::calculate_shift_timings_shift_management($attendances['att_shift_start_time'],$attendances['att_shift_end_time']); }}
                                                                
                                                                @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                @php $holiday_count++; @endphp
                                                                        00:00:00
                                                                @endif
                                                            @endif 
                                                        @endforeach


                                                        @if($absent )
                                                            @if(in_array($name_of_day,$login_user[0]['employee_offday1']) )
                                                                00:00:00
                                                            @elseif($date_filter < $today)
                                                                00:00:00
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
                                                                00:00:00
                                                            @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                                00:00:00
                                                            @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                            {{ CustomHelper::calculate_working_hour($attendances['att_check_out_status'],$attendances['att_check_in_time'],$attendances['att_check_out_time'],$attendances['att_created_at'],$attendances['att_updated_at'])}}
                                                            @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                                00:00:00
                                                            @endif
                                                        @endif 
                                                        @endforeach
                                                    @endif

                                                    @php
                                                
                                                    $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                    $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                    
                                                    @if($absent )
                                                        @if(in_array($name_of_day,$login_user[0]['employee_offday1']) )
                                                            00:00:00
                                                        @elseif($date_filter < $today)
                                                        00:00:00
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
                                                        
                                                        @if(in_array($name_of_day,$login_user[0]['employee_offday1']) )
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
                                            </tr>
                                        
                                        @endfor
                                   
                                    <tr class="bg-light">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                      
                                        <td class="text-center"> 
                                            @if (!empty($month) && !empty($year))
                                                @php  $calculate_absent_shift = CustomHelper::employee_shift_time_calculation_absent($login_user[0]['employee_id'],$month,$year,$from_date=NUll,$to_date=Null); @endphp
                                            @else
                                                 @php  $calculate_absent_shift = CustomHelper::employee_shift_time_calculation_absent($login_user[0]['employee_id'],$month,$year,Request::get('from_date'),Request::get('to_date')); @endphp
                                            @endif
                                            @php 
                                             
                                                $total_shift_calculation =  $calculate_absent_shift + $total_working_hours_actual;
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
                                    </tr>
                            
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
</script>

    
@endpush