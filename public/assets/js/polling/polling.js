//add poll start

$('#department_all').click(function () {

    var checkbox = $('#department_all').prop('checked');

    if (checkbox) {
        $('#departments_all option').prop('selected', true);
        $('#selectedepartment').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
        $('#selectedepartment').find('.select2-search__field').attr('placeholder', '');
    } else {
        $('#departments_all option').prop('selected', false);
        $('#selectedepartment').find('.select2-selection__rendered').html('');
        $('#selectedepartment').find('.select2-search__field').attr('placeholder', 'Select Department');
        $('#selectedepartment').find('.select2-search__field').css('width', '100%');

    }

});

$('#departments_all').change(function () {
    var departments = $('#departments_all').val();
    var all_length = length_count;
    if (departments.length == all_length) {
        $('#department_all').prop('checked', true);
    } else {
        $('#department_all').prop('checked', false);

    }
});

var add_file_counter;
function addfileRow() {
    add_file_counter = $("input[name=file_length]").val();

    if (add_file_counter >= 5) {
        Toast.fire({
            icon: 'warning',
            title: 'You can\'t add more than 5 options',
        });
        return true;
    }
    add_file_counter++;
    $("input[name=file_length]").val(add_file_counter);

    $("#file_div").append(`
        <div class="row mt-3" id="deletefile${add_file_counter}">
            <div class="col-md-11 mb-4">
                <label class="fs-6 fw-semibold mb-2 required">Option ${add_file_counter}</label>
                <input type="text" class="form-control" maxlenght="50" name="options[]" placeholder="Enter Option" required="">
            </div>

            <div class="col-md-1 mb-4">
                <label class="fs-6 fw-semibold mb-8"></label>
                <button class="btn btn-icon btn-light-danger btn-sm" type="button" onClick="removeDiv(this)">
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
    if (add_file_counter > 2) {
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

$('#addpollingForm').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/polling/addpolling',
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

//add poll end

//update poll start

$('#all_departments_checkbox').click(function () {

    var checkbox = $('#all_departments_checkbox').prop('checked');

    if (checkbox) {
        $('#all_departments_selectbox option').prop('selected', true);
        $('#selectedepartment2').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
        $('#selectedepartment2').find('.select2-search__field').attr('placeholder', '');
    } else {
        $('#all_departments_selectbox option').prop('selected', false);
        $('#selectedepartment2').find('.select2-selection__rendered').html('');
        $('#selectedepartment2').find('.select2-search__field').attr('placeholder', 'Select Department');
        $('#selectedepartment2').find('.select2-search__field').css('width', '100%');

    }

});

$('#all_departments_selectbox').change(function () {

    var departments = $('#all_departments_selectbox').val();
    var all_length = length_count;
    if (departments.length == all_length) {
        $('#all_departments_checkbox').prop('checked', true);
    } else {
        $('#all_departments_checkbox').prop('checked', false);
    }


});

var update_poll_counter;
function updatefileRow() {
    update_poll_counter = $("input[name=polling_length]").val();

    if (update_poll_counter >= 5) {
        Toast.fire({
            icon: 'warning',
            title: 'You can\'t add more than 5 options',
        });
        return true;
    }
    update_poll_counter++;
    $("input[name=polling_length]").val(update_poll_counter);

    $("#option_html").append(`
        <div class="row mt-3" id="deletepoll${update_poll_counter}">
            <div class="col-md-11 mb-4">
                <label class="fs-6 fw-semibold mb-2 required">Option ${update_poll_counter} </label>
                <input type="text" class="form-control" maxlength="50" name="options[]" placeholder="Enter Option" required="">
            </div>

            <div class="col-md-1 mb-4">
                <label class="fs-6 fw-semibold mb-8"></label>
                <button class="btn btn-icon btn-light-danger btn-sm" type="button" onClick="removepollDiv(this)">
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

function removepollDiv(elem) {

    update_poll_counter = $("input[name=polling_length]").val()
    if (update_poll_counter > 2) {
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
                $("#deletepoll" + update_poll_counter);
                update_poll_counter--;
                $("input[name=polling_length]").val(update_poll_counter);
            }
        });
    }
}


function pollingEditModel(pollingdetail) {

    var polling_id = pollingdetail.polling_id;
    var department = pollingdetail.polling_department;
    var title = pollingdetail.polling_title;
    var days = pollingdetail.polling_days;
    var status = pollingdetail.polling_status;
    var option_html = '';
    $.ajax({
        url: base_url + '/polling/polling-options-log',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { 'polling_id': polling_id },
        success: function (data) {
            console.log(data)
            if (data.status == 'TRUE') {
                var option_html = '';
                var count = 0;
                $.each(data.data, function (key, pollingvalue) {
                    count++;
                    if (count == 1) {
                        option_html += `<div class="row mt-3">
                        <div class="col-md-11 mb-4">
                            <label class="fs-6 fw-semibold mb-2 required">Option ${count}</label>
                            <input type="text" class="form-control" maxlenght="50" name="options[]" placeholder="Enter Option" value="${pollingvalue.polling_options}" required="">
                        </div>

                        <div class="col-md-1 mb-4">
                            <label class="fs-6 fw-semibold mb-8"></label>
                            <button class="btn btn-icon btn-light-primary me-3 btn-sm" type="button" onclick="updatefileRow()">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                    </svg>
                                </span>              
                            </button>
                        </div>
                    </div>`;
                    } else {

                        option_html += `<div class="row mt-5">
                        <div class="col-md-11 mb-4">
                            <label class="fs-6 fw-semibold mb-2 required">Option ${count}</label>
                            <input type="text" class="form-control" maxlenght="50" name="options[]" placeholder="Enter Option" value="${pollingvalue.polling_options}" required="">
                        </div>

                        <div class="col-md-1 mb-4">
                            <label class="fs-6 fw-semibold mb-8"></label>
                            <button class="btn btn-icon btn-light-danger btn-sm" type="button" onClick="removepollDiv(this)">
                                <span class="svg-icon svg-icon-1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor"></rect>
                                    <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor"></rect>
                                    </svg>
                                </span>        
                                
                            </button>
                        </div>
                    </div>`;
                    }

                });
                $('#option_html').html(option_html);
                $('#poll_length').val(count);

            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 3000,
                });
            }
        },


    });

    $('#polling_title').val(title)
    $('#pollingdays').val(days).trigger('change');
    $('#pollingstatus').val(status).trigger('change');

    if (department == "*") {

        $("select[name='departments[]'] option").prop('selected', true);
        $('#selectedepartment2').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
        $('#selectedepartment2').find('.select2-search__field').attr('placeholder', '');
        $('#all_departments_checkbox').prop('checked', true);

    } else {
        $("select[name='departments[]']").val(department.split(",")).trigger('change');
    }

    $('input[name=polling_id]').val(polling_id);
}

$('#updatepollingForm').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/polling/updatepolling',
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

//update poll end

function deletepolling(polling_id) {
    $('input[name=polling_id]').val(polling_id);
    $('#delete-polling-request').modal('show');
}

$('#delete_polling_request').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/polling/discard-polling',
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
                        timer: 3000,
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