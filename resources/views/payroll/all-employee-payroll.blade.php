@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Manage Payroll ({{date("F", mktime(0, 0, 0, $month, 10))}}-{{$year}}) </h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">


                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#calculatePayrollAmount">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M3 3V17H7V21H15V9H20V3H3Z" fill="currentColor"/>
                                <path d="M20 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H20C20.6 2 21 2.4 21 3V21C21 21.6 20.6 22 20 22ZM19 4H4V8H19V4ZM6 18H4V20H6V18ZM6 14H4V16H6V14ZM6 10H4V12H6V10ZM10 18H8V20H10V18ZM10 14H8V16H10V14ZM10 10H8V12H10V10ZM14 18H12V20H14V18ZM14 14H12V16H14V14ZM14 10H12V12H14V10ZM19 14H17V20H19V14ZM19 10H17V12H19V10Z" fill="currentColor"/>
                            </svg>
                        </span>
                        Calculate Payroll Amount</a>
                </div>
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPayrollMain">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Generate Payroll</a>
                </div>
                @if(!empty($fetch_employee_disburse))
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
                        Delete Payroll</a>
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
                            <form action="{{ url('payroll') }}" method="GET">

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
                                            <option value="01" @if ((int) $month == 1) selected @endif>January
                                            </option>
                                            <option value="02" @if ((int) $month == 2) selected @endif>February
                                            </option>
                                            <option value="03" @if ((int) $month == 3) selected @endif>March
                                            </option>
                                            <option value="04" @if ((int) $month == 4) selected @endif>April
                                            </option>
                                            <option value="05" @if ((int) $month == 5) selected @endif>May
                                            </option>
                                            <option value="06" @if ((int) $month == 6) selected @endif>June
                                            </option>
                                            <option value="07" @if ((int) $month == 7) selected @endif>July
                                            </option>
                                            <option value="08" @if ((int) $month == 8) selected @endif>August
                                            </option>
                                            <option value="09" @if ((int) $month == 9) selected @endif>September
                                            </option>
                                            <option value="10" @if ((int) $month == 10) selected @endif>October
                                            </option>
                                            <option value="11" @if ((int) $month == 11) selected @endif>November
                                            </option>
                                            <option value="12" @if ((int) $month == 12) selected @endif>December
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-10">
                                        
                                        <label class="form-label fw-semibold">Select Year</label>
                                        
                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Year" name="y" required="required">
                                            <option value="">Select Year</option>
                                            @if(!empty($dynamic_years))
                                                @foreach ($dynamic_years as $year_list)
                                                    <option value="{{$year_list}}" @if ((int) $year == (int) $year_list) selected @endif>{{$year_list}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                       
                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('m')) && !empty(Request::get('y')))
                                        
                                        <a href="{{ url('payroll') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
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
                            
                            @if(!empty($fetch_employee_payroll))
                            <a href="{{url('payroll/download-payroll-pdf?month='.$month.'&year='.$year )}}" class="btn btn-secondary buttons-excel buttons-html5 mb-5 btn-sm btn-primary" tabindex="0" target="_blank" ><span>View Payroll Record</span></a>
                            <a href="javascript:;" class="btn btn-secondary buttons-excel buttons-html5 mb-5 btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDownload" ><span>Download Payroll Record</span></a>
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
                                    @if(!empty($fetch_employee_payroll))
                                        @foreach ($fetch_employee_payroll as $employee_payroll)
                                            <tr>    
                                                <td>
                                                    <div class="form-check form-check-sm form-check-custom form-check-solid" style="{{($employee_payroll['disburse'] != 2 ) ? '' : 'cursor: not-allowed;'}}" >
                                                        <input class="form-check-input checked"  type="checkbox" data-id="{{$employee_payroll['pc_id']}}" value="{{$employee_payroll['pc_id']}}" {{($employee_payroll['disburse'] != 2 ) ? '' : 'disabled' }}    />
                                                    </div>
                                                </td>

                                                <td class="ps-8">{{ $employee_payroll['pc_id'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($employee_payroll['pc_employee_id']) !!}</td>
                                                <td>{{ date('M-Y',strtotime($employee_payroll['pc_year'].'-'.$employee_payroll['pc_month'].'-'.'01')) }} </td>
                                                <td>{{ number_format($employee_payroll['pc_employee_gross_salary'],2) }}</td>
                                                <td>{{ number_format($employee_payroll['pc_employee_extra_salary'],2) }}
                                                    @if(!empty($earning_breakdown[$employee_payroll['pc_id']]))
                                                        <div class="text-muted fs-8 lh-sm mt-1">@foreach($earning_breakdown[$employee_payroll['pc_id']] as $part_label => $part_amount)<div>{{ $part_label }}: {{ number_format($part_amount, 2) }}</div>@endforeach</div>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($employee_payroll['pc_employee_deduction'],2) }}
                                                    @if(!empty($deduction_breakdown[$employee_payroll['pc_id']]))
                                                        <div class="text-muted fs-8 lh-sm mt-1">@foreach($deduction_breakdown[$employee_payroll['pc_id']] as $part_label => $part_amount)<div>{{ $part_label }}: {{ number_format($part_amount, 2) }}</div>@endforeach</div>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($employee_payroll['pc_employee_net_salary'],2) }}</td>
                                                <td>{{ date('d-M-Y',strtotime($employee_payroll['pc_created_at'])) }}</td>
                                                <td>{{ !empty($employee_payroll['pc_updated_at']) ? date('d-M-Y',strtotime($employee_payroll['pc_updated_at'])) : ''}}</td>
                                                
                                                <td>
                                                    @if( $employee_payroll['disburse'] == 2  )
                                                    <span class="badge badge-light-success font-weight-bold">Disbursed</span>
                                                    @else
                                                    <span class="badge badge-light-danger font-weight-bold">Not, Disburse</span>
                                                    @endif
                                                </td>
                                                        
                                                <td>
                                                    <a href="{{($employee_payroll['disburse'] != 2 ) ? url('payroll/edit-payroll/'.$employee_payroll['pc_id']) : 'javascript:;' }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Edit Payslip" style="{{($employee_payroll['disburse'] != 2 ) ? '' : 'cursor: not-allowed;'}}"  > 
                                                        <span class="svg-icon svg-icon-3"  >
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                    </a>

                                                    <a  href="{{ url('individual-payroll-pdf/'.$employee_payroll['pc_id'])}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Payslip" target="_blank" > 
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                        
                                                    </a>

                                                    <a  href="{{ url('individual-payroll-pdf/'.$employee_payroll['pc_id'])}}?download=TRUE" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Download Payslip" target="_blank" > 
                                                        <i class="fas fa-download" aria-hidden="true"></i>
                                                        
                                                    </a>
                                                    <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" {{($employee_payroll['disburse'] != 2 ) ? 'data-bs-toggle=modal data-bs-target=#modalDelete onclick=deletePayrollId('.$employee_payroll['pc_id'].') ' : ''}} style="{{($employee_payroll['disburse'] != 2 ) ? '' : 'cursor: not-allowed;'}}" >
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



