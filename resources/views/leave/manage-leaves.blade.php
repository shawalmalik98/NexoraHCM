@extends('layout.main')



@push('css-link')


@endpush



@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
						 
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Manage Leaves</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                 
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add_manage_leave">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Add Leave</a>   
                </div>
              
            
            </div>
            
        </div>
    </div>
       
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">

                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1 me-2">
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
                <div class="card-body ">
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">						
                            <table class="table gs-0 gy-4 dataTable table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                                                                    
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="text-center">Ref ID</th>
                                        <th class="text-center">Leave Name</th>
                                        <th class="text-center">Leave Yearly Alotment</th>
                                        <th class="text-center">Created By</th>
                                        <th class="text-center">Created At</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($leaves))
                                        @foreach ($leaves as $leave)
                                            <tr>
                                               
                                                <td class="text-center">{{ $leave['leave_manage_id'] }}</td>
                                                <td class="text-center">{{ $leave['leave_name'] }}</td>
                                                <td class="text-primary font-weight-bold text-center">{{ !empty($leave['leave_yearly_allotment']) ? $leave['leave_yearly_allotment'] : 'N/A' }}</td>
                                                <td class="text-center">{!! CustomHelper::getEmpProfileDiv($leave['leave_manag_created_by']) !!}</td>
                                                
                                                <td class="text-center">{{ date('h:i A d M, Y', strtotime( $leave['leave_manag_created_at'] )) }}</td>
                                                <td class="text-center">
                                                    @if(in_array($leave['leave_name'],array('Casual Leave','Sick Leave','Annual Leave','Maternity Leave','Paternity Leave')))
                                                       
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit_btn" data-bs-toggle="tooltip" title="Edit Leave" data-leave_id="{{ $leave['leave_manage_id'] }}" data-leave_name="{{ $leave['leave_name'] }} "  data-leave_visibility="{{$leave['leave_visibilty']}}"  data-error_message="1">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        <a href="javascript:void(0);"  class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" disabled style="cursor: not-allowed;">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @elseif(in_array($leave['leave_name'],$leave_approval_name) || in_array($leave['leave_manage_id'],$leave_manag_request_id))
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit_btn" data-bs-toggle="tooltip" title="Edit Leave" data-leave_id="{{ $leave['leave_manage_id'] }}" data-leave_name="{{ $leave['leave_name'] }} "  data-leave_visibility="{{$leave['leave_visibilty']}}"  data-error_message="1">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        <a href="javascript:void(0);"  class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" disabled style="cursor: not-allowed;">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @else
                                                    
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit_btn" data-bs-toggle="tooltip" title="Edit Leave" data-leave_id="{{ $leave['leave_manage_id'] }}" data-leave_name="{{ $leave['leave_name'] }} "  data-leave_visibility="{{$leave['leave_visibilty']}}"  data-error_message="1">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    
                                                        <a href="javascript:void(0);" onclick="deactivate_confirm('{{$leave['leave_manage_id']}}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="tooltip" title="Delete Leave">
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

    <form id="addleave_form" class="form" autocomplete="off">
        @csrf	
        <div class="modal fade" id="add_manage_leave" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Add Leave</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>
                    
                    <div class="modal-body scroll-y pt-3 pb-5">
                        
                        <div class="col-md-12 mb-8 mt-3 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Leave Name</label>
                            <div class="position-relative d-flex align-items-center">
                                <input type="text" class="form-control " placeholder="Enter a Leave" name="leave_name" id="leave_name" required="" onkeyup="limit(this);"/>
                                <input type="hidden" class="form-control" name="leave_id" id="leave_id" required=""/>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="edit_leaveform" class="form" autocomplete="off">
        @csrf
        <div class="modal fade" id="edit-leave" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Edit Leave</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>
                
                    <div class="modal-body scroll-y pt-3 pb-5">
                        
                        <div class="col-md-12 mb-8 mt-3 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Leave Name</label>
                            <div class="position-relative d-flex align-items-center">
                                <input type="text" class="form-control " placeholder="Enter a Leave" name="leave_name" id="edit_leave_name" required="" onkeyup="limit(this);"/>
                                <input type="hidden" class="form-control" name="leave_id" id="edit_leave_id" required=""/>
                            </div>
                        </div>    
                        <div class="mb-4">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" name="edit_leave_visibility" id="edit_leave_visibility" type="checkbox"   />
                                <span class="form-check-label fw-semibold text-muted">Leave Visibility</span>
                            </label>
                        </div>
                     
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update-btn" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    {{--begin::Modal - Delete Leave--}}

    <div class="modal fade" tabindex="-1" id="modal-danger">
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
                <form id="delete_leaveform" class="form" >
                    @csrf
                    <div class="modal-body">
                    <input type="hidden" class="form-control" name="leave_id" id="delete_leave_id" required=""/>
                        <p>Are you sure to delete this Leave ?</p>
                    </div>
        
                    <div class="modal-footer">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="delete-btn" class="btn btn-primary">
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
<script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>

<script src="{{url('assets/js/leave/manage-leaves.js')}}"></script>
    
@endpush