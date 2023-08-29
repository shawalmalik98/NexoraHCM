@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    <h1>My Reimbursement Request(s) {{(!empty(Request::get('status')) && Request::get('status') =='2' ) ? '- Approved' :''}}{{(!empty(Request::get('paid')) && Request::get('paid') =='2' ) ? '- Paid' :''}}</h1>
                </h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMyExpense">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Add New Request</a>   
                </div>
         
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            <div class="row g-6 g-xl-9">

                <div class="col-xl-4">
                                        
                    <a href="{{url('my-expense')}}" class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ (!empty($fetch_expense_total)) ? $fetch_expense_total[0]['total'] : 0}}"></div>
                            <div class="fw-semibold text-gray-400">Total Reimbursement</div>
                        </div>
                    </a>
                    
                </div>

                <div class="col-xl-4">
                                        
                    <a href="{{url('my-expense?status=2')}}" class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ (!empty($fetch_expense_total_approved)) ? $fetch_expense_total_approved[0]['total'] : 0}}"></div>
                            <div class="fw-semibold text-gray-400">Total Approved Reimbursement</div>
                        </div>
                    </a>
                    
                </div>


                <div class="col-xl-4">
                                        
                    <a href="{{url('my-expense?paid=2')}}" class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ (!empty($fetch_expense_total_paid)) ? $fetch_expense_total_paid[0]['total'] : 0}}"></div>
                            <div class="fw-semibold text-gray-400">Total Paid Reimbursement </div>
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
                    <div class="card-toolbar" > 
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
                    
                    <div class="table-responsive">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info" >
                               					 				
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Ref ID</th>
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
                                     @if(!empty($fetch_expense))
                                        @foreach ($fetch_expense as $expense)
                                        <tr @if(request()->get('expense-id') != '' && $expense['emp_expense_id'] == request()->get('expense-id')) style="background-color:bisque;" @endif>
                                                <td class="ps-8">{{ $expense['emp_expense_id'] }}</td>
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
                                                <td><b><span style="cursor: pointer;" data-bs-placement="top" data-bs-toggle="tooltip" title="View Amount Logs"  onclick="amountLog({{$expense['emp_expense_id']}})" >{{ number_format($expense['emp_expense_amount']) }}</span>{!! ($expense['update_amount']>1) ? '<span class=required ></span>' : '' !!}</b></td>

                                                <td class="text-center">
                                                    <a  href="javascript:void(0)" class="manager_modal_status_btn" data-request-id="{{$expense['emp_expense_id']}}" data-bs-toggle="tooltip" title="Approval(s)" > 
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
                                                    @if ($expense['emp_expense_approved_by_hr'] == 1 && !in_array($expense['emp_expense_id'],$responsed_request))

                                                    <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" onclick="expenseEditModelFunc({{json_encode($expense)}})">
                                                        
                                                        <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Edit Reimbursement Detail">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        
                                                    </a>

                                                    <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalDelete" onclick="deleteExpenseId('{{$expense['emp_expense_id']}}')">
                                                        <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Delete Reimbursement">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                    </a>

                                                    @else

                                                    <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" style="cursor: not-allowed;" >
                                                        
                                                        <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Edit Expense Detail">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        
                                                    </a>

                                                    <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" style="cursor: not-allowed;" >
                                                        <span class="svg-icon svg-icon-3" >
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
            
            
            <form id="addMyExpenseForm" class="form">
                {{--start::Modal - New Expense--}}
                @csrf  
                <div class="modal fade" id="addMyExpense" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Add Reimbursement Request</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                                </button>
                            </div>
                            
                            
                            <div class="modal-body scroll-y pt-0 pb-5">
                                
                                        
                                <div class="col-md-12 mb-8 mt-3 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Expense Date</label>
                                    <input type="date" class="form-control" placeholder="Select a date" name="expense_date" required="" max="{{date('Y-m-d')}}" >
                                </div>
                                    
                                
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Type</label>
                                    <select class="form-select expenseselect" data-control="select2" data-hide-search="false" data-placeholder="Select Expense Type" name="expense_type" required> 
                                        <option value="">Select</option>
                                        @if(!empty($expense_type_list))
                                            @foreach($expense_type_list as $key => $type)
                                                <option value="{{$key}}">{{$type}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2">Expense Attachment</label>
                                    <input type="file" accept=".xlsx,.csv,.docx,.txt,application/pdf,image/*" class="form-control" name="expense_attachment">
                                </div>
                                    <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Amount</label>
                                    <input type="number" class="form-control " name="expense_amount" placeholder="Enter Expense Amount" required="">
                                </div>

                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Reason</label>
                                    <textarea rows="4" class="form-control  kt_docs_maxlength_threshold2" maxlength="500" name="expense_reason" placeholder="Enter Expense Reason" required=""></textarea>
                                </div>
                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">
                                
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="addMyExpenseFormButton" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                {{--end::Modal - New Expense--}}
            </form>    


            <form id="updateMyExpenseForm" class="form">
                {{--start::Modal - Update Expense--}}
                @csrf  
                <div class="modal fade" id="updateMyExpense" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Update Reimbursement Request</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                                </button>
                            </div>
                            
                            
                            <div class="modal-body scroll-y pt-0 pb-5">
                                
                                        
                                <div class="col-md-12 mb-8 mt-3 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Expense Date</label>
                                    <input type="date" class="form-control" placeholder="Select a date" name="expense_date" required="" max="{{date('Y-m-d')}}" >
                                </div>
                                    
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Type</label>
                                    <select class="form-select expenseselectupdate" data-control="select2" data-hide-search="true" data-placeholder="Select Expense Type" name="expense_type" required> 
                                        <option value="">Select</option>
                                        @if(!empty($expense_type_list))
                                            @foreach($expense_type_list as $key => $type)
                                                <option value="{{$key}}">{{$type}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2">Expense Attachment</label>
                                    <input type="file" accept=".xlsx,.csv,.docx,.txt,application/pdf,image/*" class="form-control" name="expense_attachment">
                                </div>
                                    <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Amount</label>
                                    <input type="number" class="form-control " name="expense_amount" placeholder="Enter Expense Amount" required="">
                                </div>

                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Reason</label>
                                    <textarea rows="4" class="form-control  kt_docs_maxlength_threshold2" id="textarea_update" maxlength="500" name="expense_reason" placeholder="Enter Expense Reason" required=""></textarea>
                                </div>
                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">
                                <input type="hidden" name="expense_id" value="" />
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="updateMyExpenseFormButton" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                {{--end::Modal - Update Expense--}}
            </form>    

            {{-- Request Message Modal Starts Here --}}
            <div class="modal fade" id="message_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-top">
                    <div class="modal-content rounded">
                        <div class="modal-content rounded">
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Reimbursement Reason</h4>
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

            {{-- Delete Modal Start --}}
            <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-550px">
                    <form id="modalDeleteForm">
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
                                <p>Are you sure to you want to delete this Reimbursement ?</p>
                            </div>
            
                            <div class="modal-footer">
                                <input type="hidden" name="expense_id" value="" >
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="modalDeleteFormButton" class="btn btn-primary">
                                    <span class="indicator-label">Yes, Delete</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Delete Model End --}}


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
            
        </div>
        
    </div>
    
</div>

@endsection

@push('js-link')
<script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>

<script src="{{url('assets/js/employee-expense/my-expense.js')}}"></script>
    
@endpush
