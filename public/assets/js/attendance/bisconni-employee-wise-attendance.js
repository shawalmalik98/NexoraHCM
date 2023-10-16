function editAttendanceadjustment(attendance_id, att_employee_id ,att_for_date) {
    $('input[name=attendance_id]').val(attendance_id);
    $('input[name=employee_code]').val(att_employee_id);
    $("#update_dated").val(att_for_date);
    $('#attendance_adjustment').modal('show');
}

$('#add_new_adjustment').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/update-bisconni-employee-report',
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
                $('#submit_btn').prop('disabled', false);
                $('#submit_btn').css('cursor', 'pointer');
                $("#submit_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn').prop('disabled', false);
            $('#submit_btn').css('cursor', 'pointer');
            $("#submit_btn").removeAttr('data-kt-indicator');
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