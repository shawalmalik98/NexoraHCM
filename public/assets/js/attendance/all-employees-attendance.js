$(function () {
    $('.select').select2({
        dropdownParent: $('#modal-default2 .modal-content  ')
    })
    $('.select7').select2({
        dropdownParent: $('#att-share-report-modal .modal-content  ')
    })

    let d = new Date();
    let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    let month = monthNames[d.getMonth()];
    let year = d.getFullYear();
    if ($("select[name=m]").val()) {
        month = monthNames[$("select[name=m]").val() - 1];
        year = $("select[name=y]").val();
    }
    let filename = month + "-" + year + " Attendance Record";

    dataTable = $('#tableAllEmployeeAttendance').DataTable({
        // fixedHeader: {
        //     headerOffset: $('#kt_app_header_wrapper').outerHeight()
        // },

        fixedColumns: true,
        order: false,
        aLengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
        ],
        iDisplayLength: 500,
        paging: $('#tableAllEmployeeAttendance').is('[data-server-paginated]') ? false : true,

        dom: 'B',
        buttons: [
            {
                extend: 'excel',
                title: filename,
                text: "Download Attendance Records",
                className: 'mb-5 btn-sm btn-primary'
            }
        ]


    });


});

$('#update_attendance').submit(function (e) {
    $('#mark_attendance_btn').prop('disabled', true);
    $('#mark_attendance_btn').attr('data-kt-indicator', 'on');
    $('#mark_attendance_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/attendance/update_attendance',
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
                $('#mark_attendance_btn').prop('disabled', false);
                $('#mark_attendance_btn').removeAttr('data-kt-indicator');
                $('#mark_attendance_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                alert("Internet Connection Problem");
            } else {
                alert("Try Again. Error Code " + errorStatus);
            }
            $('#mark_attendance_btn').prop('disabled', false);
            $('#mark_attendance_btn').removeAttr('data-kt-indicator');
            $('#mark_attendance_btn').css('cursor', 'pointer');
        }
    });
});

$('.marked-attendance').dblclick(function () {
    var attendance_id = $(this).attr('data-attendance_id');
    var intime = $(this).attr('data-intime');
    var outtime = $(this).attr('data-outtime');
    var attendance_status = $(this).attr('data-attstatus');
    var employeeId = $(this).attr('data-employeeId');
    var dates = $(this).attr('data-date');
    var month = $(this).attr('data-month');
    var year = $(this).attr('data-year');
    var hours_worked = $(this).attr('data-workhours');
    var attendance_date = $(this).attr('data-attdate');
    var checkinsource = $(this).attr('data-checkinsource');
    var checkoutsource = $(this).attr('data-checkoutsource');
    var att_request_approval = $(this).attr('data-att_request_approval');


    if (attendance_id != "NA") {
        url = base_url + '/attendance/update_marked_attendance';
    } else {
        url = base_url + '/attendance/update_marked_attendance_absent';
    }

    if (checkinsource == '1') {
        checkinsource = 'Web Application';
    } else if (checkinsource == '2') {
        checkinsource = 'Mobile Application';
    } else if (checkinsource == '3') {
        checkinsource = 'Update Attendance';
    } else if (checkinsource == '4') {
        checkinsource = 'Machine Attendance';
    }

    if (checkoutsource == '1') {
        checkoutsource = 'Web Application';
    } else if (checkoutsource == '2') {
        checkoutsource = 'Mobile Application';
    } else if (checkoutsource == '3') {
        checkoutsource = 'Update Attendance';
    } else if (checkoutsource == '4') {
        checkoutsource = 'Machine Attendance';
    }

    if (att_request_approval == '1') {
        att_request_approval = 'Attendance Request';
    } else if (att_request_approval == '2') {
        att_request_approval = 'Leave Request';
    }else if (att_request_approval == '3') {
        att_request_approval = 'RPL Leave Request';
    }

    $('#edit_modal').modal('show');
    $('#attendance_date').val(attendance_id);
    $('#intime').val(intime);
    $('#outtime').val(outtime);
    $('#att_employee_id').val(employeeId);
    $('#att_for_date').val(dates);
    $('#att_for_month').val(month);
    $('#att_for_year').val(year);
    $('#status_show').html('-');
    $('#hours_worked_show').html('-');
    $('#attendance_date_show').html('-');
    $('#check_in_source_show').html('-');
    $('#check_out_source_show').html('-');
    $('#attendance_deputation_show').html('-');
    $('#status_show').html(attendance_status);
    $('#hours_worked_show').html(hours_worked);
    $('#attendance_date_show').html(attendance_date);
    $('#check_in_source_show').html(checkinsource);
    $('#check_out_source_show').html(checkoutsource);
    $('#attendance_deputation_show').html(att_request_approval);
    $('#attendance_status').val(attendance_status).trigger('change');
});

