$('#addrequisitionForm').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/addrequisition',
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
                $('#submit-btn').prop('disabled', false);
                $("#submit-btn").removeAttr('data-kt-indicator');
                $('#submit-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit-btn').prop('disabled', false);
            $("#submit-btn").removeAttr('data-kt-indicator');
            $('#submit-btn').css('cursor', 'pointer');
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

$("#scheduled_time").change(function () {
    var scheduled_time = $(this).val();
    if (scheduled_time == "Custom time") {
        $('#start_time_div').removeClass('d-none')
        $('#end_time_div').removeClass('d-none')
        $('#start_time').prop('required', true);
        $('#end_time').prop('required', true);
        $('#start_time').val('');
        $('#end_time').val('');
    } else {
        $('#start_time_div').addClass('d-none')
        $('#end_time_div').addClass('d-none')
        $('#start_time').prop('required', false);
        $('#end_time').prop('required', false);
        $('#start_time').val('');
        $('#end_time').val('');
    }

});

function view_requisition_detail(requisition_id, requisition_department, requisition_job_title, requisition_job_type, requisition_expected_salary, requisition_shift_time, requisition_shift_start_time, requisition_shift_end_time, requisition_date_needed, no_of_position, office_location, requisition_additional_info) {

    $('input[name=requisition_department]').val(requisition_department);
    $('#job_title2').val(requisition_job_title);
    $('#job_type2').val(requisition_job_type).trigger('change');
    $('input[name=requisition_salary]').val(requisition_expected_salary);
    $('#scheduled_time2').val(requisition_shift_time).trigger('change');
    $('input[name=shift_start]').val(requisition_shift_start_time);
    $('input[name=shift_end]').val(requisition_shift_end_time);
    $('#req_date2').val(requisition_date_needed);
    $('#req_position').val(no_of_position);
    $('#req_location').val(office_location).trigger('change');
    $('#req_additional2').val(requisition_additional_info);
    $('input[name=requisition_id]').val(requisition_id);

}
$("#scheduled_time2").change(function () {
    var scheduled_time = $(this).val();
    if (scheduled_time == "Custom time") {
        $('#start_time_div2').removeClass('d-none')
        $('#end_time_div2').removeClass('d-none')
        $('#start_time2').prop('required', true);
        $('#end_time2').prop('required', true);
    } else {
        $('#start_time_div2').addClass('d-none')
        $('#end_time_div2').addClass('d-none')
        $('#start_time2').prop('required', false);
        $('#end_time2').prop('required', false);
    }

});

$('#updaterequisitionForm').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $("#update-btn").attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/updaterequisition',
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
                if (data.autoreload == "TRUE") {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 3000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })

                $('#update-btn').prop('disabled', false);
                $('#update-btn').css('cursor', 'pointer');
                $("#update-btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#update-btn').prop('disabled', false);
            $('#update-btn').css('cursor', 'pointer');
            $("#update-btn").removeAttr('data-kt-indicator');
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

function deleterequisitionRequest(requisition_id) {
    $('input[name=requisition_id]').val(requisition_id);
    $('#delete-requisition-request').modal('show');
}

$('#delete_requisition_request').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/delete-requisition-request',
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
                if (data.autoreload == "TRUE") {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 3000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 3000,
                })

                $('#delete_submit_btn').prop('disabled', false);
                $('#delete_submit_btn').css('cursor', 'pointer');
                $("#delete_submit_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#delete_submit_btn').prop('disabled', false);
            $('#delete_submit_btn').css('cursor', 'pointer');
            $("#delete_submit_btn").removeAttr('data-kt-indicator');
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

$('#requisition_maxlength_threshold').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});