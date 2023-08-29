$(function () {
    $('.requitionstatus').select2({
        "enable": false,
        dropdownParent: $('#requisition_status')
    })
});

$('#requisition_status_candidate').submit(function (e) {

    $('#submit_btn_status').prop('disabled', true);
    $("#submit_btn_status").attr('data-kt-indicator', 'on');
    $('#submit_btn_status').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/requisition/requisition-status',
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

$('.message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#message_body').html(message);
});