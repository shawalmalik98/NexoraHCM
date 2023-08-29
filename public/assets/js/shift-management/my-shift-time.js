$('.max_length_validator').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$('.message_btn').click(function () {
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});

$("#kt_daterangepicker_add,#kt_daterangepicker_update").daterangepicker({
    locale: {
        format: 'YYYY-MM-DD'
    },
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 1901,
    minDate: new Date(Date.now()),
    maxYear: parseInt(moment().format("YYYY"), 12)
}, function (start, end, label) {
    var years = moment().diff(start, "years");
});
$("#kt_daterangepicker_add").val('');
function calculateDuration() {

    var start_time = $('input[name="shift_start"]').val();
    var end_time = $('input[name="shift_end"]').val();

    var time_start = new Date();
    var time_end = new Date();

    var value_start = start_time.split(':');
    var value_end = end_time.split(':');

    time_start.setHours(value_start[0], value_start[1], 0, 0)
    time_end.setHours(value_end[0], value_end[1], 0, 0)
    var duration_in_ms = time_end - time_start;
    var duration_period = moment.utc(duration_in_ms).format('HH:mm:ss');

    if (duration_period != 'Invalid date') {
        $('input[name="duration"]').val(duration_period);
    }
}

function calculateDurationUpdate() {

    var start_time = $('#editShiftRequest').find('input[name="shift_start"]').val();
    var end_time = $('#editShiftRequest').find('input[name="shift_end"]').val();

    var time_start = new Date();
    var time_end = new Date();

    var value_start = start_time.split(':');
    var value_end = end_time.split(':');

    time_start.setHours(value_start[0], value_start[1], 0, 0)
    time_end.setHours(value_end[0], value_end[1], 0, 0)
    var duration_in_ms = time_end - time_start;
    var duration_period = moment.utc(duration_in_ms).format('HH:mm:ss');

    if (duration_period != 'Invalid date') {
        $('#editShiftRequest').find('input[name="duration"]').val(duration_period);
    }
}
function deleteShiftRequest(request_id) {
    $('input[name=request_id]').val(request_id);
    $('#delete-shift-request').modal('show');
}

function editShiftRequest(request) {

    var start_time = request.request_shift_start;
    var end_time = request.request_shift_end;

    var time_start = new Date();
    var time_end = new Date();

    var value_start = start_time.split(':');
    var value_end = end_time.split(':');

    time_start.setHours(value_start[0], value_start[1], 0, 0)
    time_end.setHours(value_end[0], value_end[1], 0, 0)
    var duration_in_ms = time_end - time_start;
    var duration_period = moment.utc(duration_in_ms).format('HH:mm:ss');

    if (duration_period != 'Invalid date') {
        $('#editShiftRequest').find('input[name="duration"]').val(duration_period);
    }
    $('#editShiftRequest').find('input[name=request_id]').val(request.shift_request_id);
    $('#editShiftRequest').find('input[name=shift_start]').val(request.request_shift_start);
    $('#editShiftRequest').find('input[name=shift_end]').val(request.request_shift_end);
    $('#editShiftRequest').find('input[name=effect_date]').val(request.request_shift_effect_date);
    $('#editShiftRequest').find('textarea[name=reason]').html(request.request_shift_reason);
    $('#editShiftRequest').modal('show');
}
$('#shiftRequestForm').submit(function (e) {
    e.preventDefault();
    send_server_request('POST', 'add-shift-time-request', this, 'submit_btn', "Yes", '', 3000)
})

$('#delete_request').submit(function (e) {
    e.preventDefault();
    send_server_request('POST', 'delete-shift-time-request', this, 'delete_submit_btn', "Yes", '', 3000)
})
$('#editShiftRequestForm').submit(function (e) {
    e.preventDefault();
    send_server_request('POST', 'update-shift-time-request', this, 'submit_btn_update', "Yes", '', 3000)
})
