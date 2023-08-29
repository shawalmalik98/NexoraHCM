$(function () {

    $('.myselect2').select2({
        dropdownParent: $('#modal-default')
    })
});

$("#kt_daterangepicker_3").daterangepicker({
    locale: {
        format: 'YYYY-MM-DD'
    },
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 1901,
    minDate: new Date(),
    maxYear: parseInt(moment().format("YYYY"), 12)

}, function (start, end, label) {
    var years = moment().diff(start, "years");
},
);

$('#meeting_type').change(function () {
    var meeting_type = $('#meeting_type').find(":selected").val();

    $('.child-div-meeting-type').removeClass('d-none')

    if (meeting_type == 'Online') {
        $('#label-meeting-room').removeClass('required');
        $('#label-meeting-link').addClass('required');
        $('#meeting_room').prop('required', false);
        $('#meeting_link').prop('required', true);

    } else {
        $('#label-meeting-link').removeClass('required');
        $('#label-meeting-room').addClass('required');
        $('#meeting_link').prop('required', false);
        $('#meeting_room').prop('required', true);

    }
})


$('#schedule_meeting_form').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/schedule_meeting',
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

$('.message_btn').click(function () {
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});

function MeetingStatus(meeting_id, meeting_value) {
    $('input[name=meeting_id]').val(meeting_id);
    if (meeting_value == 2) {
        $('input[name=meeting_status]').val(2);
        $('#meeting-alert').html('Are you sure to Complete this Meeting ?');

    } else {
        $('input[name=meeting_status]').val(3);
        $('#meeting-alert').html('Are you sure to Cancel this Meeting ?');
    }

    $('#cancel-meeting').modal('show');

}

$('#cancel_meeting').submit(function (e) {
    $('#cancel_meeting_btn').prop('disabled', true);
    $("#cancel_meeting_btn").attr('data-kt-indicator', 'on');
    $('#cancel_meeting_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/cancel-schedule-meeting',
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

                $('#cancel_meeting_btn').prop('disabled', false);
                $('#cancel_meeting_btn').css('cursor', 'pointer');
                $("#cancel_meeting_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#cancel_meeting_btn').prop('disabled', false);
            $('#cancel_meeting_btn').css('cursor', 'pointer');
            $("#cancel_meeting_btn").removeAttr('data-kt-indicator');
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





