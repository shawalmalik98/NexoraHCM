$('#addremoteworkForm').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $("#submit-btn").attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/wfh-requests/all-wfh-request',
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
                    timer: 3000,
                })
                $('#submit-btn').prop('disabled', false);
                $('#submit-btn').css('cursor', 'pointer');
                $("#submit-btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit-btn').prop('disabled', false);
            $('#submit-btn').css('cursor', 'pointer');
            $("#submit-btn").removeAttr('data-kt-indicator');
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

$('#employee_all').click(function () {

    var checkbox = $('#employee_all').prop('checked');

    if (checkbox) {
        $('#employees option').prop('selected', true);
        $('#selectedemployee').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-employees-container-choice-5nle-Human Resource">All Employees</span>');
        $('#selectedemployee').find('.select2-search__field').attr('placeholder', '');
    } else {
        $('#employees option').prop('selected', false);
        $('#selectedemployee').find('.select2-selection__rendered').html('');
        $('#selectedemployee').find('.select2-search__field').attr('placeholder', 'Select Employee');
        $('#selectedemployee').find('.select2-search__field').css('width', '100%');

    }

});

$('#request_date').attr('max', get_two_month_future_date);

$('#employee_all').attr("disabled", true);
$('#department_all').click(function () {

    var checkbox = $('#department_all').prop('checked');

    if (checkbox) {
        $('#employee_all').removeAttr("disabled");
        $('#departments_all option').prop('selected', true);
        $('#selectedepartment').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
        $('#selectedepartment').find('.select2-search__field').attr('placeholder', '');
    } else {
        $('#employee_all').attr("disabled", true);
        $('#employee_all').prop('checked', false);
        $('#employees option').prop('selected', false);
        $('#selectedemployee').find('.select2-selection__rendered').html('');
        $('#selectedemployee').find('.select2-search__field').attr('placeholder', 'Select Employees');
        $('#departments_all option').prop('selected', false);
        $('#selectedepartment').find('.select2-selection__rendered').html('');
        $('#selectedepartment').find('.select2-search__field').attr('placeholder', 'Select Department');
        $('#selectedepartment').find('.select2-search__field').css('width', '100%');

    }

})

$('#departments_all').change(function () {

    var departments = $('#departments_all').val();
    var all_length = length_count;
    if (departments.length == all_length) {
        $('#department_all').prop('checked', true);
    } else {
        $('#department_all').prop('checked', false);

    }

});

$('.departments_all').change(function (e) {
    e.preventDefault();
    $('#employee_all').removeAttr("disabled");
    $('#employees').html('');
    var dept = $('#departments_all').val();
    if (dept.length == 0) {
        $('#employee_all').attr("disabled", true);
        $('#employee_all').prop('checked', false);
    }
    $.ajax({
        url: base_url + '/get-employee-department',
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

$('.message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#message_body').html(message);
});

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 3);
})

$('.checked').on('click', function () {

    var checkbox = dataTable.$(".checked:checked", { "page": "all" })

    if (checkbox.length <= 0) {
        $('.on-check').addClass('d-none');
    } else {
        $('.on-check').removeClass('d-none');
    }

});
var request_id = null;
var status_approved_disapproved;
var approvermessage = null;
var disapprovermessage = null;
$('.wfh_request').on('click', function (e) {

    status_approved_disapproved = $(this).attr('data-value');
    title = $(this).attr('data-title');
    request_id = $(this).attr('data-id');
    $('#approvermessage').val('');
    $('#disapprovermessage').val('');

    if (status_approved_disapproved == "DisApprove" || status_approved_disapproved == "LeadDisApprove") {
        $('#modal-danger').modal('show');

        if (title != '') {
            $('.msg').html(title);
        }

    } else {

        $('#modal-success').modal('show');
    }

});

$('.request_submit').submit(function (e) {
    e.preventDefault();
    approvermessage = $('#approvermessage').val();
    disapprovermessage = $('#disapprovermessage').val();

    $('#modal-danger').modal('hide');
    $('#modal-success').modal('hide');

    var values = [];
    var checkbox = dataTable.$(".checked:checked", { "page": "all" })

    if (checkbox.length > 0) {

        $(checkbox).each(function () {
            values.push($(this).attr('data-id'));
        });
        var value = values.join(",");

    } else {
        value = request_id;
    }

    $.ajax({
        url: base_url + '/wfh-requests/wfhapproverequest',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { ids: value, status: status_approved_disapproved, approvermsg: approvermessage, disapprovermsg: disapprovermessage },
        success: function (data) {
            if (data.status == "TRUE") {
                $(".checked").prop("checked", false);
                Toast.fire({
                    icon: 'success',
                    title: data.msg,
                    timer: 3000,
                })
                setTimeout(() => {
                    location.reload();
                }, 3000);

            } else if (data.status == "FALSE") {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 3000,
                })
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
});

$('.max_length_validator').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$(function () {
    $('#req_status').on('change', function () {
        var value = $(this).val();
        if (value == '') {
            dataTable.column(6).search('', false, true).draw();
        } else {
            dataTable.column(6).search("^" + value + "$", true, false).draw();
        }
    });
});

function get_two_month_future_date() {
    var dates = new Date();
    dates.setDate(dates.getDate());
    var future_dates = dates.getFullYear() + '-' + ('0' + (dates.getMonth() + 3)).slice(-2) + '-' + ('0' + dates.getDate()).slice(-2);
    return future_dates;
}