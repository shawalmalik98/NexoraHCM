@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Manage Employees
                    @if(Request::get('employee_isactive') == '2')
                        (Showing De-active 
                    @else
                        (Showing Active 
                    @endif
                    @if(!empty(Request::get('employee_status')))
                        and  
                        @if(!empty($employment_status))
                            @foreach ($employment_status as $employment_stats)
                                @if(!empty(Request::get('employee_status')) && Request::get('employee_status') == $employment_stats['employement_status_id']) {{$employment_stats['employement_status_name']}} @endif
                            @endforeach
                
                        @endif   
                    @endif
                    Employees)
                </h1>
              
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                <div class="m-0">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#employee_information_upload">
                         <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>										
                        Upload Employees Records
                    </a>
                </div>

                {{-- <div class="m-0">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#employee_information">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>										
                        Download Employees Records
                    </a>
                </div> --}}

                @if(!empty($no_of_employees && (!empty($employee_active))))
                    @if($no_of_employees[0]['no_of_employees'] == "unlimited" || $no_of_employees[0]['no_of_employees'] > $employee_active )
                        <div class="m-0">
                            <a href="{{url('employees/addemployee')}}" class="btn btn-sm btn-primary">											
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Add New Employee
                            </a>   
                        </div>
                    @endif
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
                        @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('employee_isactive')) ? $filter : '' }} {{ !empty(Request::get('employee_status')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1">
                            
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bold">Filter Options</div>
                            </div>

                            <div class="separator border-gray-200"></div>
                            <form action="{{ url('employees') }}" method="GET">
                                <div class="px-7 py-5">
                                    
                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Employee Status</label>
                                        
                                        <div>
                                            <select class="form-select " data-kt-select2="true" data-placeholder="Employee Activity Status" name="employee_isactive" required="">
                                                <option value="1" @if(!empty(Request::get('employee_isactive')) && Request::get('employee_isactive') == '1') selected @endif>Active Employees </option>
                                                <option value="2" @if(!empty(Request::get('employee_isactive')) && Request::get('employee_isactive') == '2') selected @endif>De-active Employees</option>
                                            </select>
                                        </div>
                                        
                                    </div>

                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Employment Status </label>
                                        
                                        <div>
                                            <select class="form-select " data-kt-select2="true" data-placeholder="Select Employment Status" name="employee_status">
                                                <option value="">Select Employment Status</option>
                                                @if(!empty($employment_status))
                                                    @foreach ($employment_status as $employment_stats)
                                                        <option value="{{$employment_stats['employement_status_id']}}" @if(!empty(Request::get('employee_status')) && Request::get('employee_status') == $employment_stats['employement_status_id']) selected @endif>{{$employment_stats['employement_status_name']}}</option>
                                                    @endforeach
                                            
                                                @endif  
                                            </select>
                                        </div> 
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        @if(!empty(Request::get('employee_isactive')))
                                            <a href="{{ url('employees') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">Reset Filter</a>
                                        @endif
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div> 
   
                <div class="card-body py-3">
                     
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                            
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        {{-- <th class="ps-4">ID</th> --}}
                                        <th class="ps-4">Code</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Depart &amp; Desig</th>
                                        <th>Business Unit</th>
                                        <th>Manager Rights</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($employees))
                                        @foreach ($employees as $employee)
                                            <tr >
                                                {{-- <td class="ps-4">{{ $employee['employee_id'] }}</td> --}}
                                                <td class="ps-4">{{ $employee['employee_code'] }}</td>
                                                <td>{{ $employee['employee_name'] }}</td>
                                            
                                                <td>{{ $employee['employee_personal_email'] }}</td>
                                                <td>
                                                    <p class="text-dark fw-bold mb-1 fs-6"> {{ $employee['department_name'] }}</p>
                                                    <p class="text-muted fw-semibold text-muted fs-7">{{$employee['designation_name'] }}</p>
                                                </td>
                                                <td>{{ $employee['business_unit'] }}</td>
                                                <td>
                                                    @if($employee['employee_is_manager'] != '' && $employee['employee_is_manager'] == 1)
                                                        <span class="badge badge-light-success font-weight-bold">Yes, Given</span>
                                                    @else
                                                        <span class="badge badge-light-danger font-weight-bold">Not, Given</span>
                                                    @endif
                                                </td>
                                                
                                                <td class="text-center">
                                                    <a href="{{ url('employees/editemployee') . '/' . $employee['employee_id'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 view_btn" data-bs-toggle="tooltip" title="Edit Employee Details">
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                    </a>

                                                    {{-- <a href="{{ url('view-employee-information-form') .'/'. $employee['employee_id'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title=" Employee Information Form" > 
                                                        <i class="fas fa-user" aria-hidden="true"></i>
                                                    </a> --}}
                                                    
                                                    {{-- <a  href="{{ url('employees/employee-documents') .'/'. $employee['employee_id'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title=" Employee Document"> 
                                                        <i class="fas fa-file" aria-hidden="true"></i>
                                                    </a> --}}

                                                    {{-- <a  href="{{ url('employees/employee-allotment') .'/'. $employee['employee_id'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Employee Allotments"> 
                                                        <i class="fas fa-car"></i>
                                                    </a> --}}
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

    <form class="form" id="downloadbutton" action="{{ url('employees/employee-information-download') }}" method="POST">
        @csrf
        <div class="modal fade" id="employee_information"uploaddex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                   
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Download Employees Records</h4>
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

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Employee Status</span>
                                </label>
                                <select class="form-select" data-control="select2" data-hide-search="true" name="emp_status" required>
                                    <option value="1">Active Employees</option>
                                    <option value="2">De-active Employees</option>
                                </select>
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Download Type</span>
                                </label>
                                <select class="form-select" data-control="select2" data-hide-search="true" id="download_information_type" name="download_type" required>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">MS Excel</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 fv-row">
                                <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) records,
                                 Please type <b>TODP-ID</b> (case sensitive) to confirm.
                                </p>
                                <input type="text" class="form-control " name="confirmation_code" placeholder="Enter Confirmation Code" required="required" autocomplete="off" onpaste="return false">
                            </div>

                            <div class="col-md-12 mt-3 fv-row text-center">
                                <span class="d-none text-danger" id="download_message">
                                    Please wait, PDF file generation is in process. It may take a while.
                                </span>
                                <span class="d-none text-danger" id="download_message_excel">
                                    Please wait, EXCEL file generation is in process. It may take a while.
                                </span>
                            </div>
            
                        </div>    
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit_btn" class="btn btn-primary">
                            <span class="indicator-label">Download</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form class="form" id="employee_record_update">
        @csrf	
        <div class="modal fade" id="employee_information_upload" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                   
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Upload Employees Records</h4>
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
                            <input type="hidden" name="Force_Approved">
                            <div class="col-md-12 fv-row">
                                <label class="fs-6 fw-semibold mb-2 required">File Attachment</label>
                                <input type="file" class="form-control" accept=".csv" name="file">
                                <p class="text-danger mt-1">Please <b>Download Format File</b> and add employee(s) record. All the fields exist in file are required.</p>
                                <p class="text-danger mt-0">Only CSV file is supported for employee(s) upload process.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update_btn" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <a href="/portal_assets/employee_excel_document/EmployeeRecord.zip" id="download_btn" class="btn btn-success">Download Format</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form class="form" id="employee_record_upload">
        @csrf	
        <div class="modal fade" id="employee_records_upload" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                   
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Confirmation Alert !</h4>
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
                            <input type="hidden" name="Force_Approved">
                            <div id="employee_record_msg">
                            </div>
            
                        </div>    
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button onclick="employeeuploadclose()" type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button onclick="employeeupload()" type="button"  class="btn btn-primary force_upload">
                            <span class="indicator-label">Force Upload</span>
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

<script src="{{url('assets/js/employees/manage-employee.js')}}"></script>
    
@endpush


