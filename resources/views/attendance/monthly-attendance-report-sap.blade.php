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
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @if (!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                        <a href="{{ url('my-attendance-report?from_date='.Request::get('from_date').'&to_date='.Request::get('to_date'))}}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @else
                        <a href="{{ url('my-attendance-report?from_date='.Request::get('from_date').'&to_date='.Request::get('to_date')) }}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
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
                                            <th class="ps-8">Employee Code & Name</th>
                                            <th class="text-center">Date &amp; Day</th>
                                            <th class="text-center">Check In Time </th>
                                            <th class="text-center">Check Out Time</th>
                                            {{-- <th class="text-center">Shift Duration</th>
                                            <th class="text-center">Leave/Deputation</th>
                                            <th class="text-center">Att Source</th>
                                            <th class="text-center">Hours Worked</th> --}}
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($attendance))
                                            @foreach ($attendance as $attendances)
                                                <tr>
                                                    <td class="ps-8">{{ ltrim($attendances['emp_code'], '0') }} - {{ $attendances['emp_name'] }} </td>
                                                    <td class="text-center">
                                                        <p class="text-dark fw-bold mb-1 fs-6"> {{ $attendances['attendance_date'] }}</p>
                                                        <p class="text-muted fw-semibold text-muted fs-7">{{$attendances['attendance_day'] }}</p>
                                                    </td>
                                                    @php
                                                        $check_in_duration = $attendances['time_in'];
                                                        $check_out_duration = $attendances['time_out'];
                                                        $check_in_time = \Carbon\CarbonInterval::fromString($check_in_duration)->cascade()->format('H:i:s');
                                                        $check_out_time = \Carbon\CarbonInterval::fromString($check_out_duration)->cascade()->format('H:i:s');

                                                    @endphp
                                                    <td class="text-center"> 
                                                        {{ $check_in_time }}
                                                    </td>

                                                    <td  class="text-center"> 
                                                        {{ $check_out_time }}
                                                    </td>

                                                    <td  class="text-center"> 
                                                        {{ $attendances['attendance_status'] }}
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