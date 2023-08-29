function markAttendance(e) {


    var attendance_id = $(e).find('p').attr('data-attendance_id');
    var intime = $(e).find('p').attr('data-intime');
    var outtime = $(e).find('p').attr('data-outtime');
    var attendance_status = $(e).find('p').attr('data-attstatus');

    var employeeId = $(e).find('p').attr('data-employeeId');
    var dates = $(e).find('p').attr('data-date');
    var month = $(e).find('p').attr('data-month');
    var year = $(e).find('p').attr('data-year');


    if (attendance_id != "NA") {

        url = base_url + '/attendance/update_marked_attendance';
    } else {
        url = base_url + '/attendance/update_marked_attendance_absent';
    }
    if (attendance_id != null) {
        $('#edit_modal').modal('show');
    }

    $('#attendance_date').val(attendance_id);
    $('#intime').val(intime);
    $('#outtime').val(outtime);
    $('#att_employee_id').val(employeeId);
    $('#att_for_date').val(dates);
    $('#att_for_month').val(month);
    $('#att_for_year').val(year);
    $('#attendance_status').val(attendance_status).trigger('change');
};

$('#update_attendance2').submit(function (e) {
    e.preventDefault();
    $('#edit_attendance_btn').prop('disabled', true);
    $('#edit_attendance_btn').attr('data-kt-indicator', 'on');
    $('#edit_attendance_btn').css('cursor', 'not-allowed');
    update_attendance_ajax();

});

function update_attendance_ajax() {

    $.ajax({
        url: url,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $('#update_attendance2').serialize(),
        success: function (data) {
            if (data.status == "TRUE") {

                $('#myDiv').load(document.URL + ' #myDiv');
                $('#edit_modal').modal('hide');
                $('#edit_attendance_btn').prop('disabled', false);
                $('#edit_attendance_btn').removeAttr('data-kt-indicator');
                $('#edit_attendance_btn').css('cursor', 'pointer');



            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })
                $('#edit_attendance_btn').prop('disabled', false);
                $('#edit_attendance_btn').removeAttr('data-kt-indicator');
                $('#edit_attendance_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                alert("Internet Connection Problem");
            } else {
                alert("Try Again. Error Code " + errorStatus);
            }
            $('#edit_attendance_btn').prop('disabled', false);
            $('#edit_attendance_btn').removeAttr('data-kt-indicator');
            $('#edit_attendance_btn').css('cursor', 'pointer');
        }
    });
}