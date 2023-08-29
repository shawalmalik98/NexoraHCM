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
        $("#wfh_request_date").prop('required', true);
        $(".date-control").prop('required', false);
        $('#wfh_request_date').attr('max', get_two_month_future_date);
    } else {
        $('#wfh_request_date').val('');
        $('.only_date_input').addClass('d-none');
        $('.from_to_date_input').removeClass('d-none');
        $(".date-control").prop('required', true);
        $("#wfh_request_date").prop('required', false);
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


$('#send_new_request').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/submit_wfh_request',
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


function editWFHRequest(wfh_request_id, wfh_request_date, wfh_request_message) {
    $('input[name=request_id]').val(wfh_request_id);
    $('input[name=request_date]').val(wfh_request_date);
    $('textarea[name=edit_home_work_reason]').html(wfh_request_message);
    $('#edit_request_date').attr('max', get_two_month_future_date);
    $('#edit-wfh-request').modal('show');
}

function deleteWFHRequest(wfh_id) {
    $('input[name=wfh_id]').val(wfh_id);
    $('#delete-wfh-request').modal('show');

}


$('#edit_wfh_request').submit(function (e) {
    $('#submit_btn_wfh').prop('disabled', true);
    $("#submit_btn_wfh").attr('data-kt-indicator', 'on');
    $('#submit_btn_wfh').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/update-wfh-request',
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

                $('#submit_btn_wfh').prop('disabled', false);
                $('#submit_btn_wfh').css('cursor', 'pointer');
                $("#submit_btn_wfh").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn_wfh').prop('disabled', false);
            $('#submit_btn_wfh').css('cursor', 'pointer');
            $("#submit_btn_wfh").removeAttr('data-kt-indicator');
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


$('#delete_wfh_request').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/delete-wfh-request',
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
    var message = $(this).attr('data-message');
    $('#message_body').html(message);
});

$('#kt_docs_maxlength_threshold2').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$('#edit-wfh-message').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 3);
});

function get_one_month_future_date(from_date) {
    var date = new Date(from_date);
    date.setDate(date.getDate() + 30);
    var future_date = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
    return future_date;
}

function get_two_month_future_date() {
    var dates = new Date();
    dates.setDate(dates.getDate());
    var future_dates = dates.getFullYear() + '-' + ('0' + (dates.getMonth() + 3)).slice(-2) + '-' + ('0' + dates.getDate()).slice(-2);
    return future_dates;
}

$(function () {
    $('#req_status').on('change', function () {
        var value = $(this).val();
        if (value == '') {
            dataTable.column(4).search('', false, true).draw();
        } else {
            dataTable.column(4).search("^" + value + "$", true, false).draw();
        }
    });
}); 