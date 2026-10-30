@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    @if (empty(Request::get('interview_month')) && empty(Request::get('interview_date')) && empty(Request::get('interview_name')) && empty(Request::get('interview_status')) && empty(Request::get('interview_type')))
                        Manage Interviews ( {{date('F Y') }})
                    @elseif (!empty(Request::get('interview_month')) && empty(Request::get('interview_name')) && empty(Request::get('interview_status')) && empty(Request::get('interview_type')) && empty(Request::get('interview_date')))
                        Manage Interviews ({{date('F-Y', strtotime(Request::get('interview_month')))}})
                    @elseif (!empty(Request::get('interview_date')) && empty(Request::get('interview_month')) && empty(Request::get('interview_name')) && empty(Request::get('interview_status')) && empty(Request::get('interview_type')))
                        Manage Interviews ({{date('d-F-Y', strtotime(Request::get('interview_date')))}})
                    @else 
                        Manage Interviews
                    @endif    
                
                </h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#schedule_interview">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Schedule Interview</a>
                </div>
         
            </div>
           
        </div>
       
    </div>
    
        <div class="app-content flex-column-fluid">
            
            <div class="app-container">
                
                <div class="card mb-5 mb-xl-8">
                    @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null))
                        <div class="card-header border-0 pt-5">

                            <div class="card-title">
                                <div class="d-flex align-items-center  my-1">
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
                                
                                <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('interview_name')) ? $filter : '' }} {{!empty(Request::get('interview_date')) ? $filter : '' }} {{!empty(Request::get('interview_status')) ? $filter : '' }} {{!empty(Request::get('interview_type')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            
                                <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                                Filter</a>
                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                    <form action="{{ url('interviews') }}" method="GET">
                                        <div class="px-7 py-5">
                                            <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                        </div>

                                        <div class="separator border-gray-200"></div>
                                        <div class="px-7 py-5">
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Interviewer Name</label>
                                                <div>
                                                    <select class="form-select" data-kt-select2="true" name="interview_name" id="company_emp_name" data-employee-select data-scope="active">
                                                        <option value="">Select Name</option>
                                                        @include('include.employee-selected-options', ['selected' => Request::get('interview_name')])
                                                    </select>
                                                </div>
                                                
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Interview Date</label>
                                                <div class=" d-flex align-items-center">
                                                    <input type="date" class="form-control" placeholder="Select a date" name="interview_date" @if(!empty(Request::get('interview_date'))) value="{{ Request::get('interview_date') }}" @endif id="interview-date">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Interview Month</label>
                                                <div class=" d-flex align-items-center">
                                                    <input type="month" class="form-control" placeholder="Select a month" name="interview_month" @if(!empty(Request::get('interview_month'))) value="{{ Request::get('interview_month') }}"  @endif id="interview-month">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Interview Status</label>
                                                <div>
                                                    <select class="form-select" data-kt-select2="true" name="interview_status" >
                                                        <option value="">Select status</option>
                                                        @if(!empty($interview_status_level))
                                                            @foreach($interview_status_level as $data)
                                                                <option value="{{$data['interview_status_level']}}" @if(Request::get('interview_status') == $data['interview_status_level']) selected @endif>{{$data['interview_status_level']}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Interview Type</label>
                                                <div>
                                                    <select class="form-select" data-kt-select2="true" name="interview_type" >
                                                        <option value="">Select Type</option>
                                                        <option value="Physical" @if(Request::get('interview_type') == 'Physical') selected @endif>Physical Interview</option>
                                                        <option value="Online" @if(Request::get('interview_type') == 'Online') selected @endif>Online Interview</option>
                                                    </select>
                                                </div>
                                            </div>
                                        
                                            <div class="d-flex justify-content-end">
                                                @if (!empty(Request::get('interview_name')) || !empty(Request::get('interview_date')) || !empty(Request::get('interview_status')) || !empty(Request::get('interview_type')))
                                                    <a href="{{ url('interviews') }}" class="btn btn-sm btn-danger  me-2">Reset Filter</a>
                                                @endif
                                                <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div> 
                    @endif                
                    <div class="card-body py-3">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive popup-visible ">
                                <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                                                
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="d-none"></th>
                                            <th class="ps-4">Ref ID</th>
                                            <th>Candidate Name</th>
                                            <th>1st Interviewer</th>
                                            <th>2nd Interviewer</th>
                                            <th>Timing</th>
                                            <th>Deptartment</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Interviewer Remarks</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($interviews))
                                            @foreach($interviews as $interview)
                                                <tr>
                                                   
                                                    <td class="ps-4  d-none">{{ $interview['candidate_contact_no'] }}</td>
                                                    <td class="ps-4">{{ $interview['interview_id'] }}</td>
                                                    <td>{!!CustomHelper::getInterviewCandidateDiv($interview['candidate_id'])!!}</td>
                                                    <td>{!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer1']); !!}   
                                                    </td>
                                                    <td>@if(!empty($interview['interview_interviewer2']))
                                                        {!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer2']) !!}
                                                        @else
                                                            <span >N\A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $interview['interview_date'] }} - {{ date('h:i A', strtotime($interview['interview_time'])) }}</td>
                                                    <td>{{ $interview['department_name'] }}</td>
                                                    <td>
                                                        {{$interview['interview_type']}}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-success text-center d-inline-block font-weight-bold">{{$interview['interview_status_level']}}</span>
                                                    </td>
                                                    <td>
                                                        @if (empty($interview['interview_interviewer_remarks']) && $interview['interview_interviewer_remarks'] == '')
                                                            <span class="badge badge-light-danger text-center d-inline-block font-weight-bold">Not given</span>
                                                        @else
                                                            <span class="badge badge-light-success text-center d-inline-block font-weight-bold">Given</span>
                                                        @endif
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
                
                
                <form id="add_interview">
                    @csrf
                    <div class="modal fade" id="schedule_interview" tabindex="-1" aria-hidden="true">
                        
                        <div class="modal-dialog modal-dialog-scrollable mw-750px">
                            
                            <div class="modal-content rounded">
                                
                                    <div class="modal-header">
                                        <h4 class="modal-title pl-4">Schedule Interview</h4>
                                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                            <span class="svg-icon svg-icon-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                
                                
                                <div class="modal-body scroll-y pt-0 pb-15">
                                        
                                    <div class="row g-4 mt-1">
                                        <div class="col-md-12 requisition_request">
                                            <label class="required fs-6 fw-semibold mb-2">Requisition Reference</label>
                                            <select class="form-select select" data-control="select2" data-hide-search="false" data-placeholder="Select Requisition"  name="requisition_reference" id="requisition_id" required=""> 
                                                <option value="">Select Requisition</option>
                                                    @if(!empty($requisitions))
                                                        @foreach($requisitions as $requisition)
                                                            <option value="{{ $requisition['requisition_id'] }}" data-id = '{{ $requisition['requisition_id']}}' data-title = '{{ $requisition['requisition_job_title']}}' data-department = '{{ $requisition['requisition_department']}}' data-employee = '{{ $requisition['requisition_added_by']}}'>{{ $requisition['requisition_id'] }} - {{ $requisition['requisition_job_title'] }} - {{ $requisition['department_name'] }} - {!! CustomHelper::getEmpProfileDiv($requisition['requisition_added_by']) !!}</option>
                                                        @endforeach
                                                    @endif
                                            </select>
                                        </div>
                                    
                                        <div class="col-md-12">
                                            <label class="required fs-6 fw-semibold mb-2">Interview Type</label>
                                            <select class="form-select select" data-control="select2" data-hide-search="false" data-placeholder="Select Interview Type" id="interview_type" name="interview_type" required=""> 
                                                <option value="">Select Interview Type</option>
                                                <option value="Online">Online Interview</option>
                                                <option value="Physical" selected>Physical Interview</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 child-div-interview-type d-none">
                                            <label class="required fs-6 fw-semibold mb-2" id="label-interview-room">Interviewer Room</label>
                                            <select class="form-select select" data-control="select2" data-hide-search="true" id="interview_room" name="room_no" required="required">
                                               
                                                @if(!empty($office_rooms))
                                                    @foreach ($office_rooms as $office_room)
                                                        <option value="{{$office_room['room_id']}}" >{{$office_room['room_name']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-6 child-div-interview-type d-none ">
                                            <label class="required fs-6 fw-semibold mb-2" id="label-interview-link">Interview Link</label>
                                            <input type="url" class="form-control" id="interview_link" name="interview_link" required="required" placeholder="Interview Link">
                                        </div>
                                    
                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2">Interview Date</label>
                                            <input class="form-control" autocomplete="off" placeholder="Select a date" id="interview_daterangepicker" name="interview_date" required="" min="{{ date('Y-m-d') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2">Interview Time</label>
                                            <input type="time" class="form-control" name="interview_time" required=""/>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2">Expected Duration Time</label>
                                            <select class="form-select select" data-control="select2" data-hide-search="true" name="duration" required="required">
                                                <option value="10">10 Minutes</option>
                                                <option value="20">20 Minutes</option>
                                                <option value="30">30 Minutes</option>
                                                <option value="40">40 Minutes</option>
                                                <option value="50">50 Minutes</option>
                                                <option value="60">60 Minutes</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2">Interview Department</label>
                                            <select class="form-select select" data-control="select2" data-hide-search="true" data-placeholder="Select Department" name="interview_department" id="requistion_department" required="required">
                                                <option value="">Select Department</option>
                                                @if(!empty($departments))
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department['department_id'] }}">{{ $department['department_name'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2">Job Title</label>
                                            <input type="text" class="form-control " placeholder="Enter Job Title" name="interview_jobtitle" required="required"/>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2">Interviewer 1st</label>
                                            <select class="form-select select " data-control="select2" data-hide-search="true" data-placeholder="Select Interviewer" name="interviewer_1" id="requisition_interviewer" required="required" data-employee-select data-scope="active">
                                                <option value="">Select Interviwer</option>
                                                @include('include.employee-selected-options', ['selected' => null])
                                            </select>
                                        </div>
        
                                        <div class="col-md-6">
                                            <label class="fs-6 fw-semibold mb-2">Interviewer 2nd</label>
                                            <select class="form-select select" data-control="select2" data-hide-search="true"  name="interviewer_2" data-employee-select data-scope="active">
                                                <option value="">No 2nd Interviewer</option>
                                                @include('include.employee-selected-options', ['selected' => null])
                                            </select>
                                        </div>


                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2">Interview Round</label>
                                            <select class="form-select select" data-control="select2" data-hide-search="true" name="interview_round" required="required">
                                                <option value="1st">1st</option>
                                                <option value="2nd">2nd</option>
                                                <option value="3rd">3rd</option>
                                                <option value="4th">4th</option>
                                                <option value="5th">5th</option>
                                                <option value="6th">6th</option>
                                                <option value="7th">7th</option>
                                                <option value="8th">8th</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="fs-6 fw-semibold mb-2">Notice Period</label>
                                            <select class="form-select select" data-control="select2" data-hide-search="true" data-placeholder="Enter Notice Period (Optional)" name="notice_period">
                                                <option value="">Select Notice Period</option>
                                                @foreach($notice_period as $notice_value)
                                                    <option value="{{ $notice_value }}">{{ $notice_value }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="fs-6 fw-semibold mb-2 interview_request">Candidate Names</label>
                                            <input type="hidden" name="new_candidate" value="new"> 
                                            <input type="hidden" name="candidate_id" value=""> 
                                            
                                            <select class="form-select select interview_request" data-control="select2" data-hide-search="true" name="">
                                                <option value="">Select Candidate Name</option>
                                                @if(!empty($interview_candidate_name))
                                                    @foreach($interview_candidate_name as $candidate_name)
                                                        <option value="{{ $candidate_name['candidate_id'] }}" 
                                                        data-id = '{{ $candidate_name['candidate_id'] }}' data-name ='{{ $candidate_name['candidate_name'] }}' data-current-sal = '{{ $candidate_name['interview_current_salary'] }}' data-exp-sal = '{{ $candidate_name['interview_expected_salary'] }}' data-contact = '{{ $candidate_name['candidate_contact_no'] }}' data-email ='{{ $candidate_name['candidate_email'] }}'> {{ $candidate_name['candidate_name'] }} - {{ $candidate_name['interview_jobtitle'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="autocomplete">
                                                <label class="required fs-6 fw-semibold mb-2">Candidate Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Candidate Name" id="candidate_interview_name" name="candidate_name" required="required" autocomplete="off"   />
                                            </div>   
                                        </div> 

                                        <div class="col-md-6">
                                            <label class="fs-6 fw-semibold mb-2">Candidate Email</label>
                                            <input type="email" class="form-control " placeholder="Enter Candidate Email" name="candidate_email" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2" id="candidate_cv">Candidate Cv</label>
                                            <input type="file" class="form-control" accept=".docx,.txt,application/pdf,image/*"  name="candidate_cv" required/>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="required fs-6 fw-semibold mb-2">Candidate Contact No</label>
                                            <input type="number" class="form-control " placeholder="Enter Candidate contact number" name="contact_number" required/>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Current Salary</label>
                                            <input type="text" class="form-control" placeholder="Enter Current Salary(optional)" name="current_salary"/>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Expected Salary</label>
                                            <input type="text" class="form-control " placeholder="Enter Expected Salary(optional)" name="expected_salary"/>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="fs-6 fw-semibold mb-2">Additional Remarks</label>
                                            <textarea class="form-control" rows="3" name="additional_remarks" placeholder="Enter Additional Remarks(optional)"></textarea>
                                        </div>

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
                </form>
                
            </div>
            
        </div>
    
</div>

@endsection



@push('js-link')

<script src="{{url('assets/js/interview/all-interviews.js')}}"></script>

<script>
var candidate_interviews = @php echo $candidate_interviews @endphp;
</script>
    
@endpush