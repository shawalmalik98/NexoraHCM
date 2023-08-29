$(function () {
    dataTable = $('#tableEmployeeOnBoarding').DataTable({
        order: false,
    });
    $('#searchFilter').keyup(function () {
        dataTable.search($(this).val()).draw();
    })
});

function deactivate_confirm(delete_url) {
    $('#modal-danger').modal('show');
    document.getElementById('delete_button').setAttribute("href", delete_url);
}

function editOnBoardingEmployee(onboarding_id, candidate_name, department_name, doj) {

    $('input[name=onboarding_id]').val(onboarding_id)
    $('input[name=candidate_name]').val(candidate_name)
    $('input[name=deprt_name]').val(department_name)
    $('input[name=joining_date]').val(doj)


}

$('#update_onboarding_employee').submit(function (e) {
    $('#submit-btn').prop('disabled', true);
    $('#submit-btn').attr('data-kt-indicator', 'on');
    $('#submit-btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/employee-onboarding/update-employee-onboarding',
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
