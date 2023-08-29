$(function () {
    $('.selectemployee').select2({
        dropdownParent: $('#kt_modal_new_target')
    })

});

$('#reason').change(function () {
    var reason_val = $(this).val();
    if (reason_val == 'Other') {
        $('.reason_div').removeClass('d-none');
        $('#reason_input').attr('required', 'required');
    } else {
        $('.reason_div').addClass('d-none');
        $('#reason_input').removeAttr('required');
    }
});

$("#loan_deduct_type").change(function () {
    var loan_deduct_type = $(this).val();
    var amount_loan = $('#amount_loan').val();
    if (loan_deduct_type == 1) {
        $('#deduction_div').removeClass('d-none')
        $('.deduct-amount').removeClass('d-none')
        $('#amount_label').removeClass('d-none')
        $('#percentage_label').addClass('d-none')
        $('.deduct-percentage').addClass('d-none')
        $('.deduct-percentage').prop('required', false);
        $('.deduct-percentage').prop('max', false);
        $('.deduct-amount').prop('required', true);
        $('.deduct-amount').prop('max', amount_loan);
    } else {
        $('.deduct-amount').prop('required', false);
        $('.deduct-amount').prop('max', false);
        $('.deduct-percentage').prop('required', true);
        $('.deduct-amount').addClass('d-none');
        $('#amount_label').addClass('d-none');
        $('#percentage_label').removeClass('d-none');
        $('#deduction_div').removeClass('d-none');
        $('.deduct-percentage').removeClass('d-none');
        $('.deduct-percentage').prop('max', 100);
    }

});

$("#amount_loan").keyup(function () {
    var amount_loan = $('#amount_loan').val();
    $('.deduct-amount').prop('max', amount_loan);
});


$('#add_loan_form').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/loans/add-new-loan',
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
                    timer: 8000,
                })
                $('#submit_btn').prop('disabled', false);
                $("#submit_btn").removeAttr('data-kt-indicator');
                $('#submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn').prop('disabled', false);
            $("#submit_btn").removeAttr('data-kt-indicator');
            $('#submit_btn').css('cursor', 'pointer');
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

function deleteloan(delete_id) {
    $('input[name=delete_id]').val(delete_id);
    $('#delete-loan').modal('show');

}

$('#delete_loan').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/loans/delete-loan',
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