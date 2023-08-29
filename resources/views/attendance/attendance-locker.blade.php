@extends('layout.main')

@push('css-link')

@endpush

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
						 
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Attendance Locking Sheet</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                 
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add_employee_group_modal">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Lock Attendance</a>
                </div>
              
            
            </div>
            
        </div>
    </div>
       
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">

                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1 me-2">
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
                <div class="card-body ">
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">						
                            <table class="table gs-0 gy-4 dataTable table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                                                                    
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="text-center">Ref ID</th>
                                        <th class="text-center">Attendance Period</th>
                                        <th class="text-center">Locked by</th>
                                        <th class="text-center">Locked at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($get_locked_sheet_records))
                                        @foreach ($get_locked_sheet_records as $employment_status)
                                            <tr>
                                                <td class="text-center">{{ $employment_status['sheet_lock_id'] }}</td>
                                                <td class="text-center">{{ date('M, Y', strtotime( $employment_status['attendance_period'] )) }}</td>
                                                <td class="text-center">{!! CustomHelper::getEmpProfileDiv($employment_status['locked_by']) !!}</td>
                                                <td class="text-center">{{ date('h:i A d M, Y', strtotime( $employment_status['locked_date'] )) }}</td>
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

    <form id="attendance_locker_form" class="form" autocomplete="off">
        @csrf	
        <div class="modal fade" id="add_employee_group_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Lock Attendance Sheet</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>
                    
                    <div class="modal-body scroll-y pt-3 pb-5">
                        
                        <div class="col-md-12 mb-8 mt-3 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Select Attendance Period</label>
                            <div class="position-relative d-flex align-items-center">
                                <input type="month" class="form-control" name="attendance_period" max="{{ date('Y-m') }}" required>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait 
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
<script type="text/javascript" src="{{ url('assets/js/request-status-response-log.js') }}?js_v=2"></script>

<script src="{{url('assets/js/attendance/attendance-locker.js')}}"></script>

@endpush