$(function () {
    $('.myselect2').select2({
        dropdownParent: $('#add-new-designation')
    })
    $('.designation_update_name').select2({
        dropdownParent: $('#updatedesignation')
    })
});

function deactivate_confirm($designation_id) {
    $('#modal-danger').modal('show');
    $('#delete_desig_id').val($designation_id)
}

$('#delete_desigform').submit(function (e) {
    $('#delete-btn').prop('disabled', true);
    $('#delete-btn').attr('data-kt-indicator', 'on');
    $('#delete-btn').css('cursor', 'not-allowed');
    var designation_id = $('#delete_desig_id').val()
    e.preventDefault();
    $.ajax({
        url: base_url + '/designations/discard_designation/' + designation_id,
        type: 'GET',
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
                $('#delete-btn').prop('disabled', false);
                $('#delete-btn').removeAttr('data-kt-indicator');
                $('#delete-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#delete-btn').prop('disabled', false);
            $('#delete-btn').removeAttr('data-kt-indicator');
            $('#delete-btn').css('cursor', 'pointer');
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

$('#adddesignation_form').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();

    let form_obj = new FormData(this);
    form_obj.set('count', document.querySelector('#departments_all').selectedOptions.length);

    $.ajax({
        url: base_url + '/designations/add_designation',
        type: 'POST',
        data: form_obj,
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
                $('#submit-btn').prop('disabled', false);
                $('#submit-btn').removeAttr('data-kt-indicator');
                $('#submit-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit-btn').prop('disabled', false);
            $('#submit-btn').removeAttr('data-kt-indicator');
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


function designationEditModel(designation_id, designation_name, department_id) {
    $('#department_id3').val(designation_name);
    $('#department_name').val(department_id).trigger('change');
    $('input[name=designation_id]').val(designation_id);
}

$('#updateddesignation_form').submit(function (e) {
    $('#update-btn').prop('disabled', true);
    $('#update-btn').attr('data-kt-indicator', 'on');
    $('#update-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/designations/update_designation',
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
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 8000,
                })
                $('#update-btn').prop('disabled', false);
                $('#update-btn').removeAttr('data-kt-indicator');
                $('#update-btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#update-btn').prop('disabled', false);
            $('#update-btn').removeAttr('data-kt-indicator');
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