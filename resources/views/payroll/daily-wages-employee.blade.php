@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Manage Daily Wages {{ (!empty($month)) ? "(".date("F", mktime(0, 0, 0, $month, 10))."-".$year.")" : '' }}</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">


                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#calculateWageAmount">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M3 3V17H7V21H15V9H20V3H3Z" fill="currentColor"/>
                                <path d="M20 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H20C20.6 2 21 2.4 21 3V21C21 21.6 20.6 22 20 22ZM19 4H4V8H19V4ZM6 18H4V20H6V18ZM6 14H4V16H6V14ZM6 10H4V12H6V10ZM10 18H8V20H10V18ZM10 14H8V16H10V14ZM10 10H8V12H10V10ZM14 18H12V20H14V18ZM14 14H12V16H14V14ZM14 10H12V12H14V10ZM19 14H17V20H19V14ZM19 10H17V12H19V10Z" fill="currentColor"/>
                            </svg>
                        </span>
                        Calculate Wage(s) Amount</a>
                </div>
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMain">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Generate Wage(s)</a>
                </div>
                @if(!empty($daily_wages))
                <div class="m-0">
                    <a href="javascript:;" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDisburse" >
                       
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19ZM14 14C14 13.4 13.6 13 13 13H5C4.4 13 4 13.4 4 14C4 14.6 4.4 15 5 15H13C13.6 15 14 14.6 14 14ZM16 15.5C16 16.3 16.7 17 17.5 17H18.5C19.3 17 20 16.3 20 15.5C20 14.7 19.3 14 18.5 14H17.5C16.7 14 16 14.7 16 15.5Z" fill="currentColor"/>
                                <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="currentColor"/>
                            </svg>
                        </span>
                        Disbursement</a>
                </div>
                @endif


                @if(!empty($fetch_employee_disburse))
                <div class="m-0">
                    <a href="javascript:;" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteDepartment" >
                       
                        <span class="svg-icon svg-icon-1">
                            <svg width="23" height="23" viewBox="-3 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Delete Wage(s)</a>
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
                    
                    <div class="card-toolbar" >
                        @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('m')) ? $filter : '' }} {{ !empty(Request::get('y')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter
                        </a>
                       
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                            <form action="{{ url('daily-wages-payroll') }}" method="GET">

                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                </div>

                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5">
                                        
                                    <div class="mb-3">
                                            
                                        <label class="form-label fw-semibold">Select Month</label>
                                            
                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Month"name="m"
                                            required="required">
                                            <option value="">Select Month</option>
                                            <option value="01" @if (!empty(Request::get('m')) && Request::get('m') == '01') selected @elseif (empty(Request::get('m')) && date('m') == '01') selected @endif>January
                                            </option>
                                            <option value="02" @if (!empty(Request::get('m')) && Request::get('m') == '02') selected @elseif (empty(Request::get('m')) && date('m') == '02') selected @endif>February
                                            </option>
                                            <option value="03" @if (!empty(Request::get('m')) && Request::get('m') == '03') selected @elseif (empty(Request::get('m')) && date('m') == '03') selected @endif>March
                                            </option>
                                            <option value="04" @if (!empty(Request::get('m')) && Request::get('m') == '04') selected @elseif (empty(Request::get('m')) && date('m') == '04') selected @endif>April
                                            </option>
                                            <option value="05" @if (!empty(Request::get('m')) && Request::get('m') == '05') selected @elseif (empty(Request::get('m')) && date('m') == '05') selected @endif>May
                                            </option>
                                            <option value="06" @if (!empty(Request::get('m')) && Request::get('m') == '06') selected @elseif (empty(Request::get('m')) && date('m') == '06') selected @endif>June
                                            </option>
                                            <option value="07" @if (!empty(Request::get('m')) && Request::get('m') == '07') selected @elseif (empty(Request::get('m')) && date('m') == '07') selected @endif>July
                                            </option>
                                            <option value="08" @if (!empty(Request::get('m')) && Request::get('m') == '08') selected @elseif (empty(Request::get('m')) && date('m') == '08') selected @endif>August
                                            </option>
                                            <option value="09" @if (!empty(Request::get('m')) && Request::get('m') == '09') selected @elseif (empty(Request::get('m')) && date('m') == '09') selected @endif>September
                                            </option>
                                            <option value="10" @if (!empty(Request::get('m')) && Request::get('m') == '10') selected @elseif (empty(Request::get('m')) && date('m') == '10') selected @endif>October
                                            </option>
                                            <option value="11" @if (!empty(Request::get('m')) && Request::get('m') == '11') selected @elseif (empty(Request::get('m')) && date('m') == '11') selected @endif>November
                                            </option>
                                            <option value="12" @if (!empty(Request::get('m')) && Request::get('m') == '12') selected @elseif (empty(Request::get('m')) && date('m') == '12') selected @endif>December
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-10">
                                        
                                        <label class="form-label fw-semibold">Select Year</label>
                                        
                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Year" name="y" required="required">
                                            <option value="">Select Year</option>
                                            @if(!empty($dynamic_years))
                                                @foreach ($dynamic_years as $year_list)
                                                    <option value="{{$year_list}}" @if (!empty(Request::get('y')) && Request::get('y') == $year_list) selected @elseif(empty(Request::get('y')) && date('Y') == $year_list) selected  @endif>{{$year_list}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                       
                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('m')) && !empty(Request::get('y')))
                                        
                                        <a href="{{ url('daily-wages-payroll') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                            >Reset Filter</a>
                                        @endif
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary" >Apply</button>
                                    </div>
                                        
                                </div>
                            </form>
                        </div>
                    </div>
                </div> 
   
                <div class="card-body py-3">

                    <div class="flex-wrap" >
                        <div class="d-flex align-items-center gap-2">
                            
                            @if(!empty($daily_wages))
                            <a href="{{url('daily-wages-payroll/download-daily-wages-pdf?month='.$month.'&year='.$year)}}" class="btn btn-secondary buttons-excel buttons-html5 mb-5 btn-sm btn-primary" tabindex="0" target="_blank" ><span>View Wages Record</span></a>
                            <a href="javascript:;" class="btn btn-secondary buttons-excel buttons-html5 mb-5 btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDownload" ><span>Download Wages Record</span></a>
                            @endif

                            <div class="on-check d-none" >
                                <a href="#" class="btn btn-danger buttons-excel buttons-html5 mb-5 btn-sm btn-primary" tabindex="0" data-bs-toggle="modal" data-bs-target="#modalDeleteSelected" ><span class="svg-icon svg-icon-3" >
                                    <svg width="24" height="24" viewBox="-3 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                        <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                        <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                    </svg>
                                </span></a>

                            </div>

                        </div>
                    </div>
                    <div class="table-responsive">
                        
                        <div id="tablePayroll_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="table-responsive">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info"  style="white-space:nowrap;" >
                                				 				
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th></th>
                                        <th class="text-center" >Ref ID</th>
                                        <th>Employee Name</th>
                                        <th>Pay Period</th>
                                        <th>Gross Salary</th>
                                        <th>Extra Earning</th>
                                        <th>Total Deduction</th>
                                        <th>Net Salary</th>
                                        <th>Generation Date</th>
                                        <th>Update Date</th>
                                        <th>Disburse</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @if(!empty($daily_wages))
                                        @foreach ($daily_wages as $daily_wage)
                                        <tr>    
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom" style="{{($daily_wage['dw_disburse'] != 2 ) ? '' : 'cursor: not-allowed;'}}" >
                                                    <input class="form-check-input checked"  type="checkbox" data-id="{{$daily_wage['dw_id']}}" value="{{$daily_wage['dw_id']}}" {{($daily_wage['dw_disburse'] != 2 ) ? '' : 'disabled' }}    />
                                                </div>
                                            </td>

                                            
                                            <td class="ps-8">{{ $daily_wage['dw_id'] }}</td>
                                            <td>{!! CustomHelper::getEmpProfileDiv($daily_wage['dw_employee_id']) !!}</td>
                                            <td>{{ date('d-M-Y',strtotime($daily_wage['dw_of_date'])) }} </td>
                                            <td>{{ number_format($daily_wage['dw_employee_gross_daily_wages'],2) }}</td>
                                            <td>{{ number_format($daily_wage['dw_employee_extra_daily_wages'],2) }}</td>
                                            <td>{{ number_format($daily_wage['dw_employee_deduction'],2) }}</td>
                                            <td>{{ number_format($daily_wage['dw_employee_net_daily_wages'],2) }}</td>
                                            <td>{{ date('d-M-Y',strtotime($daily_wage['dw_created_at'])) }}</td>
                                            <td>{{ !empty($daily_wage['dw_updated_at']) ? date('d-M-Y',strtotime($daily_wage['pc_updated_at'])) : ''}}</td>
                                            
                                            <td>
                                                @if( $daily_wage['dw_disburse'] == 2  )
                                                <span class="badge badge-light-success font-weight-bold">Disbursed</span>
                                                @else
                                                <span class="badge badge-light-danger font-weight-bold">Not, Disburse</span>
                                                @endif
                                            </td>
                                                    
                                            <td>
                                                <a href="{{($daily_wage['dw_disburse'] != 2 ) ? url('daily-wages-payroll/edit-daily-wages/'.$daily_wage['dw_id']) : 'javascript:;' }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Edit Payslip" style="{{($daily_wage['dw_disburse'] != 2 ) ? '' : 'cursor: not-allowed;'}}"  > 
                                                    <span class="svg-icon svg-icon-3"  >
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>

                                                <a  href="{{ url('pdf-daily-wages/'.$daily_wage['dw_id'])}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Payslip" target="_blank" > 
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                    
                                                </a>

                                                <a  href="{{ url('pdf-daily-wages/'.$daily_wage['dw_id'])}}?download=TRUE" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Download Payslip" target="_blank" > 
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                    
                                                </a>
                                                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" {{($daily_wage['dw_disburse'] != 2 ) ? 'data-bs-toggle=modal data-bs-target=#modalDelete onclick=deletePayrollId('.$daily_wage['dw_id'].') ' : ''}} style="{{($daily_wage['dw_disburse'] != 2 ) ? '' : 'cursor: not-allowed;'}}" >
                                                    <span class="svg-icon svg-icon-3" >
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
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
        </div>
    </div>
</div>

@include('include/generate-bulk-wages')

@include('include/generate-select-wages')

@include('include/download-wages')

@include('include/calculate-wages-amount')

@include('include/generate-individual-wages')

@include('include/wages-disbursement')

@include('include/delete-wages')

<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-650px">
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
                    <p>Are you sure to you want to delete this payslip ?</p>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="daily_wage_id" value="" >
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


<div class="modal fade" id="modalDeleteSelected" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-650px">
        <form id="modalDeleteSelectedForm">
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
                    <p>Are you sure to you want to delete selected payslips ?</p>
                </div>

                <div class="modal-footer">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="modalDeleteSelectedFormButton" class="btn btn-primary">
                        <span class="indicator-label">Yes, Delete</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection




@push('js-link')
<script>

    $(function(){
    $('#by_name').select2({
        dropdownParent: $('#addPayrollIndividual')
        })
    });

    $("#days_assign").change(function(){
        var value = $(this).children("option:selected").val();
        if(value == "single"){
            $('.only_date_input').removeClass('d-none');
            $('.from_to_date_input').addClass('d-none');
            $('.till_note').addClass('d-none');
            $('.date-control').val('');
            $("#wage_date").prop('required',true);
            $(".date-control").prop('required',false);
        }else if(value == "multiple"){
            $('#wage_date').val('');
            $('.only_date_input').addClass('d-none');
            $('.from_to_date_input').removeClass('d-none');
            $('.till_note').addClass('d-none');
            $(".date-control").prop('required',true);
            $("#wage_date").prop('required',false);
        }else{
            $('#wage_date').val('');
            $('.date-control').val('');
            $('.only_date_input').addClass('d-none');
            $('.from_to_date_input').addClass('d-none');
            $('.till_note').removeClass('d-none');
            $(".date-control").prop('required',false);
            $("#wage_date").prop('required',false);
        }
    });

    $("#days_assign_bulk").change(function(){
        var value = $(this).children("option:selected").val();
        if(value == "single"){
            $('.only_date_input_bulk').removeClass('d-none');
            $('.from_to_date_input_bulk').addClass('d-none');
            $('.till_note_bulk').addClass('d-none');
            $('.date-control-bulk').val('');
            $("#wage_date_bulk").prop('required',true);
            $(".date-control-bulk").prop('required',false);
        }else if(value == "multiple"){
            $('#wage_date_bulk').val('');
            $('.only_date_input_bulk').addClass('d-none');
            $('.from_to_date_input_bulk').removeClass('d-none');
            $('.till_note_bulk').addClass('d-none');
            $(".date-control-bulk").prop('required',true);
            $("#wage_date_bulk").prop('required',false);
        }else{
            $('#wage_date_bulk').val('');
            $('.date-control-bulk').val('');
            $('.only_date_input_bulk').addClass('d-none');
            $('.from_to_date_input_bulk').addClass('d-none');
            $('.till_note_bulk').removeClass('d-none');
            $(".date-control-bulk").prop('required',false);
            $("#wage_date_bulk").prop('required',false);
        }
    });

    $("#days_assign_calculate").change(function(){
        var value = $(this).children("option:selected").val();
        if(value == "single"){
            $('.only_date_input_calculate').removeClass('d-none');
            $('.from_to_date_input_calculate').addClass('d-none');
            $('.till_note_calculate').addClass('d-none');
            $('.date-control-calculate').val('');
            $("#wage_date_calculate").prop('required',true);
            $(".date-control-calculate").prop('required',false);
        }else if(value == "multiple"){
            $('#wage_date_calculate').val('');
            $('.only_date_input_calculate').addClass('d-none');
            $('.from_to_date_input_calculate').removeClass('d-none');
            $('.till_note_calculate').addClass('d-none');
            $(".date-control-calculate").prop('required',true);
            $("#wage_date_calculate").prop('required',false);
        }else{
            $('#wage_date_calculate').val('');
            $('.date-control-calculate').val('');
            $('.only_date_input_calculate').addClass('d-none');
            $('.from_to_date_input_calculate').addClass('d-none');
            $('.till_note_calculate').removeClass('d-none');
            $(".date-control-calculate").prop('required',false);
            $("#wage_date_calculate").prop('required',false);
        }
    });


    $('#department_all').click(function() {
         
         var checkbox = $('#department_all').prop('checked');
 
         if(checkbox){
             $('#departments_all option').prop('selected', true);
             $('#selectDepartment').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
             $('#selectDepartment').find('.select2-search__field').attr('placeholder', '');
         }else{
             $('#departments_all option').prop('selected', false);
             $('#selectDepartment').find('.select2-selection__rendered').html('');
             $('#selectDepartment').find('.select2-search__field').attr('placeholder', 'Select Department');
             $('#selectDepartment').find('.select2-search__field').css('width', '100%');
             
         }
           
     })

    $('#department_all_calculate').click(function() {
         
         var checkbox = $('#department_all_calculate').prop('checked');
 
         if(checkbox){
             $('#departments_all_calculate option').prop('selected', true);
             $('#selectDepartmentCalculate').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all_calculate-container-choice-5nle-Human Resource">All Departments</span>');
             $('#selectDepartmentCalculate').find('.select2-search__field').attr('placeholder', '');
         }else{
             $('#departments_all_calculate option').prop('selected', false);
             $('#selectDepartmentCalculate').find('.select2-selection__rendered').html('');
             $('#selectDepartmentCalculate').find('.select2-search__field').attr('placeholder', 'Select Department');
             $('#selectDepartmentCalculate').find('.select2-search__field').css('width', '100%');
             
         }
           
    })

    $('#department_all_delete').click(function() {
         
         var checkbox = $('#department_all_delete').prop('checked');
 
         if(checkbox){
             $('#departments_all_delete option').prop('selected', true);
             $('#selectDeleteDepartment').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all_delete-container-choice-5nle-Human Resource">All Departments</span>');
             $('#selectDeleteDepartment').find('.select2-search__field').attr('placeholder', '');
         }else{
             $('#departments_all_delete option').prop('selected', false);
             $('#selectDeleteDepartment').find('.select2-selection__rendered').html('');
             $('#selectDeleteDepartment').find('.select2-search__field').attr('placeholder', 'Select Department');
             $('#selectDeleteDepartment').find('.select2-search__field').css('width', '100%');
             
         }
           
    })


    $('#select_employee_check').click(function() {
         
         var checkbox = $('#select_employee_check').prop('checked');
 
         if(checkbox){
             $('#select_employee option').prop('selected', true);
             $('#selectEmployee').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-select_employee-container-choice-5nle-Human Resource">All Employee</span>');
             $('#selectEmployee').find('.select2-search__field').attr('placeholder', '');
         }else{
             $('#select_employee option').prop('selected', false);
             $('#selectEmployee').find('.select2-selection__rendered').html('');
             $('#selectEmployee').find('.select2-search__field').attr('placeholder', 'Select Employee');
             $('#selectEmployee').find('.select2-search__field').css('width', '100%');
             
         }
           
    })

    $(function(){
        $('#departments_all').select2({
            dropdownParent: $('#addPayroll')
        })
        $('#tax_deduction_bulk').select2({
            dropdownParent: $('#addPayroll')
        })
        $('#add_daily_wages_employee').select2({
            dropdownParent: $('#addDailyWages')
        })
        $('#days_assign').select2({
            dropdownParent: $('#addDailyWages')
        })
        $('#days_assign_bulk').select2({
            dropdownParent: $('#addPayroll')
        })
        $('#days_assign_calculate').select2({
            dropdownParent: $('#calculateWageAmount')
        })
        $('#tax_deduction_individual').select2({
            dropdownParent: $('#addPayrollIndividual')
        })
        
    });

    $('#calculateWageAmountForm').submit(function(e) {

        e.preventDefault();

        var url = "{{url('daily-wages-payroll/calculate-daily-wages')}}"


        $('.submit_btn').prop('disabled', true);
        $('.submit_btn').attr('data-kt-indicator', 'on');
        $('.submit_btn').css('cursor', 'not-allowed');

        let form_data = new FormData(this);

        body_data = {
    
            // Adding method type
            method: 'POST',

            // Adding body or contents to send
            // body: JSON.stringify({form_data}),
            body : form_data,
        
            // Adding headers to the request
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }

        fetch(url,body_data)

        // Converting to JSON
        .then(response =>{
            if (response.ok) {
                return response.json();
            }

            return Promise.reject(response);
        })

        // Displaying results to console
        .then(json =>{

        if (json.status == 'TRUE') {

            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

            var confirmButtonText = "Ok, got it!";
            

            Swal.fire({
            title: json.title,
            text: json.text,
            icon: json.icon,
            buttonsStyling: false,
            confirmButtonText: confirmButtonText,
            customClass: {
            confirmButton: "btn btn-primary"
            }

            }).then(function(result) {
                if (result.value) {
                    
                    if(json.link){
                        window.location.href = base_url+"/"+json.link;
                    }else{
                        location.reload();
                    }
                    
                }
            });


        } else {

            var confirmButtonText = "Ok, got it!";
            
            if(json.button){
                confirmButtonText = json.button;
               
            }

            Swal.fire({
            title: json.title,
            text: json.text,
            icon: json.icon,
            buttonsStyling: false,
            confirmButtonText: confirmButtonText,
            customClass: {
                confirmButton: "btn btn-primary"
            }
            }).then(function(result) {
                if (result.value) {
                    if(json.link){
                        window.location.href = base_url+"/"+json.link;
                    }
                }
            });


            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

        }

        }).catch(response => {

            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

            Swal.fire({
                icon: 'warning',
                title: response.statusText,
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                confirmButton: "btn btn-primary"
                }
                
            })

        });
    })


    $('#addDailyWagesForm,#addPayrollForm').submit(function(e) {

        e.preventDefault();

        var url = "{{url('daily-wages-payroll/individual-generate-daily-wages')}}"

        $('.submit_btn').prop('disabled', true);
        $('.submit_btn').attr('data-kt-indicator', 'on');
        $('.submit_btn').css('cursor', 'not-allowed');

        let form_data = new FormData(this);

        body_data = {

            // Adding method type
            method: 'POST',

            // Adding body or contents to send
            // body: JSON.stringify({form_data}),
            body : form_data,

            // Adding headers to the request
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }

        fetch(url,body_data)

        // Converting to JSON
        .then(response =>{
            if (response.ok) {
                return response.json();
            }

            return Promise.reject(response);
        })

        // Displaying results to console
        .then(json =>{

        if (json.status == 'TRUE') {

            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

            var confirmButtonText = "Ok, got it!";
            
            if(json.button){
                confirmButtonText = json.button;
            
            }

            Swal.fire({
            title: json.title,
            text: json.text,
            icon: json.icon,
            buttonsStyling: false,
            confirmButtonText: confirmButtonText,
            customClass: {
            confirmButton: "btn btn-primary"
            }

            }).then(function(result) {
                if (result.value) {
                    
                    if(json.link){
                        window.location.href = base_url+"/"+json.link;
                    }else{
                        location.reload();
                    }
                    
                }
            });


        } else {

            var confirmButtonText = "Ok, got it!";
            
            if(json.button){
                confirmButtonText = json.button;
            
            }

            Swal.fire({
            title: json.title,
            text: json.text,
            icon: json.icon,
            buttonsStyling: false,
            confirmButtonText: confirmButtonText,
            customClass: {
                confirmButton: "btn btn-primary"
            }
            }).then(function(result) {
                if (result.value) {
                    if(json.link){
                        window.location.href = base_url+"/"+json.link;
                    }
                }
            });

            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

        }

        }).catch(response => {

            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

            Swal.fire({
                icon: 'warning',
                title: response.statusText,
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                confirmButton: "btn btn-primary"
                }
                
            })

        });
    })


    $('#modalDeleteSelectedForm').submit(function(e) {

    e.preventDefault();

    var url = "{{url('daily-wages-payroll/delete-selected-daily-wages')}}"


    $('#modalDeleteSelectedFormButton').prop('disabled', true);
    $('#modalDeleteSelectedFormButton').attr('data-kt-indicator', 'on');
    $('#modalDeleteSelectedFormButton').css('cursor', 'not-allowed');

    var values = [];  

    var checkbox = dataTable.$(".checked:checked", {"page": "all"});
    
    if(checkbox.length > 0){
        
    $(checkbox).each(function() {  
            values.push($(this).attr('data-id'));
        });  
        var value = values.join(",");
        
    }else{

        value = request_id;

    }
    
    var form_data = {payroll_id : value};

    body_data = {

        // Adding method type
        method: 'POST',

        // Adding body or contents to send
        body: JSON.stringify(form_data),
        // body : form_data,

        // Adding headers to the request
        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            "Content-type": "application/json; charset=UTF-8"
        }
    }

    fetch(url,body_data)

    // Converting to JSON
    .then(response =>{
        // response.json()
        if (response.ok) {
            return response.json();
        }

        return Promise.reject(response);
    })


    // Displaying results to console
    .then(json =>{

    if (json.status == 'TRUE') {

        $('#modalDeleteSelectedFormButton').prop('disabled', false);
        $('#modalDeleteSelectedFormButton').removeAttr('data-kt-indicator');
        $('#modalDeleteSelectedFormButton').css('cursor', 'pointer');

        Toast.fire({
            icon: 'success',
            title: json.msg,
            timer: 3000,
        })
                
        setTimeout(() => {
            location.reload();
        }, 3000);

    } else {

        Toast.fire({
            icon: 'warning',
            title: json.msg,
            timer: 3000,
        })

        $('#modalDeleteSelectedFormButton').prop('disabled', false);
        $('#modalDeleteSelectedFormButton').removeAttr('data-kt-indicator');
        $('#modalDeleteSelectedFormButton').css('cursor', 'pointer');

    }

    }).catch(response => {

        $('#modalDeleteSelectedFormButton').prop('disabled', false);
        $('#modalDeleteSelectedFormButton').removeAttr('data-kt-indicator');
        $('#modalDeleteSelectedFormButton').css('cursor', 'pointer');

        Toast.fire({
            icon: 'warning',
            title:  response.statusText,
            timer: 3000,
        })

    });


    })

    $('#wage_date').change(function() {
        
        $('#add_daily_wages_employee').text('');

        var url = "{{url('daily-wages-payroll/get-employee-daily-wages')}}"

        let form_data = {
            'wage_date' : $('#wage_date').val(),
        };

        body_data = {

            // Adding method type
            method: 'POST',

            // Adding body or contents to send
            body: JSON.stringify(form_data),
            // body : form_data,

            // Adding headers to the request
            headers: {  

                "Content-type": "application/json; charset=UTF-8",
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }

        fetch(url,body_data)

        // Converting to JSON
        .then(response =>{
            // response.json()
            if (response.ok) {
                return response.json();
            }

            return Promise.reject(response);
        })


        // Displaying results to console
        .then(json =>{

        if (json.status == 'TRUE') {
            $('#add_daily_wages_employee').append(json.get_employees);
        } else {


        }

        }).catch(response => {

        });
    })

    $('#days_assign').change(function() {
        
        $('#add_daily_wages_employee').text('');

        var url = "{{url('daily-wages-payroll/get-employee-daily-wages')}}"

        let form_data = {
            'wage_date' : 'multiple',
        };

        body_data = {

            // Adding method type
            method: 'POST',

            // Adding body or contents to send
            body: JSON.stringify(form_data),
            // body : form_data,

            // Adding headers to the request
            headers: {  

                "Content-type": "application/json; charset=UTF-8",
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }


        fetch(url,body_data)

        // Converting to JSON
        .then(response =>{
            // response.json()
            if (response.ok) {
                return response.json();
            }

            return Promise.reject(response);
        })


        // Displaying results to console
        .then(json =>{

        if (json.status == 'TRUE') {
            $('#add_daily_wages_employee').append(json.get_employees);
        } else {


        }

        }).catch(response => {

        });
    })

    let deletePayrollId = (delete_id) => {

        $("input[name='daily_wage_id']").val(delete_id)

    }

    $('#modalDisburseForm').submit(function(e){
        e.preventDefault();
        send_server_request( 'POST' ,'daily-wages-payroll/wages-disbursement' , this ,'modalDisburseFormButton', "Yes" , '{{Request::getRequestUri()}}' ,3000)
    })

    $('#modalDeleteForm').submit(function(e){
        e.preventDefault();
        send_server_request( 'POST' ,'daily-wages-payroll/delete-daily-wages' , this ,'modalDeleteFormButton', "Yes" , '{{Request::getRequestUri()}}' ,3000)
    })

    $('#modalDeleteDepartmentForm').submit(function(e){
        e.preventDefault();
        send_server_request( 'POST' ,'daily-wages-payroll/wages-delete-by-department' , this ,'modalDeleteDepartmentFormButton', "Yes" , '{{Request::getRequestUri()}}' ,3000)
    })

    $('#addMainForm').submit(function(e){
        e.preventDefault();
        $('#addMain').modal('hide');
        var payroll_type = $('input[name="payroll_type"]:checked').val();
        if(payroll_type == 'bulk'){
            $('#addPayroll').modal('show');
        }else{
            $('#addDailyWages').modal('show');
        }
    })

    $('.checked').on('click',function(){
        var checkbox = dataTable.$(".checked:checked", {"page": "all"});
       if(checkbox.length <= 0){
           $('.on-check').addClass('d-none');
       }else{
           $('.on-check').removeClass('d-none');
       }
       
   });

   $('#modalDownloadForm').submit(function(){
        $('#modalDownloadButton').prop('disabled', true);
        $('#modalDownloadButton').attr('data-kt-indicator', 'on');
        $('#modalDownloadButton').css('cursor', 'not-allowed');
        setTimeout(() => {
            $('#modalDownloadButton').prop('disabled', false);
            $('#modalDownloadButton').removeAttr('data-kt-indicator');
            $('#modalDownloadButton').css('cursor', 'pointer');
        }, 4000);
   })

</script>

@endpush