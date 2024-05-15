$('#to_date,#from_date').change(function () {

    var start = moment($('#from_date').val());
    $('#to_date').attr('min', $('#from_date').val());
    var end = moment($('#to_date').val());
    total_days = end.diff(start, "days");
    if (isNaN(total_days)) {
        $('#days').val('');
    } else if (total_days < 0) {
        $('#to_date').val('');
        $('#days').val('');
    } else {
        $('#days').val(total_days + 1);
    }

});

$("#days_assign").change(function () {
    var value = $(this).children("option:selected").val();
    if (value == "single") {
        $('.only_date_input').removeClass('d-none');
        $('.from_to_date_input').addClass('d-none');
        $('.date-control').val('');
        $("#leave_date").prop('required', true);
        $(".date-control").prop('required', false);
        $('#leave_date').attr('max', get_two_month_future_date);
    } else {
        $('#leave_date').val('');
        $('.only_date_input').addClass('d-none');
        $('.from_to_date_input').removeClass('d-none');
        $(".date-control").prop('required', true);
        $("#leave_date").prop('required', false);
        $('#from_date').attr('max', get_two_month_future_date);
    }
});

$('#from_date').change(function () {
    var from_selected_date = $(this).val();
    if (from_selected_date != '') {
        $('#to_date').prop('disabled', false);
        $('#to_date').attr('min', from_selected_date);
        $('#to_date').attr('max', get_one_month_future_date(from_selected_date));
    } else {
        $('#to_date').prop('disabled', true);
        $('#to_date').removeAttr('min');
        $('#to_date').val('');
    }
});


function editLeaveRequest(leave_applied_id, leave_applied_for_date, leave_manage_id, leave_applied_reason) {
    $('input[name=leave_id]').val(leave_applied_id);
    $('#edit_leave_date').val(leave_applied_for_date);
    $('select[name=edit_leave_type]').val(leave_manage_id);
    $('#edit_leave_type').trigger('change');
    $('textarea[name=edit_leave_reason]').html(leave_applied_reason);
    $('#edit_leave_date').attr('max', get_two_month_future_date);
    $('#edit_leave_apply_request').modal('show');
}

function deleteLeaveRequest(leave_id) {
    $('input[name=leave_id]').val(leave_id);
    $('#delete-leave-request').modal('show');

}

$('#applyforleave_form').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/applyfor_leave',
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
                    timer: 3000,
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


$('#edit_applyforleave_form').submit(function (e) {
    $('#edit_submit_btn').prop('disabled', true);
    $("#edit_submit_btn").attr('data-kt-indicator', 'on');
    $('#edit_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/update-leave-request',
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
                if (data.autoreload == "TRUE") {

                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 3000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 3000,
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

$('#delete_leave_request').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/delete-leave-request',
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
                if (data.autoreload == "TRUE") {

                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 3000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 3000,
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


$('.message_btn').click(function () {
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});

$('#kt_docs_maxlength_threshold').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 2);
});

$(function () {
    $('#req_status').on('change', function () {
        var value = $(this).val();
        if (value == '') {
            dataTable.column(5).search('', false, true).draw();
        } else {
            dataTable.column(5).search("^" + value + "$", true, false).draw();
        }
    });
});