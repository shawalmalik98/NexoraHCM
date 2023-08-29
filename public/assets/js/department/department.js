$(function () {
    $('.employeedepartmentedit').select2({
        dropdownParent: $('#edit-department')
    })

});

function deactivate_confirm(department_id) {
    $('#modal-danger').modal('show');
    $('#delete_dep_id').val(department_id)
}

$('#delete_departform').submit(function (e) {

    $('#delete-btn').prop('disabled', true);
    $('#delete-btn').attr('data-kt-indicator', 'on');
    $('#delete-btn').css('cursor', 'not-allowed');
    var department_id = $('#delete_dep_id').val()
    e.preventDefault();
    $.ajax({
        url: base_url + '/departments/discard_department/' + department_id,
        type: 'GET',
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
                $('#delete-btn').prop('disabled', false);
                $('#delete-btn').removeAttr('data-kt-indicator');
                $('#delete-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#delete-btn').prop('disabled', false);
            $('#delete-btn').removeAttr('data-kt-indicator');
            $('#delete-btn').css('cursor', 'pointer');
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

$('#adddepartment_form').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/departments/add_department',
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
                $('#submit-btn').removeAttr('data-kt-indicator');
                $('#submit-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit-btn').prop('disabled', false);
            $('#submit-btn').removeAttr('data-kt-indicator');
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

$('.edit_btn').click(function () {
    $('#edit-department').modal('show');
    var depart_id = $(this).attr('data-depart_id');
    var employee_name = $(this).attr('data-employee_name');

    changeDepartment(depart_id, employee_name)
    var depart_name = $(this).attr('data-depart_name');
    $('#depart_id').val(depart_id);
    $('#depart_name').val(depart_name);
});

$('#edit_departform').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/departments/update_department',
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
                    title: 'Department Updated Successfully.',
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
                $('#update-btn').prop('disabled', false);
                $('#update-btn').removeAttr('data-kt-indicator');
                $('#update-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#update-btn').prop('disabled', false);
            $('#update-btn').removeAttr('data-kt-indicator');
            $('#update-btn').css('cursor', 'pointer');
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

function changeDepartment(department_name, employee_name) {
    $.ajax({
        url: base_url + '/departments/department_employees',
        type: 'GET',
        data: {
            department: department_name
        },

        success: function (response) {
            $('#emp_name').empty()
            var newOption = new Option("Not Available", '', false, false);
            $('#emp_name').append(newOption).trigger('change');

            response = JSON.parse(JSON.stringify(response));
            if (response !== '') {
                for (i = 0; i < response.data.length; i++) {
                    let newOption = new Option(response.data[i]['employee_name'], response.data[i]['employee_id'], false, false);
                    $('#emp_name').append(newOption).trigger('change');
                }

                $('#emp_name').val(employee_name);
                $('#emp_name').trigger('change');

            }

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

}   