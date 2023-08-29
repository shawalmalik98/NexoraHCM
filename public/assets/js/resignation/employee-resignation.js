$('#toDate,#fromDate').change(function () {

    var start = moment($('#fromDate').val());
    $('#toDate').attr('min', $('#fromDate').val());
    var end = moment($(this).val());
    total_days = end.diff(start, "days");
    if ($('#toDate').val().length > 0 && $('#fromDate').val() > $('#toDate').val()) {
        $('#toDate').val('');
        $('#days').val('');
    } else {
        $('#days').val(total_days + 1);
    }

});


$('#add_employee_resignation_form').submit(function (e) {
    e.preventDefault();

    if (editor_instance.getData() == '') {
        Toast.fire({
            icon: 'warning',
            title: 'Resignation message field is required',
            timer: 2000,
        });
        return;
    }

    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');

    swal.fire({
        title: 'Confirmation Alert !',
        text: 'Are you sure to submit your resignation letter ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
    }).then(function (value) {

        if (value.isConfirmed == true) {
            $.ajax({
                url: base_url + '/add-employee-resignation',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $('#add_employee_resignation_form').serialize(),
                success: function (data) {
                    if (data.status == 'TRUE') {
                        Toast.fire({
                            icon: 'success',
                            title: data.msg,
                            timer: 3000,
                        })
                        setTimeout(() => {
                            window.location.href = base_url + '/employee-resignation';
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
                        Toast.fire({
                            icon: 'warning',
                            title: 'Try Again. Error Code ' + errorStatus,
                            timer: 3000,
                        })
                    }
                }
            });
        } else {
            location.reload();
        }
    });
});

let editor_instance = null;

if (document.querySelector('#reason') != null) {
    ClassicEditor

        .create(document.querySelector('#reason'))

        .then(reason => {
            editor_instance = reason;

        })
        .catch(error => {
            console.error(error);
        });
}



