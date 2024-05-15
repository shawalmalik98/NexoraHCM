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

$('.leave_request').on('click', function (e) {
    status_approved_disapproved = $(this).attr('data-value');
    request_id = $(this).attr('data-id');
    title = $(this).attr('data-title');

    if (status_approved_disapproved == "DisApprove" || status_approved_disapproved == "LeadDisApprove") {
        $('#modal-danger').modal('show');
        if (title != '') {
            $('.msg').html(title);
        }
    } else {
        $('#modal-success').modal('show');
        if (status_approved_disapproved == "ForceApprove") {
            $('.msg').html(title);
        }
    }
});

$('.request_submit').submit(function (e) {
    $('.approve_btn').prop('disabled', true);
    $(".approve_btn").attr('data-kt-indicator', 'on');
    $('.approve_btn').css('cursor', 'pointer');
    e.preventDefault();
    approvermessage = $('#approvermessage').val();
    disapprovermessage = $('#disapprovermessage').val();

    // $('#modal-danger').modal('hide');
    // $('#modal-success').modal('hide');

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
        url: base_url + '/employees-leaves/leaveapproverequest',
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
                }, 2000);

            } else if (data.status == "FALSE") {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 3000,
                })

                $('.approve_btn').prop('disabled', false);
                $('.approve_btn').css('cursor', 'pointer');
                $(".approve_btn").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('.approve_btn').prop('disabled', false);
            $('.approve_btn').css('cursor', 'pointer');
            $(".approve_btn").removeAttr('data-kt-indicator');
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
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 2);
});

$(function () {
    $('#req_status').on('change', function () {
        var value = $(this).val();
        if (value == '') {
            dataTable.column(8).search('', false, true).draw();
        } else {
            dataTable.column(8).search("^" + value + "$", true, false).draw();
        }
    });
});
