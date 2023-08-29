$(document).ready(function () {

    $('.sec1_logo').click(function () {
        $('.sc_section1').addClass('sc_sec_form_show');
    });
});

$(document).keypress(function (event) {
    if (event.which == '13') {
        event.preventDefault();
    }
});

var target_url;

function get_url() {

    $(".checkin_btn").click(function () {
        target_url = base_url + '/checkinattendance';
    });
    $(".checkout_btn").click(function () {
        target_url = base_url + '/checkoutattendance';
    });
    return target_url;

}

get_url();

$('#attendance-marking-form').submit(function (e) {
    $('.btn-block').prop('disabled', true);
    $('.btn-block').css('cursor', 'not-allowed');
    $('#gl_div').html(gl);
    e.preventDefault();
    $.ajax({
        url: get_url(),
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            $('#gl_div').html('');
            if (data.status == 'CheckIN') {
                $('#checkin_modal').modal('show');
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else if (data.status == "CheckOUT") {
                $('#checkout_modal').modal('show');
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else if (data.status == "TRUE") {
                $('#close_modal').modal('show');
                $('#error_message').html(data.msg);
                $('.btn-block').prop('disabled', false);
                $('.btn-block').css('cursor', 'pointer');
                setTimeout(() => {
                    location.reload();
                }, 2000);

            } else {
                $('#close_modal').modal('show');
                $('#error_message').html(data.msg);
                $('.btn-block').prop('disabled', false);
                $('.btn-block').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            $('.btn-block').prop('disabled', false);
            $('.btn-block').css('cursor', 'pointer');
            $('#gl_div').html('');
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                $('#close_modal').modal('show');
                $('#error_message').html('Internet Connection Problem');
            } else if (errorStatus == 419) {
                location.reload();
            } else {
                $('#close_modal').modal('show');
                $('#error_message').html('Try Again. Error Code ' + errorStatus);
            }
        }
    });
});

var gl;