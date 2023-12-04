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
                            <div class="text-gray-900 fw-bold fs-2" id="present">
                                {{ collect($attendance)->where('att_check_overall_status', 'Present')->count(); }}
                            </div>
                            <div class="fw-semibold text-gray-400">On Time</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6  filter-record mb-4" data-value='Late Coming,Early out'   style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="lateComing">
                                {{ collect($attendance)->whereIn('att_check_overall_status', ['Late Coming','Early out'])->count(); }}
                            </div>
                            <div class="fw-semibold text-gray-400">Late/Early</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6  filter-record mb-4" data-value="Half Day"  style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="HalfDay">
                                {{ collect($attendance)->where('att_check_overall_status', 'Half day')->count(); }}
                            </div>
                            <div class="fw-semibold text-gray-400">Half Day</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6  filter-record mb-4" data-value="Absent"  style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="absent">
                                {{ collect($attendance)->where('att_check_overall_status', 'Absent')->count(); }}
                            </div>
                            <div class="fw-semibold text-gray-400">Absent </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6 filter-record mb-4" data-value="Off Day,Holiday"  style="cursor: pointer">
                    <div class="card">
                        <div class="p-3">
                            <div class="text-gray-900 fw-bold fs-2" id="offdays">
                                {{ collect($attendance)->where('att_check_overall_status', 'Offdays')->count(); }}
                            </div>
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
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('department_id')) ? $filter : '' }} {{ !empty(Request::get('business_unit')) ? $filter : '' }} {{ !empty(Request::get('is_active')) ? $filter : '' }} {{ !empty(Request::get('d')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
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
                                                    <option value="Candyland" @if (Request::get('business_unit') == 'Candyland') selected @endif>CandyLand</option>
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

                                        <div class="mb-3">
                                        
                                            <label class="form-label fw-semibold">Employee Group </label>
                                            
                                            <div>
                                                <select class="form-select " data-kt-select2="true" data-placeholder="Select Employee group" name="employee_group">
                                                    <option value="">Select Employee group</option>
                                                    <option value="All" @if (Request::get('employee_group') == 'All') selected @endif>All Employee Group</option>
                                                    @if(!empty($employee_groups))
                                                        @foreach ($employee_groups as $employee_group)
                                                            <option value="{{ $employee_group['employee_group_id'] }}"
                                                            @if(!empty(Request::get('employee_group')) && Request::get('employee_group') == $employee_group['employee_group_id']) selected @endif>
                                                                {{ $employee_group['group_name'] }}</option>
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
                                            
                                            <label class="form-label fw-semibold">Select Date</label>
                                            
                                            <div class="position-relative d-flex align-items-center">
                                                <input type="date" class="form-control" placeholder="Select a date" name="d"  @if(!empty(Request::get('d'))) value="{{ Request::get('d') }}" @else value="{{ date('Y-m-d') }}"  @endif  max="{{ date('Y-m-d') }}" required/>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('business_unit')) || !empty(Request::get('department_id')) || !empty(Request::get('employee_group')) || !empty(Request::get('d')) || !empty(Request::get('is_active')))
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
                                            @if($login_user[0]['employee_department'] == '34')
                                                <th class="text-center">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                  
                                    <tbody>
                                        @if (!empty($attendance))
                                            @foreach ($attendance as $attendances)
                                                <tr>
                                                    <td class="ps-8">{{ $attendances['employee_code'] }} - {{ $attendances['employee_name'] }} </td>
                                                    <td class="text-center">
                                                        <p class="text-dark fw-bold mb-1 fs-6"> {{ $attendances['department_name'] }}</p>
                                                        <p class="text-muted fw-semibold text-muted fs-7">{{$attendances['designation_name'] }}</p>
                                                    </td>
                                                    <td class="text-center"> 
                                                        {{ $attendances['business_unit'] ?? '-' }}
                                                    </td>

                                                    <td  class="text-center"> 
                                                        {{ $attendances['att_check_in_time'] }}
                                                    </td>

                                                    <td  class="text-center"> 
                                                        {{ $attendances['att_check_out_time'] }}
                                                    </td> 

                                                    <td class="text-center">
                                                        @if ($attendances['att_check_overall_status'] == 'Present' || $attendances['att_check_overall_status'] == 'Half day' || $attendances['att_check_overall_status'] == 'Late Coming' || $attendances['att_check_overall_status'] == 'Early out')
                                                            {{ CustomHelper::calculate_working_hour($attendances['att_check_out_status'],$attendances['att_check_in_time'],$attendances['att_check_out_time'],$attendances['att_created_at'],$attendances['att_updated_at']) }}
                                                        @elseif(in_array($attendances['att_check_overall_status'],$manage_leaves) || $attendances['att_check_overall_status'] == 'Absent')
                                                            -
                                                        @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                            -
                                                        @endif
                                                    </td>

                                                    <td class="text-center"> 
                                                        @if ($attendances['att_check_overall_status'] == 'Present')
                                                            <p class="text-success"><i class="fa-solid fa-calendar-check text-success"></i> -- On Time</p>
                                                        @elseif($attendances['att_check_overall_status'] == 'Half day')
                                                            <p class="text-danger"><i class="fa-solid fa-calendar-check text-danger"></i> -- Half Day</p>
                                                        @elseif($attendances['att_check_overall_status'] == 'Late Coming')
                                                            <p class="text-muted"><i class="fa-solid fa-calendar-check text-muted"></i> -- Late Coming</p>
                                                        @elseif(in_array($attendances['att_check_overall_status'], $manage_leaves))
                                                            <p class="text-info"><i class="fa-solid fa-user-large-slash"></i> -- {{$attendances['att_check_overall_status']}}</p>
                                                        @elseif($attendances['att_check_overall_status'] == 'Absent')
                                                            <p class="text-danger"><i class="fa-solid fa-xmark text-danger"></i> -- Absent</p>
                                                        @elseif($attendances['att_check_overall_status'] == 'Early out')
                                                            <p class="text-info"><i class="fa-solid fa-clock text-info"></i> -- Early out</p>
                                                        @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                            <p class="text-warning"><i class="fa-solid fa-umbrella-beach text-warning"></i> -- Holiday</p>
                                                        @elseif(substr($attendances['att_check_overall_status'] , 0, 7) == 'Offdays')
                                                            <p class="text-warning"><i class="fa-solid fa-umbrella-beach text-warning"></i> -- Off Day</p>
                                                        @endif
                                                    </td>
                                                    @if($login_user[0]['employee_department'] == '34')
                                                        <td class="text-center">
                                                            @if($attendances['att_check_in_status'] == '1' || $attendances['att_check_out_status'] == '1')
                                                                <i class="fa-regular fa-circle-xmark delete_attendance_btn text-danger mx-2" data-value="LeadDisApprove" data-id="{{ $attendances['attendance_id'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Attendance" style="font-size: 20px;cursor: pointer;"></i>
                                                            @else
                                                                <i class="fa-regular fa-circle-xmark text-danger mx-2" disabled data-value="LeadDisApprove" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Attendance" style="font-size: 20px;cursor: not-allowed;"></i>
                                                            @endif
                                                        </td>
                                                    @endif
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
                                <div class ="row">
                                    <div class="col-md-10 mb-8 mt-3 fv-row" id="selectedgroup"> 
                                        <label class="required fs-6 fw-semibold mb-2">Employees Group</label>
                                            <select class="form-select" id="groups_all"  data-control="select2" multiple="multiple" data-hide-search="true" data-placeholder="Select Group" name="emp_group[]" required> 
                                                @if(!empty($employee_groups))
                                                    @foreach ($employee_groups as $groups)
                                                        <option value="{{$groups['employee_group_id']}}">
                                                            {{ $groups['group_name']}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                    </div>
                                    <div class="col-md-2 mb-4 mt-10 fv-row">
                                            
                                        <label class="fs-6 fw-semibold mb-2">All Groups</label>
                                            <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                            {{-- <input type = "hidden" name="loc_count" value="{{$loc_count}}"> --}}
                                            <input class="form-check-input h-20px w-20px" type="checkbox" id="group_all" name="select_all" value="Select All" /> 
                                        </label>
                                    </div> 
                                </div>  
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

            <div class="modal fade" id="delete_attendance_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-top">
                    <form id="delete_attendance_record">
                        @csrf
                        <div class="modal-content rounded">
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Confirmation Alert</h4>
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
                                <p class="pt-4 fs-6 msg">Are you sure to <b>Delete</b> this attendance record ?</p>
                                <textarea class="form-control " rows="3" name="deletion_remarks" placeholder="Reason" id="approvermessage" required></textarea>
                                <input type="hidden" name="attendance_id" id="attendance_id_input">
                            </div>
    
                            <div class="modal-footer">
                                <button type="submit" id="delete_btn" class="btn btn-danger">
                                    <span class="indicator-label">Yes, Delete</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    
    </div>
</div>

@endsection



@push('js-link')
<script src="{{url('assets/js/attendance/daily-attendance-report.js?v=3')}}"></script>
@endpush