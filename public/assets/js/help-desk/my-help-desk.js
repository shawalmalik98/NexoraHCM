$('#select-department').change(function (e) {
    e.preventDefault();
    $('#employees').html('');
    var dept = $('.multi-depart').val();

    $.ajax({
        url: base_url + '/get-employee-department-hd',
        type: 'GET',

        data: {
            department: dept
        },

        success: function (response) {
            $('#employees').append(response);


        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                alert("Internet Connection Problem");
            } else {
                alert("Try Again. Error Code " + errorStatus);
            }

        }
    });


})

$('#addHelpDesk').submit(function (e) {
    $('#help-desk-btn').prop('disabled', true);
    $('#help-desk-btn').attr('data-kt-indicator', 'on');
    $('#help-desk-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/add-help-desk',
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
                    timer: 3000,
                })
                $('#help-desk-btn').prop('disabled', false);
                $("#help-desk-btn").removeAttr('data-kt-indicator');
                $('#help-desk-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#help-desk-btn').prop('disabled', false);
            $("#help-desk-btn").removeAttr('data-kt-indicator');
            $('#help-desk-btn').css('cursor', 'pointer');
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