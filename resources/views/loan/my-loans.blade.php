@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My Loans</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#loanrequest">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Request For Loan</a>
                </div>
         
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            <div class="row g-6 g-xl-9">
                    
                <div class="col-xl-4">
                                        
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ $loan_given_amount[0]['total']}}"></div>
                            <div class="fw-semibold text-gray-400">Total Loan Taken</div>
                        </div>
                        
                    </div>
                    
                </div>


                <div class="col-xl-4">
                                        
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{$loan_return_amount[0]['total']}}"></div>
                            <div class="fw-semibold text-gray-400">Total Loan Returned</div>
                        </div>
                        
                    </div>
                    
                </div>


                <div class="col-xl-4">
                                        
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{$loan_remaining_amount}}"></div>
                            <div class="fw-semibold text-gray-400">Total Loan Remaining</div>
                        </div>
                        
                    </div>
                    
                </div>

            </div>

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
   
                <div class="card-body py-3">
                    
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered">
                    
                        <table class="table table-bordered align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable">
                                            
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Message</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Deduction</th>
                                    <th class="text-center">Deduction Start</th>
                                    <th class="text-center">Applied Date</th>
                                    <th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Approval(s)">Approval(s)</th>
                                    <th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Response By HR">Status</th>
                                    <th class="text-center">Approver Message</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($all_loans))
                                    @php  $currency = CustomHelper::hcm_system_currency(); @endphp   
                                    @foreach ($all_loans as $all_loan)
                                        <tr @if(request()->get('request-id') != '' && $all_loan['loan_request_id'] == request()->get('request-id')) style="background-color:bisque;" @endif>
                                            
                                            <td class="text-center">{{ $all_loan['loan_reason'] }}</td>
                                            <td class="text-center">
                                                <a href="javascript:void(0);" class="message_btn" data-bs-toggle="modal" data-bs-target="#loan_message_modal" data-message="{{ $all_loan['loan_reason_message'] }}">
                                                    <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Message" aria-hidden="true">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                            <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                            <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </td>
                                            
                                            <td class="text-center">{{ $all_loan['loan_request_amount'] }}</td>
                                            
                                            @if($all_loan['loan_deduction_type'] == 1)
                                                <td class="text-center">{{ $all_loan['loan_deduction_amount'].' '.$currency[0]['system_currency'] }}</td>
                                            @else
                                                <td class="text-center">{{ $all_loan['loan_deduction_amount'].  " %" }}</td>
                                            @endif
                                            <td class="text-center">{{ $all_loan['loan_deduction_start']}}</td>
                                            <td class="text-center">{{ $all_loan['loan_applied_date'] }}</td>
                                            <td class="text-center">
                                                  
                                                <a href="javascript:void(0);" class="manager_modal_status_btn" data-request-id="{{$all_loan['loan_request_id'] }}"  data-bs-toggle="tooltip" data-bs-placement="top" title="Approval(s)">
                                                    @if(in_array($all_loan['loan_request_id'],$responsed_request))
                                                            <i class="fas fa-user-check text-success"></i>
                                                    @else
                                                        <i class="fas fa-user-check"></i>
                                                    @endif
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if ($all_loan['request_approved_by_finance'] == 1)
                                                    <span class="badge badge-light-secondary text-center text-muted d-inline-block  font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Finance didn't respond to this request yet.">Pending</span>
                                                @elseif($all_loan['request_approved_by_finance'] == 2)
                                                    <span class="badge badge-light-success text-center d-inline-block  font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{$all_loan['request_approved_date_finance'] ?? "N/A" }}">Approved</span>
                                                @else
                                                    <span class="badge badge-light-danger text-center d-inline-block  font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{$all_loan['request_approved_date_finance'] ?? "N/A" }}">Disapproved</span>
                                                @endif
                                            </td>
                                            <td class="text-center"> <a href="javascript:void(0);" class="approver_message_btn" data-bs-toggle="modal" data-bs-target="#Approver_loan_message_modal" data-message="{{ $all_loan['request_aprrover_message'] }}">
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
                                                @if(in_array($all_loan['loan_request_id'],$responsed_request) || $all_loan['request_approved_by_finance'] != 1)

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

                                                    <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-title="Edit Loan Request" onclick="editloanRequest('{{$all_loan['loan_request_id']}}','{{$all_loan['loan_request_amount']}}', '{{$all_loan['loan_reason']}}', '{{$all_loan['loan_deduction_type']}}', '{{$all_loan['loan_deduction_start']}}', '{{$all_loan['loan_deduction_amount']}}', '{{$all_loan['loan_reason_message']}}')">
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                    </a>

                                                    <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-title="Delete Loan Request" onclick="deleteloanRequest('{{$all_loan['loan_request_id']}}')">
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                
                                                @endif

                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-title="View Loan Detail" onclick="viewloanDetail('{{$all_loan['loan_request_id']}}')">
                                                   <i class="fa fa-eye"></i>
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

    
	<form id="send_new_loan_request" class="form">
        @csrf	
        <div class="modal fade" id="loanrequest" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                   
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Loan Request</h4>
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
                        @php  $currency = CustomHelper::hcm_system_currency(); @endphp 
                        <div class="row g-8">

                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Loan Amount</label>
                                <input type="number" class="form-control" name="loan_amount" placeholder="Enter Amount" id="loan_amount" required>

                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Loan Type</span>
                                </label>
                                <select class="form-select loanselect" data-control="select2" data-hide-search="false" data-placeholder="Select Loan Type" name="loan_reason"  required> 
                                    <option value="">Reason</option>
                                    <option value="Bike Loan">Bike Loan</option>
                                    <option value="Car Loan">Car Loan</option>
                                    <option value="Property Loan">Property Loan</option>
                                    <option value="Advance Salary">Advance Salary</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Loan Deduction</span>
                                </label>
                                <select class="form-select loanselect" data-control="select2" data-hide-search="false" data-placeholder="Select deduction Type" name="deduction_type" id="deduction_type" required> 
                                    <option value="">Loan Deduction</option>
                                    <option value="1">Fixed Amount</option>
                                    <option value="2">Percentage</option>
                                </select>
                            </div>
                        
                            <div class="col-md-6 fv-row d-none" id="deduct_div" >
                            
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2 required" id="deduct_label">Loan Deduction Amount</label> 
                                <div class="input-group deduct" >
                                    <input type="number" class="form-control deduct deduction_source" rows="5" name="deduction_amount"  placeholder="Loan Deduction Amount">
                                    <span class="input-group-text">{{ $currency[0]['system_currency']}}</span>
                                </div>    

                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2 required" id="deduct_per_label">Loan Deduction Percentage</label>
                                <div class="input-group deduct_per">
                                    <input type="number" class="form-control deduct_per deduction_source" name="deduction_percentage" max="100" placeholder="Loan Deduction Percentage" >
                                    <span class="input-group-text">%</span>
                                </div>    
                            </div>
                        
                            <div class="col-md-6 fv-row">
                                

                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Loan Deduction Start Period</span>
                                </label>
                                <input type="month" class="form-control" name="deduction_start" min="{{ date('Y-m') }}" max="{{ date('Y-m',strtotime('+1 month')) }}" placeholder="Enter Date" required>

                            </div>


                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Expected Months of Deduction</label>
                                <input type="text" class="form-control deduction_months" value="0 Months" readonly>
                            </div>

                            <div class="d-flex flex-column mb-8">
                                <label class="fs-6 fw-semibold mb-2 required">Enter Reason</label>
                                <textarea class="form-control kt_docs_loanmaxlength_threshold1" rows="5" name="loan_message" maxlength="500" placeholder="Message" required></textarea>
                            </div>

                        </div>    
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit_btn" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
     
    {{--begin::Modal - salary Message--}}

     
    <form id="edit_loan_request" class="form">
        @csrf
        <div class="modal fade" id="editloanrequest" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Loan Request</h4>
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
                    
                        <div class="row g-8">
                            <input type="hidden" name="request_id" value=""/>

                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Loan Amount</label>
                                <input type="number" class="form-control" name="loan_amount" placeholder="Enter Amount" id="edit_loan_amount" required>

                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Loan Type</span>
                                </label>
                                <select class="form-select loanselect2" data-control="select2" data-hide-search="false" data-placeholder="Select Loan Type" name="loan_reason" id="loan_reason"  required> 
                                    <option value="">Reason</option>
                                    <option value="Bike Loan">Bike Loan</option>
                                    <option value="Car Loan">Car Loan</option>
                                    <option value="Property Loan">Property Loan</option>
                                    <option value="Advance Salary">Advance Salary</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Loan Deduction</span>
                                </label>
                                <select class="form-select loanselect2" data-control="select2" data-hide-search="false" data-placeholder="Select deduction Type" name="deduction_type" id="deduct_type" required> 
                                    <option value="">Loan Deduction</option>
                                    <option value="1">Fixed Amount</option>
                                    <option value="2">Percentage</option>
                                </select>
                            </div>
                        
                            <div class="col-md-6 fv-row d-none" id="deduct_div2" >
                            
                                <label class="fs-6 fw-semibold mb-2 required" id="deduct_label2">Loan Deduction Amount</label> 
                                <div class="input-group deduct2">
                                    <input type="number" class="form-control deduct2" rows="5" name="deduction_amount" id="deduct_amount"  placeholder="Loan Deduction Amount" required>
                                    <span class="input-group-text">{{ $currency[0]['system_currency']}}</span>
                                </div>    
                            
                                <label class="fs-6 fw-semibold mb-2 required" id="deduct_per_label2">Loan Deduction Percentage</label>
                                <div class="input-group deduct_per2">
                                    <input type="number" class="form-control deduct_per2" rows="5" name="deduction_percentage" id="deduct_percentage" max="100" placeholder="Loan Deduction Percentage" required>
                                    <span class="input-group-text">%</span>
                                </div>    
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Loan Deduction Start</label>
                                <input type="month" class="form-control" name="deduction_start" id="deduct_start" min="{{ date('Y-m') }}" max="{{ date('Y-m',strtotime('+1 month')) }}" placeholder="Enter Date" required>

                            </div>

                            <div class="d-flex flex-column mb-8">
                                <label class="fs-6 fw-semibold mb-2 required">Enter Reason</label>
                                <textarea class="form-control kt_docs_loanmaxlength_threshold1" rows="5" name="loan_message" id="loan_msg" maxlength="500" placeholder="Message" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit_btn_loan" class="btn btn-primary">
                            <span class="indicator-label">Update</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
     
     {{--- Delete loan Request --------------}}
        <div class="modal fade" tabindex="-1" id="delete-loan-request">
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
                    <form id="delete_loan_request" class="form" >
                        @csrf
                        
                        <div class="modal-body">
                        <input type="hidden" class="form-control" name="delete_id" />
                            <p>Are you sure to delete this Loan Request ?</p>
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

        {{--end::Modal - loan delete Modal--}}



    {{--begin::Modal - loan Message--}}
            
    <div class="modal fade" id="loan_message_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
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
                <div class="modal-body">
                    <p id="request_message_body" class="" ></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="Approver_loan_message_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content rounded">
                <div class="modal-content rounded">
                    <div class="modal-header">
                    <h4 class="modal-title pl-4">Approver Message</h4>
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
                    <p id="approver_loan_message" class="" ></p>
                </div>
            </div>
        </div>
    </div>

    {{--end::Modal - loan Message--}}


