@extends('layout.main')






@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My Help Desk</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addHelpdesk">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Add New Complaint</a>   
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
                     
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered">
                        <div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Ref ID
                                        </th>
                                        <th class="ps-4">Ticket Generate By
                                        </th>
                                        <th class="ps-4">Message
                                        </th>
                                        <th class="ps-4">Attachment</th>
                                         <th class="ps-4">Created At</th>
                                        <th class="ps-4">Sataus
                                        </th>
                                        <th class="ps-4">Action
                                        </th>
                                       
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($get_my_help_desks))
                                        @foreach ($get_my_help_desks as $get_my_help_desk)
                                            <tr>
                                                <td class="ps-8">000{{ $get_my_help_desk['help_desk_id'] }}</td>
                                                <td class="ps-6">{!! CustomHelper::getEmpProfileDiv($get_my_help_desk['help_desk_loged_by']) !!}</td>
                                                <td class="ps-8">
                                                    <a href="javascript:void(0);" class="message_btn" data-bs-toggle="modal" data-bs-target="#message_modal" data-bs-message="{{$get_my_help_desk['help_message'] }}">
                                                        <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Message" aria-hidden="true">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="ps-4">
                                                    @if(empty($get_my_help_desk['help_attachment']))
                                                        N\A
                                                    @else
                                                        <a href="{{url('/download-my-help-desk-file?path=helpDesk/'.$get_my_help_desk['help_attachment']) }}&title=My Help Desk Attachment file"  class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Download File"><i class="fa fa-download" aria-hidden="true"></i></a>

                                                    @endif
                                                   
                                                </td>
                                                <td class="ps-4">
                                                    {{ date('h:i d M, Y', strtotime($get_my_help_desk['created_time'])) }}
                                                   
                                                </td>
                                                <td>
                                                    @if( $get_my_help_desk['ticket_status'] == 1)
                                                        <span class="badge badge-light-primary font-weight-bold">Ticket Pending</span>
                                                    @elseif( $get_my_help_desk['ticket_status'] == 2)
                                                        <span class="badge badge-light-info font-weight-bold">Ticket Open </span>
                                                    @elseif( $get_my_help_desk['ticket_status'] == 3)
                                                        <span class="badge badge-light-danger font-weight-bold">Ticket Rejected</span>
                                                    @elseif( $get_my_help_desk['ticket_status'] == 4)
                                                    <span class="badge badge-light-success font-weight-bold">Resolved From Department</span>
                                                    @else
                                                        <span class="badge badge-light-success font-weight-bold">Resolved From Employee</span>
                                                    
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
                                                            <a href="{{ url('my-help-desk-detail') }}/{{$get_my_help_desk['help_desk_id'] }}" class="menu-link px-3" data-bs-toggle="tooltip" title="View Help Desk detail">
                                                                View
                                                            </a>
                                                       
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
            
            
            <form id="addHelpDesk" class="form">
                @csrf  
                <div class="modal fade" id="addHelpdesk" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Add Complaint</h4>
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
                                
                                <div class="col-md-12 mb-8 mt-3 fv-row">
                                    <label class="fs-6 fw-semibold mb-2 required">Departments</label>
                                    <div class="col-md-12 fv-row">
                                        <select class="form-select  multi-depart" data-control="select2" multiple="multiple" data-hide-search="true" data-close-on-select="false" data-placeholder="Select Department" name="department_id[]" required id="select-department">
                                                <option value="">Select Department</option>
                                                @if(!empty($departments))
                                                    @foreach ($departments as $department)
                                                        <option value="{{$department['department_id']}}">
                                                            {{ $department['department_name'] }}</option>
                                                    @endforeach
                                                @endif
                                        </select>
                                    </div>
                                    
                                </div>

                                <div class="col-md-12 mb-8 mt-5 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Employees</label>
                                    <div class="col-md-12 fv-row">
                                        <select class="form-select  " data-control="select2" multiple="multiple" data-hide-search="true" data-close-on-select="false" data-placeholder="Select Employee" name="employee_id[]" id="employees">
                                                
                                        </select>
                                    </div>
                                    
                                </div>


                                <div class="col-md-12 mb-8 mt-5 fv-row">
                                    <div class="col-md-12 fv-row">
                                        <label class=" fs-6 fw-semibold mb-2">Attachment</label>
                                        <input type="file" accept=".docx,.txt,application/pdf,image/*" class="form-control " name="attachment">
                                    </div>
                                    {{--end::Input--}}
                                </div>

                                
                                {{--end::Input group--}}
                                
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Enter Complaint Message</label>
                                    <textarea class="form-control " rows="3" name="message" placeholder="Message" required></textarea>
                                </div>
                                {{--end::Input group--}}
                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">
                                
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="help-desk-btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                {{--end::Modal - New Target--}}
            </form>
             
            
        </div>
        
    </div>

     {{-- Complaint Message Modal Starts Here --}}
     <div class="modal fade" id="message_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content rounded">
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Complaint Message</h4>
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
    {{-- Complaint Message Modal Ends Here --}}
    
</div>

@endsection



@push('js-link')

<script src="{{url('assets/js/help-desk/my-help-desk.js')}}"></script>
    
@endpush