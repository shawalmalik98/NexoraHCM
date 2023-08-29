$("#kt_daterangepicker_update").daterangepicker({
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

$('.message_btn').click(function () {
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});
function approvedRequest(request) {

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
        $('#modalApproved').find('input[name="duration"]').val(duration_period);
    }
    $('#modalApproved').find('input[name=approved_id]').val(request.shift_request_id);
    $('#modalApproved').find('input[name=shift_start]').val(request.request_shift_start);
    $('#modalApproved').find('input[name=shift_end]').val(request.request_shift_end);
    $('#modalApproved').find('input[name=effect_date]').val(request.request_shift_effect_date);
    $('#modalApproved').modal('show');
}

function disapprovedRequest(request_id) {
    $('input[name=disapproved_id]').val(request_id)
    $('#modalDisapproved').modal('show');
}

$('#modalDisapprovedForm').submit(function (e) {
    e.preventDefault();
    send_server_request('POST', 'shift-time-request/disapproved-shift-time-request', this, 'modalDisapprovedFormButton', "Yes", '', 3000)
})

$('#modalApprovedForm').submit(function (e) {

    e.preventDefault();

    var url = base_url + '/shift-time-request/approved-shift-time-request';

    let form_data = new FormData(this);


    Swal.fire({
        title: 'Are you sure want to approve ?',
        text: 'Once you approve this, the shift time of employee will be updated on the effective date.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
    }).then(function (result) {

        if (result.value) {

            $('#modalApprovedFormButton').prop('disabled', true);
            $('#modalApprovedFormButton').attr('data-kt-indicator', 'on');
            $('#modalApprovedFormButton').css('cursor', 'not-allowed');

            body_data = {
                // Adding method type
                method: 'POST',
                // Adding body or contents to send
                // body: JSON.stringify({form_data}),
                body: form_data,
                // Adding headers to the request
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }
            fetch(url, body_data)
                // Converting to JSON
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    return Promise.reject(response);
                })
                // Displaying results to console
                .then(json => {

                    if (json.status == 'TRUE') {
                        Toast.fire({
                            icon: 'success',
                            title: json.msg,
                            timer: 3000,
                        })
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                        $('#modalApprovedFormButton').prop('disabled', false);
                        $('#modalApprovedFormButton').removeAttr('data-kt-indicator');
                        $('#modalApprovedFormButton').css('cursor', 'pointer');
                    } else {

                        Toast.fire({
                            icon: 'warning',
                            title: json.msg,
                            timer: 3000,
                        })
                        $('#modalApprovedFormButton').prop('disabled', false);
                        $('#modalApprovedFormButton').removeAttr('data-kt-indicator');
                        $('#modalApprovedFormButton').css('cursor', 'pointer');
                    }
                }).catch(response => {
                    $('#modalApprovedFormButton').prop('disabled', false);
                    $('#modalApprovedFormButton').removeAttr('data-kt-indicator');
                    $('#modalApprovedFormButton').css('cursor', 'pointer');
                });
        }
    });
});