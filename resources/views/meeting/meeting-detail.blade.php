@extends('layout.main')

@push('cs-link')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
@endpush

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Meeting Detail</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                @if($meeting_detail[0]['host_id'] == session('user_session'))
                    @if($meeting_detail[0]['meeting_status'] == '1')
                        <div class="m-0">
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-user-meeting">											
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Add New User</a>   
                        </div>
                    @endif
                    
                    @if($meeting_detail[0]['meeting_status'] == '2' || $meeting_detail[0]['meeting_status'] == '3')
                        <div class="m-0">
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#re-schedule-meeting">											
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Re-Schedule Meeting</a>   
                        </div>
                    @endif
                @endif

                @if($meeting_detail[0]['host_id'] == session('user_session'))
                    @if($meeting_detail[0]['meeting_status'] == 1)
                        <div class="m-0">
                            <a href="javascript:void(0);" class="btn btn-sm btn-success"  title="Meeting Completed"  onclick="MeetingStatus('{{$meeting_detail[0]['meeting_id']}}','2')">											
                                Completed
                            </a>   
                        </div>

                        <div class="m-0">
                            <a href="javascript:void(0);" class="btn btn-sm btn-danger"  title="Meeting Completed"  onclick="MeetingStatus('{{$meeting_detail[0]['meeting_id']}}','3')">											
                                Cancle
                            </a>   
                        </div>
                    @endif
                @endif

                <div class="m-0">
                     <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
                </div>
         
            </div>
           
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
      
        <div class="app-container">
          
            <div class="row">
                <div class="col-md-4 card mb-5">
                    <div class="card-body p-5">
                        {{--begin::Item--}}
                            <div class="d-flex flex-stack">
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    {{--begin:Author--}}
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800  fs-6 fw-bold">Meeting Time</span>
                                    </div>
                                    {{--end:Author--}}
                                    {{--begin:Action--}}
                                    <span class="fs-6"> 
                                            @if (!empty($meeting_detail[0]['meeting_time']))
                                            {{ $meeting_detail[0]['meeting_time'] }}
                                            @else
                                                N/A
                                            @endif
                                    </span>
                               
                                    {{--end:Action--}}
                                </div>
                                {{--end::Section--}}
                         </div>
                        {{--end::Item--}}
                        {{--begin::Separator--}}
                         <div class="separator separator-dashed my-4"></div>
                        {{--end::Separator--}}
                         {{--begin::Item--}}
                         <div class="d-flex flex-stack">
                             <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                {{--begin:Author--}}
                                 <div class="flex-grow-1 me-2">
                                     <span class="text-gray-800  fs-6 fw-bold">Meeting Host</span>
                                 </div>

                                    {!! CustomHelper::getEmpProfileDiv($meeting_detail[0]['host_id']) !!}
                               
                             </div>
                            {{--end::Section--}}
                         </div>
                        {{--end::Item--}}
                        {{--begin::Separator--}}
                         <div class="separator separator-dashed my-4"></div>
                        {{--end::Separator--}}
     
                         {{--begin::Item--}}
                        <div class="d-flex flex-stack">
                             <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                {{--begin:Author--}}
                                 <div class="flex-grow-1 me-2">
                                     <span class="text-gray-800  fs-6 fw-bold">Created At</span>
                                 </div>
                                {{--end:Author--}}
                                {{--begin:Action--}}
                                <span class="fs-6">{{ date('d, F Y h:i A', strtotime($meeting_detail[0]['meeting_created_at'])) }}</span>
                               
                                {{--end:Action--}}
                             </div>
                            {{--end::Section--}}
                        </div>
                        {{--end::Item--}}
                        {{--begin::Separator--}}
                         <div class="separator separator-dashed my-4"></div>
                        {{--end::Separator--}}
                         {{--begin::Item--}}
                         <div class="d-flex flex-stack">
                             <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                {{--begin:Author--}}
                                 <div class="flex-grow-1 me-2">
                                     <span class="text-gray-800  fs-6 fw-bold">Meeting Total Employees</span>
                                 </div>
                                {{--end:Author--}}
                                {{--begin:Action--}}
                                <span class="fs-6">
                                        @if (!empty($meeting_users))
                                            {{ count($meeting_users) }}
                                        @else
                                            N/A
                                        @endif
                                </span>
                               
                                {{--end:Action--}}
                             </div>
                            {{--end::Section--}}
                         </div>
                        {{--end::Item--}}
                        {{--begin::Separator--}}
                         <div class="separator separator-dashed my-4"></div>
                        {{--end::Separator--}}
                         {{--begin::Item--}}

                         @if(!empty($meeting_users))	
                            @php $key = 0; 
                            $chat_members = [$meeting_detail[0]['host_id']];
                            @endphp
                            @foreach($meeting_users as $meeting_user)
                                <div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    {{--begin:Author--}}
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800  fs-6 fw-bold">User {{ ++$key }}</span>
                                        </div>
                                        
                                    {{--end:Author--}}
                                    {{--begin:Action--}}
                                    {!! CustomHelper::getEmpProfileDiv($meeting_user['user_id']) !!}
                                    {{-- <span class="fs-6 fw-bold text-primary">{{  $meeting_user['employee_name'] }} - {{ $meeting_user['employee_code'] }}</span> --}}
                                    
                                    {{--end:Action--}}
                                    </div>
                                {{--end::Section--}}
                                </div>
                                <div class="separator separator-dashed my-4"></div>
                                {{-- @php array_push($chat_members,$meeting_user['employee_id']) @endphp --}}
                            @endforeach
                        @endif 
                            
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card card-flush mb-10">
                        <div class="card-body">
                            <div class="row g-9">
                                <label class="text-dark fw-bold">Meeting Title </label> 
                                <p>
                                    @if (!empty($meeting_detail[0]['meeting_title']) && $meeting_detail[0]['meeting_title'] != '')
                                        {{ $meeting_detail[0]['meeting_title'] }}
                                    @else
                                        Meeting title is not available.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card card-flush mb-10">
                        <div class="card-body">
                            <div class="row g-9">
                                {{-- <div class="col-md-12 fv-row"> --}}
                                    <label class="text-dark fw-bold"> Meeting Description </label> 
                                    <p>
                                       @if (!empty($meeting_detail[0]['meeting_description']) && $meeting_detail[0]['meeting_description'] != '')
                                            <textarea class="form-control" id="meeting_reason" rows="4">{{ $meeting_detail[0]['meeting_description'] }}</textarea> 
                                        @else
                                            Meeting description is not available.
                                        @endif
                                    </p>
                                {{-- </div> --}}
                            </div>
                        </div>
                    </div>

                    <div class="card card-flush mb-10">
                        <div class="card-body">
                            <div class="row g-9">
                                {{-- <div class="col-md-12 fv-row"> --}}
                                    <label class="text-dark fw-bold"> Meeting Conclusion </label> 
                                    <p>
                                       @if (!empty($meeting_detail[0]['meeting_conclusion']) && $meeting_detail[0]['meeting_conclusion'] != '')
                                            {{ $meeting_detail[0]['meeting_conclusion'] }}
                                        @else
                                            Meeting Conclusion is not available.
                                        @endif
                                    </p>
                                {{-- </div> --}}
                            </div>
                        </div>
                    </div>


                    <div class="flex-lg-row-fluid card card-flush mb-10">
                            {{--begin::Messenger--}}
                        <div class="card direct-chat direct-chat-primary">
                                        {{--begin::Card header--}}
                            <div class="card-header" id="kt_chat_messenger_header">
                                {{--begin::Title--}}
                                <div class="card-title">
                                    {{--begin::User--}}
                                    <div class="d-flex justify-content-center flex-column me-3">
                                        <p class="fs-4 fw-bold text-gray-900 me-1 mb-2 lh-1">Meeting Chat</p>
                                        
                                    </div>
                                    {{--end::User--}}
                                </div>
                                {{--end::Title--}}
                                
                            </div>
                            {{--end::Card header--}}
                            {{--begin::Card body--}}
                            <div class="card-body" id="kt_chat_messenger_body">
                                {{--begin::Messages--}}
                                <div class="scroll-y me-n5 pe-5 h-300px h-lg-auto " data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_app_toolbar, #kt_toolbar, #kt_footer, #kt_app_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer" data-kt-scroll-wrappers="#kt_content, #kt_app_content, #kt_chat_messenger_body" data-kt-scroll-offset="5px" style="max-height: 151px;" id="chat-message">
                                    <!--begin::Message(in)-->
                                    <div class="direct-chat-messages">

                                    </div>
                        
                                </div>  
                            </div>
                            {{--end::Card body--}}
                                    {{--begin::Card footer--}}
                            <div class="card-footer pt-4" >
                                <form id="sendChatMessage">
                                    {{--begin::Input--}}
                                    {{-- <input type="hidden" name="meeting_chat_id" value="Ch-0001">
                                    <input type="hidden" name="meeting_index_id" id="index_input"> --}}
                                    <input type="hidden" name="meeting_id" value="{{Request::segment(2)}}">
                                    @if($meeting_detail[0]['meeting_status'] == '1')
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
                                    
                                </form>    {{-- end::Toolbar--}}
                            </div>
                                    {{-- end::Card footer --}}
                        </div>
                                {{-- end::Messenger --}}
                    </div>
                </div>
            </div>
          
        </div>
    </div>


    {{--begin::Modal - Add New User--}}
    <div class="modal fade" id="add-user-meeting" tabindex="-1" aria-hidden="true">
        
        <div class="modal-dialog modal-dialog-top mw-600px">
            
            <div class="modal-content rounded">
                
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                    <h4 class="modal-title pl-4">Add New User</h4>
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
                    
                    <form id="update_schedule_meeting_form" class="form">
                        @CSRF
                        <input type="hidden" name="update_meeting_id" value="{{$meeting_detail[0]['meeting_id']}}"/>
                        
                        <div class="row g-9 mb-8 mt-1" >
                            <div class="col-md-12 fv-row">
                                <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                  <span class="required">Meeting With</span>
                                </label>
                               
                                <div class="position-relative d-flex align-items-center mb-2">
                                    <select class="form-select myselect2" data-control="select2" multiple="multiple" data-close-on-select="false" data-hide-search="false" data-placeholder="Select Employees For Meeting Invitation" name="meeting_users[]" required=""> 
                                        @if (!empty($active_employees))
                                            @foreach ($active_employees as $key => $active_employee)
                                                @if(in_array($active_employee['employee_id'],$users_id))
                                                    @if($active_employee['employee_id'] != session('user_session'))
                                                        <option value="{{$active_employee['employee_id'] }}" selected>{{ $active_employee['employee_code'] }} - {{ $active_employee['employee_name'] }}</option>
                                                    @endif
                                                @else
                                                    @if($active_employee['employee_id'] != session('user_session'))
                                                        <option value="{{$active_employee['employee_id'] }}">{{ $active_employee['employee_code'] }} - {{ $active_employee['employee_name'] }}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                               
                            </div>
                        </div>
                       
                        
                        <div class="text-center">
                        
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="new_user_submit_btn" class="btn btn-primary">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        
                    </form>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
    {{--end::Modal -Add New user--}}

     
    <form id="re_schedule_meeting_form" class="form">
        @CSRF
        <div class="modal fade" id="re-schedule-meeting" tabindex="-1" aria-hidden="true">
            
            <div class="modal-dialog modal-dialog-scrollable mw-800px">
                
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Re-Schedule a meeting</h4>
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
                                        <option value="Online" @if ($meeting_detail[0]['meeting_type'] == 'Online') selected @endif>Online Meeting</option>
                                        <option value="Physical" @if ($meeting_detail[0]['meeting_type'] == 'Physical') selected @endif>Physical Meeting</option>
                                        
                                            
                                    </select>
                                </div>
                            
                            </div>
                        </div>
                        <div class="row g-9 mb-8 child-div-meeting-type" >
                            
                            <div class="col-md-6 fv-row">
                                {{--begin::Label--}}
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required" id="label-meeting-room">Room No/Location</span>
                                </label>

                                <div class="position-relative d-flex align-items-center">
                                    <select class="form-select" data-control="select2" data-hide-search="true" id="meeting_room" name="meeting_room" required="">
                                        @if (!empty($office_rooms))
                                            @foreach ($office_rooms as $office_room)
                                                <option value="{{ $office_room['room_id'] }}"
                                                    @if ($meeting_detail[0]['meeting_room'] == $office_room['room_id']) selected @endif>
                                                    {{ $office_room['room_name'] }}</option>
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
                                    <input type="url" class="form-control" id="meeting_link" name="meeting_link" required="required" placeholder="Meeting Link" value="{{$meeting_detail[0]['meeting_link'] ?? ''}}">
                                </div>
                                
                                
                            </div>
                        </div>
                        <div class="row g-9 mb-8" >
                            
                            <div class="col-md-6 fv-row">
                                {{--begin::Label--}}
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Meeting Title</span>
                                </label>

                                <div class="position-relative d-flex align-items-center">
                                    <input type="text" class="form-control" name="meeting_title" required="required" placeholder="For eg : Policy Planning" value="{{$meeting_detail[0]['meeting_title']}}">
                                </div>

                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                    <span class="required">Meeting With</span>
                                </label>

                                <div class="position-relative d-flex align-items-center">
                                    <select class="form-select myselect2" data-control="select2" multiple="multiple" data-hide-search="false" data-close-on-select="false" data-placeholder="Select Employees For Meeting Invitation" name="meeting_users[]" required=""> 
                                        @if (!empty($active_employees))
                                            @foreach ($active_employees as $key => $active_employee)
                                                @if(in_array($active_employee['employee_id'],$users_id))
                                                    @if($active_employee['employee_id'] != session('user_session'))
                                                        <option value="{{$active_employee['employee_id'] }}" selected>{{ $active_employee['employee_name'] }} - {{ $active_employee['employee_code'] }}</option>
                                                    @endif
                                                @else
                                                    @if($active_employee['employee_id'] != session('user_session'))
                                                        <option value="{{$active_employee['employee_id'] }}">{{ $active_employee['employee_name'] }} - {{ $active_employee['employee_code'] }}</option>
                                                    @endif
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
                                    <textarea class="form-control" id="meeting_description" rows="4" placeholder="Enter Meeting Description" name="meeting_description">{{$meeting_detail[0]['meeting_description'] ?? ''}}</textarea>
                            </div>
                        </div>
                        <div class="row g-9 mb-8" >
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Meeting Date</span>
                                </label>
                            
                                <input type="date" class="form-control " name="meeting_date" min="{{date('Y-m-d')}}"  required/>
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
                        <button type="submit" id="re_schedule_submit_btn" class="btn btn-primary">
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

    @if($meeting_detail[0]['host_id'] == session('user_session'))
        @if($meeting_detail[0]['meeting_status'] == 1)
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
        @endif  
    @endif      
    
</div>
    


@endsection


@push('js-link')
<script>
   var meeting_id = '{{ Request::segment(2) }}';
</script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script>
<script src="{{url('/')}}/assets/js/firebase-meeting-chat.js?js_v=2"></script>

<script src="{{url('assets/js/meeting/meeting-detail.js')}}"></script>

@endpush