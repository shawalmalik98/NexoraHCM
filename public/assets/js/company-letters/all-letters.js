$(function () {

    $('.select').select2({
        "enable": false,
        dropdownParent: $('#addletter')
    })

    $('.select-update').select2({
        "enable": false,
        dropdownParent: $('#updateletterForm')
    })

});

$('#addletterForm').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/letters/addletter',
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

$('#updateletterForm').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/letters/updateletter',
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
                $('#update-btn').prop('disabled', false);
                $("#update-btn").removeAttr('data-kt-indicator');
                $('#update-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#update-btn').prop('disabled', false);
            $("#update-btn").removeAttr('data-kt-indicator');
            $('#update-btn').css('cursor', 'pointer');
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

function letterEditModel(letter_id, letter_employee_id, letter_title) {

    $('#letterupdate').val(letter_employee_id).trigger('change');
    $('#title2').val(letter_title);
    $('input[name=letter_id]').val(letter_id);

}

var add_file_counter;

function addfileRow() {
    add_file_counter = $("input[name=file_length]").val();

    if (add_file_counter >= 5) {
        toastFire();
        return true;
    }
    add_file_counter++;
    $("input[name=file_length]").val(add_file_counter);

    $("#file_div").append(`
            <div class="row mt-5" id="deletefile${add_file_counter}">
                 <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2 required">Title  ${add_file_counter}</label>
                        <input type="text" class="form-control " name="title[]" placeholder="title" required="">
                    </div>

                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Document ${add_file_counter}</label>
                        <input type="file" class="form-control " name="document_name[]" required="">
                    </div>
            </div>

        `);
}

function deletefileRow() {
    add_file_counter = $("input[name=file_length]").val()
    if (add_file_counter > 1) {
        swal.fire({
            title: 'Confirmation Alert !',
            text: 'Are you sure to delete this last record ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
        }).then(function (value) {
            if (value.isConfirmed == true) {
                $("#deletefile" + add_file_counter).remove();
                add_file_counter--;
                $("input[name=file_length]").val(add_file_counter);
            }
        });
    }
}
