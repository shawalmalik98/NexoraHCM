$('.max_length_validator_address').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$(function () {
    // console.log('1231231231');
    dataTable = $('#tableEmployee').DataTable();
    $('#searchFilter').keyup(function () {
        dataTable.search($(this).val()).draw();
    })
    $('[data-toggle="tooltip"]').tooltip();
    var Employee_status = employee_isactive;
    if (Employee_status == '1') {
        $('#append_row_date').addClass("d-none");
        $('#deactivate_date').removeAttr('required');
    } else {
        $('#append_row_date').removeClass("d-none");
        $('#deactivate_date').attr('required', 'required');
    }
    $('.gross_salary').keyup();
    

});

$('.gross_salary').keyup(function(){
    console.log('1231231231');
    var gross_salary = $(this).val();
    var basic_salary = (gross_salary / 100) * gross_salary_ratio;
    $('#basic_salary').val(basic_salary.toFixed(2));
});

// $('#customRadio5').click(function () {
//     reporting_authority_given = $('#customRadio5').val();
//     if (reporting_authority_given) {
//         $('.checked').prop('checked', true);
//     }
// });

// $('#customRadio6').click(function () {
//     reporting_authority_not_given = $('#customRadio6').val();
//     if (reporting_authority_not_given) {
//         $('.checked').prop('checked', false);
//     }
// });

$('#manager_rights').change(function(){
    var reporting_access = $(this).val();
    if(reporting_access == 1){
        $('.checked').prop('checked', true);
        $('#manager_right_div').removeClass('col-md-6').addClass('col-md-3');
        $('#attendance_access_div').removeClass('d-none');
        $('#attendance_access').attr('required','required');
    }else{
        $('#manager_right_div').removeClass('col-md-3').addClass('col-md-6');
        $('.checked').prop('checked', false);
        $('#attendance_access_div').addClass('d-none');
        $('#attendance_access').removeAttr('required','required');
    }
});

$('#activity_status').change(function () {
    var selectedOption = $(this).val();
    if (selectedOption == '1') {
        $('#append_row_date').addClass("d-none");
        $('#deactivate_date').removeAttr('required');
        $('#deactivate_date').attr('max', '');
        $('#deactivate_type').removeAttr('required');


    } else {
        $('#append_row_date').removeClass("d-none");
        $('#deactivate_date').attr('required', 'required');
        $('#deactivate_date').attr('max');
        $('#deactivate_type').attr('required', 'required');
    }
});

$('#employement-status').change(function () {
    previous_status = employement_status;
    previous_date = efffective_date;
    var selectedOption = $(this).val();

    if (selectedOption == previous_status) {

        $('#employment_status_date').val(previous_date)
        $('#employment_status_date').attr('required', 'required');
        $('#append_row_date_emp_status').addClass("d-none");
    } else {
        $('#employment_status_date').val('');
        $('#append_row_date_emp_status').removeClass("d-none");

        $('#employment_status_date').attr('required', 'required');

    }


});

$('#employee_doj').change(function () {
    var employee_doj = doj;
    var change_doj = $('#employee_doj').val();
    if (employee_doj != change_doj) {
        $('#append_row_date_emp_status').addClass("d-none");
    }

});

