@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Employees Managers</h1>

            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if (!empty($employees))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Manager Report
                        </a>
                    </div>

                    <div class="m-0">
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#employees_modal">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Edit Bulk Employee(s) Managers
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
                        @php $filter = 'btn-color-primary'; @endphp

                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('employee_department')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">

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
                            <form action="{{ url('employees-managers') }}" method="GET">
                                <div class="px-7 py-5">

                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            <span>Select Department</span>
                                        </label>

                                        <div>
                                            <select class="form-select" data-control="select2" data-hide-search="false" name="employee_department" >
                                                <option value="">Select Department</option>
                                                @if(!empty($departments))
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department['department_id'] }}" @if (Request::get('employee_department') == $department['department_id']) selected @endif>{{ $department['department_name'] }}
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                    </div>

                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('employee_department')))

                                            <a href="{{ url('employees-managers') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">
                                                Reset Filter
                                            </a>
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
                        <div class="table-responsive popup-visible ">
                            <table class="table gs-0 gy-4 dataTable table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">

                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Employee</th>
                                        <th>Depart &amp; Desig</th>
                                        <th>Senior Manager</th>
                                        <th>Junior Manager</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if (!empty($employees))
                                        @foreach ($employees as $employee)
                                            <tr>
                                                <td class="ps-4">
                                                    @if (!empty($employee['employee_id']) && $employee['employee_id'] != '')
                                                        {{$employee['employee_code']}} - {{$employee['employee_name']}}
                                                    @else
                                                        Not Available
                                                    @endif
                                                </td>
                                                <td>

                                                    @if (!empty($employee['employee_department']) && $employee['employee_department'] != '')
                                                        <p class="text-dark fw-bold  d-block mb-1 fs-6"> {{ $employee['department_name'] }}</p>
                                                        <p class="text-muted fw-semibold text-muted d-block fs-7">{{$employee['designation_name'] }}</p>
                                                    @else
                                                        Not Available
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($employee['employee_ra_id']) && $employee['employee_ra_id'] != '')
                                                         {{$employee['employee_ra_code']}} - {{$employee['employee_ra_name']}}
                                                       
                                                    @else
                                                        <span class="badge badge-light-danger fs-7 fw-bold">Not Assigned Yet</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($employee['employee_ra2_id']) && $employee['employee_ra2_id'] != '')
                                                    {{$employee['employee_ra2_code']}} - {{$employee['employee_ra2_name']}}
                                                    @else
                                                        <span class=" badge badge-light-danger fs-7 fw-bold">Not Assigned Yet</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">

                                                    <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 manager_button" data-bs-toggle="modal" data-bs-target="#sm_modal" data-employee_id="{{ $employee['employee_id'] }}" data-employee_name_sm="{{ $employee['employee_name'] }}" data-employee_sm="{{ $employee['employee_ra_id'] }}" data-employee_name_jm="{{ $employee['employee_ra2_name'] }}" data-employee_jm="{{ $employee['employee_ra2_id'] }}">
                                                        <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Edit  Manager">
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


            {{-- Senior Manager Modal Starts Here --}}
                <form id="changemanager">
                    @csrf
                    <div class="modal fade" id="sm_modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable mw-650px">
                            <div class="modal-content rounded">
                                <div class="modal-header">
                                <h4 class="modal-title pl-4">Edit Manager(s)</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                        </svg>
                                    </span>
                                </button>
                                </div>

                                <div class="modal-body pb-15">

                                    <div class="col-md-12 mb-8 mt-3 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Employee Name</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="text" class="form-control " placeholder="Employee Name" id="employee_name_sm" readonly>
                                            <input type="hidden" class="form-control" name="employee_id"
                                            placeholder="Employee Name" id="employee_id_sm" />
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-8 mt-3 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Senior Manager</label>
                                        <select class="form-select  myselect4" data-control="select2" data-hide-search="false" name="senior_manager"
                                        id="senior_manager"
                                        required="required">
                                        {{-- <option value="">No Senior Manager</option> --}}
                                        @if (!empty($managers))
                                            @foreach ($managers as $manager)
                                                <option value="{{ $manager['employee_id'] }}"
                                                    @if ($manager['employee_code'] == '' || $manager['employee_code'] == 'N/A') disabled @endif>
                                                    {{ $manager['employee_code'] . ' - ' . $manager['employee_name'] }}
                                                </option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-8 mt-3 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">Junior Manager</label>
                                        <select class="form-select  myselect5" data-control="select2" data-hide-search="false" name="junior_manager"  id="junior_manager" >
                                        <option value="">No Junior Manager</option>
                                        @if (!empty($managers))
                                            @foreach ($managers as $manager)
                                                <option value="{{ $manager['employee_id'] }}"
                                                    @if ($manager['employee_code'] == '' || $manager['employee_code'] == 'N/A') disabled @endif>
                                                    {{ $manager['employee_code'] . ' - ' . $manager['employee_name'] }}
                                                </option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>

                                </div>
                                <div class="modal-footer justify-content-center">

                                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-btn2" class="btn btn-primary">
                                        <span class="indicator-label">Submit</span>
                                        <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            {{-- Senior Manager Modal Ends Here --}}

            <form id="employeesmanager">
                @csrf
                <div class="modal fade" id="employees_modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        <div class="modal-content rounded">
                            <div class="modal-header">
                            <h4 class="modal-title pl-4">Edit Bulk Employee(s) Manager</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                            </button>
                            </div>

                            <div class="modal-body pb-10">

                                <div class ="row">
                                    <div class="col-md-10 mb-8 mt-3 fv-row" id="selectedepartment">
                                        <label class="fs-6 fw-semibold mb-2 required">Department</label>
                                            <select class="form-select multi-depart" id="departments_all" data-close-on-select="false" data-control="select2" multiple="multiple" data-hide-search="true" data-placeholder="Select Department" name="department[]" required>

                                                @php $count = 0 @endphp
                                                @if(!empty($departments))
                                                    @foreach ($departments as $department)
                                                    @php $count ++; @endphp
                                                        <option value="{{$department['department_id']}}">
                                                            {{ $department['department_name']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                    </div>
                                    <div class="col-md-2 mb-8 mt-3 fv-row">

                                        <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                            <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                                <input type = "hidden" name="count" value="{{$count}}">
                                                <input class="form-check-input h-20px w-20px  multi-depart" type="checkbox" id="department_all"  value="Select All" />

                                            </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 mb-8 fv-row" id="selectedemployee">
                                        <label class="fs-6 fw-semibold mb-2 required">Employees</label>

                                        <select class="form-select" data-control="select2" multiple="multiple" data-hide-search="true" data-close-on-select="false" data-placeholder="Select Employee" name="employee_id[]" id="employees" required>
                                            @if(!empty($employees))
                                                @foreach($employees as $employee)
                                                 <option value="{{$employee['employee_id']}}">{{$employee['employee_code']}} - {{$employee['employee_name']}}</option>';
                                                @endforeach
                                            @endif
                                        </select>

                                    </div>
                                    <div class="col-md-2 mb-8 fv-row">

                                        <label class="fs-6 fw-semibold mb-2">All Employees</label>
                                            <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                                <input class="form-check-input h-20px w-20px" type="checkbox" id="employee_all" value="Select All" />

                                            </label>
                                    </div>
                                </div>

                                 <div class="col-md-12 mb-8 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Manager Type</span>
                                        </label>

                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select" data-control="select2" data-hide-search="true" data-placeholder="Select Manager Type" id="select_manager"  name="manager_type"
                                                required="required">
                                                <option value="">Select Manager Type</option>
                                                <option value="Senior">Senior Manager</option>
                                                <option value="Junior">Junior Manager</option>

                                            </select>
                                        </div>
                                        {{--end::Input--}}
                                    </div>

                                <div class="col-md-12 mb-8 fv-row d-none" id="employees_senior_manager">
                                    <label class="required fs-6 fw-semibold mb-2">Senior Manager</label>
                                    <select class="form-select bulk_employee_select" data-control="select2" data-placeholder="Select Senior Manager" id="selectseniormanager" data-hide-search="false" name="employee_senior_manager">
                                    <option value=""></option>
                                    @if (!empty($managers))
                                        @foreach ($managers as $manager)
                                            <option value="{{ $manager['employee_id'] }}"
                                                @if ($manager['employee_code'] == '' || $manager['employee_code'] == 'N/A') disabled @endif>
                                                {{ $manager['employee_code'] . ' - ' . $manager['employee_name'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                    </select>
                                </div>

                                <div class="col-md-12 mb-8 fv-row d-none" id="employees_junior_manager">
                                    <label class="fs-6 fw-semibold mb-2">Junior Manager</label>
                                    <select class="form-select bulk_employee_select" data-control="select2" id="selectjuniormanager" data-hide-search="false" name="employee_junior_manager">
                                    <option value="">No Junior Manager</option>
                                    @if (!empty($managers))
                                        @foreach ($managers as $manager)
                                            <option value="{{ $manager['employee_id'] }}"
                                                @if ($manager['employee_code'] == '' || $manager['employee_code'] == 'N/A') disabled @endif>
                                                {{ $manager['employee_code'] . ' - ' . $manager['employee_name'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer justify-content-center">

                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit-btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <form class="form" id="downloadbutton" action="{{ url('employees-managers/employees-managers-download') }}" method="POST">
            @csrf
            <div class="modal fade" id="download_modal_pdf"uploaddex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-650px">
                    <div class="modal-content rounded">

                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Download Employee Manager Report</h4>
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

                                <input name="employee_department" type="hidden" value="{{Request::get('employee_department')}}">

                                <div class="col-md-12 mt-3 fv-row">
                                    <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) manager report,
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

<script src="{{url('assets/js/employees/employee-managers.js')}}"></script>
<script>
    var count_val = {{ $count }};
</script>

@endpush
