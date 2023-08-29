$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 5);
});

$('.message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#message_body').html(message);
});

$("#deduct_type").change(function () {
    var deduct_type = $(this).val();
    var loan_amount = $('#edit_loan_amount').val();
    if (deduct_type == 1) {
        $('#deduct_div2').removeClass('d-none')
        $('.deduct2').removeClass('d-none')
        $('#deduct_label2').removeClass('d-none')
        $('#deduct_per_label2').addClass('d-none')
        $('.deduct_per2').addClass('d-none')
        $('.deduct_per2').prop('required', false);
        $('.deduct_per2').prop('max', false);
        $('.deduct2').prop('required', true);
        $('.deduct2').prop('max', loan_amount);
    } else {
        $('.deduct2').prop('required', false);
        $('.deduct2').prop('max', false);
        $('.deduct_per2').prop('required', true);
        $('.deduct2').addClass('d-none')
        $('#deduct_label2').addClass('d-none')
        $('#deduct_per_label2').removeClass('d-none')
        $('#deduct_div2').removeClass('d-none')
        $('.deduct_per2').removeClass('d-none')
        $('.deduct_per2').prop('max', 100);
    }

});

$("#edit_loan_amount").keyup(function () {
    var loan_amount = $('#edit_loan_amount').val();
    $('.deduct2').prop('max', loan_amount);

});

function loanRequest(loan_request) {

    var loan_request_id = loan_request.loan_request_id;
    var loan_request_amount = loan_request.loan_request_amount;
    var loan_deduction_type = loan_request.loan_deduction_type;
    var loan_deduction_amount = loan_request.loan_deduction_amount;
    request_id = loan_request_id;
    status_approved_disapproved = 'Approve';

    $('#edit_loan_amount').val(loan_request_amount);
    $('#edit_loan_amount').attr('max', loan_request_amount);
    $('#deduct_type').val(loan_deduction_type).trigger('change')
    if (loan_deduction_type == 1) {
        $('#deduct_amount').val(loan_deduction_amount);
    } else {
        $('#deduct_percentage').val(loan_deduction_amount);
    }
    $('#modal-success').modal('show');
}


var request_id = null;
var status_approved_disapproved;
var approvermessage = null;
var disapprovermessage = null;
var loan_request_amount = null;
var loan_deduction_type = null;
var loan_deduction_amount = null;
var loan_deduction_percentage = null;

$('.loan_request').on('click', function (e) {

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
    $('#approve_btn').prop('disabled', true);
    $("#approve_btn").attr('data-kt-indicator', 'on');
    $('#approve_btn').css('cursor', 'pointer');
    e.preventDefault();
    approvermessage = $('#approvermessage').val();
    disapprovermessage = $('#disapprovermessage').val();
    loan_request_amount = $('#edit_loan_amount').val();
    loan_deduction_type = $('#deduct_type').val();
    if (loan_deduction_type == 1) {
        loan_deduction_amount = $('#deduct_amount').val();
    } else {
        loan_deduction_percentage = $('#deduct_percentage').val();

    }

    $('#modal-danger').modal('hide');
    /* $('#modal-success').modal('hide'); */

    var values = [];
    value = request_id;

    $.ajax({
        url: base_url + '/all-loan-request/loanapproverequest',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { ids: value, status: status_approved_disapproved, approvermsg: approvermessage, disapprovermsg: disapprovermessage, loan_amount: loan_request_amount, deduction_type: loan_deduction_type, deduction_amount: loan_deduction_amount, deduction_percentage: loan_deduction_percentage },
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

                $('#approve_btn').prop('disabled', false);
                $("#approve_btn").attr('data-kt-indicator', 'off');
                $('#approve_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#approve_btn').prop('disabled', false);
            $("#approve_btn").attr('data-kt-indicator', 'off');
            $('#approve_btn').css('cursor', 'pointer');
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
            dataTable.column(8).search('', false, true).draw();
        } else {
            dataTable.column(8).search("^" + value + "$", true, false).draw();
        }
    });
});   