@extends('layout.main')

@section('main-section')
   
    <style>
        .custom-div-size-250{
            height:250px !important;
        }
        .imagenone{
            pointer-events: none;
        }
    </style>

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div class="app-container col-12 d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Attendance Visuaization Report</h1>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <div class="m-0">
                            <select class="form-select employee_status" data-kt-select2="true">
                                <option value="All" @if(Request::get('q') == '') selected @endif>All Employees</option>
                                <option value="Active" @if(Request::get('q') == 'Active') selected @endif>Active Employees</option>
                                <option value="Deactive" @if(Request::get('q') == 'Deactive') selected @endif>Deactive Employees</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content flex-column-fluid">
                {{--begin::Content container--}}
                <div class="app-container">

                    @if(empty(Request::get('q')) || Request::get('q') == 'All')

                        <div class="row gy-5 g-xl-10">

                            <div class="col-md-2 col-6">
                            
                                <div class="card card-flush">
                                    
                                        <div class="card-header pt-5">
                                        
                                            <div class="card-title d-flex flex-column">

                                                <div class="d-flex align-items-center">
                                                    
                                                    <h1 class="fw-bold text-dark me-2 lh-1 ls-n2">{{ $get_attendance_stats[0]['present_att'] }}</h1>
                                            
                                                </div>
                                            
                                                <span class="text-gray-400 pt-1 mb-3 fw-semibold fs-6">Total Present</span>
                                            
                                            </div>
                                        
                                        </div>
                                    
                                    
                                
                                </div>
                            
                                
                            </div>

                            <div class="col-md-2 col-6">
                            
                                <div class="card card-flush">
                                    
                                        <div class="card-header pt-5">
                                        
                                            <div class="card-title d-flex flex-column">

                                                <div class="d-flex align-items-center">
                                                    
                                                    <h1 class="fw-bold text-dark me-2 lh-1 ls-n2">{{ $get_attendance_stats[0]['present_absent'] }}</h1>
                                            
                                                </div>
                                            
                                                <span class="text-gray-400 pt-1 mb-3 fw-semibold fs-6">Total Absent</span>
                                            
                                            </div>
                                        
                                        </div>
                                    
                                    
                                
                                </div>
                            
                                
                            </div>

                            <div class="col-md-2 col-6">
                            
                                <div class="card card-flush">
                                    
                                        <div class="card-header pt-5">
                                        
                                            <div class="card-title d-flex flex-column">

                                                <div class="d-flex align-items-center">
                                                    
                                                    <h1 class="fw-bold text-dark me-2 lh-1 ls-n2">{{ $get_attendance_stats[0]['late'] + $get_attendance_stats[0]['early_out'] }}</h1>
                                            
                                                </div>
                                            
                                                <span class="text-gray-400 pt-1 mb-3 fw-semibold fs-6">Total Late / Early</span>
                                            
                                            </div>
                                        
                                        </div>
                                    
                                    
                                
                                </div>
                            
                                
                            </div>

                            <div class="col-md-2 col-6">
                            
                                <div class="card card-flush">
                                    
                                        <div class="card-header pt-5">
                                        
                                            <div class="card-title d-flex flex-column">

                                                <div class="d-flex align-items-center">
                                                    
                                                    <h1 class="fw-bold text-dark me-2 lh-1 ls-n2">{{ $get_attendance_stats[0]['half_day'] }}</h1>
                                            
                                                </div>
                                            
                                                <span class="text-gray-400 pt-1 mb-3 fw-semibold fs-6">Total Half Day</span>
                                            
                                            </div>
                                        
                                        </div>
                                    
                                    
                                
                                </div>
                            
                                
                            </div>

                            <div class="col-md-2 col-6">
                            
                                <div class="card card-flush">
                                    
                                    <div class="card-header pt-5">
                                    
                                        <div class="card-title d-flex flex-column">

                                            <div class="d-flex align-items-center">
                                                
                                                <h1 class="fw-bold text-dark me-2 lh-1 ls-n2">{{ $get_attendance_stats[0]['leave_att'] }}</h1>
                                        
                                            </div>
                                        
                                            <span class="text-gray-400 pt-1 mb-3 fw-semibold fs-6">Total Leaves</span>
                                        
                                        </div>
                                    
                                    </div>
                                    
                                </div>
                                
                            </div>

                            <div class="col-md-2 col-6">
                            
                                <div class="card card-flush">
                                    
                                        <div class="card-header pt-5">
                                        
                                            <div class="card-title d-flex flex-column">

                                                <div class="d-flex align-items-center">
                                                    
                                                    <h1 class="fw-bold text-dark me-2 lh-1 ls-n2">{{ $get_attendance_stats[0]['holiday'] }}</h1>
                                            
                                                </div>
                                            
                                                <span class="text-gray-400 pt-1 mb-3 fw-semibold fs-6">Total Holidays</span>
                                            
                                            </div>
                                        
                                        </div>
                                    
                                    
                                
                                </div>
                            
                                
                            </div>
                            
                        </div>

                    @endif

                    <div class="row">
                        {{-- <div class="col-xl-8 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Head Count Department Wise</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <div id="department_employee_graph" style="height: 350px"></div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-xl-4 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Overall Attendance Device Source Wise</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <canvas id="attendance_over_all_device_source_wise" class="mh-400px"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Check In Count Device Source Wise</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <canvas id="attendance_check_in_device_source_wise" class="mh-400px"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Check Out Count Device Source Wise</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <canvas id="attendance_check_out_device_source_wise" class="mh-400px"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Head Count Office Wise</span>
                                        {{-- <span class="text-muted fw-semibold fs-7">Department Employee</span> --}}
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <div id="head_count_office_wise" style="height: 350px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Head Count Employement Type</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <canvas id="head_count_by_employment_wise" class="mh-400px"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Head Count By Designation Wise</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <canvas id="head_count_by_designation_wise" class="mh-600px"></canvas>
                                </div>
                            </div>
                        </div>

                        

                        <div class="col-xl-8 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Head Count Age Range</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <canvas id="head_count_by_age_range" class="mh-600px"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8" style="height: 550px;">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Head Count Tenure Wise</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <div id="head_count_tenure_wise" style="height: 350px;"></div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-4 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Head Count By Management Level</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <canvas id="head_count_by_management_level" class="mh-400px"></canvas>
                                </div>
                            </div>
                        </div>
                        

                        <div class="col-xl-8 mt-4">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 mb-1">Head Count By Hiring Year Wise</span>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <canvas id="head_count_by_hiring_wise" class="mh-600px"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                {{--end::Content container--}}
            </div>
            {{--end::Content--}}
                   
        </div>
        {{--end::Content wrapper--}}
       
@endsection

@push('js-link')

<script>

    let att_count_check_in_wise_label = {!! json_encode(array_column($get_check_in_device_wise, 'attendance_source')); !!};
    let att_count_check_in_wise_data = {!! json_encode(array_column($get_check_in_device_wise, 'count_attendance')); !!};
    let att_count_check_out_wise_label = {!! json_encode(array_column($get_check_out_device_wise, 'attendance_source')); !!};
    let att_count_check_out_wise_data = {!! json_encode(array_column($get_check_out_device_wise, 'count_attendance')); !!};
    let over_all_attendance_wise_label = {!! json_encode(array_column($get_over_all_att_device_wise, 'attendance_source')); !!};
    let over_all_attendance_wise_data = {!! json_encode(array_column($get_over_all_att_device_wise, 'count_attendance')); !!};

    $('.employee_status').change(function(){
        var employee_status = $(this).val();
        if(employee_status){
            swal.fire({
                title: 'Confirmation !',
                text: 'Are you sure to see records of ' + employee_status + ' employee(s) ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText:'Yes',
                cancelButtonText:'Cancel',
                closeOnConfirm: true,
            }).then(function (value) {
                if(value.isConfirmed == true){
                    window.location.href = "{{ url('reports/employee-report') }}?q="+employee_status+"";
                }
            });
        }
    });
</script>

<script src="{{url('assets/js/widgets.bundle.js')}}"></script>
<script src="{{url('assets/js/custom/widgets.js')}}"></script>

<script src="{{url('assets/js/attendance-report.js')}}?js_v=2"></script>
@endpush