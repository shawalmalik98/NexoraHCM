$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 4);
})

$('.message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#message_body').html(message);
});

var request_id = null;
var status_approved_disapproved;
var approvermessage = null;
var disapprovermessage = null;
$('.adv_request').on('click', function (e) {

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
        url: base_url + '/all-salary-request/advancesalaryapproverequest',
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