$(function () {
    $('#interview_type').trigger('change');
    $('#interview_type1').trigger('change');
    $('.mySelect3').select2({
        "enable": false,
        dropdownParent: $('#update_interview')
    })
    $('.requitionselect').select2({
        "enable": false,
        dropdownParent: $('#schedule_interview')
    })
});

$('#interview_type').change(function () {
    var interview_type = $('#interview_type').find(":selected").val();

    $('.child-div-interview-type').removeClass('d-none')

    if (interview_type == 'Online') {
        $('#label-interview-room').removeClass('required');
        $('#label-interview-link').addClass('required');
        $('#interview_room').prop('required', false);
        $('#interview_link').prop('required', true);

    } else {
        $('#label-interview-link').removeClass('required');
        $('#label-interview-room').addClass('required');
        $('#interview_link').prop('required', false);
        $('#interview_room').prop('required', true);

    }
})


$('#update_interview').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/interviews/modify_interview',
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
                    timer: 5000,
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

$('#add_interview').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $('#submit_btn').attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/interviews/scheduleinterview',
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
                    timer: 5000,
                })
                $('#submit_btn').prop('disabled', false);
                $('#submit_btn').removeAttr('data-kt-indicator');
                $('#submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn').prop('disabled', false);
            $('#submit_btn').removeAttr('data-kt-indicator');
            $('#submit_btn').css('cursor', 'pointer');
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

$('#hired_interviewer_employee').submit(function (e) {
    $('#submit_btn_hired').prop('disabled', true);
    $("#submit_btn_hired").attr('data-kt-indicator', 'on');
    $('#submit_btn_hired').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/interviews/interviewer-hired',
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
                $('#submit_btn_hired').prop('disabled', false);
                $('#submit_btn_hired').css('cursor', 'pointer');
                $("#submit_btn_hired").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn_hired').prop('disabled', false);
            $('#submit_btn_hired').css('cursor', 'pointer');
            $("#submit_btn_hired").removeAttr('data-kt-indicator');
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


$('#interviewer_status_candidate').submit(function (e) {

    $('#submit_btn_status').prop('disabled', true);
    $("#submit_btn_status").attr('data-kt-indicator', 'on');
    $('#submit_btn_status').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/interviews/interviewer-status',
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
                $('#submit_btn_status').prop('disabled', false);
                $('#submit_btn_status').css('cursor', 'pointer');
                $("#submit_btn_status").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn_status').prop('disabled', false);
            $('#submit_btn_status').css('cursor', 'pointer');
            $("#submit_btn_status").removeAttr('data-kt-indicator');
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


$("#interview_reschedule").daterangepicker({
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
}
);


$("#interview_update").daterangepicker({
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
}
);

$('#interview_type1').change(function () {
    var interview_type = $('#interview_type1').find(":selected").val();

    $('.child-div-interview-type').removeClass('d-none')

    if (interview_type == 'Online') {
        $('#label-interview-room1').removeClass('required');
        $('#label-interview-link1').addClass('required');
        $('#interview_room1').prop('required', false);
        $('#interview_link1').prop('required', true);

    } else {
        $('#label-interview-link1').removeClass('required');
        $('#label-interview-room1').addClass('required');
        $('#interview_link1').prop('required', false);
        $('#interview_room1').prop('required', true);

    }
});

