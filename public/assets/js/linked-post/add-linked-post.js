$('#linked_post_form').submit(function (e) {
    $('#linked_post_btn').prop('disabled', true);
    $('#linked_post_btn').attr('data-kt-indicator', 'on');
    $('#linked_post_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/linked-in-post/add-linked-post',
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
                    window.location.href = base_url;
                }, 3000);
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                });
                $('#linked_post_btn').prop('disabled', false);
                $("#linked_post_btn").removeAttr('data-kt-indicator');
                $('#linked_post_btn').css('cursor', 'pointer');

            }
        },
        error: function (jqXHR, textStatus) {
            $('#linked_post_btn').prop('disabled', false);
            $('#linked_post_btn').removeAttr('data-kt-indicator');
            $('#linked_post_btn').css('cursor', 'pointer');
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
});