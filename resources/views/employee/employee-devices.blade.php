@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Employees Devices
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                {{-- @if(!empty($all_devices)) --}}
                    <div class="m-0">
                        <a href="{{ url('employee-devices') }}?download=TRUE" class="btn btn-sm btn-success" method="GET">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Employee Device Details
                        </a>
                    </div>
                {{-- @endif --}}
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
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('department_id')) ? $filter : '' }} {{ !empty(Request::get('business_unit')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                       
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('employee-devices') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                    </div>

                                    <div class="separator border-gray-200"></div>
                                    <div class="px-7 py-5">

                                        <div class="mb-3">
                                    
                                            <label class="form-label fw-semibold">
                                                <span>Department</span>
                                            </label>
                                            <div>
                                                <select class="form-select" data-kt-select2="true" name="department_id" data-placeholder="Select Department">
                                                    <option value="">Select Department</option>
                                                    <option value="All" @if (Request::get('department_id') == 'All') selected @endif>All Departments</option>
                                                    @if(!empty($departments))
                                                        @foreach ($departments as $department)
                                                            <option value="{{ $department['department_id'] }}" @if (Request::get('department_id') == $department['department_id']) selected @endif>{{ $department['department_name'] }}</option>
                                                        @endforeach
                                                    @endif                                                             
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 row" >
                                            <div class="mb-3">
                                            
                                                <label class="form-label fw-semibold">Business Unit</label>
                                                
                                                <div>
                                                    <select class="form-select " data-kt-select2="true" data-placeholder="Select Business Unit" name="business_unit">
                                                    <option value="">Select Business Unit</option>
                                                    <option value="All" @if (Request::get('business_unit') == 'All') selected @endif>All Business Units</option>
                                                    @foreach (CustomHelper::business_units() as $business_unit)
                                                        <option value="{{ $business_unit }}" @if (Request::get('business_unit') == $business_unit) selected @endif>{{ $business_unit }}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                        
                                            <label class="form-label fw-semibold">Employee Group </label>
                                            
                                            <div>
                                                <select class="form-select " data-kt-select2="true" data-placeholder="Select Employee group" name="employee_group">
                                                    <option value="">Select Employee group</option>
                                                    <option value="All" @if (Request::get('employee_group') == 'All') selected @endif>All Employee Group</option>
                                                    @if(!empty($employee_groups))
                                                        @foreach ($employee_groups as $employee_group)
                                                            <option value="{{ $employee_group['employee_group_id'] }}"
                                                            @if(!empty(Request::get('employee_group')) && Request::get('employee_group') == $employee_group['employee_group_id']) selected @endif>
                                                                {{ $employee_group['group_name'] }}</option>
                                                        @endforeach 
                                                    @endif    
                                                </select>
                                            </div> 
                                        </div>
                                        
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('business_unit')) || !empty(Request::get('department_id')) || !empty(Request::get('employee_group')))
                                                <a href="{{ url('employee-devices') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">Reset Filter</a>
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

                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">
                            
                                <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" data-server-paginated="true" aria-describedby="tableEmployee_info">

                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-4">Employee Name</th>
                                            <th>Depart &amp; Desig</th>
                                            <th>Device Name</th>
                                            <th>Device Token</th>
                                            <th>Device Status</th>
                                            <th>Created at</th>
                                            <th>Updated at</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($all_devices))
                                            @foreach($all_devices as $all_device)
                                                <tr>
                                                    <td class="ps-4">{{$all_device['Employee Code']}} - {{$all_device['Employee Name']}}</td>
                                                    <td>
                                                        <p class="text-dark fw-bold mb-1 fs-6"> {{ $all_device['Department Name'] }}</p>
                                                        <p class="text-muted fw-semibold text-muted fs-7">{{$all_device['Designation Name'] }}</p>
                                                    </td>
                                                    <td> {{ $all_device['Device Name']}}</td>
                                                    <td> {{ $all_device['Device Token']}}</td>
                                                    <td>
                                                        @if( $all_device['Device Status'] == 1)
                                                            <span class="badge badge-light-success font-weight-bold">Active</span>
                                                        @else
                                                            <span class="badge badge-light-danger font-weight-bold">De-active</span>

                                                        @endif
                                                    </td>
                                                    <td> {{ $all_device['Device Created At']}}</td>
                                                    <td> {{ $all_device['Deviced Updated At'] ?? 'N/A'}}</td>

                                                    <td>

                                                        @if ($all_device['Device Status'] == 1 )
                                                            <i class="fa-regular fa-circle-check  text-success mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                        @else
                                                                <a href="#."  data-bs-toggle="modal" data-bs-target="#approverequest" onclick="deviceEditModel({{$all_device['Device ID']}})">  <i class="fa-regular fa-circle-check  text-info mx-2" style="font-size: 20px; cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="Approve device" ></i>
                                                            </a>
                                                        @endif

                                                        @if ($all_device['Device Status'] == 2)
                                                            <i class="fa-regular fa-circle-xmark  text-danger mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                        @else
                                                            <a href="#." data-bs-toggle="modal"  data-bs-target="#disapproverequest" onclick="deviceModel({{$all_device['Device ID']}})"><i class="fa-regular fa-circle-xmark   text-warning mx-2" style="font-size: 20px;cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="Disapprove device"></i></a>

                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>

                                </table>
                            @include('include.server-pagination', ['paginator' => $pagination ?? null])
                           
                                
                        </div>

                    </div>



                </div>
            </div>

        </div>

    </div>
</div>

      {{----Approve Request Modal--}}

    <div class="modal fade" id="approverequest" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
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

                <form id="Approve_device_request" class="form">
                 @csrf
                    <div class="modal-body pt-0">

                        <p class="pt-4 fs-6">Are you sure to active this device ?</p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="device_id">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <span class="indicator-label">Yes, Approve</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{----DisApprove Request Modal--}}

    <div class="modal fade" id="disapproverequest" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
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

                <form id="disApprove_device_request" class="form">
                 @csrf
                    <div class="modal-body pt-0">

                        <p class="pt-4 fs-6">Are you sure to De-activate this device ?</p>

                    </div>
                   <div class="modal-footer">
                        <input type="hidden" name="device_id">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update-btn" class="btn btn-primary">
                            <span class="indicator-label">Yes, Disapprove</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>


@endsection

@push('js-link')

<script src="{{url('assets/js/employees/employee-devices.js')}}"></script>


@endpush
