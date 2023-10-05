$('#addlocationForm').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/locations/addlocation',
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

function editlocationRequest(location_id, location_title, location_address, location_latitude, location_longitude, location_allowed_range) {
    $('input[name=location_id]').val(location_id);
    $('#title').val(location_title);
    $('#address').val(location_address);
    $('input[name=latitude]').val(location_latitude);
    $('input[name=longitude]').val(location_longitude);
    $('input[name=location_allowed_range]').val(location_allowed_range);
    $('#updatelocation').modal('show');
    updatelocation();
}

$('#updatelocationForm').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/locations/updatelocation',
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

function deletelocationRequest(location_id) {
    $('input[name=location_id]').val(location_id);
    $('#delete-location-request').modal('show');

}

$('#delete_location_request').submit(function (e) {
    $('#delete_submit_btn').prop('disabled', true);
    $("#delete_submit_btn").attr('data-kt-indicator', 'on');
    $('#delete_submit_btn').css('cursor', 'pointer');
    e.preventDefault();
    $.ajax({
        url: base_url + '/locations/deletelocation',
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

function checkLatLong() {
    var latitude = $('#latitude').val();
    var longitude = $('#longitude').val();

    setTimeout(() => {
        if (latitude && longitude) {
            var locationUrl = `https://maps.google.com/maps?q=${latitude},${longitude}&hl=es&z=14&output=embed`;
            $('#locationIframe').attr('src', locationUrl);
        } else {
            $('#locationIframe').attr('src', '');
        }

    }, 2000);
}

function updatelocation() {
    var latitude = $('#location_latitude').val();
    var longitude = $('#location_longitude').val();

    setTimeout(() => {
        if (latitude && longitude) {
            var locationUrl = `https://maps.google.com/maps?q=${latitude},${longitude}&hl=es&z=14&output=embed`;
            $('#updatelocationIframe').attr('src', locationUrl);
        } else {
            $('#updatelocationIframe').attr('src', '');
        }

    }, 2000);
}


function locationuploadclose() {

    $('#update_btn').prop('disabled', false);
    $("#update_btn").removeAttr('data-kt-indicator');
    $('#update_btn').css('cursor', 'pointer');
    $('input[name=Force_Approved]').val('FALSE');
}

function locationupload() {

    $('.force_upload').prop('disabled', true);
    $('.force_upload').attr('data-kt-indicator', 'on');
    $('.force_upload').css('cursor', 'not-allowed');
    $('#employee_location_update').submit();

}

$('#employee_location_update, #employee_location_upload').submit(function (e) {

    $('#update_btn').prop('disabled', true);
    $("#update_btn").attr('data-kt-indicator', 'on');
    $('#update_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/locations/upload-location-information',
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
                if (data.validation == 'TRUE') {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 3000,
                    })
                    $('#update_btn').prop('disabled', false);
                    $("#update_btn").removeAttr('data-kt-indicator');
                    $('#update_btn').css('cursor', 'pointer');

                } else {
                    $('#location_records_upload').modal('show');
                    $('input[name=Force_Approved]').val('TRUE');

                    var employee = '<div>' +
                        '<p>' + data.msg + '</p>' +
                        '<p>' + data.error_logs.join('<br>') + '</p>' +
                        '</div>';
                    $('#location_record_msg').html(employee);
                }

            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#update_btn').prop('disabled', false);
            $("#update_btn").removeAttr('data-kt-indicator');
            $('#update_btn').css('cursor', 'pointer');
            if (errorStatus == 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Location upload failed please try again',
                    timer: 4000,
                });
                setTimeout(() => {
                    location.reload();
                }, 4000);
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


function officelocationuploadclose() {

    $('#upload_location_btn').prop('disabled', false);
    $('#upload_location_btn').removeAttr('data-kt-indicator');
    $('#upload_location_btn').css('cursor', 'pointer');
    $('input[name=Location_Approved]').val('FALSE');
}

function officelocationupload() {

    $('.distributor_upload').prop('disabled', true);
    $('.distributor_upload').attr('data-kt-indicator', 'on');
    $('.distributor_upload').css('cursor', 'not-allowed');
    $('#office_location_upload').submit();

}

$('#office_location_upload, #add_office_location').submit(function (e) {

    $('#upload_location_btn').prop('disabled', true);
    $('#upload_location_btn').attr('data-kt-indicator', 'on');
    $('#upload_location_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/locations/upload-office-location',
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
                if (data.validation == 'TRUE') {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 3000,
                    })
                    $('#upload_location_btn').prop('disabled', false);
                    $('#upload_location_btn').removeAttr('data-kt-indicator');
                    $('#upload_location_btn').css('cursor', 'pointer');

                } else {
                    $('#office_location_records_upload').modal('show');
                    $('input[name=Location_Approved]').val('TRUE');

                    var employee = '<div>' +
                        '<p>' + data.msg + '</p>' +
                        '<p>' + data.error_logs.join('<br>') + '</p>' +
                        '</div>';
                    $('#office_location_record_msg').html(employee);
                }

            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#upload_location_btn').prop('disabled', false);
            $('#upload_location_btn').removeAttr('data-kt-indicator');
            $('#upload_location_btn').css('cursor', 'pointer');
            if (errorStatus == 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Location upload failed please try again',
                    timer: 4000,
                });
                setTimeout(() => {
                    location.reload();
                }, 4000);
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




