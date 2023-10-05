@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    <h1>Reimbursement Request(s) {{(!empty(Request::get('status')) && Request::get('status') =='2' ) ? '- Approved' :''}}{{(!empty(Request::get('paid')) && Request::get('paid') =='2' ) ? '- Paid' :''}}</h1>
                </h1>   
            </div>

        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            <div class="row g-6 g-xl-9">

                <div class="col-xl-4">
                                        
                    <a href="{{url('employee-expense')}}"  class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ (!empty($fetch_expenses_total)) ? $fetch_expenses_total[0]['total'] : 0}}"></div>
                            <div class="fw-semibold text-gray-400">Total Reimbursement</div>
                        </div>
                        
                    </a>
                    
                </div>

                <div class="col-xl-4">
                                        
                    <a href="{{url('employee-expense?status=2')}}" class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ (!empty($fetch_expenses_approved)) ? $fetch_expenses_approved[0]['total'] : 0}}"></div>
                            <div class="fw-semibold text-gray-400">Total Approved Reimbursement</div>
                        </div>
                        
                    </a>
                    
                </div>


                <div class="col-xl-4">
                                        
                    <a href="{{url('employee-expense?paid=2')}}" class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ (!empty($fetch_expenses_paid)) ? $fetch_expenses_paid[0]['total'] : 0}}"></div>
                            <div class="fw-semibold text-gray-400">Total Paid Reimbursement</div>
                        </div>
                        
                    </a>
                    
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
                        @php $filter = 'btn-color-primary'; @endphp

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
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('employee_location')) ? $filter : '' }} {{ !empty(Request::get('emp_status')) ? $filter : '' }} {{!empty(Request::get('emp_name')) ? $filter : '' }} {{!empty(Request::get('req_status')) ? $filter : '' }} {{!empty(Request::get('expense_type')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                       
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('employee-expense') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                    </div>

                                    <div class="separator border-gray-200"></div>
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

                                        <div class="mb-3">
                                        
                                            <label class="form-label fw-semibold">Employee Status</label>
                                            
                                            <div>
                                                <select class="form-select" data-kt-select2="true" data-placeholder="Select option"  name="emp_status" id="employee_data" required>
                                                    <option value="1" @if (!empty(Request::get('emp_status')) && Request::get('emp_status') == '1') selected @endif>Active Employees</option>
                                                    <option value="2" @if(!empty(Request::get('emp_status')) && Request::get('emp_status') == '2') selected @endif>De-active Employees</option>
                                                </select>
                                            
                                            </div>
                                            
                                        </div>

                                        <div class="mb-3">
                                        
                                            <label class="form-label fw-semibold">By Name</label>
                                            
                                            <div>
                                                <select class="form-select" data-kt-select2="true" data-placeholder="Select Name" name="emp_name"
                                                id="company_emp_name" >
                                                    <option value="">Select Name</option>
                                                    @if(!empty($employees_data))
                                                        @foreach($employees_data as $data)
                                                            <option value="{{$data['employee_id']}}"@if (Request::get('emp_name') == $data['employee_id']) selected @endif>{{$data['employee_code']. ' - ' .$data['employee_name']}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            
                                        </div>

                                        <div class="mb-3">
                                                
                                            <label class="form-label fw-semibold">Status</label>
                                            
                                            <div>
                                                <select class="form-select " data-kt-select2="true"  name="req_status">
                                                    <option value="">All Requests</option>
                                                    <option value="1" @if (Request::get('req_status') == '1') selected @endif>Pending Requests</option>
                                                    <option value="2" @if (Request::get('req_status') == '2') selected @endif>Approved Requests</option>
                                                    <option value="3" @if (Request::get('req_status') == '3') selected @endif>Disapproved Requests</option>
                                                </select>
                                            </div>
                                            
                                        </div>
                                        
                                        
                                        <div class="mb-3">
                                        
                                            <label class="form-label fw-semibold">Reimbursement Type</label>
                                            
                                            <div>
                                                <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Reimbursement Type" name="expense_type"> 
                                                    <option value="">Select</option>
                                                    @if(!empty($expense_type_list))
                                                        @foreach($expense_type_list as $key => $type)
                                                        <option value="{{$key}}" @if(Request::get('expense_type') == $key ) selected @endif >{{$type}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('employee_location')) || !empty(Request::get('expense_type')) || !empty(Request::get('emp_status')) || !empty(Request::get('emp_name')))
                                                <a href="{{ url('employee-expense') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                                    >Reset Filter</a>
                                            @endif
                                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                        </div>
                                        
                                    </div>
                                 </form>
                            </div>
                       
                    </div>
                </div> 
                <div class="card-body py-3">
                    
                    <div class="table-responsive">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info" >
                               					 				
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Ref ID</th>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>Reason</th>
                                        <th>Type</th>
                                        <th>Attachment</th>
                                        <th>Amount	</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Approval(s)">Approval(s)
                                        </th>
                                        <th>Status</th>
                                        <th>Approver Message</th>
                                        <th>Reimbursement</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($fetch_expenses))
                                       
                                        @foreach ($fetch_expenses as $expense)
                                            <tr @if(request()->get('expense-id') != '' && $expense['emp_expense_id'] == request()->get('expense-id')) style="background-color:bisque;" @endif>
                                                <td class="ps-8">{{ $expense['emp_expense_id'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($expense['emp_expense_employee']) !!}</td>
                                                <td>{{ date('d-M-Y',strtotime($expense['emp_expense_date'])) }}</td>
                                              
                                                <td class="text-center">
                                                    <a href="javascript:void(0);" class="message_btn" data-bs-toggle="modal" data-bs-target="#message_modal" data-bs-message="{{ $expense['emp_expense_description'] }}">
                                                        <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Reason" aria-hidden="true">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                                <td>{{ $expense_type_list[$expense['emp_expense_type']] ?? 'N/A' }}</td>
                                                <td class="ps-8">
                                                    @if(!empty($expense['emp_expense_attachment']))
                                                        <a href="{{ url('download-employee-expense-file?path=employee_expenses/'.$expense['emp_expense_attachment'])}}&title=employee expense file" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Download File"><i class="fa fa-download" aria-hidden="true"></i></a>
                                                    @else
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-sm me-1 " disabled style="cursor: not-allowed;" data-bs-toggle="tooltip" title="Download File"><i class="fa fa-download" aria-hidden="true"></i></a>
                                                    @endif
                                                </td>
                                                <td><b><span style="cursor: pointer;" data-bs-placement="top" data-bs-toggle="tooltip" title="View Amount Logs"  onclick="amountLog({{$expense['emp_expense_id']}})"> {{ number_format($expense['emp_expense_amount']) }}</span>{!! ($expense['update_amount']>1) ? '<span class=required ></span>' : '' !!}</b></td>

                                                <td class="text-center">
                                                    <a  href="javascript:void(0)" class="manager_modal_status_btn" data-request-id="{{$expense['emp_expense_id']}}"  data-bs-toggle="tooltip" title="Approval(s)" > 
                                                        @if(in_array($expense['emp_expense_id'],$responsed_request))
                                                            <i class="fas fa-user-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-user-check"></i>
                                                        @endif
                                                    </a>
                                                </td>

                                                <td class="text-center" style="white-space:nowrap;" >
                                                    @if ($expense['emp_expense_approved_by_hr'] == 1)
                                                        <span class="badge badge-secondary text-center text-muted font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Your hr didn't respond to this request yet.">Pending</span>
                                                    @elseif($expense['emp_expense_approved_by_hr'] == 2)
                                                        <span class="badge badge-light-success text-center font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{(!empty($expense['employee_name'])) ? 'Approved by '.$expense['employee_name'].'-('.$expense['employee_code'].') at '.date('h:i: A d-M-Y',strtotime($expense['emp_expense_approved_by_hr_time'])) : 'N/A' }}">Approved</span>

                                                    @else
                                                        <span class="badge badge-light-danger text-center font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{(!empty($expense['employee_name'])) ? 'Disapproved by '.$expense['employee_name'].'-('.$expense['employee_code'].') at '.date('h:i: A d-M-Y',strtotime($expense['emp_expense_approved_by_hr_time'])) : 'N/A' }}">Disapproved</span>
                                                        
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="reason_btn" data-bs-toggle="modal" data-bs-target="#reason_modal" data-bs-reason="{{ $expense['emp_expense_approved_by_hr_reason'] }}">
                                                        <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Response Message" aria-hidden="true">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                                <td>
                                                @if($expense['emp_expense_reimbursement']==2)
                                                <span class="badge badge-light-success font-weight-bold" @if(!empty($expense['pc_id'])) data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{'Slip No.'.$expense['pc_id'].' '}} of {{date("F", mktime(0, 0, 0, $expense['pc_month'], 10))}}-{{$expense['pc_year']}} @if($expense['disburse'] == 1) but not disburse @else Disbursed @endif " @endif >Paid</span>
                                                @else
                                                    <span class="badge badge-light-danger font-weight-bold">Unpaid</span>
                                                @endif
                                                </td>
                                                <td>{{ $expense['emp_expense_created_at'] }}</td>
                                                <td>
                                                       
                                                    @if($login_user[0]['employee_department'] != 1 && $login_user[0]['employee_department'] != 2)
                                                        
                                                        @if ($expense['emp_expense_approved_by_hr'] == 1 && !in_array($expense['emp_expense_id'],$responsed_request))
                                        
                                                            <i class="fa-regular fa-circle-check text-success mx-2"  onClick="approvedExpense({{$expense['emp_expense_id']}},{{$expense['emp_expense_amount']}})" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve Request" style="font-size: 20px;cursor: pointer;" ></i>
                                                            <i class="fa-regular fa-circle-xmark text-danger mx-2" onClick="disapprovedExpense({{$expense['emp_expense_id']}})" data-bs-toggle="tooltip" data-bs-placement="top" title="DisApprove Request" style="font-size: 20px;cursor: pointer;"></i>


                                                        @else

                                                            <i class="fa-regular fa-circle-check  text-success mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                            <i class="fa-regular fa-circle-xmark  text-danger mx-2"  style="font-size: 20px; cursor: not-allowed;" disabled></i>

                                                        @endif
                                                    @else
                                                        @if ($expense['emp_expense_approved_by_hr'] == 1)
                                                            
                                                            <i class="fa-regular fa-circle-check text-success mx-2"  onClick="approvedExpense({{$expense['emp_expense_id']}},{{$expense['emp_expense_amount']}})" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve Request" style="font-size: 20px;cursor: pointer;" ></i>
                                                            <i class="fa-regular fa-circle-xmark text-danger mx-2" onClick="disapprovedExpense({{$expense['emp_expense_id']}})" data-bs-toggle="tooltip" data-bs-placement="top" title="DisApprove Request" style="font-size: 20px;cursor: pointer;"></i>


                                                        @else

                                                            <i class="fa-regular fa-circle-check  text-success mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                            <i class="fa-regular fa-circle-xmark  text-danger mx-2"  style="font-size: 20px; cursor: not-allowed;" disabled></i>

                                                        @endif
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
</div>


{{-- Request Message Modal Starts Here --}}
<div class="modal fade" id="message_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content rounded">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Expense Reason</h4>
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


{{-- Reason Message Modal Starts Here --}}
<div class="modal fade" id="reason_modal" tabindex="-1" aria-hidden="true">
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
            </div>
            <div class="modal-body scroll-y">
                <p id="reason_body"></p>
            </div>
        </div>
    </div>
</div>
{{-- Reason Message Modal Ends Here --}}

{{-- Approved Modal Start --}}
<div class="modal fade" id="modalApproved" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-550px">
        <form id="modalApprovedForm">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Confirmation Alert</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>Are you sure to you want to approved this Reimbursement ?</p>
                    <p><b class="required" >Note </b> This amount will be disbursed in payroll.</p>

                    <div class="d-flex flex-column mb-8">
                        <label class="fs-6 fw-semibold mb-2 required">Reimbursement Amount</label>
                        <input type="number" class="form-control " name="expense_amount" placeholder="Enter Expense Amount" required="">
                    </div>

                    <div class="d-flex flex-column mb-4">
                        <label class="fs-6 fw-semibold mb-2 required">Message</label>
                        <textarea class="form-control" name="expense_message" placeholder="Enter Message" rows="3" required></textarea>
                    </div>

                </div>
                
                <div class="modal-footer">
                    <input type="hidden" name="approved_expense_id" value="" >
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="modalApprovedFormButton" class="btn btn-primary">
                        <span class="indicator-label">Yes, Approved</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- Approved Modal End --}}


{{-- Disapproved Modal Start --}}
<div class="modal fade" id="modalDisapproved" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-550px">
        <form id="modalDisapprovedForm">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Confirmation Alert</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="mb-4" >Are you sure to you want to disapproved this Reimbursement ?  </p>

                    <div class="d-flex flex-column mb-4">
                        <label class="fs-6 fw-semibold mb-2 required">Message</label>
                        <textarea class="form-control" name="expense_message" placeholder="Enter Message" required rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="disapproved_expense_id" value="" >
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="modalDisapprovedFormButton" class="btn btn-primary">
                        <span class="indicator-label">Yes, Disapproved</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- Disapproved Modal End --}}


{{-- Amount Log Modal Starts Here --}}
<div class="modal fade" id="expenseAmountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-650px">
        <div class="modal-content rounded">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Reimbursement Amount Log</h4>
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
                <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="expenseAmountTable" >

                </table>
            </div>
        </div>
    </div>
</div>
{{-- Amount Log Modal Ends Here --}}


@endsection

@push('js-link')
<script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>

@if(!empty($login_user) && $login_user[0]['employee_department'] != 1 && $login_user[0]['employee_department'] != 2)
    <script src="{{url('assets/js/employee-expense/all-expense-manager-approved.js')}}"></script>
    <script src="{{url('assets/js/employee-expense/all-expense-script.js')}}"></script>
@else
    <script src="{{url('assets/js/employee-expense/all-expense-hr-approved.js')}}"></script>
    <script src="{{url('assets/js/employee-expense/all-expense-script.js')}}"></script>
@endif
    
@endpush
