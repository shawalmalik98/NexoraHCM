@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
						 
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Attendance Requests   
                    @if(count($all_requests) >=500)
                        @if(empty(Request::get('attendance')) && empty(Request::get('emp_status')) && empty(Request::get('emp_name')))
                            (Showing Recent 500 Requests)
                        @else
                            @if(!empty(Request::get('attendance')))
                            (All Records)
                            @endif
                        
                        @endif
                    @endif
                </h1> 
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                @if(count($all_requests) >=500)
                    @if(empty(Request::get('attendance')))
                        
                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                            <a href="{{ url('attendance-requests?attendance=all') }}"   class="btn btn-sm fw-bold btn-primary" >View All Attendance Request</a>
                        </div>

                    @else
                        @if(!empty(Request::get('attendance')))
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <a href="{{ url('attendance-requests') }}"   class="btn btn-sm fw-bold btn-primary" >Show Recent 500 Records</a>
                            </div>
                        @endif
                    @endif
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

            
                        <div class="d-flex align-items-center  on-check d-none">
                        
                            @if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1)

                                <a href="javascript:void(0)"  class="attendnace_request text-white  btn btn-sm fw-bold btn-success" data-value="Approve" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve Request" >Approve</a>
                               
                                <a href="javascript:void(0)"  class="attendnace_request text-white  mx-2 btn btn-sm fw-bold btn-danger" data-value="DisApprove" data-bs-toggle="tooltip" data-bs-placement="top" title="Disapprove Request">DisApprove</a>
                            @else

                                <a href="javascript:void(0)"  class="attendnace_request text-white btn btn-sm fw-bold btn-success" data-value="LeadApprove" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve Request">Approve</a>
                               
                                <a href="javascript:void(0)"  class="attendnace_request  text-white btn btn-sm fw-bold btn-danger mx-2" data-value="LeadDisApprove" data-bs-toggle="tooltip" data-bs-placement="top" title="Disapprove Request">DisApprove</a>
                                
                            @endif
                        </div>
                    </div>

                        <div class="card-toolbar">
                            @php $filter = 'btn-color-primary'; @endphp

                            <div class="px-7 py-5">
                                <div>
                                    <select class="form-select" data-kt-select2="true"  name="req_status" id="req_status">
                                        <option value="">All Requests</option>
                                        <option value="Pending" @if(Request::get('req_status') == '1') selected @endif>Pending Requests</option>
                                        <option value="Approved" @if(Request::get('req_status') == '2') selected @endif>Approved Requests</option>
                                        <option value="Disapproved" @if(Request::get('req_status') == '3') selected @endif>Disapproved Requests</option>
                                    </select>
                                </div>
                            </div>
                        
                            <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('employee_location')) ? $filter : '' }} {{ !empty(Request::get('emp_status')) ? $filter : '' }} {{ !empty(Request::get('emp_name')) ? $filter : '' }} {{ !empty(Request::get('req_status')) ? $filter : '' }} {{ !empty(Request::get('request_for_date')) ? $filter : '' }} {{ !empty(Request::get('request_submited_date')) ? $filter : '' }}"  data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        
                            <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                </svg>
                            </span> 
                            Filter</a>
                        
                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                
                                    <form action="{{ url('attendance-requests') }}" method="GET">
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
                                                    <select class="form-select " data-kt-select2="true" data-placeholder="Select option" name="emp_status" id="employee_data">
                                                        <option value="1" @if(!empty(Request::get('emp_status')) && Request::get('emp_status') == '1') selected @endif>Active Employees</option>
                                                        <option value="2" @if(!empty(Request::get('emp_status')) && Request::get('emp_status') == '2') selected @endif>De-active Employees</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">By Name</label>
                                                <div>
                                                    <select class="form-select " data-kt-select2="true"  name="emp_name" id="company_emp_name">
                                                        @if(!empty($employees_data))
                                                        <option value="">All Employees</option>
                                                            @foreach($employees_data as $data)
                                                                <option value="{{$data['employee_id']}}"@if(Request::get('emp_name') == $data['employee_id']) selected @endif>{{$data['employee_code']. ' - ' .$data['employee_name']}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Status</label>
                                                <div>
                                                    <select class="form-select" data-kt-select2="true"  name="req_status">
                                                        <option value="">All Requests</option>
                                                        <option value="1" @if(Request::get('req_status') == '1') selected @endif>Pending Requests</option>
                                                        <option value="2" @if(Request::get('req_status') == '2') selected @endif>Approved Requests</option>
                                                        <option value="3" @if(Request::get('req_status') == '3') selected @endif>Disapproved Requests</option>
                                                    </select>
                                                </div>
                                            </div>

                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department']== 2)

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Request For Applied Date</label>
                                                    <div class="position-relative d-flex align-items-center">
                                                        <input type="date" class="form-control" placeholder="Select a date" name="request_for_date" max="{{ date('Y-m-d') }}" @if(!empty(Request::get('request_for_date'))) value="{{ Request::get('request_for_date') }}" @endif />
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Submission Date</label>
                                                    <div class="position-relative d-flex align-items-center">
                                                        <input type="date" class="form-control" placeholder="Select a date" name="request_submited_date" max="{{ date('Y-m-d') }}" @if(!empty(Request::get('request_submited_date'))) value="{{ Request::get('request_submited_date') }}" @endif />
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-end">
                                                @if(!empty(Request::get('employee_location')) || !empty(Request::get('emp_status')) || !empty(Request::get('emp_name')) || !empty(Request::get('request_for_date')) || !empty(Request::get('request_submited_date')))
                                            
                                                    <a href="{{ url('attendance-requests') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                                        >Reset Filter</a>
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
                        
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                                                            
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th></th>
                                        <th>Request By</th>
                                        <th>For Date</th>
                                        <th>Type</th>
                                        <th>Message</th>
                                        <th>Applied Date</th>
                                        <th>Check In Time</th>
                                        <th>Check Out Time</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Approval(s)">Approval(s)</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Response By HR">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($all_requests))
                                        @foreach ($all_requests as $all_request)
                                            <tr @if(request()->get('employee_id') != '' && $all_request['request_employee_id'] == request()->get('employee_id') && in_array($all_request['request_attendance_date'],$dates)) style="background-color:bisque;" @endif >
                                                <td>
                                                    @if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1)

                                                        @if($all_request['request_approved_by_hr'] != 1 )
                                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                <i class="form-check-input checked" type="checkbox"  style="cursor: not-allowed;" disabled></i>
                                                            </div>  
                                                        @else
                                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                <input class="form-check-input checked" type="checkbox" data-id="{{$all_request['attendance_request_id']}}" value="{{$all_request['attendance_request_id']}}"   data-hr="{{ $all_request['request_approved_by_hr'] }}" />
                                                            </div>                                                            
                                                        @endif
                                                    @else
                                                        @if(in_array($all_request['attendance_request_id'],$responsed_request) || $all_request['request_approved_by_hr'] != 1)
                                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                <i class="form-check-input checked" type="checkbox"  style="cursor: not-allowed;" disabled></i>
                                                            </div>
                                                        @else
                                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                <input class="form-check-input checked"  type="checkbox" data-id="{{$all_request['attendance_request_id']}}" value="{{$all_request['attendance_request_id']}}"   data-hr="{{ $all_request['request_approved_by_hr'] }}" />
                                                            </div>
                                                        @endif    
                                                    @endif 
                                                </td>

                                                <td>{!! CustomHelper::getEmpProfileDiv($all_request['employee_id']) !!}</td>
                                                
                                                <td>{{ $all_request['request_attendance_date'] }}</td>
                                                <td>{{ $all_request['request_attendance_status'] }}</td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0);" class="message_btn" data-bs-toggle="modal" data-bs-target="#message_modal" data-bs-message="{{ $all_request['request_attendance_message'] }}">
                                                        <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Message" aria-hidden="true">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                                <td>{{ date('h:i A d M, Y', strtotime($all_request['request_submition_date'])) }}</td>
                                                <td class="text-center">
                                                    @if(!empty($all_request['attendance_checkin_time']))
                                                        <span class="badge badge-light-success text-center font-weight-bold"> {{$all_request['attendance_checkin_time']}}</span>
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Edit Attendance CheckIn Time  {{$all_request['attendance_checkin_time']}}" onclick="editAttendanceCheckInTime('{{$all_request['attendance_request_id']}}','{{$all_request['attendance_checkin_time']}}','{{$all_request['request_approved_by_hr']}}')">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"  disabled style="cursor:not-allowed">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="text-center"> 
                                                    @if(!empty($all_request['attendance_checkout_time']))
                                                        <span class="badge badge-light-success text-center font-weight-bold"> {{$all_request['attendance_checkout_time']}}</span>
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Edit Attendance CheckOut Time {{$all_request['attendance_checkout_time']}}" onclick="editAttendanceCheckOutTime('{{$all_request['attendance_request_id']}}','{{$all_request['attendance_checkout_time']}}','{{$all_request['request_approved_by_hr']}}')">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"  disabled style="cursor:not-allowed">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                    
                                                    <a href="javascript:void(0);" class="manager_modal_status_btn"  data-request-id="{{$all_request['attendance_request_id'] }}"  data-bs-toggle="tooltip" data-bs-placement="top" title="Approval(s)">
                                                        
                                                        @if(in_array($all_request['attendance_request_id'],$responsed_request))
                                                            <i class="fas fa-user-check text-success"></i>
                                                            @else
                                                            <i class="fas fa-user-check"></i>
                                                        @endif
                                                    </a>
                                                </td>
                                               
                                                <td class="text-center">
                                                    @if($all_request['request_approved_by_hr'] == 1)
                                                        <span class="badge badge-secondary text-center  text-muted font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="HR didn't respond to this request yet.">Pending</span>
                                                    @elseif($all_request['request_approved_by_hr'] == 2)
                                                        <span class="badge badge-light-success text-center font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $all_request['request_approved_date_hr'] }}">Approved</span>
                                                    @else
                                                        <span class="badge badge-light-danger text-center font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $all_request['request_approved_date_hr'] }}">Disapproved</span>
                                                    @endif
                                                </td>
                                               
                                               
                                                <td class="text-center">
                                                    @if($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1)

                                                        @if($all_request['request_approved_by_hr'] != 1 )
                                                            
                                                            <i class="fa-regular fa-circle-check  text-success mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                        @else

                                                            <i class="fa-regular fa-circle-check attendnace_request text-success mx-2"  data-id="{{$all_request['attendance_request_id'] }}" data-value="Approve" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve Request" style="font-size: 20px;cursor: pointer;"></i>
                                                            
                                                        @endif

                                                         @if($all_request['request_approved_by_hr'] != 1 )

                                                            <i class="fa-regular fa-circle-xmark  text-warning mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                            
                                                        
                                                        @else
                                                            
                                                            <i class="fa-regular fa-circle-xmark attendnace_request text-warning mx-2" data-value="DisApprove" data-id="{{$all_request['attendance_request_id'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Disapprove Request" style="font-size: 20px;cursor: pointer;"></i>

                                                        @endif

                                                    @else

                                                        @if(in_array($all_request['attendance_request_id'],$responsed_request) || $all_request['request_approved_by_hr'] != 1)
                                                            
                                                        <i class="fa-regular fa-circle-check  text-success mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                        
                                                        @else

                                                        <i class="fa-regular fa-circle-check attendnace_request text-success mx-2"  data-id="{{$all_request['attendance_request_id'] }}" data-value="LeadApprove" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve Request" style="font-size: 20px;cursor: pointer;"></i>

                                                        @endif
                                                       
                                                       
                                                        @if(in_array($all_request['attendance_request_id'],$responsed_request) || $all_request['request_approved_by_hr'] != 1)
                                                                <i class="fa-regular fa-circle-xmark  text-warning mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                          
                                                        @else
                                                            
                                                            <i class="fa-regular fa-circle-xmark attendnace_request text-warning mx-2" data-value="LeadDisApprove" data-id="{{$all_request['attendance_request_id'] }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Disapprove Request" style="font-size: 20px;cursor: pointer;"></i>

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
    
    {{-- Approve Request Modal Starts Here --}}
        <div class="modal fade" id="modal-success" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top">
                <form class="request_submit">
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
                            <p class="pt-4 fs-6 msg">Are you sure to <b>Approve</b>  selected attendance request(s) ?</p>
                            @if($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 1)
                                <textarea class="form-control " rows="3" name="approvermessage" placeholder="Message" id="approvermessage" required></textarea>
                            @endif
                            </div>

                        <div class="modal-footer">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="approve_btn" class="btn btn-primary">
                                <span class="indicator-label">Yes, Approve</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {{-- Approve Request Modal Ends Here --}}

    {{-- DisApprove Request Modal Starts Here --}}
        <div class="modal fade" id="modal-danger" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top">
                <form class="request_submit">
                    @csrf
                    <div class="modal-content rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Confirmation Alert</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                                    </svg>
                                </span>
                            </button>
                        </div>

                        <div class="modal-body pt-0 ">
                            <p class="pt-5 fs-6 msg">Are you sure to <b>Disapprove</b> selected attendance request(s) ?</p>
                            @if($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 1)
                                <textarea class="form-control " rows="3" name="disapprovermessage" placeholder="Message" id="disapprovermessage" required></textarea>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="disapprove_btn" class="btn btn-primary">
                                <span class="indicator-label">Yes, Disapprove</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {{-- DisApprove Request Modal Ends Here --}}

    {{-- Request Message View Modal Starts Here --}}
        <div class="modal fade" id="message_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content rounded">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                        <h4 class="modal-title pl-4">Request Message</h4>
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
                        <p id="message_body" class=""></p>
                    </div>
                </div>
            </div>
        </div>
    {{-- Request Message View Modal Ends Here --}}


