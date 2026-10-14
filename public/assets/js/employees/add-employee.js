
$('.max_length_validator_address').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$(function () {
    dataTable = $('#tableEmployee').DataTable();
    $('#searchFilter').keyup(function () {
        dataTable.search($(this).val()).draw();
    })
    $('#append_row_date').hide();
    calculateShiftDuration();
});

// $('#make_manager').click(function () {
//     reporting_authority_given = $('#make_manager').val();
//     if (reporting_authority_given) {
//         $('.checked').prop('checked', true);
//     }
// });

// $('#make_employee').click(function () {
//     reporting_authority_not_given = $('#make_employee').val();
//     if (reporting_authority_not_given) {
//         $('.checked').prop('checked', false);
//     }
// });

$('#manager_rights').change(function(){
    var reporting_access = $(this).val();
    if(reporting_access == 1){
        $('.checked').prop('checked', true);
        $('#manager_right_div').removeClass('col-md-6').addClass('col-md-6');
        $('#attendance_access_div').removeClass('d-none');
        // $('#attendance_access').attr('required','required');
    }else{
        $('#manager_right_div').removeClass('col-md-6').addClass('col-md-6');
        $('.checked').prop('checked', false);
        $('#attendance_access_div').addClass('d-none');
        // $('#attendance_access').removeAttr('required','required');
    }
});

$('#activity_status').change(function () {
    var selectedOption = $(this).val();
    if (selectedOption == '1') {
        $('#append_row_date').hide("");
        $('#deactivate_date').removeAttr('required');
        $('#deactivate_type').removeAttr('required');
        $('#deactivate_date').val('');
        $('#deactivate_type').find('.select2-selection__rendered').html('');
        $('#deactivate_type').val('');
        $('#main-div-history-status').find('.select2-search__field').attr('placeholder', 'Select Employees');

    } else {
        $('#append_row_date').show("");
        $('#deactivate_date').attr('required', 'required');
        $('#deactivate_type').attr('required', 'required');

    }
});


