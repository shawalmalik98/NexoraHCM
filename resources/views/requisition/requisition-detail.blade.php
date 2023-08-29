@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Requisition Detail</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#requisition_status">Change Requisition Status</a>
                </div>
             
                <div class="m-0">
                     <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
                      
                </div>
         
            </div>
           
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
      
        <div class="app-container">
            <div class="row mb-5">
                <div class="col-md-5 card scroll scroll-pull" data-scroll="true" data-wheel-propagation="true" style="height:1130px">
                    <div class="card card-flush">
                        <div class="card-body pt-5"  >
                            {{-- begin::Nav --}}
                            <ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9"
                                id="custom-tabs-four-tab" role="tablist">

                                <li class="nav-item col mx-0 p-0">
                                    <a class="nav-link active d-flex justify-content-center w-100 border-0 h-100"
                                        id="custom-tabs-requisition-detail-tab" data-bs-toggle="pill"
                                        href="#custom-tabs-requisition-detail" role="tab"
                                        aria-controls="custom-tabs-requisition-detail" aria-selected="true">
                                        <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Requisition Detail</span>
                                        <span
                                            class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                    </a>
                                </li>

                                <li class="nav-item col mx-0 px-0">
                                    <a class="nav-link d-flex justify-content-center w-100 border-0 h-100"
                                        id="custom-tabs-requisition-status-tab" data-bs-toggle="pill"
                                        href="#custom-tabs-requisition-status" role="tab"
                                        aria-controls="custom-tabs-requisition-status" aria-selected="false">
                                        <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Status Log Activity</span>
                                        <span
                                            class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                    </a>
                                </li>
                                <span class="position-absolute z-index-1 bottom-0 w-100 h-5px bg-light rounded"></span>
                            </ul>

                            <div class="tab-content"  id="custom-tabs-four-tabContent">

                                <div class="tab-pane fade show active" id="custom-tabs-requisition-detail" role="tabpanel"
                                    aria-labelledby="custom-tabs-requisition-detail-tab">

                                    <div class="card-body p-0">
                            
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition ID</span>
                                                </div>
                                                
                                                <span>
                                                    @if (!empty($requisitions[0]['requisition_id']))
                                                        {{ $requisitions[0]['requisition_id'] }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            
                                                
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Department Name</span>
                                                </div>
                                                    
                                                <span >
                                                    @if (!empty($requisitions[0]['department_name']))
                                                        {{ $requisitions[0]['department_name'] }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                    
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition Job Title</span>
                                                </div>
                                                    
                                                <span >
                                                    @if (!empty($requisitions[0]['requisition_job_title']))
                                                        {{  $requisitions[0]['requisition_job_title'] }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            
                                                
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition Job Type</span>
                                                </div>
                                                
                                                <span >
                                                    @if (!empty($requisitions[0]['requisition_job_type']))
                                                        {{  $requisitions[0]['requisition_job_type'] }}
                                                    @else
                                                        N/A
                                                    @endif
                                                
                                                </span>
                                            
                                                
                                            </div>
                                            
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
                            
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Expected Salary</span>
                                                </div>
                                                
                                                <span >
                                                    @if (!empty($requisitions[0]['requisition_expected_salary']))
                                                        {{  $requisitions[0]['requisition_expected_salary'] }}
                                                    @else
                                                        N/A
                                                    @endif                                        
                                                </span>
                                                
                                            </div>
                                            
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Shift Start Time</span>
                                                </div>
                                                
                                                <span >
                                                    @if (!empty($requisitions[0]['requisition_shift_start_time']))
                                                        {{  $requisitions[0]['requisition_shift_start_time'] }}
                                                    @else
                                                        N/A
                                                    @endif                                        
                                                </span>
                                                
                                            </div>
                                            
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Shift End Time</span>
                                                </div>
                                                
                                                <span >
                                                    @if (!empty($requisitions[0]['requisition_shift_end_time']))
                                                        {{  $requisitions[0]['requisition_shift_end_time'] }}
                                                    @else
                                                        N/A
                                                    @endif                                       
                                                </span>
                                                
                                            </div>
                                            
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition Shift Time</span>
                                                </div>
                                            
                                                <span >
                                                    @if (!empty($requisitions[0]['requisition_shift_time']))
                                                        {{  $requisitions[0]['requisition_shift_time'] }}
                                                    @else
                                                        N/A
                                                    @endif                                      
                                                </span>
                                            
                                            </div>
                                            
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition Date Needed</span>
                                                </div>
                                            
                                                <span >
                                                    @if (!empty($requisitions[0]['requisition_date_needed']))
                                                        {{  $requisitions[0]['requisition_date_needed'] }}
                                                    @else
                                                        N/A
                                                    @endif                                       
                                                </span>
                                                
                                            </div>
                                            
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition Additional Info</span>
                                                </div>
                                            
                                                <span >
                                                    @if (!empty($requisitions[0]['requisition_additional_info']))
                                                        <a href="javascript:void(0);" class="message_btn" data-bs-toggle="modal" data-bs-target="#message_modal" data-message="{{ $requisitions[0]['requisition_additional_info'] }}">
                                                            <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Message" aria-hidden="true">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                    <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                    <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                                </svg>
                                                            </span>
                                                        </a>       
                                                    @else
                                                        N/A
                                                    @endif              
                                                                       
                                                </span>
                                                
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Created By</span>
                                                </div>
                                                
                                                    <span >
                                                        @if(!empty($requisitions[0]['requisition_added_by']))
                                                            {!! CustomHelper::getEmpProfileDiv($requisitions[0]['requisition_added_by']) !!}
                                                        @else
                                                            N/A
                                                        @endif    
                                                    </span>
                                                
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>

                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">No of Positions</span>
                                                </div>
                                                
                                                <span >
                                                    @if (!empty($requisitions[0]['no_of_position']))
                                                        {{ $requisitions[0]['no_of_position'] }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            
                                            </div>
                                            
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Office Location</span>
                                                </div>
                                                
                                                <span >
                                                    @if (!empty($requisitions[0]['location_title']))
                                                        {{ $requisitions[0]['location_title'] }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition Created At</span>
                                                </div>
                                                
                                                
                                                    <span >
                                                        @if (!empty($requisitions[0]['create_at']))
                                                            {{ $requisitions[0]['create_at'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </span>
                                            
                                                
                                            </div>
                                            
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Total Interview Count</span>
                                                </div>
                                                
                                                <span >
                                                    @if (!empty($interviews))
                                                        {{ count($interviews) }}
                                                    @else
                                                        0
                                                    @endif
                                                </span>
                                                
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition Status</span>
                                                </div>
                                                
                                                <span >

                                                    @if (!empty($requisitions[0]['requisition_status']))
                                                        {{ $requisitions[0]['requisition_status'] }}
                                                    @else
                                                        N/A
                                                    @endif                           
                                                </span>
                                                
                                            </div>
                                            
                                        </div>
                                        
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="custom-tabs-requisition-status" role="tabpanel"
                                    aria-labelledby="custom-tabs-requisition-status">
                                    <div class="card-body p-0">
                                        @if(!empty($requisitions_status))
                                            @foreach ($requisitions_status as $requisition_status )
                                                <div class="d-flex flex-stack">
                                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                        {{--begin:Author--}}
                                                        <div class="flex-grow-1 me-2">
                                                            <span class="text-gray-800  fs-6 fw-bold">
                                                                {!! CustomHelper::getEmpProfileDiv($requisition_status['requisition_status_created_by']) !!}
                                                            </span>
                                                        </div>
                                                        @if($requisition_status['requisition_status'] == 'Pending')
                                                            <span class="badge badge-secondary text-center  text-muted font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Pending</span>
                                                        @elseif($requisition_status['requisition_status'] == 'Interview Scheduled')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Interview Scheduled</span>
                                                        @elseif($requisition_status['requisition_status'] == 'Hold for more interviews')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Hold for more Interviews</span>
                                                        @elseif($requisition_status['requisition_status'] == 'Candidate Shortlisted')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Candidate Shortlisted</span>
                                                        @elseif($requisition_status['requisition_status'] == 'Candidate Rejected')
                                                            <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Candidate Rejected</span>
                                                        @elseif($requisition_status['requisition_status'] == 'Offer Placed')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Offer Placed</span>
                                                        @elseif($requisition_status['requisition_status'] == 'Offer Rejected')
                                                            <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Offer Rejected</span>
                                                        @elseif($requisition_status['requisition_status'] == "Candidate didn't Appeared")
                                                            <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Candidate didn't Appeared</span>
                                                        @elseif($requisition_status['requisition_status'] == 'Candidate Hired')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Candidate Hired</span>
                                                        @elseif($requisition_status['requisition_status'] == 'Interview Cancelled')
                                                            <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $requisition_status['requisition_status_created_at'] }}">Interview Cancelled</span>
                                                        @endif
                                                      

                                                    </div>
                                                </div>
                                                <div class="separator separator-dashed my-4"></div>
                                            @endforeach
                                       
                                        @endif
                                            
                                    </div>
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card card-flush mb-10">
                        <div class="card-header border-0 pt-5">
                            <div class="card-title">
                                Request with requisition
                            </div>
                        </div> 
                        <div class="card-body">
                            <div class="row g-9 mb-2">
                               <div class="table-responsive">
                                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                        <div class="table-responsive">
                                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                                                            
                                                <thead>
                                                    <tr class="fw-bold text-muted bg-light">
                                                        <th>Candidate Name</th>
                                                        <th>1st Interviewer</th>
                                                        <th>2nd Interviewer</th>
                                                        <th>Interview Timing</th>
                                                        <th>Interviewer Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($interviews))
                                                        @foreach($interviews as $interview)
                                                            <tr>
                                                                <td>{{ $interview['candidate_name'] }}</td>
                                                                <td>{!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer1']); !!}   
                                                                </td>
                                                                <td>
                                                                    @if(!empty($interview['interview_interviewer2']))
                                                                    {!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer2']) !!}
                                                                    @else
                                                                        N\A
                                                                    @endif
                                                                </td>
                                                                <td>{{ $interview['interview_date'] }} - {{ date('h:i A', strtotime($interview['interview_time'])) }}</td>
                                                                <td>
                                                                    <span class="badge badge-light-success text-center d-inline-block font-weight-bold">{{$interview['interview_status_level']}}</span>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ url('interviews/interviewdetail') . '/' . $interview['interview_id']}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Interview detail">
                                                                        <i class="fa fa-eye" aria-hidden="true"></i>
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

                </div>
            
            </div>

        </div>
    
    </div>

    <form id="requisition_status_candidate" class="form">
        @csrf
        <div class="modal fade" id="requisition_status" tabindex="-1" aria-hidden="true">
            
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Change Requisition Status</h4>
                    
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                        
                    </div>
                    
                    
            
                    {{--end::Modal header--}}
                    
                    <div class="modal-body scroll-y pt-0 ">                    
                        
                        <div class="d-flex flex-column  mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Requisition Status</label>

                            <input type="hidden" class="form-control "  name="requisition_id" value="{{$requisitions[0]['requisition_id'] ?? 'N/A' }}"  />
                            <input type="hidden" class="form-control "  name="employee_id"  value="{{$login_user[0]['employee_id'] ?? 'N/A' }}"  />

                            <select class="form-select requitionstatus" data-control="select2" data-hide-search="true" placeholder="Select Requisition Status" name="requisition_status" required="required">
                                <option value="">Pending</option>
                                @if(!empty($requistion_status_level) && !empty($requisitions))
                                    @foreach($requistion_status_level as $requistion_status)  
                                        <option value="{{$requistion_status}}" @if ( $requisitions[0]['requisition_status'] == $requistion_status) selected @endif>{{$requistion_status}}</option>
                                    @endforeach  
                                @endif         
                                

                            </select>

                        </div>

                        {{--end::Input group--}}
                        
                    </div>
                    
                    
                    <div class="modal-footer justify-content-center">
                    
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit_btn_status" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        {{--end::Modal - requisition Status--}}
    </form>

    <div class="modal fade" id="message_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content rounded">
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Requisition Additional Info</h4>
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
                        <p id="message_body" ></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js-link')

<script src="{{url('assets/js/requisition/requisition-detail.js')}}"></script>

@endpush