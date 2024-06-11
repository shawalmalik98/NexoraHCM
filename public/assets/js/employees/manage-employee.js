$('#downloadbutton').submit(function () {
    var download_type = $("#download_information_type").val();
    if (download_type == "pdf") {
        $('#submit_btn').prop('disabled', true);
        $('#submit_btn').attr('data-kt-indicator', 'on');
        $('#submit_btn').css('cursor', 'not-allowed');
        $('#download_message').removeClass('d-none');
        $('#download_message_excel').addClass('d-none');
    } else {
        $('#submit_btn').prop('disabled', true);
        $('#submit_btn').attr('data-kt-indicator', 'on');
        $('#submit_btn').css('cursor', 'not-allowed');
        $('#download_message_excel').removeClass('d-none');
        $('#download_message').addClass('d-none');

    }
});

function employeeuploadclose() {

    $('#update_btn').prop('disabled', false);
    $("#update_btn").removeAttr('data-kt-indicator');
    $('#update_btn').css('cursor', 'pointer');
    $('input[name=Force_Approved]').val('FALSE');
}

function employeeupload() {

    $('.force_upload').prop('disabled', true);
    $('.force_upload').attr('data-kt-indicator', 'on');
    $('.force_upload').css('cursor', 'not-allowed');
    $('#employee_record_update').submit();

}

$('#employee_record_update, #employee_record_upload').submit(function (e) {

    $('#update_btn').prop('disabled', true);
    $("#update_btn").attr('data-kt-indicator', 'on');
    $('#update_btn').css('cursor', 'not-allowed');
    e.preventDefault();
       
    $.ajax({
        url: base_url + '/employees/upload-employee-information',
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
                    $('#employee_records_upload').modal('show');
                    $('input[name=Force_Approved]').val('TRUE');

                    // var employee = '<div>' +
                    //     '<p>' + data.msg + '</p>' +
                    //     '<p>' + data.error_logs.join('<br>') + '</p>' +
                    //     '</div>';
                    // $('#employee_record_msg').html(employee);

                }
            
                swal.fire({ 
                    icon: 'warning',
                    title: data.msg,
                })
                $('#update_btn').prop('disabled', false);
                $("#update_btn").removeAttr('data-kt-indicator');
                $('#update_btn').css('cursor', 'pointer');
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
                    title: 'Employee upload failed please try again',
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