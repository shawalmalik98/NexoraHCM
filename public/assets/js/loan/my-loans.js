$(function () {
    $('.loanselect').select2({
        dropdownParent: $('#loanrequest')
    })
    $('.loanselect2').select2({
        dropdownParent: $('#editloanrequest')
    })

});
$('.kt_docs_loanmaxlength_threshold1').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$('.message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#request_message_body').html(message);
});

$('.approver_message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#approver_loan_message').html(message);
});

$('#send_new_loan_request').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/loan-request',
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

$("#deduction_type").change(function () {
    var deduction_type = $(this).val();
    var loan_amount = $('#loan_amount').val();
    if (deduction_type == 1) {
        $('#deduct_div').removeClass('d-none')
        $('.deduct').removeClass('d-none')
        $('#deduct_label').removeClass('d-none')
        $('#deduct_per_label').addClass('d-none')
        $('.deduct_per').addClass('d-none')
        $('.deduct_per').prop('required', false);
        $('.deduct_per').prop('max', false);
        $('.deduct').prop('required', true);
        $('.deduct').prop('max', loan_amount);
    } else {
        $('.deduct').prop('required', false);
        $('.deduct').prop('max', false);
        $('.deduct_per').prop('required', true);
        $('.deduct').addClass('d-none')
        $('#deduct_label').addClass('d-none')
        $('#deduct_per_label').removeClass('d-none')
        $('#deduct_div').removeClass('d-none')
        $('.deduct_per').removeClass('d-none')
        $('.deduct_per').prop('max', 100);
    }

});

$("#loan_amount").keyup(function () {
    var loan_amount = $('#loan_amount').val();
    $('.deduct').prop('max', loan_amount);
    $('.deduction_source').val('');

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

function editloanRequest(loan_request_id, loan_request_amount, loan_reason, loan_deduction_type, loan_deduction_start, loan_deduction_amount, loan_reason_message) {
    $('input[name=request_id]').val(loan_request_id);
    $('#edit_loan_amount').val(loan_request_amount);
    $('#loan_reason').val(loan_reason).trigger('change')
    $('#deduct_type').val(loan_deduction_type).trigger('change')
    $('#deduct_start').val(loan_deduction_start);
    if (loan_deduction_type == 1) {
        $('#deduct_amount').val(loan_deduction_amount);
    } else {
        $('#deduct_percentage').val(loan_deduction_amount);
    }
    $('#loan_msg').html(loan_reason_message);
    $('#editloanrequest').modal('show');

}

$('#edit_loan_request').submit(function (e) {
    $('#submit_btn_loan').prop('disabled', true);
    $("#submit_btn_loan").attr('data-kt-indicator', 'on');
    $('#submit_btn_loan').css('cursor', 'pointer');

    e.preventDefault();
    $.ajax({
        url: base_url + '/update-loan-request',
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

                $('#submit_btn_loan').prop('disabled', false);
                $('#submit_btn_loan').css('cursor', 'pointer');
                $("#submit_btn_loan").removeAttr('data-kt-indicator');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn_loan').prop('disabled', false);
            $('#submit_btn_loan').css('cursor', 'pointer');
            $("#submit_btn_loan").removeAttr('data-kt-indicator');
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

function deleteloanRequest(delete_id) {
    $('input[name=delete_id]').val(delete_id);
    $('#delete-loan-request').modal('show');

}

$('#delete_loan_request').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/delete-loan-request',
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

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 5);
});

function viewloanDetail(loan_id) {
    my_loan_request_detail(loan_id);

}

function my_loan_request_detail(my_loan_req_id) {
    $.ajax({

        url: base_url + '/my-loan-request-detail',
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { request_id: my_loan_req_id },
        success: function (response) {
            var record = '';
            var installment_record = '';
            if (response.status == "TRUE") {
                $('#loan-request-detail-response').html('');
                $('#manage-loan-detail-response').html('');
                record += `<tr class="mb-4">
                       <td class="ps-6 "><b>Loan Given Amount</b></td>
                       <td class="ps-6">${response.data[0].loan_given_amount}</td>
                       <td class="ps-6 "><b>Loan Remaining Amount</b></td>
                       <td class="ps-6">${response.data[0].loan_remaining_amount}</td>
                      
                   </tr>
                   <tr class="pt-4">
                       <td class="ps-6 pt-4"><b>Loan Submitted Amount</b></td>
                       <td class="ps-6 pt-4">${response.data[0].loan_submitted_amount}</td>
                      
                   </tr>`;
                if (response.loan_installment != '') {
                    installment_record += `<thead>
                       <tr class="fw-bold text-muted bg-light">
                           <th class="ps-7">Loan Installment Amount</th>
                           <th class="ps-7">Loan Installment Date</th>

                       </tr>
                   </thead> <tbody>`;

                    $.each(response.loan_installment, function (key, employee_loan_installment) {

                        installment_record += `<tr>
                           <td class="ps-6 ">${employee_loan_installment.loan_installment_amount}</td>
                           <td class="ps-6">${employee_loan_installment.loan_installment_date}</td>
                             
                       </tr>`;
                    });
                    installment_record += ` </tbody>`;
                }

                $('#manage-loan-detail-response').html(record);
                $('#loan-request-detail-response').html(installment_record);
            } else {
                if (response.error_status == "FALSE") {
                    Toast.fire({
                        icon: 'warning',
                        title: response.msg,
                        timer: 5000,
                    });
                    $('#viewLoanDetailModal').modal('hide');

                    return false;
                }
                record = 'Your Loan request is pending .';
                $('#manage-loan-detail-response').html(record);

            }
            $('#viewLoanDetailModal').modal('show');

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

$(function () {
    $('#req_status').on('change', function () {
        var value = $(this).val();
        if (value == '') {
            dataTable.column(7).search('', false, true).draw();
        } else {
            dataTable.column(7).search("^" + value + "$", true, false).draw();
        }
    });
});

$('.deduction_source').keyup(function () {
    var source = $(this).attr('name');
    var val = $(this).val();
    var loan_amount = $('#loan_amount').val();
    if (loan_amount != '') {
        if (val != '') {
            if (source == 'deduction_amount') {
                var months = loan_amount / val;
                var official_months = months.toFixed(1);
            } else {
                actual_loan_amount = 100 / val;
                var months = actual_loan_amount;
                var official_months = months.toFixed(1);
            }
            if (official_months >= 1) {
                $('.deduction_months').val(official_months + ' Months');
            } else {
                $('.deduction_months').val('0 Months');
            }
        } else {
            $('.deduction_months').val('0 Months');
        }
    }
});