</div>
    {{-- Loan Detail View Modal--}}

      
   
    <div class="modal fade" id="viewLoanDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top mw-650px">
          
            <div class="modal-content rounded">
                
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Employee Loan Detail</h4>
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
              
                <div class="modal-body" style="overflow-y:scroll;overflow-x:hidden;max-height: 500px;">
                    <div class="row">
                        <table class="table" id="manage-loan-detail-response">
                           
                            
                        </table>
                    </div>
                    <table class="table table-row-bordered mt-4" id="loan-request-detail-response">
                    </table>
                  
                </div>
              
            </div>
         
        </div>
    </div>

@endsection

@push('js-link')
<script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>

<script src="{{url('assets/js/loan/my-loans.js')}}"></script>
{{-- <script>
    $(function(){
         $('.loanselect').select2({
            dropdownParent: $('#loanrequest')
        })
        $('.loanselect2').select2({
            dropdownParent: $('#editloanrequest')
        })
      
	});
    $('.kt_docs_loanmaxlength_threshold1').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
    });

    $('.message_btn').click(function() {
        var message = $(this).attr('data-message');
        $('#request_message_body').html(message);
    });

    $('.approver_message_btn').click(function() {
        var message = $(this).attr('data-message');
        $('#approver_loan_message').html(message);
    });

     $('#send_new_loan_request').submit(function(e) {
        $('#submit_btn').prop('disabled', true);
        $("#submit_btn").attr('data-kt-indicator', 'on');
        $('#submit_btn').css('cursor', 'not-allowed');
        e.preventDefault();
        $.ajax({
            url: '{{ url('loan-request') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == 'TRUE') {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg,
                        timer: 3000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 8000,
                    })
                    $('#submit_btn').prop('disabled', false);
                    $("#submit_btn").removeAttr('data-kt-indicator');
                    $('#submit_btn').css('cursor', 'pointer');
                }
            },
            error: function(jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#submit_btn').prop('disabled', false);
                $("#submit_btn").removeAttr('data-kt-indicator');
                $('#submit_btn').css('cursor', 'pointer');
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
                } else {
                    if(errorStatus == 419){
                        location.reload();
                    }else{
                        Toast.fire({
                            icon: 'warning',
                            title: 'Try Again. Error Code ' + errorStatus,
                            timer: 3000,
                        })
                    }
                }
            }
        });
    });

     $("#deduction_type").change(function () {
            var deduction_type = $(this).val();
            var loan_amount = $('#loan_amount').val();
            if(deduction_type == 1){
                $('#deduct_div').removeClass('d-none')
                $('.deduct').removeClass('d-none')
                $('#deduct_label').removeClass('d-none')
                $('#deduct_per_label').addClass('d-none')
                $('.deduct_per').addClass('d-none')
                $('.deduct_per').prop('required',false);
                $('.deduct_per').prop('max',false);
                $('.deduct').prop('required',true);
                $('.deduct').prop('max',loan_amount);
            }else{
                $('.deduct').prop('required',false);
                $('.deduct').prop('max',false);
                $('.deduct_per').prop('required',true);
                $('.deduct').addClass('d-none')
                $('#deduct_label').addClass('d-none')
                $('#deduct_per_label').removeClass('d-none')
                $('#deduct_div').removeClass('d-none')
                $('.deduct_per').removeClass('d-none')
                $('.deduct_per').prop('max',100);
            }
      
        });

        $("#loan_amount").keyup(function () {
            var loan_amount = $('#loan_amount').val();
            $('.deduct').prop('max',loan_amount);
            $('.deduction_source').val('');

        });

         $("#deduct_type").change(function () {
            var deduct_type = $(this).val();
            var loan_amount = $('#edit_loan_amount').val();
            if(deduct_type == 1){
                $('#deduct_div2').removeClass('d-none')
                $('.deduct2').removeClass('d-none')
                $('#deduct_label2').removeClass('d-none')
                $('#deduct_per_label2').addClass('d-none')
                $('.deduct_per2').addClass('d-none')
                $('.deduct_per2').prop('required',false);
                $('.deduct_per2').prop('max',false);
                $('.deduct2').prop('required',true);
                $('.deduct2').prop('max',loan_amount);
            }else{
                $('.deduct2').prop('required',false);
                $('.deduct2').prop('max',false);
                $('.deduct_per2').prop('required',true);
                $('.deduct2').addClass('d-none')
                $('#deduct_label2').addClass('d-none')
                $('#deduct_per_label2').removeClass('d-none')
                $('#deduct_div2').removeClass('d-none')
                $('.deduct_per2').removeClass('d-none')
                $('.deduct_per2').prop('max',100);
            }
      
        });

          $("#edit_loan_amount").keyup(function () {
            var loan_amount = $('#edit_loan_amount').val();
            $('.deduct2').prop('max',loan_amount);


        });

    function editloanRequest(loan_request_id, loan_request_amount, loan_reason, loan_deduction_type, loan_deduction_start, loan_deduction_amount, loan_reason_message ){
        $('input[name=request_id]').val(loan_request_id);
        $('#edit_loan_amount').val(loan_request_amount);
        $('#loan_reason').val(loan_reason).trigger('change')
        $('#deduct_type').val(loan_deduction_type).trigger('change')
        $('#deduct_start').val(loan_deduction_start);
            if(loan_deduction_type == 1){
                $('#deduct_amount').val(loan_deduction_amount);
            }else{
                $('#deduct_percentage').val(loan_deduction_amount);
            }
        $('#loan_msg').html(loan_reason_message);
        $('#editloanrequest').modal('show');

    }

       $('#edit_loan_request').submit(function(e) {
        $('#submit_btn_loan').prop('disabled', true);
        $("#submit_btn_loan").attr('data-kt-indicator', 'on');
        $('#submit_btn_loan').css('cursor', 'pointer');
        
        e.preventDefault();
        $.ajax({
            url: '{{ url('update-loan-request') }}',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == 'TRUE') {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg,
                        timer: 5000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                } else {
                    if(data.autoreload == "TRUE"){
                      
                      Toast.fire({
                      icon: 'warning',
                      title: data.msg,
                      timer: 5000,
                      })
                      setTimeout(() => {
                          location.reload();
                      }, 5000);
                    }
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                   
                    $('#submit_btn_loan').prop('disabled', false);
                    $('#submit_btn_loan').css('cursor', 'pointer');
                    $("#submit_btn_loan").removeAttr('data-kt-indicator');
                }
            },
            error: function(jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#submit_btn_loan').prop('disabled', false);
                $('#submit_btn_loan').css('cursor', 'pointer');
                $("#submit_btn_loan").removeAttr('data-kt-indicator');
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
                } else {
                    if(errorStatus == 419){
                        location.reload();
                    }else{
                        Toast.fire({
                            icon: 'warning',
                            title: 'Try Again. Error Code ' + errorStatus,
                            timer: 3000,
                        })
                    }
                }
            }
        });
    });

     function deleteloanRequest(delete_id){
       $('input[name=delete_id]').val(delete_id);
       $('#delete-loan-request').modal('show');

    }

      $('#delete_loan_request').submit(function(e) {
        $('#delete_submit_btn').prop('disabled', true);
        $("#delete_submit_btn").attr('data-kt-indicator', 'on');
        $('#delete_submit_btn').css('cursor', 'pointer');
        e.preventDefault();
        $.ajax({
            url: '{{ url('delete-loan-request') }}',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == 'TRUE') {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg,
                        timer: 5000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                } else {
                    if(data.autoreload == "TRUE"){
                      
                        Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                      })
                      setTimeout(() => {
                          location.reload();
                      }, 5000);
                    }
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                   
                    $('#delete_submit_btn').prop('disabled', false);
                    $('#delete_submit_btn').css('cursor', 'pointer');
                    $("#delete_submit_btn").removeAttr('data-kt-indicator');
                }
            },
            error: function(jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#delete_submit_btn').prop('disabled', false);
                $('#delete_submit_btn').css('cursor', 'pointer');
                $("#delete_submit_btn").removeAttr('data-kt-indicator');
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
                } else {
                    if(errorStatus == 419){
                        location.reload();
                    }else{
                        Toast.fire({
                            icon: 'warning',
                            title: 'Try Again. Error Code ' + errorStatus,
                            timer: 3000,
                        })
                    }
                }
            }
        });
    });

    $('.manager_modal_status_btn').click(function(){
        request_id =  $(this).attr('data-request-id');
        request_response_log(request_id, 5);
    });

    function viewloanDetail(loan_id){
        my_loan_request_detail(loan_id);

    }

    function my_loan_request_detail(my_loan_req_id){
        $.ajax({

            url: base_url +'/my-loan-request-detail',
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {request_id:my_loan_req_id},
            success: function (response) {
                var record = '';
                var installment_record = '';
                if (response.status == "TRUE") {
                    $('#loan-request-detail-response').html('');
                    $('#manage-loan-detail-response').html('');
                    record +=`<tr class="mb-4">
                            <td class="ps-6 "><b>Loan Given Amount</b></td>
                            <td class="ps-6">${response.data[0].loan_given_amount}</td>
                            <td class="ps-6 "><b>Loan Remaining Amount</b></td>
                            <td class="ps-6">${response.data[0].loan_remaining_amount}</td>
                           
                        </tr>
                        <tr class="pt-4">
                            <td class="ps-6 pt-4"><b>Loan Submitted Amount</b></td>
                            <td class="ps-6 pt-4">${response.data[0].loan_submitted_amount}</td>
                           
                        </tr>`; 
                    if(response.loan_installment != ''){
                        installment_record += `<thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-7">Loan Installment Amount</th>
                                <th class="ps-7">Loan Installment Date</th>

                            </tr>
                        </thead> <tbody>`;
                    
                        $.each(response.loan_installment, function(key, employee_loan_installment) {
                    
                            installment_record +=`<tr>
                                <td class="ps-6 ">${employee_loan_installment.loan_installment_amount}</td>
                                <td class="ps-6">${employee_loan_installment.loan_installment_date}</td>
                                  
                            </tr>`; 
                        });
                        installment_record+=` </tbody>`;
                    }
                    
                    $('#manage-loan-detail-response').html(record);
                    $('#loan-request-detail-response').html(installment_record);
                }else{
                    if(response.error_status == "FALSE"){
                        Toast.fire({
                        icon: 'warning',
                        title: response.msg,
                        timer: 5000,
                      });
                      $('#viewLoanDetailModal').modal('hide');

                      return false;
                    }
                    record = 'Your Loan request is pending .';
                    $('#manage-loan-detail-response').html(record);
                   
                }
                $('#viewLoanDetailModal').modal('show');

            },
            error: function(jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
                } else {
                    if(errorStatus == 419){
                        location.reload();
                    }else{
                        Toast.fire({
                            icon: 'warning',
                            title: 'Try Again. Error Code ' + errorStatus,
                            timer: 3000,
                        })
                    }
                }
            }
        });
    }

    $(function(){
        $('#req_status').on('change', function () {
           var value = $(this).val();
           if(value == ''){
                dataTable.column(7).search('',false,true).draw();
           }else{   
                dataTable.column(7).search("^" + value + "$", true, false).draw();
           }    
        });
	});

    $('.deduction_source').keyup(function(){
        var source = $(this).attr('name');
        var val = $(this).val();
        var loan_amount = $('#loan_amount').val();
        if(loan_amount != ''){
            if(val != ''){
                if(source == 'deduction_amount'){
                    var months = loan_amount / val;
                    var official_months = months.toFixed(1);
                }else{
                    actual_loan_amount = 100 / val;
                    var months = actual_loan_amount;
                    var official_months = months.toFixed(1);
                }
                if(official_months >= 1){
                    $('.deduction_months').val(official_months + ' Months');
                }else{
                    $('.deduction_months').val('0 Months');
                }
            }else{
                $('.deduction_months').val('0 Months');
            }
        }
    });

</script> --}}

@endpush