$('#update_attendance2').submit(function (e) {
    $('#edit_attendance_btn').prop('disabled', true);
    $('#edit_attendance_btn').attr('data-kt-indicator', 'on');
    $('#edit_attendance_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: url,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.status == "TRUE") {
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
                });

                $('#edit_attendance_btn').prop('disabled', false);
                $('#edit_attendance_btn').removeAttr('data-kt-indicator');
                $('#edit_attendance_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {

                Toast.fire({
                    icon: 'warning',
                    title: "Internet Connection Problem",
                    timer: 5000,
                });
            } else {

                Toast.fire({
                    icon: 'warning',
                    title: "Try Again. Error Code " + errorStatus,
                    timer: 5000,
                });
            }
            $('#edit_attendance_btn').prop('disabled', false);
            $('#edit_attendance_btn').removeAttr('data-kt-indicator');
            $('#edit_attendance_btn').css('cursor', 'pointer');
        }
    });
});

ra = '';
if (window.location.href.indexOf('?m=') != -1) {
    changeDepartment($("select[name=dept]").val());
    var loc = window.location.href;
    var index = loc.indexOf("&ra=");
    ra = loc.substring(index + 4)
}

function changeDepartment(dept) {
    $.ajax({
        url: base_url + '/attendance/get-department-managers',
        type: 'GET',

        data: {
            department: dept
        },

        success: function (response) {
            $('#reportingAuthority').empty()
            var newOption = new Option("Select Reporting Authority", '', false, false);
            $('#reportingAuthority').append(newOption).trigger('change');

            response = JSON.parse(JSON.stringify(response));
            if (response !== '') {
                for (i = 0; i < response.data.length; i++) {
                    let newOption = new Option(response.data[i]['employee_name'], response.data[i]['employee_id'], false, false);
                    $('#reportingAuthority').append(newOption).trigger('change');
                }

                $('#reportingAuthority').val(ra);
                $('#reportingAuthority').trigger('change');

            }

        },
        error: function (jqXHR, textStatus) {
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


}

$('#department_all').click(function () {

    var checkbox = $('#department_all').prop('checked');

    if (checkbox) {
        $('#departments_all option').prop('selected', true);
        $('#selectedepartment').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
        $('#selectedepartment').find('.select2-search__field').attr('placeholder', '');
        // no employee picked = everyone in the selected departments (decided on the server)
        $('#select_all_employee').val(null).trigger('change');
        $('#selectedemployees').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-select_all_employee-container-choice-5nle">All Employees</span>');
        $('#selectedemployees').find('.select2-search__field').attr('placeholder', '');

    } else {
        $('#departments_all option').prop('selected', false);
        $('#selectedepartment').find('.select2-selection__rendered').html('');
        $('#selectedepartment').find('.select2-search__field').attr('placeholder', 'Select Department');
        $('#selectedepartment').find('.select2-search__field').css('width', '100%');
        $('#select_all_employee').val(null).trigger('change');
        $('#selectedemployees').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-select_all_employee-container-choice-5nle"></span>');
        $('#selectedemployees').find('.select2-search__field').attr('placeholder', 'Select Employee');
        $('#selectedemployees').find('.select2-search__field').css('width', '100%');


    }

})

$('#departments_all').change(function () {

    var departments = $('#departments_all').val();
    var all_length = length_count;

    // the employee dropdown searches inside the picked departments; a new department choice starts it empty (= all of them)
    $('#select_all_employee').val(null).trigger('change');

    if ((departments || []).length == all_length) {
        $('#department_all').prop('checked', true);

    } else {
        $('#department_all').prop('checked', false);


    }

});


$('#att_share_report').submit(function (e) {

    $('#submit_btn').prop('disabled', true);
    $('#submit_btn').attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/attendance/share-employee-attendance-report',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.status == "TRUE") {
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
            if (errorStatus == 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Internet Connection Problem',
                    timer: 3000,
                })
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: 'Try Again. Error Code ' + errorStatus,
                    timer: 3000,
                })
            }
            $('#submit_btn').prop('disabled', false);
            $('#submit_btn').removeAttr('data-kt-indicator');
            $('#submit_btn').css('cursor', 'pointer');
        }
    });
});
