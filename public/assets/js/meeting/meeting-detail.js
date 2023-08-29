$(function () {
    $('#meeting_type').trigger('change');
});
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
$('#re_schedule_meeting_form').submit(function (e) {
    $('#re_schedule_submit_btn').prop('disabled', true);
    $('#re_schedule_submit_btn').attr('data-kt-indicator', 'on');
    $('#re_schedule_submit_btn').css('cursor', 'not-allowed');
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
                $('#re_schedule_submit_btn').prop('disabled', false);
                $('#re_schedule_submit_btn').removeAttr('data-kt-indicator');
                $('#re_schedule_submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#re_schedule_submit_btn').prop('disabled', false);
            $('#re_schedule_submit_btn').removeAttr('data-kt-indicator');
            $('#re_schedule_submit_btn').css('cursor', 'pointer');
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

$('#update_schedule_meeting_form').submit(function (e) {
    $('#new_user_submit_btn').prop('disabled', true);
    $('#new_user_submit_btn').attr('data-kt-indicator', 'on');
    $('#new_user_submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/add_another_user_schedule_meeting',
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
                $('#new_user_submit_btn').prop('disabled', false);
                $('#new_user_submit_btn').removeAttr('data-kt-indicator');
                $('#new_user_submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#new_user_submit_btn').prop('disabled', false);
            $('#new_user_submit_btn').removeAttr('data-kt-indicator');
            $('#new_user_submit_btn').css('cursor', 'pointer');
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


if (document.querySelector('#meeting_reason') != null) {
    ClassicEditor

        .create(document.querySelector('#meeting_reason'))
        .then(meeting_reason => {
            meeting_reason.enableReadOnlyMode("meeting_reason");
        })
        .catch(error => {
            console.error(error);
        });
}
