@extends('layout.main')

@section('main-section')

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

            <div class="app-container col-12 d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">All Remote
                        Work Requests</h1>
                </div>
                @if ($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                    <div class="d-flex align-items-center gap-2 gap-lg-3">

                        <div class="m-0">
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addremotework">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Assign Remote Work to Employees
                            </a>
                        </div>

                    </div>
                @endif

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

                            <div class="d-flex align-items-center  on-check d-none">

                                @if ($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1)
                                    <a href="javascript:void(0)"
                                        class="wfh_request text-white  btn btn-sm fw-bold btn-success mx-2"
                                        data-value="Approve" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Approve Request">Approve</a>

                                    <a href="javascript:void(0)"
                                        class="wfh_request text-white  btn btn-sm fw-bold btn-danger mx-2"
                                        data-value="DisApprove" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Disapprove Request">DisApprove</a>
                                @else
                                    <a href="javascript:void(0)"
                                        class="wfh_request text-white  btn btn-sm fw-bold btn-success mx-2"
                                        data-value="LeadApprove" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Approve Request">Approve</a>

                                    <a href="javascript:void(0)"
                                        class="wfh_request text-white  btn btn-sm fw-bold btn-danger mx-2"
                                        data-value="LeadDisApprove" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Disapprove Request">DisApprove</a>
                                @endif
                            </div>
                        </div>

                        <div class="card-toolbar">
                            @php $filter = 'btn-color-primary'; @endphp

                            <div class="px-7 py-5">
                                <div>
                                    <select class="form-select" data-kt-select2="true" name="req_status" id="req_status">
                                        <option value="">All Requests</option>
                                        <option value="Pending" @if (Request::get('req_status') == '1') selected @endif>Pending
                                            Requests</option>
                                        <option value="Approved" @if (Request::get('req_status') == '2') selected @endif>Approved
                                            Requests</option>
                                        <option value="Disapproved" @if (Request::get('req_status') == '3') selected @endif>
                                            Disapproved Requests</option>
                                    </select>
                                </div>
                            </div>

                            <a href="#"
                                class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('employee_location')) ? $filter : '' }} {{ !empty(Request::get('emp_status')) ? $filter : '' }} {{ !empty(Request::get('emp_name')) ? $filter : '' }} {{ !empty(Request::get('req_status')) ? $filter : '' }} {{ !empty(Request::get('request_for_date')) ? $filter : '' }} {{ !empty(Request::get('request_submited_date')) ? $filter : '' }}"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">

                                <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </span>
                                Filter</a>
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true"
                                id="kt_menu_63347db386dd1">

                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                </div>

                                <div class="separator border-gray-200"></div>

                                <form action="{{ url('wfh-requests') }}" method="GET">
                                    <div class="px-7 py-5">

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                                <span>Office Location</span>
                                            </label>

                                            <div>
                                                <select class="form-select" data-control="select2"
                                                    data-hide-search="false" name="employee_location"
                                                    id="employee_location">
                                                    <option value="">Select Office Location</option>
                                                    @if (!empty($employee_locations))
                                                        @foreach ($employee_locations as $location)
                                                            <option value="{{ $location['location_id'] }}"
                                                                @if (Request::get('employee_location') == $location['location_id']) selected @endif>
                                                                {{ $location['location_title'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">Employee Status</label>

                                            <div>
                                                <select class="form-select" data-kt-select2="true"
                                                    data-placeholder="Select employee status" name="emp_status"
                                                    id="employee_data">
                                                    <option value="1"
                                                        @if (!empty(Request::get('emp_status')) && Request::get('emp_status') == '1') selected @endif>Active Employees
                                                    </option>
                                                    <option
                                                        value="2"@if (!empty(Request::get('emp_status')) && Request::get('emp_status') == '2') selected @endif>
                                                        De-active Employees</option>
                                                </select>

                                            </div>

                                        </div>
                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">By Name</label>

                                            <div>
                                                <select class="form-select" data-kt-select2="true" name="emp_name"
                                                    id="company_emp_name" aria-required="true">
                                                    <option value="">All Employees</option>
                                                    @if (!empty($employees_data))
                                                        @foreach ($employees_data as $data)
                                                            <option
                                                                value="{{ $data['employee_id'] }}"@if (Request::get('emp_name') == $data['employee_id']) selected @endif>
                                                                {{ $data['employee_code'] . ' - ' . $data['employee_name'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">Status</label>

                                            <div>
                                                <select class="form-select " data-kt-select2="true" name="req_status">
                                                    <option value="">All Requests</option>
                                                    <option value="1"
                                                        @if (Request::get('req_status') == '1') selected @endif>Pending Requests
                                                    </option>
                                                    <option value="2"
                                                        @if (Request::get('req_status') == '2') selected @endif>Approved Requests
                                                    </option>
                                                    <option value="3"
                                                        @if (Request::get('req_status') == '3') selected @endif>Disapproved
                                                        Requests</option>
                                                </select>
                                            </div>

                                        </div>

                                        @if ($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                            <div class="mb-3">

                                                <label class="form-label fw-semibold">Request Date</label>

                                                <div class="position-relative d-flex align-items-center">
                                                    <input type="date" class="form-control"
                                                        placeholder="Select a date" name="request_for_date"
                                                        @if (!empty(Request::get('request_for_date'))) value="{{ Request::get('request_for_date') }}" @endif />
                                                </div>

                                            </div>
                                            <div class="mb-3">

                                                <label class="form-label fw-semibold">Applied Date</label>

                                                <div class="position-relative d-flex align-items-center">
                                                    <input type="date" class="form-control"
                                                        placeholder="Select a date" name="request_submited_date"
                                                        max="{{ date('Y-m-d') }}"
                                                        @if (!empty(Request::get('request_submited_date'))) value="{{ Request::get('request_submited_date') }}" @endif>
                                                </div>

                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-end">
                                            @if (
                                                !empty(Request::get('employee_location')) ||
                                                    !empty(Request::get('emp_name')) ||
                                                    !empty(Request::get('request_for_date')) ||
                                                    !empty(Request::get('request_submited_date')))
                                                <a href="{{ url('wfh-requests') }}"
                                                    class="btn btn-sm btn-danger btn-active-light-danger me-2">
                                                    Reset Filter</a>
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
                    <div class="card-body ">

                        <div class="table-responsive popup-visible ">

                            <div id="tableEmployee_wrapper"
                                class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered">
                                <div class="table-responsive popup-visible ">
                                    <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="system_datatable"
                                        aria-describedby="tableEmployee_info">
                                        <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th></th>
                                                <th class="ps-4">Request By</th>
                                                <th>For Date</th>
                                                <th>Message</th>
                                                <th>Applied Date</th>
                                                <th data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Approval(s)">Approval(s)</th>
                                                <th data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Response By HR">Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($all_requests))
                                                @foreach ($all_requests as $all_request)
                                                    <tr
                                                        @if (request()->get('employee_id') != '' &&
                                                                $all_request['wfh_employee_id'] == request()->get('employee_id') &&
                                                                in_array($all_request['wfh_request_date'], $dates)) style="background-color:bisque;" @endif>
                                                        <td>

                                                            @if ($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1)
                                                                @if ($all_request['wfh_request_approved_by_hr'] != 1 || $all_request['wfh_request_date'] < date('Y-m-d'))
                                                                    <div
                                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <i class="form-check-input checked"
                                                                            type="checkbox" style="cursor: not-allowed;"
                                                                            disabled>
                                                                        </i>
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input checked"
                                                                            type="checkbox"
                                                                            data-id="{{ $all_request['wfh_request_id'] }}"
                                                                            value="{{ $all_request['wfh_request_id'] }}"
                                                                            data-hr="{{ $all_request['wfh_request_approved_by_hr'] }}" />
                                                                    </div>
                                                                @endif
                                                            @else
                                                                @if (in_array($all_request['wfh_request_id'], $responsed_request) ||
                                                                        $all_request['wfh_request_date'] < date('Y-m-d') ||
                                                                        $all_request['wfh_request_approved_by_hr'] != 1)
                                                                    <div
                                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <i class="form-check-input checked"
                                                                            type="checkbox" style="cursor: not-allowed;"
                                                                            disabled>
                                                                        </i>
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input checked"
                                                                            type="checkbox"
                                                                            data-id="{{ $all_request['wfh_request_id'] }}"
                                                                            value="{{ $all_request['wfh_request_id'] }}"
                                                                            data-hr="{{ $all_request['wfh_request_approved_by_hr'] }}" />
                                                                    </div>
                                                                @endif
                                                            @endif

                                                        </td>

                                                        <td class="ps-6">{!! CustomHelper::getEmpProfileDiv($all_request['employee_id']) !!}</td>
                                                        <td>{{ $all_request['wfh_request_date'] }}</td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0);" class="message_btn"
                                                                data-bs-toggle="modal" data-bs-target="#message_modal"
                                                                data-message="{{ $all_request['wfh_request_message'] }}">
                                                                <span class="svg-icon svg-icon-1" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="View Message"
                                                                    aria-hidden="true">
                                                                    <svg width="24" height="24"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3"
                                                                            d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z"
                                                                            fill="currentColor"></path>
                                                                        <rect x="6" y="12"
                                                                            width="7" height="2" rx="1"
                                                                            fill="currentColor"></rect>
                                                                        <rect x="6" y="7"
                                                                            width="12" height="2" rx="1"
                                                                            fill="currentColor"></rect>
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                        </td>
                                                        <td>{{ date('h:i A d M, Y', strtotime($all_request['wfh_request_submition_date'])) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0);" class="manager_modal_status_btn"
                                                                data-request-id="{{ $all_request['wfh_request_id'] }}"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Approval(s)">
                                                                @if (in_array($all_request['wfh_request_id'], $responsed_request))
                                                                    <i class="fas fa-user-check text-success"></i>
                                                                @else
                                                                    <i class="fas fa-user-check"></i>
                                                                @endif
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if ($all_request['wfh_request_approved_by_hr'] == 1)
                                                                <span
                                                                    class="badge badge-secondary text-center  text-muted font-weight-bold">Pending
                                                                </span>
                                                            @elseif($all_request['wfh_request_approved_by_hr'] == 2)
                                                                <span
                                                                    class="badge badge-light-success text-center font-weight-bold"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-title="{{ $all_request['wfh_request_approved_date_hr'] }}">Approved
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="badge badge-light-danger text-center font-weight-bold"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-title="{{ $all_request['wfh_request_approved_date_hr'] }}">Disapproved
                                                                </span>
                                                            @endif
                                                        </td>

                                                        <td class="text-center">
                                                            @if ($login_user[0]['employee_department'] == 2 || $login_user[0]['employee_department'] == 1)
                                                                @if ($all_request['wfh_request_approved_by_hr'] != 1 || $all_request['wfh_request_date'] < date('Y-m-d'))
                                                                    <i class="fa-regular fa-circle-check  text-success mx-2"
                                                                        style="font-size: 20px; cursor: not-allowed;"
                                                                        disabled>
                                                                    </i>
                                                                @else
                                                                    <i class="fa-regular fa-circle-check wfh_request text-success mx-2"
                                                                        data-id="{{ $all_request['wfh_request_id'] }}"
                                                                        data-value="Approve" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" title="Approve Request"
                                                                        style="font-size: 20px;cursor: pointer;">
                                                                    </i>
                                                                @endif

                                                                @if ($all_request['wfh_request_approved_by_hr'] != 1 || $all_request['wfh_request_date'] < date('Y-m-d'))
                                                                    <i class="fa-regular fa-circle-xmark  text-warning mx-2"
                                                                        style="font-size: 20px; cursor: not-allowed;"
                                                                        disabled>
                                                                    </i>
                                                                @else
                                                                    <i class="fa-regular fa-circle-xmark wfh_request text-warning mx-2"
                                                                        data-value="DisApprove"
                                                                        data-id="{{ $all_request['wfh_request_id'] }}"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="Disapprove Request"
                                                                        style="font-size: 20px;cursor: pointer;">
                                                                    </i>
                                                                @endif
                                                            @else
                                                                @if (in_array($all_request['wfh_request_id'], $responsed_request) ||
                                                                        $all_request['wfh_request_approved_by_hr'] != 1 ||
                                                                        $all_request['wfh_request_date'] < date('Y-m-d'))
                                                                    <i class="fa-regular fa-circle-check  text-success mx-2"
                                                                        style="font-size: 20px; cursor: not-allowed;"
                                                                        disabled>
                                                                    </i>
                                                                @else
                                                                    <i class="fa-regular fa-circle-check wfh_request text-success mx-2"
                                                                        data-id="{{ $all_request['wfh_request_id'] }}"
                                                                        data-value="LeadApprove" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" title="Approve Request"
                                                                        style="font-size: 20px;cursor: pointer;">
                                                                    </i>
                                                                @endif


                                                                @if (in_array($all_request['wfh_request_id'], $responsed_request) ||
                                                                        $all_request['wfh_request_approved_by_hr'] != 1 ||
                                                                        $all_request['wfh_request_date'] < date('Y-m-d'))
                                                                    <i class="fa-regular fa-circle-xmark  text-warning mx-2"
                                                                        style="font-size: 20px; cursor: not-allowed;"
                                                                        disabled>
                                                                    </i>
                                                                @else
                                                                    <i class="fa-regular fa-circle-xmark wfh_request text-warning mx-2"
                                                                        data-value="LeadDisApprove"
                                                                        data-id="{{ $all_request['wfh_request_id'] }}"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="Disapprove Request"
                                                                        style="font-size: 20px;cursor: pointer;">
                                                                    </i>
                                                                @endif
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

                    <form id="addremoteworkForm" class="form">
                        @csrf
                        <div class="modal fade" id="addremotework" tabindex="-1" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-scrollable mw-650px">

                                <div class="modal-content rounded">


                                    <div class="modal-header">
                                        <h4 class="modal-title pl-4">Assign Remote Work to Employees</h4>
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
                                                <select class="form-select departments_all" id="departments_all"
                                                    data-close-on-select="false" data-control="select2"
                                                    multiple="multiple" data-hide-search="true"
                                                    data-placeholder="Select Department" name="department[]" required>

                                                    @php $count = 0 @endphp
                                                    @if (!empty($departments))
                                                        @foreach ($departments as $department)
                                                            @php $count ++; @endphp
                                                            <option value="{{ $department['department_id'] }}">
                                                                {{ $department['department_name'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-8 mt-3 fv-row">

                                                <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                                <label
                                                    class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                                    <input type="hidden" name="count" value="{{ $count }}">
                                                    <input class="form-check-input h-20px w-20px departments_all"
                                                        type="checkbox" id="department_all" value="Select All" />

                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-10 mb-8 fv-row" id="selectedemployee">
                                                <label class="fs-6 fw-semibold mb-2 required">Employees</label>

                                                <select class="form-select" data-control="select2" multiple="multiple"
                                                    data-hide-search="true" data-close-on-select="false"
                                                    data-placeholder="Select Employee" name="employee[]" id="employees"
                                                    required>
                                                    @if (!empty($employees_data))
                                                        @foreach ($employees_data as $employee)
                                                            <option value="{{ $employee['employee_id'] }}">
                                                                {{ $employee['employee_code'] }} -
                                                                {{ $employee['employee_name'] }}</option>';
                                                        @endforeach
                                                    @endif
                                                </select>

                                            </div>
                                            <div class="col-md-2 mb-8 fv-row">

                                                <label class="fs-6 fw-semibold mb-2">All Employees</label>
                                                <label
                                                    class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2 form-check-solid">
                                                    <input class="form-check-input h-20px w-20px" type="checkbox"
                                                        id="employee_all" value="Select All" />

                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column mb-8">
                                            <label class="required fs-6 fw-semibold mb-2">Message</label>
                                            <textarea class="form-control max_length_validator" placeholder="Enter a message" rows="4"
                                                name="home_work_reason" maxlength="500" required="required"></textarea>
                                        </div>

                                        <div class="col-md-12 mb-8 mt-3 fv-row">
                                            <label class="required fs-6 fw-semibold mb-2">Select Date</label>

                                            <div class="position-relative d-flex align-items-center">
                                                <input type="date" class="form-control" id="request_date" name="request_date"
                                                    min="{{ date('Y-m-d') }}">
                                            </div>

                                        </div>

                                    </div>


                                    <div class="modal-footer justify-content-center">

                                        <button type="reset" class="btn btn-light me-3"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="submit-btn" class="btn btn-primary">
                                            <span class="indicator-label">Submit</span>
                                            <span class="indicator-progress">Please wait
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </form>


                </div>

            </div>

            {{-- --Approve Request Modal --}}
            <div class="modal fade" id="modal-success" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-top">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Confirmation Alert</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-white"
                                data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill=""
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16"
                                            height="2" rx="1" transform="rotate(-45 6 17.3137)"
                                            fill="" />
                                        <rect x="7.41422" y="6" width="16" height="2"
                                            rx="1" transform="rotate(45 7.41422 6)" fill="" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <form class="request_submit">
                            @csrf
                            <div class="modal-body pt-0">
                                <p class="pt-4 fs-6 msg">Are you sure to <b>Approve</b> selected Remote Work request ?</p>

                                @if ($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 1)
                                    <textarea class="form-control " rows="3" name="approvermessage" placeholder="Message" id="approvermessage"
                                        required></textarea>
                                @endif
                            </div>

                            <div class="modal-footer">
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="approve_btn" class="btn btn-primary">
                                    <span class="indicator-label">Yes, Approve</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- --DisApprove Request Modal --}}

            <div class="modal fade" id="modal-danger" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-top">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Confirmation Alert</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-white"
                                data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill=""
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16"
                                            height="2" rx="1" transform="rotate(-45 6 17.3137)"
                                            fill="" />
                                        <rect x="7.41422" y="6" width="16" height="2"
                                            rx="1" transform="rotate(45 7.41422 6)" fill="" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <form class="request_submit">
                            @csrf
                            <div class="modal-body pt-0">
                                <p class="pt-4 fs-6 msg">Are you sure to <b>Disapprove</b> selected Remote Work request ?
                                </p>

                                @if ($login_user[0]['employee_department'] != 2 && $login_user[0]['employee_department'] != 1)
                                    <textarea class="form-control " rows="3" name="disapprovermessage" placeholder="Message"
                                        id="disapprovermessage" required></textarea>
                                @endif
                            </div>

                            <div class="modal-footer">
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="disapprove_btn" class="btn btn-primary">
                                    <span class="indicator-label">Yes, Disapprove</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            {{-- begin::Modal - WorkFrom Home  Message --}}

            <div class="modal fade" id="message_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-top">
                    <div class="modal-content rounded">
                        <div class="modal-content rounded">
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Request Message</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                                    data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                height="2" rx="1" transform="rotate(-45 6 17.3137)"
                                                fill="currentColor" />
                                            <rect x="7.41422" y="6" width="16" height="2"
                                                rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p id="message_body"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        @endsection



        @push('js-link')
            <script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>
            <script src="{{ url('assets/js/wfh/all-request.js') }}"></script>
            <script>
                var length_count = {{ $count }};
            </script>
        @endpush
