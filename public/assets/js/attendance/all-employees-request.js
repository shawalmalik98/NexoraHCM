$('.checked').on('click', function () {

    checkbox = dataTable.$(".checked:checked", { "page": "all" })

    if (checkbox.length <= 0) {
        $('.on-check').addClass('d-none');
    } else {
        $('.on-check').removeClass('d-none');
    }

});
var request_id = null;
var status_approved_disapproved;
var approvermessage = null;
var disapprovermessage = null;
$('.attendnace_request').on('click', function (e) {

    status_approved_disapproved = $(this).attr('data-value');
    title = $(this).attr('data-title');
    request_id = $(this).attr('data-id');


    if (status_approved_disapproved == "DisApprove" || status_approved_disapproved == "LeadDisApprove") {
        $('#modal-danger').modal('show');

        if (title != '') {
            $('.msg').html(title);
        }

    } else {

        $('#modal-success').modal('show');

    }

});

$('.request_submit').submit(function (e) {
    e.preventDefault();

    approvermessage = $('#approvermessage').val();
    disapprovermessage = $('#disapprovermessage').val();

    $('#modal-danger').modal('hide');
    $('#modal-success').modal('hide');


    var values = [];

    var checkbox = dataTable.$(".checked:checked", { "page": "all" });

    if (checkbox.length > 0) {
        $(checkbox).each(function () {
            values.push($(this).attr('data-id'));
        });
        var value = values.join(",");

    } else {
        value = request_id;
    }


    $.ajax({
        url: base_url + '/attendance-requests/attendanceapproverequest',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { ids: value, status: status_approved_disapproved, approvermsg: approvermessage, disapprovermsg: disapprovermessage },
        success: function (data) {
            if (data.status == "TRUE") {
                $(".checked").prop("checked", false);
                Toast.fire({
                    icon: 'success',
                    title: data.msg,
                    timer: 5000,
                })
                setTimeout(() => {
                    location.reload();
                }, 5000);

            } else if (data.status == "FALSE") {
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


});


$('#update_attendance_request_check_in_time').submit(function (e) {
    e.preventDefault();
    $('#edit_attendance_checkin_submit_btn').prop('disabled', true);
    $("#edit_attendance_checkin_submit_btn").attr('data-kt-indicator', 'on');
    $('#edit_attendance_checkin_submit_btn').css('cursor', 'pointer');

    $.ajax({
        url: base_url + '/attendance-requests/attendance-check-in-time',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.status == "TRUE") {
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

                $('#edit_attendance_checkin_submit_btn').prop('disabled', false);
                $('#edit_attendance_checkin_submit_btn').css('cursor', 'pointer');
                $("#edit_attendance_checkin_submit_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#edit_attendance_checkin_submit_btn').prop('disabled', false);
            $('#edit_attendance_checkin_submit_btn').css('cursor', 'pointer');
            $("#edit_attendance_checkin_submit_btn").removeAttr('data-kt-indicator');
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


$('#update_attendance_request_check_out_time').submit(function (e) {
    e.preventDefault();
    $('#edit_attendance_checkout_submit_btn').prop('disabled', true);
    $("#edit_attendance_checkout_submit_btn").attr('data-kt-indicator', 'on');
    $('#edit_attendance_checkout_submit_btn').css('cursor', 'pointer');

    $.ajax({
        url: base_url + '/attendance-requests/attendance-check-out-time',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.status == "TRUE") {
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

                $('#edit_attendance_checkout_submit_btn').prop('disabled', false);
                $('#edit_attendance_checkout_submit_btn').css('cursor', 'pointer');
                $("#edit_attendance_checkout_submit_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#edit_attendance_checkout_submit_btn').prop('disabled', false);
            $('#edit_attendance_checkout_submit_btn').css('cursor', 'pointer');
            $("#edit_attendance_checkout_submit_btn").removeAttr('data-kt-indicator');
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

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');

    request_response_log(request_id, 1);


})

function editAttendanceCheckInTime(attendance_req_id, check_in_time, hr_status) {

    $('input[name=attendance_req_id]').val(attendance_req_id);
    $('input[name=check_in_time]').val(check_in_time);

    check_in_request_time_log(attendance_req_id);
    if (hr_status != 1) {
        $('.check-in-time-div').addClass('d-none');
        $('.modal-footer-action-btn').addClass('d-none');
    } else {
        $('.check-in-time-div').removeClass('d-none');
        $('.modal-footer-action-btn').removeClass('d-none');
    }
    $('#update_attendance_request_checkintime').modal('show');
}

function editAttendanceCheckOutTime(attendance_req_id, check_out_time, hr_status) {

    $('input[name=attendance_req_id]').val(attendance_req_id);
    $('input[name=check_out_time]').val(check_out_time);
    check_out_request_time_log(attendance_req_id);
    if (hr_status != 1) {
        $('.check-out-time-div').addClass('d-none');
        $('.modal-footer-action-check-out-btn').addClass('d-none');
    } else {
        $('.check-out-time-div').removeClass('d-none');
        $('.modal-footer-action-check-out-btn').removeClass('d-none');
    }
    $('#update_attendance_request_checkouttime').modal('show');
}

function check_in_request_time_log(attendance_req_id) {
    $.ajax({

        url: base_url + '/request-check-in-time-log',
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { request_id: attendance_req_id },
        success: function (response) {
            var record = '';
            if (response.status == "TRUE") {
                $('#request-check-in-time-response').html('');
                record += `<thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-7">Employee Name</th>
                        <th>CheckIn Time</th>
                        <th>Approval Time</th>
                        
                    </tr>
                </thead> <tbody>`;
                $.each(response.data, function (key, employee_request) {

                    record += `<tr>
                        <td class="ps-6 "><b>${employee_request.employee_name}- (${employee_request.employee_code})</b></td>
                        <td class="ps-6">${employee_request.attendance_checkin_time}</td>
                        <td class="ps-6">${employee_request.att_req_time_created_at}</td>
                    </tr>`;
                });
                record += ` </tbody>`;
                $('#request-check-in-time-response').html(record);
            } else {
                record = 'No One responded to this request yet.';
                $('#request-check-in-time-response').html(record);
            }

            //$('#manager_modal_status').modal('show');
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


function check_out_request_time_log(attendance_req_id) {
    $.ajax({

        url: base_url + '/request-check-out-time-log',
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { request_id: attendance_req_id },
        success: function (response) {
            var record = '';
            if (response.status == "TRUE") {
                $('#request-check-out-time-response').html('');
                record += `<thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-7">Employee Name</th>
                        <th>CheckOut Time</th>
                        <th>Approval Time</th>
                        
                    </tr>
                </thead> <tbody>`;
                $.each(response.data, function (key, employee_request) {

                    record += `<tr>
                        <td class="ps-6 "><b>${employee_request.employee_name}- (${employee_request.employee_code})</b></td>
                        <td class="ps-6">${employee_request.attendance_checkout_time}</td>
                        <td class="ps-6">${employee_request.att_req_time_created_at}</td>
                    </tr>`;
                });
                record += ` </tbody>`;
                $('#request-check-out-time-response').html(record);
            } else {
                record = 'No One responded to this request yet.';
                $('#request-check-out-time-response').html(record);
            }

            //$('#manager_modal_status').modal('show');
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

$(function () {
    $('#req_status').on('change', function () {
        var value = $(this).val();
        if (value == '') {
            dataTable.column(9).search('', false, true).draw();
        } else {
            dataTable.column(9).search("^" + value + "$", true, false).draw();
        }
    });
});