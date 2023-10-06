$(function () {
    dataTable = $('#tableDailyAttendance').DataTable({
        order: false,
        search: { regex: true },
        aLengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
        ],
        iDisplayLength: 500,
    });

    const present = present_attendance;
    const absent_count = absent_count_attendance;
    const late_coming = late_coming_attendance;
    const early_out = early_out_attendance;
    const halfday = halfday_attendance;
    const total_offDays_holiday = total_offDays_holiday_attendance;

    const total_late_early = late_coming + early_out;
    $('#present').html(present);
    $('#absent').html(absent_count);
    $('#lateComing').html(total_late_early);
    $('#HalfDay').html(halfday);
    $('#offdays').html(total_offDays_holiday);


});

function attendancenuploadclose() {

    $('#update_btn').prop('disabled', false);
    $("#update_btn").removeAttr('data-kt-indicator');
    $('#update_btn').css('cursor', 'pointer');
    $('input[name=Force_Approved]').val('FALSE');
}

function attendanceupload() {

    $('.force_upload').prop('disabled', true);
    $('.force_upload').attr('data-kt-indicator', 'on');
    $('.force_upload').css('cursor', 'not-allowed');
    $('#attendance_update').submit();

}

$('#attendance_update, #attendance_upload').submit(function (e) {

    $('#update_btn').prop('disabled', true);
    $("#update_btn").attr('data-kt-indicator', 'on');
    $('#update_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/daily-attendance-report/upload-attendance-report',
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
                if (data.validation == 'TRUE') {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 3000,
                    })
                    $('#update_btn').prop('disabled', false);
                    $("#update_btn").removeAttr('data-kt-indicator');
                    $('#update_btn').css('cursor', 'pointer');

                } else {
                    $('#attendance_records_upload').modal('show');
                    $('input[name=Force_Approved]').val('TRUE');

                    var employee = '<div>' +
                        '<p>' + data.msg + '</p>' +
                        '<p>' + data.error_logs.join('<br>') + '</p>' +
                        '</div>';
                    $('#attendance_record_msg').html(employee);
                }

            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#update_btn').prop('disabled', false);
            $("#update_btn").removeAttr('data-kt-indicator');
            $('#update_btn').css('cursor', 'pointer');
            if (errorStatus == 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Attendance upload failed please try again',
                    timer: 4000,
                });
                setTimeout(() => {
                    location.reload();
                }, 4000);
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