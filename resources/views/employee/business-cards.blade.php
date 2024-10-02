@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
    {{--begin::Toolbar--}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        {{--begin::Toolbar container--}}
        <div class="app-container col-12 d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Employee Business Cards</h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary">Go Back</a>
            </div>
        </div>
    </div>
    <div class="app-content flex-column-fluid">
        {{--begin::Content container--}}
        <div class="app-container">
            {{--begin::Tables Widget 11--}}
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Employee Details</span>
                        <span class="text-muted mt-1 fw-semibold fs-7"></span>
                    </h3>
                    <div class="card-toolbar">
                        
                    </div>
                </div> 

                <div class="card-body py-3">
                    <form  id="edit_business_card_form" class="form" autocomplete="off">
                        @csrf
                        {{-- <div class="row g-9 mb-8">
                            <div class="col-md-12 ">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Employee Code</span>
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Employee Code" name="employee_code" required  value="{{ $employee_detail[0]['employee_code'] }}">
                            </div>
                        </div> --}}
                        {{-- <input type="hidden" class="form-control" placeholder="Enter Employee Code" name="employee_code" required  value="{{ $employee_detail[0]['employee_code'] }}"> --}}
                        <div class="row g-9 mb-8">

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee ID</span>
                                </label>
                                
                                <input type="text" class="form-control" placeholder="Enter Employee ID" name="employee_code" required value="{{ $employee_detail[0]['employee_code'] }}" readonly style="background-color:lightgray"/>
                            </div>
                            
                            <div class="col-md-6 ">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2"> Employee Name</label>
                                <input type="text" class="form-control " placeholder="Enter Employee Name" name="employee_name" required  value="{{ $employee_detail[0]['employee_name'] }}">
                                <input type="hidden" class="form-control" name="employee_id" placeholder="Enter Employee Name" required="required" value="{{ $employee_detail[0]['employee_id'] }}">
                            </div>
                            
                        </div>

                        <div class="row g-9 mb-8">

                            <div class="col-md-6">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Contact No </label>
                                <input type="text" class="form-control contactInput " name="employee_contact_no" placeholder="Enter Contact No" required="required" value="{{$employee_detail[0]['employee_contactno'] }}" onkeyup="checkValidationExists(this,'contact no',2,'{{$employee_id}}')"/>
                                <span class="status"></span>
                            </div>
                                                        
                            <div class="col-md-6 fv-row">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Official Email</label>
                                <input type="email" class="form-control emailInput" name="employee_official_email"
                                placeholder="Enter Employee Official Email" required="required" value="{{$employee_detail[0]['employee_work_email'] }}" onkeyup="checkValidationExists(this,'email',2)"/>
                                <span class="status"></span>
                            </div>
 
                        </div>
                                              
                        <div class="card-header border-0 px-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Company Details</span>
                            </h3>
                        </div> 
                        
                        <div class="row g-9 mb-8">
                            <div class="col-md-4 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Company</label>
                                <select class="form-select" data-control="select2" data-hide-search="false" name="employee_company" required="required">
                                    @if (!empty($all_companies))
                                        @foreach ($all_companies as $company)
                                            <option value="{{ $company['company_id'] }}" @if($employee_detail[0]['employee_company'] == $company['company_id']) selected @endif>{{ $company['company_name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Business Unit</label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Business Unit" name="business_unit">
                                    <option value="">Select Business Unit</option>
                                    <option value="Candyland" @if ($employee_detail[0]['business_unit'] == 'Candyland') selected @endif>CandyLand</option>
                                    <option value="Bisconni" @if ($employee_detail[0]['business_unit'] == 'Bisconni') selected @endif>Bisconni</option>
                                    <option value="Snackcity" @if ($employee_detail[0]['business_unit'] == 'Snackcity') selected @endif>Snackcity</option>
                                    <option value="Hudson Pharma" @if ($employee_detail[0]['business_unit'] == 'Hudson Pharma') selected @endif>Hudson Pharma</option>
                                    <option value="Astro" @if ($employee_detail[0]['business_unit'] == 'Astro') selected @endif>Astro</option>  
                                    <option value="Act Group" @if ($employee_detail[0]['business_unit'] == 'Act Group') selected @endif>Act Group</option>  
                                    <option value="Ismail Nutrition" @if ($employee_detail[0]['business_unit'] == 'Ismail Nutrition') selected @endif>Ismail Nutrition</option>   
                                    <option value="Flour" @if ($employee_detail[0]['business_unit'] == 'Flour') selected @endif>Flour</option> 
                                    <option value="Super Cereal"@if ($employee_detail[0]['business_unit'] == 'Super Cereal') selected @endif>Super Cereal</option> 
                                    <option value="PET Resin"@if ($employee_detail[0]['business_unit'] == 'PET Resin') selected @endif>PET Resin</option>
                                    <option value="Ismail Global"@if ($employee_detail[0]['business_unit'] == 'Ismail Global') selected @endif>Ismail Global </option>        
                                    <option value="Plastiflex" @if ($employee_detail[0]['business_unit'] == 'Plastiflex') selected @endif>Plastiflex</option>  
                                    <option value="Ismail Resin" @if ($employee_detail[0]['business_unit'] == 'Ismail Resin') selected @endif>Ismail Resin</option>   
                                    <option value="Ismail Industries Limited" @if ($employee_detail[0]['business_unit'] == 'Ismail Industries Limited') selected @endif>Ismail Industries Limited</option>                       
                       
                                </select>
                            </div>
                            
                            <div class="col-md-4 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Department</label>
                                <select class="form-select" data-control="select2" data-hide-search="false" name="employee_department" required="required" id="department_select">
                                    @if (!empty($all_departments))
                                        @foreach ($all_departments as $all_department)
                                            <option value="{{ $all_department['department_id'] }}" data-depart_id="{{ $all_department['department_id'] }}" @if ($employee_detail[0]['employee_department'] == $all_department['department_id']) selected @endif>{{ $all_department['department_name'] }}</option>
                                        @endforeach
                                    @endif                               
                                </select>
                            </div>
                            
                            
                            <div class="col-md-4 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Designation</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Designation" name="employee_designation"
                                id="designss" pla required>
                                @if (!empty($employee_designation_detail))
                                    @foreach ($employee_designation_detail as $designation)
                                    <option value="{{$designation['designation_id'] }}"
                                        @if ($employee_detail[0]['employee_designation'] == $designation['designation_id'] ) selected @endif>
                                        {{ $designation['designation_name'] }}
                                    </option>
                                    @endforeach
                                @endif                                                      
                                </select>
                            </div>
    
                            <div class="col-md-4">
                                <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Employee Job Title</label>
                                
                                <input type="text" class="form-control" placeholder="Enter Job Title"
                                required name="employee_jobtitle"  value="{{ $employee_detail[0]['employee_jobtitle'] }}"/>
                            </div>
                            
                                                                                                        
                            @if($employment_period_count > 1)
                                @php $classes = 'col-md-5'; @endphp
                                @else
                                @php $classes = 'col-md-6'; @endphp
                            @endif
                            <div class={{$classes}}>
                                <label class="required fs-6 fw-semibold mb-2">Employment Status</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" name="employee_status"
                                required="required" id="employement-status">
                                    @if(!empty($employment_status))
                                        @foreach ($employment_status as $employment_stats)
                                            <option value="{{$employment_stats['employement_status_id']}}" @if ($employee_detail[0]['employee_status'] == $employment_stats['employement_status_id']) selected @endif>{{$employment_stats['employement_status_name']}}</option>
                                        @endforeach
                                
                                    @endif  
                                   
                                </select>
                            </div>                                                                         
                                                       
                            <div class="col-md-4 ">
                                <label class="required fs-6 fw-semibold mb-2">Employee Activity Status</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" name="employee_isactive"
                                required="required" id="activity_status">
                                <option value="">Select Activity Status</option>
                                <option value="1" @if ($employee_detail[0]['employee_isactive'] == 1) selected @endif>Active</option>
                                <option value="0" @if ($employee_detail[0]['employee_isactive'] == 0) selected @endif>De-Active</option>                                                                                                                                         
                                </select>
                            </div>

                        {{-- </div>
                                                
                        <div class="row  g-9 mb-8" id="append_row_date"> --}}                            
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Deactivation Type<span>
                                </label>
                                
                                <select class="form-select " data-control="select2" data-hide-search="false"  name="employee_deactive_type"
                                id="deactivate_type">
                                    <option value="">Select Employee Deactivation Type</option>
                                    <option value="1" @if($employee_detail[0]['employee_deactivation_type'] == 1) selected @endif>Resigned</option>
                                    <option value="2" @if($employee_detail[0]['employee_deactivation_type'] == 2) selected @endif>Terminated</option>    
                                </select>
                            </div>
                        </div>
                              
                        <div class="col-md-4 ">
                            {!!$qrCode!!}
                        </div>
                        
                        
                        <div class="text-center">
                            <button type="submit"class="btn btn-lg btn-success" id="submit_btn">
                                <span class="indicator-label">Update Employee</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                                                                        
                    </form>
                    {{--end::Table container--}}
                </div>
                {{--begin::Body--}}
            </div>
            {{--end::Tables Widget 11--}}
            
            
        </div>
        {{--end::Content container--}}

        
    </div>

    
</div>

@endsection



@push('js-link')
<script src="{{url('assets/js/employees/edit-employee.js')}}"></script>
<script>

    $('#edit_business_card_form').submit(function (e) {
        var previous_url = url;
        previous_url = previous_url.replace("amp;", '');
        $('#submit_btn').prop('disabled', true);
        $("#submit_btn").attr('data-kt-indicator', 'on');
        $('#submit_btn').css('cursor', 'not-allowed');
        e.preventDefault();
        $.ajax({
            url: base_url + '/employees/update_business_card',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                response = JSON.parse(JSON.stringify(data));
                if (response.status == 'TRUE') {

                    if (response.total_employee > 0) {
                        employee_name_list();

                        $('#emp_deactivate_date').val(response.deactivate_date)
                        $('#emp_deactivate_type').val(response.deactivation_type)
                        $('#emp_can_ra').val(response.employee_is_manager)
                    }
                    else {
                        Toast.fire({
                            icon: 'success',
                            title: data.msg,
                            timer: 3000,
                        })
                        setTimeout(() => {
                            window.location.href = previous_url;
                        }, 3000);

                    }

                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    $('#submit_btn').prop('disabled', false);
                    $("#submit_btn").removeAttr('data-kt-indicator');
                    $('#submit_btn').css('cursor', 'pointer');
                }
            },
            error: function (jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#submit_btn').prop('disabled', false);
                $("#submit_btn").removeAttr('data-kt-indicator');
                $('#submit_btn').css('cursor', 'pointer');
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
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
    
    var employee_isactive = '{{ $employee_detail[0]['employee_isactive'] }}';
    var employement_status = @json($employee_detail[0]['employee_status']);    
    var doj = @json($employee_detail[0]['employee_doj']);
    var url = '{{url()->previous()}}';
</script>

    
@endpush