$('#edit_employee_form').submit(function (e) {
    var previous_url = url;
    previous_url = previous_url.replace("amp;", '');
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/employees/update_employee',
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

$('#edit_employee_salary').submit(function (e) {
    $('#submit_btn_salary').prop('disabled', true);
    $("#submit_btn_salary").attr('data-kt-indicator', 'on');
    $('#submit_btn_salary').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/employees/submit_edit_salary_request',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.status == 'TRUE') {
                Toast.fire({
                    icon: 'success',
                    title: data.msg,
                    timer: 5000,
                })
                setTimeout(() => {
                    location.reload();
                }, 5000);
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })
                $('#submit_btn_salary').prop('disabled', false);
                $('#submit_btn_salary').css('cursor', 'pointer');
                $("#submit_btn_salary").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn_salary').prop('disabled', false);
            $('#submit_btn_salary').css('cursor', 'pointer');
            $("#submit_btn_salary").removeAttr('data-kt-indicator');
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

function employee_name_list() {

    $.ajax({
        url: base_url + "/employees/check-employee-name-list",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $("#edit_employee_form").serialize(),

        success: function (data) {
            if (data.status == 'TRUE') {
                $('#employee-data').html('');
                $('#employee-manager').html('');
                employee_record = data.reporting_employee;
                employee_record_can_ra = employee_is_manager(data.employee_is_manager);
                employeeArray = [];
                $.each(employee_record, function (key, employee) {
                    if (employee) {
                        employeeArray.push(employee_reporting_authorties(key, employee));
                        $('#employee-data').html(employeeArray);
                        $('.employee_reporting_manager_select2').select2({
                            dropdownParent: $('#check-employee-name')
                        });
                    }
                });

                $('#employee-manager').html(parent_employee_can_ra(data.employee_is_manager));
                $('#parent_employee_reporting_manager').select2({
                    dropdownParent: $('#check-employee-name')
                });

                $('#parent_employee_reporting_manager').change(function () {
                    var selectedOption = $(this).val();
                    $('.employee_reporting_manager_select2').val(selectedOption).change();
                });

                $('#old_reporting').val(data.old_reporting_manager);

                $('#check-employee-name').modal({ backdrop: 'static', keyboard: false }, 'show');
                $('#check-employee-name').modal('show');



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

function employee_reporting_authorties(key = Null, data = Null) {
    var record = '';
    record += `<tr>
            <td class="ps-7"><b>${data.employee_name}</b><input type="hidden" value="${data.employee_id}" name="employees[]" /></td>
            <td>${data.department_name}</td>
            <td>${data.designation_name}</td>
            <td>${employee_record_can_ra}</td>
           
        </tr>`;

    return record;
}

function parent_employee_can_ra(employee_record_can_ra) {


    var can_ra = '';
    can_ra += `<div class="col-md-12 ">
        <select class="form-select" data-control="select2" id="parent_employee_reporting_manager"  data-hide-search="true">
            <option value="">Select Reporting Manager</option>`;
    $.each(employee_record_can_ra, function (key, data) {
        can_ra += `<option value="${data.employee_id}">${data.employee_code}-${data.employee_name} (${data.department_name}- ${data.designation_name})</option>`;
    });

    can_ra += `</select>
    </div>`;

    return can_ra;
}

function employee_is_manager(employee_record_can_ra) {


    var can_ra = '';
    can_ra += `<div class="col-md-12 ">
         <select class="form-select employee_reporting_manager_select2" data-control="select2"  data-hide-search="true" name="employee_reporting_manager[]" required>
             <option value="">Select Reporting Manager</option>`;
    $.each(employee_record_can_ra, function (key, data) {
        can_ra += `<option value="${data.employee_id}">${data.employee_code}-${data.employee_name} (${data.department_name}- ${data.designation_name})</option>`;
    });

    can_ra += `</select>
     </div>`;

    return can_ra;
}

$('#update_employee_can_ra').submit(function (e) {
    $('#employees_reporting_submit_btn').prop('disabled', true);
    $("#employees_reporting_submit_btn").attr('data-kt-indicator', 'on');
    $('#employees_reporting_submit_btn').css('cursor', 'not-allowed');

    e.preventDefault();
    $.ajax({
        url: base_url + '/employees/update-employee-can-ra',
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
                    window.location.href = base_url + '/employees';
                }, 3000);


            } else {

                $('#employees_reporting_submit_btn').prop('disabled', false);
                $("#employees_reporting_submit_btn").removeAttr('data-kt-indicator');
                $('#employees_reporting_submit_btn').css('cursor', 'pointer');

            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#employees_reporting_submit_btn').prop('disabled', false);
            $("#employees_reporting_submit_btn").removeAttr('data-kt-indicator');
            $('#employees_reporting_submit_btn').css('cursor', 'pointer');

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


$('.close_btn').click(function () {
    $('#submit_btn').prop('disabled', false);
    $("#submit_btn").removeAttr('data-kt-indicator');
    $('#submit_btn').css('cursor', 'pointer');
});


