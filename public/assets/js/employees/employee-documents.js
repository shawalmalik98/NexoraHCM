$(function () {
    dataTable = $('#tableEmployeeDocument').DataTable({
        order: false,
    });
    $('#searchFilter').keyup(function () {
        dataTable.search($(this).val()).draw();
    })
});

$('#addDocumentForm').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/employees/upload_document',
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

var add_file_counter;
function addfileRow() {
    add_file_counter = $("input[name=file_length]").val();

    if (add_file_counter >= 5) {
        Toast.fire({
            icon: 'warning',
            title: 'You can\'t add more than 5 documents',
        });
        return true;
    }
    add_file_counter++;
    $("input[name=file_length]").val(add_file_counter);

    $("#file_div").append(`
        <div class="row mt-5" id="deletefile${add_file_counter}">
            <div class="col-md-5">
                <input type="text" class="form-control " name="title[]" placeholder="Enter Title e.g CNIC, Degree etc" required="">
            </div>

            <div class="col-md-5"> 
                <input type="file" class="form-control" accept=".docx,.txt,application/pdf,image/*" name="file[]" required="">
            </div>

            <div class="col-md-1">
                <button class="btn btn-icon btn-light-primary me-3" type="button" onclick="addfileRow()">
                    <span class="svg-icon svg-icon-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                        </svg>
                    </span>              
                </button>
            </div>

            <div class="col-md-1">
                <button class="btn btn-icon btn-light-danger" type="button" onClick="removeDiv(this)">
                    <span class="svg-icon svg-icon-1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor"></rect>
                        <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor"></rect>
                        </svg>
                    </span>        
                    
                </button>
            </div>
        </div>`

    );

}

function removeDiv(elem) {

    add_file_counter = $("input[name=file_length]").val()
    if (add_file_counter > 1) {
        swal.fire({
            title: 'Confirmation Alert !',
            text: 'Are you sure to delete record ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
        }).then(function (value) {
            if (value.isConfirmed == true) {
                $(elem).parent('div').parent('div').remove();
                $("#deletefile" + add_file_counter);
                add_file_counter--;
                $("input[name=file_length]").val(add_file_counter);
            }
        });
    }
}

$('.checked').on('click', function () {

    var checkbox = $(".checked:checked");

    if (checkbox.length <= 0) {
        $('.on-check').addClass('d-none');
    } else {
        $('.on-check').removeClass('d-none');
    }

});
var request_id = null;
let status_delete;
$('.document_request').on('click', function (e) {
    status_delete = $(this).attr('data-value');
    request_id = $(this).attr('data-id');

    if (status_delete == "Delete") {
        $('#delete-employee-document').modal('show');
    }

});

$('#delete_employee_document').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();

    var values = [];
    var checkbox = $(".checked:checked");

    if (checkbox.length > 0) {

        $(checkbox).each(function () {
            values.push($(this).attr('data-id'));
        });
        var value = values.join(",");

    } else {
        value = request_id;
    }

    $.ajax({
        url: base_url + '/employees/discard_document',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { ids: value, status: status_delete },
        success: function (data) {
            if (data.status == 'TRUE') {
                $(".checked").prop("checked", false);
                Toast.fire({
                    icon: 'success',
                    title: data.msg,
                    timer: 3000,
                })
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                if (data.autoreload == "TRUE") {

                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                }
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 3000,
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

function editDocuments(document_id, title) {
    $('input[name=document_id]').val(document_id);
    $('input[name=title]').val(title);
    $('')
    $('#edit_employee_documents_modal').modal('show');
}

$('#edit_employee_documents_form').submit(function (e) {
    $('#edit_submit_btn').prop('disabled', true);
    $("#edit_submit_btn").attr('data-kt-indicator', 'on');
    $('#edit_submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/employees/edit_upload_document',
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
                $('#edit_submit_btn').prop('disabled', false);
                $("#edit_submit_btn").removeAttr('data-kt-indicator');
                $('#edit_submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#edit_submit_btn').prop('disabled', false);
            $("#edit_submit_btn").removeAttr('data-kt-indicator');
            $('#edit_submit_btn').css('cursor', 'pointer');
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
