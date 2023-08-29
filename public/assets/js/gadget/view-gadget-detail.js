$('.check').on('click', function () {

    var checkbox = $('input[type="checkbox"]:checked');

    if (checkbox.length > 0) {
        $('.select-employee').val('').trigger('change');
        $('.select-employee').prop('required', false);
        $('.select-employee').prop('disabled', true);
    } else {
        $('.select-employee').prop('required', true);
        $('.select-employee').prop('disabled', false);
    }

});

$('#update_gadget_detail').submit(function (e) {
    $('#update_gadget_detail_submit_btn').prop('disabled', true);
    $("#update_gadget_detail_submit_btn").attr('data-kt-indicator', 'on');
    $('#update_gadget_detail_submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/gadgets-inventory/update_gadget',
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
                    timer: 8000,
                })
                $('#update_gadget_detail_submit_btn').prop('disabled', false);
                $("#update_gadget_detail_submit_btn").removeAttr('data-kt-indicator');
                $('#update_gadget_detail_submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#update_gadget_detail_submit_btn').prop('disabled', false);
            $("#update_gadget_detail_submit_btn").removeAttr('data-kt-indicator');
            $('#update_gadget_detail_submit_btn').css('cursor', 'pointer');
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


$('#assign_new_gadget').submit(function (e) {
    $('#assign_new_gadget_submit_btn').prop('disabled', true);
    $("#assign_new_gadget_submit_btn").attr('data-kt-indicator', 'on');
    $('#assign_new_gadget_submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/gadgets-inventory/assign_gadget',
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
                    timer: 8000,
                })
                $('#assign_new_gadget_submit_btn').prop('disabled', false);
                $("#assign_new_gadget_submit_btn").removeAttr('data-kt-indicator');
                $('#assign_new_gadget_submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#assign_new_gadget_submit_btn').prop('disabled', false);
            $("#assign_new_gadget_submit_btn").removeAttr('data-kt-indicator');
            $('#assign_new_gadget_submit_btn').css('cursor', 'pointer');
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