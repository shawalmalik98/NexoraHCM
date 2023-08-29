@extends('layout.main')

@section('main-section')

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

            <div class="app-container col-12 d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My Remote
                        Work Requests</h1>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">

                    <div class="m-0">

                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_new_target">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                        rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                        fill="currentColor"></rect>
                                </svg>
                            </span>
                            Request Now</a>
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
                        </div>

                    </div>
                    <div class="card-body ">

                        <div class="table-responsive popup-visible ">

                            <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="table-responsive popup-visible ">
                                    <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered"
                                        id="system_datatable" aria-describedby="tableEmployee_info">

                                        <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th class="text-center ps-4">Request Date</th>
                                                <th class="text-center">Request Message</th>
                                                <th class="text-center">Applied Date</th>
                                                <th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Approval(s)">Approval(s)</th>
                                                <th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Response By HR">Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($my_requests))
                                                @foreach ($my_requests as $my_request)
                                                    <tr
                                                        @if (request()->get('request-id') != '' && $my_request['wfh_request_id'] == request()->get('request-id')) style="background-color:bisque;" @endif>
                                                        <td class="text-center">{{ $my_request['wfh_request_date'] }}</td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0);" class="message_btn"
                                                                data-bs-toggle="modal" data-bs-target="#message_modal"
                                                                data-message="{{ $my_request['wfh_request_message'] }}">
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
                                                        <td class="text-center">
                                                            {{ $my_request['wfh_request_submition_date'] }}</td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0);" class="manager_modal_status_btn"
                                                                data-request-id="{{ $my_request['wfh_request_id'] }}"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Approval(s)">
                                                                @if (in_array($my_request['wfh_request_id'], $responsed_request))
                                                                    <i class="fas fa-user-check text-success"></i>
                                                                @else
                                                                    <i class="fas fa-user-check"></i>
                                                                @endif
                                                            </a>
                                                        </td>

                                                        <td class="text-center">
                                                            @if ($my_request['wfh_request_approved_by_hr'] == 1)
                                                                <span
                                                                    class="badge badge-light-secondary text-center text-muted d-inline-block  font-weight-bold"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-title="HR didn't respond to this request yet.">Pending</span>
                                                            @elseif($my_request['wfh_request_approved_by_hr'] == 2)
                                                                <span
                                                                    class="badge badge-light-success text-center d-inline-block  font-weight-bold"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-title="{{ $my_request['wfh_request_approved_date_hr'] }}">Approved</span>
                                                            @else
                                                                <span
                                                                    class="badge badge-light-danger text-center d-inline-block  font-weight-bold"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-title="{{ $my_request['wfh_request_approved_date_hr'] }}">Disapproved</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if (in_array($my_request['wfh_request_id'], $responsed_request) || $my_request['wfh_request_approved_by_hr'] != 1)
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                                    <span class="svg-icon svg-icon-3" disabled
                                                                        style="font-size: 20px; cursor: not-allowed;">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                                fill="currentColor"></path>
                                                                            <path
                                                                                d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                                fill="currentColor"></path>
                                                                        </svg>
                                                                    </span>
                                                                </a>

                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                                    <span class="svg-icon svg-icon-3" disabled
                                                                        style="font-size: 20px; cursor: not-allowed;">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                                                fill="currentColor"></path>
                                                                            <path opacity="0.5"
                                                                                d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                                                fill="currentColor"></path>
                                                                            <path opacity="0.5"
                                                                                d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                                                fill="currentColor"></path>
                                                                        </svg>
                                                                    </span>
                                                                </a>
                                                            @else
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit_btn"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Edit Remote Work Request"
                                                                    onclick="editWFHRequest('{{ $my_request['wfh_request_id'] }}','{{ $my_request['wfh_request_date'] }}','{{ $my_request['wfh_request_message'] }}')">
                                                                    <span class="svg-icon svg-icon-3">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                                fill="currentColor"></path>
                                                                            <path
                                                                                d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                                fill="currentColor"></path>
                                                                        </svg>
                                                                    </span>
                                                                </a>

                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Delete Remote Work Request"
                                                                    onclick="deleteWFHRequest('{{ $my_request['wfh_request_id'] }}')">
                                                                    <span class="svg-icon svg-icon-3">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                                                fill="currentColor"></path>
                                                                            <path opacity="0.5"
                                                                                d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                                                fill="currentColor"></path>
                                                                            <path opacity="0.5"
                                                                                d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                                                fill="currentColor"></path>
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


                </div>

            </div>

            <form id="send_new_request" class="form">
                @csrf
                <div class="modal fade" id="kt_modal_new_target" tabindex="-1" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-scrollable mw-650px">

                        <div class="modal-content rounded">

                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Remote Work Request</h4>
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

                                <div class="d-flex flex-column mb-8 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Remote Work Day(s)</label>
                                    <select class="form-select" data-control="select2" data-hide-search="true"
                                        data-placeholder="Select Remote Work Day(s)" name="days_assign" id="days_assign"
                                        required>
                                        <option value="">Select Remote Work Day(s)</option>
                                        <option value="single">Single Day Request</option>
                                        <option value="multiple">Multiple Days Requests</option>
                                    </select>
                                </div>

                                <div class="from_to_date_input row g-9 mb-8 d-none">
                                    <div class="col-md-4 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">From Date</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="date" class="form-control date-control"
                                                placeholder="Select a date" name="from_date" id="from_date"
                                                min="{{ date('Y-m-d') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">To Date</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="date" class="form-control date-control"
                                                placeholder="Select a date" name="to_date" id="to_date" disabled />
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

                                <div class="only_date_input col-md-12 mb-8 fv-row d-none">
                                    <label class="required fs-6 fw-semibold mb-2">Select Date</label>
                                    {{-- begin::Input --}}
                                    <div class="position-relative d-flex align-items-center">
                                        <input type="date" class="form-control" autocomplete="off"
                                            placeholder="Select a date" id="wfh_request_date" name="request_date"
                                            min="{{ date('Y-m-d') }}" required />
                                        {{-- end::Datepicker --}}
                                    </div>
                                    {{-- end::Input --}}
                                </div>

                                {{-- end::Input group --}}

                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Enter Reason</label>
                                    <textarea class="form-control " rows="5" name="home_work_reason" id="kt_docs_maxlength_threshold2"
                                        maxlength="500" placeholder="Message" required></textarea>
                                </div>
                                {{-- end::Input group --}}
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
            </form>
            {{-- end::Modal - New Target --}}

            {{-- ----Edit WFH REQUEST---- --}}
            <form id="edit_wfh_request" class="form">
                @csrf
                <div class="modal fade" id="edit-wfh-request" tabindex="-1" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-scrollable mw-650px">

                        <div class="modal-content rounded">

                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Edit Remote Work Request</h4>
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


                                <input type="hidden" name="request_id" value="" />

                                <div class="col-md-12 mb-8 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Select Date</label>
                                    {{-- begin::Input --}}
                                    <div class="position-relative d-flex align-items-center">
                                        <input type="date" class="form-control" autocomplete="off"
                                            placeholder="Select a date" id="edit_request_date" name="request_date"
                                            min="{{ date('Y-m-d') }}" required />
                                        {{-- end::Datepicker --}}
                                    </div>
                                    {{-- end::Input --}}
                                </div>

                                {{-- end::Input group --}}

                                <div class="d-flex flex-column mb-8">
                                    <label class="fs-6 fw-semibold mb-2 required">Enter Reason</label>
                                    <textarea class="form-control " rows="5" name="edit_home_work_reason" maxlength="500" placeholder="Message"
                                        required id="edit-wfh-message"></textarea>
                                </div>
                                {{-- end::Input group --}}
                            </div>


                            <div class="modal-footer justify-content-center">

                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit_btn_wfh" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>

                        </div>

                    </div>

                </div>
            </form>
            {{-- ----Edit WFH REQUEST---- --}}


            {{-- - Delete WFH Request ------------ --}}
            <div class="modal fade" tabindex="-1" id="delete-wfh-request">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Confirmation Alert</h3>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                                data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1 bg-white">
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
                        <form id="delete_wfh_request" class="form">
                            @csrf

                            <div class="modal-body">
                                <input type="hidden" class="form-control" name="wfh_id" />
                                <p>Are you sure to delete this Remote Work Request ?</p>
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

            {{-- begin::Modal - WFH Message --}}

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
                                <p id="message_body" class=""></p>
                            </div>

                        </div>

                    </div>

                </div>
                {{-- end::Modal - WFH Message --}}
            </div>

        @endsection



        @push('js-link')
            <script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>

            <script src="{{ url('assets/js/wfh/personal-request.js') }}"></script>
        @endpush
