@extends('layout.main')






@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Employees Birthday
                    @if(empty(Request::get('mob')))
                        (In Month of {{ date('F') }})
                    @else
                        (In Month of {{ date("F", mktime(0, 0, 0, Request::get('mob'), 10)) }})
                    @endif</h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">

                @if(!empty($birthdays))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Employee Birthday Report
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

                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5">
                                <form action="{{ url('employee-birthdays') }}" method="GET">
                                    <div class="mb-10">

                                        <label class="form-label fw-semibold">Month:</label>

                                        <div>
                                            <select class="form-select " data-kt-select2="true" data-placeholder="Select Month" name="mob" required="">
                                                <option value="">Select Month</option>
                                                <option value="01" @if (!empty(Request::get('mob')) && Request::get('mob') == '01') selected @elseif(empty(Request::get('mob')) && date('m') == '01') selected @endif>January</option>
                                                <option value="02" @if (!empty(Request::get('mob')) && Request::get('mob') == '02') selected @elseif(empty(Request::get('mob')) && date('m') == '02') selected  @endif>February</option>
                                                <option value="03" @if (!empty(Request::get('mob')) && Request::get('mob') == '03') selected @elseif(empty(Request::get('mob')) && date('m') == '03') selected @endif>March</option>
                                                <option value="04" @if (!empty(Request::get('mob')) && Request::get('mob') == '04') selected @elseif(empty(Request::get('mob')) && date('m') == '04') selected  @endif>April</option>
                                                <option value="05" @if (!empty(Request::get('mob')) && Request::get('mob') == '05') selected @elseif(empty(Request::get('mob')) && date('m') == '05') selected @endif>May</option>
                                                <option value="06" @if (!empty(Request::get('mob')) && Request::get('mob') == '06') selected @elseif(empty(Request::get('mob')) && date('m') == '06') selected @endif>June</option>
                                                <option value="07" @if (!empty(Request::get('mob')) && Request::get('mob') == '07') selected @elseif(empty(Request::get('mob')) && date('m') == '07') selected @endif>July</option>
                                                <option value="08" @if (!empty(Request::get('mob')) && Request::get('mob') == '08') selected @elseif(empty(Request::get('mob')) && date('m') == '08') selected @endif>August</option>
                                                <option value="09" @if (!empty(Request::get('mob')) && Request::get('mob') == '09') selected @elseif(empty(Request::get('mob')) && date('m') == '09') selected  @endif>September</option>
                                                <option value="10" @if (!empty(Request::get('mob')) && Request::get('mob') == '10') selected @elseif(empty(Request::get('mob')) && date('m') == '10') selected @endif>October</option>
                                                <option value="11" @if (!empty(Request::get('mob')) && Request::get('mob') == '11') selected @elseif(empty(Request::get('mob')) && date('m') == '11') selected  @endif>November</option>
                                                <option value="12" @if (!empty(Request::get('mob')) && Request::get('mob') == '12') selected @elseif(empty(Request::get('mob')) && date('m') == '12') selected @endif>December</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('mob')))

                                        <a href="{{ url('employee-birthdays') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                            >Reset Filter</a>
                                        @endif
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary" >Apply</button>
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-body py-3">

                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">

                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Employee Code & Name</th>
                                        <th>Employee Department</th>
                                        <th>Birthday</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($birthdays))
                                        @foreach($birthdays as $birthday)
                                            <tr>
                                                <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($birthday['employee_id']) !!}</td>
                                                <td>
                                                    <span class="text-dark fw-bold mb-1 fs-6"> {{ $birthday['department_name'] }}</span> - 
                                                    <span class="text-muted fw-semibold text-muted fs-7">{{$birthday['designation_name'] }}</span>
                                                </td>
                                                <td>{{ date('d, F' , strtotime($birthday['dob'])) }}</td>
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

        <form class="form" id="downloadbutton" action="{{ url('employee-birthdays/employee-birthdays-download') }}" method="POST">
            @csrf
            <div class="modal fade" id="download_modal_pdf"uploaddex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable mw-650px">
                    <div class="modal-content rounded">

                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Download Employee Birthday Report</h4>
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

                                <input name="mob" type="hidden" value="{{Request::get('mob')}}">

                                <div class="col-md-12 mt-3 fv-row">
                                    <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) birthday report,
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
</div>

@endsection

