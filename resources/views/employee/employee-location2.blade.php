@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Employees Locations
                   
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">

                {{-- @if(!empty( $employee_location_detail)) --}}
                    <div class="m-0">
                        <a href="{{ url('employee-location') }}?download=TRUE" class="btn btn-sm btn-success" method="GET">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Employee Location Report
                        </a>
                    </div>
                {{-- @endif     --}}

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

                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('mob')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">

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

                            <div class="px-7 py-5">
                                <form action="{{ url('employee-location') }}" method="GET">
                                    
    
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
    
                                    {{-- <div class="mb-3">
                                        <label class="form-label fw-semibold">Employee Status</label>

                                        <div>
                                            <select class="form-select" data-kt-select2="true" data-placeholder="Select option"  name="emp_status" id="employee_data" required>
                                                <option value="1" @if (!empty(Request::get('emp_status')) && Request::get('emp_status') == '1') selected @endif>Active Employees</option>
                                                <option value="2" @if(!empty(Request::get('emp_status')) && Request::get('emp_status') == '2') selected @endif>De-active Employees</option>
                                            </select>

                                        </div>

                                    </div> --}}

    
                                    <div class="d-flex justify-content-end">
                                        @if(!empty(Request::get('emp_status')) || !empty(Request::get('employee_location')))
                                            <a href="{{ url('employee-location') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">Reset Filter</a>
                                        @endif
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                    </div>
    
                                   
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-body py-3">

                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">
                            @if (!empty(Request::get('employee_location'))) 
                                <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="location_datatable" aria-describedby="tableEmployee_info">
                                    
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-4">Employee Code & Name</th>
                                            <th>Depart & Desig</th>
                                            <th>Business Unit</th>
                                            <th>Location Title</th>
                                            <th>Latitude</th>
                                            <th>Longitude</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($employee_location_detail))
                                            @foreach($employee_location_detail as $employee_location)
                                                <tr>
                                                    <td class="ps-4">{{ $employee_location['Employee ID'] }} - {{ $employee_location['Employee Name'] }}</td>
                                                    <td>
                                                        <p class="text-dark fw-bold mb-1 fs-6"> {{ $employee_location['Department'] }}</p>
                                                        <p class="text-muted fw-semibold text-muted fs-7">{{$employee_location['Designation'] }}</p>
                                                    </td>
                                                    <td>{{ $employee_location['Business unit'] ?? '-' }}</td>
                                                    <td>{{ $employee_location['Location title'] }}</td>
                                                    <td>{{ $employee_location['Location latitude'] }}</td>
                                                    <td>{{ $employee_location['Location longitude'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                        
                                </table>
                            @else
                                <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="location_datatable_ajax" aria-describedby="tableEmployee_info">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-4">Employee Code & Name</th>
                                            <th>Depart & Desig</th>
                                            <th>Business Unit</th>
                                            <th>Location Title</th>
                                            <th>Latitude</th>
                                            <th>Longitude</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loc_datatable_ajax">

                                    </tbody>
                                </table>     
                            @endif   
                           
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

@endsection

@push('js-link')
<script>
    $(function () {
        dataTable = $('#location_datatable').DataTable({
            order: false,
            search: { regex: true },
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 100

        });
    });  

    var location_main_url =  "{{ url('/employee-location') }}";
   
    if (window.location.href == location_main_url) {

        $.ajax({
            url: "{{ url('/api/get-employee-location') }}",
            type: 'GET',
            success: function(response) {
                //  console.log(response);   
                if (response.status == true) {
                    var emp_record = '';
                    var employees_location = response.data; 

                    $.each(employees_location, function (key, employee_loc) {
                        emp_record +=  
                        
                            `<tr>
                                <td class="ps-4">${employee_loc.employee_code} - ${employee_loc.employee_name}</td>
                                <td>
                                    <p class="text-dark fw-bold mb-1 fs-6">${employee_loc.department_name}</p>
                                    <p class="text-muted fw-semibold text-muted fs-7">${employee_loc.designation_name}</p>
                                </td>                        
                                <td> ${employee_loc.business_unit ?? '-'}</td>
                                <td> ${employee_loc.location_title}</td>
                                <td> ${employee_loc.location_latitude}</td>
                                <td> ${employee_loc.location_longitude}</td>
                            </tr>`;
                    });
                    $('#loc_datatable_ajax').html(emp_record);

                    dataTable = $('#location_datatable_ajax').DataTable({
                        order: false,
                        search: { regex: true },
                        aLengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        iDisplayLength: 100
                    
                    });
                
                } else {
                    
                    console.error('Failed to retrieve employees location:', response.message);
                }
            },
            error: function(jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Employee location records failed please try again',
                        timer: 4000,
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 4000);
                } else {
                    if (errorStatus == 419) {
                        location.reload();
                    } else {
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
    
</script>
@endpush