function candidate_name_list() {

    $.ajax({
        url: base_url + '/employees/check-candidate-name-list',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        // the controller only filters on name + department, so don't send the whole form
        // (121+ fields) in the query string - that exceeds the URL limit (HTTP 414)
        data: {
            employee_name: $('#add_employee_form [name="employee_name"]').val(),
            employee_department: $('#add_employee_form [name="employee_department"]').val()
        },

        success: function (data) {
            if (data.status == 'TRUE') {
                candidate_record = data.data;
                employeeOnBoardingArray = [];
                $.each(candidate_record, function (key, candidate) {
                    if (candidate) {
                        employeeOnBoardingArray.push(employeeOnBoarding(key, candidate));
                        $('.onboarding-employees').html(employeeOnBoardingArray);
                    }
                });
                $('#check-candidate-name').modal({ backdrop: 'static', keyboard: false }, 'show');
                $('#check-candidate-name').modal('show', { backdrop: 'static', keyboard: false });
                $("#submit_btn").removeAttr('data-kt-indicator');
                $('#submit_btn').css('cursor', 'pointer');
            } else {

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
}


$('#update_onboarding_employee_status').submit(function (e) {

    $('#submit_btnn1').prop('disabled', true);
    $("#submit_btnn1").attr('data-kt-indicator', 'on');
    $('#submit_btnn1').css('cursor', 'not-allowed');
    $('#submit_btnn2').prop('disabled', true);
    $("#submit_btnn2").attr('data-kt-indicator', 'on');
    $('#submit_btnn2').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/employees/update-onboarding-employee-status',
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
                $('#check-candidate-name').modal('hide');
                var employee_detail = data.employee_data;
                var result = `
                        <div class="row">
                            <textarea class="form-control text-right" id="employee_infomcation_copier" readonly style="height: 135px;">Code : ${employee_detail[0].employee_code}\nName : ${employee_detail[0].employee_name}\nPassword : ${data.emp_password}\nDepartment : ${employee_detail[0].department_name}\nDesignation  : ${employee_detail[0].designation_name}</textarea>  
                        </div>`;
                $('#employee-registered').html(result);
                $('#employee-registered-successfuly').modal({ backdrop: 'static', keyboard: false }, 'show');
                $('#employee-registered-successfuly').modal('show', { backdrop: 'static', keyboard: false });

            } else {
                $('#submit_btnn1').prop('disabled', false);
                $("#submit_btnn1").removeAttr('data-kt-indicator');
                $('#submit_btnn1').css('cursor', 'pointer');
                $('#submit_btnn2').prop('disabled', false);
                $("#submit_btnn2").removeAttr('data-kt-indicator');
                $('#submit_btnn2').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
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

$('#add_employee_form').submit(function (e) {

    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/employees/create_employee',
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

                if (data.total_candidate > 0) {
                    candidate_name_list();
                } else {
                    var employee_detail = data.employee_data;
                    var result = `
                                <div class="row ">
                                     <textarea class="form-control text-right" id="employee_infomcation_copier" readonly style="height: 135px;">Code : ${employee_detail[0].employee_code}\nName : ${employee_detail[0].employee_name}\nPassword : ${data.emp_password}\nDepartment : ${employee_detail[0].department_name}\nDesignation  : ${employee_detail[0].designation_name}</textarea>
                                </div>`;

                    $('#employee-registered').html(result);
                    $('#employee-registered-successfuly').modal({ backdrop: 'static', keyboard: false }, 'show');
                    $('#employee-registered-successfuly').modal('show', { backdrop: 'static', keyboard: false });


                }


            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 8000,
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



$('#department_select').change(function (e) {
    e.preventDefault();
    var department = $(this).find(':selected').attr('data-depart_id');
    $('#designss').html('');
    $.ajax({
        url: base_url + '/employees/get_desinations/' + department,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            department: department
        },
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            $('#designss').append(data);
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
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

function employeeOnBoarding(key = Null, data = Null) {
    var interview_current_salary;
    var interview_expected_salary;

    if (data.interview_current_salary == null) {
        interview_current_salary = 'N/A';
    } else {
        interview_current_salary = data.interview_current_salary
    }

    if (data.interview_expected_salary == null) {
        interview_expected_salary = 'N/A';
    } else {
        interview_expected_salary = data.interview_expected_salary
    }


    var record = '';
    record += `<div class="py-1">                        
            <div class="py-3 d-flex flex-stack flex-wrap">
                <div class="d-flex align-items-center toggle collapsed" data-bs-toggle="collapse" data-bs-target="#kt_create_new_payment_method_${key}">
                    <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2">
                        <span class="svg-icon toggle-on svg-icon-primary svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                <rect x="6.0104" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                            </svg>
                       </span>
                        <span class="svg-icon toggle-off svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor" />
                                <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                            </svg>
                       </span>
                    </div>
                    <div class="me-3">
                        <div class="d-flex align-items-center fw-bold">${data.candidate_name} 
                        <div class="badge badge-light-primary ms-5">${data.department_name}</div></div>
                        <div class="text-muted">Last Interview Date: ${data.interview_date} - ${data.interview_time}</div>
                    </div>
                </div>
                <div class="d-flex my-3 ms-9">
                    <label class="form-check form-check-custom me-5 form-check-solid">
                        <input class="form-check-input" type="radio" id="candidate_id" required name="onboarding_id" value="${data.onboarding_id}" />
                    </label>
                </div>
            </div>
        
            <div id="kt_create_new_payment_method_${key}" class="collapse show fs-6 ps-10">
                
                <div class="d-flex flex-wrap py-5">
                
                    <div class="flex-equal me-5">
                        <table class="table table-flush fw-semibold gy-1">
                            <tr>
                                <td class="text-muted">1st Interviewer Name</td>
                                <td class="text-gray-800 ps-1">${data.interviewer_name1} </td>
                            </tr>
                            <tr>
                                <td class="text-muted">2nd Interviewer Name</td>
                                <td class="text-gray-800 ps-1">${(data.interviewer_name2) ?? 'N/A'} </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Interview Created By</td>
                                <td class="text-gray-800 ps-1">${data.interviewer_created_by}</td>
                            </tr>
                           
                        </table>
                    </div>
                    
                    <div class="flex-equal">
                        <table class="table table-flush fw-semibold gy-1">
                            <tr>
                                <td class="text-muted">Interview Job Title </td>
                                <td class="text-gray-800 ps-1">${data.interview_jobtitle} </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Current Salary </td>
                                <td class="text-gray-800 ps-1">
                                    ${interview_current_salary}
                                 </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Expected Salary </td>
                                <td class="text-gray-800 ps-1">${interview_expected_salary} </td>
                            </tr>
                           
                           
                        </table>
                    </div>
                </div>
            
            </div>
        
        </div>
        <div class="separator separator-dashed"></div>
        `;

    return record;
}

function proceed_btn() {
    $('#candidate_id').prop('required', false);
}



function Copy() {

    $('#copy_btn').prop('disabled', true);
    $("#copy_btn").attr('data-kt-indicator', 'on');
    $('#copy_btn').css('cursor', 'not-allowed');

    // var employee_details = document.getElementById('employee_infomcation_copier');
    var employee_details = $("#employee_infomcation_copier");

    // employee_details.select()
    // document.execCommand('copy')
    // navigator.clipboard.writeText(employee_details.value)
        // .then(function () {
            window.location.href = base_url + '/employees';
        // })
        // .catch(function (error) {
        //     console.error('Unable to copy text to clipboard:', error);
        // });

}

$('.gross_salary').keyup(function(){
    var gross_salary = $(this).val();
    var basic_salary = (gross_salary / 100) * gross_salary_ratio;
    $('#basic_salary').val(basic_salary.toFixed(2));
});

