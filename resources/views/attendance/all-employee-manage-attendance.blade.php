@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
						 
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div class="app-container col-12 d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{-- @if (empty($month) && empty($year))
                        Attendance Sheet ({{ date('F') . '-' . date('Y') }})
                    @else
                        Attendance Sheet ({{ date("F", mktime(0, 0, 0, $month, 10)) }}-{{ $year }})
                    @endif --}}
                    Attendance Sheet ({{ date('d M, Y', strtotime($from_date)) }} - {{ date('d M, Y', strtotime($to_date)) }})
                </h1>   
            </div>
            {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ url('/monthly-attendance?month='.$month.'&year='.$year) }}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >Downlaod Month Attendance</a>
                </div>

            </div> --}}

            @if(!empty($all_attendances))
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Download Payroll Attendance Report
                    </a>
                </div>
            @endif    

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
                    <div class="card-toolbar" >
                        @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('employee_location')) ? $filter : '' }} {{ !empty(Request::get('is_active')) ? $filter : '' }} {{ !empty(Request::get('emp_name')) ? $filter : '' }} {{ !empty(Request::get('m')) ? $filter : '' }} {{ !empty(Request::get('y')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        Filter
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                            <form action="{{ url('monthly-attendance') }}" method="GET">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                </div>
                            
                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5">
                                    <div class="mb-3">
                                    
                                        <label class="form-label fw-semibold">
                                            <span>Department</span>
                                        </label>
                                        <div>
                                            <select class="form-select" data-kt-select2="true" name="department_id" data-placeholder="Select Department">
                                                <option value="">Select Department</option>
                                                <option value="All" @if (Request::get('department_id') == 'All') selected @endif>All Departments</option>
                                                @if(!empty($departments))
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department['department_id'] }}" @if (Request::get('department_id') == $department['department_id']) selected @endif>{{ $department['department_name'] }}</option>
                                                    @endforeach
                                                @endif                                                             
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row" >
                                        <div class="mb-3">
                                        
                                            <label class="form-label fw-semibold">Business Unit</label>
                                            
                                            <div>
                                                <select class="form-select " data-kt-select2="true" data-placeholder="Select Business Unit" name="business_unit">
                                                <option value="">Select Business Unit</option>
                                                <option value="All" @if (Request::get('business_unit') == 'All') selected @endif>All Business Units</option>
                                                <option value="Candyland" @if (Request::get('business_unit') == 'CandyLand') selected @endif>CandyLand</option>
                                                <option value="Bisconni" @if (Request::get('business_unit') == 'Bisconni') selected @endif>Bisconni</option>
                                                <option value="Snackcity" @if (Request::get('business_unit') == 'Snackcity') selected @endif>Snackcity</option>
                                                <option value="Hudson Pharma" @if (Request::get('business_unit') == 'Hudson Pharma') selected @endif>Hudson Pharma</option>
                                                <option value="Astro" @if (Request::get('business_unit') == 'Astro') selected @endif>Astro</option>  
                                                <option value="Act Group" @if (Request::get('business_unit') == 'Act Group') selected @endif>Act Group</option>  
                                                <option value="Ismail Nutrition" @if (Request::get('business_unit') == 'Ismail Nutrition') selected @endif>Ismail Nutrition</option>
                                                <option value="Flour" @if (Request::get('business_unit') == 'Flour') selected @endif>Flour</option>
                                            </select>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">From Date</label>
                                            <input type="date" class="form-control" name="from_date" value="{{ Request::get('from_date') }}">
                                        </div>
                                    </div> 

                                    <div class="mb-3 row">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">To Date</label>
                                            <input type="date" class="form-control" name="to_date" value="{{ Request::get('to_date') }}">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('employee_location')) || !empty(Request::get('is_active')) || !empty(Request::get('emp_name')) ||  !empty(Request::get('m')) || !empty(Request::get('y')))
                                            <a href="{{ url('monthly-attendance') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2">Reset Filter</a>
                                        @endif
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" >Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                
                    </div>
                </div> 
                <div class="card-body">
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                            <table class="table gs-0 gy-4 dataTable table-row-bordered" id="tableAllEmployeeAttendance">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-3">Employee Code</th>
                                        <th class="text-center">Employee Name</th>
                                        <th class="text-center">Date of Joining</th>
                                        <th class="text-center">Department</th>
                                        <th class="text-center">Designation</th>
                                        <th class="text-center">Business Unit</th>
                                        <th class="text-center">Shift</th>
                                        <th class="text-center">Absent</th>
                                        <th class="text-center">DA</th>
                                        <th class="text-center">Leave Days</th>
                                        <th class="text-center">Off Days</th>
                                        <th class="text-center">Patrol</th>
                                        <th class="text-center">Salary Days</th>
                                        <th class="text-center">Extra Working</th>
                                        <th class="ps-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                    @if(!empty($all_attendances))
                                        @foreach($all_attendances as $all_attendance)
                                            <tr>
                                                <td class="ps-3">{{ $all_attendance['employee_code'] }}</td>
                                                <td class="text-center">{{$all_attendance['employee_name']}}</td>
                                                <td class="text-center">{{$all_attendance['employee_doj']}}</td>
                                                <td class="text-center">{{$all_attendance['department_name']}}</td>
                                                <td class="text-center">{{$all_attendance['designation_name']}}</td>
                                                <td class="text-center">{{$all_attendance['business_unit']}}</td>
                                                <td class="text-center">{{$all_attendance['Shift']}}</td>
                                                <td class="text-center">{{$all_attendance['absent']}}</td>
                                                <td class="text-center">{{$all_attendance['DA']}}</td>
                                                <td class="text-center">{{$all_attendance['total_leaves']}}</td>
                                                <td class="text-center">{{$all_attendance['off_days_count']}}</td>
                                                <td class="text-center">{{$all_attendance['Patrol']}}</td>
                                                <td class="text-center">{{$all_attendance['salary_days']}}</td>
                                                <td class="text-center">{{$all_attendance['salary_days']}}</td>
                                                <td class="ps-4">
                                                    <a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="View Attendance">
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

    <form class="form" id="downloadbutton" action="{{url('/monthly-attendance/download-monthly-attendance')}}" method="POST">
        @csrf
        <div class="modal fade" id="download_modal_pdf" uploaddex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">

                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Download Employee Monthly Attendance</h4>
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

                            {{-- <input name="m" type="hidden" value="{{$month}}"> --}}
                            {{-- <input name="y" type="hidden" value="{{$year}}"> --}}
                            <input name="emp_name" type="hidden" value="{{Request::get('emp_name') }}">
                            <input name="is_active" type="hidden" value="{{Request::get('is_active') }}">
                            <input name="employee_location" type="hidden" value="{{Request::get('employee_location')}}">

                            <div class="col-md-12 mt-3 fv-row">
                                <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) monthly attendance  report,
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

</div>

    

@endsection



@push('js-link')

<script src="{{url('assets/js/attendance/all-employee-manage-attendance.js')}}"></script>
    
@endpush