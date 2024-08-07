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
                        
                        My Attendance ({{date('d-M', strtotime($from_date)) .' to ' .date('d-M-Y', strtotime($to_date))}})
                    @endif
                </h1>   
            </div>
            {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @if (!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                        <a href="{{ url('get-employee-attendance-sap-api?from_date='.Request::get('from_date').'&to_date='.Request::get('to_date'))}}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @else
                        <a href="{{ url('get-employee-attendance-sap-api?from_date='.Request::get('from_date').'&to_date='.Request::get('to_date')) }}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @endif
                  
                </div>
            </div> --}}

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="m-0">
                    <button id="print_attendance" class="btn btn-sm btn-success">Print Attendance Report</button>

                    {{-- <button class="dt-button buttons-print" tabindex="0" aria-controls="example" type="button"><span>Print</span></button> --}}
                    {{-- <a href="javascript:void(0)" class="btn btn-sm btn-success" id="print_attendance">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Print Attendance Report
                    </a>   --}}
                </div>
            </div>   
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container" >
            {{-- <div class="row" id="card-container">
                    
                <div class="col-md-2 col-6 filter-record mb-4" data-value="On Time"  style="cursor: pointer" >
                    <div class="card" >
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="present">0</div>
                            <div class="fw-semibold text-gray-400 ">On Time</div>
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
                            <div class="text-gray-900 fw-bold fs-2" id="Leaves">{{$t_employee_leaves??'0'}}</div>
                            <div class="fw-semibold text-gray-400 ">On Leaves</div>
                        </div>
                    </div>
                </div>

            </div> --}}
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
                                <form action="{{ url('/sap-login') }}" method="GET">
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
                                                <a href="{{ url('/sap-login') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2">Reset Filter</a>
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
                                            <th >Date &amp; Day</th>
                                            <th class="ps-8">Employee Code & Name</th>
                                           
                                            <th >Check In Time </th>
                                            <th >Check Out Time</th>
                                          
                                            {{-- <th >Att Source</th>
                                            <th >Hours Worked</th> --}}
                                            <th >Status</th>
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

                                        $attendance_total_count = count($attendance);
                                        $login_user[0]['employee_offdays'] = (!empty($login_user[0]['employee_offdays'])) ? explode(",",$login_user[0]['employee_offdays']):[]; 
                                        @endphp
                                        @for ($month_count = $month_count; $month_count <= $month_days; $month_count++)
                                            @php
                                                $absent = true;
                                                $loop_count = 0;
                                            @endphp
                                           
                                            {{-- <tr>

                                                @php
                                                    $current_date = $dates[$month_count];
                                                    $name_of_day = date("l", strtotime($current_date));	
                                                    $name_of_day_short = date("D", strtotime($current_date));	
                                                @endphp

                                                <td>
                                                    {{ $current_date }} - {{  $name_of_day_short }} 
                                                </td>
                                    
                                               
                                                <td class="ps-8">

                                                    @php
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count])));
                                                        $today = strtotime(date("d-m-Y"));
                                                        $attendance_found = false;
                                                    @endphp
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                            
                                                            @if (date("d-m-Y", strtotime($attendances['attendance_date']))  == $dates[$month_count])
                                                                @if($attendances['attendance_status'] == 'ABSENT')
                                                                    <span style="color:red">{{ ltrim($attendances['emp_code'], '0') }} - {{ $attendances['emp_name'] }} </span>
                                                                @elseif($attendances['attendance_status'] == 'DAY OFF')
                                                                    <span style="color:#a9a9a9">{{ ltrim($attendances['emp_code'], '0') }} - {{ $attendances['emp_name'] }}</span>
                                                                @else
                                                                    {{ ltrim($attendances['emp_code'], '0') }} - {{ $attendances['emp_name'] }} 
                                                                @endif
                                                                
                                                                @php $attendance_found = true; @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if (!$attendance_found && $date_filter <= $today)
                                                     -
                                                    @endif
                                              
                                                </td>
                                                <td > 

                                                    @php
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count])));
                                                        $today = strtotime(date("d-m-Y"));
                                                        $attendance_found = false;
                                                    @endphp
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                            
                                                            @if (date("d-m-Y", strtotime($attendances['attendance_date']))  == $dates[$month_count])
                                                                @if($attendances['attendance_status'] == 'ABSENT')
                                                                    <span style="color:red">{{ $attendances['time_in'] }}</span>
                                                                @elseif($attendances['attendance_status'] == 'DAY OFF')
                                                                    <span style="color:#a9a9a9">{{ $attendances['time_in'] }}</span>
                                                                @else
                                                                    {{ $attendances['time_in'] }}
                                                                @endif
                                                            
                                                                @php $attendance_found = true; @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if (!$attendance_found && $date_filter <= $today)
                                                     -
                                                    @endif
                                                    
                                                </td>
                                                <td> 
                                                    @php
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count])));
                                                        $today = strtotime(date("d-m-Y"));
                                                        $attendance_found = false;
                                                    @endphp
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                            
                                                            @if (date("d-m-Y", strtotime($attendances['attendance_date']))  == $dates[$month_count])
                                                                @if($attendances['attendance_status'] == 'ABSENT')
                                                                    <span style="color:red">{{ $attendances['time_out'] }}</span>
                                                                @elseif($attendances['attendance_status'] == 'DAY OFF')
                                                                    <span style="color:#a9a9a9">{{ $attendances['time_out'] }}</span>
                                                                @else
                                                                    {{ $attendances['time_out'] }}
                                                                @endif
                                                                @php $attendance_found = true; @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if (!$attendance_found && $date_filter <= $today)
                                                     -
                                                    @endif
                                                    
                                                </td>
                                                
                                                <td > 
                                                    @php
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count])));
                                                        $today = strtotime(date("d-m-Y"));
                                                        $attendance_found = false;
                                                    @endphp
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                            
                                                            @if (date("d-m-Y", strtotime($attendances['attendance_date']))  == $dates[$month_count])
                                                                @if($attendances['attendance_status'] == 'ABSENT')
                                                                    <span style="color:red">{{ $attendances['attendance_status'] }}</span>
                                                                @elseif($attendances['attendance_status'] == 'DAY OFF')
                                                                    <span style="color:#a9a9a9">{{ $attendances['attendance_status'] }}</span>
                                                                @else
                                                                    {{ $attendances['attendance_status'] }}
                                                                @endif
                                                                
                                                                @php $attendance_found = true; @endphp  
                                                            @endif
                                                        @endforeach
                                                    @endif 

                                                    @if (!$attendance_found && $date_filter <= $today)
                                                    -
                                                   @endif
                                                    
                                                </td> 
                                                        
                                                   
                                            </tr> --}}

                                            <tr>

                                                @php
                                                    $current_date = $dates[$month_count];
                                                    $name_of_day = date("l", strtotime($current_date));	
                                                    $name_of_day_short = date("D", strtotime($current_date));	
                                                @endphp

                                                <td >
                                                     {{ $current_date }} - {{  $name_of_day_short }}
                                                  
                                                </td>
                                    
                                               
                                                <td class="ps-8">

                                                    {{-- @php
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count])));
                                                        $today = strtotime(date("d-m-Y"));
                                                        $attendance_found = false;
                                                    @endphp --}}
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)

                                                            @if (date("d-m-Y", strtotime($attendances['attendance_date']))  == $dates[$month_count])
                                                                
                                                                @php $absent = false; @endphp

                                                                {{ ltrim($attendances['emp_code'], '0') }} - {{ $attendances['emp_name'] }} 
                                                                {{-- @php $attendance_found = true; @endphp --}}
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    {{-- @if (!$attendance_found && $date_filter <= $today)
                                                     -
                                                    @endif --}}

                                                    @php
                                                
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                
                                                    @if($absent )
                                                    
                                                        @if (in_array($name_of_day, $login_user[0]['employee_offdays']))
                                                                -
                                                            @elseif ($date_filter < $today)
                                                                -
                                                            @else
                                                                -    
                                                        @endif
                                                    @endif
                                              
                                                </td>
                                                <td > 

                                                    {{-- @php
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count])));
                                                        $today = strtotime(date("d-m-Y"));
                                                        $attendance_found = false;
                                                    @endphp --}}
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)

                                                            @if (date("d-m-Y", strtotime($attendances['attendance_date']))  == $dates[$month_count])
                                                          
                                                                @php $absent = false; @endphp

                                                             {{ $attendances['time_in'] }}  
                                                                {{-- @php $attendance_found = true; @endphp --}}
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    {{-- @if (!$attendance_found && $date_filter <= $today)
                                                    -
                                                    @endif --}}

                                                    @php
                                                
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                
                                                    @if($absent )
                                                    
                                                        @if (in_array($name_of_day, $login_user[0]['employee_offdays']))
                                                            
                                                            <span style="color:#a9a9a9">00:00:00</span>

                                                            @elseif ($date_filter < $today)
                                                                <span style="color:#a9a9a9">00:00:00</span>
                                                            @else
                                                                -    
                                                        @endif
                                                    @endif
                                                    
                                                </td>
                                                <td > 
                                                    {{-- @php
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count])));
                                                        $today = strtotime(date("d-m-Y"));
                                                        $attendance_found = false;
                                                    @endphp --}}
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                            
                                                            @if (date("d-m-Y", strtotime($attendances['attendance_date']))  == $dates[$month_count])
                                                               
                                                                @php $absent = false; @endphp

                                                                {{ $attendances['time_out'] }}  
                                                                {{-- @php $attendance_found = true; @endphp --}}
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    {{-- @if (!$attendance_found && $date_filter <= $today)
                                                     -
                                                    @endif --}}

                                                    @php
                                                
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                
                                                    @if($absent )
                                                    
                                                        @if (in_array($name_of_day, $login_user[0]['employee_offdays']))
                                                            
                                                            <span style="color:#a9a9a9">00:00:00</span>

                                                            @elseif ($date_filter < $today)
                                                                <span style="color:#a9a9a9">00:00:00</span>
                                                            @else
                                                                -    
                                                        @endif
                                                    @endif
                                                    
                                                </td>
                                                
                                                <td > 
                                                    {{-- @php
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count])));
                                                        $today = strtotime(date("d-m-Y"));
                                                        $attendance_found = false;
                                                    @endphp --}}
                                                    @if (!empty($attendance))
                                                        @foreach ($attendance as $attendances)
                                                            
                                                            @if (date("d-m-Y", strtotime($attendances['attendance_date']))  == $dates[$month_count])
                                                                
                                                                @php $absent = false; @endphp

                                                                {{ $attendances['attendance_status'] }} 
                                                                {{-- @php $attendance_found = true; @endphp   --}}
                                                            @endif
                                                        @endforeach
                                                    @endif 

                                                    {{-- @if (!$attendance_found && $date_filter <= $today)
                                                    -
                                                   @endif --}}

                                                    @php
                                                       
                                                        $date_filter = strtotime(date("d-m-Y", strtotime($dates[$month_count] )));
                                                        $today = strtotime(date("d-m-Y"));
                                                    @endphp
                                                    
                                                    @if($absent )
                                                        
                                                    @if (in_array($name_of_day, $login_user[0]['employee_offdays']))
                                                        @php $offdays++; @endphp
                                                            <p>
                                                                <span style="color:#a9a9a9">DAY OFF</span>
                                                            </p>

                                                        @elseif($date_filter < $today)
                                                            @php $absent_count++; @endphp
                                                            <p>
                                                                <span style="color:red">Absent</span>
                                                            </p>
                                                        @endif
                                                    @endif
                                                    
                                                </td> 
                                                        
                                                   
                                            </tr>
                                        @endfor 
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

    // $(document).ready(function() {

    //     $('#print_attendance').on('click', function() {
    //         var printWindow = window.open('', '_blank');
    //         printWindow.document.write('<html><head><title>Print Attendance Report</title></head><body>');
    //         printWindow.document.write($('#system_datatable').clone().wrap('<p>').parent().html());
    //         printWindow.document.write('</body></html>');
    //         printWindow.document.close();
    //         printWindow.print();
    //     });
        
    // });

    $(document).ready(function() {
        $('#system_datatable').DataTable();

        $('#print_attendance').on('click', function() {
            var tableHtml = $('#system_datatable').prop('outerHTML');
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Attendance Report</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">');
            printWindow.document.write('<style>body { font-family: Arial, sans-serif; } table th, table td { text-align: center; }</style>'); // Modified style
            printWindow.document.write('</head><body>');
            printWindow.document.write(tableHtml);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        });
    });

   
</script>

    
@endpush