</div>

{{-- Edit Attendance Request Check In time Modal Starts Here --}}
    <form id="update_attendance_request_check_in_time" class="form">
        @csrf
        <div class="modal fade" id="update_attendance_request_checkintime" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Edit Check In Time Attendance Request </h4>
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
                            
                            <input type="hidden" name="attendance_req_id" />
                            <div class="col-md-12 mb-8 fv-row check-in-time-div">
                                <label class="required fs-6 fw-semibold mb-2">Check In Time</label>
                                <div class="position-relative d-flex align-items-center">
                                  
                                    <input type="time" step="1" class="form-control" name="check_in_time"
                                            placeholder="Enter in Time" >
                                </div>
                            </div>
                            
                            <table class="table table-row-bordered" id="request-check-in-time-response">
                            </table>
                        </div>
                        <div class="modal-footer justify-content-center modal-footer-action-btn">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="edit_attendance_checkin_submit_btn" class="btn btn-primary">
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
{{-- Edit Attendance Request Check Out time Modal Ends Here --}}

{{-- Edit Attendance Request Check Out time Modal Starts Here --}}
    <form id="update_attendance_request_check_out_time" class="form">
        @csrf
        <div class="modal fade" id="update_attendance_request_checkouttime" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Edit Check Out Time Attendance Request </h4>
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
                            
                            <input type="hidden" name="attendance_req_id" />
                            <div class="col-md-12 mb-8 fv-row check-out-time-div">
                                <label class="required fs-6 fw-semibold mb-2">Check Out Time</label>
                                <div class="position-relative d-flex align-items-center">
                                    
                                    <input type="time" step="1" class="form-control" name="check_out_time"
                                            placeholder="Enter out Time" >
                                </div>
                            </div>

                            <table class="table table-row-bordered" id="request-check-out-time-response">
                            </table>
                          
                            
                        </div>
                        <div class="modal-footer justify-content-center modal-footer-action-check-out-btn">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="edit_attendance_checkout_submit_btn" class="btn btn-primary">
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
{{-- Edit Attendance Request Check In time Modal Ends Here --}}







@endsection



@push('js-link')
<script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>

<script src="{{url('assets/js/attendance/all-employees-request.js')}}"></script>
  
@endpush