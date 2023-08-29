@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My Meetings</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-default">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Schedule a new meeting </a>   
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
                </div> 
                <div class="card-body py-3">
                    
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer ">
                        <div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Ref ID</th>
                                        <th>Meeting Title</th>
                                        <th>Meeting Host</th>
                                        <th>Meeting Room/Location</th>
                                        <th>Meeting Link</th>
                                        <th>Meeting Time</th>
                                        <th>Meeting Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @if (!empty($meetings))
                                            @foreach ($meetings as $meeting)
                                                <tr>
                                                    <td class="ps-4">{{ $meeting['meeting_id'] }}</td>
                                                    <td>{{ $meeting['meeting_title'] }}</td>
                                                    <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($meeting['employee_id']) !!}</td>
                                                    <td class="ps-6">{{$meeting['room_name'] ?? 'N\A'}}</td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0);" class="message_btn" data-bs-toggle="modal" data-bs-target="#message_modal" data-bs-message="{{$meeting['meeting_link'] }}">
                                                            <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Link" aria-hidden="true">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                    <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                    <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </td>
                                                    <td>{{ $meeting['meeting_time'] }}</td>
                                                    <td>
                                                        @if ($meeting['meeting_status'] == '1')
                                                        <span class="badge badge-light-success font-weight-bold">Meeting Scheduled</span>
                                                        @elseif($meeting['meeting_status'] == '2')
                                                            <span class="badge badge-light-success font-weight-bold">Meeting Compeleted</span>
                                                        @else

                                                        <span class="badge badge-light-danger font-weight-bold">Meeting Canceled</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions 
                                                       
                                                            <span class="svg-icon svg-icon-5 m-0">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                        </a>
                                                       
                                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                            <div class="menu-item px-3">
                                                                <a href="{{ url('my-meeting-detail') }}/{{$meeting['meeting_id'] }}" class="menu-link px-3" data-bs-toggle="tooltip" title="View Meeting detail">
                                                                    View
                                                                </a>
                                                            @if($meeting['host_id'] == session('user_session'))
                                                                </div>
                                                                @if($meeting['meeting_status'] == 1)
                                                                    <div class="menu-item px-3">
                                                                    
                                                                        <a href="javascript:void(0);" class="menu-link px-3" data-bs-toggle="tooltip" title="Meeting Completed"  onclick="MeetingStatus('{{$meeting['meeting_id']}}','2')">
                                                                            Completed
                                                                        </a>  
                                                                    </div>
                                                                    <div class="menu-item px-3">
                                                                    
                                                                            <a href="javascript:void(0);" class="menu-link px-3" data-bs-toggle="tooltip" title="Meeting Cancel" onclick="MeetingStatus('{{$meeting['meeting_id']}}','3')">
                                                                                Cancel
                                                                            </a>  
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
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
            
            
            <form id="schedule_meeting_form" class="form">
                @CSRF
                <div class="modal fade" id="modal-default" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-800px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                            <h4 class="modal-title pl-4">Schedule a meeting</h4>
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
                                
                                <div class="row g-9 mb-8 mt-2">
                                    <div class="col-md-12 fv-row">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                        <span class="required">Meeting Type</span>
                                        </label>

                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select myselect2" data-control="select2" data-hide-search="false" data-placeholder="Select Meeting Type" id="meeting_type" name="meeting_type" required=""> 
                                                <option value="">Select Meeting Type</option>
                                                <option value="Online">Online Meeting</option>
                                                <option value="Physical">Physical Meeting</option>
                                                    
                                            </select>
                                        </div>
                                    
                                    </div>
                                </div>

                                <div class="row g-9 mb-8 child-div-meeting-type d-none" >
                                    
                                    {{-- <div class="col-md-6 fv-row">
                                       
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required" id="label-meeting-room">Room No/Location</span>
                                        </label>

                                        <div class="position-relative d-flex align-items-center">
                                            <input type="text" class="form-control" id="meeting_room" name="meeting_room" required="required" placeholder="Meeting Room/Location">
                                        </div>

                                    </div> --}}
                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required" id="label-meeting-room">Room No/Location</span>
                                        </label>
                                        
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select select" data-control="select2" data-hide-search="true"   id="meeting_room" name="meeting_room" required="required" >
                                                <option value="">Select Room</option>

                                                @if(!empty($office_rooms))

                                                    @foreach ($office_rooms as $office_room)
                                                        <option value="{{$office_room['room_id']}}">{{$office_room['room_name']}}</option>
                                                    @endforeach

                                                @endif
                                                
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                            <span class="required" id="label-meeting-link">Meeting Link</span>
                                        </label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="url" class="form-control" id="meeting_link" name="meeting_link" required="required" placeholder="Meeting Link">
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                                <div class="row g-9 mb-8 " >
                                    
                                    <div class="col-md-6 fv-row">
                                        {{--begin::Label--}}
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Meeting Title</span>
                                        </label>

                                        <div class="position-relative d-flex align-items-center">
                                            <input type="text" class="form-control" name="meeting_title" required="required" placeholder="For eg : Policy Planning">
                                        </div>

                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                            <span class="required">Meeting With</span>
                                        </label>

                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select myselect2" data-control="select2" multiple="multiple" data-hide-search="false" data-close-on-select="false" data-placeholder="Select Employees For Meeting Invitation" name="meeting_users[]" required=""> 
                                                @if (!empty($active_employees))
                                                    @foreach ($active_employees as $active_employee)
                                                        @if($active_employee['employee_id'] != session('user_session'))
                                                            <option value="{{ $active_employee['employee_id'] }}">{{ $active_employee['employee_name'] }} - {{ $active_employee['employee_code'] }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row g-9 mb-8" >
                                    <div class="col-md-12 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span>Meeting Description</span>
                                        </label>
                                            <textarea class="form-control" id="meeting_description" rows="4" placeholder="Enter Meeting Description" name="meeting_description"></textarea>
                                    </div>
                                </div>
                                <div class="row g-9 mb-8" >
                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Meeting Date</span>
                                        </label>
                                    
                                        <input type="date" class="form-control "  name="meeting_date" min="{{date('Y-m-d')}}"  required/>
                                    </div>

                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Meeting Time</span>
                                        </label>
                                    
                                        <input type="time" class="form-control " name="meeting_time" required/>
                                    </div>
                                </div>   
                            
                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">
                            
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit-btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                    {{--end::Modal - New Target--}}
            </form>
            

              {{--- Cancel Meeting Modal --------------}}
            <div class="modal fade" tabindex="-1" id="cancel-meeting">
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
                        <form id="cancel_meeting" class="form" >
                            @csrf
                            
                            <div class="modal-body">
                                <input type="hidden" class="form-control" name="meeting_id" />
                                <input type="hidden" class="form-control" name="meeting_status" />
                                <p id="meeting-alert" ></p>

                                <textarea class="form-control" rows="3" name="conclusion" placeholder="Conclusion" required></textarea>
                               
                            </div>
                
                            <div class="modal-footer">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="cancel_meeting_btn" class="btn btn-primary">
                                        <span class="indicator-label">Submit</span>
                                        <span class="indicator-progress">Please wait 
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                            </div>
                        </form>    
                    </div>
                </div>
            </div>


            {{-- Link View Modal Starts Here --}}
            <div class="modal fade" id="message_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content rounded">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                        <h4 class="modal-title pl-4">Meeting Link</h4>
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
                        <p id="message_body"></p>
                    </div>
                </div>
            </div>
            </div>
            {{-- Link View Modal Ends Here --}}
            
        </div>
        
    </div>
    
</div>

@endsection

@push('js-link')

<script src="{{url('assets/js/meeting/my-meetings.js')}}"></script>

@endpush