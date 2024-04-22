@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
       
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                 
                    @if (!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                      Attendance Adjustment ({{date('F Y',strtotime($year . '-' . $month . '-01')) }})
                    @else
                        
                        Attendance Adjustment ({{date('d-M', strtotime($from_date)) .' to ' .date('d-M-Y', strtotime($to_date))}})
                    @endif
                </h1>   
            </div>
            {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @if (!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                        <a href="{{ url('my-attendance-report?from_date='.Request::get('from_date').'&to_date='.Request::get('to_date'))}}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @else
                        <a href="{{ url('my-attendance-report?from_date='.Request::get('from_date').'&to_date='.Request::get('to_date')) }}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >View Attendance Report</a>
                    @endif
                  
                </div>
            </div> --}}
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container" >
            <form action="{{ url('attendance-adjustment') }}" method="GET">
                <div class="row" id="card-container">
                        
                    <div class="col-md-2 col-6 mb-4"  >
                        <div class="card" >
                            <div class="p-3">
                                <label class="form-label fw-semibold @if(!empty(Request::get('from_date'))) required  @endif" id="label-from-date">Form Date</label>
                                <input class="form-control" type="date" name="from_date" id="from_date" value="{{$from_date}}" @if(!empty(Request::get('from_date'))) required  @endif max="{{date('Y-m-d')}}" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 col-6 mb-4"  >
                        <div class="card" >
                            <div class="p-3">
                                <label class="form-label fw-semibold @if(!empty(Request::get('to_date'))) required  @endif" id="label-to-date">To Date</label>
                                <input class="form-control" type="date" name="to_date" id="to_date" value="{{$to_date}}" @if(!empty(Request::get('to_date'))) required  @endif max="{{date('Y-m-d')}}"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 mb-4"  >
                        <div class="card" >
                            <div class="p-3">
                                        
                                <label class="form-label fw-semibold required">
                                    <span>Select Employee</span>
                                </label>
                                <div>
                                    <select class="form-select" data-kt-select2="true" name="employee_id" data-placeholder="Select employees" required>
                                        <option value="">Select Employee</option>
                                        @if(!empty($all_employees))
                                            @foreach ($all_employees as $employees)
                                                <option value="{{ $employees['employee_id'] }}" @if (Request::get('employee_id') == $employees['employee_id']) selected @endif>{{$employees['employee_code']. ' - ' .$employees['employee_name']}} </option>
                                            @endforeach
                                        @endif                                                             
                                    </select>
                                    
                                </div>
                            </div>
                        </div>    
                    </div>

                    <div class="col-md-2 col-6 mb-4"  >
                        <div class="card" >
                            <div class="p-3">
                                <label class="form-label fw-semibold required">
                                    <span>Select Status</span>
                                </label>
                                <div>
                                    <select class="form-select " data-kt-select2="true" data-placeholder="Attendance Status" name="att_status" required>
                                        <option value="">Attendance status</option>
                                        <option value="All" @if (Request::get('att_status') == 'All' || empty(Request::get('att_status'))) selected @endif>All Status</option>
                                        <option value="Absent" @if (!empty(Request::get('att_status')) && Request::get('att_status') == 'Absent') selected @endif>Absent</option>
                                        <option value="Off Day" @if (!empty(Request::get('att_status')) && Request::get('att_status') == 'Off Day') selected @endif>Off Day</option>
                                       
                                    </select>
                                </div> 
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 mb-4"  >
                        <div class="card" style="width:max-content !important; height: 6.9rem; justify-content: center;" >
                            <div class="p-4">

                                @if (!empty(Request::get('from_date')) || !empty(Request::get('to_date')) || !empty(Request::get('employee_id')) || !empty(Request::get('att_status')))
                                    <a href="{{ url('attendance-adjustment') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2">Reset Filter</a>
                                @endif
                                <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                <button type="submit" class="btn btn-sm btn-primary" >Apply</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>    
            <div class="card mb-5 mb-xl-8">

                <div class="card-header border-0 pt-5">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1 me-5">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilter">
                        </div>  

                        <div class="" style="width:6.5rem">  
                        <div class="d-flex align-items-center  on-check d-none">

                            <a href="javascript:void(0)"  class="request_submit text-white btn btn-sm fw-bold btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve Attendance" >Approve</a>
                        </div>
                                
                        </div>
                        <div class="d-flex ps-15" style="width: 58.7rem; justify-content: end;">
                            <div class="row" style="width: 100%; justify-content:flex-end;">
                                <div class="col-md-6" style="width: 46% !important">
                                <input type="text" class="form-control th-remarks-input" placeholder="Enter Remarks" />
                                </div>
                                <div class="form-check form-check-sm form-check-custom form-check-solid col-md-1" style="width: 6.3333%">
                                    <input class="form-check-input th-checkbox" type="checkbox"> 
                                
                                </div>
                            </div> 
                        </div>    
                      
                    </div>
                </div>
                @if(Session::has('error'))
                    <script>
                         swal.fire("Error", "{{Session::get('error') }}", "error");
                    </script>
                @endif
                    <div class="card-body py-3">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive popup-visible ">
                                <table class="table table-bordered align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable">
                                                            
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-8">Date</th>
                                            <th class="ps-8">Employee Name</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center ps-15">Remarks </th>

                                        </tr>
                                    </thead>

                                    <tbody>

                                        @if (!empty($attendance))
                                            @foreach ($attendance as $attendances)
                                                @if($attendances['Attendance Status'] == Request::get('att_status') || Request::get('att_status') == 'All')
                                                    <tr>
                                                    
                                                        <td class="ps-8">
                                                            {{ $attendances['Date'] }} -  {{ $attendances['Day'] }}
                                                        </td>
                                                        <td class="ps-8">
                                                            {{ $attendances['employee_code'] }} - {{ $attendances['employee_name'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $attendances['Attendance Status']}} 
                                                        </td>
                                                        
                                                        <td class="text-center ps-15">

                                                            <form class="add_new_adjustment">
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-10">
                                                                    <input type="text" class="form-control td-remarks-input" placeholder="Enter Remarks" name="attendance_remarks" required/>
                                                                    </div>
                                                                    <div class="form-check form-check-sm form-check-custom form-check-solid col-md-2">
                                                                        <input class="form-check-input td-checkbox" type="checkbox">
                                                                        <input type="hidden" name="employee_code" class="auth_employee_Code" value="{{ Request::get('employee_id') ?? $login_user[0]['employee_id'] }}" />
                                                                        <input type="hidden" name="attendance_date" class="att_date" value="{{ date('Y-m-d', strtotime($attendances['Date'])) }}"/>
                                                                    
                                                                    </div>
                                                                </div>    
                                                            </form>   

                                                        </td>
                                                        
                                                    </tr>
                                                @endif    
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
    
    </div>
</div>

@endsection



@push('js-link')
{{-- <script src="{{url('assets/js/attendance/my-attendance.js')}}"></script> --}}
<script>

    $('.add_new_adjustment input[name="attendance_remarks"]').keydown(function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        }
    });

    $(document).ready(function() {
        $('.th-checkbox').change(function() {
            var th_Checked = $(this).is(':checked');
            $('.td-checkbox').prop('checked', th_Checked);

            if (th_Checked) {
                $('.on-check').removeClass('d-none');
            }
            else {
                $('.on-check').addClass('d-none');
            }
        });

        $('.td-checkbox').change(function() {
            var isChecked = $('.td-checkbox').is(':checked');
                
            if (isChecked) {
                $('.on-check').removeClass('d-none');
            }
            else {
                $('.on-check').addClass('d-none');
            }
        });

        $('.th-remarks-input').keyup(function() {
            var remarks = $(this).val();
            $('.td-remarks-input').val(remarks);
        });

        var valid_employee_code = $('.auth_employee_Code').val();

        $('.request_submit').click(function() {

            var button = $(this); 
            event.preventDefault(); 

            var current_employee_code = $('.auth_employee_Code').val();

            if (valid_employee_code !== current_employee_code) {
                alert('Employee code cannot be changed.');
                checkbox.prop('checked', false);
                return; 
            }

            var dataToSend = []; 
            var isValid = true;

            $('tbody td').each(function() {
                var remarks = $(this).find('.td-remarks-input').val();
                var employeeCode = $(this).find('[name="employee_code"]').val();
                var attendanceDate = $(this).find('.att_date').val();
                var isChecked = $(this).find('.td-checkbox').prop('checked');

                if (isChecked) {
                    if (remarks.trim() === '') {
                        alert('Remarks cannot be empty for selected checkboxes.');
                        isValid = false;
                        return false; 
                    }
                    var rowData = {
                        attendance_remarks: remarks,
                        employee_code: employeeCode,
                        attendance_date: attendanceDate,
                        is_checked: isChecked
                    };

                    dataToSend.push(rowData);   

                }
            });

            if (isValid && dataToSend.length > 0) {

                button.addClass('disabled');
                
                $.ajax({
                    url: base_url + '/attendance-adjustment-update',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { data: dataToSend }, 
                    success: function (data) {
                        if (data.status == 'TRUE') {
                            Toast.fire({
                                icon: 'success',
                                title: data.msg,
                                timer: 3000,
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1000);

                        } else {
                            button.removeClass('disabled');
                            Toast.fire({
                                icon: 'warning',
                                title: data.msg,
                                timer: 3000,
                            });

                        }
                    },
                    error: function (jqXHR, textStatus) {
                        var errorStatus = jqXHR.status;
                        if (errorStatus == 0) {
                            Toast.fire({
                                icon: 'warning',
                                title: 'Internet Connection Problem',
                                timer: 3000,
                            });
                        } else {
                            if (errorStatus == 419) {
                                location.reload();
                            } else {
                                Toast.fire({
                                    icon: 'warning',
                                    title: 'Try Again. Error Code ' + errorStatus,
                                    timer: 3000,
                                });

                            }
                        }
                    }
                });
            } 
        });

    });

   
</script>

    
@endpush