@extends('layout.main')

@push('css-link')

@endpush

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
						 
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My Resignation</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                @if(empty($resignations) || (!empty($resignations) && $resignations[0]['resignation_status'] == '3'))
                    <div class="m-0">
                    
                        <a href="{{ url('employee-resignation/apply-resignation') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Place Resignation">											
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Place Resignation
                        </a>   
                    </div>
                @endif    
            
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
                <div class="card-body ">
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">						
                            <table class="table gs-0 gy-4 dataTable table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                                                                    
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Employee Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Total Day(s)</th>
                                        <th>Message</th>
                                        <th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Approval(s)">Approval(s)</th>
                                        <th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Response By HR">Status</th>
                                        <th class="text-center">Response message</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($resignations))
                                        @foreach ($resignations as $all_resignation)
                                        <tr @if(request()->get('resignation-id') != '' && $all_resignation['resignation_id'] == request()->get('resignation-id')) style="background-color:bisque;" @endif >
                                                <td class="ps-6">{!! CustomHelper::getEmpProfileDiv($all_resignation['resignation_employee']) !!}</td>
                                                
                                                <td>{{ $all_resignation['resignation_from_date'] }}</td>
                                                <td>{{ $all_resignation['resignation_to_date'] }}</td>
                                                <td class="ps-10">
                                                    @php
                                                        $startDate = new DateTime($all_resignation['resignation_from_date']); 
                                                        $endDate = new DateTime($all_resignation['resignation_to_date']);
                                                        
                                                        $total_days = $endDate->diff($startDate)->format("%a");
                                                        echo $total_days+1;
                                                         
                                                    @endphp
                                                </td>
                                                <td class="ps-8">
                                                    <a href="javascript:void(0);" class="message_btn" data-bs-toggle="modal" data-bs-target="#message_modal" data-message="{{$all_resignation['resignation_description']}}">
                                                        <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Message" aria-hidden="true">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                  
                                                    <a href="javascript:void(0);" class="manager_modal_status_btn" data-request-id="{{$all_resignation['resignation_id'] }}"  data-bs-toggle="tooltip" data-bs-placement="top" title="Approval(s)">
                                                        @if(in_array($all_resignation['resignation_id'],$responsed_request))
                                                            <i class="fas fa-user-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-user-check"></i>
                                                        @endif
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    @if ($all_resignation['resignation_status'] == 1)
                                                        <span class="badge badge-light-secondary text-center text-muted font-weight-bold">Pending</span>
                                                    @elseif($all_resignation['resignation_status'] == 2)
                                                        <span class="badge badge-light-success text-center font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $all_resignation['request_approved_date_hr'] ?? "N/A" }}">Approved</span>
                                                    @else
                                                        <span class="badge badge-light-danger text-center font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $all_resignation['request_approved_date_hr'] ?? "N/A" }}">Disapproved</span>
                                                    @endif
                                                </td>
                                                 <td class="text-center"> 
                                                
                                                    <a href="javascript:void(0);" class="approved_message_btn" data-bs-toggle="modal" data-bs-target="#Approved_message_modal" data-message="{{ $all_resignation['request_aprroved_message']}}">
                                                        <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Message" aria-hidden="true">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>

                                                <td>
                                                    <a href="{{ url('employee-resignation/resignation-detail') . '/' . $all_resignation['resignation_id'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Resignation detail">
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

    <div class="modal fade" id="Approved_message_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content rounded">
                <div class="modal-content rounded">
                    <div class="modal-header">
                    <h4 class="modal-title pl-4">Response Message</h4>
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
                        <p id="approver_message" class="" ></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js-link')

<script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>

<script>

$('.message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#message_body').html(message);
});

$('.approved_message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#approver_message').html(message);
});

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 6);
});

$(function () {
    $('#req_status').on('change', function () {
        var value = $(this).val();
        if (value == '') {
            dataTable.column(6).search('', false, true).draw();
        } else {
            dataTable.column(6).search("^" + value + "$", true, false).draw();
        }
    });
}); 

</script>

@endpush