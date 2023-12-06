function deactivate_confirm(delete_url) {
    $('#modal-danger').modal('show');
    document.getElementById('delete_button').setAttribute("href", delete_url);
}

$('#location_all').click(function () {

    var checkbox = $('#location_all').prop('checked');

    if (checkbox) {
        $('#locations_all option').prop('selected', true);
        $('#selectedlocation').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-locations_all-container-choice-5nle-Human Resource">All Locations</span>');
        $('#selectedlocation').find('.select2-search__field').attr('placeholder', '');
    } else {
        $('#locations_all option').prop('selected', false);
        $('#selectedlocation').find('.select2-selection__rendered').html('');
        $('#selectedlocation').find('.select2-search__field').attr('placeholder', 'Select Location');
        $('#selectedlocation').find('.select2-search__field').css('width', '100%');

    }

})

$('#locations_all').change(function () {
    var locations = $('#locations_all').val();
    var all_length = "{{$count}}";
    if (locations.length == all_length) {
        $('#location_all').prop('checked', true);
    } else {
        $('#location_all').prop('checked', false);

    }
});

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

})

$('#departments_all').change(function () {
    var departments = $('#departments_all').val();
    var all_length = "{{$count}}";
    if (departments.length == all_length) {
        $('#department_all').prop('checked', true);
    } else {
        $('#department_all').prop('checked', false);

    }
});

$('#all_locations_checkbox').click(function () {

    var checkbox = $('#all_locations_checkbox').prop('checked');

    if (checkbox) {
        $('#all_locations_selectbox option').prop('selected', true);
        $('#selectelocation2').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-locations_all-container-choice-5nle-Human Resource">All Locations</span>');
        $('#selectelocation2').find('.select2-search__field').attr('placeholder', '');
    } else {
        $('#all_locations_selectbox  option').prop('selected', false);
        $('#selectelocation2').find('.select2-selection__rendered').html('');
        $('#selectelocation2').find('.select2-search__field').attr('placeholder', 'Select Location');
        $('#selectelocation2').find('.select2-search__field').css('width', '100%');

    }

})

$('#all_locations_selectbox').change(function () {
    var locations = $('#all_locations_selectbox').val();
    var all_length = "{{$count}}";
    if (locations.length == all_length) {
        $('#all_locations_checkbox').prop('checked', true);
    } else {
        $('#all_locations_checkbox').prop('checked', false);

    }
});

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

})

$('#all_departments_selectbox').change(function () {

    var departments = $('#all_departments_selectbox').val();
    var all_length = "{{$count}}";
    if (departments.length == all_length) {
        $('#all_departments_checkbox').prop('checked', true);
    } else {
        $('#all_departments_checkbox').prop('checked', false);
    }


});

$('#addalertForm').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/alerts/addalert',
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


$("#scheduled_date").change(function () {
    var scheduled_date = $(this).val();
    var currentDate = new Date().toISOString().slice(0, 16);
  
    if (scheduled_date == "Custom Time") {
        $('#date_div').removeClass('d-none');
        $('#date').attr('min', currentDate);
        $('#date').prop('required', true);
    } else {
        $('#date_div').addClass('d-none');
        $('#date').prop('required', false);
    }

});

$('.message_btn').click(function () {
    var message = $(this).attr('data-message');
    $('#message_body').html(message);
});

function alertEditModel(alertdetail) {

    var alert_id = alertdetail.alert_id;
    var title = alertdetail.title;
    var message = alertdetail.message;
    var department = alertdetail.department;
    var location = alertdetail.location;
    var scheduled_time = alertdetail.scheduled_time;
    var days = alertdetail.days;

    $('#alert_title').val(title)
    $('#alertdays').val(days).trigger('change');
    $('#message2').html(message)

    if (department == "*") {

        $("select[name='departments[]'] option").prop('selected', true);
        $('#selectedepartment2').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
        $('#selectedepartment2').find('.select2-search__field').attr('placeholder', '');
        $('#all_departments_checkbox').prop('checked', true);

    } else {
        $("select[name='departments[]']").val(department.split(",")).trigger('change');
    }

    if (location == "*") {

        $("select[name='locations[]'] option").prop('selected', true);
        $('#selectelocation2').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-locations_all-container-choice-5nle-Human Resource">All Locations</span>');
        $('#selectelocation2').find('.select2-search__field').attr('placeholder', '');
        $('#all_locations_checkbox').prop('checked', true);

    } else {
        $("select[name='locations[]']").val(location.split(",")).trigger('change');
    }

    $('#scheduled_date2').val(scheduled_time);
    $('input[name=alert_id]').val(alert_id);
}

$('#updatealertForm').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/alerts/updatealert',
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

$('.max_length_validator').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

function deletealert(alert_id) {
    $('input[name=alert_id]').val(alert_id);
    $('#delete-alert-request').modal('show');
}

$('#delete_alert_request').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/alerts/discard_alert',
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


function viewAlertEmployee(alert_id) {
    get_all_alert_read_employee(alert_id)

}


function get_all_alert_read_employee(alert_id) {

    $.ajax({

        url: base_url + '/alert-employee-mark-as-read',
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { alert_id: alert_id },
        success: function (response) {
            var record = '';
            if (response.status == "TRUE") {
                $('#alert-employee-response').html('');
                record += `<thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-6">Employee name</th>   
                        <th class="ps-6">Viewed at</th>   
                    </tr>
                </thead> <tbody>`;
                $.each(response.data, function (key, employee_request) {
                    record += `<tr>
                        <td class="ps-6"><b>${employee_request.employee_code} - ${employee_request.employee_name}</b></td>
                        <td>${employee_request.created_at}</td>
          
                    </tr>`;
                });
                record += ` </tbody>`;
                $('#alert-employee-response').html(record);
            }

            $('#view-all-employee-alert-read').modal('show');
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