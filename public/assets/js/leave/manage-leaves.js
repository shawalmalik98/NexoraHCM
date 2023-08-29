function limit(element) {
    var max_chars = 30;

    if (element.value.length > max_chars) {
        element.value = element.value.substr(0, max_chars);
    }

}
$('#addleave_form').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();

    swal.fire({
        title: 'Confirmation Alert !',
        text: 'This leave cant be delete if any employee avail / apply. Are you sure to create ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
    }).then(function (value) {

        if (value.isConfirmed == true) {
            $.ajax({
                url: base_url + '/leave-management/add-leave',
                type: 'POST',
                data: $('#addleave_form').serialize(),
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
        } else {
            location.reload();
        }
    });

});

function deactivate_confirm(leave_id) {
    $('#modal-danger').modal('show');
    $('#delete_leave_id').val(leave_id)
}

$('#delete_leaveform').submit(function (e) {

    $('#delete-btn').prop('disabled', true);
    $('#delete-btn').attr('data-kt-indicator', 'on');
    $('#delete-btn').css('cursor', 'not-allowed');
    var leave_id = $('#delete_leave_id').val()
    e.preventDefault();
    $.ajax({
        url: base_url + '/leave-management/discard_leave/' + leave_id,
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

$('.edit_btn').click(function () {

    $('#edit-leave').modal('show');
    var leave_id = $(this).attr('data-leave_id');
    var leave_name = $(this).attr('data-leave_name');
    var leave_visibility = $(this).attr('data-leave_visibility');
    var leave_request_count = $(this).attr('data-leave-request-count');
    var leave_att_count = $(this).attr('data-att-request-count');

    $('#edit_leave_id').val(leave_id);
    $('#edit_leave_name').val(leave_name);

    if (leave_visibility == 1) {
        $('#edit_leave_visibility').prop('checked', true);
    } else {
        $('#edit_leave_visibility').prop('checked', false);
    }
    $('#edit_leave_visibility').val(leave_visibility);
});

$('#edit_leaveform').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/leave-management/update-leave-management',
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
