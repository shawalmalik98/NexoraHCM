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
                @if(!empty($all_devices))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Employee Device Details
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
                </div>

                <div class="card-body py-3">

                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">

                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Employee Name</th>
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
                                                <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($all_device['device_employee_id']) !!}</td>
                                                <td> {{ $all_device['device_name']}}</td>
                                                <td> {{ $all_device['device_token']}}</td>
                                                <td>
                                                    @if( $all_device['device_status'] == 1)
                                                        <span class="badge badge-light-success font-weight-bold">Active</span>
                                                    @else
                                                        <span class="badge badge-light-danger font-weight-bold">De-active</span>

                                                    @endif
                                                </td>
                                                <td> {{ $all_device['device_created_at']}}</td>
                                                <td> {{ $all_device['device_updated_at']}}</td>

                                                <td>

                                                    @if ($all_device['device_status'] == 1 )
                                                        <i class="fa-regular fa-circle-check  text-success mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                    @else
                                                            <a href="#."  data-bs-toggle="modal" data-bs-target="#approverequest" onclick="deviceEditModel({{$all_device['device_id']}})">  <i class="fa-regular fa-circle-check  text-info mx-2" style="font-size: 20px; cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="Approve device" ></i>
                                                        </a>
                                                    @endif

                                                    @if ($all_device['device_status'] == 2)
                                                        <i class="fa-regular fa-circle-xmark  text-danger mx-2" style="font-size: 20px; cursor: not-allowed;" disabled></i>
                                                    @else
                                                        <a href="#." data-bs-toggle="modal"  data-bs-target="#disapproverequest" onclick="deviceModel({{$all_device['device_id']}})"><i class="fa-regular fa-circle-xmark   text-warning mx-2" style="font-size: 20px;cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="Disapprove device"></i></a>

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

    <form class="form" id="downloadbutton" action="{{ url('employee-devices/employee-devices-download') }}" method="POST">
        @csrf
        <div class="modal fade" id="download_modal_pdf"uploaddex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">

                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Download Employee Device Details</h4>
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

                            <div class="col-md-12 mt-3 fv-row">
                                <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) device detail,
                                Please type <b>TODP-ID</b> (case sensitive) to confirm.</p>
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
