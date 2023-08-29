$(function () {
    $('#select-department').trigger('change');

});
$('#select-department').change(function (e) {
    e.preventDefault();
    $('#employees').html('');
    var dept = $('.multi-depart').val();

    $.ajax({
        url: base_url + '/get-employee-department-hd',
        type: 'GET',

        data: {
            department: dept,
            help_desk_id: help_desk_id,
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

function ticketStatusModal(help_desk_id, emp_id, status) {
    $('input[name=hd_id]').val(help_desk_id);
    $('input[name=emp_id]').val(emp_id);
    $('input[name=ticket_status]').val(status);

    if (status == 1) {
        $('.status-msg').html('Are you sure to Activate this Token Again ?')
    }

    if (status == 2) {
        $('.status-msg').html('Are you sure to activate chat on this ticket?')
    }
    if (status == 3) {
        $('.status-msg').html('Are you sure to Rejected this Ticket ?')
    }
    if (status == 4) {
        $('.status-msg').html('Are you sure to Resolve this Query From Department?')
    }
    if (status == 5) {
        $('.status-msg').html('Are you sure to Resolve this Query From Employee?')
    }
    $('#ticket-status-modify').modal('show');

}


$('#ticket_status_modify').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/ticket-status',
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
                if (data.autoreload == "TRUE") {

                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                }
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

$('#addEmployeeHelpDesk').submit(function (e) {
    $('#help-desk-employee-btn').prop('disabled', true);
    $("#help-desk-employee-btn").attr('data-kt-indicator', 'on');
    $('#help-desk-employee-btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/update-help-desk-employee',
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
                if (data.autoreload == "TRUE") {

                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })

                $('#help-desk-employee-btn').prop('disabled', false);
                $('#help-desk-employee-btn').css('cursor', 'pointer');
                $("#help-desk-employee-btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#help-desk-employee-btn').prop('disabled', false);
            $('#help-desk-employee-btn').css('cursor', 'pointer');
            $("#help-desk-employee-btn").removeAttr('data-kt-indicator');
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

$('#addDepartmentHelpDesk').submit(function (e) {
    $('#help-desk-department-btn').prop('disabled', true);
    $("#help-desk-department-btn").attr('data-kt-indicator', 'on');
    $('#help-desk-department-btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/update-help-desk-department',
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
                if (data.autoreload == "TRUE") {

                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })

                $('#help-desk-department-btn').prop('disabled', false);
                $('#help-desk-department-btn').css('cursor', 'pointer');
                $("#help-desk-department-btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#help-desk-department-btn').prop('disabled', false);
            $('#help-desk-department-btn').css('cursor', 'pointer');
            $("#help-desk-department-btn").removeAttr('data-kt-indicator');
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