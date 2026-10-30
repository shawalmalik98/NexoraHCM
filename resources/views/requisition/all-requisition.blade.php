@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                   All Requisition
                </h1>
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

                    <div class="card-toolbar" >
                        @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('employee_location')) ? $filter : '' }} {{!empty(Request::get('requisition_department')) ? $filter : '' }} {{!empty(Request::get('emp_status')) ? $filter : '' }} {{!empty(Request::get('emp_name')) ? $filter : '' }} {{!empty(Request::get('requisition_job_type')) ? $filter : '' }} {{!empty(Request::get('requisition_status')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                            
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bold">Filter Options</div>
                            </div>

                            <div class="separator border-gray-200"></div>

                            <form action="{{ url('requisition') }}" method="GET">
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

                                    @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null))
                                        <div class="mb-3">
                                            
                                            <label class="form-label fw-semibold">Requisition Department</label>
                                            
                                            <div>
                                                <select class="form-select" data-kt-select2="true" data-placeholder="Select Department" name="requisition_department"
                                                aria-required="true">
                                                    <option value="">Select Department</option>
                                                    @if(!empty($departments))
                                                        @foreach($departments as $department)
                                                            <option value="{{$department['department_id']}}"@if (Request::get('requisition_department') == $department['department_id']) selected @endif>{{$department['department_name']}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            
                                        </div>
                                    @endif    
                                    
                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Employee Status</label>
                                        
                                        <div>
                                            <select class="form-select" data-kt-select2="true" data-placeholder="Select option"  name="emp_status" id="employee_data">
                                                <option value="1" @if ( Request::get('emp_status') == '1') selected @endif>Active Employees</option>
                                                <option value="2" @if ( Request::get('emp_status') == '2') selected @endif>De-active Employees</option>
                                            </select>
                                        
                                        </div>
                                        
                                    </div>
                                 
                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Requisition Request By</label>
                                        
                                        <div>
                                            <select class="form-select" data-kt-select2="true" name="emp_name"
                                            id="company_emp_name" aria-required="true" data-employee-select data-scope="team">
                                                <option value="">All Employees</option>
                                                @include('include.employee-selected-options', ['selected' => Request::get('emp_name')])
                                            </select>
                                        </div>
                                        
                                    </div>
                                     <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Requisition Job Type</label>
                                        
                                        <div>
                                            
                                            <select class="form-select" data-control="select2" data-hide-search="true" data-placeholder="Select a Job Type" name="requisition_job_type">
                                                <option value="">Select Job Type</option>
                                                <option value="Intern" @if ( Request::get('requisition_job_type') == 'Intern') selected @endif>Intern</option>  
                                                <option value="Probation" @if ( Request::get('requisition_job_type') == 'Probation') selected @endif> Probation</option>
                                                <option value="Permanent" @if ( Request::get('requisition_job_type') == 'Permanent') selected @endif> Permanent</option>
                                                <option value="PartTime" @if ( Request::get('requisition_job_type') == 'PartTime') selected @endif> Part Time</option>   
                                            </select>
                                            
                                        </div>
                                        
                                    </div>

                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Requisition Status</label>
                                        
                                        <div>
                                            <select class="form-select" data-kt-select2="true"  name="requisition_status">
                                                <option value="">Select Requisition Status</option>
                                                @if(!empty($requistion_status_levels))
                                                    @foreach($requistion_status_levels as $requistion_status_level)
                                                        <option value="{{ $requistion_status_level}}"@if (Request::get('requisition_status') ==  $requistion_status_level) selected @endif>{{ $requistion_status_level}}</option>
                                                    @endforeach
                                                @endif

                                            </select>
                                        
                                        </div>
                                        
                                    </div>
                    
                                    <div class="d-flex justify-content-end">
                                        @if(!empty(Request::get('employee_location')) || !empty(Request::get('requisition_department')) || !empty(Request::get('emp_status')) || !empty(Request::get('requisition_added_by')) || !empty(Request::get('requisition_job_type')) || !empty(Request::get('requisition_status')))
                                        
                                            <a href="{{ url('requisition') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">
                                            Reset Filter</a>
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

                    <div class="table-responsive popup-visible ">

                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">

                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Ref ID</th>
                                        <th>Department</th>
                                        <th>Job Title</th>
                                        <th>Job Type</th>
                                        <th>Created By</th>
                                        <th>No Of Positions</th>
                                        <th>Created At</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Response">Response</th>
                                        <th>Total Interviews</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @if(!empty($requisitions))
                                        @foreach ($requisitions as $Requisition)
                                            <tr @if(request()->get('requisition-id') != '' && $Requisition['requisition_id'] == request()->get('requisition-id')) style="background-color:bisque;" @endif>
                                                <td class="ps-8">{{ $Requisition['requisition_id'] }}</td>
                                                <td>{{ $Requisition['department_name'] }}</td>
                                                <td>{{ $Requisition['requisition_job_title'] }}</td>
                                                <td>{{ $Requisition['requisition_job_type'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($Requisition['requisition_added_by']) !!}</td>
                                                <td>{{ $Requisition['no_of_position'] }}</td>
                                                <td>{{ $Requisition['create_at'] }}</td>
                                                <td>

                                                    @if($Requisition['requisition_status'] == 'Pending')
                                                        <span class="badge badge-light-secondary text-center text-muted d-inline-block  font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Pending</span>
                                                    @elseif($Requisition['requisition_status'] == 'Interview Scheduled')
                                                        <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Interview Scheduled</span>
                                                    @elseif($Requisition['requisition_status'] == 'Hold for more interviews')
                                                        <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Hold for more Interviews</span>
                                                    @elseif($Requisition['requisition_status'] == 'Candidate Shortlisted')
                                                        <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Candidate Shortlisted</span>
                                                    @elseif($Requisition['requisition_status'] == 'Candidate Rejected')
                                                        <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Candidate Rejected</span>
                                                    @elseif($Requisition['requisition_status'] == 'Offer Placed')
                                                        <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Offer Placed</span>
                                                    @elseif($Requisition['requisition_status'] == 'Offer Rejected')
                                                        <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Offer Rejected</span>
                                                    @elseif($Requisition['requisition_status'] == "Candidate didn't Appeared")
                                                        <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Candidate didn't Appeared</span>
                                                    @elseif($Requisition['requisition_status'] == 'Candidate Hired')
                                                        <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Candidate Hired</span>
                                                    @elseif($Requisition['requisition_status'] == 'Interview Cancelled')
                                                        <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">Interview Cancelled</span>
                                                    @else
                                                        <span class="badge badge-secondary text-center  text-muted font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top">{{$Requisition['requisition_status']}}</span>
                                                    @endif

                                                </td>
                                                <td class="text-center">          
                                                    {{(array_key_exists($Requisition['requisition_id'], $interview_count )) ? $interview_count[$Requisition['requisition_id']] : 0}}
                                                </td>
                                                 <td>
                                                    <a href="{{ url('requisition/requisitiondetail') . '/' . $Requisition['requisition_id'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Requisition detail">
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

@endsection

@push('js-link')
<script src="{{url('assets/js/requisition/all-requisition.js')}}"></script>

@endpush
