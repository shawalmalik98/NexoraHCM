$(function () {

    $('[data-bs-toggle="tooltip"]').tooltip();

    // the manager dropdowns are employee search selects (general_script.js), already bound to their modal

});

$('.manager_button').click(function () {

    var employee_id = $(this).attr('data-employee_id');

    var employee_name_sm = $(this).attr('data-employee_name_sm');
    var senior_manager = $(this).attr('data-employee_sm');

    var employee_name_jm = $(this).attr('data-employee_name_jm');
    var junior_manager = $(this).attr('data-employee_jm');


    $('#employee_name_sm').val(employee_name_sm);
    $('#senior_manager').val(senior_manager).trigger('change');
    $('#employee_id_sm').val(employee_id);


    $('#employee_name_jm').val(employee_name_jm);
    $('#junior_manager').val(junior_manager).trigger('change');
    $('#employee_id_jm').val(employee_id);
});


$('#changemanager').submit(function (e) {
    $('#submit-btn2').prop('disabled', true);
    $("#submit-btn2").attr('data-kt-indicator', 'on');
    $('#submit-btn2').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/employees-managers/seniormanager',
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
                    timer: 3000,
                })
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 8000,
                })
                $('#submit-btn2').prop('disabled', false);
                $("#submit-btn2").removeAttr('data-kt-indicator');
                $('#submit-btn2').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit-btn2').prop('disabled', false);
            $("#submit-btn2").removeAttr('data-kt-indicator');
            $('#submit-btn2').css('cursor', 'pointer');
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

$('#employeesmanager').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $("#submit-btn").attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/employees-managers/employeemanager',
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
                    timer: 3000,
                })
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 8000,
                })
                $('#submit-btn').prop('disabled', false);
                $("#submit-btn").removeAttr('data-kt-indicator');
                $('#submit-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit-btn').prop('disabled', false);
            $("#submit-btn").removeAttr('data-kt-indicator');
            $('#submit-btn').css('cursor', 'pointer');
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

$('#employee_all').attr('disabled', true);
$('#employee_all').click(function () {

    var checkbox = $('#employee_all').prop('checked');

    if (checkbox) {
        $('#employees option').prop('selected', true);
        $('#selectedemployee').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-employees-container-choice-5nle-Human Resource">All Employees</span>');
        $('#selectedemployee').find('.select2-search__field').attr('placeholder', '');
    } else {
        $('#employees option').prop('selected', false);
        $('#selectedemployee').find('.select2-selection__rendered').html('');
        $('#selectedemployee').find('.select2-search__field').attr('placeholder', 'Select Employee');
        $('#selectedemployee').find('.select2-search__field').css('width', '100%');

    }

});

$('#department_all').click(function () {

    var checkbox = $('#department_all').prop('checked');

    if (checkbox) {
        $('#departments_all option').prop('selected', true);
        $('#selectedepartment').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
        $('#selectedepartment').find('.select2-search__field').attr('placeholder', '');
        $('#employee_all').prop('checked', false);
        $('#employee_all').attr('disabled', false);

    } else {
        $('#employee_all').attr('disabled', true);
        $('#departments_all option').prop('selected', false);
        $('#selectedepartment').find('.select2-selection__rendered').html('');
        $('#selectedepartment').find('.select2-search__field').attr('placeholder', 'Select Department');
        $('#selectedepartment').find('.select2-search__field').css('width', '100%');

    }

});

$('#departments_all').change(function () {

    var departments = $('#departments_all').val();
    var all_length = count_val;

    if (departments.length == all_length) {
        $('#department_all').prop('checked', true);
    } else {
        $('#department_all').prop('checked', false);

    }

});


$('.multi-depart').change(function (e) {
    e.preventDefault();
    $('#employees').html('');
    var dept = $('.multi-depart').val();

    if (dept.length > 0) {
        $('#employee_all').attr('disabled', false);

    } else {
        $('#employee_all').prop('checked', false);
        $('#employee_all').attr('disabled', true);

    }
    $.ajax({
        url: base_url + '/get-employee-department',
        type: 'GET',

        data: {
            department: dept
        },

        success: function (response) {
            var response = response.toString();
            var response = response.replace('<option value="">Select Employee</option>', '');
            $('#employees').append(response);

        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                alert("Internet Connection Problem");
            } else {
                alert("Try Again. Error Code " + errorStatus);
            }

        }
    });
})

$("#select_manager").change(function () {

    var select_manager = $(this).val();

    if (select_manager == "Senior") {
        $('#selectjuniormanager').val('').trigger('change');
        $('#employees_senior_manager').removeClass('d-none');
        $('#employees_junior_manager').addClass('d-none');
        $('#selectseniormanager').prop('required', true);
        $('#selectjuniormanager').prop('required', false);

    } else {
        $('#selectseniormanager').val('').trigger('change');
        $('#employees_junior_manager').removeClass('d-none');
        $('#employees_senior_manager').addClass('d-none');
        $('#selectjuniormanager').prop('required', false);
        $('#selectseniormanager').prop('required', false);

    }

});