<form id="addPayrollForm" class="form">
    @csrf
    <div class="modal fade" id="addPayroll" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-650px">
            <div class="modal-content rounded">
                <div class="modal-header">
                <h4 class="modal-title pl-4">Generate Bulk Payroll</h4>
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </button>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15" >
                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row">
                            <label class="fs-6 fw-semibold mb-2 required">Payroll Month </label>
                            <input type="month" class="form-control " name="payroll_month"
                                placeholder="" required="" max="{{date('Y-m')}}" >
                        </div>
                    </div>
                    <div class="row mt-5" >
                        <div class="col-md-9 fv-row" id="selectDepartment" >
                            <label class="fs-6 fw-semibold mb-2 required">Department</label>
                            <select class="form-select" id="departments_all" multiple="multiple" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Department" name="department[]" data-close-on-select="false" required >
                                @if(!empty($all_department))
                                    @foreach ($all_department as $department)
                                        <option value="{{ $department['department_id'] }}" @if (!empty(Request::get('dept')) && Request::get('dept') == $department['department_id']) selected @endif>{{ $department['department_name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mt-3 fv-row">
                            <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" id="department_all" name="select_all" value="Select All" />
                                </label>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-12 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Hold payroll of</label>
                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="These employees are left out of this run; generate their payroll later from Individual Payroll."></i>
                            <select class="form-select" multiple="multiple" name="hold_employee_id[]" data-close-on-select="false" data-placeholder="Nobody (pay everyone in the departments)" data-employee-select data-scope="active" data-departments-from="#departments_all">
                            </select>
                        </div>
                    </div>
                    @include('payroll.partials.sandwich-toggle', ['toggle_id' => 'sandwich_rule_bulk'])
                    @if(!empty($tax_system_setting) && $tax_system_setting[0]['tax_deduction_apply'] == 1 )
                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row">

                            <label class="fs-6 fw-semibold mb-2 required">Tax Deduction</label>
                            <select class="form-select" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Tax Deduction" name="tax_deduction" data-close-on-select="true" id="tax_deduction_bulk" required >
                                <option value="" >Select</option>
                                <option value="yes" >Yes</option>
                                <option value="no" >No</option>
                            </select>

                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit_btn">
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


<form id="addPayrollMainForm" class="form">
    @csrf
    <div class="modal fade" id="addPayrollMain" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-650px">
            <div class="modal-content rounded">
                <div class="modal-header">
                <h4 class="modal-title pl-4">Generate Payroll</h4>
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </button>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15 pt-15" >
                    <div class="row mt-5" >
                        {{--begin::Radio group--}}
                        <div data-kt-buttons="true">
                            {{--begin::Radio button--}}
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5">
                                {{--end::Description--}}
                                <div class="d-flex align-items-center me-2">
                                    {{--begin::Radio--}}
                                    <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                        <input class="form-check-input" type="radio" name="payroll_type" value="bulk" required />
                                    </div>
                                    {{--end::Radio--}}

                                    {{--begin::Info--}}
                                    <div class="flex-grow-1">
                                        <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                            Generate Bulk Payroll
                                        </h2>
                                        <div class="fw-semibold opacity-50">
                                            Create department wise payroll or all departments
                                        </div>
                                    </div>
                                    {{--end::Info--}}
                                </div>
                                {{--end::Description--}}
                            </label>
                            {{--end::Radio button--}}

                            {{--begin::Radio button--}}
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5">
                                {{--end::Description--}}
                                <div class="d-flex align-items-center me-2">
                                    {{--begin::Radio--}}
                                    <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                        <input class="form-check-input" type="radio" name="payroll_type" value="individual" required />
                                    </div>
                                    {{--end::Radio--}}

                                    {{--begin::Info--}}
                                    <div class="flex-grow-1">
                                        <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                            Generate Individual Employee Payroll
                                        </h2>
                                        <div class="fw-semibold opacity-50">
                                            Create specific employee payroll
                                        </div>
                                    </div>
                                    {{--end::Info--}}
                                </div>
                                {{--end::Description--}}
                            </label>
                            {{--end::Radio button--}}
                        </div>
                        {{--end::Radio group--}}
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit_btn">
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

<form action="{{url('payroll/download-payroll-pdf?month='.$month.'&year='.$year )}}&download=TRUE" class="form" method="GET" id="modalDownloadForm" >
    <div class="modal fade" id="modalDownload" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-650px">
            <div class="modal-content rounded">
                <div class="modal-header">
                <h4 class="modal-title pl-4">Download Payroll ({{date("F", mktime(0, 0, 0, $month, 10))}}-{{$year}})</h4>
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </button>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15 pt-15" >
                    <div class="row mt-5" >
                        {{--begin::Radio group--}}
                        <div data-kt-buttons="true">
                            {{--begin::Radio button--}}
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5">
                                {{--end::Description--}}
                                <div class="d-flex align-items-center me-2">
                                    {{--begin::Radio--}}
                                    <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                        <input class="form-check-input" type="radio" name="download_file" value="pdf" required />
                                    </div>
                                    {{--end::Radio--}}

                                    {{--begin::Info--}}
                                    <div class="flex-grow-1">
                                        <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                            PDF
                                        </h2>
                                        <div class="fw-semibold opacity-50">
                                            Portable Document Format
                                        </div>
                                    </div>
                                    {{--end::Info--}}
                                </div>
                                {{--end::Description--}}
                            </label>
                            {{--end::Radio button--}}

                            {{--begin::Radio button--}}
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5">
                                {{--end::Description--}}
                                <div class="d-flex align-items-center me-2">
                                    {{--begin::Radio--}}
                                    <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                        <input class="form-check-input" type="radio" name="download_file" value="excel" required />
                                    </div>
                                    {{--end::Radio--}}

                                    {{--begin::Info--}}
                                    <div class="flex-grow-1">
                                        <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                            Excel
                                        </h2>
                                        <div class="fw-semibold opacity-50">
                                            Microsoft Excel File
                                        </div>
                                    </div>
                                    {{--end::Info--}}
                                </div>
                                {{--end::Description--}}
                            </label>
                            {{--end::Radio button--}}
                        </div>
                        {{--end::Radio group--}}
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <input type="hidden" name="month" value="{{$month}}" />
                    <input type="hidden" name="year" value="{{$year}}" />
                    <input type="hidden" name="download" value="TRUE" />
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="modalDownloadButton" >
                        <span class="indicator-label">Download</span>
                        <span class="indicator-progress">Please wait
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
        {{--end::Modal - New Target--}}
</form>

<form id="calculatePayrollAmountForm" class="form">
    @csrf
    <div class="modal fade" id="calculatePayrollAmount" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-650px">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Calculate Payroll Amount</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15" >
                    <div class="row mt-5" >
                        <input type="hidden" name="calculatePayrollAmountForm" value="true" > 
                        <div class="col-md-12 fv-row">
                            <label class="fs-6 fw-semibold mb-2 required">Payroll Month </label>
                            <input type="month" class="form-control " name="payroll_month"
                                placeholder="" required="" max="{{date('Y-m')}}" min="{{date('Y-m',strtotime('-1 month'))}}">
                        </div>
                    </div>
                    <div class="row mt-5" >
                        <div class="col-md-9 fv-row" id="selectDepartmentCalculate" >
                            <label class="fs-6 fw-semibold mb-2 required">Department</label>
                            <select class="form-select" id="departments_all_calculate" multiple="multiple" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Department" name="department[]" data-close-on-select="false" required >
                                @if(!empty($all_department))
                                    @foreach ($all_department as $department)
                                        <option value="{{ $department['department_id'] }}" @if (!empty(Request::get('dept')) && Request::get('dept') == $department['department_id']) selected @endif>{{ $department['department_name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-8 mt-3 fv-row">
                            
                            <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                {{-- <input type = "hidden" name="count" value=""> --}}
                                <input class="form-check-input h-20px w-20px" type="checkbox" id="department_all_calculate" name="select_all" value="Select All" />

                            </label>
                        </div>
                    </div>
                    @include('payroll.partials.sandwich-toggle', ['toggle_id' => 'sandwich_rule_calculate'])
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit_btn">
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


<form id="addPayrollIndividualForm" class="form">
     @csrf
    <div class="modal fade" id="addPayrollIndividual" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-650px">
            <div class="modal-content rounded">
                <div class="modal-header">
                <h4 class="modal-title pl-4">Generate Individual Payroll</h4>
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                    
                </button>
                </div>
                <div class="m-0">
                </div>
                <div class="modal-body scroll-y pt-0 pb-15" >
                    <div class="row mt-5" >
                        <input type="hidden" name="addPayrollIndividualForm" value="true" > 
                        <div class="col-md-12 fv-row">
                            <label class="fs-6 fw-semibold mb-2 required">Payroll Month </label>
                            <input type="month" class="form-control " name="payroll_month"
                                placeholder="" required="" max="{{date('Y-m')}}" id="payroll_month" >
                        </div>
                    </div>
                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row" id="selectDepartmentCalculate" >
                            <label class="fs-6 fw-semibold mb-2 required">Employee</label>
                            <select class="form-select" id="company_emp_name"  data-kt-select2="true" data-placeholder="Select Employee" name="employee_id" data-close-on-select="true" required >
                            </select>
                        </div>
                    </div>
                    @include('payroll.partials.sandwich-toggle', ['toggle_id' => 'sandwich_rule_individual'])
                    @if(!empty($tax_system_setting) && $tax_system_setting[0]['tax_deduction_apply'] == 1 )
                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row">

                            <label class="fs-6 fw-semibold mb-2 required">Tax Deduction</label>
                            <select class="form-select" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Tax Deduction" name="tax_deduction" data-close-on-select="true" id="tax_deduction_individual"  required >
                                <option value="" >Select</option>
                                <option value="yes" >Yes</option>
                                <option value="no" >No</option>
                            </select>

                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit_btn">
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

<div class="modal fade" id="modalDisburse" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-650px">
        <form id="modalDisburseForm">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Disbursement of ({{date("F", mktime(0, 0, 0, $month, 10))}}-{{$year}})</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="modal-body pt-0">
                     
                    <div class="row mt-5" >
                        <div class="col-md-9 fv-row" id="selectEmployee" >
                            <label class="fs-6 fw-semibold mb-2 required">Employee</label>
                            <select class="form-select" multiple="multiple" id="select_employee"  data-kt-select2="true" data-placeholder="Select Employee" name="employee_ids[]" data-close-on-select="true" required >
                                @if(!empty($fetch_employee_disburse))
                                    @foreach ($fetch_employee_disburse as $employee_disburse)
                                        <option value="{{ $employee_disburse['pc_id'] }}" >{{ $employee_disburse['employee_name'] }} - {{ $employee_disburse['employee_code'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-8 mt-3 fv-row">
                            
                            <label class="fs-6 fw-semibold mb-2">All Employee</label>
                                <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2">
                                <input class="form-check-input h-20px w-20px" type="checkbox" id="select_employee_check" name="select_all" value="Select All" /> 
                        
                            </label>
                        </div>
                    </div>

                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="included" id="included"  />
                                <label class="form-check-label" for="included">
                                    Disburse payroll exclduing these selected employees 
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5" >

                        <div class="col-md-12 fv-row">

                            <p onselectstart="javascript:return false;" >This action can't be undone. If you want to disburse the payroll,
                                Please type <b>TODP-ID</b> (case sensitive) to confirm.</p>
                            <input type="text" class="form-control " name="confirmation_code" placeholder="Enter Confirmation Code" required="" autocomplete="off" onpaste="return false">

                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="modalDisburseFormButton" class="btn btn-primary">
                        <span class="indicator-label">Yes, Disburse</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>




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
                    <input type="hidden" name="payroll_id" value="" >
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


<div class="modal fade" id="modalDeleteDepartment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-650px">
        <form id="modalDeleteDepartmentForm">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Delete Payroll of ({{date("F", mktime(0, 0, 0, $month, 10))}}-{{$year}})</h4>
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
                    <div class="row mt-5" >
                        <div class="col-md-9 fv-row" id="selectDeleteDepartment" >
                            <label class="fs-6 fw-semibold mb-2 required">Department</label>
                            <select class="form-select" id="departments_all_delete" multiple="multiple" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Department" name="department[]" data-close-on-select="false" required >
                                @if(!empty($all_department))
                                    @foreach ($all_department as $department)
                                        <option value="{{ $department['department_id'] }}" @if (!empty(Request::get('dept')) && Request::get('dept') == $department['department_id']) selected @endif>{{ $department['department_name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-8 mt-3 fv-row">
                            
                            <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2">
                                <input class="form-check-input h-20px w-20px" type="checkbox" id="department_all_delete" name="select_all" value="Select All" /> 
                        
                            </label>
                        </div> 
                    </div>
                </div>

                <div class="modal-footer">
                    <input type = "hidden" name="month" value="{{$month}}">
                    <input type = "hidden" name="year" value="{{$year}}">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="modalDeleteDepartmentFormButton" class="btn btn-primary">
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
        $('#payrollMonth').select2({
            dropdownParent: $('#addPayroll')
        })
        $('#departments_all').select2({
            dropdownParent: $('#addPayroll')
        })
        $('#tax_deduction_bulk').select2({
            dropdownParent: $('#addPayroll')
        })
        $('#company_emp_name').select2({
            dropdownParent: $('#addPayrollIndividual')
        })
        $('#tax_deduction_individual').select2({
            dropdownParent: $('#addPayrollIndividual')
        })
        
    });

    $(function(){
    $('#payrollYear').select2({
        dropdownParent: $('#addPayroll')
        })
    });

    $('#addPayrollForm,#calculatePayrollAmountForm,#addPayrollIndividualForm').submit(function(e) {

        e.preventDefault();

        var url = "{{url('payroll/generate-payroll')}}"


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

    var url = "{{url('payroll/payroll-delete')}}"


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

    $('#payroll_month').change(function() {

    // alert($('#payroll_month').val());
   $('#company_emp_name').text('');

    var url = "{{url('payroll/get-employee-payroll-month')}}"

    let form_data = {
        'payroll_month' : $('#payroll_month').val(),
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

        $('#company_emp_name').append(json.get_employees);
    } else {


    }

    }).catch(response => {


    });


    })

    let deletePayrollId = (delete_id) => {

        $("input[name='payroll_id']").val(delete_id)

    }

    $('#modalDisburseForm').submit(function(e){
        e.preventDefault();
        send_server_request( 'POST' ,'payroll/payroll-disbursement' , this ,'modalDisburseFormButton', "Yes" , '{{Request::getRequestUri()}}' ,3000)
    })

    $('#modalDeleteForm').submit(function(e){
        e.preventDefault();
        send_server_request( 'POST' ,'payroll/payroll-delete' , this ,'modalDeleteFormButton', "Yes" , '{{Request::getRequestUri()}}' ,3000)
    })

    $('#modalDeleteDepartmentForm').submit(function(e){
        e.preventDefault();
        send_server_request( 'POST' ,'payroll/payroll-delete-by-department' , this ,'modalDeleteDepartmentFormButton', "Yes" , '{{Request::getRequestUri()}}' ,3000)
    })

    $('#addPayrollMainForm').submit(function(e){
        e.preventDefault();
        $('#addPayrollMain').modal('hide');
        var payroll_type = $('input[name="payroll_type"]:checked').val();
        if(payroll_type == 'bulk'){
            $('#addPayroll').modal('show');
        }else{
            $('#addPayrollIndividual').modal('show');
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