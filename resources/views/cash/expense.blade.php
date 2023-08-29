@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    @if (empty(Request::get('expense_period')))
                        <h1>Expense Sheet ({{ date('F') . '-' . date('Y') }})</h1>
                    @else
                        <h1>Expense Sheet ({{  date('F - Y', strtotime(Request::get('expense_period'))) }})</h1>
                    @endif
                </h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                @if(!empty($expenses))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Office Expense Report
                        </a>
                    </div>
                @endif    

                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#expense_locked">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Lock Expense Sheet
                    </a>
                </div>
                
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addexpense">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Add New Expense</a>   
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
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{$total_expense}}"></div>
                            <div class="fw-semibold text-gray-400">Total Expense 
                                @if (empty(Request::get('expense_period')))
                                    ({{ date('F') . '-' . date('Y') }})
                                @else
                                    ({{  date('F - Y', strtotime(Request::get('expense_period'))) }})
                                @endif
                            </div>
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
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('expense_period')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                       
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('daily-expense') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                    </div>

                                    <div class="separator border-gray-200"></div>
                                    <div class="px-7 py-5">
                                        
                                        
                                        <div class="mb-10">
                                            
                                            <label class="form-label fw-semibold">
                                                <span class="required">Expense Period</span> 
                                            </label>
                                            
                                            <div class="position-relative d-flex align-items-center">
                                            
                                                    
                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                                        <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                                        <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                {{--end::Svg Icon--}}
                                                {{--end::Icon--}}
                                                {{--begin::Datepicker--}}
                                                <input type="month" class="form-control  ps-12 " placeholder="Select a date" name="expense_period" @if(!empty(Request::get('expense_period'))) value="{{ Request::get('expense_period') }}" @else value="{{ date('Y-m') }}" @endif />
                                                {{--end::Datepicker--}}
                                            </div>
                                            
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('expense_period')))
                                                <a href="{{ url('daily-expense') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
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
                    
                    <div class="table-responsive popup-visible ">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                               					 				
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4 min-w-50px sorting">Ref ID
                                        </th>
                                        <th class="min-w-50px sorting">Date
                                        </th>
                                        <th class="min-w-50px sorting">Reason
                                        </th>
                                        <th class="min-w-50px sorting">Source
                                        </th>
                                        <th class="min-w-50px sorting">Attachment
                                        </th>
                                        <th class="min-w-50px sorting">Amount	
                                        </th>
                                        <th class="min-w-50px sorting">Created By
                                        </th>
                                        <th class="min-w-50px sorting">Created At	
                                        </th>
                                        <th class="min-w-50px sorting">Action	
                                        </th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($expenses))
                                        @foreach ($expenses as $expense)
                                            <tr>
                                                <td class="ps-8">{{ $expense['expense_id'] }}</td>
                                                <td>{{ $expense['expense_date'] }}</td>
                                                <td>{{ $expense['expense_reason'] }}</td>
                                                <td>{{ $expense['expense_source'] }}</td>
                                                <td class="ps-16">
                                                    @if(!empty($expense['expense_attachment']))
                                                        <a href="{{ url('/daily-expense/download-expense-file?path=expenses/'.$expense['expense_attachment'])}}&title=office expense file" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Download File"><i class="fa fa-download" aria-hidden="true"></i></a>
                                                    @else
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-sm me-1 " disabled style="cursor: not-allowed;" data-bs-toggle="tooltip" title="Download File"><i class="fa fa-download" aria-hidden="true"></i></a>
                                                    @endif
                                                </td>
                                                <td>{{ $expense['expense_amount'] }}</td>
                                                <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($expense['expense_created_by']) !!}</td>
                                                <td>{{ $expense['expense_created_at'] }}</td>
                                                <td>
                                                    <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#updateexpense" onclick="expenseEditModel( '{{$expense['expense_id']}}','{{$expense['expense_date']}}','{{ $expense['expense_reason']}}','{{$expense['expense_amount']}}', '{{$expense['expense_source']}}' ,'{{$expense['expense_attachment']}}')">
                                                        
                                                        <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Edit Expense Detail">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        
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
            
            
            <form id="addexpenseForm" class="form">
                @csrf  
                <div class="modal fade" id="addexpense" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Add Expense</h4>
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
                                    <label class="required fs-6 fw-semibold mb-2">Expense Date</label>
                                    <input type="date" class="form-control" placeholder="Select a date" name="expense_date" required="">
                                </div>
                                    
                                {{--end::Input group--}}
                                
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2">Expense Reason</label>
                                    <input type="text" class="form-control  kt_docs_maxlength_threshold2" maxlength="500" name="expense_reason" placeholder="Enter Expense Reason" required="">
                                </div>
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Source</label>
                                    <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Expense Source" name="expense_source" required> 
                                        <option value="">Select Cash</option>
                                        <option value="ATM">ATM</option>
                                        <option value="CHEQUE">CHEQUE</option>
                                        <option value="CASH">CASH</option>
                                        <option value="REIMBURESEMENT">REIMBURESEMENT</option>
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
                {{--end::Modal - New Target--}}
            </form>
            
            <form id="updateexpenseForm" class="form">
                @csrf  
                <div class="modal fade" id="updateexpense" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Update Expense</h4>
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
                                    <input type="hidden" name="expense_id">
                                    <label class="required fs-6 fw-semibold mb-2">Expense Date</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <input type="date" class="form-control" name="expense_date" id="exp_date" required="">
                                    </div>
                                </div>

                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2">Expense Reason</label>
                                    <input type="text" class="form-control  kt_docs_maxlength_threshold2" maxlength="500" name="expense_reason" id="exp_reason"  required="">
                                </div>
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Amount</label>
                                    <input type="number" class="form-control  kt_docs_maxlength_threshold2" maxlength="500" name="expense_amount" id="exp_amount"  required="">
                                </div>
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Expense Source</label>
                                    <select class="form-select " data-control="select2" data-hide-search="true" name="expense_source" id="expense_source" required> 
                                        <option value="ATM">ATM</option>
                                        <option value="CHEQUE">CHEQUE</option>
                                        <option value="CASH">CASH</option>
                                        <option value="REIMBURESEMENT">REIMBURESEMENT</option>
                                    </select>
                                </div>
                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2">Expense Attachment</label>
                                    <input type="file" accept=".xlsx,.csv,.docx,.txt,application/pdf,image/*" class="form-control " name="expense_attachment" >
                                </div>
                                
                            </div>
                            
                            <div class="modal-footer justify-content-center">
                                    
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit-btn" class="btn btn-primary">
                                    <span class="indicator-label">Update</span>
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

            <form id="expense_lock_form" class="form">
                @csrf  
                <div class="modal fade" id="expense_locked" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Lock Expense Sheet</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                                </button>
                            </div>
                            
                            <div class="modal-body scroll-y pt-2 pb-4">
                                         
                                <div class="col-md-12 mb-8 mt-3 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Expense Month</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <input type="month" class="form-control" placeholder="Select month" max="{{ date('Y-m') }}" value="{{ date('Y-m') }}" name="expense_date" id="expenses_date" required="">
                                    </div>
                                </div>
                                    
                            </div>
                            
                            <div class="modal-footer justify-content-center">
                                
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="locked-btn" class="btn btn-primary">
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

        <form class="form" id="downloadbutton" action="{{ url('daily-expense/daily-expense-download') }}" method="POST">
            @csrf
            <div class="modal fade" id="download_modal_pdf"uploaddex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-650px">
                    <div class="modal-content rounded">

                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Download Office Expense Report</h4>
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

                                <input name="expense_period" type="hidden" value="{{ Request::get('expense_period') }}">

                                <div class="col-md-12 mt-3 fv-row">
                                    <p onselectstart="javascript:return false;" >This action required a security check. If you want to download office expense report,
                                     Please type <b>TODP-ID</b> (case sensitive) to confirm.
                                    </p>
                                    <input type="text" class="form-control " name="confirmation_code" placeholder="Enter Confirmation Code" required="required" autocomplete="off" onpaste="return false" >
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
    
</div>

@endsection

@push('js-link')

<script src="{{url('assets/js/cash/expense.js')}}"></script>
    
@endpush
