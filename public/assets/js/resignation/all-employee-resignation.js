$(function () {
    $('.resignationselect').select2({
        dropdownParent: $('#rejectresignation')
    })

});

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 6);
})

var request_id = null;
var status_approved_disapproved;
var approvermessage = null;
var disapprovermessage = null;
$('.resignation_request').on('click', function (e) {

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
    value = request_id;

    $.ajax({
        url: base_url + '/resignations/resignationapproverequest',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { ids: value, status: status_approved_disapproved, approvermsg: approvermessage, disapprovermsg: disapprovermessage },
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


$('#reject_resignation').submit(function (e) {
    $('#reject_btn').prop('disabled', true);
    $('#reject_btn').attr('data-kt-indicator', 'on');
    $('#reject_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/resignations/resignation-reject',
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
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })
                $('#reject_btn').prop('disabled', false);
                $('#reject_btn').css('cursor', 'pointer');
                $("#reject_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#reject_btn').prop('disabled', false);
            $('#reject_btn').css('cursor', 'pointer');
            $("#reject_btn").removeAttr('data-kt-indicator');
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

function resignationapprove(resignation_id) {
    $('input[name=resignation_id]').val(resignation_id)

}
$('#approve_resignation').submit(function (e) {
    e.preventDefault();
    $('#approve_btn').prop('disabled', true);
    $('#approve_btn').attr('data-kt-indicator', 'on');
    $('#approve_btn').css('cursor', 'not-allowed');
    e.preventDefault();

    $.ajax({
        url: base_url + '/resignations/resignation-approval',
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
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })
                $('#approve_btn').prop('disabled', false);
                $('#approve_btn').css('cursor', 'pointer');
                $("#approve_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#approve_btn').prop('disabled', false);
            $('#approve_btn').css('cursor', 'pointer');
            $("#approve_btn").removeAttr('data-kt-indicator');
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


function resignationdisapprove(resignation_id) {
    $('input[name=resignation_id]').val(resignation_id)

}
$('#disapprove_resignation').submit(function (e) {
    e.preventDefault();
    $('#disapprove_btn').prop('disabled', true);
    $('#disapprove_btn').attr('data-kt-indicator', 'on');
    $('#disapprove_btn').css('cursor', 'not-allowed');
    e.preventDefault();

    $.ajax({
        url: base_url + '/resignations/resignation-disapproval',
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
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })
                $('#disapprove_btn').prop('disabled', false);
                $('#disapprove_btn').css('cursor', 'pointer');
                $("#disapprove_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#disapprove_btn').prop('disabled', false);
            $('#disapprove_btn').css('cursor', 'pointer');
            $("#disapprove_btn").removeAttr('data-kt-indicator');
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
    var message = $(this).attr('data-message');
    $('#message_body').html(message);
});

$('.approved_message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#approver_message').html(message);
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