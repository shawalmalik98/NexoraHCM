function pollingshow(polling_id, polling_option_id) {

    swal.fire({
        title: 'Confirmation Alert !',
        text: 'Are you sure to select this option ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
    }).then(function (value) {

        if (value.isConfirmed == true) {

            $.ajax({
                url: base_url + '/polling/employee-polling',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                data: { 'polling_id': polling_id, 'polling_option_id': polling_option_id },

                success: function (data) {

                    if (data.status == 'TRUE') {

                        var count = 0;
                        var vote = `<div class="card wrapper">
                                        <div class="row">
                                            <input type="hidden" name="poll" value="">

                                            <div class="col-md-12">
                                                <header class="mb-4 ms-4">${data.data[0].polling_title} <br></header>

                                            </div> `

                        $.each(data.data, function (key, pollingvoting) {
                            count++;

                            if (count == 1) {

                                vote += `<div class="col-xl-6 col-md-12" id="${count}">
                                            <div class="row align-items-center mb-4">
                                                <div class="col-10">
                                                    <div class="progress">
                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="${pollingvoting.percentage}" aria-valuemin="0" aria-valuemax="100" style="width: ${pollingvoting.percentage}%">
                                                        <span class="skill-name">${pollingvoting.polling_options}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-left"><span>${pollingvoting.percentage}%</span></div>
                                            </div>
                                        </div> `

                            } else if (count == 2) {

                                vote += `<div class="col-xl-6 col-md-12" id="${count}">
                                            <div class="row align-items-center mb-4">
                                                <div class="col-10">
                                                    <div class="progress">
                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="${pollingvoting.percentage}" aria-valuemin="0" aria-valuemax="100" style="width: ${pollingvoting.percentage}%">
                                                        <span class="skill-name">${pollingvoting.polling_options}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-left"><span>${pollingvoting.percentage}%</span></div>
                                            </div>

                                        </div> `

                            } else if (count == 3) {
                                vote += `<div class="col-xl-6 col-md-12" id="${count}">
                                            <div class="row align-items-center mb-4">
                                                <div class="col-10">
                                                    <div class="progress">
                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="${pollingvoting.percentage}" aria-valuemin="0" aria-valuemax="100" style="width: ${pollingvoting.percentage}%">
                                                        <span class="skill-name">${pollingvoting.polling_options}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-left"><span>${pollingvoting.percentage}%</span></div>
                                            </div>

                                        </div> `

                            } else if (count == 4) {

                                vote += `<div class="col-xl-6 col-md-12" id="${count}">
                                            <div class="row align-items-center mb-4">
                                                <div class="col-10">
                                                    <div class="progress">
                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="${pollingvoting.percentage}" aria-valuemin="0" aria-valuemax="100" style="width: ${pollingvoting.percentage}%">
                                                        <span class="skill-name">${pollingvoting.polling_options}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-left"><span>${pollingvoting.percentage}%</span></div>
                                            </div>

                                        </div> `

                            } else {
                                vote += `<div class="col-xl-6 col-md-12" id="${count}">
                                            <div class="row align-items-center mb-4">
                                                <div class="col-10">
                                                    <div class="progress">
                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="${pollingvoting.percentage}" aria-valuemin="0" aria-valuemax="100" style="width: ${pollingvoting.percentage}%">
                                                        <span class="skill-name">${pollingvoting.polling_options}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-left"><span>${pollingvoting.percentage}%</span></div>
                                            </div>

                                        </div> `

                            }

                        });
                        vote += `</div>
                            </div>`;
                        $('#polling_div_' + polling_id).html(vote)
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: data.msg,
                            timer: 3000,
                        })
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    }
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
    });

}

function alert_remove(alert_id) {
    $('#alert_div' + alert_id).remove();
    $.ajax({
        url: base_url + '/remove_alert/' + alert_id,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });
}

$('#select-department').change(function (e) {
    e.preventDefault();
    $('#employees').html('');
    var dept = $('#select-department').val();

    $.ajax({
        url: base_url + '/get-employee-department',
        type: 'GET',

        data: {
            department: dept
        },

        success: function (response) {
            $('#employees').append(response);


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


})

$('#select-department-wfh').change(function (e) {
    e.preventDefault();
    $('#employees-wfh').html('');
    var dept = $('#select-department-wfh').val();

    $.ajax({
        url: base_url + '/get-employee-department',
        type: 'GET',

        data: {
            department: dept
        },

        success: function (response) {
            $('#employees-wfh').append(response);


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


})

$('#select-department-employee').change(function (e) {
    e.preventDefault();
    $('#employee_list').html('');
    var dept = $('#select-department-employee').val();

    $.ajax({
        url: base_url + '/get-employee-department',
        type: 'GET',

        data: {
            department: dept
        },

        success: function (response) {
            $('#employee_list').append(response);


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
});