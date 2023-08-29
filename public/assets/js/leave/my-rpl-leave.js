$('#rpl_leave_worked').change(function () {
    var from_selected_date = $(this).val();
    if (from_selected_date != '') {
        $('#rpl_leave_required').prop('disabled', false);
        $('#rpl_leave_required').attr('min', from_selected_date);
        $('#rpl_leave_required').attr('max', get_one_month_future_date(from_selected_date));
    } else {
        $('#rpl_leave_required').prop('disabled', true);
        $('#rpl_leave_required').removeAttr('min');
        $('#rpl_leave_required').val('');
    }
});

$('#edit_rpl_leave_worked').change(function () {
    var from_selected_date = $(this).val();
    if (from_selected_date != '') {
        $('#edit_rpl_leave_required').prop('disabled', false);
        $('#edit_rpl_leave_required').attr('min', from_selected_date);
        $('#edit_rpl_leave_required').attr('max', get_one_month_future_date(from_selected_date));
    } else {
        $('#edit_rpl_leave_required').prop('disabled', true);
        $('#edit_rpl_leave_required').removeAttr('min');
        $('#edit_rpl_leave_required').val('');
    }
});

$('#apply_for_leave_rpl_form').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/apply-rpl-leave',
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
                $('#submit_btn').prop('disabled', false);
                $('#submit_btn').css('cursor', 'pointer');
                $("#submit_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn').prop('disabled', false);
            $('#submit_btn').css('cursor', 'pointer');
            $("#submit_btn").removeAttr('data-kt-indicator');
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

$('.message_btn').click(function () {
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});

$('#delete_leave_request_rpl').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/delete-rpl-leave-request',
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
                if (data.autoreload == "TRUE") {

                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })

                $('#delete_submit_btn').prop('disabled', false);
                $('#delete_submit_btn').css('cursor', 'pointer');
                $("#delete_submit_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#delete_submit_btn').prop('disabled', false);
            $('#delete_submit_btn').css('cursor', 'pointer');
            $("#delete_submit_btn").removeAttr('data-kt-indicator');
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

$('#edit_applyrplforleave_form').submit(function (e) {
    $('#edit_submit_btn').prop('disabled', true);
    $("#edit_submit_btn").attr('data-kt-indicator', 'on');
    $('#edit_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/update-rpl-leave-request',
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
                if (data.autoreload == "TRUE") {

                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })

                $('#edit_submit_btn').prop('disabled', false);
                $('#edit_submit_btn').css('cursor', 'pointer');
                $("#edit_submit_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#edit_submit_btn').prop('disabled', false);
            $('#edit_submit_btn').css('cursor', 'pointer');
            $("#edit_submit_btn").removeAttr('data-kt-indicator');
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


function deleteRplLeaveRequest(rpl_leave_id) {
    $('input[name=rpl_leave_id]').val(rpl_leave_id);
    $('#delete-rpl-leave-request').modal('show');

}

function editRplLeaveRequest(rpl_leave_id, rpl_leave_worked_on_date, rpl_leave_required_for_date, leave_applied_reason) {
    $('input[name=rpl_leave_id]').val(rpl_leave_id);
    $('#edit_rpl_leave_worked').val(rpl_leave_worked_on_date);
    $('#edit_rpl_leave_required').val(rpl_leave_required_for_date);
    $('textarea[name=leave_reason]').html(leave_applied_reason);
    $('#edit_leave_date').attr('max', get_one_month_future_date);
    $('#edit_leave_rpl_apply_request').modal('show');
}