function request_response_log(request_id, request_module_id) {
    $.ajax({

        url: base_url + '/request-manager-status',
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { request_id: request_id, request_module_id: request_module_id },
        success: function (response) {
            var record = '';
            if (response.status == "TRUE") {
                $('#request-manager-response').html('');
                record += `<thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-7">Manager Name</th>
                        <th>Approval Status</th>
                        <th>Approval Time</th>
                        <th>Approval Message</th>
                        
                    </tr>
                </thead> <tbody>`;
                $.each(response.data, function (key, employee_request) {
                    if (employee_request.request_approver_status == 2) {
                        request_status = '<span class="badge badge-light-success text-center font-weight-bold">Approved</span>';
                    } else {
                        request_status = '<span class="badge badge-light-danger text-center font-weight-bold">Disapproved</span>';
                    }

                    record += `<tr>
                        <td class="ps-6 "><b>${employee_request.employee_code} - ${employee_request.employee_name}</b></td>
                        <td class="ps-6">${request_status}</td>
                        <td class="ps-6">${employee_request.request_approval_time}</td>
                        <td class="ps-6">${employee_request.request_approver_msg != null ? employee_request.request_approver_msg : ''}</td>
                    </tr>`;
                });
                record += ` </tbody>`;
                $('#request-manager-response').html(record);
            } else {
                record = 'No manager responded to this request yet.';
                $('#request-manager-response').html(record);
            }

            $('#manager_modal_status').modal('show');
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
                Toast.fire({
                    icon: 'warning',
                    title: 'Try Again. Error Code ' + errorStatus,
                    timer: 3000,
                })
            }
        }
    });
}