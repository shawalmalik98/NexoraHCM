@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div class="app-container col-12 d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Add Leave Policy</h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
            </div>
        </div>
    </div>
    
    <div class="app-content flex-column-fluid">
        <div class="app-container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body py-3 ">
                    <form  id="add_employee_form" class="form" autocomplete="off">
                        @csrf
                        <div class="row g-9 mb-8 mt-2">
                            
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Policy Name</span>
                                </label>
                                <input type="text" class="form-control " placeholder="Enter Policy Name" name="employee_name" required />
                            </div>
                            
                            
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Leave Type</span>
                                </label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Carry Forward" name="employee_gender"
                                required="required">
                                    <option value="">Select Encashment Status</option>
                                    @if(!empty($manage_leaves))
                                        @foreach ($manage_leaves as $manage_leave)
                                            <option value="{{$manage_leave['leave_name']}}">{{$manage_leave['leave_name']}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Employee Group</span>
                                </label>
                                <input type="text" class="form-control " name="employee_contact_no"
                                placeholder="Enter Contact No" required="required" />
                            </div>
                            
                        </div>
                    
                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Leave Apply Limit (Per Month)<span>
                                </label>
                                
                                <input type="email" class="form-control " name="employee_personal_email"
                                placeholder="Enter Employee Email" required="required"/>
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Balance Required to Apply<span>
                                </label>
                                
                                <input type="email" class="form-control " name="employee_official_email"
                                placeholder="Enter Employee Official Email" required="required"/>
                            </div>
 
                        </div>

                        <div class="row g-9 mb-8">

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Eligibility</span>
                                </label>
                                <input type="text" class="form-control " name="employee_cnic" placeholder="Enter NIC No" required="required" />
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Eligibility From<span>
                                </label>
                                
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Employee Gender" name="employee_gender"
                                required="required">
                                    <option value="">Select Eligibility Date</option>
                                    <option value="Male">Date of Joining</option>
                                    <option value="Female">Date of Confirmation</option>
                                </select>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">From Days</span>
                                </label>
                                <input type="text" class="form-control " name="employee_cnic" placeholder="Enter NIC No" required="required" />
                            </div>
                            
                        </div>
                        <div class="row  g-9 mb-8">

                            <div class="col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Carry Forward</span></label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Carry Forward" name="employee_gender"
                                required="required">
                                    <option value="">Select Eligibility Date</option>
                                    <option value="Male">Date of Joining</option>
                                    <option value="Female">Date of Confirmation</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Carry Forward Limit</span></label>
                                <input type="text" class="form-control " name="employee_cnic" placeholder="Carry Forward Limit" required="required" />
                            </div>

                        </div>

                        <div class="row  g-9 mb-8">

                            <div class="col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Encashment</span></label>
                                <select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Carry Forward" name="employee_gender"
                                required="required">
                                    <option value="">Select Encashment Status</option>
                                    <option value="Male">Yes</option>
                                    <option value="Female">No</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Encashment Limit</span></label>
                                <input type="text" class="form-control " name="employee_cnic" placeholder="Encashment Limit" required="required" />
                            </div>

                        </div>
                    
                        <div class="text-center">
                            <button type="submit"class="btn btn-lg btn-success" id="submit_btn">
                                <span class="indicator-label">Add Leave Policy</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                               </span>
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
    {{--end::Content--}}
</div>

@endsection



@push('js-link')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>

<script>

    $('.max_length_validator_address').maxlength({
        threshold: 500,
        warningClass: "badge badge-primary",
        limitReachedClass: "badge badge-success"
    });

    $(function(){
		dataTable = $('#tableEmployee').DataTable();
		$('#searchFilter').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
        $('#append_row_date').hide();
	});

    $('#make_manager').click(function(){
        reporting_authority_given =  $('#make_manager').val();
        if(reporting_authority_given){
            $('.checked').prop('checked',true);
        }
    });
   
    $('#make_employee').click(function(){
        reporting_authority_not_given =  $('#make_employee').val();
        if(reporting_authority_not_given){
            $('.checked').prop('checked',false);
        }
    });
    
    $('#activity_status').change(function(){
        var selectedOption = $(this).val();
        if(selectedOption == '1'){
            $('#append_row_date').hide("");
            $('#deactivate_date').removeAttr('required');
            $('#deactivate_type').removeAttr('required');
            $('#deactivate_date').val('');
            $('#deactivate_type').find('.select2-selection__rendered').html('');
            $('#deactivate_type').val('');
            $('#main-div-history-status').find('.select2-search__field').attr('placeholder', 'Select Employees');
           
        }else{
            $('#append_row_date').show("");
            $('#deactivate_date').attr('required','required');
            $('#deactivate_type').attr('required','required');
            
        }
    });


    function candidate_name_list(){
        
        $.ajax({
            url: "{{ url('employees/check-candidate-name-list') }}",
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:$("#add_employee_form").serialize(),
           
            success: function(data) {
                if (data.status == 'TRUE') {
                    candidate_record = data.data;
                    employeeOnBoardingArray = [];
                    $.each(candidate_record, function(key, candidate) {
                        if(candidate){
                            employeeOnBoardingArray.push(employeeOnBoarding(key,candidate));
                            $('.onboarding-employees').html(employeeOnBoardingArray);
                        }
                    });
                    $('#check-candidate-name').modal({backdrop: 'static', keyboard: false},'show');
                    $('#check-candidate-name').modal('show',{backdrop: 'static', keyboard: false});
                    $("#submit_btn").removeAttr('data-kt-indicator');
                    $('#submit_btn').css('cursor', 'pointer');
                } else {
                   
                    $('#submit_btn').prop('disabled', false);
                    $("#submit_btn").removeAttr('data-kt-indicator');
                    $('#submit_btn').css('cursor', 'pointer');
                }
            },
            error: function(jqXHR, textStatus) {
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
                    if(errorStatus == 419){
                        location.reload();
                    }else{
                        Toast.fire({
                            icon: 'warning',
                            title: 'Try Again. Error Code ' + errorStatus,
                            timer: 3000,
                        })
                    }
                }
            }
        });
    }

   
    $('#update_onboarding_employee_status').submit(function(e) {
  
        $('#submit_btnn1').prop('disabled', true);
        $("#submit_btnn1").attr('data-kt-indicator', 'on');
        $('#submit_btnn1').css('cursor', 'not-allowed');
        $('#submit_btnn2').prop('disabled', true);
        $("#submit_btnn2").attr('data-kt-indicator', 'on');
        $('#submit_btnn2').css('cursor', 'not-allowed');
        e.preventDefault();
        $.ajax({
            url: '{{ url('employees/update-onboarding-employee-status') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == 'TRUE') {
                    $('#check-candidate-name').modal('hide');
                    var employee_detail = data.employee_data;
                        var result =`
                        <div class="row">
                            <textarea class="form-control text-right" id="employee_infomcation_copier" readonly style="height: 135px;">Code : ${ employee_detail[0].employee_code}\nName : ${ employee_detail[0].employee_name}\nPassword : ${ data.emp_password}\nDepartment : ${ employee_detail[0].department_name}\nDesignation  : ${ employee_detail[0].designation_name}</textarea>  
                        </div>`; 
                        $('#employee-registered').html(result);
                        $('#employee-registered-successfuly').modal({backdrop: 'static', keyboard: false},'show');
                        $('#employee-registered-successfuly').modal('show',{backdrop: 'static', keyboard: false});
                      
                } else {
                    $('#submit_btnn1').prop('disabled', false);
                    $("#submit_btnn1").removeAttr('data-kt-indicator');
                    $('#submit_btnn1').css('cursor', 'pointer');
                    $('#submit_btnn2').prop('disabled', false);
                    $("#submit_btnn2").removeAttr('data-kt-indicator');
                    $('#submit_btnn2').css('cursor', 'pointer');
                }
            },
            error: function(jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#submit_btnn1').prop('disabled', false);
                $("#submit_btnn1").removeAttr('data-kt-indicator');
                $('#submit_btnn1').css('cursor', 'pointer');
                $('#submit_btnn2').prop('disabled', false);
                $("#submit_btnn2").removeAttr('data-kt-indicator');
                $('#submit_btnn2').css('cursor', 'pointer');
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
                } else {
                    if(errorStatus == 419){
                        location.reload();
                    }else{
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