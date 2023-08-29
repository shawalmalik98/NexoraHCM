$(function () {

    dataTable = $('#tableEmployeeOverTime').DataTable({
        "ordering": false,
        aLengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
        ],
        iDisplayLength: 50,
    });
});

function overtime(emp_id, extra_hour, date, salary_amount) {

    $('.submit_btn').prop('disabled', true);
    $(".submit_btn").attr('data-kt-indicator', 'on');
    $('.submit_btn').css('cursor', 'not-allowed');

    swal.fire({
        title: 'Confirmation Alert !',
        text: 'By approving this overtime, it will be automatically reimbursed in payroll. Are you sure to submit Overtime ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
    }).then(function (value) {

        if (value.isConfirmed == true) {
            $.ajax({
                url: base_url + '/overtime-management/overtime-record-insert',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { employee_id: emp_id, extra_hour: extra_hour, date: date, salary_amount: salary_amount },
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
                            timer: 5000,
                        })
                        $('.submit_btn').prop('disabled', false);
                        $(".submit_btn").removeAttr('data-kt-indicator');
                        $('.submit_btn').css('cursor', 'pointer');
                    }
                },
                error: function (jqXHR, textStatus) {
                    var errorStatus = jqXHR.status;
                    $('.submit_btn').prop('disabled', false);
                    $(".submit_btn").removeAttr('data-kt-indicator');
                    $('.submit_btn').css('cursor', 'pointer');
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
        } else {
            $('.submit_btn').prop('disabled', false);
            $(".submit_btn").removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');
        }
    });
}

function remove_overtime_salary(emp_id, date) {
    $('#modal-danger').modal('show');
    $('#emp_id').val(emp_id)
    $('#date').val(date)

}

$('.edit_btn').click(function () {
    $('#edit-overtime-salary').modal('show');
    var salary = $(this).attr('data-salary');
    var date = $(this).attr('data-date');
    var emp_id = $(this).attr('data-id');
    var extra_hours = $(this).attr('data-extra-hours');
    var amount_payable = $(this).attr('data-amount-payable');
    $('#employee_id').val(emp_id);
    $('#salary_amount').val(salary);
    $('#ovt_date').val(date);
    $('#extra_hours').val(extra_hours);
    $('#amount_payable').val(amount_payable);

});

var numberOfChildCheckBoxes = $('.checked').length;
$('.checked').on('change', function () {

    var checkedChildCheckBox = $('.checked:checked').length;

    if (checkedChildCheckBox <= 0) {
        $('.on-check').addClass('d-none');
    } else {
        $('.on-check').removeClass('d-none');
    }
    if (checkedChildCheckBox == numberOfChildCheckBoxes) {

        $('#parent_checkbox').prop('checked', true)
    }
    else {

        $('#parent_checkbox').prop('checked', false);
    }

});


$('#parent_checkbox').on('change', function () {

    var checkedParentCheckBox = $('#parent_checkbox:checked').length;

    if (checkedParentCheckBox >= 1) {
        $('.checked').prop('checked', true)
        $('.on-check').removeClass('d-none');

    } else {

        $('#parent_checkbox').prop('checked', false);
        $('.checked').prop('checked', false);
        $('.on-check').addClass('d-none');
    }


});

$('.overtime_reibursment').on('click', function (e) {

    var title = $(this).attr('data-value');
    $('#title').val(title);

    swal.fire({
        title: 'Confirmation Alert !',
        text: 'By approving this overtime, it will be automatically reimbursed in payroll. Are you sure to submit Overtime ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
    }).then(function (value) {

        if (value.isConfirmed == true) {
            $.ajax({
                url: base_url + '/overtime-management/multiple-overtime-record-insert-or-remove',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $("#overtime-insert-form").serialize(),
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
                            timer: 5000,
                        })

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
        } else {
            $('.checked').prop('checked', false);
            $('#parent_checkbox').prop('checked', false);
            $('.on-check').addClass('d-none');
        }
    });

});

$('#removeReimburesement').submit(function (e) {
    e.preventDefault();
    $('#delete-btn').prop('disabled', true);
    $('#delete-btn').attr('data-kt-indicator', 'on');
    $('#delete-btn').css('cursor', 'not-allowed');

    $.ajax({
        url: base_url + '/overtime-management/delete-overtime-record-insert',
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

$('#edit_overtime_salary').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/overtime-management/edit-overtime-record',
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
