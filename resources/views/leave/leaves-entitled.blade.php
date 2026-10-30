@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Leaves Entitlement

                    @if(Request::get('is_active') == '2')
                        (De-active Employees)
                    @else
                        (Active Employees)
                    @endif
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                <div class="m-0">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#leave_upload">
                         <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>										
                        Update Employee Leave Records
                    </a>
                </div>
                
                @if (!empty($leaves_entitled))

                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">											
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Download Leaves Entitlement
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

                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('employee_location')) ? $filter : '' }} {{!empty(Request::get('is_active')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">

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
                            <form action="{{ url('manage-leaves') }}" method="GET">
                                <div class="px-7 py-5">

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

                                    <div class="mb-10">

                                        <label class="form-label fw-semibold">Employee Status</label>

                                        <div>
                                            <select class="form-select" data-kt-select2="true" data-placeholder="Select option" required name="is_active">
                                                <option value="1" @if (Request::get('is_active') == '1') selected @endif>Active
                                                    Employees</option>
                                                <option value="2" @if (Request::get('is_active') == '2') selected @endif>De-active
                                                    Employees</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('employee_location')) || !empty(Request::get('is_active')))
                                            <a href="{{ url('manage-leaves') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">
                                                Reset Filter</a>
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

                    <div class="table-responsive popup-visible ">

                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" data-server-paginated="true" aria-describedby="tableEmployee_info">

                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-6">Name</th>
                                        <th >Total Alloted Leaves</th>
                                        <th >Total Availed Leaves</th>
                                        <th >Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($leaves_entitled))
                                        @foreach ($leaves_entitled as $leaves_entitlement)
                                            <tr>
                                                <td class="ps-6">{{$leaves_entitlement['employee_code']}} - {{$leaves_entitlement['employee_name']}}</td>
                                                <td class="text-primary font-weight-bold ps-10">{{ $leaves_entitlement['total_alloted_leave'] }}</td>
                                                <td class="text-danger font-weight-bold ps-10">{{ $leaves_entitlement['used_get_availed_leaves'] ?? '0'}}</td>
                                                
                                                <td>
                                                    <a href="{{ url('manage-leaves/leave-detail').'/'.$leaves_entitlement['employee_id']}}"  class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Leave detail">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{{ url('manage-leaves/leave-detail-pdf').'/'.$leaves_entitlement['employee_id']}}"  class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Leave Report">
                                                        <i class="fa fa-book" aria-hidden="true"></i>
                                                    </a>
                                                
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

            {{--end::Modal - New Target--}}
        </div>
    </div>

    <form class="form" id="employee_leave_update">
        @csrf	
        <div class="modal fade" id="leave_upload" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Update Leave Records</h4>
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
                                <p class="text-danger mt-1">Please <b>Download Format File</b> and add leave(s) record. All the fields exist in file are required.</p>
                                <p class="text-danger mt-0">Only CSV file is supported for leave(s) upload process.</p>
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
                        <a href="/portal_assets/leave_excel_document/LeaveRecord.zip" id="download_btn" class="btn btn-success">Download Format</a>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <form class="form" id="downloadbutton" action="{{ url('manage-leaves/manage-leaves-download') }}" method="POST">
        @csrf
        <div class="modal fade" id="download_modal_pdf" uploaddex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Download Leaves Entitlement Report</h4>
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

                            <input name="is_active" type="hidden" value="{{Request::get('is_active')}}">

                            <input name="employee_location" type="hidden" value="{{Request::get('employee_location')}}">

                            <div class="col-md-12 mt-3 fv-row">
                                <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) leaves entitlement report,
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

@endsection

@push('js-link')
<script>
$('#employee_leave_update').submit(function (e) {

$('#update_btn').prop('disabled', true);
$("#update_btn").attr('data-kt-indicator', 'on');
$('#update_btn').css('cursor', 'not-allowed');
e.preventDefault();
$.ajax({
    url: "{{url('/manage-leaves/upload-employee-leave')}}",
    type: 'POST',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: new FormData(this),
    contentType: false,
    cache: false,
    processData: false,
    success: function (data) {

        if (data.status == 'TRUE') {
            Toast.fire({
                icon: 'success',
                title: data.msg,
                timer: 3000,
            })
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {

            Toast.fire({
                icon: 'warning',
                title: data.msg,
                timer: 3000,
            })
            $('#update_btn').prop('disabled', false);
            $('#update_btn').css('cursor', 'pointer');
            $("#update_btn").removeAttr('data-kt-indicator');

        }
    },
    error: function (jqXHR, textStatus) {
        var errorStatus = jqXHR.status;
        $('#update_btn').prop('disabled', false);
        $("#update_btn").removeAttr('data-kt-indicator');
        $('#update_btn').css('cursor', 'pointer');
        if (errorStatus == 0) {
            Toast.fire({
                icon: 'warning',
                title: 'Leave upload failed please try again',
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
});
    
</script>
@endpush

