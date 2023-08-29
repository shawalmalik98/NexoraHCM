@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Manage Loans</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if(!empty($all_loans))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">											
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Employee Loan Details
                        </a>  
                    </div>
                @endif    
                
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Grant New Loan</a>   
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
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ $total_loan }}"></div>
                            <div class="fw-semibold text-gray-400">Total Loan Given (By SourceCode)</div>
                        </div>
                        
                    </div>
                    
                </div>


                <div class="col-xl-4">
                                        
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ $loan_return }}"></div>
                            <div class="fw-semibold text-gray-400">Total Loan Return (By Employees)</div>
                        </div>
                        
                    </div>
                    
                </div>


                <div class="col-xl-4">
                                        
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{ $loan_reamining }}"></div>
                            <div class="fw-semibold text-gray-400">Total Loan Remaining (On Employees)</div>
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
                    <div class="card-toolbar" >
                        @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('employee_location')) ? $filter : '' }} {{!empty(Request::get('emp_status')) ? $filter : '' }} {{!empty(Request::get('emp_name')) ? $filter : '' }} {{!empty(Request::get('loan_issuance_date')) ? $filter : '' }} {{!empty(Request::get('loan_status')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                       
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('loans') }}" method="GET">
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
                                            
                                            <label class="form-label fw-semibold">
                                                <span>Employee Status</span>
                                            </label>
                                            
                                            <div>
                                                <select class="form-select" data-kt-select2="true" data-placeholder="Select Employee Status" name="emp_status" id="employee_data">
                                                    <option value="">Select Employee Status</option>
                                                    <option value="1" @if ( Request::get('emp_status') == '1') selected @endif>Active Employees</option>
                                                    <option value="2" @if( Request::get('emp_status') == '2') selected @endif>De-active Employees</option>
                                                </select>
                                            </div>
                                            
                                        </div>
                                        <div class="mb-3">
                                            
                                            <label class="form-label fw-semibold"><span>By Name</span></label>
                                            <div>
                                                <select class="form-select" data-kt-select2="true"  name="emp_name" id="company_emp_name">
                                                    @if(!empty($all_employees))
                                                    <option value="">All Employees</option>
                                                        @foreach($all_employees as $data)
                                                            <option value="{{$data['employee_id']}}"@if (Request::get('emp_name') == $data['employee_id']) selected @endif>{{$data['employee_code']. ' - ' .$data['employee_name']}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            
                                        </div>
                                        <div class="mb-3">
                                            
                                            <label class="form-label fw-semibold">
                                                <span>Issued Date</span> 
                                            </label>
                                            
                                            <div class="position-relative d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                                        <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                                        <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <input type="date" class="form-control  ps-12 " placeholder="Select a date" name="loan_issuance_date" max="{{ date('Y-m-d') }}" @if(!empty(Request::get('loan_issuance_date'))) value="{{ Request::get('loan_issuance_date') }}" @endif />
                                            </div>
                                            
                                        </div>

                                        <div class="mb-3">
                                        
                                            <label class="form-label fw-semibold">Loan Status</label>
                                            
                                            <div>
                                                <select class="form-select" data-kt-select2="true" name="loan_status">
                                                    <option value="">Select Type</option>
                                                    <option value="1" @if (Request::get('loan_status') == '1') selected @endif>Unpaid</option>
                                                    <option value="2" @if (Request::get('loan_status') == '2') selected @endif>Paid</option>
                                                </select>
                                            </div>
                                            
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('employee_location'))  || !empty(Request::get('emp_name')) || !empty(Request::get('loan_issuance_date')) || !empty(Request::get('emp_status')) || !empty(Request::get('loan_status')))
                                           
                                                <a href="{{ url('loans') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">Reset Filter</a>
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
                    
                    <div class="table-responsive popup-visible ">
                        
                        <div id="tableloans_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered">
                        <div class="table-responsive popup-visible ">
                            <table class="table table-bordered align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable">
                                				
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="text-center">Ref ID</th>
                                        <th class="text-center">Employee</th>
                                        <th class="text-center">Loan amount</th>
                                        <th class="text-center">Submitted amount</th>
                                        <th class="text-center">Remaining amount</th>
                                        <th class="text-center">Deduction amount</th>
                                        <th class="text-center">Deduction start</th>
                                        <th class="text-center">Loan status</th>
                                        <th class="text-center">Issued date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($all_loans))
                                        @php  $currency = CustomHelper::hcm_system_currency(); @endphp
                                        @foreach ($all_loans as $all_loan)
                                            <tr>
                                                <td class="text-center">{{ $all_loan['loan_id'] }}</td>
                                                <td class="text-center">{!! CustomHelper::getEmpProfileDiv($all_loan['employee_id']) !!}</td>
                                                <td class="text-center">{{ $all_loan['loan_given_amount'].' '.$currency[0]['system_currency']}}</td>
                                                <td class="text-center">{{ $all_loan['loan_submitted_amount'].' '.$currency[0]['system_currency'] }}</td>
                                                <td class="text-center">{{ $all_loan['loan_remaining_amount'].' '.$currency[0]['system_currency'] }}</td>

                                                @if($all_loan['loan_deduction_type'] == 1)
                                                    <td class="text-center">{{ $all_loan['loan_deduction_amount'].' '.$currency[0]['system_currency'] }}</td>
                                                @else
                                                    <td class="text-center">{{ $all_loan['loan_deduction_amount'].  " %" }}</td>
                                                @endif
                                                <td class="text-center">{{ $all_loan['loan_deduction_start']}}</td>

                                                <td class="text-center">
                                                    @if ($all_loan['loan_status'] == '1')
                                                        <span class="badge badge-light-danger fs-7 fw-bold">Unpaid</span>
                                                    @else
                                                        <span class="badge badge-light-success fs-7 fw-bold">Paid</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $all_loan['loan_issuance_date'] }}</td>
                                            
                                                <td class="text-center">
                                                    
                                                    <a href="{{ url('loans/loan-detail') . '/' . $all_loan['loan_id']  }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View loan detail">
                                                        
                                                        <span class="svg-icon svg-icon-3">
                                                           <i class="fa fa-eye"></i>
                                                        </span>
                                                        
                                                    </a>

                                                    @if($all_loan['loan_submitted_amount'] == '0' )

                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-title="Delete Loan Request" onclick="deleteloan('{{$all_loan['loan_id']}}')">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        
                                                    @else

                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                        
                                                            <span class="svg-icon svg-icon-3" disabled style="font-size: 20px; cursor: not-allowed;">
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
            
             
            <form id="add_loan_form" class="form">
             @csrf
                <div class="modal fade" id="kt_modal_new_target" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-800px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                            <h4 class="modal-title pl-4">Grant New Loan</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                            </button>
                            </div>
                            
                            
                            <div class="modal-body scroll-y pt-0 pb-10">
                                    
                                <div class="row g-9 mb-8 mt-2" >
                                    
                                    <div class="col-md-6 fv-row">
                                        {{--begin::Label--}}
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Employee</span>
                                        </label>

                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select selectemployee" data-control="select2" data-hide-search="false" data-placeholder="Select Employee" name="employee_code" required> 
                                                <option value="">Select Employee</option>
                                                @if(!empty($all_employees))
                                                    @foreach($all_employees as $all_employee)
                                                        <option value="{{ $all_employee['employee_id'] }}">{{ $all_employee['employee_name'] }} ({{ $all_employee['department_name'] }} - {{ $all_employee['designation_name'] }})</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Loan Amount</label>

                                        <div class="position-relative d-flex align-items-center">
                                            <input type="number" class="form-control " placeholder="Enter Loan Amount" step="0.01" name="loan_amount" id="amount_loan" required/>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row g-9 mb-8" >
                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Loan Type</span>
                                        </label>
                                    
                                        <select class="form-select selectemployee" data-control="select2" data-hide-search="false" data-placeholder="Select Loan Type" name="loan_type" id="reason" required> 
                                            <option value="">Reason</option>
                                            <option value="Bike Loan">Bike Loan</option>
                                            <option value="Car Loan">Car Loan</option>
                                            <option value="Property Loan">Property Loan</option>
                                            <option value="Advance Salary">Advance Salary</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 fv-row d-none reason_div">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Reason</span>
                                        </label>
                                    
                                        <input type="text" class="form-control " name="loan_other_reason" placeholder="Enter Loan Reason" id="reason_input"/>
                                    </div>

                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Loan Deduction</span>
                                        </label>
                                        <select class="form-select selectemployee" data-control="select2" data-hide-search="false" data-placeholder="Select deduction Type" name="deduction_type" id="loan_deduct_type" required> 
                                            <option value="">Loan Deduction</option>
                                            <option value="1">Fixed Amount</option>
                                            <option value="2">Percentage</option>
                                        </select>
                                    </div>
                        
                                    <div class="col-md-6 fv-row d-none" id="deduction_div" >
                                    
                                        <label class="fs-6 fw-semibold mb-2 required" id="amount_label">Loan Deduction Amount</label> 
                                        <div class="input-group deduct-amount">
                                            <input type="number" class="form-control deduct-amount" rows="5" name="deduction_amount"  placeholder="Loan Deduction Amount">
                                            <span class="input-group-text">{{$currency[0]['system_currency']}}</span>
                                        </div>    
                                    
                                        <label class="fs-6 fw-semibold mb-2 required" id="percentage_label">Loan Deduction Percentage</label>
                                        <div class="input-group deduct-percentage">
                                            <input type="number" class="form-control deduct-percentage" rows="5" name="deduction_percentage" max="100" placeholder="Loan Deduction Percentage">
                                            <span class="input-group-text">%</span>
                                        </div>    
                                    </div>

                                    <div class="col-md-6 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Loan Deduction Start</label>
                                        <input type="month" class="form-control" name="deduction_start" min="{{ date('Y-m') }}" max="{{ date('Y-m',strtotime('+1 month')) }}" placeholder="Enter Amount" required>

                                    </div>

                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Loan Approval Date</span>
                                        </label>
                                    
                                        <input type="date" class="form-control " name="loan_approval_date" max="{{ date('Y-m-d') }}" required/>
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
                    {{--end::Modal - New Target--}}
            </form>
            

             {{--- Delete loan Request --------------}}
            <div class="modal fade" tabindex="-1" id="delete-loan">
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
                        <form id="delete_loan" class="form" >
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
            
        </div>
        
    </div>

    <form class="form" id="downloadbutton" action="{{ url('loans/employee-loan-download') }}" method="POST">
        @csrf
        <div class="modal fade" id="download_modal_pdf"uploaddex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                   
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Download Employees Loans Details</h4>
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

                            <input name="emp_status" type="hidden" value="{{Request::get('emp_status')}}">

                            <input name="employee_location" type="hidden" value="{{Request::get('employee_location')}}">

                            <input name="emp_name" type="hidden" value="{{Request::get('emp_name')}}">

                            <input name="loan_issuance_date" type="hidden" value="{{Request::get('loan_issuance_date')}}">

                            <input name="loan_status" type="hidden" value="{{Request::get('loan_status')}}">
                       
                            <div class="col-md-12 mt-3 fv-row">
                                <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) loan records,
                                 Please type <b>TODP-ID</b> (case sensitive) to confirm.
                                </p>
                                <input type="text" class="form-control " name="confirmation_code" placeholder="Enter Confirmation Code" required="required" autocomplete="off" onpaste="return false">
                            </div>

                            <div class="col-md-12 mt-3 fv-row text-center">
                                <span class="d-none text-danger" id="download_message">
                                    Please wait, PDF file generation is in process. It may take a while.
                                </span>
                            </div>
                        </div> 
                           
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="download_btn" class="btn btn-primary">
                            <span class="indicator-label">Download</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
</div>

@endsection



@push('js-link')

<script src="{{url('assets/js/loan/all-loans.js')}}"></script>

    
@endpush