@extends('layout.main')



@push('css-link')
table.dataTable
{
 overflow-x:hidden !important;
 overflow-y:auto !important;
}
@endpush



@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
						 
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                   
                    @if (empty($month) && empty($year))
                        Overtime Management ({{ date('F') . '-' . date('Y') }})
                    @else
                        Overtime Management ({{ date("F", mktime(0, 0, 0, $month, 10)) }}-{{ $year }})
                    @endif
                </h1>   
            </div>
          
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @php $download_overtime = false;  @endphp
                @if(!empty($all_attendaces))
                    <div class="m-0" id="overtime_button">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">											
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Overtime Report
                        </a>  
                    </div>
                @endif        
              
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
                    
                    <div class="card-toolbar">
                        @php $filter = 'btn-color-primary'; @endphp
                    
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('employee_location')) ? $filter : '' }} {{!empty(Request::get('is_active')) ? $filter : '' }} {{!empty(Request::get('emp_name')) ? $filter : '' }} {{!empty(Request::get('m')) ? $filter : '' }} {{!empty(Request::get('y')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                          
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('overtime-management') }}" method="GET">
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
                                                <select class="form-select " data-kt-select2="true" data-placeholder="Select Name" name="is_active"
                                                required="" id="employee_data">
                                                <option value="1" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '1') selected @endif>Active
                                                    Employees</option>
                                                <option value="2" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '2') selected @endif>De-active
                                                    Employees</option>
                                                <option value="3" @if (!empty(Request::get('is_active')) && Request::get('is_active') == '3') selected @endif>All
                                                        Employees</option>
                                            </select>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="mb-3">
                                            
                                            <label class="form-label fw-semibold">
                                                <span>Employee Name</span>  
                                            </label>
                                            
                                            <div>
                                                <select class="form-select " data-kt-select2="true"  name="emp_name"
                                                 id="company_emp_name" data-employee-select data-scope="active">
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
                                        
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('employee_location')) || !empty(Request::get('is_active')) || !empty(Request::get('emp_name')) || !empty(Request::get('m')) || !empty(Request::get('y')))
                                                <a href="{{ url('overtime-management') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                                    >Reset Filter</a>
                                             @endif
                                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" >Close</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                        </div>
                                        
                                    </div>
                                 </form>
                            </div>
                       
                    </div>
                </div> 
                <div class="card-body py-3">
                  						 
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                        
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="tableEmployeeOverTime" data-server-paginated="true" aria-describedby="tableEmployee_info">
                                                                                                            
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-7">Employee Name</th>
                                        <th>Shift Duration</th>
                                        <th>Extra Time Worked</th>
                                        <th>Overtime Period</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
									@if (!empty($all_attendaces))
                                        @foreach (array_reverse(is_array($all_attendaces) ? $all_attendaces : []) as $all_attendance)
                                             
                                            @if(!empty($all_attendance['attendance']))
                                                
                                                @foreach ($all_attendance['attendance'] as $key=>$atty)
                                                    @php 
                                                      
                                                        $employee_shift_times =  CustomHelper::calculate_shift_timings_shift_management($atty['att_shift_start_time'],$atty['att_shift_end_time'],$atty['att_check_in_status'],$atty['att_check_out_status']);
                                                        $employee_working_hours =  CustomHelper::calculate_working_hour($atty['att_check_out_status'],$atty['att_check_in_time'],$atty['att_check_out_time'],$atty['att_created_at'],$atty['att_updated_at']);
                                                    
                                                    @endphp
                                                    @if( $employee_working_hours > $employee_shift_times)
                                                        @php $download_overtime = true; @endphp
                                                        <tr>
                                                            <td class="ps-6">{!! CustomHelper::getEmpProfileDiv($all_attendance['employee_id']) !!}</td>
                                                            <td class="ps-6">  {{ $employee_shift_times }}</td>
                                                            <td class="ps-6">  {{CustomHelper::calculate_total_overtime_monthly($all_attendance['attendance'])}}</td>
                                                            <td class="ps-6">  {{date('F-Y', strtotime($atty['att_for_year'].'-'.$atty['att_for_month'] ))}}</td>
                                                            <td>
                                                                <a href="{{ url('overtime-management/overtime-detail') }}/{{ $all_attendance['employee_id'] }}?is_active={{$all_attendance['employee_isactive']}}&y={{$atty['att_for_year']}}&m={{$atty['att_for_month']}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Overtime detail">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @php break; @endphp;
                                                    @endif
                                                   
                                                @endforeach
                                            @endif
                                        @endforeach
									@endif
								</tbody>
								
                            
                            </table>
                            @include('include.server-pagination', ['paginator' => $pagination ?? null])
                        </div>
                    </div>  
                </div>
                 
            </div>
            
           
        </div>

        <form class="form" id="downloadbutton" action="{{ url('overtime-management/overtime-management-download') }}" method="POST">
            @csrf
            <div class="modal fade" id="download_modal_pdf"uploaddex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-650px">
                    <div class="modal-content rounded">
                    
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Download Overtime Management Report</h4>
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

                                <input name="m" type="hidden" value="{{$month}}">

                                <input name="y" type="hidden" value="{{$year}}">

                                <input name="emp_name" type="hidden" value="{{Request::get('emp_name')}}">
                        
                                <div class="col-md-12 mt-3 fv-row">
                                    <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) overtime management report,
                                    Please type <b>TODP-ID</b> (case sensitive) to confirm.</p>
                                    <input type="text" class="form-control " name="confirmation_code" placeholder="Enter Confirmation Code" required="required" autocomplete="off" onpaste="return false">
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
    

</div>

@endsection



@push('js-link')

<script>
    var download_button =  '{{ $download_overtime}}';
</script>

<script src="{{url('assets/js/overtime-management/overtime-sheet.js')}}?v=2"></script>

@endpush