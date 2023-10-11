@extends('layout.main')
@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Attendance Summary</h1>
                {{-- <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    @if (empty(Request::get('d')))
                        <h1>Daily Attendance Report ({{ date('d') .  '-' . date('F') . '-' . date('Y') }})</h1>
                    @else
                        <h1>Daily Attendance Report ({{  date('d-F-Y', strtotime(Request::get('d'))) }})</h1>
                    @endif
                </h1> --}}
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

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
                {{-- @if(!empty($attendance))
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
                @endif     --}}
            
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            {{-- <div class="row" id="card-container">
                    
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

                <div class="col-md-2 col-6 filter-record mb-4" data-value="{{$leave_name}}" style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="Leaves">{{$t_employee_leaves??''}}</div>
                            <div class="fw-semibold text-gray-400 ">Leaves</div>
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
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('employee_location')) ? $filter : '' }} {{ !empty(Request::get('is_active')) ? $filter : '' }} {{ !empty(Request::get('d')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                       
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('bisconni-attendance-report') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                    </div>

                                    <div class="separator border-gray-200"></div>
                                    <div class="px-7 py-5">

                                        {{-- <div class="row"> --}}
                                            {{-- <div class="col-md-6"> --}}
                                                {{-- <div class="mb-3">
                                            
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
                                                    
                                                </div> --}}
        
                                                {{-- <div class="mb-3">
                                                    
                                                    <label class="form-label fw-semibold">Employee Status</label>
                                                    
                                                    <div>
                                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Name" name="is_active">
                                                        <option value="1" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '1') selected @endif>Active Employees</option>
                                                        <option value="2" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '2') selected @endif>De-active Employees</option>
                                                        <option value="3" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '3') selected @endif>All Employees</option>
                                                    </select>
                                                    </div>
                                                    
                                                </div> --}}

                                                @php
                                                    
                                                    if(empty(Request::get('from_date'))){
                                                        $today = new DateTime();
                                                        $today->modify('last day of last month'); // Go to the last day of the previous month
                                                        $today->setDate($today->format('Y'), $today->format('m'), 26); // Set the day to 26th

                                                        $previousMonth26th = $today->format('Y-m-d');

                                                        $from_date = $previousMonth26th;
                                                    }else{
                                                        $from_date = Request::get('from_date');
                                                    }

                                                    if(empty(Request::get('to_date'))){
                                                        $to_date = date('Y-m-d');
                                                    }else{
                                                        $to_date = Request::get('to_date');
                                                    }
                                                    

                                                @endphp
        
                                                <div class="mb-3">
                                                    
                                                    <label class="form-label fw-semibold">From Date</label>
                                                    
                                                    <div class="position-relative d-flex align-items-center">
                                                        <input type="date" class="form-control" placeholder="Select a date" name="from_date" value="{{$from_date}}"  max="{{ date('Y-m-d') }}" required/>
                                                    </div>
                                                    
                                                </div>
        
                                                <div class="mb-3">
                                                    
                                                    <label class="form-label fw-semibold">To Date</label>
                                                    
                                                    <div class="position-relative d-flex align-items-center">
                                                        <input type="date" class="form-control" placeholder="Select a date" name="to_date" value="{{ $to_date }}"  max="{{ date('Y-m-d') }}" required/>
                                                    </div>
                                                    
                                                </div>
                                            {{-- </div> --}}
                                        {{-- </div> --}}

                                        
                                        
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('employee_location')) || !empty(Request::get('from_date')) || !empty(Request::get('to_date')))
                                                <a href="{{ url('bisconni-attendance-report') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2">Reset Filter</a>
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
                                            <th class="text-center">On Time</th>
                                            <th class="text-center">Late / Early Out</th>
                                            <th class="text-center">Half Day</th>
                                            <th class="text-center">Leaves</th>
                                            <th class="text-center">Off Days</th>
                                            <th class="text-center">Absent</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                  
                                    <tbody>
                                        @if (!empty($attendance))
                                        
                                            @foreach ($attendance as $attendances)
                                                <tr>
                                                    <td class="ps-8">{{ $attendances['employee_code'] }} - {{ $attendances['employee_name'] }} </td>
                                                    <td class="text-center">{{ $attendances['total_present'] }}</td>
                                                    <td class="text-center">{{ $attendances['total_late'] }}</td>
                                                    <td class="text-center">{{ $attendances['total_halfday'] }}</td>
                                                    <td class="text-center">{{ $attendances['total_leaves'] }}</td>
                                                    <td class="text-center">{{ $attendances['off_days_count'] }}</td>
                                                    <td class="text-center">{{ $attendances['absent'] }}</td>
                                                    <td class="text-center">{{ $attendances['total_days'] }}</td>
                                                    <td class="ps-4">
                                                        <a target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="{{ url('bisconni-employee-report?emp_id='.$attendances['employee_id'].'&from_date='.$from_date.'&to_date='.$to_date) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="View Attendance">
                                                            <i class="fa fa-eye"></i>
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
@endpush