@extends('layout.main')



@section('main-section')

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

            <div class="app-container col-12 d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        @if (empty(Request::get('m')))
                            Manage Holidays ({{ date('Y') }})
                        @else
                            Manage Holidays ({{ date('F', mktime(0, 0, 0, $month, 10)) . '-' . $year }})
                        @endif
                    </h1>
                </div>

                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @if (!empty($get_all_holidays))
                        <div class="m-0">
                            <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#download_modal_pdf">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Download Gazzetted Holiday
                            </a>
                        </div>
                    @endif
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addHoliday">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                        rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                        fill="currentColor"></rect>
                                </svg>
                            </span>
                            Add New Holiday</a>
                    </div>

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
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                            rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor">
                                        </rect>
                                        <path
                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </span>
                                <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..."
                                    id="searchFilter">
                            </div>
                        </div>

                        <div class="card-toolbar">
                            @php $filter = 'btn-color-primary'; @endphp

                            <a href="#"
                                class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('m')) ? $filter : '' }} {{ !empty(Request::get('y')) ? $filter : '' }} "
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">

                                <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </span>
                                Filter
                            </a>

                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true"
                                id="kt_menu_63347db386dd1">
                                <form action="{{ url('holidays') }}" method="GET">

                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                    </div>

                                    <div class="separator border-gray-200"></div>
                                    <div class="px-7 py-5">

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">Select Month</label>

                                            <select class="form-select " data-kt-select2="true" name="m">
                                                <option value="">Select Month</option>
                                                <option value="01" @if (!empty(Request::get('m')) && Request::get('m') == '01') selected @endif>
                                                    January
                                                </option>
                                                <option value="02" @if (!empty(Request::get('m')) && Request::get('m') == '02') selected @endif>
                                                    February
                                                </option>
                                                <option value="03" @if (!empty(Request::get('m')) && Request::get('m') == '03') selected @endif>
                                                    March
                                                </option>
                                                <option value="04" @if (!empty(Request::get('m')) && Request::get('m') == '04') selected @endif>
                                                    April
                                                </option>
                                                <option value="05" @if (!empty(Request::get('m')) && Request::get('m') == '05') selected @endif>
                                                    May
                                                </option>
                                                <option value="06" @if (!empty(Request::get('m')) && Request::get('m') == '06') selected @endif>
                                                    June
                                                </option>
                                                <option value="07" @if (!empty(Request::get('m')) && Request::get('m') == '07') selected @endif>
                                                    July
                                                </option>
                                                <option value="08" @if (!empty(Request::get('m')) && Request::get('m') == '08') selected @endif>
                                                    August
                                                </option>
                                                <option value="09" @if (!empty(Request::get('m')) && Request::get('m') == '09') selected @endif>
                                                    September
                                                </option>
                                                <option value="10" @if (!empty(Request::get('m')) && Request::get('m') == '10') selected @endif>
                                                    October
                                                </option>
                                                <option value="11" @if (!empty(Request::get('m')) && Request::get('m') == '11') selected @endif>
                                                    November
                                                </option>
                                                <option value="12" @if (!empty(Request::get('m')) && Request::get('m') == '12') selected @endif>
                                                    December
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-10">

                                            <label class="form-label fw-semibold">Select Year</label>

                                            <select class="form-select " data-kt-select2="true" name="y">
                                                <option value="">Select Year</option>
                                                @if (!empty($dynamic_years))
                                                    @foreach ($dynamic_years as $year_list)
                                                        <option value="{{ $year_list }}"
                                                            @if (!empty($year) && $year == $year_list) selected @elseif(empty($year) && date('Y') == $year_list) selected @endif>
                                                            {{ $year_list }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('m')) && !empty(Request::get('y')))
                                                <a href="{{ url('holidays') }}"
                                                    class="btn btn-sm btn-danger btn-active-light-danger me-2 ">Reset
                                                    Filter</a>
                                            @endif
                                            <button type="reset"
                                                class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                data-kt-menu-dismiss="true">Close</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="card-body py-3">

                        <div class="table-responsive popup-visible ">

                            <div id="tableEmployee_wrapper"
                                class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered">
                                <div class="table-responsive popup-visible ">
                                    <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="system_datatable"
                                        aria-describedby="tableEmployee_info">

                                        <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th class="ps-4 min-w-100px sorting">Ref ID
                                                </th>
                                                <th class="min-w-100px sorting">Holiday Date
                                                </th>
                                                <th class="min-w-100px sorting">Holiday Name
                                                </th>
                                                <th class="min-w-100px sorting">Department(s) Name
                                                </th>
                                                <th class="min-w-100px sorting">Created By
                                                </th>
                                                <th class="min-w-100px sorting">Holiday Create Date
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($get_all_holidays))
                                                @foreach ($get_all_holidays as $holiday)
                                                    <tr>
                                                        <td class="ps-8">{{ $holiday['holiday_id'] }}</td>
                                                        <td>{{ $holiday['holiday_date'] }}</td>
                                                        <td>{{ $holiday['holiday_name'] }}</td>

                                                        <td>
                                                            @php
                                                                $department_name = explode(',', $holiday['department_name']);
                                                                $count = 0;
                                                            @endphp
                                                            @foreach ($department_name as $department)
                                                                @php $count ++ ; @endphp
                                                                {!! CustomHelper::get_department_name($department) !!}
                                                                @if ($count != count($department_name))
                                                                    ||
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($holiday['holiday_created_by']) !!}</td>
                                                        <td>{{ $holiday['holiday_created_at'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif

                                        </tbody>

                                    </table>
                                </div>

                            </div>

                        </div>

                    </div>


                    <form id="addHolidayForm" class="form">
                        @csrf
                        <div class="modal fade" id="addHoliday" tabindex="-1" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-scrollable mw-650px">

                                <div class="modal-content rounded">

                                    <div class="modal-header">
                                        <h4 class="modal-title pl-4">Add Holiday</h4>
                                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                                            data-bs-dismiss="modal" aria-label="Close">
                                            <span class="svg-icon svg-icon-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                        height="2" rx="1" transform="rotate(-45 6 17.3137)"
                                                        fill="currentColor" />
                                                    <rect x="7.41422" y="6" width="16" height="2"
                                                        rx="1" transform="rotate(45 7.41422 6)"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                        </button>
                                    </div>


                                    <div class="modal-body scroll-y pt-0 pb-15">

                                        <div class="row">
                                            <div class="col-md-10 mb-8 mt-3 fv-row" id="selectedepartment">
                                                <label class="fs-6 fw-semibold mb-2 required">Department</label>

                                                <select class="form-select" id="departments_all" data-control="select2"
                                                    multiple="multiple" data-hide-search="true"
                                                    data-placeholder="Select Department" name="department_name[]"
                                                    data-close-on-select="false" required>

                                                    @php $count = 0 @endphp
                                                    @if (!empty($departments))
                                                        @foreach ($departments as $department)
                                                            @php $count ++; @endphp
                                                            <option value="{{ $department['department_id'] }}">
                                                                {{ $department['department_name'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-8 mt-3 fv-row">

                                                <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                                <label
                                                    class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                                    <input type="hidden" name="count" value="{{ $count }}">
                                                    <input class="form-check-input h-20px w-20px" type="checkbox"
                                                        id="department_all" name="select_all" value="Select All" />

                                                </label>
                                            </div>

                                        </div>

                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="required fs-6 fw-semibold mb-2">Holiday(s)</label>
                                            <select class="form-select" data-control="select2" data-hide-search="true"
                                                data-placeholder="Select holiday(s)" name="days_assign" id="days_assign"
                                                required>
                                                <option value="">Select holiday Day(s)</option>
                                                <option value="single">Single Day</option>
                                                <option value="multiple">Multiple Day(s)</option>
                                            </select>
                                        </div>

                                        <div class="from_to_date_input row g-9 mb-8 d-none">
                                            <div class="col-md-4 fv-row">
                                                <label class="required fs-6 fw-semibold mb-2">From Date</label>
                                                <div class="position-relative d-flex align-items-center">
                                                    <input type="date" class="form-control  date-control"
                                                        placeholder="Select a date" name="from_date" id="from_date" />
                                                </div>
                                            </div>
                                            <div class="col-md-4 fv-row">
                                                <label class="required fs-6 fw-semibold mb-2">To Date</label>
                                                <div class="position-relative d-flex align-items-center">
                                                    <input type="date" class="form-control  date-control"
                                                        placeholder="Select a date" name="to_date" id="to_date"
                                                        disabled />
                                                </div>
                                            </div>

                                            <div class="col-md-4 fv-row">
                                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                    <span>Total Days</span>
                                                </label>


                                                <input type="text" class="form-control" placeholder="Total Days"
                                                    id="days" readonly />
                                            </div>

                                        </div>

                                        <div class="only_date_input d-flex flex-column mb-8 d-none">
                                            <label class="required fs-6 fw-semibold mb-2">Holiday Date</label>
                                            {{-- begin::Input --}}
                                            <div class="position-relative d-flex align-items-center">
                                                {{-- <input class="form-control form-datetime" autocomplete="off" placeholder="Select a date"  id="kt_daterangepicker_4" name="holiday_date" required=""> --}}
                                                <input type="date" class="form-control date-control"
                                                    placeholder="Select a date" name="holiday_date" id="holiday_date"
                                                    name="holiday_date" required="" />
                                                {{-- end::Datepicker --}}
                                            </div>
                                            {{-- end::Input --}}
                                        </div>

                                        {{-- end::Input group --}}

                                        <div class="d-flex flex-column mb-8">
                                            <label class="fs-6 fw-semibold mb-2 required">Holiday Title</label>
                                            <input type="text" class="form-control" maxlength="30"
                                                name="holiday_reason" placeholder="Enter Holiday Title" required="">
                                        </div>
                                        {{-- end::Input group --}}

                                        <div class="row mt-5">

                                            <div class="col-md-6 fv-row d-none" id="present_employees_dates">
                                                <label class="form-check-label fs-6 fw-semibold mb-2" for="all_holiday">
                                                    Mark holiday of present employees
                                                </label>
                                                <div
                                                    class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                                    <input class="form-check-input" type="checkbox" name="all_holiday">
                                                </div>
                                            </div>

                                            <div class="col-md-6 fv-row">
                                                <label class="form-check-label fs-6 fw-semibold mb-2"
                                                    for="overtime_applicable">
                                                    Overtime applicable
                                                </label>
                                                <div
                                                    class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="overtime_applicable" id="overtime_applicable"
                                                        value="1">
                                                </div>
                                            </div>
                                            <div class="row mt-5">
                                                <div class="col-md-6 fv-row d-none" id="payment_type">
                                                    <label class="required fs-6 fw-semibold mb-2">Overtime Payment
                                                        Type</label>
                                                    <select class="form-select" data-control="select2"
                                                        data-hide-search="true" data-placeholder="Select Payment Type"
                                                        name="overtime_payment_type" id="overtime_payment">
                                                        <option value=""> Select Payment Type</option>
                                                        <option value="0">Employee Salary</option>
                                                        <option value="1">Custom Amount</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 fv-row d-none" id="overtime_amount">
                                                    <label class="fs-6 fw-semibold mb-2 required"
                                                        id="overtime_label">Holiday Overtime Amount</label>
                                                    <input type="number" class="form-control"
                                                        name="holiday_overtime_amount" id="holiday_overtime"
                                                        step="0.01" placeholder="Enter Holiday Overtime Amount">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="modal-footer justify-content-center">

                                        <button type="reset" class="btn btn-light me-3"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="submit_btn" class="btn btn-primary">
                                            <span class="indicator-label">Submit</span>
                                            <span class="indicator-progress">Please wait
                                                <span class="spinner-border spinner-border-sm align-middle ms-2">
                                                </span>
                                            </span>
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>
                        {{-- end::Modal - New Target --}}
                    </form>


                </div>

                <form class="form" id="downloadbutton" action="{{ url('holidays/holidays-download') }}"
                    method="POST">
                    @csrf
                    <div class="modal fade" id="download_modal_pdf" uploaddex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable mw-650px">
                            <div class="modal-content rounded">

                                <div class="modal-header">
                                    <h4 class="modal-title pl-4">Download Gazzetted Holiday</h4>
                                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                    height="2" rx="1" transform="rotate(-45 6 17.3137)"
                                                    fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2"
                                                    rx="1" transform="rotate(45 7.41422 6)"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <div class="row g-8">

                                        <input name="m" type="hidden" value="{{ Request::get('m') }}">

                                        <input name="y" type="hidden" value="{{ Request::get('y') }}">

                                        <div class="col-md-12 mt-3 fv-row">
                                            <p onselectstart="javascript:return false;">This action required a security
                                                check. If you want to download employee(s) holiday(s) report,
                                                Please type <b>TODP-ID</b> (case sensitive) to confirm.</p>
                                            <input type="text" class="form-control " name="confirmation_code"
                                                placeholder="Enter Confirmation Code" required="required"
                                                autocomplete="off" onpaste="return false">
                                        </div>

                                        <div class="col-md-12 mt-3 fv-row text-center">
                                            <span class="d-none text-danger" id="download_message">
                                                Please wait, PDF file generation is in process. It may take a while.
                                            </span>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="reset" class="btn btn-light me-3"
                                        data-bs-dismiss="modal">Close</button>
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
        <script src="{{ url('assets/js/holiday/holiday.js') }}"></script>

        <script>
            var count = {{ $count }};
        </script>
    @endpush
