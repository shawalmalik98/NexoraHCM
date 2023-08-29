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
    }
    else {
        $('#days').val(total_days + 1);
    }

});

$("#days_assign").change(function () {
    var value = $(this).children("option:selected").val();
    if (value == "single") {
        $('.only_date_input').removeClass('d-none');
        $('.from_to_date_input').addClass('d-none');
        $('.date-control').val('');
        $("#attendance_date").prop('required', true);
        $(".date-control").prop('required', false);
    } else {
        $('#attendance_date').val('');
        $('.only_date_input').addClass('d-none');
        $('.from_to_date_input').removeClass('d-none');
        $(".date-control").prop('required', true);
        $("#attendance_date").prop('required', false);
    }
});

$('#from_date').change(function () {
    var from_selected_date = $(this).val();
    if (from_selected_date != '') {
        $('#to_date').prop('disabled', false);
        $('#to_date').attr('min', from_selected_date);
    } else {
        $('#to_date').prop('disabled', true);
        $('#to_date').removeAttr('min');
        $('#to_date').val('');
    }
});

$('#send_new_request').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/submit_attendance_request',
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

$('#attendance_type').change(function () {
    attendance_type_status = $(this).find(":selected").val();

    if (attendance_type_status == 'On Time Check In') {
        $('.main-check-out-div').addClass('d-none');
        $('.main-check-in-div').removeClass('d-none');
        $("#check_in_time").prop('required', true);
        $("#check_out_time").prop('required', false);

    }
    else if (attendance_type_status == 'On Time Check Out') {
        $('.main-check-in-div').addClass('d-none');
        $('.main-check-out-div').removeClass('d-none');
        $("#check_in_time").prop('required', false);
        $("#check_out_time").prop('required', true);

    } else {
        $("#check_in_time").prop('required', false);
        $("#check_out_time").prop('required', false);
        $('.main-check-in-div').addClass('d-none');
        $('.main-check-out-div').addClass('d-none');
    }
    //alert(attendance_type_status)
})

$('#edit_attendance_type').change(function () {
    attendance_type_status = $(this).find(":selected").val();

    if (attendance_type_status == 'On Time Check In') {
        $('.edit-check-out-div').addClass('d-none');
        $('.edit-check-in-div').removeClass('d-none');
        $("#edit_check_in_time").prop('required', true);
        $("#edit_check_out_time").prop('required', false);

    }
    else if (attendance_type_status == 'On Time Check Out') {
        $('.edit-check-in-div').addClass('d-none');
        $('.edit-check-out-div').removeClass('d-none');
        $("#edit_check_in_time").prop('required', false);
        $("#edit_check_out_time").prop('required', true);

    } else {
        $("#edit_check_in_time").prop('required', false);
        $("#edit_check_out_time").prop('required', false);
        $('.edit-check-in-div').addClass('d-none');
        $('.edit-check-out-div').addClass('d-none');
    }
    //alert(attendance_type_status)
})


function editAttendanceRequest(attendance_id, attendance_request_time_id, request_date, request_attendance_status, attendance_checkin_time, attendance_checkout_time, message) {
    $('input[name=attendance_id]').val(attendance_id);
    $('input[name=attendance_request_time_id]').val(attendance_request_time_id);
    $('input[name=edit_attendance_date]').val(request_date);
    $('select[name=edit_attendance_type]').val(request_attendance_status).trigger('change');
    if (request_attendance_status == 'On Time Check In') {
        $('#edit_check_in_time').val(attendance_checkin_time);
    } else {
        $('#edit_check_out_time').val(attendance_checkout_time);
    }
    $('textarea[name=edit_attendance_message]').html(message);
    $('#edit_attendance_request_apply').modal('show');
}


function deleteAttendanceRequest(attendance_id) {
    $('input[name=attendance_id]').val(attendance_id);
    $('#delete-attendance-request').modal('show');
}

$('#update_attendance_request').submit(function (e) {
    $('#edit_attendance_submit_btn').prop('disabled', true);
    $("#edit_attendance_submit_btn").attr('data-kt-indicator', 'on');
    $('#edit_attendance_submit_btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/update-attendance-request',
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

                $('#edit_attendance_submit_btn').prop('disabled', false);
                $('#edit_attendance_submit_btn').css('cursor', 'pointer');
                $("#edit_attendance_submit_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#edit_attendance_submit_btn').prop('disabled', false);
            $('#edit_attendance_submit_btn').css('cursor', 'pointer');
            $("#edit_attendance_submit_btn").removeAttr('data-kt-indicator');
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

$('#delete_attendance_request').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/delete-attendance-request',
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

$('.message_btn').click(function () {
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});

$('#kt_docs_maxlength_threshold3').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$('#edit_kt_docs_maxlength_threshold3').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});


$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 1);
});

function editAttendanceCheckInTime(attendance_req_id) {

    check_in_request_time_log(attendance_req_id);
    $('#update_attendance_request_checkintime').modal('show');
}

function editAttendanceCheckOutTime(attendance_req_id) {

    check_out_request_time_log(attendance_req_id);
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
            dataTable.column(7).search('', false, true).draw();
        } else {
            dataTable.column(7).search("^" + value + "$", true, false).draw();
        }
    });
});