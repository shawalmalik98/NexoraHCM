function pollingshow(polling_id, polling_option_id) {

    $.ajax({
        url: base_url + '/polling/polling-options-log',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        data: { 'polling_id': polling_id, 'polling_option_id': polling_option_id },

        success: function (data) {

            if (data.status == 'TRUE') {
                var record;
                var percentagebar;

                $.each(data.data, function (key, pollingLog) {
                    if (polling_option_id == pollingLog.polling_option_id) {

                        percentagebar = `<div class="row">
                                        <div class="col-xl-12 col-md-12">
                                            <div class="row align-items-center mb-4">
                                                <div class="col-10">
                                                    <div class="progress">
                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="${pollingLog.percentage}" aria-valuemin="0" aria-valuemax="100" style="width:${pollingLog.percentage}%">
                                                         <span class="skill-name">${pollingLog.polling_options}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-left"><span>${pollingLog.percentage}%</span></div>
                                            </div>
                                        </div> 
                                    </div> `


                    }

                });
                $('#progresbar').html(percentagebar);
                $('#pollingTable').html(record);
                record = '';
                record += `<thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-7">Employee Name</th>
                        <th class="ps-7">Employee Department</th>
                        <th class="ps-7">Employee Designation</th>
                    </tr>
                    </thead>
                    <tbody>`;
                $.each(data.employee, function (key, pollingLog) {

                    record += `<tr>
                <td class="ps-6 ">${pollingLog.employee_name}</td>
                <td class="ps-6 ">${pollingLog.department_name}</td>
                <td class="ps-6 ">${pollingLog.designation_name}</td>
                </tr>`;

                });
                record += ` </tbody>`;
                $('#system_datatable').html(record);


            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 3000,
                });
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