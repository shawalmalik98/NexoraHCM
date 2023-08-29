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

$('#installement_source').change(function () {
    var source_value = $(this).val();

    if (source_value == 'Cash') {
        $('.resoucrce-use').removeClass('d-none');
        $('#installment_resource_label').html('Reciever Name');
        $('#installment_resource').attr('required', 'required');
    } else if (source_value == 'Cheque') {
        $('.resoucrce-use').removeClass('d-none');
        $('#installment_resource_label').html('Cheque No');
        $('#installment_resource').attr('required', 'required');
    } else {
        $('.resoucrce-use').removeClass('d-none');
        $('#installment_resource_label').html('Account No');
        $('#installment_resource').attr('required', 'required');
    }
});

$('#installment_amount').keyup(function () {
    let deduction_amount = $(this).val();
    let employee_loan = $('#loan_remaining').val();
    if (employee_loan == '0' || employee_loan == '0.00') {
        $('#installment_amount').attr('readonly', 'readonly');
    } else {
        if (Number(deduction_amount) > Number(employee_loan)) {
            $('#installment_amount').val('');
            Toast.fire({
                icon: 'warning',
                title: 'Installment amount can\'t be greater than loan remaining amount',
                timer: 8000,
            })
        } else {

        }
    }
});

$('#add_installment').submit(function (e) {
    $('#installment_btn').prop('disabled', true);
    $('#installment_btn').attr('data-kt-indicator', 'on');
    $('#installment_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    console.log($('#add_installment').serialize());
    swal.fire({
        title: 'Confirmation Alert !',
        text: 'Are you sure to submit your Pay Installment ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
    }).then(function (value) {

        if (value.isConfirmed == true) {
            $.ajax({
                url: base_url + '/loans/payinstallment',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $('#add_installment').serialize(),
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
                        $('#installment_btn').prop('disabled', false);
                        $('#installment_btn').removeAttr('data-kt-indicator');
                        $('#installment_btn').css('cursor', 'pointer');
                    }
                },
                error: function (jqXHR, textStatus) {
                    var errorStatus = jqXHR.status;
                    $('#installment_btn').prop('disabled', false);
                    $('#installment_btn').removeAttr('data-kt-indicator');
                    $('#installment_btn').css('cursor', 'pointer');
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
                }
            });
        }
        else {
            location.reload();
        }
    });
});

$('#update_loan_detail').submit(function (e) {
    $('#update_loan_detail_submit_btn').prop('disabled', true);
    $("#update_loan_detail_submit_btn").attr('data-kt-indicator', 'on');
    $('#update_loan_detail_submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/loans/update_loan',
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
                $('#update_loan_detail_submit_btn').prop('disabled', false);
                $("#update_loan_detail_submit_btn").removeAttr('data-kt-indicator');
                $('#update_loan_detail_submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#update_loan_detail_submit_btn').prop('disabled', false);
            $("#update_loan_detail_submit_btn").removeAttr('data-kt-indicator');
            $('#update_loan_detail_submit_btn').css('cursor', 'pointer');
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

var update_deduct_type = $("#update_deduct_type").val();
var loan_edit_amount = $('#loan_edit_amount').val();
if (update_deduct_type == 1) {
    $('#deduction_edit_div').removeClass('d-none')
    $('.deduct-amout-edit').removeClass('d-none')
    $('#label_amount').removeClass('d-none')
    $('#label_percentage').addClass('d-none')
    $('.deduct-percentage-edit').addClass('d-none')
    $('.deduct-percentage-edit').prop('required', false);
    $('.deduct-percentage-edit').prop('max', false);
    $('.deduct-amout-edit').prop('required', true);
    $('.deduct-amout-edit').prop('max', loan_edit_amount);
} else {
    $('.deduct-amout-edit').prop('required', false);
    $('.deduct-amout-edit').prop('max', false);
    $('.deduct-percentage-edit').prop('required', true);
    $('.deduct-amout-edit').addClass('d-none')
    $('#label_amount').addClass('d-none')
    $('#label_percentage').removeClass('d-none')
    $('#deduction_edit_div').removeClass('d-none')
    $('.deduct-percentage-edit').removeClass('d-none')
    $('.deduct-percentage-edit').prop('max', 100);
}

$("#update_deduct_type").change(function () {
    var update_deduct_type = $(this).val();
    var loan_edit_amount = $('#loan_edit_amount').val();
    if (update_deduct_type == 1) {
        $('#deduction_edit_div').removeClass('d-none')
        $('.deduct-amout-edit').removeClass('d-none')
        $('#label_amount').removeClass('d-none')
        $('#label_percentage').addClass('d-none')
        $('.deduct-percentage-edit').addClass('d-none')
        $('.deduct-percentage-edit').prop('required', false);
        $('.deduct-percentage-edit').prop('max', false);
        $('.deduct-amout-edit').prop('required', true);
        $('.deduct-amout-edit').prop('max', loan_edit_amount);
    } else {
        $('.deduct-amout-edit').prop('required', false);
        $('.deduct-amout-edit').prop('max', false);
        $('.deduct-percentage-edit').prop('required', true);
        $('.deduct-amout-edit').addClass('d-none')
        $('#label_amount').addClass('d-none')
        $('#label_percentage').removeClass('d-none')
        $('#deduction_edit_div').removeClass('d-none')
        $('.deduct-percentage-edit').removeClass('d-none')
        $('.deduct-percentage-edit').prop('max', 100);
    }

});

function installementSource(loan_installement_id) {
    if (loan_installement_id.length > 0) {
        $.ajax({
            url: base_url + '/loans/get_loan_installement_source/' + loan_installement_id,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                loan_installement_id: loan_installement_id
            },
            success: function (response) {
                var record = '';
                if (response.status == "TRUE") {
                    $('#installement-source-response').html('');
                    record += `<thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-7">Title</th>
                                <th>Remarks</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead> <tbody>`;

                    record += `<tr>
                            <td class="ps-6 "><b>${response.data[0].loan_installment_source}</b></td>
                            <td class="ps-6">${response.data[0].loan_installment_remarks}</td>
                            <td class="ps-6">${response.data[0].employee_name}</td>
                            <td class="ps-6">${response.data[0].loan_installment_date}</td>
                        </tr>`;

                    record += ` </tbody>`;
                    $('#installement-source-response').html(record);
                } else {
                    record = 'No installement responed  to this loan yet.';
                    $('#installement-source-response').html(record);
                }

                $('#view-installement-source').modal('show');

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

}