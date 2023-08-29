@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
						 
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My Attendance Requests</h1> 
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                 
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#attendance_request_apply">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Request Now</a>   
                </div>
              
            
            </div>
            
        </div>
    </div>
       
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">

                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
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
                    </div>   
                    
                </div> 
                <div class="card-body">
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">
                        
                            <table class="table gs-0 gy-4 dataTable table-row-bordered" id="system_datatable">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="text-center">Request Date</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Message</th>
                                        <th class="text-center">Applied Date</th>
                                        <th class="text-center">Check In Time</th>
                                        <th class="text-center">Check Out Time</th>
                                        <th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Approval(s)">Approval(s)</th>
                                        <th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Response By HR">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($my_requests))
                                        @foreach($my_requests as $my_request)
                                            <tr @if(request()->get('request-id') != '' && $my_request['attendance_request_id'] == request()->get('request-id')) style="background-color:bisque;" @endif>
                                                <td class="text-center">{{ $my_request['request_attendance_date'] }}</td>
                                                <td class="text-center">{{ $my_request['request_attendance_status'] }}</td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0);" class="message_btn" data-bs-toggle="modal" data-bs-target="#message_modal" data-bs-message="{{ $my_request['request_attendance_message'] }}">
                                                        <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Message" aria-hidden="true">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="text-center">{{ date('h:i A d M, Y', strtotime($my_request['request_submition_date'])) }}</td>

                                                 <td class="text-center">
                                                    @if(!empty($my_request['request_attendance_status'] == 'On Time Check In'))
                                                        <span class="badge badge-light-success text-center font-weight-bold"> {{$my_request['attendance_checkin_time']}}</span>
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Edit Attendance CheckIn Time  {{$my_request['attendance_checkin_time']}}" onclick="editAttendanceCheckInTime('{{$my_request['attendance_request_id']}}')">
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
                                                    @if(!empty($my_request['request_attendance_status'] == 'On Time Check Out'))
                                                        <span class="badge badge-light-success text-center font-weight-bold"> {{$my_request['attendance_checkout_time']}}</span>
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Edit Attendance CheckOut Time {{$my_request['attendance_checkout_time']}}" onclick="editAttendanceCheckOutTime('{{$my_request['attendance_request_id']}}')">
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
                                                  
                                                    <a href="javascript:void(0);" class="manager_modal_status_btn"  data-request-id="{{$my_request['attendance_request_id'] }}"  data-bs-toggle="tooltip" data-bs-placement="top" title="Approval(s)">
                                                        @if(in_array($my_request['attendance_request_id'],$responsed_request))
                                                            <i class="fas fa-user-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-user-check"></i>
                                                        @endif
                                                    </a>
                                                </td>
                                               
                                                <td class="text-center">
                                                    @if ($my_request['request_approved_by_hr'] == 1)
                                                        <span class="badge badge-secondary text-center text-muted font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="HR didn't respond to this request yet.">Pending</span>
                                                    @elseif($my_request['request_approved_by_hr'] == 2)
                                                        <span class="badge badge-light-success text-center font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $my_request['request_approved_date_hr'] }}">Approved</span>
                                                    @else
                                                        <span class="badge badge-light-danger text-center font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $my_request['request_approved_date_hr'] }}">Disapproved</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if(in_array($my_request['attendance_request_id'],$responsed_request) || $my_request['request_approved_by_hr'] != 1 || in_array($my_request['attendance_request_id'],$get_manager_response))
                                                         <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                            
                                                            <span class="svg-icon svg-icon-3" disabled style="font-size: 20px; cursor: not-allowed;">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>

                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                        
                                                            <span class="svg-icon svg-icon-3" disabled style="font-size: 20px; cursor: not-allowed;">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        
                                                    @else

                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit_btn" data-bs-toggle="tooltip" title="Edit Attendance Request" onclick="editAttendanceRequest('{{$my_request['attendance_request_id']}}', '{{$my_request['attendance_request_time_id']}}', '{{$my_request['request_attendance_date']}}','{{$my_request['request_attendance_status']}}', '{{$my_request['attendance_checkin_time']}}', '{{$my_request['attendance_checkout_time']}}', '{{$my_request['request_attendance_message']}}')">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Delete Attendance Request" onclick="deleteAttendanceRequest('{{$my_request['attendance_request_id']}}')">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                   

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

    {{-- New Request Modal Starts Here --}}
        <form id="send_new_request" class="form">
            @csrf
            <div class="modal fade" id="attendance_request_apply" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-650px">
                    <div class="modal-content rounded">
                        <div class="modal-content rounded">
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Apply For Attendance Request </h4>
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
                                
                                <div class="d-flex flex-column mb-8 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Attendance Day(s)</label>
                                    <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Attendance Days" name="days_assign" id="days_assign" required>
                                        <option value="">Select Attendance Day(s)</option>
                                        <option value="single">Single Day Request</option>
                                        <option value="multiple">Multiple Days Requests</option>
                                    </select>
                                </div>

                                <div class="from_to_date_input row g-9 mb-8 d-none">

                                    <div class="col-md-4">
                                        <label class="required fs-6 fw-semibold mb-2">From Date</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="date" class="form-control date-control" name="from_date" id="from_date"  max="{{date('Y-m-d')}}" required/>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="required fs-6 fw-semibold mb-2">To Date</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="date" class="form-control date-control" name="to_date" id="to_date" max="{{ date('Y-m-d') }}" required disabled/>
                                        </div>
                                    </div>

                                    <div class="col-md-4 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span>Total Days</span>
                                        </label>
                                       
                            
                                        <input type="text" class="form-control" placeholder="Total Days" id="days" readonly  />
                                    </div>
                                
                                
                                </div>

                                <div class="only_date_input col-md-12 mb-8 fv-row d-none">
                                    <label class="required fs-6 fw-semibold mb-2">Select Attendance Date</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <input type="date" class="form-control" name="attendance_date" id="attendance_date" max="{{ date('Y-m-d') }}" required/>
                                    </div>
                                </div>

                                <div class="d-flex flex-column mb-8 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">Please Mark My Attendance</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Please select the option below which you are willing to mark your attendance."></i>
                                    </label>
                                    <select class="form-select" data-control="select2" data-hide-search="true" data-placeholder="Select Attendance type" id="attendance_type" name="attendance_type" required> 
                                        <option value="" >Select Attendance Type</option>
                                        <option value="Present">As Present</option>
                                        <option value="Half day">As Half Day</option>
                                        <option value="On Time Check In">Update My Check In Time</option>
                                        <option value="On Time Check Out">Update My Check Out Time</option>
                                    </select>
                                </div>

                                <div class="main-check-in-div col-md-12 mb-8 fv-row d-none">
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
                            </div>
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
    {{-- New Request Modal Ends Here --}}


     {{-- Edit Attendance Request Modal Starts Here --}}
    <form id="update_attendance_request" class="form">
        @csrf
        <div class="modal fade" id="edit_attendance_request_apply" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Edit Apply Attendance Request </h4>
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
                            <input type="hidden" name="attendance_request_time_id" />
                            <div class="col-md-12 mb-8 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Select Attendance Date</label>
                                <div class="position-relative d-flex align-items-center">
                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                            <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                            <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <input type="date" class="form-control ps-12" name="edit_attendance_date" id="edit_attendance_date" max="{{ date('Y-m-d') }}" required/>
                                </div>
                            </div>

                            <div class="d-flex flex-column mb-8 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Please Mark My Attendance</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Please select the option below which you are willing to mark your attendance."></i>
                                </label>
                                <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Attendance type" id="edit_attendance_type" name="edit_attendance_type" required> 
                                    <option value="" >Select Attendance Type</option>
                                    <option value="Present">As Present</option>
                                    <option value="Half day">As Half Day</option>
                                    <option value="On Time Check In">Update My Check In Time</option>
                                    <option value="On Time Check Out">Update My Check Out Time</option>
                                </select>
                            </div>

                            <div class="edit-check-in-div col-md-12 mb-8 fv-row d-none">
                                    <label class="required fs-6 fw-semibold mb-2">Check In Time</label>
                                    <div class="position-relative d-flex align-items-center">
                                       
                                        <input type="time" step="1" class="form-control" name="check_in_time" placeholder="Enter in Time" id="edit_check_in_time">
                                    </div>
                                </div>

                                <div class="edit-check-out-div col-md-12 mb-8 fv-row d-none">
                                    <label class="required fs-6 fw-semibold mb-2">Check Out Time</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <input type="time" step="1" class="form-control" name="check_out_time"
                                            placeholder="Enter out Time"  id="edit_check_out_time"  >
                                    </div>
                                </div>

                            <div class="d-flex flex-column mb-8">
                                <label class="fs-6 fw-semibold mb-2 required">Enter Reason</label>
                                <textarea class="form-control " rows="3" name="edit_attendance_message" id="edit_kt_docs_maxlength_threshold3" maxlength="500" placeholder="Message" required></textarea>
                            </div>
                            
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="edit_attendance_submit_btn" class="btn btn-primary">
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
    {{-- Edit Attendance Modal Ends Here --}}

     {{--- Delete Attendance Request --------------}}
    <div class="modal fade" tabindex="-1" id="delete-attendance-request">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Confirmation Alert</h3>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1 bg-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="delete_attendance_request" class="form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="attendance_id" />
                        <p>Are you sure to delete this Attendance Request ?</p>
                    </div>
        
                    <div class="modal-footer">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="delete_submit_btn" class="btn btn-primary">
                            <span class="indicator-label">Delete</span>
                            <span class="indicator-progress">Please wait 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>    
            </div>
        </div>
    </div>
    {{-- Request Message Modal Starts Here --}}
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
                    </div>
                    <div class="modal-body scroll-y">
                        <p id="message_body"></p>
                    </div>
                </div>
            </div>
        </div>
    {{-- Request Message Modal Ends Here --}}

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
                            <table class="table table-row-bordered" id="request-check-in-time-response">
                            </table>
                        </div>
                        <div class="modal-footer justify-content-center modal-footer-action-btn">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
                            <table class="table table-row-bordered" id="request-check-out-time-response">
                            </table> 
                        </div>
                        <div class="modal-footer justify-content-center modal-footer-action-check-out-btn">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

    

@endsection



@push('js-link')
<script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>
<script src="{{url('assets/js/attendance/my-request.js')}}"></script>
    
@endpush