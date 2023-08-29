@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    {{-- begin::Toolbar --}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        {{-- begin::Toolbar container --}}
        <div class="app-container col-12 d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Help Desk  Detail</h1>
    
            </div>

          
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @php  $ticket_status = array_column($help_desk_status,'ticket_status');
                      $last_status =   end( $ticket_status);
                @endphp

                @if(!empty($help_desk_departments))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartment">											
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Add Department</a>   
                    </div>
                @endif

                @if(!empty($help_desk_employees))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployee">											
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Add Employee</a>   
                    </div>
                @endif
                @if($help_desk[0]['help_desk_loged_by'] != session('user_session'))
                    @if($last_status == 1)
                        <div class="m-0">
                            <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Chat Activate"  onclick="ticketStatusModal('{{$help_desk[0]['help_desk_id']}}','{{session('user_session')}}','2')">											
                            Chat Activate</a>   
                        </div>
                    @elseif($last_status == 2)
                        <div class="m-0">
                            <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Query Resolved From Department"  onclick="ticketStatusModal('{{$help_desk[0]['help_desk_id']}}','{{session('user_session')}}','4')">																				
                            Query Resolved</a>   
                        </div>
                    @endif
                @else
                    @if($last_status == 1)
                        <div class="m-0">
                            <a href="javascript:void(0);" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Ticket Rejected"  onclick="ticketStatusModal('{{$help_desk[0]['help_desk_id']}}','{{session('user_session')}}','3')">																				
                                Ticket Reject</a>
                        </div>									
                      
                    @elseif($last_status == 2)
                        <div class="m-0">
                            <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Query Resolved From Employee"  onclick="ticketStatusModal('{{$help_desk[0]['help_desk_id']}}','{{session('user_session')}}','5')">																				
                                Query Resolved</a>
                        </div>
                
                    @elseif($last_status == 3 || $last_status == 4 || $last_status == 5)
                    <div class="m-0">
                        <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Same Ticket Open Again"  onclick="ticketStatusModal('{{$help_desk[0]['help_desk_id']}}','{{session('user_session')}}','1')">																				
                            Open Ticket Again</a>
                    </div>
                    @endif
                  
                @endif
                <div class="m-0">
                    <a href="{{ url()->previous() }}" class="btn btn-sm fw-bold btn-primary">Go Back</a>
                </div>
            </div>
         
        </div>

    </div>

    <div class="app-content flex-column-fluid">
        {{-- begin::Content container --}}
        <div class="app-container">
            {{-- begin::Social - Activity --}}

            <div class="row">
                <div class="col-md-4 card mb-5">
                    <div class="card card-flush">
                        <div class="card-body pt-5">
                            {{-- begin::Nav --}}
                            <ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9"
                                id="custom-tabs-four-tab" role="tablist">

                                <li class="nav-item col mx-0 p-0">
                                    <a class="nav-link active d-flex justify-content-center w-100 border-0 h-100"
                                        id="custom-tabs-vehicle-detail-tab" data-bs-toggle="pill"
                                        href="#custom-tabs-vehicle-detail" role="tab"
                                        aria-controls="custom-tabs-vehicle-detail" aria-selected="true">
                                        <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Help Desk</span>
                                        <span
                                            class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                    </a>
                                </li>

                                <li class="nav-item col mx-0 px-0">
                                    <a class="nav-link d-flex justify-content-center w-100 border-0 h-100"
                                        id="custom-tabs-vehicle-maintenance-tab" data-bs-toggle="pill"
                                        href="#custom-tabs-vehicle-maintenance" role="tab"
                                        aria-controls="custom-tabs-vehicle-maintenance" aria-selected="false">
                                        <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Status Log Activity</span>
                                        <span
                                            class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                    </a>
                                </li>
                                <span class="position-absolute z-index-1 bottom-0 w-100 h-5px bg-light rounded"></span>
                            </ul>

                            <div class="tab-content" id="custom-tabs-four-tabContent">

                                <div class="tab-pane fade show active" id="custom-tabs-vehicle-detail" role="tabpanel"
                                    aria-labelledby="custom-tabs-vehicle-detail-tab">

                                    <div class="card-body p-0">
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                {{--begin:Author--}}
                                                 <div class="flex-grow-1 me-2">
                                                     <span class="text-gray-800  fs-6 fw-bold">Help Desk Host</span>
                                                 </div>
            
                                                    {!! CustomHelper::getEmpProfileDiv($help_desk[0]['help_desk_loged_by']) !!}
                                               
                                            </div>
                                        </div>
                                        <div class="separator separator-dashed my-4"></div>
                                        <div class="d-flex flex-stack">
                                             <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                 <div class="flex-grow-1 me-2">
                                                     <span class="text-gray-800  fs-6 fw-bold">Created At</span>
                                                 </div>
                                                <span class="fs-6">{{ date('d, F Y h:i A', strtotime($help_desk[0]['created_time'])) }}</span>
                                             </div>
                                        </div>
                                         <div class="separator separator-dashed my-4"></div>
                                         <div class="d-flex flex-stack">
                                             <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                 <div class="flex-grow-1 me-2">
                                                     <span class="text-gray-800  fs-6 fw-bold">
                                                        @if(!empty($help_desk_employees))	
                                                            Help Desk Total Employees
                                                        @elseif(!empty($help_desk_departments))
                                                             Help Desk Total Department
                                                       
                                                        @endif
                                                    </span>
                                                 </div>
                                                <span class="fs-6">
                                                        @if (!empty($help_desk_employees))
                                                            {{ count($help_desk_employees) }}

                                                        @elseif (!empty($help_desk_departments))
                                                        {{ count($help_desk_departments) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                </span>
                                             </div>
                                         </div>
                                         <div class="separator separator-dashed my-4"></div>
                                        @if(!empty($help_desk_employees))	
                                            @php $key = 0;  @endphp
                                            @foreach($help_desk_employees as $help_desk_employee)
                                                <div class="d-flex flex-stack">
                                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                        <div class="flex-grow-1 me-2">
                                                            <span class="text-gray-800  fs-6 fw-bold">User {{ ++$key }}</span>
                                                        </div>
                                                      
                                                            {!! CustomHelper::getEmpProfileDiv($help_desk_employee['hdf_employee_id']) !!}
                                                      
                                                    </div>
                                                </div>
                                                <div class="separator separator-dashed my-4"></div>
                                            @endforeach
                                        @elseif(!empty($employee_department_can_ra))
                                          
                                            @php $key = 0;  @endphp
                                            @foreach($employee_department_can_ra as $department_employee)
                                                @if($department_employee['employee_id'] != session('user_session'))
                                                    <div class="d-flex flex-stack">
                                                        <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                            <div class="flex-grow-1 me-2">
                                                                <span class="text-gray-800  fs-6 fw-bold">User {{ ++$key }}</span>
                                                            </div>
                                                        
                                                                {!! CustomHelper::getEmpProfileDiv($department_employee['employee_id']) !!}
                                                           
                                                        </div>
                                                    </div>
                                                    <div class="separator separator-dashed my-4"></div>
                                                @endif
                                            @endforeach
                                           
                                        @endif 
                                            
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="custom-tabs-vehicle-maintenance" role="tabpanel"
                                    aria-labelledby="custom-tabs-vehicle-maintenance-tab">
                                    <div class="card-body p-0">

                                        @if(!empty($help_desk_status))
                                            @foreach ($help_desk_status as $help_desk_status )
                                                <div class="d-flex flex-stack">
                                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                        {{--begin:Author--}}
                                                        <div class="flex-grow-1 me-2">
                                                            <span class="text-gray-800  fs-6 fw-bold">
                                                                {!! CustomHelper::getEmpProfileDiv($help_desk_status['ticket_status_changed_by']) !!}
                                                            </span>
                                                        </div>
                                                        @if($help_desk_status['ticket_status'] == '1')
                                                            <span class="badge badge-light-success font-weight-bold">Pending</span>
                                                        @elseif($help_desk_status['ticket_status'] == '2')
                                                            <span class="badge badge-light-success font-weight-bold">Ticket Open</span>
                                                       
                                                        @elseif($help_desk_status['ticket_status'] == '3')
                                                            <span class="badge badge-light-danger font-weight-bold">Ticket Rejected</span>

                                                        @elseif($help_desk_status['ticket_status'] == '4')
                                                            <span class="badge badge-light-success font-weight-bold">Query Resolved From Department</span>

                                                        @elseif($help_desk_status['ticket_status'] == '5')
                                                            <span class="badge badge-light-success font-weight-bold">Query Resolved From Employees</span>
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

                <div class="col-md-8">
                    <div class="card card-flush mb-10">
                        <div class="card-body">
                            <div class="row g-9">
                                <label class="text-dark fw-bold"> Help Desk Message </label> 
                                <p>
                                    {{$help_desk[0]['help_message']}}
                                </p>
                            </div>
                        </div>
                    </div>
    
    
                    <div class="flex-lg-row-fluid card card-flush mb-10">
                        <div class="card direct-chat direct-chat-primary">
                            <div class="card-header" id="kt_chat_messenger_header">
                                <div class="card-title">
                                    <div class="d-flex justify-content-center flex-column me-3">
                                        <p class="fs-4 fw-bold text-gray-900 me-1 mb-2 lh-1">Help Desk Chat</p>
                                        
                                    </div>
                                
                                </div>
                            </div>
                        
                            <div class="card-body" id="kt_chat_messenger_body">
                                <div class="scroll-y me-n5 pe-5 h-300px h-lg-auto " data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_app_toolbar, #kt_toolbar, #kt_footer, #kt_app_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer" data-kt-scroll-wrappers="#kt_content, #kt_app_content, #kt_chat_messenger_body" data-kt-scroll-offset="5px" style="max-height: 151px;" id="chat-message">
                                    <div class="direct-chat-messages">
    
                                    </div>
                        
                                </div>  
                            </div>
                            <div class="card-footer pt-4" >
                                <form id="sendChatMessage">
                                    {{--begin::Input--}}
                                    <input type="hidden" name="hd_index_id" id="index_input">
                                    <input type="hidden" name="hd_id" value="{{Request::segment(2)}}">
                                    
                                    @if($last_status == 2 )
                                        <div class="input-group">
                                            <input type="hidden" id="fileHolder" name="chat_file" >
                                            <textarea class="form-control form-control-flush mb-3" id="messageInputBox"  name="message" rows="1" data-kt-element="input" placeholder="Type a message"></textarea>
                                            {{-- end::Input --}}
                                            {{--begin:Toolbar--}}
                                        
                                            
                                                <div class="d-flex flex-stack">
                                                    <button type="submit" id="sendMessageBtn"class="btn btn-primary">
                                                        <span class="indicator-label">Submit</span>
                                                        <span class="indicator-progress">Please wait... 
                                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                        </div>  
                                    @endif
                                </form>   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>
       
    </div>


    {{--Add Department Help Desk Modal --}}
    @if(!empty($help_desk_departments))
        <div class="modal fade" id="addDepartment" tabindex="-1" aria-hidden="true">
            
            <div class="modal-dialog modal-dialog-top mw-650px">
                
                <div class="modal-content rounded">
                    
                    <div class="modal-content rounded">
                        
                        <div class="modal-header">
                        <h4 class="modal-title pl-4">Add Department</h4>
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
                    
                    
                    <div class="modal-body scroll-y pt-0 pb-15">
                        
                        <form id="addDepartmentHelpDesk" class="form">
                            @csrf  
                            
                            <div class="col-md-12 mb-8 mt-3 fv-row">
                                <input type="hidden" name="hd_id"  value="{{$help_desk[0]['help_desk_id']}}"/> 
                                <label class="fs-6 fw-semibold mb-2 required">Departments</label>
                                <div class="col-md-12 fv-row">
                                    <select class="form-select" data-control="select2" multiple="multiple" data-hide-search="true" data-placeholder="Select Department" name="department_name[]" required >
                                            <option value="">Select Department</option>
                                            @if(!empty($departments))
                                                @foreach ($departments as $department)
                                                    @if(in_array($department['department_id'],$help_desk_departments))
                                                        <option value="{{$department['department_id']}}" selected>
                                                        {{ $department['department_name'] }}</option>
                                                    @else
                                                    
                                                    <option value="{{$department['department_id']}}">
                                                        {{ $department['department_name'] }}</option>
                                                    @endif
                                                
                                                @endforeach
                                            
                                            @endif
                                    </select>
                                </div>
                            
                            </div>
                            <div class="text-center">
                            
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="help-desk-department-btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </form>
                        
                    </div>
                    
                </div>
                
            </div>
            
        </div>
    @endif
    {{--Add Department Help Desk Modal --}}

    {{--Add Employee Help Desk Modal --}}
    @if(!empty($help_desk_employees))
        <div class="modal fade" id="addEmployee" tabindex="-1" aria-hidden="true">
            
            <div class="modal-dialog modal-dialog-top mw-650px">
                
                <div class="modal-content rounded">
                    
                    <div class="modal-content rounded">
                        
                        <div class="modal-header">
                        <h4 class="modal-title pl-4">Add Employee</h4>
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
                    
                    
                    <div class="modal-body scroll-y pt-0 pb-15">
                        
                        <form id="addEmployeeHelpDesk" class="form">
                            @csrf  
                            
                            <div class="col-md-12 mb-8 mt-3 fv-row">
                                <input type="hidden" name="hd_id"  value="{{$help_desk[0]['help_desk_id']}}"/> 
                                <label class="fs-6 fw-semibold mb-2 required">Departments</label>
                                <div class="col-md-12 fv-row">
                                    <select class="form-select  multi-depart" data-control="select2" multiple="multiple" data-hide-search="true" data-placeholder="Select Department" name="department_name[]" id="select-department" required >
                                            @if(!empty($departments))
                                                    @foreach ($departments as $department)
                                                        @if(in_array($department['department_id'],$employee_department_ids))
                                                            <option value="{{$department['department_id']}}" selected>
                                                            {{ $department['department_name'] }}</option>
                                                        @else
                                                        
                                                            <option value="{{$department['department_id']}}">
                                                                {{ $department['department_name'] }}</option>
                                                        @endif
                                                    
                                                    @endforeach
                                            
                                            @endif
                                    </select>
                                </div>
                            
                            </div>
                            <div class="col-md-12 mb-8 mt-5 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Employees</label>
                                <div class="col-md-12 fv-row">
                                    <select class="form-select  " data-control="select2" multiple="multiple" data-hide-search="true" data-close-on-select="false" data-placeholder="Select Employee" name="employee_id[]" id="employees" required>
                                        @if (!empty($get_employees))
                                            @foreach ($get_employees as $active_employee)
                                                @if(in_array($active_employee['employee_id'],$help_desk_employees_selected))
                            
                                                     <option value="{{$active_employee['employee_id'] }}" selected>{{$active_employee['employee_name'] }} - {{ $active_employee['employee_code'] }}</option>
                                                @else
                                                    <option value="{{$active_employee['employee_id'] }}">{{ $active_employee['employee_name'] }} - {{ $active_employee['employee_code'] }}</option>
                                                @endif
                                               
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                              
                            </div>
                            <div class="text-center">
                            
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="help-desk-employee-btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </form>
                        
                    </div>
                    
                </div>
                
            </div>
            
        </div>
    @endif
    {{--Add Employee Help Desk Modal --}}

    <div class="modal fade" tabindex="-1" id="ticket-status-modify">
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
                <form id="ticket_status_modify" class="form" >
                    @csrf
                    
                    <div class="modal-body">
                    <input type="hidden" class="form-control" name="hd_id" />
                    <input type="hidden" class="form-control" name="emp_id" />
                    <input type="hidden" class="form-control" name="ticket_status" />
                        <p class="status-msg"></p>
                    </div>
        
                    <div class="modal-footer">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit_btn" class="btn btn-primary">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                    </div>
                </form>    
            </div>
        </div>
    </div>

</div>



@endsection


@push('js-link')

<script>
    var help_desk_id = '{{ Request::segment(2) }}';
 </script>
 <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
 <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script>
 <script src="{{url('/')}}/assets/js/firebase-helpdesk-chat.js?js_v=2"></script>

 <script src="{{url('assets/js/help-desk/help-desk-detail.js')}}"></script>


@endpush